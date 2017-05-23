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

class MpAdvPaymentGetContentController
{   
    
    public function renderForm()
    {
        $smarty = Context::getContext()->smarty;
        $this->setMedia();
        
        $image_file = glob(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." 
            . DIRECTORY_SEPARATOR . ".."
            . DIRECTORY_SEPARATOR . "paypal_logo.*");
        if ($image_file) {
            //$filename = _PS_BASE_URL_ . __PS_BASE_URI__ . "/modules/mpadvpayment/" . basename($image_file[0]);
            $filename = _MPADVPAYMENT_URL_ . basename($image_file[0]);
            $smarty->assign('paypal_logo', $filename);
        } else {
            $smarty->assign('paypal_logo', '');
        }
        
        classMpTools::addSwitch('input_cash_switch', $this->l('Activate cash payment?', 'getContent'), $smarty);
        classMpTools::addSwitch('input_cash_switch_included_tax', $this->l('Tax included?', 'getContent'), $smarty);
        classMpTools::addSwitch('input_bankwire_switch', $this->l('Activate Bankwire payments?', 'getContent'), $smarty);
        classMpTools::addSwitch('input_bankwire_switch_included_tax', $this->l('Tax included?', 'getContent'), $smarty);
        classMpTools::addSwitch('input_paypal_switch', $this->l('Activate Paypal payment?', 'getContent'), $smarty);
        classMpTools::addSwitch('input_paypal_switch_included_tax', $this->l('Tax included?', 'getContent'), $smarty);
        classMpTools::addSwitch('input_paypal_switch_test', $this->l('Test sandbox?', 'getContent'), $smarty);
        classMpTools::addSwitch('input_paypal_switch_pro', $this->l('Activate Paypal Pro payments?', 'getContent'), $smarty);
        
        
        $smarty->assign('path', _MPADVPAYMENT_URL_);
        $smarty->assign('base_uri', __PS_BASE_URI__);
        $smarty->assign('tax_list', $this->getTaxList());
        $smarty->assign('carrier_list', $this->getCarrierList());
        $smarty->assign('categories_list', $this->getCategoriesList());
        $smarty->assign('manufacturers_list', $this->getManufacturersList());
        $smarty->assign('suppliers_list', $this->getSuppliersList());
        $smarty->assign('products_list', $this->getProductsList());
        $smarty->assign('order_state_list', $this->getOrderStateList());
        $smarty->assign('cash_values', $this->getCashValues());
        $smarty->assign('bankwire_values', $this->getBankwireValues());
        $smarty->assign('paypal_values', $this->getPaypalValues());
        $smarty->assign('ps_version', Tools::substr(_PS_VERSION_, 0, 3));
        $smarty->assign('form_cash', $smarty->fetch(_MPADVPAYMENT_TEMPLATES_HOOK_ . 'form_cash.tpl'));
        $smarty->assign('form_bankwire', $smarty->fetch(_MPADVPAYMENT_TEMPLATES_HOOK_ . 'form_bankwire.tpl'));
        $smarty->assign('form_paypal', $smarty->fetch(_MPADVPAYMENT_TEMPLATES_HOOK_ . 'form_paypal.tpl'));
        $smarty->assign('form_card', $smarty->fetch(_MPADVPAYMENT_TEMPLATES_HOOK_ . 'form_card.tpl'));
            
        $template  = $this->display(_MPADVPAYMENT_, 'getContent.tpl');
        //$psui_tags = $this->class->display(_MPADVPAYMENT_, 'views/templates/admin/prestui/ps-tags.tpl');
        return $template;
    }

    public function setMedia()
    {
        $controller = Context::getContext()->controller;
        $controller->addCSS(_MPADVPAYMENT_CSS_URL_ . 'getContent.css');
    }
    
    public function getTaxList()
    {
        $taxes = TaxCore::getTaxes($this->lang);
        $options = array();
        $options[] = "<option value='0'>" . $this->l('Please select', 'getContent') . "</option>";
        foreach ($taxes as $tax) {
            $options[] = "<option value='" . $tax['rate'] . "'>" . $tax['name'] . "</option>";
        }
        return implode("\n", $options);
    }
    
    public function getCarrierList()
    {
        $carriers = CarrierCore::getCarriers($this->lang);
        $options = array();
        foreach ($carriers as $carrier) {
            $options[] = "<option value='" . $carrier['id_carrier'] . "'>" . Tools::strtoupper($carrier['name']) . "</option>";
        }
        return implode("\n", $options);
    }
    
    public function getCategoriesList()
    {
        $categories = CategoryCore::getCategories($this->lang);
        $options = array();
        foreach ($categories as $category) {
            foreach ($category as $cat) {
                $options[] = "<option value='" . $cat['infos']['id_category'] . "'>" . Tools::strtoupper($cat['infos']['name']) . "</option>";
            }
        }
        return implode("\n", $options);
    }
    
    public function getManufacturersList()
    {
        $items = ManufacturerCore::getManufacturers();
        $options = array();
        foreach ($items as $item) {
            $options[] = "<option value='" . $item['id_manufacturer'] . "'>" . Tools::strtoupper($item['name']) . "</option>";
        }
        return implode("\n", $options);
    }
    
    public function getSuppliersList()
    {
        $items = SupplierCore::getSuppliers();
        $options = array();
        foreach ($items as $item) {
            $options[] = "<option value='" . $item['id_supplier'] . "'>" . Tools::strtoupper($item['name']) . "</option>";
        }
        return implode("\n", $options);
    }
    
    public function getProductsList()
    {
        $items = ProductCore::getSimpleProducts($this->lang);
        $options = array();
        foreach ($items as $item) {
            $options[] = "<option value='" . $item['id_product'] . "'>" . Tools::strtoupper($item['name']) . "</option>";
        }
        return implode("\n", $options);
    }
    
    public function getOrderStateList()
    {
        $items = OrderStateCore::getOrderStates($this->lang);
        $options = array();
        foreach ($items as $item) {
            $options[] = "<option value='" . $item['id_order_state'] . "'>" . Tools::strtoupper($item['name']) . "</option>";
        }
        return implode("\n", $options);
    }
    
    public function getCashValues()
    {
        $cash = new stdClass();
        $values = new classMpPaymentConfiguration();
        $values->read(classCart::CASH);
        
        $cash->input_switch_on      = $values->is_active;
        $cash->fee_type             = $values->fee_type;
        $cash->fee_amount           = $values->fee_amount;
        $cash->fee_percent          = $values->fee_percent;
        $cash->fee_min              = $values->fee_min;
        $cash->fee_max              = $values->fee_max;
        $cash->order_min            = $values->order_min;
        $cash->order_max            = $values->order_max;
        $cash->order_free           = $values->order_free;
        $cash->tax_included         = $values->tax_included;
        $cash->tax_rate             = number_format($values->tax_rate, 3);
        $cash->carriers             = $this->toArray($values->carriers);
        $cash->categories           = $this->toArray($values->categories);
        $cash->manufacturers        = $this->toArray($values->manufacturers);
        $cash->suppliers            = $this->toArray($values->suppliers);
        $cash->products             = $this->toArray($values->products);
        $cash->id_order_state       = $values->id_order_state;
        
        return $cash;
    }
    
    public function getBankwireValues()
    {
        $bankwire = new stdClass();
        $values = new classMpPaymentConfiguration();
        $values->read(classCart::BANKWIRE);
        
        $bankwire->input_switch_on      = $values->is_active;
        $bankwire->discount             = $values->discount;
        $bankwire->fee_type             = $values->fee_type;
        $bankwire->fee_amount           = $values->fee_amount;
        $bankwire->fee_percent          = $values->fee_percent;
        $bankwire->fee_min              = $values->fee_min;
        $bankwire->fee_max              = $values->fee_max;
        $bankwire->order_min            = $values->order_min;
        $bankwire->order_max            = $values->order_max;
        $bankwire->order_free           = $values->order_free;
        $bankwire->tax_included         = $values->tax_included;
        $bankwire->tax_rate             = number_format($values->tax_rate, 3);
        $bankwire->carriers             = $this->toArray($values->carriers);
        $bankwire->categories           = $this->toArray($values->categories);
        $bankwire->manufacturers        = $this->toArray($values->manufacturers);
        $bankwire->suppliers            = $this->toArray($values->suppliers);
        $bankwire->products             = $this->toArray($values->products);
        $bankwire->id_order_state       = $values->id_order_state;
        
        return $bankwire;
    }
    
    public function getPaypalValues()
    {
        $paypal = new stdClass();
        $values = new classMpPaymentConfiguration();
        $values->read(classCart::PAYPAL);
        
        $paypal->input_switch_on      = $values->is_active;
        $paypal->discount             = $values->discount;
        $paypal->fee_type             = $values->fee_type;
        $paypal->fee_amount           = $values->fee_amount;
        $paypal->fee_percent          = $values->fee_percent;
        $paypal->fee_min              = $values->fee_min;
        $paypal->fee_max              = $values->fee_max;
        $paypal->order_min            = $values->order_min;
        $paypal->order_max            = $values->order_max;
        $paypal->order_free           = $values->order_free;
        $paypal->tax_included         = $values->tax_included;
        $paypal->tax_rate             = number_format($values->tax_rate, 3);
        $paypal->carriers             = $this->toArray($values->carriers);
        $paypal->categories           = $this->toArray($values->categories);
        $paypal->manufacturers        = $this->toArray($values->manufacturers);
        $paypal->suppliers            = $this->toArray($values->suppliers);
        $paypal->products             = $this->toArray($values->products);
        $paypal->id_order_state       = $values->id_order_state;
        
        return $paypal;
    }
    
    public function toArray($input_string, $separator = ",")
    {
        if (empty($input_string)) {
            return array();
        }
        
        if (is_array($input_string)) {
            return $input_string;
        }
        
        return explode($separator, $input_string);
    }
    
    public function postProcess()
    {
        if (Tools::isSubmit('input_cash_submit')) {
            return $this->saveCashValues();
        } elseif (Tools::isSubmit('input_bankwire_submit')) {
            return $this->saveBankwireValues();
        } elseif (Tools::isSubmit('input_paypal_submit')) {
            return $this->savePaypalValues();
        }
    }
    
    public function saveCashValues()
    {
        $conf = new classMpPaymentConfiguration();
        
        $conf->is_active = Tools::getValue('input_cash_switch', 0);
        $conf->fee_type = Tools::getValue('input_cash_select_type', 0);
        $conf->fee_amount = Tools::getValue('input_cash_fee_amount', 0);
        $conf->fee_percent = Tools::getvalue('input_cash_fee_percent', 0);
        $conf->fee_min = Tools::getValue('input_cash_fee_min', 0);
        $conf->fee_max = Tools::getValue('input_cash_fee_max', 0);
        $conf->order_min = Tools::getValue('input_cash_order_min', 0);
        $conf->order_max = Tools::getValue('input_cash_order_max', 0);
        $conf->order_free = Tools::getValue('input_cash_order_free', 0);
        $conf->tax_included = Tools::getValue('input_cash_switch_included_tax', 0);
        $conf->tax_rate = Tools::getValue('input_cash_select_tax', 0);
        $conf->carriers = implode(",",Tools::getValue('input_cash_select_carriers', array(0)));
        $conf->categories = implode(",",Tools::getValue('input_cash_select_categories', array(0)));
        $conf->manufacturers = implode(",",Tools::getValue('input_cash_select_manufacturers', array(0)));
        $conf->suppliers = implode(",",Tools::getValue('input_cash_select_suppliers', array(0)));
        $conf->carriers = implode(",",Tools::getValue('input_cash_select_carriers', array(0)));
        $conf->products = implode(",",Tools::getValue('input_cash_select_products', array(0)));
        $conf->id_order_state = Tools::getValue('input_cash_select_order_state', 0);
        $conf->payment_type = classCart::CASH;
        $conf->save();
        
        $values = Tools::getAllValues();
        $smarty->assign('POSTVALUES', $values);
        
        return $this->class->displayConfirmation($this->l('Cash configuration saved successfully.', 'getContent'));
    }
    
    public function saveBankwireValues()
    {
        $conf = new classMpPaymentConfiguration();
        
        $conf->is_active = Tools::getValue('input_bankwire_switch', 0);
        $conf->fee_type = Tools::getValue('input_bankwire_select_type', 0);
        $conf->discount = Tools::getValue('input_bankwire_discount', 0);
        $conf->fee_amount = Tools::getValue('input_bankwire_fee_amount', 0);
        $conf->fee_percent = Tools::getvalue('input_bankwire_fee_percent', 0);
        $conf->fee_min = Tools::getValue('input_bankwire_fee_min', 0);
        $conf->fee_max = Tools::getValue('input_bankwire_fee_max', 0);
        $conf->order_min = Tools::getValue('input_bankwire_order_min', 0);
        $conf->order_max = Tools::getValue('input_bankwire_order_max', 0);
        $conf->order_free = Tools::getValue('input_bankwire_order_free', 0);
        $conf->tax_included = Tools::getValue('input_bankwire_switch_included_tax', 0);
        $conf->tax_rate = Tools::getValue('input_bankwire_select_tax', 0);
        $conf->carriers = implode(",",Tools::getValue('input_bankwire_select_carriers', array(0)));
        $conf->categories = implode(",",Tools::getValue('input_bankwire_select_categories', array(0)));
        $conf->manufacturers = implode(",",Tools::getValue('input_bankwire_select_manufacturers', array(0)));
        $conf->suppliers = implode(",",Tools::getValue('input_bankwire_select_suppliers', array(0)));
        $conf->carriers = implode(",",Tools::getValue('input_bankwire_select_carriers', array(0)));
        $conf->products = implode(",",Tools::getValue('input_bankwire_select_products', array(0)));
        $conf->id_order_state = Tools::getValue('input_bankwire_select_order_state', 0);
        $conf->payment_type = classCart::BANKWIRE;
        $conf->save();
        
        ConfigurationCore::updateValue('MP_ADVPAYMENT_BANKWIRE_OWNER', 
                Tools::getValue('input_bankwire_owner', ''));
        ConfigurationCore::updateValue('MP_ADVPAYMENT_BANKWIRE_IBAN',
                Tools::getValue('input_bankwire_iban', ''));
        ConfigurationCore::updateValue('MP_ADVPAYMENT_BANKWIRE_BANK', 
                Tools::getValue('input_bankwire_bank', ''));
        ConfigurationCore::updateValue('MP_ADVPAYMENT_BANKWIRE_ADDR', 
                Tools::getValue('input_bankwire_address', ''));
        
        $values = Tools::getAllValues();
        $smarty->assign('POSTVALUES', $values);
        
        return $this->class->displayConfirmation($this->l('Bankwire configuration saved successfully.', 'getContent'));
    }
    
    public function savePaypalValues()
    {
        $conf = new classMpPaymentConfiguration();
        
        $conf->is_active = Tools::getValue('input_paypal_switch', 0);
        $conf->fee_type = Tools::getValue('input_paypal_select_type', 0);
        $conf->fee_amount = Tools::getValue('input_paypal_fee_amount', 0);
        $conf->fee_percent = Tools::getvalue('input_paypal_fee_percent', 0);
        $conf->fee_min = Tools::getValue('input_paypal_fee_min', 0);
        $conf->fee_max = Tools::getValue('input_paypal_fee_max', 0);
        $conf->order_min = Tools::getValue('input_paypal_order_min', 0);
        $conf->order_max = Tools::getValue('input_paypal_order_max', 0);
        $conf->order_free = Tools::getValue('input_paypal_order_free', 0);
        $conf->tax_included = Tools::getValue('input_paypal_switch_included_tax', 0);
        $conf->tax_rate = Tools::getValue('input_paypal_select_tax', 0);
        $conf->carriers = implode(",",Tools::getValue('input_paypal_select_carriers', array(0)));
        $conf->categories = implode(",",Tools::getValue('input_paypal_select_categories', array(0)));
        $conf->manufacturers = implode(",",Tools::getValue('input_paypal_select_manufacturers', array(0)));
        $conf->suppliers = implode(",",Tools::getValue('input_paypal_select_suppliers', array(0)));
        $conf->carriers = implode(",",Tools::getValue('input_paypal_select_carriers', array(0)));
        $conf->products = implode(",",Tools::getValue('input_paypal_select_products', array(0)));
        $conf->id_order_state = Tools::getValue('input_paypal_select_order_state', 0);
        $conf->payment_type = classCart::PAYPAL;
        $conf->save();
        
        ConfigurationCore::updateValue('MP_ADVPAYMENT_PAYPAL_TEST_API',
                Tools::getValue('input_paypal_switch_test', ''));
        ConfigurationCore::updateValue('MP_ADVPAYMENT_PAYPAL_USER_API',
                Tools::getValue('input_paypal_user_api', ''));
        ConfigurationCore::updateValue('MP_ADVPAYMENT_PAYPAL_PWD_API',
                Tools::getValue('input_paypal_password_api', ''));
        ConfigurationCore::updateValue('MP_ADVPAYMENT_PAYPAL_SIGN_API',
                Tools::getValue('input_paypal_signature_api', ''));
        ConfigurationCore::updateValue('MP_ADVPAYMENT_PAYPAL_APP_TEST_API',
                Tools::getValue('input_paypal_test_api', ''));
        ConfigurationCore::updateValue('MP_ADVPAYMENT_PAYPAL_PRO_API',
                Tools::getValue('input_paypal_switch_pro', ''));
        ConfigurationCore::updateValue('MP_ADVPAYMENT_PAYPAL_EMAIL_API',
                Tools::getValue('input_paypal_pro_email_api', ''));
        
        $logo = Tools::getValue('filename','');
        $image = Tools::getValue('input_paypal_logo','');

        if (!empty($image)) {
            $data = base64_decode($image);
            $filename = $logo;
            $ext = pathinfo($filename, PATHINFO_EXTENSION);

            $serverFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . ".."
                    . DIRECTORY_SEPARATOR . "paypal_logo.";

            array_map('unlink', glob($serverFile . "*"));

            $fp = file_put_contents($serverFile . $ext, $data);
            chmod($serverFile . $ext, 0777);
        }
        
        $values = Tools::getAllValues();
        $smarty->assign('POSTVALUES', $values);
        
        return $this->class->displayConfirmation($this->l('Paypal configuration saved successfully.', 'getContent'));
    }
    
    public function run()
    {
        $message = $this->postProcess();
        $html_form = $this->renderForm();
        return $message . $html_form;
    }
}
