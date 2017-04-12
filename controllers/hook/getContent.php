<?php

class MpAdvPaymentGetContentController
{
    private $local_path;
    private $_path;
    private $_lang;
    private $smarty;
    private $class;
    private $file_path;
    
    public function __construct($module, $file, $path)
    {
            $this->file = $file;
            $this->module = $module;
            $this->context = Context::getContext(); $this->_path = $path;
            $this->_path = __PS_BASE_URI__.'modules/mpadvpayment/';
            $this->_lang = Context::getContext()->language->id;
    }
    
    public function setFilePath($file_path)
    {
        $this->file_path = $file_path;
    }
    
    public function setLocalPath($local_path)
    {
        $this->local_path = $local_path;
    }
    
    public function setClass($class)
    {
        $this->class = $class;
    }
    
    public function setSmarty($smarty)
    {
        $this->smarty = $smarty;
    }
    
    public function renderForm()
    {
        
        $this->class->setMedia();
        
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
        $this->smarty->assign('bankwire_values', $this->getBankwireValues());
        $this->smarty->assign('ps_version', Tools::substr(_PS_VERSION_, 0, 3));
        $this->smarty->assign('form_cash', $this->smarty->fetch($this->local_path . 'views/templates/hook/form_cash.tpl'));
        $this->smarty->assign('form_bankwire', $this->smarty->fetch($this->local_path . 'views/templates/hook/form_bankwire.tpl'));
        $template  = $this->class->display($this->file_path, 'getContent.tpl');
        $psui_tags = $this->class->display($this->file_path, 'views/templates/admin/prestui/ps-tags.tpl');
        return $template . $psui_tags;
    }

    public function setMedia()
    {
        $this->context->controller->addJqueryUI('ui.tabs');
        $this->context->controller->addJS("https://cdnjs.cloudflare.com/ajax/libs/riot/3.4.0/riot+compiler.min.js");
        $this->context->controller->addCSS($this->_path . 'js/chosen/chosen.min.css');
        $this->context->controller->addJS($this->_path . 'js/chosen/chosen.jquery.min.js');
    }
    
    public function getTaxList()
    {
        $taxes = TaxCore::getTaxes($this->_lang);
        $options = [];
        $options[] = "<option value='0'>" . $this->module->l('Please select','getContent') . "</option>";
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
    
    public function getBankwireValues()
    {
        $bankwire = new stdClass();
        $values = new classMpPayment();
        $values->read(classMpPayment::BANKWIRE);
        
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
        $bankwire->tax_rate             = number_format($values->tax_rate,3);
        $bankwire->carriers             = $this->toArray($values->carriers);
        $bankwire->categories           = $this->toArray($values->categories);
        $bankwire->manufacturers        = $this->toArray($values->manufacturers);
        $bankwire->suppliers            = $this->toArray($values->suppliers);
        $bankwire->products             = $this->toArray($values->products);
        $bankwire->id_order_state       = $values->id_order_state;
        
        return $bankwire;
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
    
    public function run()
    {
        $html_form = $this->renderForm();
        return $html_form;
    }
}
