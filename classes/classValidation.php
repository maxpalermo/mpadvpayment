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

class classValidation {
    /**
     * Retrieve product id from product reference
     * @param string $reference product reference
     * @return int product id
     */
    public static function getProductIdByReference($reference)
    {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        $sql->select('id_product')
                ->from('product')
                ->where('reference = \'' . pSQL($reference) . '\'');
        return (int)$db->getValue($sql);
    }
    
    /**
     * Retrieve order reference from id cart
     * @param int $id_cart cart id
     * @return string product reference
     */
    public static function getOrderReferenceByIdCart($id_cart)
    {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        $sql->select('reference')
                ->from('orders')
                ->where('id_cart = ' . (int)$id_cart);
        return $db->getValue($sql);
    }
    
    /**
     * Retrieve order id from id cart
     * @param int $id_cart
     * @return int order id
     */
    public static function getOrderIdByIdCart($id_cart)
    {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        $sql->select('id_order')
                ->from('orders')
                ->where('id_cart = ' . (int)$id_cart);
        return (int)$db->getValue($sql);
    }
    
    /**
     * Finalize cart and convert it to an order,
     * save extra info bill
     */
    public static function FinalizeOrder($payment_type,$transaction_id,$module)
    {
        /**
         * Get Currency
         * @var CurrencyCore $currency
         */
        $currency = Context::getContext()->currency;
        /**
         * @var classCart $fee_cart
         */
        $summary = classSession::getSessionSummary();
        if($payment_type == classCart::CASH) {
            $fee_cart = $summary->cash->cart;
            $payment_type_display = $module->l('Cash', 'classValidation');
        } elseif ($payment_type == classCart::BANKWIRE) {
            $fee_cart = $summary->bankwire->cart;
            $payment_type_display = $module->l('Bankwire', 'classValidation');
        } elseif ($payment_type == classCart::PAYPAL) {
            $fee_cart = $summary->paypal->cart;
            $payment_type_display = $module->l('Paypal', 'classValidation');
        }
        /**
         * Get fee and discount virtual products
         */
        $product_fee = new ProductCore(self::getProductIdByReference('fee'));
        /**
         * Get Cart
         * @var CartCore $cart
         */
        $cart = new Cart($summary->id_cart);
        /**
         * Validate cart
         */
        if ($cart->id_customer == 0
                || $cart->id_address_delivery == 0
                || $cart->id_address_invoice == 0
                || !$module->active) {
            print "<div class='panel panel-warning'>"
                    . "<pre>" 
                    . print_r($cart, 1) 
                    . "</pre>"
                    . "</div>";
            //Tools::redirect('index.php?controller=order&step=1');
        }
        /**
         * Check if module is enabled
         * @var bool $authorized 
         */
        $authorized = false;
        foreach (ModuleCore::getPaymentModules() as $pay_module) {
            if ($pay_module['name'] == $module->name) {
                $authorized = true;
                break;
            }
        }
        
        if (!$authorized) {
            Tools::d($module->l('This payment method is not available.', 'classValidation'));
        }
        /**
         * Validate customer
         */
        $customer = new CustomerCore($cart->id_customer);
        if (!ValidateCore::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
        }
        
        /**
         * Cart validated, check if exists a discount
         */
        if ($fee_cart->isVoucher()) {
            return self::addVoucher($fee_cart, $module, $payment_type_display);
        }
        
        /**
         * Sets extra_vars
         */
        $extra_vars = array();
        /**
         * Set fee to cart
         */
        self::removeFeeFromCart();
        self::addFeeToCart($cart, $fee_cart, $product_fee);
        
        /**
         * Validate order
         */
        if ($module->validateOrder(
                $cart->id,
                $fee_cart->payment->id_order_state,
                $cart->getOrderTotal(true, Cart::BOTH),
                $payment_type_display,
                null,
                $extra_vars,
                (int)$currency->id,
                false,
                $customer->secure_key)) {
            self::redirect($payment_type,$summary,$module, $transaction_id);
            return true;
        } else {
            print $module->l('Error during Cart validation', 'classValidation');
        }
        return false;
    }
    
    /**
     * Create a Voucher to add a cart discount
     * @author Massimiliano Palermo <maxx.palermo@gmail.com>
     * @param classCart $bank
     * @param ModuleCore $module
     * @return boolean True if success, false otherwise
     */
    public static function addVoucher($bank, $module, $payment_type_display)
    {
        /**
         * @var ClassMpPaymentConfiguration $payment
         */
        $payment = $bank->payment;
        $id_cart = ContextCore::getContext()->cart->id;
        $cart = new CartCore($id_cart);
        $date = date('Y-m-d h:i:s');
        $voucher = new CartRuleCore();
        $voucher->id_customer = $cart->id_customer;
        $voucher->date_from = $date;
        $voucher->date_to = date('Y-m-d h:i:s', strtotime($date. ' + 1 days'));
        $voucher->description = 'Cart reference: ' . $id_cart;
        $voucher->quantity = 1;
        $voucher->reduction_percent = $payment->discount;
        $voucher->reduction_currency = 1;
        $voucher->reduction_tax = 1;
        $voucher->partial_use = 0;
        $voucher->priority = 1;
        $voucher->minimum_amount_shipping = 0;
        $voucher->minimum_amount_currency = 1;
        $voucher->minimum_amount_tax = 1;
        $voucher->name[$cart->id_lang] = $module->l('Bankwire payment method discount', 'classValidation');
        if ($voucher->save()) {
            $voucher->autoAddToCart();
            /**
             * Create order
             */
            $customer = new CustomerCore($cart->id_customer);
            $extra_vars = array();
            $orderTotal = $bank->getTotalCart() + $bank->getShipping() - $bank->getDiscount();
            
            self::removeFeeFromCart();
            if ($module->validateOrder(
                $cart->id,
                $payment->id_order_state,
                $orderTotal,
                $payment_type_display,
                null,
                $extra_vars,
                (int)$cart->id_currency,
                false,
                $customer->secure_key)) {
            
                //$order_reference = self::getOrderReferenceByIdCart($cart->id);
                //self::deleteOrderPayment($order_reference);
                //self::UpdateOrderPayment($order_reference, $transaction_id);
                self::redirect(classCart::BANKWIRE, classSession::getSessionSummary(), $module, '');
                return true;
            } else {
                print $module->l('Error during Cart validation', 'classValidation');
            }
            return false;
        } else {
            return $module->l('Error during Voucher creation', 'classValidation');
        }
    }
    
    public static function deleteOrderPayment($reference)
    {
        $db = Db::getInstance();
        return $db->delete('order_payment', "order_reference = '" . pSQL($reference) . "'");
    }
    
    public static function UpdateOrderPayment($order_reference, $transaction_id)
    {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        $sql->select('id_order_payment')
                ->from('order_payment')
                ->where('order_reference = \'' . pSQL($order_reference) . '\'');
        $id_order_payment = (int)$db->getValue($sql);
        if($id_order_payment>0)
        {
            $order_payment = new OrderPaymentCore($id_order_payment);
            $order_payment->transaction_id = $transaction_id;
            $order_payment->update();
        }
    }
    
    public static function redirect($payment_type, $summary, $module, $transaction_id)
    {
        $order = new OrderCore($module->currentOrder);
        $link = new LinkCore();
        $url = '';
        
        if ($payment_type == ClassMpPayment::CASH) {
            //Redirect on order confirmation page
            $url = $link->getModuleLink('mpadvpayment', 'cashReturn', array('id_order' => $order->id));
        } elseif ($payment_type == ClassMpPayment::BANKWIRE) {
            //Redirect on order confirmation page
            $url = $link->getModuleLink('mpadvpayment', 'bankwireReturn', array('id_order' => $order->id));
        } elseif ($payment_type == ClassMpPayment::PAYPAL) {
            //Redirect on order confirmation page
            $url = '';
            return true;
        }
        /**
         * Remove summary variable from Session
         */
        //classSession::delSessionSummary();
        /**
         * Clean cart from fees
         */
        self::removeFeeFromCart();
        /**
         * Redirect to confirmation page
         */
        if (!empty($url)) {
            Tools::redirect($url);
        }
    }
    
    public static function removeFeeFromCart()
    {
        /**
         * Get cart
         */
        $cart = new CartCore(ContextCore::getContext()->cart->id);
        /**
         * Get fee and discount virtual products
         */
        $product_fee = new ProductCore(self::getProductIdByReference('fee'));
        $product_discount = new ProductCore(self::getProductIdByReference('discount'));
        /**
         * Remove old fee and discount from cart
         */
        foreach ($cart->getProducts() as $product) {
            if ($product['id_product'] == $product_fee->id) {
                Context::getContext()->cart->deleteProduct($product_fee->id);
            }
        }
    }
    
    /**
     * Add a fee as a virtual product into cart
     * @param CartCore $cart current cart
     * @param classCart $fee_cart fee cart class
     * @param ProductCore $product_fee virtual product associated to fee
     * @return boolean True if success, false otherwise
     */
    public static function addFeeToCart($cart, $fee_cart, $product_fee)
    {
        /**
         * Add new fee or discount to cart
         * @var classCart $fee_cart
         * @return boolean True if success, false otherwise
         */
        if ($fee_cart->getFee() == 0) {
            return false;
        }
           
        $result = $cart->updateQty(1, $product_fee->id); // add fee to cart
        if ($result===true) {
            $product_fee->price = $fee_cart->getFeeNoTax();
            $product_fee->update();
        } else {
            return false;
        }
        
        return true;
    }
}
