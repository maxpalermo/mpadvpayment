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

abstract class CRUD {
    public abstract function create();
    public abstract function read($payment_type);
    public abstract function update();
    public abstract function delete();
    public abstract function save();
}

/**
 * Description of classPaymentConfiguration
 *
 * @author Massimiliano Palermo <maxx.palermo@gmail.com>
 */
class classPaymentConfiguration extends CRUD{
    public $id_configuration;
    public $fee_type;
    public $fee_amount;
    public $fee_percent;
    public $fee_min;
    public $fee_max;
    public $order_min;
    public $order_max;
    public $order_free;
    public $tax_included;
    public $tax_rate;
    public $carriers;
    public $categories;
    public $manufacturers;
    public $suppliers;
    public $products;
    public $payment_type;
    public $is_active;
    
    private $tablename;
    
    public function __construct() {
        $this->tablename = 'mp_advpayment_configuration';
    }

    public function create() {
        $db = Db::getInstance();
        try {
            $id = $db->insert($this->tablename,
                    [
                        'fee_type' => $this->fee_type,
                        'fee_amount' => $this->fee_amount,
                        'fee_percent' => $this->fee_percent,
                        'fee_min' => $this->fee_min,
                        'fee_max' => $this->fee_max,
                        'order_min' => $this->order_min,
                        'order_max' => $this->order_max,
                        'order_free' => $this->order_free,
                        'tax_included' => $this->tax_included,
                        'tax_rate' => $this->tax_rate,
                        'carriers' => $this->carriers,
                        'categories' => $this->categories,
                        'manufacturers' => $this->manufacturers,
                        'suppliers' => $this->suppliers,
                        'products' => $this->products,
                        'payment_type' => $this->payment_type,
                        'is_active' => $this->is_active
                    ]
                    );
        } catch (Exception $exc) {
            return $exc->getMessage();
        }
        return $id;
    }

    public function delete() {
        $db = Db::getInstance();
        return $db->delete($this->tablename, 'id_configuration = ' . $this->id_configuration);
    }

    public function read($payment_type) {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        
        $sql    ->select("*")
                ->from($this->tablename)
                ->where("payment_type = '" . $payment_type . "'");
        
        $result = $db->getRow($sql);
        $this->fee_type = $result['fee_type'];
        $this->fee_amount = $result['fee_amount'];
        $this->fee_percent = $result['fee_percent'];
        $this->fee_min = $result['fee_min'];
        $this->fee_max = $result['fee_max'];
        $this->order_min = $result['order_min'];
        $this->order_max = $result['order_max'];
        $this->order_free = $result['order_free'];
        $this->tax_included = $result['tax_included'];
        $this->tax_rate = $result['tax_rate'];
        $this->carriers = $result['carriers'];
        $this->categories = $result['categories'];
        $this->manufacturers = $result['manufacturers'];
        $this->suppliers = $result['suppliers'];
        $this->products = $result['products'];
        $this->payment_type = $result['payment_type'];
        $this->is_active = $result['is_active'];
    }

    public function update() {
        $db = Db::getInstance();
        try {
            $id = $db->update($this->tablename,
                    [
                        'fee_type' => $this->fee_type,
                        'fee_amount' => $this->fee_amount,
                        'fee_percent' => $this->fee_percent,
                        'fee_min' => $this->fee_min,
                        'fee_max' => $this->fee_max,
                        'order_min' => $this->order_min,
                        'order_max' => $this->order_max,
                        'order_free' => $this->order_free,
                        'tax_included' => $this->tax_included,
                        'tax_rate' => $this->tax_rate,
                        'carriers' => $this->carriers,
                        'categories' => $this->categories,
                        'manufacturers' => $this->manufacturers,
                        'suppliers' => $this->suppliers,
                        'products' => $this->products,
                        'payment_type' => $this->payment_type,
                        'is_active' => $this->is_active
                    ],
                    'id_configuration = ' . $this->id_configuration
                    );
        } catch (Exception $exc) {
            return $exc->getMessage();
        }
        return $id;
    }

    public function save() {
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


class classPaymentOrders extends CRUD {
    public $id_cart;
    public $id_order;
    public $total_amount;
    public $tax_rate;
    public $discounts;
    public $fees;
    public $transaction_id;
    public $payment_type;
    
    private $tablename;
    
    public function __construct() {
        $this->tablename = 'mp_advpayment_orders';
    }

    public function create() {
        $db = Db::getInstance();
        try {
            $id = $db->insert($this->tablename,
                    [
                        'id_cart' => $this->id_cart,
                        'id_order' => $this->id_order,
                        'total_amount' => $this->total_amount,
                        'tax_rate' => $this->tax_rate,
                        'discounts' => $this->discounts,
                        'fees' => $this->fees,
                        'transaction_id' => $this->transaction_id,
                        'payment_type' => $this->payment_type,
                    ]
                    );
        } catch (Exception $exc) {
            return $exc->getMessage();
        }
        return $id;
    }

    public function delete() {
        $db = Db::getInstance();
        return $db->delete(
                $this->tablename, 
                'id_cart = ' . $this->id_cart . ' and id_order = ' . $this->id_order);
    }

    public function read($id_order) {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        
        $sql    ->select("*")
                ->from($this->tablename)
                ->where("id_order = " . $id_order);
        
        $result = $db->getRow($sql);
        $this->id_cart = $result['id_cart'];
        $this->id_order = $result['id_order'];
        $this->total_amount = $result['total_amount'];
        $this->tax_rate = $result['tax_rate'];
        $this->discounts = $result['discounts'];
        $this->fees = $result['fees'];
        $this->transaction_id = $result['transaction_id'];
        $this->payment_type = $result['payment_type'];
    }

    public function update() {
        $db = Db::getInstance();
        try {
            $id = $db->update($this->tablename,
                    [
                        'total_amount' => $this->total_amount,
                        'tax_rate' => $this->tax_rate,
                        'discounts' => $this->discounts,
                        'fees' => $this->fees,
                        'transaction_id' => $this->transaction_id,
                        'payment_type' => $this->payment_type,
                    ],
                    'id_cart = ' . $this->id_cart . ' and id_order = ' . $this->id_order
                    );
        } catch (Exception $exc) {
            return $exc->getMessage();
        }
        return $id;
    }

    public function save() {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        
        $sql    ->select('count(*)')
                ->from($this->tablename)
                ->where('id_cart = ' . $this->id_cart . ' and id_order = ' . $this->id_order);
        
        $result = $db->getValue($sql);
        if ($result==0) {
            $this->create();
        } else {
            $this->update();
        }
    }

}