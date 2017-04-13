<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(__FILE__).'/../../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../../init.php');

$owner = ConfigurationCore::get('MP_ADVPAYMENT_BANKWIRE_OWNER');
$iban  = ConfigurationCore::get('MP_ADVPAYMENT_BANKWIRE_IBAN');
$bank  = ConfigurationCore::get('MP_ADVPAYMENT_BANKWIRE_BANK');
$addr  = ConfigurationCore::get('MP_ADVPAYMENT_BANKWIRE_ADDR');

$output = new stdClass();
$output->owner = $owner;
$output->iban  = $iban;
$output->bank  = $bank;
$output->addr  = $addr;

print Tools::jsonEncode($output);

exit();