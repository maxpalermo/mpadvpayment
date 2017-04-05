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
<ps-switch 
    name="input_switch_activate_cash" 
    label="{l s='Activate Cash payments' mod='mpadvpayment'}" 
    yes="{l s='YES' mod='mpadvpayment'}" 
    no="{l s='NO' mod='mpadvpayment'}" 
    active="true"
    onSwitch='hideCashPanel'>
</ps-switch>
<input type="hidden" id="input_switch_hidden_activate_cash" value="1">
<div id='cash_panel'>
        <label class="control-label col-lg-3 ">{l s='Fee type' mod='mpadvpayment'}</label>
    <select id="input_select_cash_type" data-placeholder="{l s='Choose a tax rate' mod='mpadvpayment'}" style="width:350px;" class="chosen-select">
        <option value='0'>{l s='None' mod='mpadvpayment'}</option>
        <option value='1'>{l s='Amount' mod='mpadvpayment'}</option>
        <option value='2'>{l s='Percent' mod='mpadvpayment'}</option>
        <option value='3'>{l s='Amount + Percent' mod='mpadvpayment'}</option>
    </select>
    <input type='hidden' id='input_hidden_cash_type' value=''>
    <br>
    <br>
    <div id='cash_tax_panel'>
        <div id="div_fee_amount">
            <label class="control-label col-lg-3 ">{l s='Fee amount' mod='mpadvpayment'}</label>
            <div class="input-group input fixed-width-lg">
                <input type="text" id="input_fee_amount" class="input fixed-width-lg number_align">
                <span class="input-group-addon">€</span>
            </div>
            <br>
        </div>
        <div id="div_fee_percent">
            <label class="control-label col-lg-3 ">{l s='Fee percent' mod='mpadvpayment'}</label>
            <div class="input-group input fixed-width-lg">
                <input type="text" id="input_fee_percent" class="input fixed-width-lg number_align">
                <span class="input-group-addon">%</span>
            </div>
            <br>    
        </div>
        <label class="control-label col-lg-3 ">{l s='Fee min' mod='mpadvpayment'}</label>
        <div class="input-group input fixed-width-lg">
            <input type="text" id="input_fee_min" class="input fixed-width-lg number_align">
            <span class="input-group-addon">€</span>
        </div>
        <br>
        <label class="control-label col-lg-3 ">{l s='Fee max' mod='mpadvpayment'}</label>
        <div class="input-group input fixed-width-lg">
            <input type="text" id="input_fee_max" class="input fixed-width-lg number_align">
            <span class="input-group-addon">€</span>
        </div>
        <br>
        <label class="control-label col-lg-3 ">{l s='Order min' mod='mpadvpayment'}</label>
        <div class="input-group input fixed-width-lg">
            <input type="text" id="input_order_min" class="input fixed-width-lg number_align">
            <span class="input-group-addon">€</span>
        </div>
        <br>
        <label class="control-label col-lg-3 ">{l s='Order max' mod='mpadvpayment'}</label>
        <div class="input-group input fixed-width-lg">
            <input type="text" id="input_order_max" class="input fixed-width-lg number_align">
            <span class="input-group-addon">€</span>
        </div>
        <br>
        <label class="control-label col-lg-3 ">{l s='Order free' mod='mpadvpayment'}</label>
        <div class="input-group input fixed-width-lg">
            <input type="text" id="input_order_free" class="input fixed-width-lg number_align">
            <span class="input-group-addon">€</span>
        </div>
        <br>
        <ps-switch 
            name="input_switch_cash_included_tax" 
            label="{l s='Tax included' mod='mpadvpayment'}" 
            yes="{l s='YES' mod='mpadvpayment'}" 
            no="{l s='NO' mod='mpadvpayment'}" 
            active="false"
            onswitch='log'>
        </ps-switch>

        <label class="control-label col-lg-3 ">{l s='Fee tax' mod='mpadvpayment'}</label>
        <select id="input_select_cash_tax" data-placeholder="{l s='Choose a tax rate' mod='mpadvpayment'}" style="width:350px;" class="chosen-select">
            {$tax_list}
        </select>
        <input type='hidden' id='input_hidden_cash_tax' value=''>
        <br>
        <br>
    </div>
        
    <label class="control-label col-lg-3 ">{l s='Select carriers' mod='mpadvpayment'}</label>
    <select id="input_select_cash_carriers" data-placeholder="{l s='Choose a carrier' mod='mpadvpayment'}" style="width:350px;" multiple class="chosen-select">
        {$carrier_list}
    </select>
    <input type='hidden' id='input_hidden_cash_carriers' value=''>
    <br>
    <br>
    <label class="control-label col-lg-3 ">{l s='Exclude categories' mod='mpadvpayment'}</label>
    <select id="input_select_cash_categories" data-placeholder="{l s='Choose a category' mod='mpadvpayment'}" style="width:350px;" multiple class="chosen-select">
        {$categories_list}
    </select>
    <input type='hidden' id='input_hidden_cash_categories' value=''>
    <br>
    <br>
    <label class="control-label col-lg-3 ">{l s='Exclude manufacturers' mod='mpadvpayment'}</label>
    <select id="input_select_cash_manufacturers" data-placeholder="{l s='Choose a manufacturer' mod='mpadvpayment'}" style="width:350px;" multiple class="chosen-select">
        {$manufacturers_list}
    </select>
    <input type='hidden' id='input_hidden_cash_manufacturers' value=''>
    <br>
    <br>
    <label class="control-label col-lg-3 ">{l s='Exclude suppliers' mod='mpadvpayment'}</label>
    <select id="input_select_cash_suppliers" data-placeholder="{l s='Choose a supplier' mod='mpadvpayment'}" style="width:350px;" multiple class="chosen-select">
        {$suppliers_list}
    </select>
    <input type='hidden' id='input_hidden_cash_suppliers' value=''>
    <br>
    <br>
    <label class="control-label col-lg-3 ">{l s='Exclude products' mod='mpadvpayment'}</label>
    <select id="input_select_cash_products" data-placeholder="{l s='Choose a product' mod='mpadvpayment'}" style="width:350px;" multiple class="chosen-select">
        {$products_list}
    </select>
    <input type='hidden' id='input_hidden_cash_products' value=''>
    <br>
    <br>
</div>


<button type="button" value="1" id="submit_check_all_discount" name="submit_check_all_discount" class="btn btn-default pull-right">
    <i class="process-icon-save"></i> 
    {l s='Save' mod='mpadvpayment'}
</button>

<script type="text/javascript">
    $(document).ready(function(){
        $("input[name='input_select_cash_exclude_categories']").on("change",function(){
            log(this);
        });
        $("#input_button_add_category").on('click', function(){
            log($("input[name='input_select_cash_exclude_categories']").val());
        });
        
        $('#input_select_cash_type')
                .chosen({ no_results_text: "{l s='No match type:' mod='mpadvpayment'}" })
                .on("change",function()
        {
            addHiddenList(this);
            if($(this).val()==0) {
                $("#cash_tax_panel").fadeOut();
            } else {
                $("#cash_tax_panel").fadeIn();
            }
            
            if ($("#input_select_cash_type").val()==1) {
                $("#cash_tax_panel").fadeIn();
                $("#div_fee_amount").fadeIn();
                $("#div_fee_percent").fadeOut();
            } else if ($("#input_select_cash_type").val()==2) {
                $("#cash_tax_panel").fadeIn();
                $("#div_fee_amount").fadeOut();
                $("#div_fee_percent").fadeIn();
            } else if ($("#input_select_cash_type").val()==3) {
                $("#cash_tax_panel").fadeIn();
                $("#div_fee_amount").fadeIn();
                $("#div_fee_percent").fadeIn();
            }
        }); 
        
        $('#input_select_cash_tax')
                .chosen({ no_results_text: "{l s='No match tax:' mod='mpadvpayment'}" })
                .on("change",function()
        {
            addHiddenList(this);
        }); 
        
        $('#input_select_cash_carriers')
                .chosen({ no_results_text: "{l s='No match carrier:' mod='mpadvpayment'}" })
                .on("change",function()
        {
            addHiddenList(this);
        }); 
        
        $('#input_select_cash_categories')
                .chosen({ no_results_text: "{l s='No match category:' mod='mpadvpayment'}" })
                .on("change",function()
        {
            addHiddenList(this);
        }); 
        
        $('#input_select_cash_manufacturers')
                .chosen({ no_results_text: "{l s='No match manufacturer:' mod='mpadvpayment'}" })
                .on("change",function()
        {
            addHiddenList(this);
        }); 
        
        $('#input_select_cash_suppliers')
                .chosen({ no_results_text: "{l s='No match supplier:' mod='mpadvpayment'}" })
                .on("change",function()
        {
            addHiddenList(this);
        }); 
        
        $('#input_select_cash_products')
                .chosen({ no_results_text: "{l s='No match product:' mod='mpadvpayment'}" })
                .on("change",function()
        {
            addHiddenList(this);
        }); 
        
        //howto set values in select
        $('#input_select_cash_products').val(["300","2177","628"]).trigger('chosen:updated');
        
        if($("#input_select_cash_type").val()==0) {
            $("#cash_tax_panel").hide();
        }
        
        setValues();
        
    });
    
    function log(logger)
    {
        console.log(logger);
    }
    
    function addHiddenList(element)
    {
        var selected = $(element).val();
        var target   = String(element.id).replace("select","hidden");
        if(selected !== null) {
            $("#" + target).val(selected.toString());
        } else {
            $("#" + target).val("");
        }
    }
    
    function hideCashPanel(value)
    {
        if(value==0) {
            $("#cash_panel").fadeOut();
        } else {
            $("#cash_panel").fadeIn();
        }
        $("#input_switch_hidden_activate_cash").val(value);
    }
    
    function setValues()
    {
        console.log("setValues");
        
        if({$cash_values->input_switch_on}) {
            $("#input_switch_cash_on").click();
        } else {
            $("#input_switch_cash_off").click();
        }
        $('#input_select_cash_type').val({$cash_values->fee_type}).trigger('chosen:updated').change();
        $("#input_fee_amount").val(Number({$cash_values->fee_amount}).toFixed(2));
        $("#input_fee_percent").val(Number({$cash_values->fee_percent}).toFixed(2));
        $("#input_fee_min").val(Number({$cash_values->fee_min}).toFixed(2));
        $("#input_fee_max").val(Number({$cash_values->fee_max}).toFixed(2));
        $("#input_order_min").val(Number({$cash_values->order_min}).toFixed(2));
        $("#input_order_max").val(Number({$cash_values->order_max}).toFixed(2));
        $("#input_order_free").val(Number({$cash_values->order_free}).toFixed(2));
        if({$cash_values->tax_included}) {
            $("#input_switch_cash_included_tax_on").click();
        } else {
            $("#input_switch_cash_included_tax_off").click();
        }
        $("#input_select_cash_tax").val("{$cash_values->tax_rate}").trigger('chosen:updated').change();
        $("#input_select_cash_carriers").val([{$cash_values->carriers|implode:','}]).trigger('chosen:updated').change();
        $("#input_select_cash_categories").val([{$cash_values->categories|implode:','}]).trigger('chosen:updated').change();
        $("#input_select_cash_manufacturers").val([{$cash_values->manufacturers|implode:','}]).trigger('chosen:updated').change();
        $("#input_select_cash_suppliers").val([{$cash_values->suppliers|implode:','}]).trigger('chosen:updated').change();
        $("#input_select_cash_products").val([{$cash_values->products|implode:','}]).trigger('chosen:updated').change();
    }
</script>


 

