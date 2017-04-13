<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(__FILE__).'/../../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../../init.php');

$test      = Tools::getValue("test",'');
$user      = Tools::getValue("user",'');
$password  = Tools::getValue("password",'');
$signature = Tools::getValue("signature",'');

ConfigurationCore::updateValue('MP_ADVPAYMENT_PAYPAL_TEST', $test);
ConfigurationCore::updateValue('MP_ADVPAYMENT_PAYPAL_USER_API', $user);
ConfigurationCore::updateValue('MP_ADVPAYMENT_PAYPAL_PWD_API', $password);
ConfigurationCore::updateValue('MP_ADVPAYMENT_PAYPAL_SIGN_API', $signature);

exit();