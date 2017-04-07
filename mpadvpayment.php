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

require_once (dirname(__FILE__) . '/classes/classMpPayment.php');
    
class MpAdvPayment extends PaymentModuleCore
{
    private $css;
    private $js;
    private $headers;
    private $payment;
    
    public function __construct()
    {
      $this->name = 'mpadvpayment';
      $this->tab = 'payments_gateway';
      $this->version = '1.0.0';
      $this->author = 'mpsoft';
      $this->need_instance = 0;
      $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
      $this->bootstrap = true;

      parent::__construct();

      $this->displayName = $this->l('MP Advanced payment module');
      $this->description = $this->l('This module include three payments method with advanced custom parameters');
      $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
      
      $this->payment = new classMpPayment();
    }
  
    public function install()
    {
        if (Shop::isFeatureActive())
        {
          Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install() ||
          !$this->registerHook('displayPayment') ||
          !$this->registerHook('displayPaymentReturn') ||
          !$this->installSql()) {
          return false;
        }
        return true;
    }
    
    public function uninstall()
    {
        if (!parent::uninstall() || !$this->uninstallSql()) {
          return false;
        }
        return true;
    }
    
    public function hookDisplayPayment($params)
    {
        /** @var CartCore $cart */
        $cart = new CartCore();
        $cart = Context::getContext()->cart;
        $fee        = $this->payment->calculateFee(classMpPayment::CASH, $cart);
        
        
        
        $this->smarty->assign(
                "cash_summary", 
                $this->l("Cart:") . ' ' . Tools::displayPrice($fee['total_cart']) . ', ' .
                $this->l("Fee:") . ' ' . Tools::displayPrice($fee['total_fee_with_taxes']) . ', ' .
                $this->l("Total:") . ' ' . Tools::displayPrice($fee['total_cart']+$fee['total_fee_with_taxes']));
                
        $controller = $this->getHookController('displayPayment');
        return $controller->run($params);
    }
    
    public function getContent()
    {
        $this->_lang = Context::getContext()->language->id;
        $this->smarty = Context::getContext()->smarty;
        $this->setMedia();
        
        $this->smarty->assign('path', __PS_BASE_URI__ . 'modules/mpadvpayment');
        $this->smarty->assign('base_uri', __PS_BASE_URI__);
        $this->smarty->assign('tax_list', $this->getTaxList());
        $this->smarty->assign('carrier_list', $this->getCarrierList());
        $this->smarty->assign('categories_list', $this->getCategoriesList());
        $this->smarty->assign('manufacturers_list', $this->getManufacturersList());
        $this->smarty->assign('suppliers_list', $this->getSuppliersList());
        $this->smarty->assign('products_list', $this->getProductsList());
        $this->smarty->assign('order_state_list', $this->getOrderStateList());
        $this->smarty->assign('cash_values', $this->getCashValues());
        $this->smarty->assign('ps_version', Tools::substr(_PS_VERSION_, 0, 3));
        $this->smarty->assign('form_cash', $this->smarty->fetch($this->local_path . 'views/templates/hook/form_cash.tpl'));
        $template  = $this->display(__FILE__, 'getContent.tpl');
        $psui_tags = $this->display(__FILE__, 'views/templates/admin/prestui/ps-tags.tpl');
        return $template . $psui_tags;
    }
    
    public function setMedia()
    {
        $this->context->controller->addJqueryUI('ui.tabs');
        $this->context->controller->addJS("https://cdnjs.cloudflare.com/ajax/libs/riot/3.4.0/riot+compiler.min.js");
    }
    
    private function installSQL()
    {
        $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "sql" . DIRECTORY_SEPARATOR . "install.sql";
        $sql = explode(";",file_get_contents($filename));
        if(empty($sql)){return FALSE;}
        foreach($sql as $query)
        {
            if(!empty($query))
            {
                $query = str_replace("{_DB_PREFIX_}", _DB_PREFIX_, $query);
                $db = Db::getInstance();
                $result = $db->execute($query);
                if(!$result){return FALSE;}
            }
        }
        return TRUE;
    }
    
    private function uninstallSQL()
    {
        $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "sql" . DIRECTORY_SEPARATOR . "uninstall.sql";
        $sql = explode(";",file_get_contents($filename));
        if(empty($sql)){return FALSE;}
        foreach($sql as $query)
        {
            if(!empty($query))
            {
                $query = str_replace("{_DB_PREFIX_}", _DB_PREFIX_, $query);
                $db = Db::getInstance();
                $result = $db->execute($query);
                if(!$result){return FALSE;}
            }
        }
        return TRUE;
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
    
    /**
     * Adds jQuery UI component(s) to queued JS file list
     *
     * @param string|array $component
     * @param string $theme
     * @param bool $check_dependencies
     */
    public function addJqueryUI($component, $theme = 'base', $check_dependencies = true)
    {
        if (!is_array($component)) {
            $component = array($component);
        }

        foreach ($component as $ui) {
            $ui_path = Media::getJqueryUIPath($ui, $theme, $check_dependencies);
            //print "<pre>\n\n\n\n\n\n\n\n\n";
            //print_r($ui_path);
            //print "</pre>";
            $this->headers[] = $ui_path;
        }
    }
    
    public function getTaxList()
    {
        $taxes = TaxCore::getTaxes($this->_lang);
        $options = [];
        $options[] = "<option value='0'>" . $this->l('Please select') . "</option>";
        foreach($taxes as $tax)
        {
            $options[] = "<option value='" . $tax['rate'] . "'>" . $tax['name'] . "</option>";
        }
        return implode("\n", $options);
    }
    
    public function getCarrierList()
    {
        $carriers = CarrierCore::getCarriers($this->_lang);
        $options = [];
        foreach($carriers as $carrier)
        {
            $options[] = "<option value='" . $carrier['id_carrier'] . "'>" . Tools::strtoupper($carrier['name']) . "</option>";
        }
        return implode("\n", $options);
    }
    
    public function getCategoriesList()
    {
        $categories = CategoryCore::getCategories($this->_lang);
        $options = [];
        foreach($categories as $category)
        {
            foreach($category as $cat)
            {
                $options[] = "<option value='" . $cat['infos']['id_category'] . "'>" . Tools::strtoupper($cat['infos']['name']) . "</option>";
            }
        }
        return implode("\n", $options);
    }
    
    public function getManufacturersList()
    {
        $items = ManufacturerCore::getManufacturers();
        $options = [];
        foreach($items as $item)
        {
            $options[] = "<option value='" . $item['id_manufacturer'] . "'>" . Tools::strtoupper($item['name']) . "</option>";
        }
        return implode("\n", $options);
    }
    
    public function getSuppliersList()
    {
        $items = SupplierCore::getSuppliers();
        $options = [];
        foreach($items as $item)
        {
            $options[] = "<option value='" . $item['id_supplier'] . "'>" . Tools::strtoupper($item['name']) . "</option>";
        }
        return implode("\n", $options);
    }
    
    public function getProductsList()
    {
        $items = ProductCore::getSimpleProducts($this->_lang);
        $options = [];
        foreach($items as $item)
        {
            $options[] = "<option value='" . $item['id_product'] . "'>" . Tools::strtoupper($item['name']) . "</option>";
        }
        return implode("\n", $options);
    }
    
    public function getOrderStateList()
    {
        $items = OrderStateCore::getOrderStates($this->_lang);
        $options = [];
        foreach($items as $item)
        {
            $options[] = "<option value='" . $item['id_order_state'] . "'>" . Tools::strtoupper($item['name']) . "</option>";
        }
        return implode("\n", $options);
    }
    
    public function getCashValues()
    {
        $cash = new stdClass();
        $values = new classMpPayment();
        $values->read(classMpPayment::CASH);
        
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
        $cash->tax_rate             = number_format($values->tax_rate,3);
        $cash->carriers             = $this->toArray($values->carriers);
        $cash->categories           = $this->toArray($values->categories);
        $cash->manufacturers        = $this->toArray($values->manufacturers);
        $cash->suppliers            = $this->toArray($values->suppliers);
        $cash->products             = $this->toArray($values->products);
        $cash->id_order_state       = $values->id_order_state;
        
        return $cash;
    }
    
    public function toArray($input_string, $separator = ",")
    {
        if (empty($input_string)) {
            return [];
        }
        
        if (is_array($input_string)) {
            return $input_string;
        }
        
        return explode($separator,$input_string);
    }
}