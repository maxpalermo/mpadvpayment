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
class classMpTools {
    /**
     * Get the list of activated modules
     * @return array associative array of activated payment modules [payment_type=>is_active]
     */
    public static function getActiveModules()
    {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        
        $sql    ->select("is_active")
                ->select("payment_type")
                ->from("mp_advpayment_configuration")
                ->orderBy("payment_type");
        $result = $db->executeS($sql);
        $output = array();
        foreach ($result as $record) {
            $output[$record['payment_type']] = $record['is_active'];
        }
        
        //Check if PaypalPro is active
        $paypal_pro = (bool)ConfigurationCore::get('MP_ADVPAYMENT_PAYPAL_PRO_API');
        if ($paypal_pro) {
            $output['paypal pro'] = 1;
            $output['paypal'] = 0;
        }
        
        //Check module restrictions
        $Payment = new classMpPaymentCalc;
        $cashExclusions = $Payment->getListProductsExclusion(classMpPayment::CASH);
        $bankExclusions = $Payment->getListProductsExclusion(classMpPayment::BANKWIRE);
        $paypalExclusions = $Payment->getListProductsExclusion(classMpPayment::PAYPAL);
        $cartProducts = Context::getContext()->cart->getProducts();
        
        //print_r($cartProducts);
        
        foreach ($cartProducts as $product) {
            if (in_array($product['id_product'], $cashExclusions)) {
                $output['cash']=false;
            }
            if (in_array($product['id_product'], $bankExclusions)) {
                $output['bankwire']=false;
            }
            if (in_array($product['id_product'], $paypalExclusions)) {
                $output['paypal']=false;
                $output['paypal pro']=false;
            }
        }
        
        return $output;
    }
    
    public static function addSwitch($name, $label, $smarty)
    {
        $switch = new stdClass();
        $switch->label = $label;
        $switch->name = $name;
        $smarty->assign('switch', $switch);
        $smarty->assign($name, $smarty->fetch(_MPADVPAYMENT_TEMPLATES_HOOK_ . 'switch.tpl'));
    }
    
    public static function getTaxList()
    {
        $lang = Context::getContext()->language->id;
        $taxes = TaxCore::getTaxes($lang);
        $options = array();
        $options[] = "<option value='0'>----</option>";
        foreach ($taxes as $tax) {
            $options[] = "<option value='" . $tax['rate'] . "'>" . $tax['name'] . "</option>";
        }
        return implode("\n", $options);
    }
    
    public static function getCarrierList()
    {
        $lang = Context::getContext()->language->id;
        $carriers = CarrierCore::getCarriers($lang);
        $options = array();
        foreach ($carriers as $carrier) {
            $options[] = "<option value='" . $carrier['id_carrier'] . "'>" . Tools::strtoupper($carrier['name']) . "</option>";
        }
        return implode("\n", $options);
    }
    
    public static function getCategoriesList()
    {
        $lang = Context::getContext()->language->id;
        $categories = CategoryCore::getCategories($lang);
        $options = array();
        foreach ($categories as $category) {
            foreach ($category as $cat) {
                $options[] = "<option value='" . $cat['infos']['id_category'] . "'>" . Tools::strtoupper($cat['infos']['name']) . "</option>";
            }
        }
        return implode("\n", $options);
    }
    
    public static function getManufacturersList()
    {
        $items = ManufacturerCore::getManufacturers();
        $options = array();
        foreach ($items as $item) {
            $options[] = "<option value='" . $item['id_manufacturer'] . "'>" . Tools::strtoupper($item['name']) . "</option>";
        }
        return implode("\n", $options);
    }
    
    public static function getSuppliersList()
    {
        $items = SupplierCore::getSuppliers();
        $options = array();
        foreach ($items as $item) {
            $options[] = "<option value='" . $item['id_supplier'] . "'>" . Tools::strtoupper($item['name']) . "</option>";
        }
        return implode("\n", $options);
    }
    
    public static function getProductsList()
    {
        $lang = Context::getContext()->language->id;
        $items = ProductCore::getSimpleProducts($lang);
        $options = array();
        foreach ($items as $item) {
            $options[] = "<option value='" . $item['id_product'] . "'>" . Tools::strtoupper($item['name']) . "</option>";
        }
        return implode("\n", $options);
    }
    
    public static function getOrderStateList()
    {
        $lang = Context::getContext()->language->id;
        $items = OrderStateCore::getOrderStates($lang);
        $options = array();
        foreach ($items as $item) {
            $options[] = "<option value='" . $item['id_order_state'] . "'>" . Tools::strtoupper($item['name']) . "</option>";
        }
        return implode("\n", $options);
    }
    
    public static function getCashValues()
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
        $cash->carriers             = self::toArray($values->carriers);
        $cash->categories           = self::toArray($values->categories);
        $cash->manufacturers        = self::toArray($values->manufacturers);
        $cash->suppliers            = self::toArray($values->suppliers);
        $cash->products             = self::toArray($values->products);
        $cash->id_order_state       = $values->id_order_state;
        
        return $cash;
    }
    
    public static function getBankwireValues()
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
        $bankwire->carriers             = self::toArray($values->carriers);
        $bankwire->categories           = self::toArray($values->categories);
        $bankwire->manufacturers        = self::toArray($values->manufacturers);
        $bankwire->suppliers            = self::toArray($values->suppliers);
        $bankwire->products             = self::toArray($values->products);
        $bankwire->id_order_state       = $values->id_order_state;
        
        return $bankwire;
    }
    
    public static function getPaypalValues()
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
        $paypal->carriers             = self::toArray($values->carriers);
        $paypal->categories           = self::toArray($values->categories);
        $paypal->manufacturers        = self::toArray($values->manufacturers);
        $paypal->suppliers            = self::toArray($values->suppliers);
        $paypal->products             = self::toArray($values->products);
        $paypal->id_order_state       = $values->id_order_state;
        
        return $paypal;
    }
    
    public static function toArray($input_string, $separator = ",")
    {
        if (empty($input_string)) {
            return array();
        }
        
        if (is_array($input_string)) {
            return $input_string;
        }
        
        return explode($separator, $input_string);
    }
}
