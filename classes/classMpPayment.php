<?php

require_once (dirname(__FILE__) . '/classMpPaymentTables.php');
require_once (dirname(__FILE__) . '/classMpPaymentCalc.php');

class classMpPayment extends classPaymentConfiguration{
    const CASH = 'cash';
    const BANKWIRE = 'bankwire';
    const PAYPAL = 'paypal';
    
    const FEE_TYPE_NONE = '0';
    const FEE_TYPE_FIXED = '1';
    const FEE_TYPE_PERCENT = '2';
    const FEE_TYPE_MIXED = '3';
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 
     * @param string $payment_type
     * @param CartCore $cart
     */
    public function calculateFee($payment_type, $cart)
    {
        $this->read($payment_type);
        $total_cart      = $cart->getOrderTotal(true, CartCore::BOTH);
        $total_products  = $cart->getOrderTotal(false, CartCore::ONLY_PRODUCTS_WITHOUT_SHIPPING);
        $total_discounts = $cart->getOrderTotal(false, CartCore::ONLY_DISCOUNTS);
        $total_shipping  = $cart->getOrderTotal(false, CartCore::ONLY_SHIPPING);
        $total_wrapping  = $cart->getOrderTotal(false, CartCore::ONLY_WRAPPING);
        
        $shipping_no_tax = $cart->getTotalShippingCost(null, true);
        
        
        $product_list = classPaymentCalc::getListProductsExclusion($payment_type);
        
        
        
        switch ($this->fee_type) {
            case self::FEE_TYPE_NONE:
                $fee = 0;
                break;
            case self::FEE_TYPE_FIXED:
                $fixed = $this->fee_amount;
                $fee = $fixed;
                break;
            case self::FEE_TYPE_PERCENT:
                $percent = $this->fee_percent;
                $fee = $total_cart * $percent /100;
                break;
            case self::FEE_TYPE_MIXED:
                $fixed = $this->fee_amount;
                $percent = $this->fee_percent;
                $fee = $fixed + ($total * $percent /100);
                break;
            default:
                $fee = 0;
                break;
        }
        
        $output = [
            'total_cart' => $total_cart,
            'total_products' => $total_products,
            'total_discounts' => $total_discounts,
            'total_shipping' => $total_shipping,
            'total_wrapping' => $total_wrapping,
            'total_shipping_no_tax' => $shipping_no_tax,
            'total_fee_with_taxes' => $fee,
            'total_fee_without_taxes' => ($fee / ((100+$this->tax_rate)/100)),
            'total_fee_taxes' => ($fee - ($fee / ((100+$this->tax_rate)/100))),
        ];
        
        return $output;
    }
    
}
