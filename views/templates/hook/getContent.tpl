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


<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/riot/3.4.0/riot+compiler.min.js"></script>
<script type="text/javascript" src="{$base_uri}modules/mpadvpayment/views/js/chosen/chosen.jquery.js"></script>
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
                            console.log("Class loaded")
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
                            console.log("Class saved")
                            console.log(response);
                        }
            });
        }
    };
    
</script>
<style type="text/css">
    .number_align
    {
        text-align: right !important;
        padding-right: 10px !important;
    }
    #cover-wait-operations
    {
        background: url("../modules/mpmanageproducts/views/img/waiting.gif") no-repeat scroll center center #FFF;
        background-size: 128px;
        position: fixed;
        z-index: 99999999;
        height: 100%;
        width: 100%;
        top: 0;
        left: 0;
        display: block;
    }
    
    /* The alert message box */
    .mp-dialog
    {
        display: none;
        z-index: 999999;
        position: fixed;
        top: 10%;
        left: 30%;
        padding: 20px;
        width: 40%;
        border: 2px solid #555555;
        border-radius: 10px;
        box-shadow: 2px 2px 6px #cccccc;
    }
    .mp-dialog-alert {
        background-color: #FFCC00; /* Yellow */
        color: white;
        margin-bottom: 15px;
    }
    
    .mp-dialog-success {
        background-color: #090; /* Green */
        color: white;
        margin-bottom: 15px;
    }
    
    .mp-dialog-error {
        background-color: #f44336; /* Red */
        color: white;
        margin-bottom: 15px;
    }

    /* The close button */
    .mp-dialog .closebtn {
        margin-left: 15px;
        color: white;
        font-weight: bold;
        float: right;
        font-size: 22px;
        line-height: 20px;
        cursor: pointer;
        transition: 0.3s;
        position: absolute;
        top: 5px;
        left: calc(100% -10px);
    }

    /* When moving the mouse over the close button */
    .mp-dialog .closebtn:hover {
        color: black;
    }
    
</style>

<div id='cover-wait-operations'></div>

<form class='defaultForm form-horizontal' method='post' id="form_manage_products">
    <div class='panel' id='panel-config'>
        <div class='panel-heading'>
            <i class="icon-cogs"></i>
            {l s='Configuration section' mod='mpadvpayment'}
        </div>  
        <div class="form-wrapper">
            <div id="tabs">
                <ul>
                    <li><a href="#tabs-1"><i class="icon-dollar"></i> {l s='Cash configuration' mod='mpadvpayment'}</a></li>
                    <li><a href="#tabs-2"><i class="icon-building"></i> {l s='Bankwire configuration' mod='mpadvpayment'}</a></li>
                    <li><a href="#tabs-3"><i class="icon-credit-card"></i> {l s='Paypal configuration' mod='mpadvpayment'}</a></li>
                </ul>
                <div id="tabs-1">
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
                <div id="tabs-2">
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
                <div id="tabs-3">
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
                            
<script type="text/javascript">
    $(window).bind("load",function()
    {
        $("#tabs").tabs();
        
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
        cash_payment.fee_type = $("#input_cash_type_hidden").val();
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
        $("#input_bankwire_select_order_states").val([{$bankwire_values->id_order_state}]).trigger('chosen:updated').change();
        
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
        $("#input_paypal_select_order_states").val([{$paypal_values->id_order_state}]).trigger('chosen:updated').change();
        
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
                $("#input_paypal_user_api").val(obj.user);
                $("#input_paypal_password_api").val(obj.password);
                $("#input_paypal_signature_api").val(obj.signature);
                $("#input_paypal_test_api").val(obj.test_id);
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
                                "image"    : result,
                            },
                    success: function(response)
                            {
                                console.log(response);
                            }
                });
                
            };
        }
        paypal_payment.save();
        
        $.ajax({
            url : '../modules/mpadvpayment/ajax/savePaypalInfo.php',
            type: 'POST',
            data: 
                    {
                        test      : $("#input_paypal_switch_test_hidden").val(),
                        user      : $("#input_paypal_user_api").val(),
                        password  : $("#input_paypal_password_api").val(),
                        signature : $("#input_paypal_signature_api").val(),
                        test_id   : $("#input_paypal_test_api").val()
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
</script>