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

class MpAdvPaymentCashModuleFrontController extends ModuleFrontControllerCore
{
    public $ssl = true;
    private $_cart;
    
    public function initContent() 
    {
        $this->_cart = new CartCore(Context::getContext()->cart->id);
        //Cast into Cart to avoid exception
        $this->_cart = $this->cast($this->_cart, "Cart");
        
        if(!$this->checkCurrency()) {
            Tools::redirect('index.php?controller=order');
        }
        
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::initContent();
        
        //Assign to Smarty
        $this->context->smarty->assign([
            'nb_products'=> $this->_cart->nbProducts(),
            'cart' => $this->_cart,
            'cart_currency' => $this->_cart->id_currency,
            'currencies' => $this->module->getCurrency($this->_cart->id_currency),
            'total_amount' => $this->_cart->getOrderTotal(true),
            'path' => $this->module->getPathUri(),
            'summary' => $this->_cart->getSummaryDetails(),
            'params' => "'payment_method' => 'cash', 'payment_display' => 'cash'",
        ]);
        
        $this->setTemplate('cash.tpl');
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
}

