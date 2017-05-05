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

$test       = Tools::getValue("test", '');
$user       = Tools::getValue("user", '');
$password   = Tools::getValue("password", '');
$signature  = Tools::getValue("signature", '');
$test_id    = Tools::getValue("test_id", '');
$paypal_pro = Tools::getValue("paypal_pro", '');
$email      = Tools::getValue("email", '');

ConfigurationCore::updateValue('MP_ADVPAYMENT_PAYPAL_TEST_API', $test);
ConfigurationCore::updateValue('MP_ADVPAYMENT_PAYPAL_USER_API', $user);
ConfigurationCore::updateValue('MP_ADVPAYMENT_PAYPAL_PWD_API', $password);
ConfigurationCore::updateValue('MP_ADVPAYMENT_PAYPAL_SIGN_API', $signature);
ConfigurationCore::updateValue('MP_ADVPAYMENT_PAYPAL_TEST_API', $test_id);
ConfigurationCore::updateValue('MP_ADVPAYMENT_PAYPAL_PRO_API', $paypal_pro);
ConfigurationCore::updateValue('MP_ADVPAYMENT_PAYPAL_EMAIL_API', $email);

exit();
