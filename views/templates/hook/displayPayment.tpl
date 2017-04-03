{*
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
*}
<style>
    p.payment_module a.mpadvpayment:after {
        color: #777777;
        content: "";
        display: block;
        font-family: "FontAwesome";
        font-size: 25px;
        height: 22px;
        margin-top: -11px;
        position: absolute;
        right: 15px;
        top: 50%;
        width: 14px;
    }
    
    p.payment_module a.mpadvpayment:hover
    {
        background-color: #D0D1D5;
    }
    
    p.payment_module a.mpadvpayment_cash {
        background: url("modules/mpadvpayment/views/img/cash.png") no-repeat scroll 15px 15px #fbfbfb;
    }
    
    p.payment_module a.mpadvpayment_bankwire
    {
        background: url("modules/mpadvpayment/views/img/bankwire.png") no-repeat scroll 15px 15px #fbfbfb;
    }
    p.payment_module a.mpadvpayment_paypal
    {
        background: url("modules/mpadvpayment/views/img/paypal.png") no-repeat scroll 15px 15px #fbfbfb;
    }
</style>
<div class="row">
    <div class="col-xs-12">
        <p class="payment_module">
            <a href="{$link->getModuleLink('mpadvpayment','cash')|escape:'html'}" class="mpadvpayment mpadvpayment_cash">
                {l s='Cash' mod='mpadvpayment'}
            </a>
        </p>
    </div>
    <div class="col-xs-12">
        <p class="payment_module">
            <a href="#" class="mpadvpayment mpadvpayment_bankwire">
                {l s='Bankwire' mod='mpadvpayment'}
            </a>
        </p>
    </div>
    <div class="col-xs-12">
        <p class="payment_module">
            <a href="#" class="mpadvpayment mpadvpayment_paypal">
                {l s='Paypal' mod='mpadvpayment'}
            </a>
        </p>
    </div>
</div>

