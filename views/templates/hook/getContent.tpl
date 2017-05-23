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

<script type="text/javascript">
    /*
     * CONFIG OBJECT
     */
    var Payment = function(){
        this.active = true;
        this.fee_type = 0;
        this.fee_amount = 0;
        this.fee_percent = 0;
        this.fee_min = 0;
        this.fee_max = 0;
        this.order_min = 0;
        this.order_max = 0;
        this.order_free = 0;
        this.tax_included = 0;
        this.tax_rate = 0;
        this.carriers = new Array();
        this.categories = new Array();
        this.manufacturers = new Array();
        this.suppliers = new Array();
        this.products = new Array();
        this.payment_type = '';
        this.id_order_state = 0;
        this.logo = '';
        this.data = null;
        
        this.load = function(){
            $.ajax({
                url: '{$path}/ajax/getPayment.php',
                type: 'POST',
                data:   { 'type' : this.payment_type },
                success: function(response)
                        {
                            console.log("Class loaded");
                            var result = JSON.parse(response);
                            console.log(result);
                        }
            });
        },
        this.save = function(){
            $.ajax({
                url: '{$path}/ajax/setPayment.php',
                type: 'POST',
                data:   { 
                            'class' : JSON.stringify(this)
                        },
                success: function(response)
                        {
                            console.log("Class saved");
                            console.log(response);
                        }
            });
        };
    };
    
</script>

<div id='cover-wait-operations'></div>

<form class='defaultForm form-horizontal' method='post' id="form_manage_products">
    <div class='panel' id="panel-config">
        <div class='panel-heading'>
            <i class="icon-cogs"></i>
            {l s='Configuration section' mod='mpadvpayment'}
        </div>
        
        <div class="panel-body">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#tab-cash" data-toggle="tab">
                        <i class="icon-dollar"></i>
                        {l s='Cash configuration' mod='mpadvpayment'}
                    </a>
                </li>
                <li>
                    <a href="#tab-bankwire" data-toggle="tab">
                        <i class="icon-shopping-cart"></i>
                        {l s='Bankwire configuration' mod='mpadvpayment'}
                    </a>
                </li>
                <li>
                    <a href="#tab-paypal" data-toggle="tab">
                        <i class="icon-credit-card"></i>
                        {l s='Paypal configuration' mod='mpadvpayment'}
                    </a>
                </li>
            </ul>
            <div class="tab-content panel collapse in">
                <div id="tab-cash" class="tab-pane active">
                    <!-- ******************************
                         ** CASH CONFIGURATION **
                         ****************************** -->
                    <div class="form-wrapper">
                        <div class="form-group" id="div_tree_categories">
                            <p class='panel-heading' style="margin-top: 20px;">
                                <img src='../modules/mpadvpayment/views/img/cash.png' alt='Config' style="width: 28px;">
                                {l s='Cash configuration' mod='mpadvpayment'}
                            </p>  
                            {$form_cash}
                            <br style="clear: both;">
                        </div>
                    </div>
                </div>
                <div id="tab-bankwire" class="tab-pane">
                    <!-- ****************************
                         ** BANKWIRE CONFIGURATION **
                         **************************** -->
                    <div class="form-wrapper">
                        <div class="form-group">
                            <p class='panel-heading' style="margin-top: 20px;">
                                <img src='../modules/mpadvpayment/views/img/bankwire.png' alt='Config' style="width: 28px;">
                                {l s='Bankwire configuration' mod='mpadvpayment'}
                            </p>
                            {$form_bankwire}
                            <br style="clear: both;">       
                        </div>
                    </div>
                    
                </div>
                <div id="tab-paypal" class="tab-pane">
                    <!-- **************************
                         ** PAYPAL CONFIGURATION **
                         ************************** -->
                    <div class="form-wrapper">
                        <div class="form-group">
                            <p class='panel-heading' style="margin-top: 20px;">
                                <img src='../modules/mpadvpayment/views/img/paypal.png' alt='Config' style="width: 28px;">
                                {l s='Paypal configuration' mod='mpadvpayment'}
                            </p>  
                            {$form_paypal}
                            <br style="clear: both;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
                            
        <div class='panel-footer'>
            
        </div>
    </div>
</form>
                            
<div class="mp-dialog mp-dialog-success" style="margin: 0 auto;" id="mp-dialog-box">
  This is an alert box.
</div>

<pre>
{$POSTVALUES|print_r}
</pre>                            
                            
<script type="text/javascript">
    $(window).bind("load",function()
    {
        $("#tabs").tabs();
        //$("#idTabMenu").idTabs();
        
        $("input[id^='input_'][type='input']").each(function()
        {
            $(this).on("blur",formatCurrency(this)).on("focus",selectAll(this));
        });
        
        $("select[id*='_select_']").each(function(){
            //console.log('formatting ' + this.id);
            var result_text = '';
            
            if(String(this.id).indexOf("type")) {
                result_text = "{l s='No match type:' mod='mpadvpayment'}";
            } else if(String(this.id).indexOf("tax")) {
                result_text = "{l s='No match tax:' mod='mpadvpayment'}";
            } else if(String(this.id).indexOf("carriers")) {
                result_text = "{l s='No match carrier:' mod='mpadvpayment'}";
            } else if(String(this.id).indexOf("categories")) {
                result_text = "{l s='No match category:' mod='mpadvpayment'}";
            } else if(String(this.id).indexOf("manufacturers")) {
                result_text = "{l s='No match manufacturer:' mod='mpadvpayment'}";
            } else if(String(this.id).indexOf("suppliers")) {
                result_text = "{l s='No match supplier:' mod='mpadvpayment'}";
            } else if(String(this.id).indexOf("products")) {
                result_text = "{l s='No match product:' mod='mpadvpayment'}";
            } else if(String(this.id).indexOf("order_states")) {
                result_text = "{l s='No match order_state:' mod='mpadvpayment'}";
            }

            if(String(this.id).indexOf("type")) {
                $(this).chosen({ no_result_text: result_text, width : "350px" }).on("change",function(){
            
                    var match_type  = '';

                    if(String(this.id).indexOf('cash')>-1) {
                        match_type = '#div_cash_';
                    } else if(String(this.id).indexOf('bankwire')>-1) {
                        match_type = '#div_bankwire_';
                    } else if(String(this.id).indexOf('paypal')>-1) {
                        match_type = '#div_paypal_';
                    }
                    
                    //console.log("change: " + this.id);
                    //console.log("match: " + match_type);
                    
                    addHiddenList(this);
                    if($(this).val()==0) { //Tax panel
                        $(match_type + "tax_panel").fadeOut();
                    } else {
                        $(match_type + "tax_panel").fadeIn();
                        if($(this).val()==1) {
                            $(match_type + 'fee_amount').fadeIn();
                            $(match_type + 'fee_percent').fadeOut();
                        } else if($(this).val()==2) {
                            $(match_type + 'fee_amount').fadeOut();
                            $(match_type + 'fee_percent').fadeIn();
                        } else if($(this).val()==3) {
                            $(match_type + 'fee_amount').fadeIn();
                            $(match_type + 'fee_percent').fadeIn();
                        } else if($(this).val()==4) {
                            $(match_type + 'fee_amount').fadeOut();
                            $(match_type + 'fee_percent').fadeOut();
                            $(match_type + 'fee_min').fadeOut();
                            $(match_type + 'fee_max').fadeOut();
                            $(match_type + 'discount').fadeIn();
                        }
                    }
                }).css({ "width" : "350px" });  
            } else {
                $(this).chosen({ no_result_text: result_text, width : "350px"}).on("change",addHiddenList(this));
            }
        }); // end each function
        
        setCashValues();
        setBankwireValues();
        setPaypalValues();
        
        $("#cover-wait-operations").fadeOut();
        
    }); // ed onload function
    
    function setSwitchBtn(item, value)
    {
        var name = "#" + item.name + "_val";
        $(name).attr('switch', value);
        
        if (value==1) {
            name = "#" + item.name + "_on";
        } else {
            name = "#" + item.name + "_off";
        }
        $(name).click();
    }
    
    function switch_btn(item, value)
    {
        console.log("SWITCH: " + item.name + " = " + value);
        var name = item.name;
        $("#" + name + "_val").attr("switch", value);
        getSwitchValue(item);
        
        if(item.name === 'input_cash_switch') {
            hidePanel(value, 'cash');
        } else if(item.name === 'input_bankwire_switch') {
            hidePanel(value, 'bankwire');
        } else if(item.name === 'input_paypal_switch') {
            hidePanel(value, 'paypal');
        }
    }
    
    function getSwitchValue(item)
    {
        var id = "#" + item.name + "_val";
        var value = $(id).attr('switch');
        console.log("switch value of " + id + ": " + value);
        return value;
    }
    
    function setCashValues()
    {
        console.log("setCashValues");
        
        {if isset({$cash_values->input_switch_on}) && (int)$cash_values->input_switch_on==1}
            $("#input_cash_switch_on").click();
        {else}
            $("#input_cash_switch_off").click();
        {/if}
        $('#input_cash_select_type').val({(int)$cash_values->fee_type}).trigger('chosen:updated').change();
        $("#input_cash_fee_amount").val(Number({(float)$cash_values->fee_amount}).toFixed(2));
        $("#input_cash_fee_percent").val(Number({(float)$cash_values->fee_percent}).toFixed(2));
        $("#input_cash_fee_min").val(Number({(float)$cash_values->fee_min}).toFixed(2));
        $("#input_cash_fee_max").val(Number({(float)$cash_values->fee_max}).toFixed(2));
        $("#input_cash_order_min").val(Number({(float)$cash_values->order_min}).toFixed(2));
        $("#input_cash_order_max").val(Number({(float)$cash_values->order_max}).toFixed(2));
        $("#input_cash_order_free").val(Number({(float)$cash_values->order_free}).toFixed(2));
        {if isset($cash_values->tax_included) && $cash_values->tax_included==1 } 
            $("#input_cash_switch_included_tax_on").click();
        {else}
            $("#input_cash_switch_included_tax_off").click();
        {/if}
        $("#input_cash_select_tax").val("{$cash_values->tax_rate}").trigger('chosen:updated').change();
        $("#input_cash_select_carriers").val([{$cash_values->carriers|implode:','}]).trigger('chosen:updated').change();
        $("#input_cash_select_categories").val([{$cash_values->categories|implode:','}]).trigger('chosen:updated').change();
        $("#input_cash_select_manufacturers").val([{$cash_values->manufacturers|implode:','}]).trigger('chosen:updated').change();
        $("#input_cash_select_suppliers").val([{$cash_values->suppliers|implode:','}]).trigger('chosen:updated').change();
        $("#input_cash_select_products").val([{$cash_values->products|implode:','}]).trigger('chosen:updated').change();
        $("#input_cash_select_order_state").val([{$cash_values->id_order_state}]).trigger('chosen:updated').change();
        
        return;
    }
    
    function saveCashValues()
    {
        console.log("saveValues");
        
        var cash_payment = new Payment();
        
        cash_payment.active = $("#input_cash_switch_hidden").val();
        cash_payment.fee_type = $("#input_cash_select_type_hidden").val();
        cash_payment.fee_amount = $("#input_cash_fee_amount").val();
        cash_payment.fee_percent = $("#input_cash_fee_percent").val();
        cash_payment.fee_min = $("#input_cash_fee_min").val();
        cash_payment.fee_max = $("#input_cash_fee_max").val();
        cash_payment.order_min = $("#input_cash_order_min").val();
        cash_payment.order_max = $("#input_cash_order_max").val();
        cash_payment.order_free = $("#input_cash_order_free").val();
        cash_payment.tax_included = $("#input_cash_switch_included_tax_hidden").val();
        cash_payment.tax_rate = $("#input_cash_select_tax_hidden").val();
        cash_payment.carriers = $("#input_cash_select_carriers_hidden").val();
        cash_payment.categories = $("#input_cash_select_categories_hidden").val();
        cash_payment.manufacturers = $("#input_cash_select_manufacturers_hidden").val();
        cash_payment.suppliers = $("#input_cash_select_suppliers_hidden").val();
        cash_payment.products = $("#input_cash_select_products_hidden").val();
        cash_payment.id_order_state = $("#input_cash_select_order_states_hidden").val();
        cash_payment.payment_type = 'cash';
        cash_payment.logo = '';
        cash_payment.data = null;
        
        cash_payment.save();
        
    }
    
    function setBankwireValues()
    {
        console.log("setBankwireValues");
        
        {if isset({$bankwire_values->input_switch_on}) && (int)$bankwire_values->input_switch_on==1}
            $("#input_bankwire_switch_on").click();
        {else}
            $("#input_bankwire_switch_off").click();
        {/if}
        $('#input_bankwire_select_type').val({(int)$bankwire_values->fee_type}).trigger('chosen:updated').change();
        $("#input_bankwire_discount").val(Number({(float)$bankwire_values->discount}).toFixed(2));
        $("#input_bankwire_fee_amount").val(Number({(float)$bankwire_values->fee_amount}).toFixed(2));
        $("#input_bankwire_fee_percent").val(Number({(float)$bankwire_values->fee_percent}).toFixed(2));
        $("#input_bankwire_fee_min").val(Number({(float)$bankwire_values->fee_min}).toFixed(2));
        $("#input_bankwire_fee_max").val(Number({(float)$bankwire_values->fee_max}).toFixed(2));
        $("#input_bankwire_order_min").val(Number({(float)$bankwire_values->order_min}).toFixed(2));
        $("#input_bankwire_order_max").val(Number({(float)$bankwire_values->order_max}).toFixed(2));
        $("#input_bankwire_order_free").val(Number({(float)$bankwire_values->order_free}).toFixed(2));
        {if isset($bankwire_values->tax_included) && $bankwire_values->tax_included==1 } 
            $("#input_bankwire_switch_included_tax_on").click();
        {else}
            $("#input_bankwire_switch_included_tax_off").click();
        {/if}
        $("#input_bankwire_select_tax").val("{$bankwire_values->tax_rate}").trigger('chosen:updated').change();
        $("#input_bankwire_select_carriers").val([{$bankwire_values->carriers|implode:','}]).trigger('chosen:updated').change();
        $("#input_bankwire_select_categories").val([{$bankwire_values->categories|implode:','}]).trigger('chosen:updated').change();
        $("#input_bankwire_select_manufacturers").val([{$bankwire_values->manufacturers|implode:','}]).trigger('chosen:updated').change();
        $("#input_bankwire_select_suppliers").val([{$bankwire_values->suppliers|implode:','}]).trigger('chosen:updated').change();
        $("#input_bankwire_select_products").val([{$bankwire_values->products|implode:','}]).trigger('chosen:updated').change();
        $("#input_bankwire_select_order_state").val([{$bankwire_values->id_order_state}]).trigger('chosen:updated').change();
        
        $.ajax({
            url : '../modules/mpadvpayment/ajax/loadBankInfo.php',
            success: function(response)
            {
                var obj = JSON.parse(response);
                $("#input_bankwire_owner").val(obj.owner);
                $("#input_bankwire_iban").val(obj.iban);
                $("#input_bankwire_bank").val(obj.bank);
                $("#input_bankwire_address").val(obj.addr);
            }
        });
        
        return true;
    }
    
    function saveBankwireValues()
    {
        console.log("saveValues");
        
        var bankwire_payment = new Payment();
        
        bankwire_payment.active = $("#input_bankwire_switch_hidden").val();
        bankwire_payment.fee_type = $("#input_bankwire_select_type_hidden").val();
        bankwire_payment.discount = $("#input_bankwire_discount").val();
        bankwire_payment.fee_amount = $("#input_bankwire_fee_amount").val();
        bankwire_payment.fee_percent = $("#input_bankwire_fee_percent").val();
        bankwire_payment.fee_min = $("#input_bankwire_fee_min").val();
        bankwire_payment.fee_max = $("#input_bankwire_fee_max").val();
        bankwire_payment.order_min = $("#input_bankwire_order_min").val();
        bankwire_payment.order_max = $("#input_bankwire_order_max").val();
        bankwire_payment.order_free = $("#input_bankwire_order_free").val();
        bankwire_payment.tax_included = $("#input_bankwire_switch_included_tax_hidden").val();
        bankwire_payment.tax_rate = $("#input_bankwire_select_tax_hidden").val();
        bankwire_payment.carriers = $("#input_bankwire_select_carriers_hidden").val();
        bankwire_payment.categories = $("#input_bankwire_select_categories_hidden").val();
        bankwire_payment.manufacturers = $("#input_bankwire_select_manufacturers_hidden").val();
        bankwire_payment.suppliers = $("#input_bankwire_select_suppliers_hidden").val();
        bankwire_payment.products = $("#input_bankwire_select_products_hidden").val();
        bankwire_payment.id_order_state = $("#input_bankwire_select_order_states_hidden").val();
        bankwire_payment.payment_type = 'bankwire';
        bankwire_payment.logo = '';
        bankwire_payment.data = null;
        
        bankwire_payment.save();
        
        $.ajax({
            url : '../modules/mpadvpayment/ajax/saveBankInfo.php',
            type: 'POST',
            data: 
                    {
                        owner: $("#input_bankwire_owner").val(),
                        iban : $("#input_bankwire_iban").val(),
                        bank : $("#input_bankwire_bank").val(),
                        addr : $("#input_bankwire_address").val()
                    }
        });
        
    }
    
    function setPaypalValues()
    {
        console.log("setPaypalValues");
        
        {if isset({$paypal_values->input_switch_on}) && (int)$paypal_values->input_switch_on==1}
            $("#input_paypal_switch_on").click();
        {else}
            $("#input_paypal_switch_off").click();
        {/if}
        $('#input_paypal_select_type').val({(int)$paypal_values->fee_type}).trigger('chosen:updated').change();
        //$("#input_paypal_discount").val(Number({(float)$paypal_values->discount}).toFixed(2));
        $("#input_paypal_fee_amount").val(Number({(float)$paypal_values->fee_amount}).toFixed(2));
        $("#input_paypal_fee_percent").val(Number({(float)$paypal_values->fee_percent}).toFixed(2));
        $("#input_paypal_fee_min").val(Number({(float)$paypal_values->fee_min}).toFixed(2));
        $("#input_paypal_fee_max").val(Number({(float)$paypal_values->fee_max}).toFixed(2));
        $("#input_paypal_order_min").val(Number({(float)$paypal_values->order_min}).toFixed(2));
        $("#input_paypal_order_max").val(Number({(float)$paypal_values->order_max}).toFixed(2));
        $("#input_paypal_order_free").val(Number({(float)$paypal_values->order_free}).toFixed(2));
        {if isset($paypal_values->tax_included) && $paypal_values->tax_included==1 } 
            $("#input_paypal_switch_included_tax_on").click();
        {else}
            $("#input_paypal_switch_included_tax_off").click();
        {/if}
        $("#input_paypal_select_tax").val("{$paypal_values->tax_rate}").trigger('chosen:updated').change();
        $("#input_paypal_select_carriers").val([{$paypal_values->carriers|implode:','}]).trigger('chosen:updated').change();
        $("#input_paypal_select_categories").val([{$paypal_values->categories|implode:','}]).trigger('chosen:updated').change();
        $("#input_paypal_select_manufacturers").val([{$paypal_values->manufacturers|implode:','}]).trigger('chosen:updated').change();
        $("#input_paypal_select_suppliers").val([{$paypal_values->suppliers|implode:','}]).trigger('chosen:updated').change();
        $("#input_paypal_select_products").val([{$paypal_values->products|implode:','}]).trigger('chosen:updated').change();
        $("#input_paypal_select_order_state").val([{$paypal_values->id_order_state}]).trigger('chosen:updated').change();
        
        $.ajax({
            url : '../modules/mpadvpayment/ajax/loadPaypalInfo.php',
            success: function(response)
            {
                //console.log("PAYPAL GET INFO");
                //console.log(response);
                var obj = JSON.parse(response);
                if(obj.test==1) {
                    $("#input_paypal_switch_test_on").click();
                } else {
                    $("#input_paypal_switch_test_off").click();
                }
                if(obj.paypal_pro==1) {
                    $("#input_paypal_switch_pro_on").click();
                } else {
                    $("#input_paypal_switch_pro_off").click();
                }
                
                $("#input_paypal_user_api").val(obj.user);
                $("#input_paypal_password_api").val(obj.password);
                $("#input_paypal_signature_api").val(obj.signature);
                $("#input_paypal_test_api").val(obj.test_id);
                $("#input_paypal_pro_email_api").val(obj.email);
            }
        });
        
        return true;
    }
    
    function savePaypalValues()
    {
        console.log("saveValues");
        
        var paypal_payment = new Payment();
        
        paypal_payment.active = $("#input_paypal_switch_hidden").val();
        paypal_payment.fee_type = $("#input_paypal_select_type_hidden").val();
        paypal_payment.discount = 0;
        paypal_payment.fee_amount = $("#input_paypal_fee_amount").val();
        paypal_payment.fee_percent = $("#input_paypal_fee_percent").val();
        paypal_payment.fee_min = $("#input_paypal_fee_min").val();
        paypal_payment.fee_max = $("#input_paypal_fee_max").val();
        paypal_payment.order_min = $("#input_paypal_order_min").val();
        paypal_payment.order_max = $("#input_paypal_order_max").val();
        paypal_payment.order_free = $("#input_paypal_order_free").val();
        paypal_payment.tax_included = $("#input_paypal_switch_included_tax_hidden").val();
        paypal_payment.tax_rate = $("#input_paypal_select_tax_hidden").val();
        paypal_payment.carriers = $("#input_paypal_select_carriers_hidden").val();
        paypal_payment.categories = $("#input_paypal_select_categories_hidden").val();
        paypal_payment.manufacturers = $("#input_paypal_select_manufacturers_hidden").val();
        paypal_payment.suppliers = $("#input_paypal_select_suppliers_hidden").val();
        paypal_payment.products = $("#input_paypal_select_products_hidden").val();
        paypal_payment.id_order_state = $("#input_paypal_select_order_states_hidden").val();
        paypal_payment.payment_type = 'paypal';
        paypal_payment.logo = '';
        paypal_payment.data = '';
        paypal_payment.save();
        
        var file = document.getElementById('files').files[0];
        
        if(file) {
            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function(e) {
                var result = e.target.result.split(",", 2)[1];
                var fileName = document.getElementById('files').files[0].name; 
                
                console.log(result);
                
                $.ajax({
                    type : 'POST',
                    url  : '../modules/mpadvpayment/ajax/savePaypalLogo.php',
                    data :
                            {
                                "filename" : fileName,
                                "image"    : result
                            },
                    success: function(response)
                            {
                                console.log(response);
                            }
                });
                
            };
        }
        
        $.ajax({
            url : '../modules/mpadvpayment/ajax/savePaypalInfo.php',
            type: 'POST',
            data: 
                    {
                        test        : $("#input_paypal_switch_test_hidden").val(),
                        user        : $("#input_paypal_user_api").val(),
                        password    : $("#input_paypal_password_api").val(),
                        signature   : $("#input_paypal_signature_api").val(),
                        app_test_id : $("#input_paypal_test_api").val(),
                        paypal_pro  : $("#input_paypal_switch_pro_hidden").val(),
                        email       : $("#input_paypal_pro_email_api").val()
                    }
        });
        
    }
    
    function formatCurrency(element)
    {
        //console.log("formatCurrency element: " + element.id);
        $(element).val(Number(element.value).toFixed(2));
        //console.log("value: " + element.value);
    }
    
    function selectAll(element)
    {
        //console.log("selectAll element: " + element.id);
        $(element).select();
    }
    
    function log(logger)
    {
        console.log(logger);
    }
    
    function addHiddenList(element)
    {
        var selected = $(element).val();
        var target   = String(element.id) + "_hidden";
        if(selected !== null) {
            $("#" + target).val(selected.toString());
        } else {
            $("#" + target).val("");
        }
    }
    
    function setCashIncludedTax(value)
    {
        $("#input_cash_switch_included_tax_hidden").val(value);
    }
    
    function setBankwireIncludedTax(value)
    {
        $("#input_bankwire_switch_included_tax_hidden").val(value);
    }
    
    function setPaypalIncludedTax(value)
    {
        $("#input_paypal_switch_included_tax_hidden").val(value);
    }
    
    function hideCashPanel(value)
    {
        hidePanel(value, 'cash');
    }
    
    function hideBankwirePanel(value)
    {
        hidePanel(value, 'bankwire');
    }
    
    function hidePaypalPanel(value)
    {
        hidePanel(value, 'paypal');
    }
    
    function setPaypalSwitchTest(value)
    {
        console.log("switch test: " + Number(value));
        $("#input_paypal_switch_test_hidden").val(Number(value));
    }
    
    function hidePanel(value, panel)
    {
        var div_panel    = '';
        var input_switch = '';
        
        if(panel=='cash') {
            div_panel = "#div_cash_panel";
            input_switch = '#input_cash_switch_';
        } else if(panel=='bankwire') {
            div_panel = "#div_bankwire_panel";
            input_switch = '#input_bankwire_switch_';
        } if(panel=='paypal') {
            div_panel = "#div_paypal_panel";
            input_switch = '#input_paypal_switch_';
        }
        
        if(Number(value)===0) {
            $(div_panel).fadeOut();
        } else {
            $(div_panel).fadeIn();
        }
        $(input_switch + "hidden").val(value);
    }
    
    function setSwitchPaypalPro(value)
    {
        $("#input_paypal_switch_pro_hidden").val(value);
    }
</script>