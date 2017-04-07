<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . ".."
        . DIRECTORY_SEPARATOR . ".." 
        . DIRECTORY_SEPARATOR . "classes"
        . DIRECTORY_SEPARATOR . "classMpPaymentTables.php";

class MpAdvPaymentValidationModuleFrontController extends ModuleFrontControllerCore
{
    private $payment_method;
    private $payment_display;
    private $paymentConfig;
    
    public function postProcess()
    {
        //Set Class payment
        $this->paymentConfig = new classPaymentConfiguration();

        //Set params
        $this->payment_method = Tools::getValue('payment_method');
        $this->payment_display = Tools::getValue('payment_display');
        
        //Get configuration data
        $this->paymentConfig->read($this->payment_method);
        
        //Check if cart exists
        /** @var CartCore $cart */
        $cart = $this->context->cart;
        if($cart->id_customer == 0 
                || $cart->id_address_delivery == 0
                || $cart->id_address_invoice == 0
                || !$this->module->active) {
            Tools::redirect('index.php?controller=order&step=1');
        }
        
        //Check if module is enabled
        /** @var bool $authorized */
        $authorized = false;
        foreach(ModuleCore::getPaymentModules() as $module)
        {
            if($module['name'] == $this->module->name) {
                $authorized = true;
                break;
            }
        }
        
        if(!$authorized) {
            die($this->l("This payment method is not available."));
        }
        
        //Check if customer exists
        $customer = new CustomerCore($cart->id_customer);
        if (!ValidateCore::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
        }
        
        //Sets data
        $currency = $this->context->currency;
        $total = (float)$cart->getOrderTotal(true, CartCore::BOTH);
        $extra_vars = [];
        
        //Validate order
        $this->module->validateOrder(
                $cart->id,
                $this->paymentConfig->id_order_state,
                $total,
                $this->payment_display,
                NULL,
                $extra_vars,
                (int)$currency->id,
                false,
                $customer->secure_key);
        
        //Redirect on order confirmation page
        Tools::redirect('index.php?controller=order-confirmation'
                .'&idcart='.$cart->id
                .'&id_module='.$this->module->id
                .'&id_order='.$this->module->currentOrder
                .'&key='.$customer->secure_key);
    }
}
