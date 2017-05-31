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
class ClassPaymentFee {    
    private $id_fee;
    private $id_order;
    private $id_order_payment;
    private $total_paid;
    private $total_paid_tax_incl;
    private $total_paid_tax_excl;
    private $total_paid_real;
    private $fee;
    private $fee_tax_incl;
    private $fee_tax_excl;
    private $fee_tax_rate;
    private $total_document;
    private $total_document_tax_incl;
    private $total_document_tax_excl;
    private $transaction_id;
    private $payment_type;
    private $date_add;
    private $date_upd;
    private $tax_included;
    private $tablename;
    
    public function getId_fee() {
        return $this->id_fee;
    }

    public function getId_order() {
        return $this->id_order;
    }

    public function getTotal_paid() {
        return $this->total_paid;
    }

    public function getTotal_paid_tax_incl() {
        return $this->total_paid_tax_incl;
    }

    public function getTotal_paid_tax_excl() {
        return $this->total_paid_tax_excl;
    }

    public function getTotal_paid_real() {
        return $this->total_paid_real;
    }

    public function getFee() {
        return $this->fee;
    }

    public function getFee_tax_incl() {
        return $this->fee_tax_incl;
    }

    public function getFee_tax_excl() {
        return $this->fee_tax_excl;
    }

    public function getFee_tax_rate() {
        return $this->fee_tax_rate;
    }

    public function getTotal_document() {
        return $this->total_document;
    }

    public function getTotal_document_tax_incl() {
        return $this->total_document_tax_incl;
    }

    public function getTotal_document_tax_excl() {
        return $this->total_document_tax_excl;
    }

    public function getTransaction_id() {
        return $this->transaction_id;
    }

    public function getPayment_type() {
        return $this->payment_type;
    }

    public function getDate_add() {
        return $this->date_add;
    }

    public function getTax_included() {
        return $this->tax_included;
    }

    public function setId_fee($id_fee) {
        $this->id_fee = $id_fee;
    }

    public function setId_order($id_order) {
        $this->id_order = $id_order;
    }

    public function setTotal_paid($total_paid) {
        $this->total_paid = $total_paid;
    }

    public function setTotal_paid_tax_incl($total_paid_tax_incl) {
        $this->total_paid_tax_incl = $total_paid_tax_incl;
    }

    public function setTotal_paid_tax_excl($total_paid_tax_excl) {
        $this->total_paid_tax_excl = $total_paid_tax_excl;
    }

    public function setTotal_paid_real($total_paid_real) {
        $this->total_paid_real = $total_paid_real;
    }

    public function setFee($fee) {
        $this->fee = $fee;
    }

    public function setFee_tax_incl($fee_tax_incl) {
        $this->fee_tax_incl = $fee_tax_incl;
    }

    public function setFee_tax_excl($fee_tax_excl) {
        $this->fee_tax_excl = $fee_tax_excl;
    }

    public function setFee_tax_rate($fee_tax_rate) {
        $this->fee_tax_rate = $fee_tax_rate;
    }

    public function setTotal_document($total_document) {
        $this->total_document = $total_document;
    }

    public function setTotal_document_tax_incl($total_document_tax_incl) {
        $this->total_document_tax_incl = $total_document_tax_incl;
    }

    public function setTotal_document_tax_excl($total_document_tax_excl) {
        $this->total_document_tax_excl = $total_document_tax_excl;
    }

    public function setTransaction_id($transaction_id) {
        $this->transaction_id = $transaction_id;
    }

    public function setPayment_type($payment_type) {
        $this->payment_type = $payment_type;
    }

    public function setDate_add($date_add) {
        $this->date_add = $date_add;
    }

    public function setTax_included($tax_included) {
        $this->tax_included = $tax_included;
    }
    
    public function getIdOrderPayment()
    {
        $order = new OrderCore($this->id_order);
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        $sql->select('id_order_payment')
                ->from('order_payment')
                ->where('order_reference = \'' . pSQL($order->reference) . '\'');
        return $db->getValue($sql);
    }
    
    public function __construct() 
    {
        $this->tablename = 'mp_advpayment_fee';
        $this->id_fee = 0;
        $this->date_upd = '';
        $this->id_order_payment = 0;
    }
    
    public function create($id_order, $payment_type, $fee, $fee_tax_rate, $tax_included=true)
    {
        $this->id_order = $id_order;
        $this->payment_type = $payment_type;
        $this->fee_tax_rate = $fee_tax_rate;
        $this->tax_included = $tax_included;
        $this->fee = $fee;
        
        $order = new OrderCore($id_order);
        $this->total_paid = $order->total_paid;
        $this->total_paid_tax_incl = $order->total_paid_tax_incl;
        $this->total_paid_tax_excl = $order->total_paid_tax_excl;
        $this->total_paid_real = $order->total_paid_real;
        
        classMpLogger::add('Order: ');
        classMpLogger::add('total paid: ' . $this->total_paid);
        classMpLogger::add('total paid_tax_incl: ' . $this->total_paid_tax_incl);
        classMpLogger::add('total paid_tax_excl: ' . $this->total_paid_tax_excl);
        classMpLogger::add('total paid_real: ' . $this->total_paid_real);
        
        $this->calc();
    }
    
    private function calc()
    {
        if ($this->tax_included) {
            $this->fee_tax_incl = $this->fee;
            $this->fee_tax_excl = $this->extractTax($this->fee_tax_incl);
        } else {
            $this->fee_tax_excl = $this->fee;
            $this->fee_tax_incl = $this->insertTax($this->fee_tax_excl);
            $this->fee = $this->fee_tax_incl;
        }
        
        $this->total_document = $this->total_paid + $this->fee;
        $this->total_document_tax_incl = $this->total_document;
        $this->total_document_tax_excl = $this->total_paid_tax_excl + $this->fee_tax_excl;
        
        classMpLogger::add('Document: ');
        classMpLogger::add('total document: ' . $this->total_document);
        classMpLogger::add('total document_tax_incl: ' . $this->total_document_tax_incl);
        classMpLogger::add('total document_tax_excl: ' . $this->total_document_tax_excl);
    }
    
    public function insert()
    {
        if((int)$this->id_order==0) {
            classMpLogger::add('NO ORDER ID!');
            return false;
        }
        
        $this->date_add = date('Y-m-d');
        $db = Db::getInstance();
        
        try {
            $result = $db->insert(
                    $this->tablename,
                    array(
                        'id_order' => (int)$this->id_order,
                        'total_paid' => (float)$this->total_paid,
                        'total_paid_tax_incl' => (float)$this->total_paid_tax_incl,
                        'total_paid_tax_excl' => (float)$this->total_paid_tax_excl,
                        'total_paid_real' => (float)$this->total_paid_real,
                        'fee' => (float)$this->fee,
                        'fee_tax_incl' => (float)$this->fee_tax_incl,
                        'fee_tax_excl' => (float)$this->fee_tax_excl,
                        'fee_tax_rate' => (float)$this->fee_tax_rate,
                        'total_document' => (float)$this->total_document,
                        'total_document_tax_incl' => (float)$this->total_document_tax_incl,
                        'total_document_tax_excl' => (float)$this->total_document_tax_excl,
                        'transaction_id' => pSQL($this->transaction_id),
                        'payment_type' => pSQL($this->payment_type),
                        'date_add' => pSQL($this->date_add),
                    ),
                    false,
                    false,
                    Db::REPLACE
                    );
            if ($result) {
                classMpLogger::add('Order payment fee inserted with id ' . $db->Insert_ID());
            } else {
                classMpLogger::add('ERROR: ' . $db->getMsgError());
                return false;
            }
        } catch (Exception $exc) {
            classMpLogger::add('ERROR DURING INSERTION:' . $exc->getMessage());
        }
        
        return $result;
    }
    
    public function delete()
    {
        if ((int)$this->id_order==0) {
            return false;
        }
        
        $db = Db::getInstance();
        $result = $db->delete(
                $this->tablename,
                'id_order = ' . (int)$this->id_order
                );
        return $result;
    }
    
    public function load($id_order)
    {
        if((int)$id_order==0) {
            return false;
        }
        
        $this->id_order = $id_order;
        
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        $sql->select('*')
                ->from($this->tablename)
                ->where('id_order = ' . $id_order);
        $result = $db->getRow($sql);
        
        if ($result) {
           foreach($result as $key=>$value)
           {
               $this->$key = $value;
           }
        } else {
            return $result;
        }
        
        return true;
    }
    
    private function extractTax($value)
    {
        return number_format($value / ((100 + $this->fee_tax_rate)/100), 2);
    }
    
    private function insertTax($value)
    {
        return number_format(($value * (100 + $this->fee_tax_rate))/100, 2);
    }
    
    public function setTransactionId($transaction_id)
    {
        $this->transaction_id = $transaction_id;
        $db = Db::getInstance();
        $result = $db->update(
                $this->tablename,
                array('transaction_id' => pSQL($this->transaction_id)),
                'id_order = ' . (int)$this->id_order
        );
        return $result;
    }
}
