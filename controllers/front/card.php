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

class MpAdvPaymentCardModuleFrontController extends ModuleFrontControllerCore
{
    public $ssl = true;
    
    private $test;
    private $user;
    private $password;
    private $signature;
    
    public function initContent()
    {
        $this->display_column_left = false;
        $this->display_column_right = false;
        
        parent::initContent();
        
        /**
         * @var CartCore $cart
         */
        $cart = ContextCore::getContext()->cart;
        //Get session cart summary
        $summary = classSession::getSessionSummary();
        
        $this->test = (bool)ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_TEST_API");
        $this->user = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_USER_API");
        $this->password = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_PWD_API");
        $this->signature = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_SIGN_API");
        
        if($this->test) {
            $this->context->smarty->assign("PAYPAL_URL", "https://securepayments.sandbox.paypal.com/acquiringweb");
        } else {
            $this->context->smarty->assign("PAYPAL_URL", "https://securepayments.paypal.com/acquiringweb");
        }
        
        $this->context->smarty->assign("classSummary", $summary);
        $this->setTemplate('card.tpl');
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
