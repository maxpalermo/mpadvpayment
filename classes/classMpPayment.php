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

class classMpPayment
{
    const CASH      = 'cash';
    const BANKWIRE  = 'bankwire';
    const PAYPAL    = 'paypal';
    
    const FEE_TYPE_NONE     = '0';
    const FEE_TYPE_FIXED    = '1';
    const FEE_TYPE_PERCENT  = '2';
    const FEE_TYPE_MIXED    = '3';
    const FEE_TYPE_DISCOUNT = '4';
    
    private $fee;
    private $discount;
    private $payment_type;
    
    public function __construct()
    {
        $this->discount = 0;
        $this->fee = 0;
    }
    
    public function getFee()
    {
        return $this->fee;
    }
    
    public function getDiscount()
    {
        return $this->discount;
    }
    
    /**
     * Get cart fee values
     * @param string $payment_type
     * @param Cart $cart
     * @return bool 
     */
    public function calculateFee($payment_type)
    {
        /**
         * Get cart
         */
        $cart = new Cart(ContextCore::getContext()->cart->id);
        /**
         * Get Payment configuration
         */
        $payment = new classMpPaymentConfiguration();
        $payment->read($payment_type);
        
        //Get total cart
        $total_cart = $cart->getOrderTotal(true, Cart::BOTH);
        
        //Calulate fee
        switch ($payment->fee_type) {
            case self::FEE_TYPE_NONE:
                $fee = 0;
                break;
            case self::FEE_TYPE_FIXED:
                $fixed = $payment->fee_amount;
                $fee = $fixed;
                break;
            case self::FEE_TYPE_PERCENT:
                $percent = $payment->fee_percent;
                $fee = $total_cart * $percent / 100;
                break;
            case self::FEE_TYPE_MIXED:
                $fixed = $payment->fee_amount;
                $percent = $payment->fee_percent;
                $fee = $fixed + ($total_cart * $percent / 100);
                break;
            case self::FEE_TYPE_DISCOUNT:
                $percent = $payment->discount;
                $fee = -($total_cart * $percent / 100);
                break;
            default:
                $fee = 0;
                break;
        }
        
        //Check restrictions
        if ($payment->fee_type!=self::FEE_TYPE_DISCOUNT) {
            if ($payment->fee_min!=0 && $payment->fee_min>$fee) {
                $fee = $payment->fee_min;
            }
            if ($payment->fee_max>0 && $payment->fee_max<$fee) {
                $fee = $payment->fee_max;
            }
        }
        
        if ($payment->order_min!=0 && $payment->order_min>$total_cart) {
            $fee=0;
        }
        if ($payment->order_max!=0 && $payment->order_max<$total_cart) {
            $fee=0;
        }
        if ($payment->order_free!=0 && $payment->order_free<$total_cart) {
            $fee=0;
        }

        if ($payment->fee_type == classCart::FEE_TYPE_DISCOUNT) {
            $this->discount = -number_format($fee,6);
            $this->fee = 0;
        } else {
            $this->discount = 0;
            $this->fee = number_format($fee,6);
        }
        $this->payment_type = $payment_type;
        
        return true;
    }
}
