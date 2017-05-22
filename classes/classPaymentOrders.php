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

class ClassMpPaymentOrders extends CRUD
{
    public $id_cart;
    public $id_order;
    public $total_amount;
    public $tax_rate;
    public $discounts;
    public $fees;
    public $transaction_id;
    public $payment_type;
    
    private $tablename;
    
    public function __construct()
    {
        $this->tablename = 'mp_advpayment_orders';
    }

    public function create()
    {
        $db = Db::getInstance();
        try {
            $id = $db->insert($this->tablename,
                    array(
                        'id_cart' => $this->id_cart,
                        'id_order' => $this->id_order,
                        'total_amount' => $this->total_amount,
                        'tax_rate' => $this->tax_rate,
                        'discounts' => $this->discounts,
                        'fees' => $this->fees,
                        'transaction_id' => $this->transaction_id,
                        'payment_type' => $this->payment_type,
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
        return $db->delete(
                $this->tablename,
                'id_cart = ' . $this->id_cart . ' and id_order = ' . $this->id_order);
    }

    public function read($id_order)
    {
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

    public function update()
    {
        $db = Db::getInstance();
        try {
            $id = $db->update($this->tablename,
                    array(
                        'total_amount' => $this->total_amount,
                        'tax_rate' => $this->tax_rate,
                        'discounts' => $this->discounts,
                        'fees' => $this->fees,
                        'transaction_id' => $this->transaction_id,
                        'payment_type' => $this->payment_type,
                        ),
                    'id_cart = ' . $this->id_cart . ' and id_order = ' . $this->id_order
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
