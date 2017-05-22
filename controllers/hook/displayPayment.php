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

class MpAdvPaymentDisplayPaymentController
{
    public $context;
    public $module;
    public $file;
    public $_path;
    public $smarty;
    
    /**
     *
     * @param ModuleCore $module
     * @param string $file
     * @param string $path
     */
    public function __construct($module, $file, $path)
    {
        $this->file = $file;
        $this->module = $module;
        $this->_path = $path;
        $this->context = Context::getContext();
    }
    
    public function setSmarty($smarty)
    {
        $this->smarty = $smarty;
    }
    
    public function run($params)
    {
        $this->context->controller->addCSS(_MPADVPAYMENT_CSS_URL_ . 'displayPayment.css', 'all');
        $this->smarty->assign('activeModules', $this->getActiveModules());
        return $this->module->display($this->file, 'displayPayment.tpl');
    }
    
    /**
     * Get the list of activated modules
     * @return array associative array of activated payment modules [payment_type=>is_active]
     */
    public function getActiveModules()
    {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        
        $sql    ->select("is_active")
                ->select("payment_type")
                ->from("mp_advpayment_configuration")
                ->orderBy("payment_type");
        $result = $db->executeS($sql);
        $output = array();
        foreach ($result as $record) {
            $output[$record['payment_type']] = $record['is_active'];
        }
        
        //Check if PaypalPro is active
        $paypal_pro = (bool)ConfigurationCore::get('MP_ADVPAYMENT_PAYPAL_PRO_API');
        if ($paypal_pro) {
            $output['paypal pro'] = 1;
            $output['paypal'] = 0;
        }
        
        //Check module restrictions
        $Payment = new classMpPaymentCalc;
        $cashExclusions = $Payment->getListProductsExclusion(classMpPayment::CASH);
        $bankExclusions = $Payment->getListProductsExclusion(classMpPayment::BANKWIRE);
        $paypalExclusions = $Payment->getListProductsExclusion(classMpPayment::PAYPAL);
        $cartProducts = Context::getContext()->cart->getProducts();
        
        //print_r($cartProducts);
        
        foreach ($cartProducts as $product) {
            if (in_array($product['id_product'], $cashExclusions)) {
                $output['cash']=false;
            }
            if (in_array($product['id_product'], $bankExclusions)) {
                $output['bankwire']=false;
            }
            if (in_array($product['id_product'], $paypalExclusions)) {
                $output['paypal']=false;
                $output['paypal pro']=false;
            }
        }
        
        return $output;
    }
}
