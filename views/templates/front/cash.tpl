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
    #table_summary th
    {
        text-align: center;
        background-color: #DFDCDC;
        font-weight: bold;
        text-shadow: 1px 1px 1px #cccccc;
    }
    #table_summary tbody td:nth-child(1)
    {
        text-align: center;
        width: 72px;
    }
    #table_summary tbody td:nth-child(2)
    {
        text-align: left;
        padding-left: 5px;
    }
    #table_summary tbody td:nth-child(3)
    {
        text-align: right;
        padding-right: 5px;
        width: 128px;
    }
    #table_summary tbody td:nth-child(4)
    {
        text-align: right;
        padding-right: 5px;
        width: 64px;
    }
    #table_summary tbody td:nth-child(5)
    {
        text-align: right;
        padding-right: 5px;
        width: 128px;
    }
    #table_summary tbody td:nth-child(6)
    {
        text-align: right;
        padding-right: 5px;
        width: 64px;
    }
</style>


{capture name=path}
    {l s='Cash payment' mod='mpadvpayment'}
{/capture}

<form class='defaultForm form-horizontal' action='{$link->getModuleLink('mpadvpayment', 'validateCash', [], true)|escape:'html'}' method='POST'>
    <div class="panel panel-default">
        <div class='panel-heading'>
            <i class="icon-dollar"></i>
            {l s='Payment method:' mod='mpadvpayment'} {l s='cash' mod='mpadvpayment'}
        </div>  
        
        <div class="panel-body panel-info">
            {l s='You have chosen to pay with' mod='mpadvpayment'}
            &nbsp;
            <strong>{l s='CASH' mod='mpadvpayment'}</strong>
        </div>
        
        <div class='panel'>
            <div class='panel-body panel-info'>
                {l s='TOTAL CART:' mod='mpadvpayment'}
                &nbsp;
                <strong>{displayPrice price=$cash_cart->getTotalCart()}</strong>
            </div>
            <div class='panel-body panel-info'>
                {if $cash_cart->payment->fee_type == classCart::FEE_TYPE_DISCOUNT}
                    {l s='TOTAL DISCOUNTS:' mod='mpadvpayment'}
                    &nbsp;
                    <strong>{displayPrice price=$cash_cart->getDiscount()}</strong>
                {else}
                    {l s='TOTAL FEES:' mod='mpadvpayment'}
                    &nbsp;
                    <strong>{displayPrice price=$cash_cart->getFee()}</strong>
                {/if}
            </div>
            <div class='panel-body panel-info'>
                {l s='TOTAL TO PAY:' mod='mpadvpayment'}
                &nbsp;
                <strong>{displayPrice price=$cash_cart->getTotalToPay()}</strong>
            </div>
        </div>
    </div>
    <br>
    <p class="cart_navigation clearfix" id="cart_navigation">
        <a
            class="button-exclusive btn btn-default"
            href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}">
            <i class="icon-chevron-left"></i>{l s='Other payment methods' mod='mpadvpayment'}
        </a>
        <button
            class="button btn btn-default button-medium"
            type="submit">
            <span>{l s='I confirm my order' mod='mpadvpayment'}<i class="icon-chevron-right right"></i></span>
        </button>
	</p>
</form>

{assign var=test value=true}
{if $test==true}
    <div class="panel panel-body panel-info">
        <pre>
            {$cash_cart|print_r}
        </pre>
    </div>
{/if}