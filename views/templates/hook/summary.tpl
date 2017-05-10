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
{else if $payment=='bankwire'}
    {assign var=payment value=$classSummary->bankwire->cart}
    {assign var=payment_type value={l s='Bankwire' mod='mpadvpayment'}}
{else if $payment=='paypal'}
    {assign var=payment value=$classSummary->paypal->cart}
    {assign var=payment_type value={l s='Paypal or Credit Card' mod='mpadvpayment'}}
{else}
    {assign var=payment value=''}
    <div class='alert alert-error'>
        <i class='icon-2x icon-warning'></i>
        <h3>{l s='ERROR: No payment method selected' l='mpadvpayment'}</h3>
    </div>
{/if}

{if !empty($payment)}
<table class="table">
    <tbody>
        <tr>
            <td rowspan='2' style='width: 256px; font-size: 1.2em; text-align: left;'><strong>{$payment_type}</strong></td>
            <td>{l s='TOTAL CART' mod='mpadvpayment'}</td>
            {if $payment->total_fee_with_taxes<0}
                <td style='text-align: left;'>{l s='DISCOUNTS' mod='mpadvpayment'}</td>
            {else}
                <td style='text-align: left;'>{l s='FEES' mod='mpadvpayment'}</td>
            {/if}
            
            <td style='text-align: left;'>{l s='TOTAL TO PAY' mod='mpadvpayment'}</td>
        </tr>
        <tr>
            <td style='text-align: right;'>
                {displayPrice price=$payment->total_cart}
            </td>
            <td style='text-align: right;'>
                {displayPrice price=$payment->total_fee_with_taxes}
            </td>
            <td style='text-align: right;'>
                <strong>{displayPrice price=$payment->total}</strong>
            </td>
        </tr>
    </tbody>
</table>
{/if}

