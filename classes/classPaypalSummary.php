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
    
    public function __construct($id_cart) 
    {
        $this->test = (bool)ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_TEST_API");
        $this->user = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_USER_API");
        $this->password = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_PWD_API");
        $this->signature = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_SIGN_API");
        $this->email = ConfigurationCore::get("MP_ADVPAYMENT_PAYPAL_EMAIL_API");
        
        $image_file = glob(_MPADVPAYMENT_ . "paypal_logo.*");
        if ($image_file) {
            $filename = _MPADVPAYMENT_URL_ . basename($image_file[0]);
        } else {
            $filename = "";
        }
        
        $this->cart = new classCart($id_cart, 'paypal');
        $this->customer = new classCustomerMain($id_cart);
        $this->URL = new classURL();
        $this->image = $filename;
    }
}