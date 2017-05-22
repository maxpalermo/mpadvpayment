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

require_once(dirname(__FILE__).'/../../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../../init.php');
require_once(dirname(__FILE__).'/../classes/autoload.php');

$class = Tools::getValue('class');

if (empty($class)) {
    exit();
}


$pay_in   = Tools::jsonDecode($class);
$pay_out  = new classMpPaymentConfiguration();

$pay_out->is_active      = $pay_in->active;
$pay_out->fee_type       = $pay_in->fee_type;
$pay_out->discount       = $pay_in->discount;
$pay_out->fee_amount     = $pay_in->fee_amount;
$pay_out->fee_percent    = $pay_in->fee_percent;
$pay_out->fee_min        = $pay_in->fee_min;
$pay_out->fee_max        = $pay_in->fee_max;
$pay_out->order_min      = $pay_in->order_min;
$pay_out->order_max      = $pay_in->order_max;
$pay_out->order_free     = $pay_in->order_free;
$pay_out->tax_included   = $pay_in->tax_included;
$pay_out->tax_rate       = $pay_in->tax_rate;
$pay_out->carriers       = is_array($pay_in->carriers)?implode(",", $pay_in->carriers):$pay_in->carriers;
$pay_out->categories     = is_array($pay_in->categories)?implode(",", $pay_in->categories):$pay_in->categories;
$pay_out->manufacturers  = is_array($pay_in->manufacturers)?implode(",", $pay_in->manufacturers):$pay_in->manufacturers;
$pay_out->suppliers      = is_array($pay_in->suppliers)?implode(",", $pay_in->suppliers):$pay_in->suppliers;
$pay_out->products       = is_array($pay_in->products)?implode(",", $pay_in->products):$pay_in->products;
$pay_out->id_order_state = $pay_in->id_order_state;
$pay_out->payment_type   = $pay_in->payment_type;
$pay_out->logo           = $pay_in->logo;
$pay_out->data           = $pay_in->data;
print "RESULT: " . $pay_out->save();

print_r($pay_out);

exit();
