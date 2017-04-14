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
    private $mpPayment;
    
    private $test;
    private $user;
    private $password;
    private $signature;
    
    public function initContent() 
    {
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::initContent();
        
        $this->setTemplate('paypal_error.tpl');
    }
    
    public function checkCurrency()
    {   
        $currency_order = new CurrencyCore($this->_cart->id_currency);
        $currencies_module = $this->module->getCurrency($this->_cart->id_currency);
        
        //Check if module accept currency
        if (is_array($currencies_module)) {
            foreach($currencies_module as $currency_module)
            {
                if($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    function cast($obj, $to_class) {
        if(class_exists($to_class)) {
            $obj_in = serialize($obj);
            $obj_out = 'O:' . strlen($to_class) . ':"' . $to_class . '":' . substr($obj_in, $obj_in[2] + 7);
            return unserialize($obj_out);
        } else {
            return false;
        }
    }
    
    function getPaypalDetails()
    {
        $det = new stdClass();
        $det->test       = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_TEST");
        $det->user       = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_USER");
        $det->password   = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_PWD");
        $det->signature  = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_SIGN");
        
        return $det;
    }
    
    public function redirectPaypal($token)
    {
        if ($this->test == '0')
            $route = 'https://www.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token=' . $token;
        else
            $route = 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token=' . $token;
        
        $this->context->smarty->assign(array(
            'route' => $route
        ));

        Tools::redirect($route);

    }
    
    public function request($method, $params)
    {

        $params = array_merge($params, array(
            'METHOD' => $method,
            'VERSION' => '74.0',
            'USER' => $this->user,
            'PWD' => $this->password,
            'SIGNATURE' => $this->signature
        ));
        $params = http_build_query($params, '', '&');

        if ($this->test == '1')
            $this->endpoint = 'https://api-3T.sandbox.paypal.com/nvp';
        else
            $this->endpoint = 'https://api-3T.paypal.com/nvp';

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->endpoint,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSLVERSION => defined(CURL_SSLVERSION_TLSv1) ? CURL_SSLVERSION_TLSv1 : 1,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_VERBOSE => 1
        ));

        $response = curl_exec($curl);
        $responseArray = array();
        parse_str($response, $responseArray);
        if (curl_errno($curl)) {
            $this->errors = curl_error($curl);
            curl_close($curl);
            return false;
        } else {
            if ($responseArray['ACK'] == 'Success') {
                curl_close($curl);
                return $responseArray;
            } else {
                $this->errors = $responseArray;
                curl_close($curl);
                return false;
            }
        }
    }
}
