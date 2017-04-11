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
    {l s='Bankwire payment' mod='mpadvpayment'}
{/capture}

<pre>
    FEE
    {$fee|print_r}
</pre>
<form class='defaultForm form-horizontal' action='{$link->getModuleLink('mpadvpayment', 'validation', $params, true)|escape:'html'}' method='POST'>
    <div class="panel panel-default">
        <div class='panel-heading'>
            <i class="icon-dollar"></i>
            {l s='Payment method:' mod='mpadvpayment'} {l s='cash' mod='mpadvpayment'}
        </div>  
        <div class='form-wrapper'>
            <table class='table-bordered' id='table_summary' style='width: 100%;'>
                <thead>
                    <tr>
                        <th></th>
                        <th>{l s='Product' mod='mpadvpayment'}</th>
                        <th>{l s='Price' mod='mpadvpayment'}</th>
                        <th>{l s='Qty' mod='mpadvpayment'}</th>
                        <th>{l s='Total' mod='mpadvpayment'}</th>
                        <th>{l s='Tax' mod='mpadvpayment'}</th>
                    </tr>
                </thead>
                <tbody>
                    {assign var=total_products value=0}
                    {assign var=sign value=$currencies[0]['sign']}
                    {assign var=fee_cost value=$fee['total_fee_without_taxes']}
                    {assign var=discounts value=-$fee['total_discounts']}
                    {assign var=carrier_cost value=$cart->getCarrierCost($cart->id_carrier, false)}
                    
                    {foreach $cart_product_list as $cart_product}
                        {assign var=prod_total value=$cart_product['price']*$cart_product['cart_quantity']}
                        {assign var=total_products value=$total_products+$prod_total}
                        <tr>
                            <td>{$cart_product['image_tag']}</td>
                            <td>{$cart_product['name']}</td>
                            <td>{displayPrice price=$cart_product['price']}</td>
                            <td>{$cart_product['cart_quantity']}</td>
                            <td>{displayPrice price=$prod_total}</td>
                            <td>{$cart_product['rate']|string_format:"%.2f"} %</td>
                        </tr>
                    {/foreach}
                    
                    {assign var=taxable value=$fee_cost+$discounts+$carrier_cost+$total_products}
                    {assign var=taxes value=$cart->getOrderTotal()+$fee['total_fee_with_taxes']-$taxable}
                    
                    {if $total_products}
                    <tr>
                        <td colspan='4' style='text-align: right; padding-right: 5px; font-weight: bold;'>{l s='Total products' mod='mpadvpayment'}: {$cart->nbProducts()}</td>
                        <td style='text-align: right; padding-right: 5px;'>{displayPrice price=$total_products}</td>
                    </tr>
                    {/if}
                    {if $carrier_cost}
                    <tr>
                        <td colspan="4" style='text-align: right; padding-right: 5px; font-weight: bold;'>{l s='Shipping cost' mod='mpadvpayment'}</td>
                        <td style='text-align: right; padding-right: 5px;'>{displayPrice price=$carrier_cost}</td>
                    </tr>
                    {/if}
                    {if $fee_cost}
                    <tr>
                        <td colspan="4" style='text-align: right; padding-right: 5px; font-weight: bold;'>{l s='Fee cost' mod='mpadvpayment'}</td>
                        <td style='text-align: right; padding-right: 5px;'>{displayPrice price=$fee_cost}</td>
                    </tr>
                    {/if}
                    {if $discounts}
                    <tr>
                        <td colspan="4" style='text-align: right; padding-right: 5px; font-weight: bold;'>{l s='Discounts' mod='mpadvpayment'}</td>
                        <td style='text-align: right; padding-right: 5px;'>{displayPrice price=$discounts}</td>
                    </tr>
                    {/if}
                    <tr>
                        <td colspan="4" style='text-align: right; padding-right: 5px; font-weight: bold; font-size: 1.2em; background-color: #DFDCDC'>{l s='TOTAL TAXABLE' mod='mpadvpayment'}</td>
                        <td style='font-size: 1.2em; font-weight: bold; text-align: right; padding-right: 5px;'>{displayPrice price=$taxable}</td>
                    </tr>
                    <tr>
                        <td colspan="4" style='text-align: right; padding-right: 5px; font-weight: bold; font-size: 1.2em; background-color: #DFDCDC'>{l s='TOTAL TAXES' mod='mpadvpayment'}</td>
                        <td style='font-size: 1.2em; font-weight: bold; text-align: right; padding-right: 5px;'>{displayPrice price=$taxes}</td>
                    </tr>
                    <tr>
                        <td colspan="4" style='text-align: right; padding-right: 5px; font-weight: bold; font-size: 1.2em; background-color: #DFDCDC'>{l s='TOTAL CART' mod='mpadvpayment'}</td>
                        <td style='font-size: 1.2em; font-weight: bold; text-align: right; padding-right: 5px;'>{displayPrice price={$taxable+$taxes}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <br>
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
            <i class="icon-chevron-left"></i>{l s='Other payment methods' mod='mpadvpayment'}
        </a>
        <button
            class="button btn btn-default button-medium"
            type="submit">
            <span>{l s='I confirm my order' mod='mpadvpayment'}<i class="icon-chevron-right right"></i></span>
        </button>
	</p>
</form>
        
<pre>
    <h3>Products excluded: {$excluded_products|count}</h3>
    {$excluded_products|@print_r}
</pre>
<pre>
    <h3>Products list: {$cart_product_list|count}</h3>
    {$cart_product_list|@print_r}
</pre>
<pre>
    <h3>Cart</h3>
    {$cart|@print_r}
</pre>