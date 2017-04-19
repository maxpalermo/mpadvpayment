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

$test      = ConfigurationCore::get('MP_ADVPAYMENT_PAYPAL_TEST');
$user      = ConfigurationCore::get('MP_ADVPAYMENT_PAYPAL_USER_API');
$password  = ConfigurationCore::get('MP_ADVPAYMENT_PAYPAL_PWD_API');
$signature = ConfigurationCore::get('MP_ADVPAYMENT_PAYPAL_SIGN_API');
$test_id   = ConfigurationCore::get('MP_ADVPAYMENT_PAYPAL_TEST_API');

$output = new stdClass();
$output->test      = $test;
$output->user      = $user;
$output->password  = $password;
$output->signature = $signature;
$output->test_id   = $test_id;

print Tools::jsonEncode($output);

exit();
