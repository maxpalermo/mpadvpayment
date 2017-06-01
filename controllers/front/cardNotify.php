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

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR
        . '..' . DIRECTORY_SEPARATOR
        . '..' . DIRECTORY_SEPARATOR
        . 'classes' . DIRECTORY_SEPARATOR
        . 'classPaypalIPN.php';

class MpAdvPaymentCardNotifyModuleFrontController extends ModuleFrontControllerCore{
    public $ssl = true;
    
    public function initContent()
    {   
        $ipn = new classPaypalIPN();
        classMpLogger::add('INIT IPN');
        $ipn->usePHPCerts(false);
        if(ConfigurationCore::get('MP_ADVPAYMENT_PAYPAL_TEST_API')==1) {
            $ipn->useSandbox();
        }
        
        classMpLogger::add('Verifing response');
        $verified = $ipn->verifyIPN();
        classMpLogger::add('Verified response: ' . (int)$verified);
        if($verified) {
            $values = Tools::getAllValues();
            classMpLogger::add(print_r($values, 1));
            classMpLogger::blank();
            classMpLogger::addEvidencedMsg('END OF IPN TRANSMISSION');
            
            /**
            * FINALIZE ORDER
            */
            $transaction_id=Tools::getValue('tnx_id', '');
            $cart_id = Tools::getValue('custom', '');
            if(!empty($cart_id) && Tools::strpos($cart_id, 'cart_id:')!==false) {
                $cart_id = Tools::substr($cart_id, 8);
            }
            classMpLogger::addEvidencedMsg('finalize order from cardNotify controller');
            classMpLogger::add('cart id from context: ' . (int)Context::getContext()->cart->id);
            classMpLogger::add('cart id from context summary: ' . (int)Context::getContext()->summary->id_cart);
            classMpLogger::add('cart id from response: ' . (int)$cart_id);
            
            //classValidation::FinalizeOrder(classCart::PAYPAL, $transaction_id, $this->module, false, $cart_id);
        } else {
            classMpLogger::add(print_r($values, 1));
            classMpLogger::addEvidencedMsg('ERROR VERIFY IPN');
        }
    }
}
