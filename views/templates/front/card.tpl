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
    <form class="form-horizontal" method="POST" role="form" action='{$PAYPAL_URL}' style="display:none;" id="form_iframe" target="hss_iframe">
        <fieldset>
            <legend>{l s='Payment' mod='mpadvpayment'}</legend>
            <div class="form-group">
                <input type="hidden" name="cmd" value="_hosted-payment">
                <input type="hidden" name="paymentaction" value="sale">
                <input type="hidden" name="business" value="{$EMAIL_BUSINESS}">
                <input type="hidden" name="template" value="templateD" >

                <input type="hidden" name="first_name" value="{$customer->shipping->first_name}">
                <input type="hidden" name="last_name" value="{$customer->shipping->last_name}">
                <input type="hidden" name="address1" value="{$customer->shipping->address1}">
                <input type="hidden" name="address1" value="{$customer->shipping->address2}">
                <input type="hidden" name="city" value="{$customer->shipping->city}">
                <input type="hidden" name="state" value="{$customer->shipping->state}">
                <input type="hidden" name="zip" value="{$customer->shipping->zip}">
                <input type="hidden" name="country" value="{$customer->shipping->country}">
                <input type="hidden" name="email" value="{$customer->shipping->email}">

                <input type="hidden" name="showBillingAddress" value="true">
                <input type="hidden" name="address_override" value="true">
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

                <input type="hidden" value="{$notifyURL}" name="notify_url">
                <input type="hidden" value="{$returnURL}" name="return">
                <input type="hidden" value="{$cancelURL}" name="cancel_return">

                <label class="col-sm-3 control-label" for="card-holder-name">{l s='Total Amount' mod='mpadvpayment'}</label>
                <div class="col-sm-9">
                    <input type="text" readonly='readonly' style='width: 200px;' class="form-control align-right" name="subtotal" value="{$AMT}">
                    <input type="hidden" value="0" name="shipping">
                    <input type="hidden" value="0" name="tax">
                    <input type="hidden" name="currency_code" value="EUR">
                </div>
            </div>
        </fieldset>
    </form>
    
    <form class='defaultForm form-horizontal' method='post' id="form_manage_products">
        <div class='panel' id='panel-config'>
            <div class='panel-heading'>
                <i class="icon-2x icon-credit-card"></i>
                {l s='Payment summary' mod='mpadvpayment'}
            </div>
                    
            <br>
            
            <div class='panel-body'>
                <label class="control-label">{l s='Total order' mod='mpadvpayment'}</label>
                <i class='icon-chevron-right'></i>
                <input
                    type="text" 
                    id="input_card_email"  
                    readonly='readonly' 
                    style='background-color: #fefefe; 
                            text-align: right; 
                            padding-right: 10px; 
                            border-radius: 5px; 
                            border-color: #aaaaaa;
                            border-width: 1px;
                            font-weight: bold;
                            font-size: 1.3em;'
                    value='{$total_order}'
                >
            </div>
        </div>
    </form>
    <br>
    <div class='panel-advice'>
        <iframe 
            name="hss_iframe" 
            width="570px" 
            height="540px" 
            style="display: block; margin: 10px auto; box-shadow: 2px 2px 6px #999999;">
        </iframe>
    </div>
    <br>
    <div class='panel-footer'>        
        <p class="cart_navigation clearfix" id="cart_navigation">
            <a
                class="button-exclusive btn btn-default"
                href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}">
                <i class="icon-chevron-left"></i>{l s='Other payment methods' mod='mpadvpayment'}
            </a>
        </p>
    </div>
</div>

<script type='text/javascript'>
    $(document).ready(function(){
        $("#form_iframe").submit();
    });
</script>