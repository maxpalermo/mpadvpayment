<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classPaymentCalc
 *
 * @author imprendo
 */
class classPaymentCalc {
    /**
     * 
     * @param string $payment_method
     * @return array sorted array of id products
     */
    public static function getListProductsExclusion($payment_method)
    {
        $products = [];
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
        $purified_array = [];
        foreach($array as $item)
        {
            foreach($item as $key=>$value)
            {
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
            return [];
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
            return [];
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
            return [];
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
        if (empty($suppliers)) {
            return [];
        }
        
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        
        $sql    ->select("id_product")
                ->from("product")
                ->where("virtual = 1");
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
            return [];
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
        foreach($product_list as $id_product){
            if (!in_array($id_product, $products, true)){
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
