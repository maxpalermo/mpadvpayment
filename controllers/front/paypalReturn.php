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

class MpAdvPaymentPaypalReturnModuleFrontController extends ModuleFrontControllerCore 
{
    public function initContent() {
        $this->display_column_left = false;
        $this->display_column_right = false;
        /**
         * INITIALIZE CLASS
         */
        parent::initContent();
        
        $transaction_id = Tools::getValue('transaction_id', '');
        
        /**
        * Get order reference from cart
        */
        $id_cart = Tools::getValue('id_cart', 0);
        $id_order = Tools::getValue('id_order', 0);
        classMpLogger::blank();
        classMpLogger::addEvidencedMsg('PAYPAL RETURN id_order: ' . (int)$id_order);
        classMpLogger::blank();
        classMpLogger::add('Updating order total: ' . (int)classValidation::updateOrder((int)$id_order));
        classMpLogger::add('Updating payment: ' . (int)classValidation::updateOrderPayment((int)$id_order, $transaction_id));
        classMpLogger::add('Updating invoice: ' . (int)classValidation::updateInvoice((int)$id_order));
        classMpLogger::add('Updating status: ' . (int)classValidation::setOrderState((int)$id_order, classCart::PAYPAL));
        classMpLogger::add('Updating module payment: ' 
                . (int)classValidation::updateOrderPaymentModule($id_order, $this->module->l('paypal')));

        $order_reference = classValidation::getOrderReferenceByIdCart($id_cart);
        
        $total_paid = Tools::getValue('total_paid', 0);
        //Show success page
        $this->context->smarty->assign("order_id",$id_order);
        $this->context->smarty->assign("order_reference",$order_reference);
        $this->context->smarty->assign("transaction_id",$transaction_id);
        $this->context->smarty->assign("total",$total_paid);
        $this->setTemplate("cardSuccess.tpl");
    }
}