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
    {l s='Credit Card payment' mod='mpadvpayment'}
{/capture}

<div class="container">
  <form class="form-horizontal" method="POST" role="form" action='{$PAYPAL_URL}' target='_blank'>
    <fieldset>
        <legend>{l s='Payment' mod='mpadvpayment'}</legend>
        <div class="form-group">
            <input type="hidden" name="cmd" value="_hosted-payment">
            <input type="hidden" name="paymentaction" value="sale">
            
            <input type="hidden" name="business" value="{$EMAIL_BUSINESS}">
            
            <input type="hidden" name="showShippingAddress" value="false">
            <input type="hidden" name="address_override" value="true">
            <input type="hidden" name="template" value="templateB" >
            
            <input type='hidden' name='USER' value='{$USER}'>
            <input type='hidden' name='PWD' value='{$PWD}'>
            <input type='hidden' name='SIGNATURE' value='{$SIGNATURE}'>
            
            <input type="hidden" name="first_name" value="Mario">
            <input type="hidden" name="last_name" value="Rossi">
            <input type="hidden" name="address1" value="Via Roma, 1">
            <input type="hidden" name="city" value="Roma">
            <input type="hidden" name="state" value="Roma">
            <input type="hidden" name="zip" value="00100">
            <input type="hidden" name="country" value="IT">
            
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
</div>
        
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

<script type='text/javascript'>
    $(document).ready(function(){
        $("#expiry-month").on("change", function(){ setExpiryDate(); });
        $("#expiry-year").on("change", function(){ setExpiryDate(); });
    });
    
    function setExpiryDate()
    {
        $("#EXPDATE").val($("#expiry-month").val() + "" + $("#expiry-year").val());
    }
</script>