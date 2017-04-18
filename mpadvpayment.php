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

if (!defined('_PS_VERSION_')) {
    exit;
    }

require_once (dirname(__FILE__) . '/classes/classMpPayment.php');
    
class MpAdvPayment extends PaymentModuleCore
{
    private $payment;
    
    public function __construct()
    {
      $this->name = 'mpadvpayment';
      $this->tab = 'payments_gateways';
      $this->version = '1.0.0';
      $this->author = 'mpsoft';
      $this->need_instance = 0;
      $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
      $this->bootstrap = true;

      parent::__construct();

      $this->displayName = $this->l('MP Advanced payment module');
      $this->description = $this->l('This module include three payments method with advanced custom parameters');
      $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
      
      $this->payment = new classMpPayment();
    }
  
    public function install()
    {
        if (Shop::isFeatureActive())
        {
          Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install() ||
          !$this->registerHook('displayPayment') ||
          !$this->registerHook('displayPaymentReturn') ||
          !$this->installSql() ||
          !$this->installPdf()) {
          return false;
        }
        return true;
    }
    
    public function uninstall()
    {
        if (!parent::uninstall() || !$this->uninstallSql() || $this->uninstallPdf()) {
          return false;
        }
        return true;
    }
    
    public function hookDisplayPayment($params)
    {
        $this->smarty = Context::getContext()->smarty;
        $this->context->controller->addCSS($this->_path . 'views/css/displayPayment.css');
        /** @var CartCore $cart */
        //$cart = new CartCore();
        $cart = Context::getContext()->cart;
        $cash_fee       = $this->payment->calculateFee(classMpPayment::CASH, $cart);
        $bankwire_fee   = $this->payment->calculateFee(classMpPayment::BANKWIRE, $cart);
        $paypal_fee   = $this->payment->calculateFee(classMpPayment::PAYPAL, $cart);
        
        $this->smarty->assign([
            'total_cart' => $cash_fee['total_cart'],
            'fees' => $cash_fee['total_fee_with_taxes'],
            'total_pay' => $cash_fee['total_cart']+$cash_fee['total_fee_with_taxes'],
            'payment_type' => $this->l('Cash'),
        ]);
        $this->smarty->assign('cash_summary', $this->smarty->fetch($this->local_path . 'views/templates/hook/summary.tpl'));
        
        $this->smarty->assign([
            'total_cart' => $bankwire_fee['total_cart'],
            'fees' => $bankwire_fee['total_fee_with_taxes'],
            'total_pay' => $bankwire_fee['total_cart']+$bankwire_fee['total_fee_with_taxes'],
            'payment_type' => $this->l('Bankwire'),
        ]);
        $this->smarty->assign('bankwire_summary', $this->smarty->fetch($this->local_path . 'views/templates/hook/summary.tpl'));
        
        $link = new LinkCore();
        $returnUrl = $link->getModuleLink('mpadvpayment', 'paypal', ['action' => 'GetExpressCheckoutDetails']);
        $cancelUrl = $link->getModuleLink('mpadvpayment', 'paypalerror');
        $controllerUrl = $link->getModuleLink('mpadvpayment', 'paypal', ['action'=>'SetExpressCheckout']);
        $this->smarty->assign([
            'total_cart' => $paypal_fee['total_cart'],
            'fees' => $paypal_fee['total_fee_with_taxes'],
            'total_pay' => $paypal_fee['total_cart']+$paypal_fee['total_fee_with_taxes'],
            'payment_type' => $this->l('Paypal'),
            'action' => 'SetExpressCheckout',
            'returnURL' => $returnUrl,
            'cancelURL' => $cancelUrl,
            'controllerURL' => $controllerUrl,
        ]);
        $this->smarty->assign('paypal_summary', $this->smarty->fetch($this->local_path . 'views/templates/hook/summary.tpl'));
        
        /*
        $this->smarty->assign(
                "cash_summary", 
                $this->l("Cart:") . ' ' . Tools::displayPrice($cash_fee['total_cart']) . ' + ' .
                $this->l("Fee:") . ' ' . Tools::displayPrice($cash_fee['total_fee_with_taxes']) . ' = ' .
                $this->l("Total:") . ' ' . Tools::displayPrice($cash_fee['total_cart']+$cash_fee['total_fee_with_taxes']));
        
        $this->smarty->assign(
                "bankwire_summary", 
                $this->l("Cart:") . ' ' . Tools::displayPrice($bankwire_fee['total_cart']) . ' + ' .
                $this->l("Fee:") . ' ' . Tools::displayPrice($bankwire_fee['total_fee_with_taxes']) . ' = ' .
                $this->l("Total:") . ' ' . Tools::displayPrice($bankwire_fee['total_cart']+$bankwire_fee['total_fee_with_taxes']));
        
        $this->smarty->assign(
                "paypal_summary", 
                $this->l("Cart:") . ' ' . Tools::displayPrice($paypal_fee['total_cart']) . ' + ' .
                $this->l("Fee:") . ' ' . Tools::displayPrice($paypal_fee['total_fee_with_taxes']) . ' = ' .
                $this->l("Total:") . ' ' . Tools::displayPrice($paypal_fee['total_cart']+$paypal_fee['total_fee_with_taxes']));
        */
        
        $controller = $this->getHookController('displayPayment');
        $controller->setSmarty($this->smarty);
        return $controller->run($params);
    }
    
    public function hookDisplayPaymentReturn($params)
    {
        if (!$this->active) {
            return;
        }
        
        $this->context->smarty->assign(['id_order' => $params['objOrder']->id]);
        
        $controller = $this->getHookController('displayPaymentReturn');
        return $controller->run($params);
    }
    
    public function getContent()
    {
        $this->smarty = Context::getContext()->smarty;
        $controller = $this->getHookController('getContent');
        $controller->setClass($this);
        $controller->setSmarty($this->smarty);
        $controller->setLocalPath($this->local_path);
        $controller->setFilePath(__FILE__);
        return $controller->run();
    }
    
    public function setMedia()
    {
        $this->context->controller->addJqueryUI('ui.tabs');
        $this->context->controller->addJS("https://cdnjs.cloudflare.com/ajax/libs/riot/3.4.0/riot+compiler.min.js");
        $this->context->controller->addCSS($this->_path . 'js/chosen/chosen.min.css');
        $this->context->controller->addJS($this->_path . 'js/chosen/chosen.jquery.min.js');
    }
    
    private function installSQL()
    {
        $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "sql" . DIRECTORY_SEPARATOR . "install.sql";
        $sql = explode(";",file_get_contents($filename));
        if(empty($sql)){return FALSE;}
        foreach($sql as $query)
        {
            if(!empty($query))
            {
                $query = str_replace("{_DB_PREFIX_}", _DB_PREFIX_, $query);
                $db = Db::getInstance();
                $result = $db->execute($query);
                if(!$result){return FALSE;}
            }
        }
        return TRUE;
    }
    
    private function uninstallSQL()
    {
        return true;
        
        $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "sql" . DIRECTORY_SEPARATOR . "uninstall.sql";
        $sql = explode(";",file_get_contents($filename));
        if(empty($sql)){return FALSE;}
        foreach($sql as $query)
        {
            if(!empty($query))
            {
                $query = str_replace("{_DB_PREFIX_}", _DB_PREFIX_, $query);
                $db = Db::getInstance();
                $result = $db->execute($query);
                if(!$result){return FALSE;}
            }
        }
        return TRUE;
    }
    
    public function installPdf()
    {
        $source = dirname(__FILE__) . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR;
        $dest_class   = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . "classes" . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR;
        $dest_pdf   = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR;
        
        rename($dest_class . 'HTMLTemplateInvoice.php' , $dest_class . 'HTMLTemplateInvoice.old.php');        
        copy($source . 'HTMLTemplateInvoice.php' , $dest_class . 'HTMLTemplateInvoice.php');
        
        rename($dest_pdf . 'invoice.tax-tab.tpl' , $dest_pdf . 'invoice.tax-tab.old.tpl');
        rename($dest_pdf . 'invoice.total-tab.tpl' , $dest_pdf . 'invoice.total-tab.old.tpl');
        copy($source . 'invoice.tax-tab.tpl' , $dest_pdf . 'invoice.tax-tab.tpl');
        copy($source . 'invoice.total-tab.tpl' , $dest_pdf . 'invoice.total-tab.tpl');
        
        return true;
    }
    
    public function uninstallPdf()
    {
        $dest_class   = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . "classes" . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR;
        $dest_pdf   = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR;
        
        unlink($dest_class . 'HTMLTemplateInvoice.php');
        rename($dest_class . 'HTMLTemplateInvoice.old.php' , $dest_class . 'HTMLTemplateInvoice.php');        
        
        unlink($dest_pdf . 'invoice.tax-tab.tpl');
        unlink($dest_pdf . 'invoice.total-tab.tpl');
        rename($dest_pdf . 'invoice.tax-tab.old.tpl' , $dest_pdf . 'invoice.tax-tab.tpl');
        rename($dest_pdf . 'invoice.total-tab.old.tpl' , $dest_pdf . 'invoice.total-tab.tpl');
        
        return true;
    }
    
    public function getHookController($hook_name)
    {
        // Include the controller file
        require_once(dirname(__FILE__).'/controllers/hook/'. $hook_name.'.php');

        // Build dynamically the controller name
        $controller_name = $this->name.$hook_name.'Controller';

        // Instantiate controller
        $controller = new $controller_name($this, __FILE__, $this->_path);

        // Return the controller
        return $controller;
    }
    
    
}