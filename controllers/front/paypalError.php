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
        . DIRECTORY_SEPARATOR . 'classMpPaymentCalc.php';

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..'
        . DIRECTORY_SEPARATOR . '..'
        . DIRECTORY_SEPARATOR . 'classes'
        . DIRECTORY_SEPARATOR . 'classMpPaypal.php';

class MpAdvPaymentPaypalErrorModuleFrontController extends ModuleFrontControllerCore
{
    public $ssl = true;
    
    public function initContent()
    {
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::initContent();
        
        $id_order = Tools::getValue('id_order', 0);
        $order = new OrderCore($id_order);
        
        $token = Tools::getValue('token', '');
        
        if ($token) {
            context::getContext()->smarty->assign("function", 'module paypalerror');
            context::getContext()->smarty->assign("paypal_params", array('TOKEN' => $token));
            context::getContext()->smarty->assign("transaction_id", Tools::getValue('transaction_id', ''));
            context::getContext()->smarty->assign("order", $order);
            context::getContext()->smarty->assign("paypal_error", array());
            
            $paypal = new classMpPaypal();
            $result = $paypal->request('GetExpressCheckoutDetails', array('TOKEN' => $token));
            if ($result) {
                context::getContext()->smarty->assign("paypal_error", $paypal->getResponse());
                switch ($paypal->getStatus()) {
                    case 'PaymentActionNotInitiated':
                        context::getContext()->smarty->assign("paypal_error_message", $this->module->l('Transaction cancelled by user', 'paypalerror'));
                        break;
                    default:
                        context::getContext()->smarty->assign("paypal_error_message", $this->module->l('Unknown error during redirecting', 'paypalerror'));
                        break;
                }
            } else {
                context::getContext()->smarty->assign("paypal_error_message", $this->module->l('Unknown error during redirecting', 'paypalerror'));
            }
        }
        $this->setTemplate('paypal_error.tpl');
    }
}
