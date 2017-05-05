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

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..'
        . DIRECTORY_SEPARATOR . '..'
        . DIRECTORY_SEPARATOR . 'classes'
        . DIRECTORY_SEPARATOR . 'autoload.php';

class MpAdvPaymentBankwireModuleFrontController extends ModuleFrontControllerCore
{
    public $ssl = true;
    private $_cart;
    private $mpPayment;
    
    public function initContent()
    {
        $this->_lang = Context::getContext()->language->id;
        $this->mpPayment = new ClassMpPayment();
        
        $id_cart = Context::getContext()->cart->id;
        $this->_cart = new CartCore($id_cart);
        //Cast into Cart to avoid exception
        $this->_cart = $this->cast($this->_cart, "Cart");
        
        if (!$this->checkCurrency()) {
            Tools::redirect('index.php?controller=order');
        }
        
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::initContent();
        
        //Check product list
        $cart_product_list = ClassMpPaymentCalc::getCartProductList($id_cart);
        //add thumb image to product list
        foreach ($cart_product_list as &$cart_product) {
            $id_product = $cart_product['id_product'];
            $product_attribute = isset($cart_product['id_product_attribute'])?'_'
                    . $cart_product['id_product_attribute']:'';
            $product = new ProductCore($id_product);
            $images = $product->getImages($this->_lang);
            if (is_array($images)) {
                $image_id = $images[0]['id_image'];
                $name = 'product_mini_'
                       .(int)$id_product
                       .$product_attribute
                       .'.jpg';
                $thumb = new ImageCore($image_id);
                $thumb_path = $thumb->getExistingImgPath();
                $path = _PS_PROD_IMG_DIR_ . $thumb_path . '.jpg';
                $thumb_src = ImageManager::thumbnail($path, $name, 45, 'jpg', false, true);
            } else {
                $thumb_src = '';
            }
            
            $cart_product['image_tag'] = $thumb_src;
        }
        
        //$this->_cart->getCarrierCost($this->_cart->id_carrier);
        
        //Assign to Smarty
        $this->context->smarty->assign(array(
            'nb_products'=> $this->_cart->nbProducts(),
            'cart' => $this->_cart,
            'cart_currency' => $this->_cart->id_currency,
            'currencies' => $this->module->getCurrency($this->_cart->id_currency),
            'total_amount' => $this->_cart->getOrderTotal(true),
            'path' => $this->module->getPathUri(),
            'summary' => $this->_cart->getSummaryDetails(),
            'params' => array(
                'payment_method' => 'bankwire',
                'payment_display' => $this->module->l('Bankwire payment')
            ),
            'excluded_products' => ClassMpPaymentCalc::getListProductsExclusion('cash'),
            'cart_product_list' => $cart_product_list,
            'fee' => $this->mpPayment->calculateFee(ClassMpPayment::BANKWIRE, $this->_cart),
            'arr_details' => $this->getBankwireDetails(),
        ));
        
        $this->setTemplate('bankwire.tpl');
    }
    
    public function checkCurrency()
    {
        $currency_order = new CurrencyCore($this->_cart->id_currency);
        $currencies_module = $this->module->getCurrency($this->_cart->id_currency);
        
        //Check if module accept currency
        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    public function cast($obj, $to_class)
    {
        if (class_exists($to_class)) {
            $obj_in = serialize($obj);
            $obj_out = 'O:' . Tools::strlen($to_class) . ':"' . $to_class . '":' 
                    . Tools::substr($obj_in, $obj_in[2] + 7);
            return unserialize($obj_out);
        } else {
            return false;
        }
    }
    
    public function getBankwireDetails()
    {
        $det = new stdClass();
        $det->owner = ConfigurationCore::get("MP_ADVPAYMENT_OWNER");
        $det->iban  = ConfigurationCore::get("MP_ADVPAYMENT_IBAN");
        $det->bank  = ConfigurationCore::get("MP_ADVPAYMENT_BANK");
        $det->addr  = ConfigurationCore::get("MP_ADVPAYMENT_ADDR");
        
        return $det;
    }
}
