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

class classCustomer {
    public $first_name;
    public $last_name;
    public $address1;
    public $address2;
    public $city;
    public $state;
    public $zip;
    public $country;
    public $email;
    public $phone_prefix;
    public $phone_number;
    
    /**
     * Initialize class and fills values
     * @param int $id_cart Cart id
     * @param AddressCore $address class Address
     */
    public function __construct($address) 
    {
        $state = new StateCore($address->id_state);
        $country = new CountryCore($address->id_country);
        $customer = new CustomerCore($address->id_customer);
        
        $this->first_name = $address->firstname;
        $this->last_name = $address->lastname;
        $this->address1 = $address->address1;
        $this->address2 = $address->address2;
        $this->city = $address->city;
        $this->state = $state->name;
        $this->zip = $address->postcode;
        $this->country = $country->iso_code;
        $this->email = $customer->email;
        $this->phone_prefix = $country->call_prefix;
        $this->phone_number = empty($address->phone)?$address->phone_mobile:$address->phone;
    }
}

class classCart extends ClassMpPaymentConfiguration{
    const CASH      = 'cash';
    const BANKWIRE  = 'bankwire';
    const PAYPAL    = 'paypal';
    
    const FEE_TYPE_NONE     = '0';
    const FEE_TYPE_FIXED    = '1';
    const FEE_TYPE_PERCENT  = '2';
    const FEE_TYPE_MIXED    = '3';
    const FEE_TYPE_DISCOUNT = '4';
    
    private $id;
    private $payment_type;
    public $subtotal;
    public $products;
    public $discounts;
    public $shipping;
    public $wrapping;
    public $fee_no_tax;
    public $fee_with_tax;
    public $fee_taxes;
    public $total;
    public $currency;
    public $decimals;
    public $suffix;
    
    
    public function __construct($id_cart, $payment_type) 
    {
        parent::__construct();
        $this->id = $id_cart;
        $this->payment_type = $payment_type;
        $this->calc();
    }
    
    private function calc()
    {
        //Get Payment cnfiguration
        $this->read($this->payment_type);
        
        //Get total cart
        $cart = new CartCore($this->id);
        $total_cart      = $cart->getOrderTotal(true, CartCore::BOTH);
        $total_products  = $cart->getOrderTotal(false, CartCore::ONLY_PRODUCTS_WITHOUT_SHIPPING);
        $total_discounts = $cart->getOrderTotal(false, CartCore::ONLY_DISCOUNTS);
        $total_shipping  = $cart->getOrderTotal(false, CartCore::ONLY_SHIPPING);
        $total_wrapping  = $cart->getOrderTotal(false, CartCore::ONLY_WRAPPING);
        $shipping_no_tax = $cart->getTotalShippingCost(null, true);
        
        //Calulate fee
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
                $fee = $total_cart * $percent / 100;
                break;
            case self::FEE_TYPE_MIXED:
                $fixed = $this->fee_amount;
                $percent = $this->fee_percent;
                $fee = $fixed + ($total_cart * $percent / 100);
                break;
            case self::FEE_TYPE_DISCOUNT:
                $percent = $this->discount;
                $fee = ($total_cart * $percent / 100);
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
            );
        }
        
        $this->id = (int)$result['id'];
        $this->subtotal = (float)$result['subtotal'];
        $this->fee = (float)$result['fee'];
        $this->total = (float)$result['total'];
        $this->currency = $currency->name;
        $this->decimals = $currency->decimals;
        $this->suffix = $currency->suffix;
        
        $this->total_cart = $total_cart;
        $this->total_products=$total_products;
        $this->total_discounts=$total_discounts;
        $this->total_shipping=$total_shipping;
        $this->total_wrapping=$total_wrapping;
        $this->total_shipping_no_tax=$shipping_no_tax;
        $this->total_fee_with_taxes=$output['total_fee_with_taxes'];
        $this->total_fee_without_taxes=$output['total_fee_without_taxes'];
        $this->total_fee_taxes = $output['total_fee_taxes'];
        
        return $output;
    }
}

class classURL {
    public $action;
    public $cancel;
    public $error;
    public $success;
    public $notify;
    
    public function __construct() 
    {
        $link = new LinkCore();
        
        $this->cancel = $link->getModuleLink('mpadvpayment', 'card', array('cancel' => '1'));
        $this->return = $link->getModuleLink('mpadvpayment', 'card', array('success' => '1'));
        $this->notify = $link->getModuleLink('mpadvpayment', 'card', array('notify' => '1'));
        $this->erorr = $link->getModuleLink('mpadvpayment', 'card', array('error' => '1'));
        $this->action = Tools::getValue('action', '');
    }
}

class classCustomerMain {
    /**
     *
     * @var classCustomer $shipping Shipping Customer values
     */
    public $shipping;
    /**
     *
     * @var classCustomer $billing Invoice Customer values
     */
    public $billing;
    
    /**
     * Initialize class and fills values
     * @param int $id_cart cart id
     */
    public function __construct($id_cart) {
        $cart = new CartCore($id_cart);
        
        $this->shipping = new classCustomer(new AddressCore($cart->id_address_delivery));
        $this->billing = new classCustomer(new AddressCore($cart->id_address_invoice));
    }
}

class classPaypalSummary {
    public $test;
    public $user;
    public $password;
    public $signature;
    public $email;
    public $image;
    public $cart;
    public $URL;
    public $customer;
    
    public function __construct($id_cart, $url) 
    {
        $this->test = (bool)ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_TEST_API");
        $this->user = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_USER_API");
        $this->password = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_PWD_API");
        $this->signature = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_SIGN_API");
        $this->email_business = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_EMAIL_API");
        
        $image_file = glob(_MPADVPAYMENT_ . "paypal_logo.*");
        if ($image_file) {
            $filename = _MPADVPAYMENT_URL_ . basename($image_file[0]);
        } else {
            $filename = "";
        }
        
        $this->cart = new classCart($id_cart, 'paypal');
        $this->customer = new classCustomerMain($id_cart);
        $this->URL = new classURL();
        
        
    }
}

class classSummary {
    public $cash;
    public $bankwire;
    public $paypal;
    public $id_cart;
    public $payment_type;
    
    /**
     * Initialize class and fills values
     * @param int $id_cart cart id
     * @param string $paymentType Type Payment ['cash', 'bankwire', 'paypal']
     */
    public function __construct($id_cart, $payment_type) 
    {
        $cart = new CartCore($id_cart);
        $this->paypal = new classPaypalSummary($cart->id, $url);
    }
}
