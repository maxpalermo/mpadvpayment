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

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__) . '/classes/classMpAdvPaymentAutoload.php');
    
class MpAdvPayment extends PaymentModule
{
    private $summary;
    
    public function __construct()
    {
        $this->name = 'mpadvpayment';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->author = 'mpsoft';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('MP Advanced payment module');
        $this->description = $this->l('This module include three payments method with advanced custom parameters');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
      
        //SET DEFINITIONS
        if(!defined('_MPADVPAYMENT_URL_')) {
            define('_MPADVPAYMENT_URL_', $this->_path);
        }
        
        if(!defined('_MPADVPAYMENT_')) {
            define('_MPADVPAYMENT_', $this->local_path);
        }

        if(!defined('_MPADVPAYMENT_CLASSES_')) {
            define('_MPADVPAYMENT_CLASSES_', _MPADVPAYMENT_ . "classes/");
        }

        if(!defined('_MPADVPAYMENT_CONTROLLERS_')) {
            define('_MPADVPAYMENT_CONTROLLERS_', _MPADVPAYMENT_ . "controllers/");
        }

        if(!defined('_MPADVPAYMENT_CSS_URL_')) {
            define('_MPADVPAYMENT_CSS_URL_', _MPADVPAYMENT_URL_ . "views/css/");
        }

        if(!defined('_MPADVPAYMENT_JS_URL_')) {
            define('_MPADVPAYMENT_JS_URL_', _MPADVPAYMENT_URL_ . "views/js/");
        }

        if(!defined('_MPADVPAYMENT_IMG_URL_')) {
            define('_MPADVPAYMENT_IMG_URL_', _MPADVPAYMENT_URL_ . "views/img/");
        }

        if(!defined('_MPADVPAYMENT_TEMPLATES_')) {
            define('_MPADVPAYMENT_TEMPLATES_', _MPADVPAYMENT_ . "views/templates/");
        }
        
        if(!defined('_MPADVPAYMENT_TEMPLATES_HOOK_')) {
            define('_MPADVPAYMENT_TEMPLATES_HOOK_', _MPADVPAYMENT_TEMPLATES_ . "hook/");
        }
        
        if(!defined('_MPADVPAYMENT_TEMPLATES_FRONT_')) {
            define('_MPADVPAYMENT_TEMPLATES_FRONT_', _MPADVPAYMENT_TEMPLATES_ . "front/");
        }
    }
  
    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install() ||
          !$this->registerHook('displayPayment') ||
          !$this->installSql()) {
            return false;
        }
        return true;
    }
    
    public function uninstall()
    {
        if (!parent::uninstall() || 
                !$this->uninstallSql()) {
            return false;
        }
        return true;
    }
    
    /**
     * Called to display payment during finalize cart on frontend
     * @param type $params
     * @return type
     */
    public function hookDisplayPayment($params)
    {
        $smarty = Context::getContext()->smarty;
        $cart = Context::getContext()->cart;
        classValidation::removeFeeFromCart($cart->id);
        $summary = new classSummary($cart->id, classCart::NONE);
        $result = classSession::setSessionSummary($summary);
        if (!$result) {
           Tools::d('Error during session save');
        }
        
        $controller = Context::getContext()->controller;
        $controller->addCSS(_MPADVPAYMENT_CSS_URL_ . 'displayPayment.css', 'all');
        $controller->addCSS(_MPADVPAYMENT_CSS_URL_ . 'displayPayment.css');
        $smarty->assign('activeModules', classMpTools::getActiveModules());
        $smarty->assign('cart', new Cart($cart->id));
        $smarty->assign('classSummary', $summary);
        
        /*
         * CASH PAYMENT
         */
        $smarty->assign('payment', 'cash');
        $smarty->assign('cash_summary', $smarty->fetch(_MPADVPAYMENT_TEMPLATES_HOOK_ . 'summary.tpl'));
        
        /*
         * BANKWIRE PAYMENT
         */
        $smarty->assign('payment', 'bankwire');
        $smarty->assign('bankwire_summary', $smarty->fetch(_MPADVPAYMENT_TEMPLATES_HOOK_ . 'summary.tpl'));
        
        /*
         * PAYPAL PAYMENT
         */
        $link = new LinkCore();
        $returnUrl = $link->getModuleLink('mpadvpayment', 'paypal', array('action' => 'GetExpressCheckoutDetails'));
        $cancelUrl = $link->getModuleLink('mpadvpayment', 'paypalerror');
        $controllerUrl = $link->getModuleLink('mpadvpayment', 'paypal', array('action'=>'SetExpressCheckout'));
        $smarty->assign('payment', 'paypal');
        $smarty->assign('paypal_summary', $smarty->fetch(_MPADVPAYMENT_TEMPLATES_HOOK_ . 'summary.tpl'));
        
        return $this->display(_MPADVPAYMENT_, 'displayPayment.tpl');
    }
    
    public function getContent()
    {
        $message = $this->postProcess();
        $this->setMedia();
        $form = $this->renderBackOfficeForm();
        
        return $message . $form;
    }
    
    public function setMedia()
    {
        $controller = Context::getContext()->controller;
        $controller->addJqueryPlugin(array('idTabs','chosen'));
        $controller->addJqueryUI('ui.tabs');
        $controller->addJS(_PS_JS_DIR_ . "jquery/plugins/jquery.idTabs.js");
        $controller->addCSS(_MPADVPAYMENT_CSS_URL_ . 'getContent.css');
    }
    
    /**
     * Call after page submit
     * @return string HTML confirmation message
     */
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
    
    private function installSQL()
    {
        $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "sql" . DIRECTORY_SEPARATOR . "install.sql";
        $sql = explode(";", Tools::file_get_contents($filename));
        if (empty($sql)) {
            return false;
        }
        foreach ($sql as $query) {
            if (!empty($query)) {
                $query = str_replace("{_DB_PREFIX_}", _DB_PREFIX_, $query);
                $db = Db::getInstance();
                $result = $db->execute($query);
                if (!$result) {
                    return false;
                }
            }
        }
        return true;
    }
    
    private function uninstallSQL()
    {
        return true;
        
        $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "sql" . DIRECTORY_SEPARATOR . "uninstall.sql";
        $sql = explode(";", Tools::file_get_contents($filename));
        if (empty($sql)) {
            return false;
        }
        foreach ($sql as $query) {
            if (!empty($query)) {
                $query = str_replace("{_DB_PREFIX_}", _DB_PREFIX_, $query);
                $db = Db::getInstance();
                $result = $db->execute($query);
                if (!$result) {
                    return false;
                }
            }
        }
        return true;
    }
    
    public function getHookController($hook_name)
    {
        // Include the controller file
        require_once(dirname(__FILE__).'/controllers/hook/'. $hook_name.'.php');

        // Build dynamically the controller name
        $controller_name = $this->name.$hook_name.'Controller';

        // Instantiate controller
        $controller = new $controller_name($this, __FILE__, $this->_path);

        // Return the controller
        return $controller;
    }
    
    public function renderBackOfficeForm()
    {
        $smarty = Context::getContext()->smarty;
        $this->setMedia();
        
        $image_file = glob(_MPADVPAYMENT_ . "paypal_logo.*");
        if ($image_file) {
            //$filename = _PS_BASE_URL_ . __PS_BASE_URI__ . "/modules/mpadvpayment/" . basename($image_file[0]);
            $filename = _MPADVPAYMENT_URL_ . basename($image_file[0]);
            $smarty->assign('paypal_logo', $filename);
        } else {
            $smarty->assign('paypal_logo', '');
        }
        
        classMpTools::addSwitch('input_cash_switch', $this->l('Activate cash payment?', 'mpadvpayment'), $smarty);
        classMpTools::addSwitch('input_cash_switch_included_tax', $this->l('Tax included?', 'mpadvpayment'), $smarty);
        classMpTools::addSwitch('input_bankwire_switch', $this->l('Activate Bankwire payments?', 'mpadvpayment'), $smarty);
        classMpTools::addSwitch('input_bankwire_switch_included_tax', $this->l('Tax included?', 'mpadvpayment'), $smarty);
        classMpTools::addSwitch('input_paypal_switch', $this->l('Activate Paypal payment?', 'mpadvpayment'), $smarty);
        classMpTools::addSwitch('input_paypal_switch_included_tax', $this->l('Tax included?', 'mpadvpayment'), $smarty);
        classMpTools::addSwitch('input_paypal_switch_test', $this->l('Test sandbox?', 'mpadvpayment'), $smarty);
        classMpTools::addSwitch('input_paypal_switch_pro', $this->l('Activate Paypal Pro payments?', 'mpadvpayment'), $smarty);
        
        
        $smarty->assign('path', _MPADVPAYMENT_URL_);
        $smarty->assign('base_uri', __PS_BASE_URI__);
        $smarty->assign('tax_list', classMpTools::getTaxList());
        $smarty->assign('carrier_list', classMpTools::getCarrierList());
        $smarty->assign('categories_list', classMpTools::getCategoriesList());
        $smarty->assign('manufacturers_list', classMpTools::getManufacturersList());
        $smarty->assign('suppliers_list', classMpTools::getSuppliersList());
        $smarty->assign('products_list', classMpTools::getProductsList());
        $smarty->assign('order_state_list', classMpTools::getOrderStateList());
        $smarty->assign('cash_values', classMpTools::getCashValues());
        $smarty->assign('bankwire_values', classMpTools::getBankwireValues());
        $smarty->assign('paypal_values', classMpTools::getPaypalValues());
        $smarty->assign('ps_version', Tools::substr(_PS_VERSION_, 0, 3));
        $smarty->assign('form_cash', $smarty->fetch(_MPADVPAYMENT_TEMPLATES_HOOK_ . 'form_cash.tpl'));
        $smarty->assign('form_bankwire', $smarty->fetch(_MPADVPAYMENT_TEMPLATES_HOOK_ . 'form_bankwire.tpl'));
        $smarty->assign('form_paypal', $smarty->fetch(_MPADVPAYMENT_TEMPLATES_HOOK_ . 'form_paypal.tpl'));
        $smarty->assign('form_card', $smarty->fetch(_MPADVPAYMENT_TEMPLATES_HOOK_ . 'form_card.tpl'));
            
        $template  = $this->display(_MPADVPAYMENT_, 'getContent.tpl');
        //$psui_tags = $this->class->display(_MPADVPAYMENT_, 'views/templates/admin/prestui/ps-tags.tpl');
        return $template;
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
        $smarty = Context::getContext()->smarty;
        $smarty->assign('POSTVALUES', $values);
        
        return $this->displayConfirmation($this->l('Cash configuration saved successfully.', 'mpadvpayment'));
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
        $smarty = Context::getContext()->smarty;
        $smarty->assign('POSTVALUES', $values);
        
        return $this->displayConfirmation($this->l('Bankwire configuration saved successfully.', 'mpadvpayment'));
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
        
        $logo = Tools::getValue('files','');
        $image = Tools::getValue('input_paypal_logo','');

        if (!empty($image)) {
            $data = base64_decode($image);
            $filename = $logo;
            $ext = pathinfo($filename, PATHINFO_EXTENSION);

            $serverFile = _MPADVPAYMENT_ .  "paypal_logo.";

            array_map('unlink', glob($serverFile . "*"));

            $fp = file_put_contents($serverFile . $ext, $data);
            chmod($serverFile . $ext, 0777);
        }
        
        $values = Tools::getAllValues();
        $smarty = Context::getContext()->smarty;
        $smarty->assign('POSTVALUES', $values);
        
        return $this->displayConfirmation($this->l('Paypal configuration saved successfully.', 'mpadvpayment'));
    }
}
