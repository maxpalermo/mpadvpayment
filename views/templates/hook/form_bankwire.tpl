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
{$switch_bankwire}
<input type="hidden" id="input_bankwire_switch_hidden" value="1">
<div id='div_bankwire_panel'>
        <label class="control-label col-lg-3 ">{l s='Fee type' mod='mpadvpayment'}</label>
    <select id="input_bankwire_select_type" data-placeholder="{l s='Choose a tax rate' mod='mpadvpayment'}" style="width:350px;" class="chosen-select">
        <option value='0'>{l s='None' mod='mpadvpayment'}</option>
        <option value='1'>{l s='Amount' mod='mpadvpayment'}</option>
        <option value='2'>{l s='Percent' mod='mpadvpayment'}</option>
        <option value='3'>{l s='Amount + Percent' mod='mpadvpayment'}</option>
        <option value='4'>{l s='Discount' mod='mpadvpayment'}</option>
    </select>
    <input type='hidden' id='input_bankwire_select_type_hidden' value=''>
    <br>
    <br>
    <div id='div_bankwire_tax_panel'>
        <div id="div_bankwire_discount">
            <label class="control-label col-lg-3 ">{l s='Discount' mod='mpadvpayment'}</label>
            <div class="input-group input fixed-width-lg">
                <input type="text" id="input_bankwire_discount" class="input fixed-width-lg number_align" onfocus='selectAll(this);' onblur='formatCurrency(this);'>
                <span class="input-group-addon">€</span>
            </div>
            <br>
        </div>
        <div id="div_bankwire_fee_amount">
            <label class="control-label col-lg-3 ">{l s='Fee amount' mod='mpadvpayment'}</label>
            <div class="input-group input fixed-width-lg">
                <input type="text" id="input_bankwire_fee_amount" class="input fixed-width-lg number_align" onfocus='selectAll(this);' onblur='formatCurrency(this);'>
                <span class="input-group-addon">€</span>
            </div>
            <br>
        </div>
        <div id="div_bankwire_fee_percent">
            <label class="control-label col-lg-3 ">{l s='Fee percent' mod='mpadvpayment'}</label>
            <div class="input-group input fixed-width-lg">
                <input type="text" id="input_bankwire_fee_percent" class="input fixed-width-lg number_align" onfocus='selectAll(this);' onblur='formatCurrency(this);'>
                <span class="input-group-addon">%</span>
            </div>
            <br>    
        </div>
        <div id="div_bankwire_fee_min">
            <label class="control-label col-lg-3 ">{l s='Fee min' mod='mpadvpayment'}</label>
            <div class="input-group input fixed-width-lg">
                <input type="text" id="input_bankwire_fee_min" class="input fixed-width-lg number_align" onfocus='selectAll(this);' onblur='formatCurrency(this);'>
                <span class="input-group-addon">€</span>
            </div>
            <br>
        </div>
        <div id="div_bankwire_fee_max">
            <label class="control-label col-lg-3 ">{l s='Fee max' mod='mpadvpayment'}</label>
            <div class="input-group input fixed-width-lg">
                <input type="text" id="input_bankwire_fee_max" class="input fixed-width-lg number_align" onfocus='selectAll(this);' onblur='formatCurrency(this);'>
                <span class="input-group-addon">€</span>
            </div>
            <br>
        </div>
        <label class="control-label col-lg-3 ">{l s='Order min' mod='mpadvpayment'}</label>
        <div class="input-group input fixed-width-lg">
            <input type="text" id="input_bankwire_order_min" class="input fixed-width-lg number_align" onfocus='selectAll(this);' onblur='formatCurrency(this);'>
            <span class="input-group-addon">€</span>
        </div>
        <br>
        <label class="control-label col-lg-3 ">{l s='Order max' mod='mpadvpayment'}</label>
        <div class="input-group input fixed-width-lg">
            <input type="text" id="input_bankwire_order_max" class="input fixed-width-lg number_align" onfocus='selectAll(this);' onblur='formatCurrency(this);'>
            <span class="input-group-addon">€</span>
        </div>
        <br>
        <label class="control-label col-lg-3 ">{l s='Order free' mod='mpadvpayment'}</label>
        <div class="input-group input fixed-width-lg">
            <input type="text" id="input_bankwire_order_free" class="input fixed-width-lg number_align" onfocus='selectAll(this);' onblur='formatCurrency(this);'>
            <span class="input-group-addon">€</span>
        </div>
        <br>
        {$switch_bankwire_included_tax}
        <input type="hidden" id="input_bankwire_switch_included_tax_hidden">
        
        <label class="control-label col-lg-3 ">{l s='Fee tax' mod='mpadvpayment'}</label>
        <select id="input_bankwire_select_tax" data-placeholder="{l s='Choose a tax rate' mod='mpadvpayment'}" style="width:350px;" class="chosen-select">
            {$tax_list}
        </select>
        <input type='hidden' id='input_bankwire_select_tax_hidden' value=''>
        <br>
        <br>
    </div>
        
    <label class="control-label col-lg-3 ">{l s='Select carriers' mod='mpadvpayment'}</label>
    <select id="input_bankwire_select_carriers" data-placeholder="{l s='Choose a carrier' mod='mpadvpayment'}" style="width:350px;" multiple class="chosen-select">
        {$carrier_list}
    </select>
    <input type='hidden' id='input_bankwire_select_carriers_hidden' value=''>
    <br>
    <br>
    <label class="control-label col-lg-3 ">{l s='Exclude categories' mod='mpadvpayment'}</label>
    <select id="input_bankwire_select_categories" data-placeholder="{l s='Choose a category' mod='mpadvpayment'}" style="width:350px;" multiple class="chosen-select">
        {$categories_list}
    </select>
    <input type='hidden' id='input_bankwire_select_categories_hidden' value=''>
    <br>
    <br>
    <label class="control-label col-lg-3 ">{l s='Exclude manufacturers' mod='mpadvpayment'}</label>
    <select id="input_bankwire_select_manufacturers" data-placeholder="{l s='Choose a manufacturer' mod='mpadvpayment'}" style="width:350px;" multiple class="chosen-select">
        {$manufacturers_list}
    </select>
    <input type='hidden' id='input_bankwire_select_manufacturers_hidden' value=''>
    <br>
    <br>
    <label class="control-label col-lg-3 ">{l s='Exclude suppliers' mod='mpadvpayment'}</label>
    <select id="input_bankwire_select_suppliers" data-placeholder="{l s='Choose a supplier' mod='mpadvpayment'}" style="width:350px;" multiple class="chosen-select">
        {$suppliers_list}
    </select>
    <input type='hidden' id='input_bankwire_select_suppliers_hidden' value=''>
    <br>
    <br>
    <label class="control-label col-lg-3 ">{l s='Exclude products' mod='mpadvpayment'}</label>
    <select id="input_bankwire_select_products" data-placeholder="{l s='Choose a product' mod='mpadvpayment'}" style="width:350px;" multiple class="chosen-select">
        {$products_list}
    </select>
    <input type='hidden' id='input_bankwire_select_products_hidden' value=''>
    <br>
    <br>
    <label class="control-label col-lg-3 ">{l s='Order state' mod='mpadvpayment'}</label>
    <select id="input_bankwire_select_order_states" data-placeholder="{l s='Choose an order state' mod='mpadvpayment'}" style="width:350px;" class="chosen-select">
        {$order_state_list}
    </select>
    <input type='hidden' id='input_bankwire_select_order_states_hidden' value=''>
    <br>
    <br>
    <br>
    <div class="panel-heading">
        <i class="icon-file-o"></i>
        {l s='Bankwire informations' mod='mpadvpayment'}
    </div>
    <label class="control-label">{l s='Owner' mod='mpadvpayment'}</label>
    <div class="input-group input" style="width: 80%;">
        <input type="text" id="input_bankwire_owner" class="input" onfocus='selectAll(this);'>
        <span class="input-group-addon"><i class="icon-user"></i></span>
    </div>
    <br>
    <label class="control-label">{l s='IBAN' mod='mpadvpayment'}</label>
    <div class="input-group input" style="width: 80%;">
        <input type="text" id="input_bankwire_iban" class="input" onfocus='selectAll(this);'>
        <span class="input-group-addon"><i class="icon-code"></i></span>
    </div>
    <br>
    <label class="control-label">{l s='Bank' mod='mpadvpayment'}</label>
    <div class="input-group input" style="width: 80%;">
        <input type="text" id="input_bankwire_bank" class="input" onfocus='selectAll(this);'>
        <span class="input-group-addon"><i class="icon-home"></i></span>
    </div>
    <br>
    <label class="control-label ">{l s='Address' mod='mpadvpayment'}</label>
    <div class="input-group input" style="width: 80%;">
        <input type="text" id="input_bankwire_address" class="input" onfocus='selectAll(this);'>
        <span class="input-group-addon"><i class="icon-file-text"></i></span>
    </div>
    <br>
</div>
<div class="panel-footer" style="margin: 0 auto;">
    <button type="button" value="1" id="submit_bankwire_save" name="submit_bankwire_save" class="btn btn-default pull-right">
        <i class="process-icon-save"></i> 
        {l s='Save' mod='mpadvpayment'}
    </button>
</div>


<script type="text/javascript">
    $(document).ready(function(){        
        $("#submit_bankwire_save").on("click", function(){
            saveBankwireValues();
            $("#mp-dialog-box").html("{l s='Bankwire configuration saved.' mod='mpadvpayment'}").fadeIn().delay(5000).fadeOut();
        });
    });
</script>


 

