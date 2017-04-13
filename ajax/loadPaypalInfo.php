<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(__FILE__).'/../../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../../init.php');

$test      = ConfigurationCore::get('MP_ADVPAYMENT_PAYPAL_TEST');
$user      = ConfigurationCore::get('MP_ADVPAYMENT_PAYPAL_USER_API');
$password  = ConfigurationCore::get('MP_ADVPAYMENT_PAYPAL_PWD_API');
$signature = ConfigurationCore::get('MP_ADVPAYMENT_PAYPAL_SIGN_API');

$output = new stdClass();
$output->test      = $test;
$output->user      = $user;
$output->password  = $password;
$output->signature = $signature;

print Tools::jsonEncode($output);

exit();