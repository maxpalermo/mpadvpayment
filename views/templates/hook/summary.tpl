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

{if $payment=='cash'}
    {assign var=payment value=$classSummary->cash->cart}
    {assign var=payment_type value={l s='Cash' mod='mpadvpayment'}}
    {assign var=icon value='icon-2x icon-dollar'}
{else if $payment=='bankwire'}
    {assign var=payment value=$classSummary->bankwire->cart}
    {assign var=payment_type value={l s='Bankwire' mod='mpadvpayment'}}
    {assign var=icon value='icon-2x icon-building'}
{else if $payment=='paypal'}
    {assign var=payment value=$classSummary->paypal->cart}
    {assign var=payment_type value={l s='Paypal or Credit Card' mod='mpadvpayment'}}
    {assign var=icon value='icon-2x icon-credit-card'}
{else}
    {assign var=payment value=''}
    <div class='alert alert-error'>
        <i class='icon-2x icon-warning'></i>
        <h3>{l s='ERROR: No payment method selected' l='mpadvpayment'}</h3>
    </div>
{/if}
    
{if isset($payment)}
    {assign var=total_cart value=$payment->getTotalCart()}
    {assign var=fee value=$payment->getFee()}
{/if}

{if !empty($payment)}
    <div class="panel-body">
        <div style='position:absolute; top: 10px; left: 10px;'>
            <i class='{$icon}'></i>
            &nbsp;
            <strong>{$payment_type}</strong>
        </div>
        <br>
        <div>
            <div style='display: inline-block; margin-right: 10px; padding-right: 10px; border-right: 1px solid #aaaaaa; font-weight: normal;'>
                {l s='TOTAL CART' mod='mpadvpayment'} : 
                {displayPrice price=$total_cart}
            </div>
            <div style='display: inline-block; margin-right: 10px; padding-right: 10px; border-right: 1px solid #aaaaaa; font-weight: normal;'>
                {if $fee<0}
                    <span style='text-align: left;'>{l s='DISCOUNTS' mod='mpadvpayment'}</span>
                    : {displayPrice price=($fee*-1)}
                {else}
                    <span style='text-align: left;'>{l s='FEES' mod='mpadvpayment'}</span>
                    : {displayPrice price=$fee}
                {/if}
                
            </div>
            <div style='display: inline-block; font-weight: normal;'>
                {l s='TOTAL TO PAY' mod='mpadvpayment'} : 
                <strong>{displayPrice price=$total_cart + $fee}</strong>
            </div>
        </div>
    </div>
{/if}

