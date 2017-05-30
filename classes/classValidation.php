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
     * Retrieve order payment id from order reference
     * @param string $reference product reference
     * @return int product id
     */
    public static function getOrderPaymentIdByReference($reference)
    {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        $sql->select('id_order_payment')
                ->from('order_payment')
                ->where('order_reference = \'' . pSQL($reference) . '\'');
        return (int)$db->getValue($sql);
    }
    
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
            $summary_cart = $summary->cash->cart;
            $payment_type_display = $module->l('Cash', 'classValidation');
            classMpLogger::add('payment selected: CASH');
        } elseif ($payment_type == classCart::BANKWIRE) {
            $summary_cart = $summary->bankwire->cart;
            $payment_type_display = $module->l('Bankwire', 'classValidation');
            classMpLogger::add('payment selected: BANKWIRE');
        } elseif ($payment_type == classCart::PAYPAL) {
            $summary_cart = $summary->paypal->cart;
            $payment_type_display = $module->l('Paypal', 'classValidation');
            classMpLogger::add('payment selected: PAYPAL');
        }
        /**
         * Get Cart
         */
        $cart = Context::getContext()->cart;
        
        /**************
         * VALIDATION *
         **************/
        if (!self::validateCart($cart, $module)) {
            classMpLogger::add('ERROR DURING VALIDATION');
            classMpLogger::add('CART DUMP:');
            classMpLogger::add(var_dump($cart));
            Tools::redirect('index.php?controller=order&step=1');
        }
        
        if (!self::checkValidPaymentMethod($module)) {
            classMpLogger::add('ERROR DURING VALIDATION');
            classMpLogger::add('Payment not available:');
            Tools::d($module->l('This payment method is not available.', 'classValidation'));
        }
        
        if (!self::checkCustomer($cart)) {
            classMpLogger::add('ERROR DURING CUSTOMER VALIDATION');
            classMpLogger::add('CUSTOMER DUMP:');
            classMpLogger::add(var_dump(new CustomerCore($cart->id_customer)));
            Tools::redirect('index.php?controller=order&step=1');
        }
        
        $id_order_state = $summary_cart->payment->id_order_state;
        $customer = new CustomerCore($cart->id_customer);
        classMpLogger::add('Get id order state: ' . $id_order_state);
        
        $id_order = self::createOrder($module, $cart->id, $id_order_state, $payment_type_display, $customer->secure_key);
        if($id_order) {
            classMpLogger::add('Order created.');
            
            $payment = new ClassPaymentFee();
            $payment->create($id_order, $payment_type, $summary_cart->getFee(), $summary_cart->getFeeTaxRate());
            $result = $payment->insert();
            
            if ($result) {
                //update Payment
                $order_reference = self::getOrderReferenceByIdCart($cart->id);
                $id_payment = self::getOrderPaymentIdByReference($order_reference);
                if ($id_payment) {
                    $orderPay = new OrderPaymentCore($id_payment);
                    $orderPay->amount = $payment->getTotal_document();
                    $orderPay->save();
                }
                self::redirect($payment_type,$module, $transaction_id, $payment);
            } else {
                classMpLogger::add('ERROR DURING FEE INSERT');
                Tools::d(
                        $module->l(
                                'ERROR DURING FEE INSERT, please contact our customer care. Order id: ',
                                'classValidation'
                                ) . $id_order
                        );
            }
        } else {
            classMpLogger::add('Error during Order creation.');
            Tools::d(
                    $module->l(
                            'Error during Order creation. Please contact our customer care. Cart id: '
                            , 'classValidation'
                            ) . $cart->id
                    );
        }
    }
    
    private static function checkCustomer($cart)
    {
        /**
         * Validate customer
         */
        $customer = new CustomerCore($cart->id_customer);
        if (!ValidateCore::isLoadedObject($customer)) {
            return false;
        }
        return true;
    }
    
    private static function checkValidPaymentMethod($module)
    {
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
        
        return $authorized;
    }
    
    private static function validateCart($cart, $module)
    {
         /**
         * Validate cart
         */
        if (
                $cart->id_customer == 0
                || $cart->id_address_delivery == 0
                || $cart->id_address_invoice == 0
                || !$module->active) {
            return false;
                } else {
            return true;
        }
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
            return self::getOrderIdByIdCart($cart->id);
        } else {
            print $module->l('Error during Cart validation', 'classValidation');
            return false;
        }
    }
    
    /**
     * Redirect to success page
     * @param string $payment_type
     * @param ModuleCore $module
     * @param string $transaction_id
     * @param ClassPaymentFee $payment
     */
    public static function redirect($payment_type, $module, $transaction_id, $payment)
    {
        $order = new OrderCore($module->currentOrder);
        classMpLogger::add('Get order ' . $module->currentOrder);
        $link = new LinkCore();
        $url = '';
        
        if ($payment_type == classMpPayment::CASH) {
            //Redirect on order confirmation page
            $url = $link->getModuleLink(
                    'mpadvpayment',
                    'cashReturn',
                    array(
                        'id_order' => $order->id,
                        'payment' => $payment
                    )
                );
            classMpLogger::add('Created URL redirect for CASH: ' . $url);
        } elseif ($payment_type == classMpPayment::BANKWIRE) {
            //Redirect on order confirmation page
            $url = $link->getModuleLink(
                    'mpadvpayment',
                    'bankwireReturn',
                    array(
                        'id_order' => $order->id,
                        'payment' => $payment
                    )
                );
            classMpLogger::add('Created URL redirect for BANKWIRE: ' . $url);
        } elseif ($payment_type == classMpPayment::PAYPAL) {
            //Redirect on order confirmation page
            $params = array(
                'id_order' => $order->id,
                'payment' => $payment,
                'transaction_id' => $transaction_id,
                'id_cart' => ContextCore::getContext()->cart->id,
                'total_paid' => $payment->getTotal_document(),
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
}
