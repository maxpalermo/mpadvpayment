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
        return (int)$db->getValue($sql);
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
         * @var classCart $fee_summary
         */
        $summary = classSession::getSessionSummary();
        if($payment_type == classCart::CASH) {
            $fee_summary = $summary->cash->cart;
        } elseif ($payment_type == classCart::BANKWIRE) {
            $fee_summary = $summary->bankwire->cart;
        } elseif ($payment_type == classCart::PAYPAL) {
            $fee_summary = $summary->paypal->cart;
        }
        /**
         * Get fee and discount virtual products
         */
        $product_fee = new ProductCore(self::getProductIdByReference('fee'));
        $product_discount = new ProductCore(self::getProductIdByReference('discount'));
        /**
         * Get Cart
         * @var CartCore $cart
         */
        $cart = new Cart($summary->id_cart);
        /**
         * Set display payment
         */
        if ($payment_type == classCart::CASH) {
            $pay_cart = $summary->cash->cart;
            $payment_type_display = $module->l('Cash', 'classValidation');
        } elseif ($payment_type == classCart::BANKWIRE) {
            $pay_cart = $summary->bankwire->cart;
            $payment_type_display = $module->l('Bankwire', 'classValidation');
        } elseif ($payment_type == classCart::PAYPAL) {
            $pay_cart = $summary->paypal->cart;
            $payment_type_display = $module->l('Paypal', 'classValidation');
        } else {
            Tools::d($summary);
        }
        /**
         * Validate cart
         */
        if ($cart->id_customer == 0
                || $cart->id_address_delivery == 0
                || $cart->id_address_invoice == 0
                || !$module->active) {
            var_dump($cart);
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
         * Sets extra_vars
         */
        $extra_vars = array();
        /**
         * Set fee to cart
         */
        self::removeFeeFromCart();
        self::addFeeToCart($cart, $fee_summary, $product_fee, $product_discount);
        
        /**
         * Validate order
         */
        if ($module->validateOrder(
                $cart->id,
                $pay_cart->payment->id_order_state,
                $cart->getOrderTotal(true, Cart::BOTH),
                $payment_type_display,
                null,
                $extra_vars,
                (int)$currency->id,
                false,
                $customer->secure_key)) {
            
            $order_reference = self::getOrderReferenceByIdCart($cart->id);
            //self::deleteOrderPayment($order_reference);
            //self::UpdateOrderPayment($order_reference, $transaction_id);
            self::redirect($payment_type,$summary,$module, $transaction_id);
            return true;
        } else {
            print "ERROR during cart convalidation.";
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
            if ($product['id_product'] == $product_discount->id) {
                Context::getContext()->cart->deleteProduct($product_discount->id);
            }
        }
    }
    
    public static function addFeeToCart($cart, $fee_summary, $product_fee, $product_discount)
    {
        /**
         * Add new fee or discount to cart
         * @var classCart $fee_summary
         */
        if ($fee_summary->getDiscount() == 0) {
            //$Cart->updateQty($quantity, $id_product, $id_product_attribute = null, $id_customization = false, $operator = 'up', $id_address_delivery = 0, $shop = null, $auto_add_cart_rule = true);
            $result = $cart->updateQty(1, $product_fee->id);
            if ($result===true) {
                $product_fee->price = $fee_summary->getFee();
                $product_fee->update();
            }
        } else {
            //$Cart->updateQty($quantity, $id_product, $id_product_attribute = null, $id_customization = false, $operator = 'up', $id_address_delivery = 0, $shop = null, $auto_add_cart_rule = true);
            $result = $cart->updateQty(1, $product_discount->id);
            if ($result===true) {
                Db::getInstance()->update(
                        'product',
                        array('price'=>$fee_summary->getDiscount()),
                        'id_product = ' . (int)$product_discount->id);
            }
        }
    }
}
