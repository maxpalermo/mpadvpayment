<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(__FILE__).'/../../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../../init.php');

$owner = Tools::getValue("owner",'');
$iban = Tools::getValue("iban",'');
$bank = Tools::getValue("bank",'');
$addr = Tools::getValue("addr",'');

ConfigurationCore::updateValue('MP_ADVPAYMENT_BANKWIRE_OWNER', $owner);
ConfigurationCore::updateValue('MP_ADVPAYMENT_BANKWIRE_IBAN', $iban);
ConfigurationCore::updateValue('MP_ADVPAYMENT_BANKWIRE_BANK', $bank);
ConfigurationCore::updateValue('MP_ADVPAYMENT_BANKWIRE_ADDR', $addr);

exit();