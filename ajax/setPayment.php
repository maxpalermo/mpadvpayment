<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(__FILE__).'/../../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../../init.php');
require_once(dirname(__FILE__).'/../classes/classMpPaymentTables.php');

$class = Tools::getValue('class');
if(empty($class)) {
    exit();
}
$pay_in   = Tools::jsonDecode($class);
$pay_out  = new classPaymentConfiguration();

$pay_out->is_active         = $pay_in->active;
$pay_out->fee_type          = $pay_in->fee_type;
$pay_out->fee_amount        = $pay_in->fee_amount;
$pay_out->fee_percent       = $pay_in->fee_percent;
$pay_out->fee_min           = $pay_in->fee_min;
$pay_out->fee_max           = $pay_in->fee_max;
$pay_out->order_min         = $pay_in->order_min;
$pay_out->order_max         = $pay_in->order_max;
$pay_out->order_free        = $pay_in->order_free;
$pay_out->tax_included      = $pay_in->tax_included;
$pay_out->tax_rate          = $pay_in->tax_rate;
$pay_out->carriers          = is_array($pay_in->carriers)?implode(",", $pay_in->carriers):$pay_in->carriers;
$pay_out->categories        = is_array($pay_in->categories)?implode(",", $pay_in->categories):$pay_in->categories;
$pay_out->manufacturers     = is_array($pay_in->manufacturers)?implode(",", $pay_in->manufacturers):$pay_in->manufacturers;
$pay_out->suppliers         = is_array($pay_in->suppliers)?implode(",", $pay_in->suppliers):$pay_in->suppliers;
$pay_out->products          = is_array($pay_in->products)?implode(",", $pay_in->products):$pay_in->products;
$pay_out->id_order_state    = $pay_in->id_order_state; 
$pay_out->payment_type      = $pay_in->payment_type;
print "RESULT: " . $pay_out->save();

print_r($pay_out);

exit();