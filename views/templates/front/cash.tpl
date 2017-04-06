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
    {l s='Cash payment' mod='mpadvpayment'}
{/capture}

<form action='{$link->getModuleLink('mpadvpayment', 'validation', [{$params}], true)|escape:'html'}' method='POST'>
    <input type='hidden' name='currency_payment'>
    <table class='table-bordered'>
        <tbody>
            <tr><td>nb_products: {$nb_products}</td></tr>
            <tr><td>currencies : {$currencies|print_r}</td></tr>
            <tr><td>total_amount: {$total_amount}</td></tr>
            <tr><td>path: {$path|escape:'html'}</td></tr>
        </tbody>
    </table>
    <br>
    <p class="cart_navigation clearfix" id="cart_navigation">
        <a
            class="button-exclusive btn btn-default"
            href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}">
            <i class="icon-chevron-left"></i>{l s='Other payment methods' mod='mymodpayment'}
        </a>
        <button
            class="button btn btn-default button-medium"
            type="submit">
            <span>{l s='I confirm my order' mod='mymodpayment'}<i class="icon-chevron-right right"></i></span>
        </button>
	</p>
</form>