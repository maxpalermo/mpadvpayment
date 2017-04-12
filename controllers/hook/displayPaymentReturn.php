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

class MpAdvPaymentDisplayPaymentReturnController
{
	public function __construct($module, $file, $path)
	{
		$this->file = $file;
		$this->module = $module;
		$this->context = Context::getContext();
		$this->_path = $path;
	}

	public function run($params)
	{
            return "<h1>PAYMENT RETURN</h1>";
            
            $reference = $params['objOrder']->id;
            if (isset($params['objOrder']->reference) && !empty($params['objOrder']->reference)) {
                    $reference = $params['objOrder']->reference;
            }
            $total_to_pay = Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false);

            $this->context->smarty->assign(array(
                    'reference' => $reference,
                    'total_to_pay' => $total_to_pay,
            ));

            return $this->module->display($this->file, 'displayPaymentReturn.tpl');
	}
}
