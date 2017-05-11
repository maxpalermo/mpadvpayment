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


class classCart {
    const CASH      = 'cash';
    const BANKWIRE  = 'bankwire';
    const PAYPAL    = 'paypal';
    const NONE      = '';
    
    const FEE_TYPE_NONE     = '0';
    const FEE_TYPE_FIXED    = '1';
    const FEE_TYPE_PERCENT  = '2';
    const FEE_TYPE_MIXED    = '3';
    const FEE_TYPE_DISCOUNT = '4';
    
    private $id;
    
    public $payment;
    public $total_fee_with_taxes;
    public $total_fee_without_taxes;
    public $total_fee_tax_rate;
    public $total_discount_no_tax;
    public $total_discount_with_taxes;
    public $total_discount_without_taxes;
    public $total_discount_tax_rate;
    public $total_cart_with_tax_no_fee;
    public $total_cart_with_tax_and_fee;
    public $total_cart_without_tax;
    public $total_products_without_tax;
    public $total_discounts_without_tax;
    public $total_shipping_without_tax;
    public $total_wrapping_without_tax;
    
    public $total_tax_excl;
    public $total_tax;
    public $total_tax_incl;
    
    public $cart;
    
    public function __construct($id_cart, $payment_type) 
    {
        $this->payment = new ClassMpPaymentConfiguration();
        $this->id = $id_cart;
        $this->payment_type = $payment_type;
        $this->calc();
    }
    
    /**
     * Calculates commissions on the current cart
     * @return boolean true if success, false otherwise
     */
    private function calc()
    {
        /**
         * Get Payment configuration from database. 
         * Function inherited from classMpPaymentConfiguration
         */
        if(!$this->payment->read($this->payment_type)) {
            return false;
        }
        
        //Get total cart
        $this->cart = new Cart($this->id);
        $currency = new CurrencyCore($this->cart->id_currency);
        $this->payment->currency_name = $currency->name;
        $this->payment->currency_decimals = $currency->decimals;
        $this->payment->currency_suffix = $currency->iso_code;
        
        $this->total_cart_with_tax_no_fee = $this->cart->getOrderTotal(true, Cart::BOTH);
        $this->total_products_without_tax = $this->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING);
        $this->total_discounts_without_tax = $this->cart->getOrderTotal(false, Cart::ONLY_DISCOUNTS);
        $this->total_shipping_without_tax = $this->cart->getOrderTotal(false, Cart::ONLY_SHIPPING);
        $this->total_wrapping_without_tax = $this->cart->getOrderTotal(false, Cart::ONLY_WRAPPING);
        
        //Calulate fee
        switch ($this->payment->fee_type) {
            case self::FEE_TYPE_NONE:
                $fee = 0;
                $this->fee_type_processed = 'none';
                break;
            case self::FEE_TYPE_FIXED:
                $fixed = $this->payment->fee_amount;
                $fee = $fixed;
                $this->fee_type_processed = 'fixed';
                break;
            case self::FEE_TYPE_PERCENT:
                $percent = $this->payment->fee_percent;
                $fee = $this->total_cart_with_tax_no_fee * $percent / 100;
                $this->fee_type_processed = 'percent';
                break;
            case self::FEE_TYPE_MIXED:
                $fixed = $this->payment->fee_amount;
                $percent = $this->payment->fee_percent;
                $fee = $fixed + ($this->total_cart_with_tax_no_fee * $percent / 100);
                $this->fee_type_processed = 'mixed';
                break;
            case self::FEE_TYPE_DISCOUNT:
                $percent = $this->payment->discount;
                $fee = -($this->total_cart_with_tax_no_fee * $percent / 100);
                $this->fee_type_processed = 'discount';
                break;
            default:
                $fee = 0;
                break;
        }
        
        //Check restrictions
        if ($this->payment->fee_type!=self::FEE_TYPE_DISCOUNT) {
            if ($this->payment->fee_min!=0 && $this->payment->fee_min>$fee) {
                $fee = $this->payment->fee_min;
            }
            if ($this->payment->fee_max>0 && $this->payment->fee_max<$fee) {
                $fee = $this->payment->fee_max;
            }
        }
        if ($this->payment->order_min!=0 && $this->payment->order_min>$this->total_cart_with_tax_no_fee) {
            $fee=0;
        }
        if ($this->payment->order_max!=0 && $this->payment->order_max<$this->total_cart_with_tax_no_fee) {
            $fee=0;
        }
        if ($this->payment->order_free!=0 && $this->payment->order_free<$this->total_cart_with_tax_no_fee) {
            $fee=0;
        }
            
        if ($this->payment->fee_type==ClassCart::FEE_TYPE_DISCOUNT) {
            $this->total_fee_tax_rate = null;
            $this->total_fee_with_taxes = null;
            $this->total_fee_without_taxes = null;
            $this->total_discount_tax_rate = $this->payment->tax_rate;
            $this->total_discount_with_taxes = $fee;
            $this->total_discount_without_taxes = ($fee / ((100+$this->payment->tax_rate)/100));
            $this->total_cart_with_tax_and_fee = $this->total_cart_with_tax_no_fee - $this->total_discount_with_taxes;
            $this->total_cart_without_tax = $this->total_products_without_tax 
                    + $this->total_shipping_without_tax 
                    - $this->total_discount_without_taxes;
            $this->total_tax_excl = 
                    $this->total_products_without_tax 
                    - $this->total_discounts_without_tax
                    + $this->total_shipping_without_tax
                    - $this->total_discount_without_taxes;
            $this->total_tax_incl = $this->total_cart_with_tax_and_fee;
            $this->total_tax = $this->total_tax_incl - $this->total_tax_excl;
                    
        } else {
            $this->total_fee_tax_rate = $this->payment->tax_rate;
            $this->total_fee_with_taxes = $fee;
            $this->total_fee_without_taxes = ($fee / ((100+$this->payment->tax_rate)/100));
            $this->total_discount_tax_rate = null;
            $this->total_discount_with_taxes = null;
            $this->total_discount_without_taxes = null;
            $this->total_cart_with_tax_and_fee = $this->total_cart_with_tax_no_fee + $this->total_fee_with_taxes;
            $this->total_cart_without_tax = $this->total_products_without_tax 
                    + $this->total_shipping_without_tax 
                    + $this->total_fee_with_taxes;
            $this->total_tax_excl = 
                    $this->total_products_without_tax 
                    - $this->total_discounts_without_tax
                    + $this->total_shipping_without_tax
                    + $this->total_fee_without_taxes;
            $this->total_tax_incl = $this->total_cart_with_tax_and_fee;
            $this->total_tax = $this->total_tax_incl - $this->total_tax_excl;
        }
        
        return true;
    }
}
