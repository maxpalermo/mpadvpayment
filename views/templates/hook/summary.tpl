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

<table class="table">
    <tbody>
        <tr>
            <td rowspan='2' style='width: 256px; font-size: 1.2em; text-align: left;'><strong>{$payment_type}</strong></td>
            <td>{l s='TOTAL CART' mod='mpadvpayment'}</td>
            {if $fees<0}
                <td style='text-align: left;'>{l s='DISCOUNTS' mod='mpadvpayment'}</td>
            {else}
                <td style='text-align: left;'>{l s='FEES' mod='mpadvpayment'}</td>
            {/if}
            
            <td style='text-align: left;'>{l s='TOTAL TO PAY' mod='mpadvpayment'}</td>
        </tr>
        <tr>
            <td style='text-align: left;'>
                {displayPrice price=$total_cart}
            </td>
            <td style='text-align: left;'>
                {displayPrice price=$fees}
            </td>
            <td style='text-align: left;'>
                <strong>{displayPrice price=$total_pay}</strong>
            </td>
        </tr>
    </tbody>
</table>