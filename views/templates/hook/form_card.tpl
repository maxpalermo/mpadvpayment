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
    name="input_card_switch" 
    label="{l s='Activate Paypal Pro payments' mod='mpadvpayment'}" 
    yes="{l s='YES' mod='mpadvpayment'}" 
    no="{l s='NO' mod='mpadvpayment'}" 
    active="true"
    onSwitch="hidePaypalPanel">
</ps-switch>
<input type="hidden" id="input_paypal_switch_hidden" value="1">

<label class="control-label col-lg-3 ">{l s='Email business' mod='mpadvpayment'}</label>
<div class="input-group input fixed-width-lg">
    <input type="text" id="input_card_email" class="input fixed-width-lg number_align" onfocus='selectAll(this);' onblur='checkEmail(this);'>
    <span class="input-group-addon"><i class="icon-mail-reply"></i></span>
</div>

<div class="panel-footer" style="margin: 0 auto;">
    <button type="button" value="1" id="submit_paypal_save" name="submit_card_save" class="btn btn-default pull-right">
        <i class="process-icon-save"></i> 
        {l s='Save' mod='mpadvpayment'}
    </button>
</div>

<script type="text/javascript">
    $(document).ready(function(){        
        $("#submit_card_save").on("click", function(){
            savePaypalValues();
            $("#mp-dialog-box").html("{l s='Paypal Pro configuration saved.' mod='mpadvpayment'}").fadeIn().delay(5000).fadeOut();
        });
    });
</script>
