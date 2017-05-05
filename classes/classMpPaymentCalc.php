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

class ClassMpPaymentCalc
{
    /**
     *
     * @param string $payment_method
     * @return array sorted array of id products
     */
    public static function getListProductsExclusion($payment_method)
    {
        $products = array();
        $exclusions = self::getExclusions($payment_method);
        self::addProduct(self::getProductsFromCarriers($exclusions['carriers']), $products);
        self::addProduct(self::getProductsFromCategories($exclusions['categories']), $products);
        self::addProduct(self::getProductsFromManufacturers($exclusions['manufacturers']), $products);
        self::addProduct(self::getProductsFromSuppliers($exclusions['suppliers']), $products);
        self::addProduct(self::getProductsVirtual(), $products);
        self::addProduct($exclusions['products'], $products);
        
        //Sort array
        asort($products);
        $product_list = array_values($products);
        
        return $product_list;
    }
    
    /**
     *
     * @param string $payment_method CONST:
     *                                      classMpPayment::CASH
     *                                      classMpPayment::BANKWIRE
     *                                      classMpPayment::PAYPAL
     * @return array result query
     */
    public static function getExclusions($payment_method)
    {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        
        $sql    ->select("carriers")
                ->select("categories")
                ->select("manufacturers")
                ->select("suppliers")
                ->select("products")
                ->from("mp_advpayment_configuration")
                ->where("payment_type = '$payment_method'");
        return $db->getRow($sql);
    }
    
    /**
     *
     * @param array $array
     * @return array returns indexed array
     */
    public static function purifyArray($array)
    {
        $purified_array = array();
        foreach ($array as $item) {
            foreach ($item as $key => $value) {
                $purified_array[] = $value;
            }
        }
        return $purified_array;
    }
    
    /**
     *
     * @param string $carriers carrier list comma separated
     * @return array id product list
     */
    public static function getProductsFromCarriers($carriers)
    {
        if (empty($carriers)) {
            return array();
        }
        
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        
        $sql    ->select("id_product")
                ->from("product_carrier")
                ->where("id_carrier_reference in ($carriers)");
        return self::purifyArray($db->ExecuteS($sql));
    }
    
    /**
     *
     * @param string $categories categories list comma separated
     * @return array id product list
     */
    public static function getProductsFromCategories($categories)
    {
        if (empty($categories)) {
            return array();
        }
        
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        
        $sql    ->select("id_product")
                ->from("category_product")
                ->where("id_category in ($categories)");
        return self::purifyArray($db->ExecuteS($sql));
    }
    
    /**
     *
     * @param string $manufacturers manufacturers list comma separated
     * @return array id product list
     */
    public static function getProductsFromManufacturers($manufacturers)
    {
        if (empty($manufacturers)) {
            return array();
        }
        
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        
        $sql    ->select("id_product")
                ->from("product")
                ->where("id_manufacturer in ($manufacturers)");
        return self::purifyArray($db->ExecuteS($sql));
    }
    
    /**
     *
     * @param string $suppliers suppliers list comma separated
     * @return array id product list
     */
    public static function getProductsVirtual()
    {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        
        $sql    ->select("id_product")
                ->from("product")
                ->where("is_virtual = 1");
        return self::purifyArray($db->ExecuteS($sql));
    }
    
    /**
     *
     * @param string $suppliers suppliers list comma separated
     * @return array id product list
     */
    public static function getProductsFromSuppliers($suppliers)
    {
        if (empty($suppliers)) {
            return array();
        }
        
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        
        $sql    ->select("id_product")
                ->from("product_supplier")
                ->where("id_supplier in ($suppliers)");
        return self::purifyArray($db->ExecuteS($sql));
    }
    
    /**
     *
     * @param array $product_list list of id products to add
     * @param array $products product list
     * @return int array size
     */
    public static function addProduct($product_list, &$products)
    {
        if (empty($product_list)) {
            return count($products);
        }
        if (!is_array($product_list)) {
            $product_list = explode(",", $product_list);
        }
        foreach ($product_list as $id_product) {
            if (!in_array($id_product, $products, true)) {
                $products[] = $id_product;
            }
        }
        
        return count($products);
    }
    
    /**
     * Get product list from given cart id
     * @param int $id_cart id
     * @return array cart product list
     */
    public static function getCartProductList($id_cart)
    {
        $cart = new CartCore($id_cart);
        $products = $cart->getProducts();
        
        return $products;
    }
}
