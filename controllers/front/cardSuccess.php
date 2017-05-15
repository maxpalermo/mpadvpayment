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
class MpAdvPaymentCardSuccessModuleFrontController extends ModuleFrontControllerCore{
    public $ssl = true;
    
    public function initContent()
    {
        $this->display_column_left = false;
        $this->display_column_right = false;
        /**
         * INITIALIZE CLASS
         */
        parent::initContent();
        
        $summary = classSession::getSessionSummary();
        $transaction_id = Tools::getValue('tx','xxxxxxxxxxxx');
        $id_cart = Context::getContext()->cart->id;

        /**
         * FINALIZE ORDER
         */
        classValidation::FinalizeOrder(classCart::PAYPAL, $transaction_id, $this->module);

        /**
         * Get order reference from cart
         */
        $id_order = classValidation::getOrderIdByIdCart($id_cart);
        $order_reference = classValidation::getOrderReferenceByIdCart($id_cart);

        //Show success page
        $this->context->smarty->assign("order_id",$id_order);
        $this->context->smarty->assign("order_reference",$order_reference);
        $this->context->smarty->assign("transaction_id",$transaction_id);
        $this->context->smarty->assign("total",$summary->paypal->cart->getTotalToPay());
        $this->setTemplate("card_success.tpl");
    }
}
