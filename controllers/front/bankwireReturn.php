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

class MpAdvPaymentBankwireReturnModuleFrontController extends ModuleFrontControllerCore
{
    public $ssl = true;
    
    public function initContent()
    {
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::initContent();
        
        //Get session cart summary
        $summary = classSession::getSessionSummary();
        
        $id_order = Tools::getValue('id_order', 0);
        classMpLogger::add('Updating order total: ' . (int)classValidation::updateOrder($id_order));
        classMpLogger::add('Updating status: ' . (int)classValidation::setOrderState($id_order, classCart::BANKWIRE));
        $payment = new ClassPaymentFee();
        $payment->load($id_order);

        context::getContext()->smarty->assign("arr_details", $this->getBankwireDetails());
        context::getContext()->smarty->assign("amount", $payment->getTotal_document());
        context::getContext()->smarty->assign("classSummary", $summary);
        $this->setTemplate('displayBankwireReturn.tpl');
    }
    
    public function getBankwireDetails()
    {
        $det = new stdClass();
        $det->owner = ConfigurationCore::get("MP_ADVPAYMENT_BANKWIRE_OWNER");
        $det->iban  = ConfigurationCore::get("MP_ADVPAYMENT_BANKWIRE_IBAN");
        $det->bank  = ConfigurationCore::get("MP_ADVPAYMENT_BANKWIRE_BANK");
        $det->addr  = ConfigurationCore::get("MP_ADVPAYMENT_BANKWIRE_ADDR");
        
        return $det;
    }
}
