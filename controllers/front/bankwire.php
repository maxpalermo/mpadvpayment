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

class MpAdvPaymentBankwireModuleFrontController extends ModuleFrontControllerCore
{
    public $ssl = true;
    
    public function initContent()
    {
        /**
         * Get summary
         */
        $summary = classSession::getSessionSummary();
        $id_cart = Context::getContext()->cart->id;
        $cart = new Cart($id_cart);
        if (!$this->checkCurrency($cart)) {
            Tools::redirect('index.php?controller=order');
        }
        
        /**
         * INITIALIZE PAGE
         */
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::initContent();
        
        //Assign to Smarty
        $this->context->smarty->assign(array(
            'bankwire_cart' => $summary->bankwire->cart,
            'payment_method' => array('payment_method' => 'bankwire'),
        ));
        
        $this->setTemplate('bankwire.tpl');
    }
    
    public function checkCurrency($cart)
    {
        $currency_order = new CurrencyCore($cart->id_currency);
        $currencies_module = $this->module->getCurrency($cart->id_currency);
        
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
            $obj_out = 'O:' . Tools::strlen($to_class) . ':"' . $to_class . '":' 
                    . Tools::substr($obj_in, $obj_in[2] + 7);
            return unserialize($obj_out);
        } else {
            return false;
        }
    }
    
    public static function getBankwireDetails()
    {
        $det = new stdClass();
        $det->owner = ConfigurationCore::get("MP_ADVPAYMENT_OWNER");
        $det->iban  = ConfigurationCore::get("MP_ADVPAYMENT_IBAN");
        $det->bank  = ConfigurationCore::get("MP_ADVPAYMENT_BANK");
        $det->addr  = ConfigurationCore::get("MP_ADVPAYMENT_ADDR");
        
        return $det;
    }
}
