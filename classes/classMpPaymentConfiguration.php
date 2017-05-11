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

require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . "CRUD.php");

class ClassMpPaymentConfiguration extends CRUD
{
    public $id_configuration;
    public $fee_type;
    public $fee_amount;
    public $fee_percent;
    public $fee_min;
    public $fee_max;
    public $order_min;
    public $order_max;
    public $order_free;
    public $discount;
    public $tax_included;
    public $tax_rate;
    public $carriers;
    public $categories;
    public $manufacturers;
    public $suppliers;
    public $products;
    public $id_order_state;
    public $payment_type;
    public $is_active;
    public $logo;
    public $data;
    public $currency_name;
    public $currency_decimals;
    public $currency_suffix;
    
    private $tablename;
    
    public function __construct()
    {
        $this->tablename = 'mp_advpayment_configuration';
    }

    public function create()
    {
        $db = Db::getInstance();
        try {
            $id = $db->insert($this->tablename,
                    array(
                        'fee_type' => $this->fee_type,
                        'fee_amount' => $this->fee_amount,
                        'fee_percent' => $this->fee_percent,
                        'fee_min' => $this->fee_min,
                        'fee_max' => $this->fee_max,
                        'order_min' => $this->order_min,
                        'order_max' => $this->order_max,
                        'order_free' => $this->order_free,
                        'discount' => $this->discount,
                        'tax_included' => $this->tax_included,
                        'tax_rate' => $this->tax_rate,
                        'carriers' => $this->carriers,
                        'categories' => $this->categories,
                        'manufacturers' => $this->manufacturers,
                        'suppliers' => $this->suppliers,
                        'products' => $this->products,
                        'id_order_state' => $this->id_order_state,
                        'payment_type' => $this->payment_type,
                        'is_active' => $this->is_active
                        )
                    );
        } catch (Exception $exc) {
            return $exc->getMessage();
        }
        return $id;
    }

    public function delete()
    {
        $db = Db::getInstance();
        return $db->delete($this->tablename, 'id_configuration = ' . $this->id_configuration);
    }
    
    /**
     * Read payment parameters from db table and fill class
     * @param string $payment_type type of payment ('cash','bankwire','paypal')
     * @return boolean true if success, false otherwise
     */
    public function read($payment_type)
    {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        
        $sql    ->select("*")
                ->from($this->tablename)
                ->where("payment_type = '" . $payment_type . "'");
        
        $result = $db->getRow($sql);
        
        if(empty($result)) {
            return false;
        }
        
        $this->fee_type = $result['fee_type'];
        $this->fee_amount = $result['fee_amount'];
        $this->fee_percent = $result['fee_percent'];
        $this->fee_min = $result['fee_min'];
        $this->fee_max = $result['fee_max'];
        $this->order_min = $result['order_min'];
        $this->order_max = $result['order_max'];
        $this->order_free = $result['order_free'];
        $this->discount = $result['discount'];
        $this->tax_included = $result['tax_included'];
        $this->tax_rate = $result['tax_rate'];
        $this->carriers = $result['carriers'];
        $this->categories = $result['categories'];
        $this->manufacturers = $result['manufacturers'];
        $this->suppliers = $result['suppliers'];
        $this->products = $result['products'];
        $this->id_order_state = $result['id_order_state'];
        $this->payment_type = $result['payment_type'];
        $this->is_active = $result['is_active'];
        
        return true;
    }

    public function update()
    {
        $db = Db::getInstance();
        try {
            $id = $db->update($this->tablename,
                    array(
                        'fee_type' => $this->fee_type,
                        'fee_amount' => $this->fee_amount,
                        'fee_percent' => $this->fee_percent,
                        'fee_min' => $this->fee_min,
                        'fee_max' => $this->fee_max,
                        'order_min' => $this->order_min,
                        'order_max' => $this->order_max,
                        'order_free' => $this->order_free,
                        'discount' => $this->discount,
                        'tax_included' => $this->tax_included,
                        'tax_rate' => $this->tax_rate,
                        'carriers' => $this->carriers,
                        'categories' => $this->categories,
                        'manufacturers' => $this->manufacturers,
                        'suppliers' => $this->suppliers,
                        'products' => $this->products,
                        'id_order_state' => $this->id_order_state,
                        'payment_type' => $this->payment_type,
                        'is_active' => $this->is_active
                        ),
                    'id_configuration = ' . $this->id_configuration
                    );
        } catch (Exception $exc) {
            return $exc->getMessage();
        }
        return $id;
    }

    public function save()
    {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        
        $sql    ->select('id_configuration')
                ->from($this->tablename)
                ->where("payment_type = '" . $this->payment_type . "'");
        
        $result = $db->getValue($sql);
        if (empty($result)) {
            $this->create();
        } else {
            $this->id_configuration = $result;
            $this->update();
        }
    }
}

