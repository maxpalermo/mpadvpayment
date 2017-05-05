<?php
/**
 * 2017 mpSOFT
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    mpSOFT <info@mpsoft.it>
 *  @copyright 2017 mpSOFT Massimiliano Palermo
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of mpSOFT
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..'
        . DIRECTORY_SEPARATOR . '..'
        . DIRECTORY_SEPARATOR . 'classes'
        . DIRECTORY_SEPARATOR . 'classMpPaymentCalc.php';

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..'
        . DIRECTORY_SEPARATOR . '..'
        . DIRECTORY_SEPARATOR . 'classes'
        . DIRECTORY_SEPARATOR . 'classMpPaypal.php';


class MpAdvPaymentPaypalModuleFrontController extends ModuleFrontControllerCore
{
    public $ssl = true;
    
    private $_cart;
    private $test;
    private $user;
    private $password;
    private $signature;
    private $action;
    private $total_pay;
    private $currency;
    private $decimals;
    private $returnURL;
    private $cancelURL;
    
    public function initContent()
    {
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::initContent();
        
        $this->test = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_TEST");
        $this->user = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_USER");
        $this->password = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_PWD");
        $this->signature = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_SIGN");
        $this->_lang = Context::getContext()->language->id;
        $this->action = Tools::getValue('action', '');
        $this->total_pay = (float)Tools::getValue('total_pay', 0);
        $this->cancelURL = Tools::getValue('cancelURL', '');
        $this->returnURL = Tools::getValue('returnURL', '');
        $this->currency = Context::getContext()->currency->iso_code;
        $this->decimals = Context::getContext()->currency->decimals;
        
        
        $requestParams = array(
            'RETURNURL' => $this->returnURL,
            'CANCELURL' => $this->cancelURL
        );
        
        $image_file = glob(
                dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." 
                . DIRECTORY_SEPARATOR . ".."
                . DIRECTORY_SEPARATOR . "paypal_logo.*"
                );
        if ($image_file) {
            $filename = _PS_BASE_URL_ . __PS_BASE_URI__ . "/modules/mpadvpayment/" . basename($image_file[0]);
        } else {
            $filename = "";
        }
        
        $orderParams = array(
            'LOGOIMG' => "https://www.dalavoro.it/img/imprendo-srls-logo-1480691391.jpg", //You can paste here your logo image URL
            "MAXAMT" => "100", //Set max transaction amount
            "NOSHIPPING" => "1", //I do not want shipping
            "ALLOWNOTE" => "0", //I do not want to allow notes
            "BRANDNAME" => ConfigurationCore::get("PS_SHOP_NAME"),
            "GIFTRECEIPTENABLE" => "0",
            "GIFTMESSAGEENABLE" => "0"
        );
        $item = array(
            'PAYMENTREQUEST_0_AMT' => number_format($this->total_pay, (int)$this->decimals),
            'PAYMENTREQUEST_0_CURRENCYCODE' => $this->currency,
            'PAYMENTREQUEST_0_ITEMAMT' => number_format($this->total_pay, (int)$this->decimals),
        );
        
        if (empty($this->action)) {
            $this->setTemplate('paypal_error.tpl');
        } else {
            switch ($this->action) {
                case 'SetExpressCheckout':
                    $this->SetExpressCheckout($requestParams + $orderParams + $item);
                    break;
                case 'GetExpressCheckoutDetails':
                    $this->GetExpressCheckoutDetails();
                    break;
                default:
                    $this->context->smarty->assign("function", "InitContent");
                    $this->context->smarty->assign("paypal_params", $requestParams + $orderParams + $item);
                    $this->context->smarty->assign("paypal_error", 'ACTION UNKNOWN:' . Tools::getValue('action', '-unknown-'));
                    $this->setTemplate('paypal_error.tpl');
                    break;
            }
        }
    }
    
    public function SetExpressCheckout($params)
    {
        $paypal = new classMpPaypal();
        
        $result = $paypal->request('SetExpressCheckout', $params);
        $this->context->smarty->assign("function", "SetExpressCheckout");
        $this->context->smarty->assign("paypal_params", $params);
        
        if ($result) { //Request successful
            //Now we have to redirect user to the PayPal
            $this->context->smarty->assign("paypal_response", $paypal->getResponse());
            $this->context->smarty->assign(array('paypal_token' => $paypal->getToken()));
            //$this->setTemplate('paypal_redirect.tpl');
            $paypal->redirectPaypal($paypal->getToken());
        } else {
            $this->context->smarty->assign(array('paypal_error' => $paypal->getErrors()));
            $this->setTemplate('paypal_error.tpl');
        }
    }
    
    public function GetExpressCheckoutDetails()
    {
        $this->context->smarty->assign("function", "GetExpressCheckoutDetails");
        
        
        $paypal = new classMpPaypal();
        $params = array(
            'TOKEN'     => Tools::getValue('token', ''),
            'PAYERID'   => Tools::getValue('PayerID', ''),
            );
        
        if (empty($params['TOKEN']) || empty($params['PAYERID'])) {
            $this->context->smarty->assign("paypal_params", $params);
            $this->context->smarty->assign(array('paypal_error' => 'BAD SERVER RESPONSE'));
            $this->setTemplate('paypal_error.tpl');
        } else {
            $params = array('TOKEN' => Tools::getValue('token', ''));
            
            $result = $paypal->request("GetExpressCheckoutDetails", $params);
            if ($result) {
                $this->context->smarty->assign("paypal_response", $paypal->getResponse());
                $this->context->smarty->assign("paypal_params", $params);
                $params = $paypal->getResponse();
                $this->DoExpressCheckoutPayment($params);
            } else {
                $this->context->smarty->assign("paypal_params", $params);
                $this->context->smarty->assign('paypal_error', $paypal->getErrors());
                $this->setTemplate('paypal_error.tpl');
            }
        }
    }
    
    public function DoExpressCheckoutPayment($params)
    {
        $this->context->smarty->assign("function", "DoExpressCheckoutPayment");
        $this->context->smarty->assign("paypal_params", $params);
        
        $paypal = new classMpPaypal();
        $result = $paypal->request('DoExpressCheckoutPayment', $params);
        
        if (!$result) {
            $this->context->smarty->assign('paypal_error', $paypal->getErrors());
            $this->setTemplate('paypal_error.tpl');
        } else {
            if ($paypal->getResponseACK() == 'Success') {
                $this->createOrder($paypal->getResponseTransactionID());
            }
        }
    }
    
    public function createOrder($transactionID)
    {
        $params = array(
            'transaction_id' => $transactionID,
            'payment_method' => ClassMpPayment::PAYPAL,
            'payment_display' => 'PAYPAL',
        );
        $this->context->smarty->assign('transaction_id', $transactionID);
        $link = new LinkCore();
        $url = $link->getModuleLink('mpadvpayment', 'validation', $params);
        Tools::redirect($url);
    }
    
    public function checkCurrency()
    {
        $currency_order = new CurrencyCore($this->_cart->id_currency);
        $currencies_module = $this->module->getCurrency($this->_cart->id_currency);
        
        //Check if module accept currency
        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    public function cast($obj, $to_class)
    {
        if (class_exists($to_class)) {
            $obj_in = serialize($obj);
            $obj_out = 'O:' . Tools::strlen($to_class) . ':"' . $to_class . '":' . Tools::substr($obj_in, $obj_in[2] + 7);
            return unserialize($obj_out);
        } else {
            return false;
        }
    }
    
    public function getPaypalDetails()
    {
        $det = new stdClass();
        $det->test       = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_TEST");
        $det->user       = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_USER");
        $det->password   = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_PWD");
        $det->signature  = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_SIGN");
        
        return $det;
    }
}
