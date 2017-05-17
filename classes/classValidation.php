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
        classMpLogger::add('');
        classMpLogger::add('');
        classMpLogger::add('********************** ');
        classMpLogger::add('*** START FUNCTION *** ');
        classMpLogger::add('********************** ');
        $summary = classSession::getSessionSummary();
        
        if($payment_type == classCart::CASH) {
            $fee_cart = $summary->cash->cart;
            $payment_type_display = $module->l('Cash', 'classValidation');
            classMpLogger::add('payment selected: CASH');
        } elseif ($payment_type == classCart::BANKWIRE) {
            $fee_cart = $summary->bankwire->cart;
            $payment_type_display = $module->l('Bankwire', 'classValidation');
            classMpLogger::add('payment selected: BANKWIRE');
        } elseif ($payment_type == classCart::PAYPAL) {
            $fee_cart = $summary->paypal->cart;
            $payment_type_display = $module->l('Paypal', 'classValidation');
            classMpLogger::add('payment selected: PAYPAL');
        }
        /**
         * Get Cart
         */
        $cart = Context::getContext()->cart;
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
        $id_voucher=0;
        if ($fee_cart->isVoucher()) {
            $id_voucher = self::addVoucher($fee_cart, $module);
            if((int)$id_voucher==0) {
                print $id_voucher;
            }
        } else {
            /**
             * Set fee to cart
             */
            classMpLogger::add('*** REMOVE product from CART ' . $cart->id);
            self::removeFeeFromCart($cart->id);
            classMpLogger::add('*** ADD new fee product to CART ' . $cart->id);
            self::addFeeToCart($cart->id, $fee_cart);
        }
        
        $id_order_state = $fee_cart->payment->id_order_state;
        classMpLogger::add('Get id order state: ' . $id_order_state);
        if(self::createOrder($module, $cart->id, $id_order_state, $payment_type_display, $customer->secure_key)) {
            classMpLogger::add('Order created.');
            self::redirect($payment_type,$summary,$module, $transaction_id);
        } else {
            classMpLogger::add('Error during Order creation.');
            print $module->l('Error during Order creation.', 'classValidation');
        }
    }
    
    /**
     * Create a Voucher to add a cart discount
     * @author Massimiliano Palermo <maxx.palermo@gmail.com>
     * @param classCart $bank
     * @param ModuleCore $module
     * @return mixed Voucher id or an error message
     */
    public static function addVoucher($bank, $module)
    {
        /**
         * @var ClassMpPaymentConfiguration $payment
         */
        $payment = $bank->payment;
        $id_cart = ContextCore::getContext()->cart->id;
        $cart = new Cart($id_cart);
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
            classMpLogger::add('Voucher ' . $voucher->id . ' created for CART ' . $id_cart);
            $cart->addCartRule($voucher->id);
            return $voucher->id;
        } else {
            classMpLogger::add('Error during Voucher creation.');
            return $module->l('Error during Voucher creation', 'classValidation');
        }
        Context::getContext()->cart = $cart;
    }
    
    public static function createOrder($module, $id_cart, $id_order_state, $payment_method, $secure_key)
    {
        classMpLogger::add('*** START FUNCTION');
        
        $extra_vars = array();
        $currency = Context::getContext()->currency;
        $cart = new Cart($id_cart);
        Context::getContext()->cart = $cart;
        
        /**
         * Validate order
         */
        $result = $module->validateOrder(
                $cart->id,
                $id_order_state,
                $cart->getOrderTotal(true, Cart::BOTH),
                $payment_method,
                null,
                $extra_vars,
                (int)$currency->id,
                false,
                $secure_key);
        classMpLogger::add('Validate order returns ' . $result);
        
        if($result) {
            self::removeFeeFromCart($cart->id);
            return true;
        } else {
            print $module->l('Error during Cart validation', 'classValidation');
        }
    }
    
    public static function redirect($payment_type, $summary, $module, $transaction_id)
    {
        $order = new OrderCore($module->currentOrder);
        classMpLogger::add('Get order ' . $module->currentOrder);
        $link = new LinkCore();
        $url = '';
        
        if ($payment_type == ClassMpPayment::CASH) {
            //Redirect on order confirmation page
            $url = $link->getModuleLink('mpadvpayment', 'cashReturn', array('id_order' => $order->id));
            classMpLogger::add('Created URL redirect for CASH: ' . $url);
        } elseif ($payment_type == ClassMpPayment::BANKWIRE) {
            //Redirect on order confirmation page
            $url = $link->getModuleLink('mpadvpayment', 'bankwireReturn', array('id_order' => $order->id));
            classMpLogger::add('Created URL redirect for BANKWIRE: ' . $url);
        } elseif ($payment_type == ClassMpPayment::PAYPAL) {
            //Redirect on order confirmation page
            $params = array(
                'id_order' => $order->id,
                'transaction_id' => $transaction_id,
                'id_cart' => ContextCore::getContext()->cart->id,
                'total_paid' => $summary->paypal->cart->getTotalToPay()
            );
            $url = $link->getModuleLink('mpadvpayment', 'paypalReturn', $params);
            classMpLogger::add('Created URL redirect for PAYPAL: ' . $url);
        }
        /* 
         * Redirect to confirmation page
         */
        if (!empty($url)) {
            classMpLogger::add('Redirecting to: ' . $url);
            Tools::redirect($url);
        }
    }
    
    public static function removeFeeFromCart($id_cart)
    {
        /**
         * Get cart
         */
        $cart = new Cart($id_cart);
        $fees = array();
        /**
         * Get fee virtual product
         */
        $fees[] = (int)self::getProductIdByReference('fee_cash');
        $fees[] = (int)self::getProductIdByReference('fee_paypal');
        /**
         * Remove old fee from cart
         */
        foreach ($cart->getProducts() as $product) {
            if (in_array($product['id_product'], $fees)) {
                $cart->deleteProduct($product['id_product']);
                classMpLogger::add('Removed product id code: ' . $product['id_product'] . " from CART " . $id_cart);
            }
        }
        
        Context::getContext()->cart = $cart;
    }
    
    /**
     * Add a fee as a virtual product into cart
     * @param Cart $cart current cart
     * @param classCart $fee_cart fee cart class
     * @param ProductCore $product_fee virtual product associated to fee
     * @return boolean True if success, false otherwise
     */
    public static function addFeeToCart($id_cart, $fee_cart)
    {
        $cart = new Cart($id_cart);
        
        if ($fee_cart->getFee() == 0) {
            classMpLogger::add('Error on fee amount.' . $cart->id);
            return false;
        }
        classMpLogger::add('Fee amount: ' . $fee_cart->getFee());
        /**
         * Get fee virtual product
         */
        if($fee_cart->getPaymentType()==classCart::CASH) {
            $product_fee = new ProductCore(self::getProductIdByReference('fee_cash'));
            classMpLogger::add('Trying to add fee product with CASH payment method');
            classMpLogger::add('Get product ' . $product_fee->reference);
        } else {
            $product_fee = new ProductCore(self::getProductIdByReference('fee_paypal'));
            classMpLogger::add('Trying to add fee product with PAYPAL payment method');
            classMpLogger::add('Get product ' . $product_fee->reference);
        }
        
        $db = Db::getInstance();
        $result = $db->insert('cart_product', array(
            'id_cart' => $cart->id,
            'id_product' => $product_fee->id,
            'id_address_delivery' => $cart->id_address_delivery,
            'id_shop' => ContextCore::getContext()->shop->id,
            'id_product_attribute' => 0,
            'quantity' => 1,
            'date_add' => date('Y-m-d h:i:s')
        ));
        
        classMpLogger::add('Add product ' . $product_fee->id . ' to CART ' . $id_cart . ': result ' . (int)$result);
        classMpLogger::add('Cart details: ' . print_r($cart->getProducts(), 1));
        
        //$result = $cart->updateQty(1, $product_fee->id); // add fee to cart
        if ($result==true) {
            $product_fee->price = $fee_cart->getFeeNoTax();
            $product_fee->update();
            classMpLogger::add('Update product ' . $product_fee->reference . ' price to ' . $product_fee->price);
        } else {
            classMpLogger::add('Error during product insertion into CART ' . $id_cart);
            classMpLogger::add('***RETURN FALSE');
            return false;
        }
        
        classMpLogger::add('Function SUCCESS.');
        classMpLogger::add('***RETURN TRUE');
        Context::getContext()->cart = $cart;
        return true;
    }
}
