<?php

require_once (dirname(__FILE__) . '/classMpPaymentTables.php');

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
        $total = $cart->getTotalCart($cart->id, true, CartCore::BOTH);
        
        switch ($this->fee_type) {
            case self::FEE_TYPE_NONE:
                return 0;
            case self::FEE_TYPE_FIXED:
                $fixed = $this->fee_amount;
                return $fixed;
            case self::FEE_TYPE_PERCENT:
                $percent = $this->fee_percent;
                return $total * $percent /100;
            case self::FEE_TYPE_MIXED:
                $fixed = $this->fee_amount;
                $percent = $this->fee_percent;
                $fee = $fixed + ($total * $percent /100);
                return $fee;
            default:
                break;
        }
    }
    
}
