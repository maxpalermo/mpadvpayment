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
    
    public $payment;
    
    private $id;
    private $fee;
    private $fee_no_tax;
    private $discount;
    private $payment_type;
    private $tax_rate;
    private $total_cart;
    private $total_to_pay;
    private $shipping;
    private $voucher;
    
    public function __construct($id_cart, $payment_type) 
    {
        if($id_cart == 0) {
            return;
        }
        $this->payment = new classMpPaymentConfiguration();
        $this->id = $id_cart;
        $this->payment_type = $payment_type;
        $this->voucher = false; //if true, module adds a voucher for current cart
        $this->shipping = 0;
        $this->calc();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getFee()
    {
        return number_format($this->fee, 6);
    }
    
    public function getFeeTaxRate()
    {
        return $this->tax_rate;
    }
    
    public function getFeeNoTax()
    {
        return number_format($this->fee_no_tax, 6);
    }
    
    public function getDiscount()
    {
        return number_format($this->discount, 6);
    }
    
    public function getPaymentType()
    {
        return $this->payment_type;
    }
    
    public function getTaxRate()
    {
        return $this->tax_rate;
    }
    
    public function getTotalCart()
    {
        return $this->total_cart;
    }
    
    public function getTotalToPay()
    {
        return number_format($this->total_to_pay, 6);
    }
    
    public function isVoucher()
    {
        return $this->voucher;
    }
    
    public function getShipping()
    {
        return number_format($this->shipping, 6);
    }
    
    /**
     * Calculates commissions on the current cart
     * @return boolean true if success, false otherwise
     */
    private function calc()
    {
        if(!$this->payment->read($this->payment_type)) {
            return false;
        }
        
        //Get total cart
        $cart = new Cart($this->id);
        $currency = new CurrencyCore($cart->id_currency);
        $this->payment->currency_name = $currency->name;
        $this->payment->currency_decimals = $currency->decimals;
        $this->payment->currency_suffix = $currency->iso_code;
        
        $this->total_cart = $cart->getOrderTotal(true, Cart::BOTH);
        
        //Calulate fee
        switch ($this->payment->fee_type) {
            case self::FEE_TYPE_NONE:
                $fee = 0;
                break;
            case self::FEE_TYPE_FIXED:
                $fixed = $this->payment->fee_amount;
                $fee = $fixed;
                break;
            case self::FEE_TYPE_PERCENT:
                $percent = $this->payment->fee_percent;
                $fee = number_format($this->total_cart * $percent / 100, 2);
                break;
            case self::FEE_TYPE_MIXED:
                $fixed = $this->payment->fee_amount;
                $percent = $this->payment->fee_percent;
                $fee = number_format($fixed + ($this->total_cart * $percent / 100), 2);
                break;
            case self::FEE_TYPE_DISCOUNT:
                $percent = $this->payment->discount;
                $fee = number_format($this->total_cart * $percent / 100, 2);
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
        if ($this->payment->order_min!=0 && $this->payment->order_min>$this->total_cart) {
            $fee=0;
        }
        if ($this->payment->order_max!=0 && $this->payment->order_max<$this->total_cart) {
            $fee=0;
        }
        if ($this->payment->order_free!=0 && $this->payment->order_free<$this->total_cart) {
            $fee=0;
        }
        
        $this->fee = number_format($fee,6);
        
        if ($this->payment->fee_type==ClassCart::FEE_TYPE_DISCOUNT) {
            $this->fee = -$this->fee;
        }
        
        $this->tax_rate = $this->payment->tax_rate;
        
        return true;
    }
}
