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

class classCart extends ClassMpPaymentConfiguration{
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
    
    public $total_cart;
    public $total_products;
    public $total_discounts;
    public $total_shipping;
    public $total_wrapping;
    public $total_shipping_no_tax;
    public $total_fee_with_taxes;
    public $total_fee_without_taxes;
    public $total_fee_taxes;
    public $tax_rate;
    public $total;
    
    public $currency;
    public $decimals;
    public $suffix;
    public $fee_type_processed;
    
    public function __construct($id_cart, $payment_type) 
    {
        parent::__construct();
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
        if(!$this->read($this->payment_type)) {
            return false;
        }
        
        //Get total cart
        $cart = new Cart($this->id);
        $currency = new CurrencyCore($cart->id_currency);
        $total_cart      = $cart->getOrderTotal(true, Cart::BOTH);
        $total_products  = $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING);
        $total_discounts = $cart->getOrderTotal(false, Cart::ONLY_DISCOUNTS);
        $total_shipping  = $cart->getOrderTotal(false, Cart::ONLY_SHIPPING);
        $total_wrapping  = $cart->getOrderTotal(false, Cart::ONLY_WRAPPING);
        $shipping_no_tax = $cart->getTotalShippingCost(null, true);
        
        //Calulate fee
        switch ($this->fee_type) {
            case self::FEE_TYPE_NONE:
                $fee = 0;
                $this->fee_type_processed = 'none';
                break;
            case self::FEE_TYPE_FIXED:
                $fixed = $this->fee_amount;
                $fee = $fixed;
                $this->fee_type_processed = 'fixed';
                break;
            case self::FEE_TYPE_PERCENT:
                $percent = $this->fee_percent;
                $fee = $total_cart * $percent / 100;
                $this->fee_type_processed = 'percent';
                break;
            case self::FEE_TYPE_MIXED:
                $fixed = $this->fee_amount;
                $percent = $this->fee_percent;
                $fee = $fixed + ($total_cart * $percent / 100);
                $this->fee_type_processed = 'mixed';
                break;
            case self::FEE_TYPE_DISCOUNT:
                $percent = $this->discount;
                $fee = ($total_cart * $percent / 100);
                $this->fee_type_processed = 'discount';
                break;
            default:
                $fee = 0;
                break;
        }
        
        //Check restrictions
        if ($this->fee_type!=self::FEE_TYPE_DISCOUNT) {
            if ($this->fee_min!=0 && $this->fee_min>$fee) {
                $fee = $this->fee_min;
            }
            if ($this->fee_max>0 && $this->fee_max<$fee) {
                $fee = $this->fee_max;
            }
            if ($this->order_min!=0 && $this->order_min>$total_cart) {
                $fee=0;
            }
            if ($this->order_max!=0 && $this->order_max<$total_cart) {
                $fee=0;
            }
            if ($this->order_free!=0 && $this->order_free<$total_cart) {
                $fee=0;
            }
            
            $output = array(
                'total_cart' => $total_cart,
                'total_products' => $total_products,
                'total_discounts' => $total_discounts,
                'total_shipping' => $total_shipping,
                'total_wrapping' => $total_wrapping,
                'total_shipping_no_tax' => $shipping_no_tax,
                'total_fee_with_taxes' => $fee,
                'total_fee_without_taxes' => ($fee / ((100+$this->tax_rate)/100)),
                'total_fee_taxes' => ($fee - ($fee / ((100+$this->tax_rate)/100))),
                'fee_type' => $this->fee_type,
                'fee_label' => 'fee',
                'fee_tax_rate' => $this->tax_rate,
                'currency'=> $currency->name,
                'decimals' => $currency->decimals,
                'suffix' => $currency->suffix,
            );
        } else {
            $output = array(
                'total_cart' => $total_cart,
                'total_products' => $total_products,
                'total_discounts' => $total_discounts,
                'total_shipping' => $total_shipping,
                'total_wrapping' => $total_wrapping,
                'total_shipping_no_tax' => $shipping_no_tax,
                'total_fee_with_taxes' => -$fee,
                'total_fee_without_taxes' => -($fee / ((100+$this->tax_rate)/100)),
                'total_fee_taxes' => -($fee - ($fee / ((100+$this->tax_rate)/100))),
                'fee_type' => $this->fee_type,
                'fee_label' => 'discount',
                'fee_tax_rate' => $this->tax_rate,
                'currency'=> $currency->name,
                'decimals' => $currency->decimals,
                'suffix' => $currency->suffix,
            );
        }
        
        $this->total_cart = $total_cart;
        $this->total_products=$total_products;
        $this->total_discounts=$total_discounts;
        $this->total_shipping=$total_shipping;
        $this->total_wrapping=$total_wrapping;
        $this->total_shipping_no_tax=$shipping_no_tax;
        $this->total_fee_with_taxes=$output['total_fee_with_taxes'];
        $this->total_fee_without_taxes=$output['total_fee_without_taxes'];
        $this->total_fee_taxes = $output['total_fee_taxes'];
        $this->tax_rate = $output['fee_tax_rate'];
        $this->total = $total_cart + $this->total_fee_with_taxes;
        $this->currency = $currency->name;
        $this->currency_decimals = $currency->decimals;
        $this->currency_suffix = $currency->suffix;
        
        return true;
    }
}
