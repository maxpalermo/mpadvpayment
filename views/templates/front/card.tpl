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
{assign var=payment value=$classSummary->paypal->cart}
{assign var=customer value=$classSummary->paypal->customer}
{assign var=url value=$classSummary->paypal->URL}
{assign var=total_cart value=$payment->getTotalCart()}
{assign var=fee value=$payment->getFee()}

<div class="container">
    <form class="form-horizontal" method="POST" role="form" action='{$PAYPAL_URL}' style="display:none;" id="form_redirect">
        <fieldset>
            <legend>{l s='Payment' mod='mpadvpayment'}</legend>
            <div class="form-group">
                <input type="hidden" name="cmd" value="_hosted-payment">
                <input type="hidden" name="paymentaction" value="sale">
                <input type="hidden" name="custom" value="cart_id:{$cart_id}">
                <input type="hidden" name="localecode" value="it_IT">
                <input type="hidden" name="business" value="{$classSummary->paypal->email}">
                <input type="hidden" name="template" value="templateC" >
                <input type="hidden" name="subtotal" value="{$total_cart + $fee|number_format:2}">
                <input type="hidden" name="shipping" value="0">
                <input type="hidden" name="tax" value="0">
                <input type="hidden" name="currency_code" value="{$payment->payment->currency_suffix}">

                <input type="hidden" name="first_name" value="{$customer->shipping->first_name}">
                <input type="hidden" name="last_name" value="{$customer->shipping->last_name}">
                <input type="hidden" name="address1" value="{$customer->shipping->address1}">
                <input type="hidden" name="address1" value="{$customer->shipping->address2}">
                <input type="hidden" name="city" value="{$customer->shipping->city}">
                <input type="hidden" name="state" value="{$customer->shipping->state}">
                <input type="hidden" name="zip" value="{$customer->shipping->zip}">
                <input type="hidden" name="country" value="{$customer->shipping->country}">
                <input type="hidden" name="email" value="{$customer->shipping->email}">

                <input type="hidden" name="showBillingAddress" value="false">
                <input type="hidden" name="address_override" value="false">
                <input type="hidden" name="billing_first_name" value="{$customer->billing->first_name}">
                <input type="hidden" name="billing_last_name" value="{$customer->billing->last_name}">
                <input type="hidden" name="billing_address1" value="{$customer->billing->address1}">
                <input type="hidden" name="billing_address2" value="{$customer->billing->address2}">
                <input type="hidden" name="billing_city" value="{$customer->billing->city}">
                <input type="hidden" name="billing_state" value="{$customer->billing->state}">
                <input type="hidden" name="billing_zip" value="{$customer->billing->zip}">
                <input type="hidden" name="billing_country" value="{$customer->billing->country}">
                <input type="hidden" name="buyer_email" value="{$customer->billing->email}">
                <input type="hidden" name="night_phone_a" value="{$customer->billing->phone_prefix}">
                <input type="hidden" name="night_phone_b" value="{$customer->billing->phone_number}">

                <input type="hidden" name="notify_url" value="{$url->notify}">
                <input type="hidden" name="return" value="{$url->success}">
                <input type="hidden" name="cancel_return" value="{$url->cancel}">
            </div>
        </fieldset>
    </form>
    
    <form class='defaultForm form-horizontal' method='post' id="form_manage_products">
        <div class='panel' id='panel-config'>
            <div class='panel-heading'>
                <i class="icon-2x icon-credit-card"></i>
                {l s='Paypal' mod='mpadvpayment'}
            </div>
                    
            <br>
            
            <div class='panel-body'>
                <span style='
                      display: block; 
                      margin: 10px auto; 
                      font-size: 1.3em;
                      text-shadow: 1px 1px 2px #bbbbcc;
                      '>
                    {l s='Please wait while redirecting to payment page...' mod='mpadvpayment'}
                </span>
                <div>
                    <img src="{$base_dir}modules/mpadvpayment/views/img/waiting.gif" style="width: 128px; margin: 0 auto; display: block;">
                </div>
            </div>
        </div>
    </form>
    <br>
    <br>
</div>

<script type='text/javascript'>
    $(document).ready(function(){
        $("#form_redirect").submit();
    });
</script>

{assign var=test value=false}
{if $test}            
<div class="panel">
    <div class="panel-heading">
        <i class="icon-2x icon-date"></i>
        <span style="color: #0066CC; text-shadow: 1px 1px 1px #aaaaaa;">
            CLASS SUMMARY
        </span>
    </div>
    <br>
    <div class="panel-body">
        <pre>
        {$classSummary|@print_r}
        </pre>
    </div>
</div>
{/if}