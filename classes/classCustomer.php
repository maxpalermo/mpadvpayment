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
