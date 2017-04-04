<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('_PS_VERSION_')) {
    exit;
    }
 
class MpAdvPayment extends PaymentModuleCore
{
    private $css;
    private $js;
    private $headers;
    
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
        return $this->display(__FILE__, 'displayPayment.tpl');
    }
    
    public function getContent()
    {
        $this->_lang = Context::getContext()->language->id;
        $this->smarty = Context::getContext()->smarty;
        $this->setMedia();
        
        $this->smarty->assign('base_uri', __PS_BASE_URI__);
        $this->smarty->assign('tax_list', $this->getTaxList());
        $this->smarty->assign('carrier_list', $this->getCarrierList());
        $this->smarty->assign('categories_list', $this->getCategoriesList());
        $this->smarty->assign('manufacturers_list', $this->getManufacturersList());
        $this->smarty->assign('suppliers_list', $this->getSuppliersList());
        $this->smarty->assign('products_list', $this->getProductsList());
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
}