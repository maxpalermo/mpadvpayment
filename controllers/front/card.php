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
        . DIRECTORY_SEPARATOR . 'autoload.php';


class MpAdvPaymentCardModuleFrontController extends ModuleFrontControllerCore
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
    private $order_id;
    private $transaction_id;
    private $cart_id;
    private $lang;
    private $order_reference;
    
    private function success($summary)
    {
       $this->order_id = Tools::getValue('id_order', 0);
       $this->transaction_id = Tools::getValue('transaction_id','xxxxxxxxxxxx');
       $this->tx = Tools::getValue('tx','');
       $this->cart_id = Context::getContext()->cart->id;
       $this->lang = Context::getContext()->language->id;

       
       $db = Db::getInstance();
       $sql = new DbQueryCore();
       
       $sql->select('id_order')
               ->select('reference')
               ->from('orders')
               ->where('id_cart = ' . $summary->id_cart);
       $result = $db->getRow($sql);
       $id_order = (int)$result['id_order'];
       $order_reference = $result['reference'];
       if($id_order == 0) 
       {
           classValidation::FinalizeOrder(classCart::PAYPAL, $this->transaction_id, $this->module);
       } else {
           classValidation::UpdateOrderPayment($order_reference, $this->transaction_id);
       }

       $this->sendMail();

       //Delete session cart summary
       classSession::delSessionSummary();

       //Show success page
       $this->context->smarty->assign("order_id",$this->order_id);
       $this->context->smarty->assign("order_reference",$this->order_reference);
       $this->context->smarty->assign("transaction_id",$this->transaction_id);
       $this->context->smarty->assign("total",$summary->paypal->cart->total_cart_with_tax_and_fee);
       $this->setTemplate("card_success.tpl");
    }
    
    private function sendMail()
    {
        /**
       /* Send mail
       $to      = $summary->paypal->customer->shipping->email;
       $subject = 'Paypal transaction success';
       $message = 'Your paypal transaction has been succesfully processed'
               . PHP_EOL . 'transaction id: ' . $this->transaction_id
               . PHP_EOL . 'order reference: ' . $this->order_reference
               . PHP_EOL . 'Total payed: ' . Tools::displayPrice($summary->paypal->cart->total_cart_with_tax_and_fee);
       $headers = 'From: ' . ConfigurationCore::get('PS_SHOP_EMAIL') . "\r\n" .
           'Reply-To: ' . ConfigurationCore::get('PS_SHOP_EMAIL') . "\r\n" .
           'X-Mailer: PHP/' . phpversion();

       mail($to, $subject, $message, $headers);
       */
    }
    
    private function displayPage($summary)
    {
        $link = new LinkCore();
        $cart = new Cart(Context::getContext()->cart->id);
        $shipping = new stdClass();
        $billing = new stdClass();
        $customer = new stdClass();

        $addr_ship = new AddressCore($cart->id_address_delivery);
        $addr_bill = new AddressCore($cart->id_address_invoice);
        $cust = new CustomerCore($addr_ship->id_customer);
        $state1 = new StateCore($addr_ship->id_state);
        $state2 = new StateCore($addr_bill->id_state);
        $country1 = new CountryCore($addr_ship->id_country);
        $country2 = new CountryCore($addr_bill->id_country);

        $shipping->first_name = $addr_ship->firstname;
        $shipping->last_name = $addr_ship->lastname;
        $shipping->address1 = $addr_ship->address1;
        $shipping->address2 = $addr_ship->address2;
        $shipping->city = $addr_ship->city;
        $shipping->state = $state1->name;
        $shipping->zip = $addr_ship->postcode;
        $shipping->country = $country1->iso_code;
        $shipping->email = $cust->email;

        $billing->first_name = $addr_bill->firstname;
        $billing->last_name = $addr_bill->lastname;
        $billing->address1 = $addr_bill->address1;
        $billing->address2 = $addr_bill->address2;
        $billing->city = $addr_bill->city;
        $billing->state = $state2->name;
        $billing->zip = $addr_bill->postcode;
        $billing->country = $country2->iso_code;
        $billing->email = $cust->email;
        $billing->phone_prefix = $country2->call_prefix;
        $billing->phone_number = empty($addr_bill->phone)?$addr_bill->phone_mobile:$addr_bill->phone;

        $customer->shipping = $shipping;
        $customer->billing = $billing;

        $this->test = (bool)ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_TEST_API");
        $this->user = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_USER_API");
        $this->password = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_PWD_API");
        $this->signature = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_SIGN_API");
        $this->email_business = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_EMAIL_API");
        $this->_lang = Context::getContext()->language->id;
        $this->action = Tools::getValue('action', '');
        $this->total_pay = (float)Tools::getValue('total_pay', 0);
        $this->cancelURL = $link->getModuleLink('mpadvpayment', 'card', array('cancel' => '1'));
        $this->returnURL = $link->getModuleLink('mpadvpayment', 'bankwire', array('success' => '1'));
        $this->notifyURL = $link->getModuleLink('mpadvpayment', 'card', array('notify' => '1'));
        $this->checkPayment = $link->getModuleLink('mpadvpayment', 'card', array('check' => '1', 'id_cart' => $cart->id));
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
        if($this->test) {
            $this->context->smarty->assign("PAYPAL_URL", "https://securepayments.sandbox.paypal.com/acquiringweb");
        } else {
            $this->context->smarty->assign("PAYPAL_URL", "https://securepayments.paypal.com/acquiringweb");
        }

        $this->context->smarty->assign("classSummary", $summary);
        $this->context->smarty->assign("EMAIL_BUSINESS",$this->email_business);
        $this->context->smarty->assign("notifyURL",$this->notifyURL);
        $this->context->smarty->assign("cancelURL",$this->cancelURL);
        $this->context->smarty->assign("returnURL",$this->returnURL);
        $this->context->smarty->assign("customer", $customer);
        $this->context->smarty->assign("total_order", Tools::displayPrice($cart->getOrderTotal(true)));
        $this->context->smarty->assign("AMT",$cart->getOrderTotal(true));
        $this->setTemplate('card.tpl');
    }
    
    public function initContent()
    {
        $this->display_column_left = false;
        $this->display_column_right = false;
        $link = new LinkCore();
        
        parent::initContent();
        //Get session cart summary
        $summary = classSession::getSessionSummary();
        
        if((int)Tools::getValue("success",0)==1) {
            /***********************
             * TRANSACTION SUCCESS *
             ***********************/
            $this->success($summary);
        } elseif((int)Tools::getValue('cancel',0)==1) {
            /************************
             * TRANSACTION CANCELED *
             ************************/
            $payment_page = $link->getPageLink('order', true, NULL, "step=3");
            header("Location: $payment_page");
        } elseif((int)Tools::getValue('notify',0)==1) {
            /****************
             * IPN LISTENER *
             ****************/
            header("Location: https://www.google.it");
        } elseif((int)Tools::getValue('error',0)==1) {
            /****************************
             * ERROR DURING TRANSACTION *
             ****************************/
            $this->setTemplate("card_error.tpl");
        } else {
            /************************
             * DISPLAY PAYMENT PAGE *
             ************************/
            $this->displayPage($summary);
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
        $det->test       = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_TEST_API");
        $det->user       = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_USER_API");
        $det->password   = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_PWD_API");
        $det->signature  = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_SIGN_API");
        $det->paypal_pro = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_PRO_API");
        $det->email      = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_EMAIL_API");
        return $det;
    }
}
