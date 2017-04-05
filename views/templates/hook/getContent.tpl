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

<script type="text/javascript">
    $(document).ready(function(){
        $("#tabs").tabs();
        $("#cover-wait-operations").fadeOut();
    });
</script>