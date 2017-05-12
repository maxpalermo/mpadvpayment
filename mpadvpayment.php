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

require_once(dirname(__FILE__) . '/classes/autoload.php');
    
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
          !$this->installProducts() ||
          !$this->installSql() ||
          !$this->installPdf()) {
            return false;
        }
        return true;
    }
    
    public function uninstall()
    {
        if (!parent::uninstall() || 
                !$this->uninstallProducts() ||
                !$this->uninstallSql() || 
                $this->uninstallPdf()) {
            return false;
        }
        return true;
    }
    
    public function hookDisplayPayment($params)
    {
        /** @var CartCore $cart */
        //$cart = new CartCore();
        $cart = Context::getContext()->cart;
        
        $this->smarty = Context::getContext()->smarty;
        $this->context->controller->addCSS(_MPADVPAYMENT_CSS_URL_ . 'displayPayment.css');
        $this->summary = new classSummary($cart->id, classCart::NONE);
        $this->smarty->assign('cart', new Cart($cart->id));
        /*
         *  SUMMARY CLASS TO SMARTY
         */
        $this->smarty->assign('classSummary', $this->summary);
        if (!session_id()) {
            session_start();
        }
        //SAVE TO SESSION
        $result = classSession::setSessionSummary($this->summary);
        
        /*
         * CASH PAYMENT
         */
        $this->smarty->assign('payment', 'cash');
        $this->smarty->assign(
                'cash_summary',
                $this->smarty->fetch(_MPADVPAYMENT_TEMPLATES_HOOK_ . 'summary.tpl'));
        
        /*
         * BANKWIRE PAYMENT
         */
        $this->smarty->assign('payment', 'bankwire');
        $this->smarty->assign(
                'bankwire_summary',
                $this->smarty->fetch(_MPADVPAYMENT_TEMPLATES_HOOK_ . 'summary.tpl'));
        
        /*
         * PAYPAL PAYMENT
         */
        $link = new LinkCore();
        $returnUrl = $link->getModuleLink('mpadvpayment', 'paypal', array('action' => 'GetExpressCheckoutDetails'));
        $cancelUrl = $link->getModuleLink('mpadvpayment', 'paypalerror');
        $controllerUrl = $link->getModuleLink('mpadvpayment', 'paypal', array('action'=>'SetExpressCheckout'));
        $this->smarty->assign('payment', 'paypal');
        $this->smarty->assign('paypal_summary', $this->smarty->fetch(_MPADVPAYMENT_TEMPLATES_HOOK_ . 'summary.tpl'));
        
        $controller = $this->getHookController('displayPayment');
        $controller->setSmarty($this->smarty);
        return $controller->run($params);
    }
    
    public function hookDisplayPaymentReturn($params)
    {
        return true;
        
        if (!$this->active) {
            return;
        }
        
        $this->context->smarty->assign(array('id_order' => $params['objOrder']->id));
        
        $controller = $this->getHookController('displayPaymentReturn');
        return $controller->run($params);
    }
    
    public function getContent()
    {
        $this->smarty = Context::getContext()->smarty;
        $controller = $this->getHookController('getContent');
        $controller->setClass($this);
        $controller->setSmarty($this->smarty);
        $controller->setLocalPath($this->local_path);
        $controller->setFilePath(__FILE__);
        return $controller->run();
    }
    
    public function setMedia()
    {
        $this->context->controller->addJS("https://cdnjs.cloudflare.com/ajax/libs/riot/3.4.0/riot+compiler.min.js");
        $this->context->controller->addJqueryPlugin(array('idTabs','chosen'));
        $this->context->controller->addJqueryUI('ui.tabs');
        $this->context->controller->addJS(_PS_JS_DIR_ . "jquery/plugins/jquery.idTabs.js");
    }
    
    private function installProducts()
    {
        $id_lang = Context::getContext()->language->id;
        $lang = new LanguageCore($id_lang);
        
        $product_fee = new ProductCore();
        $product_fee->active = true;
        $product_fee->available_for_order = true;
        $product_fee->link_rewrite[$id_lang]='product-fee';
        $product_fee->id_category_default = 1;
        if (Tools::strtolower($lang->iso_code)=='it') {
            $product_fee->name[$id_lang] = 'Commissioni';
        } else {
            $product_fee->name[$id_lang] = 'Fees';
        }
        $product_fee->save();
        
        if($product_fee->id==0) {
            return false;
        }
        ConfigurationCore::updateValue('MP_ADVPAYMENT_PRODUCT_FEE', $product_fee->id);
        
        $product_discount = new ProductCore();
        $product_discount->active = true;
        $product_discount->available_for_order = true;
        $product_discount->id_category_default = 1;
        $product_discount->link_rewrite[$id_lang]='product-discount';
        if (Tools::strtolower($lang->iso_code)=='it') {
            $product_discount->name[$id_lang] = 'Sconti';
        } else {
            $product_discount->name[$id_lang] = 'Discounts';
        }
        $product_discount->save();
        
        if($product_discount->id==0) {
            return false;
        }
        ConfigurationCore::updateValue('MP_ADVPAYMENT_PRODUCT_DISCOUNT', $product_discount->id);
        
        return true;
    }
    
    private function uninstallProducts()
    {
        $id_product_fee = ConfigurationCore::get('MP_ADVPAYMENT_PRODUCT_FEE');
        $id_product_discount = ConfigurationCore::get('MP_ADVPAYMENT_PRODUCT_DISCOUNT');
        $product_fee = new ProductCore($id_product_fee);
        $product_fee->delete();
        $product_discount = new ProductCore($id_product_discount);
        $product_discount->delete();
        return true;
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
    
    public function installPdf()
    {
        $source = dirname(__FILE__) . DIRECTORY_SEPARATOR
                . "pdf" . DIRECTORY_SEPARATOR;
        $dest_class   = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR .
                "classes" . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR;
        $dest_pdf   = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR .
                "pdf" . DIRECTORY_SEPARATOR;
        
        rename($dest_class . 'HTMLTemplateInvoice.php', $dest_class . 'HTMLTemplateInvoice.old.php');
        copy($source . 'HTMLTemplateInvoice.php', $dest_class . 'HTMLTemplateInvoice.php');
        
        rename($dest_pdf . 'invoice.tax-tab.tpl', $dest_pdf . 'invoice.tax-tab.old.tpl');
        rename($dest_pdf . 'invoice.total-tab.tpl', $dest_pdf . 'invoice.total-tab.old.tpl');
        copy($source . 'invoice.tax-tab.tpl', $dest_pdf . 'invoice.tax-tab.tpl');
        copy($source . 'invoice.total-tab.tpl', $dest_pdf . 'invoice.total-tab.tpl');
        
        return true;
    }
    
    public function uninstallPdf()
    {
        $dest_class   = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . "classes" . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR;
        $dest_pdf   = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR;
        
        unlink($dest_class . 'HTMLTemplateInvoice.php');
        rename($dest_class . 'HTMLTemplateInvoice.old.php', $dest_class . 'HTMLTemplateInvoice.php');
        
        unlink($dest_pdf . 'invoice.tax-tab.tpl');
        unlink($dest_pdf . 'invoice.total-tab.tpl');
        rename($dest_pdf . 'invoice.tax-tab.old.tpl', $dest_pdf . 'invoice.tax-tab.tpl');
        rename($dest_pdf . 'invoice.total-tab.old.tpl', $dest_pdf . 'invoice.total-tab.tpl');
        
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
}
