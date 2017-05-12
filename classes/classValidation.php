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
     * Finalize cart and convert it to an order,
     * save extra info bill
     */
    public static function FinalizeOrder($payment_type,$transaction_id,$module)
    {
        /**
         * @var classSummary $summary
         */
        $summary = classSession::getSessionSummary();
        
        /**
         * @var ClassMpPaymentConfiguration $payment
         */
        $payment = new ClassMpPaymentConfiguration();
        
        $payment->read($payment_type);
        
        $pay_cart = new classCart(0, '');
        
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
        
        //Check if cart exists
        /** @var CartCore $cart */
        $cart = new Cart($summary->id_cart);
        if ($cart->id_customer == 0
                || $cart->id_address_delivery == 0
                || $cart->id_address_invoice == 0
                || !$module->active) {
            var_dump($cart);
            //Tools::redirect('index.php?controller=order&step=1');
        }
        
        //Check if module is enabled
        /** @var bool $authorized */
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
        
        //Check if customer exists
        $customer = new CustomerCore($cart->id_customer);
        if (!ValidateCore::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
        }
        
        //Sets data
        $currency = Context::getContext()->currency;
        $extra_vars = array();
        
        //Validate order
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
            
            //Update order
            $order_id = $module->currentOrder;
            $order = new OrderCore($order_id);
            $order->total_paid = number_format($pay_cart->total_tax_incl, 6);
            $order->total_paid_tax_incl = number_format($pay_cart->total_tax_incl, 6);
            $order->total_paid_tax_excl = number_format($pay_cart->total_tax_excl, 6);
            $order->total_paid_real = number_format($pay_cart->total_tax_incl, 6);
            $order->update();
            
            //Update order payment
            if (self::deleteOrderPayment($order->reference)) {
                $orderPayment = new OrderPaymentCore();
                $orderPayment->amount = $order->total_paid;
                $orderPayment->id_currency = (int)$currency->id;
                $orderPayment->order_reference = $order->reference;
                $orderPayment->payment_method = $payment_type;
                $orderPayment->transaction_id = $transaction_id;
                $orderPayment->save();
            } else {
                // NO PAYMENT
            }
            
            //Save extra data
            $id_order = OrderCore::getOrderByCartId($cart->id);
            $classExtra = new ClassMpPaymentOrders();
            $classExtra->id_cart = $cart->id;
            $classExtra->id_order = $id_order;
            $classExtra->payment_type = $payment_type;
            $classExtra->total_amount = number_format($order->total_paid_tax_excl, 6);
            $classExtra->tax_rate = number_format($pay_cart->payment->tax_rate, 6);
            if($pay_cart->payment->fee_type==classCart::FEE_TYPE_DISCOUNT) {
                $classExtra->discounts = number_format($pay_cart->total_discount_without_taxes, 6);
                $classExtra->fees = null;
            } else {
                $classExtra->discounts = null;
                $classExtra->fees = number_format($summary->paypal->cart->total_fee_without_taxes, 6);
            }
            $classExtra->transaction_id = $transaction_id;
            $classExtra->save();
            
            self::redirect($payment_type,$summary,$module, $transaction_id);
            
        } else {
            print "ERROR during cart convalidation.";
            //ERROR
        }
        
        classSession::delSessionSummary();
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
        $customer = new CustomerCore($order->id_customer);
        $link = new LinkCore();
        
        if ($payment_type == ClassMpPayment::CASH) {
            //Redirect on order confirmation page
            Tools::redirect('index.php?controller=order-confirmation'
                    .'&idcart='.$summary->cash->cart->getId()
                    .'&id_module='.$module->id
                    .'&id_order='.$module->currentOrder
                    .'&key='.$customer->secure_key);
        } elseif ($payment_type == ClassMpPayment::BANKWIRE) {
            //Redirect on order confirmation page
            $url = $link->getModuleLink('mpadvpayment', 'bankwireReturn', array('id_order' => $order->id));
            Tools::redirect($url);
        } elseif ($payment_type == ClassMpPayment::PAYPAL) {
            //Redirect on order confirmation page
            $params = array(
                'id_order' => $order->id,
                'transaction_id' => $transaction_id,
                'success' => 1,
                    );
            $link = new LinkCore();
            $url = $link->getModuleLink('mpadvpayment', 'card', $params);
            Tools::redirect($url);
        }
    }
}
