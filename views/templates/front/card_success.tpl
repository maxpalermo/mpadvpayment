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

{capture name=path}
    {l s='Paypal Pro payment' mod='mpadvpayment'}
{/capture}

<div class="container">
    <div class='panel-advice'>
        <legend>{l s='Payment' mod='mpadvpayment'}</legend>

        <div class='panel-advice'>
            <span style='font-size: 1.2em;'>
                <i class='icon-2x icon-info-sign'></i> 
                {l s='Your transaction has benn successfully processed.' mod='modadvpayment'}
            </span>
            <br>
            <span style='font-size: 1.2em;'>
                <i class='icon-2x icon-arrow-right'></i> 
                {l s='Transaction id:' mod='mpadvpayment'} 
                <strong>{$transaction_id}</strong>
            </span>
            <br>
            <span style='font-size: 1.2em;'>
                <i class='icon-2x icon-paper-clip'></i> 
                {l s='Order id:' mod='mpadvpayment'} 
                <strong>{$order_id}</strong>
            </span>
            <br>
            <span style='font-size: 1.2em;'>
                <i class='icon-2x icon-paper-clip'></i> 
                {l s='Order reference:' mod='mpadvpayment'} 
                <strong>{$order_reference}</strong>
            </span>
            <br>
            <span style='font-size: 1.2em;'>
                <i class='icon-2x icon-dollar'></i> 
                {l s='Total paid:' mod='mpadvpayment'} 
                <strong>{Tools::displayPrice($total)}</strong>
            </span>
        </div>
            <br>
            <br>
    </div>
    <div class='panel-footer'>
        <p class="cart_navigation exclusive">
            <a class="button-exclusive btn btn-default" href="{$link->getPageLink('history')|escape:'html':'UTF-8'}">
                <i class="icon-chevron-left"></i>
                {l s='Go to order history page' mod='mpadvpayment'}
            </a>
        </p>
    </div>
</div>