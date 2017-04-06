<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(__FILE__).'/../../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../../init.php');

$id_join_attributes = Tools::getValue("combinations");
$id_products  = Tools::getValue("id_products");

if(empty($id_join_attributes) ||empty($id_products)) {
    print "no values";
    exit();
}

$actions = "<i class='icon-edit' onclick='editRow(this,3);'></i>  " 
    . "<i class='icon-remove' onclick='deleteRow(this);'>"; 

$db = Db::getInstance();
$attributes = [];
$prods = [];

//Get Attributes
$id_attributes = explode(";", $id_join_attributes);
foreach($id_attributes as $id_attr)
{
    $attr = new AttributeCore($id_attr);
    foreach($attr->name as $name)
    {
        $attributes[] = $name;
    }
}

//Get products
if (count($id_products==1)) {
    $prod = new ProductCore($id_products[0]);
    foreach($prod->name as $name)
    {
        $prods[] = $name;
    }
    $ref = $prod->reference;
} else {
    foreach($id_products as $id_prod)
    {
        $prod = new ProductCore($id_prod);
        foreach($prod->name as $name)
        {
            $prods[] = $name;
        }
    }
    $ref = "";
}

$row = 
    "<tr>"
        ."<td>"
            . "<input type='hidden' value='". implode(";", $id_attributes) . "'>"
            . implode(", ", $attributes)
        . "</td>"
        ."<td>" 
            . "<input type='hidden' value='". implode(";", $id_products) . "'>"
            . implode(", ", $prods)
        . "</td>"
        ."<td>$ref</td>"
        ."<td></td>"
        ."<td></td>"
        ."<td></td>"
        ."<td></td>"
        ."<td style='text-align: right;'>0.000000</td>"
        ."<td style='text-align: right;'>0.000000</td>"
        ."<td></td>"
        ."<td style='text-align: right;'>0</td>"
        ."<td style='text-align: right;'>0.00</td>"
        ."<td style='text-align: right;'>0.000000</td>"
        ."<td></td>"
        ."<td style='text-align: right;'>0</td>"
        ."<td style='text-align: right;'>0000-00-00 00:00:00</td>"
        ."<td></td>"
        ."<td style='text-align: center;'>" . $actions . "</td>"
    ."</tr>";
print $row;
exit();