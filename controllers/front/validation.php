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

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . ".."
        . DIRECTORY_SEPARATOR . ".."
        . DIRECTORY_SEPARATOR . "classes"
        . DIRECTORY_SEPARATOR . "autoload.php";

class MpAdvPaymentValidationModuleFrontController extends ModuleFrontControllerCore
{
    private $payment_method;
    private $payment_display;
    private $transaction_id;
    private $paymentConfig;
    
    public function postProcess()
    {
        //Set Class payment
        $this->paymentConfig = new ClassMpPaymentConfiguration();

        //Set params
        $this->payment_method   = Tools::getValue('payment_method');
        $this->payment_display  = Tools::getValue('payment_display');
        $this->transaction_id   = Tools::getValue('transaction_id');
        
        //Get configuration data
        $this->paymentConfig->read($this->payment_method);
        
        //Check if cart exists
        /** @var CartCore $cart */
        $cart = $this->context->cart;
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
            $classPaymentFee->calculateFee($this->payment_method, $cart);
            
            $objLang = new LanguageCore(Context::getContext()->language->id);
            
            
            //Payment type
            if ($this->payment_method==ClassMpPayment::CASH) {
                if ($objLang->iso_code=="it") {
                    $payment_type = "Contanti alla consegna";
                } else {
                    $payment_type = $this->module->l('Adv Payment: Cash', 'validation');
                }
            } elseif ($this->payment_method==ClassMpPayment::BANKWIRE) {
                if ($objLang->iso_code=="it") {
                    $payment_type = "Bonifico bancario anticipato";
                } else {
                    $payment_type = $this->module->l('Adv Payment: Bankwire', 'validation');
                }
            } elseif ($this->payment_method==ClassMpPayment::PAYPAL) {
                if ($objLang->iso_code=="it") {
                    $payment_type = "Pagamento tramite Paypal";
                } else {
                    $payment_type = $this->module->l('Adv Payment: Paypal', 'validation');
                }
            } else {
                if ($objLang->iso_code=="it") {
                    $payment_type = "Pagamento sconosciuto";
                } else {
                    $payment_type = $this->module->l('Adv Payment: UNKNOWN', 'validation');
                }
            }
            
            //Update order
            $order = new OrderCore($this->module->currentOrder);
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
                $orderPayment->payment_method = $payment_type;
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
            
            if ($this->payment_method == ClassMpPayment::CASH) {
                //Redirect on order confirmation page
                Tools::redirect('index.php?controller=order-confirmation'
                        .'&idcart='.$cart->id
                        .'&id_module='.$this->module->id
                        .'&id_order='.$this->module->currentOrder
                        .'&key='.$customer->secure_key);
            } elseif ($this->payment_method == ClassMpPayment::BANKWIRE) {
                //Redirect on order confirmation page
                $link = new LinkCore();
                $url = $link->getModuleLink('mpadvpayment', 'bankwireReturn', array('id_order' => $this->module->currentOrder));
                Tools::redirect($url);
                /*
                Tools::redirect('index.php?controller=mpadv'
                        .'&idcart='.$cart->id
                        .'&id_module='.$this->module->id
                        .'&id_order='.$this->module->currentOrder
                        .'&key='.$customer->secure_key);
                 *
                 */
            } elseif ($this->payment_method == ClassMpPayment::PAYPAL) {
                //Redirect on order confirmation page
                $params = array(
                    'id_order' => $this->module->currentOrder,
                    'transaction_id' => $this->transaction_id,
                        );
                $link = new LinkCore();
                $url = $link->getModuleLink('mpadvpayment', 'paypalReturn', $params);
                Tools::redirect($url);
            }
        } else {
            print "ERROR during cart convalidation.";
            //ERROR
        }
    }
    
    private function deleteOrderPayment($reference)
    {
        $db = Db::getInstance();
        return $db->delete('order_payment', "order_reference = '" . pSQL($reference) . "'");
    }
}
