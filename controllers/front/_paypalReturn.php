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

class MpAdvPaymentPaypalReturnModuleFrontController extends ModuleFrontControllerCore
{
    public $ssl = true;
    private $transaction_id;
    private $cart_id;
    private $order_id;
    private $lang;
    private $order_reference;
    
    public function initContent()
    {
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::initContent();
        
        $this->order_id = Tools::getValue('id_order', 0);
        $this->transaction_id = Tools::getValue('transaction_id', '');
        $this->cart_id = Context::getContext()->cart->id;
        $this->lang = Context::getContext()->language->id;
        
        $this->FinalizeOrder();
        
        context::getContext()->smarty->assign("order_id", $this->order_id);
        context::getContext()->smarty->assign("order_reference", $this->order_reference);
        context::getContext()->smarty->assign("transaction_id", $this->transaction_id);
        
        $this->setTemplate('paypal_success.tpl');
    }
    
    /**
     * Finalize cart and convert it to an order,
     * save extra info bill
     */
    private function FinalizeOrder()
    {
        /**
         * @var classSummary $summary
         */
        $summary = classSession::getSessionSummary();
        /**
         * @var ClassMpPaymentConfiguration $payment
         */
        $payment = new ClassMpPaymentConfiguration();
        
        $payment->read(classCart::PAYPAL);
        
        //Check if cart exists
        /** @var CartCore $cart */
        $cart = new Cart($this->cart_id);
        if ($cart->id_customer == 0
                || $cart->id_address_delivery == 0
                || $cart->id_address_invoice == 0
                || !$this->module->active) {
            Tools::redirect('index.php?controller=order&step=1');
        }
        
        //Check if module is enabled
        /** @var bool $authorized */
        $authorized = false;
        foreach (ModuleCore::getPaymentModules() as $module) {
            if ($module['name'] == $this->module->name) {
                $authorized = true;
                break;
            }
        }
        
        if (!$authorized) {
            die($this->l('This payment method is not available.'));
        }
        
        //Check if customer exists
        $customer = new CustomerCore($cart->id_customer);
        if (!ValidateCore::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
        }
        
        //Sets data
        $currency = $this->context->currency;
        $total = $summary->paypal->cart->getTotalCart();
        $extra_vars = array();
        
        //Validate order
        if ($this->module->validateOrder(
                $cart->id,
                $this->paymentConfig->id_order_state,
                $total,
                $this->payment_display,
                null,
                $extra_vars,
                (int)$currency->id,
                false,
                $customer->secure_key)) {
            //Get Extra data
            $classPaymentFee = new ClassMpPayment();
            $classPaymentFee->calculateFee(classCart::PAYPAL);
            
            $payment_type = $this->module->l('Paypal', 'validation');
            
            //Update order
            $this->order_id = $this->module->currentOrder;
            
            
            $order = new OrderCore($this->order_id);
            $this->order_reference = $order->reference;
            
            $order->payment = $payment_type;
            $order->total_paid = number_format($order->total_paid +  $classPaymentFee->total_fee_with_taxes, 6);
            $order->total_paid_tax_incl = number_format($order->total_paid_tax_incl + $classPaymentFee->total_fee_with_taxes, 6);
            $order->total_paid_tax_excl = number_format($order->total_paid_tax_excl + $classPaymentFee->total_fee_without_taxes, 6);
            $order->total_paid_real = $order->total_paid;
            $order->update();
            
            //Update order payment
            if ($this->deleteOrderPayment($order->reference)) {
                $orderPayment = new OrderPaymentCore();
                $orderPayment->amount = $order->total_paid;
                $orderPayment->id_currency = Context::getContext()->currency->id;
                $orderPayment->order_reference = $order->reference;
                $orderPayment->payment_method = classCart::PAYPAL;
                $orderPayment->transaction_id = $this->transaction_id;
                $orderPayment->save();
            } else {
                // NO PAYMENT
            }
            
            //Save extra data
            $id_order = OrderCore::getOrderByCartId($cart->id);
            $classExtra = new ClassMpPaymentOrders();
            $classExtra->id_cart = $cart->id;
            $classExtra->id_order = $id_order;
            $classExtra->payment_type = $this->payment_method;
            $classExtra->total_amount = number_format($order->total_paid_tax_excl, 6);
            $classExtra->tax_rate = number_format($this->paymentConfig->tax_rate, 6);
            $classExtra->fees = number_format($classPaymentFee->total_fee_without_taxes, 6);
            $classExtra->transaction_id = $this->transaction_id;
            $classExtra->save();
            
        } else {
            print "ERROR during cart convalidation.";
            //ERROR
        }
        
        classSession::delSessionSummary();
    }
    
    private function deleteOrderPayment($reference)
    {
        $db = Db::getInstance();
        return $db->delete('order_payment', "order_reference = '" . pSQL($reference) . "'");
    }
}
