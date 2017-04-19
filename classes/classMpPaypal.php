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

include_once(dirname(__FILE__) . '/../../../config/config.inc.php');
include_once(_PS_ROOT_DIR_ . '/classes/Configuration.php');

class classMpPaypal
{
    private $test;
    private $user;
    private $password;
    private $signature;
    private $test_id;
    private $response;
    
    private $errors;
    
    public function __construct($test = '', $user = '', $password = '', $signature = '', $test_id = '')
    {
        if (empty($test)) {
            $this->test = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_TEST");
        } else {
            $this->test = $test;
        }
        
        if (empty($user)) {
            $this->user = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_USER_API");
        } else {
            $this->user = $user;
        }
        
        if (empty($password)) {
            $this->password = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_PWD_API");
        } else {
            $this->password = $password;
        }
        
        if (empty($signature)) {
            $this->signature = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_SIGN_API");
        } else {
            $this->signature = $signature;
        }
        
        if (empty($test_id)) {
            $this->test_id = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_TEST_API");
        } else {
            $this->test_id = $test_id;
        }
    }
    
    public function request($method, $params)
    {
        $params_merge = array_merge($params, array(
            'METHOD'    => $method,
            'VERSION'   => '74.0',
            'USER'      => $this->user,
            'PWD'       => $this->password,
            'SIGNATURE' => $this->signature
        ));
        $params_build = http_build_query($params_merge, '', '&');

        if ($this->test == 1) {
            $this->endpoint = 'https://api-3T.sandbox.paypal.com/nvp';
        } else {
            $this->endpoint = 'https://api-3T.paypal.com/nvp';
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->endpoint,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params_build,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSLVERSION => defined(CURL_SSLVERSION_TLSv1) ? CURL_SSLVERSION_TLSv1 : 1,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_VERBOSE => 1
        ));

        $response = curl_exec($curl);
        $responseArray = array();
        parse_str($response, $responseArray);
        $this->response = $responseArray;
        
        if (curl_errno($curl)) {
            $this->errors = curl_error($curl);
            curl_close($curl);
            return false;
        } else {
            if ($responseArray['ACK'] == 'Success') {
                curl_close($curl);
                return true;
            } else {
                $this->errors = $responseArray;
                curl_close($curl);
                return false;
            }
        }
    }
    
    public function redirectPaypal($token)
    {
        if ($this->test == '0') {
            $route = 'https://www.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token=' . $token;
        } else {
            $route = 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token=' . $token;
        }
        
        Context::getContext()->smarty->assign(array(
            'route' => $route
        ));

        Tools::redirect($route);
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
    
    public function getToken()
    {
        return $this->response['TOKEN'];
    }
    
    public function getResponse()
    {
        return $this->response;
    }
    
    public function getResponseACK()
    {
        return $this->response['ACK'];
    }
    
    public function getResponseTransactionID()
    {
        return $this->response['PAYMENTINFO_0_TRANSACTIONID'];
    }
    
    public function getPaymentRequest0()
    {
        return $this->response['PAYMENTREQUEST_0_AMT'];
    }
    
    public function getPaymentItemRequest0()
    {
        return $this->response['PAYMENTREQUEST_0_ITEMAMT'];
    }
    
    public function getCurrencyCode0()
    {
        return $this->response['PAYMENTREQUEST_0_CURRENCYCODE'];
    }
    
    public function getStatus()
    {
        return $this->response['CHECKOUTSTATUS'];
    }
    
    public function getPayerID()
    {
        return $this->response['PAYERID'];
    }
}
