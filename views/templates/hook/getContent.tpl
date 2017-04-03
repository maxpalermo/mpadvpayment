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

{foreach $css as $css_link}
    <style type="text/css">
        @import url("{$css_link}"); 
    </style>
{/foreach}
{foreach $js as $js_link}
    <script src="{$js_link}"></script>
{/foreach}
<form class='defaultForm form-horizontal' method='post' id="form_manage_products">
    <div class='panel' id='panel-config'>
        <div class='panel-heading'>
            <img src='../modules/mpadvpayment/views/img/config.png' alt='Config'>
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
                         ** GET PRODUCTS BY CATEGORY **
                         ****************************** -->
                    <div class="form-wrapper">
                        <div class="form-group" id="div_tree_categories">
                            <p class='panel-heading' style="margin-top: 20px;">
                                <img src='../modules/mpadvpayment/views/img/category.png' alt='Config'>
                                {l s='Product Categories' mod='mpadvpayment'}
                            </p>  
                            {$category_tree}
                            <br>
                            <div>
                                <div style="display: table;">
                                    <div style="display: table-cell; vertical-align: middle;">
                                        <label class="control-label label-br" >{l s='Category' mod='mpadvpayment'}</label>
                                        <input type="text" id="input_category" value="" class="width-xl" style="display: inline-block">
                                    </div>

                                    <div style="display: table-cell; vertical-align: middle; padding-left: 10px;">
                                        <input type="checkbox" id="input_check_category_by_id" value="0" style="display: inline-block">
                                        <label class="control-label label-br" style="display: inline-block">{l s='Search by id' mod='mpadvpayment'}</label>
                                        <br>
                                        <input type="checkbox" id="input_check_category_clear" value="0" style="display: inline-block">
                                        <label class="control-label label-br" style="display: inline-block">{l s='New research' mod='mpadvpayment'}</label>
                                    </div>

                                    <div style="display: table-cell; vertical-align: middle; padding-left: 10px;">
                                        <button type="button" value="1" id="submit_find_category" id="find_product" name="find_product" class="btn btn-default">
                                            <i class="icon-search"></i> 
                                            {l s='Find' mod='mpadvpayment'}
                                        </button> 
                                    </div>
                                </div>
                            </div>    
                            <br style="clear: both;">
                            <label class="control-label label-br">{l s='Product associated' mod='mpadvpayment'}</label>
                            <br>
                            <div style="float: left; margin-right: 10px;">
                                <select size="10" style='width: 300px;' id="input_list_products">

                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="tabs-2">
                    <!-- ***********************
                         ** PRODUCT DISCOUNTS **
                         *********************** -->
                    <div class="form-wrapper">
                        <div class="form-group">
                            <p class='panel-heading' style="margin-top: 20px;">
                                <img src='../modules/mpadvpayment/views/img/discount.png' alt='Config'>
                                {l s='Product discounts' mod='mpadvpayment'}
                            </p>
                            <div id="tabs-discounts">
                                <ul>
                                    <li><a href="#tabs-discounts-1"><i class='icon-table'></i> {l s='Manage Discounts' mod='mpadvpayment'}</a></li>
                                    <li><a href="#tabs-discounts-2"><i class='icon-plus-circle'></i> {l s='Add new discounts' mod='mpadvpayment'}</a></li>                  
                                </ul>
                                <div id="tabs-discounts-1">
                                    <div>
                                        <p id='total_discounts_rows'>{l s='Total rows:' mod='mpadvpayment'}</p>
                                        <table class='table' id='table_discounts' style='display: block; overflow-y: auto; height: 25em;'>
                                            <thead>
                                                <tr>
                                                    <th>id</th>
                                                    <th>{l s='id Product' mod='mpadvpayment'}</th>
                                                    <th>{l s='Product' mod='mpadvpayment'}</th>
                                                    <th>{l s='Discount' mod='mpadvpayment'}</th>
                                                    <th>{l s='Type' mod='mpadvpayment'}</th>
                                                    <th>{l s='From' mod='mpadvpayment'}</th>
                                                    <th>{l s='To' mod='mpadvpayment'}</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>

                                    <p class='panel-heading' style="margin-top: 20px;">
                                        <img src='../modules/mpadvpayment/views/img/table.png' alt='table'>
                                        {l s='Discount Filters' mod='mpadvpayment'}
                                    </p>  

                                    <div>
                                        <div style="float: left; margin-right: 20px;">
                                            <table class="table" id="table_discounts_values">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>{l s='Values' mod='mpadvpayment'}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>
                                        <div style="float: left; margin-right: 20px;">
                                            <table class="table" id="table_discounts_types">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>{l s='Types' mod='mpadvpayment'}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <br style='clear: both;'>
                                    
                                    <p class='panel-heading' style="margin-top: 20px;">
                                        <img src='../modules/mpadvpayment/views/img/tools.png' alt='table'>
                                        {l s='Manage options' mod='mpadvpayment'}
                                    </p>  
                                    
                                    <div>
                                        <button type="button" value="1" id="submit_check_all_discount" name="submit_check_all_discount" class="btn btn-default">
                                            <i class="icon-check"></i> 
                                            {l s='Check all' mod='mpadvpayment'}
                                        </button> 
                                        <button type="button" value="1" id="submit_uncheck_all_discount" name="submit_uncheck_all_discount" class="btn btn-default">
                                            <i class="icon-check-empty"></i> 
                                            {l s='Uncheck all' mod='mpadvpayment'}
                                        </button> 
                                        <button type="button" value="1" id="submit_delete_discounts" name="submit_delete_discounts" class="btn btn-default">
                                            <i class="icon-trash"></i> 
                                            {l s='Delete selected' mod='mpadvpayment'}
                                        </button> 
                                    </div>
                                </div>
                                <div id="tabs-discounts-2">
                                    <!-- ************************
                                         *** ADD DISCOUNT TAB ***
                                         ************************
                                    -->
                                    <div>
                                        <label class="control-label label-br">{l s='Price' mod='mpadvpayment'}</label>
                                        <input type="text" id="input_discount_price" value="" style='width: 200px; text-align: right;'>
                                        <br>
                                        <label class="control-label label-br">{l s='Minimum quantity' mod='mpadvpayment'}</label>
                                        <input type="text" id="input_discount_quantity" value="" style='width: 200px; text-align: right;'>
                                        <br>
                                        <label class="control-label label-br">{l s='Reduction' mod='mpadvpayment'}</label>
                                        <input type="text" id="input_discount_reduction" value="" style='width: 200px; text-align: right;'>
                                        <br>
                                        <label class="control-label label-br">{l s='Tax' mod='mpadvpayment'}</label>
                                        <select id="input_discount_tax" style='width: auto;'>
                                            <option value='1'>{l s='Included' mod='mpadvpayment'}</option>
                                            <option value='0'>{l s='Excluded' mod='mpadvpayment'}</option>
                                        </select>
                                        <br>
                                        <label class="control-label label-br">{l s='Reduction type' mod='mpadvpayment'}</label>
                                        <select id="input_discount_reduction_type" style='width: auto;'>
                                            <option value='percentage'>{l s='Percentage' mod='mpadvpayment'}</option>
                                            <option value='amount'>{l s='Amount' mod='mpadvpayment'}</option>
                                        </select>
                                        <br>
                                        <label class="control-label label-br">{l s='From date' mod='mpadvpayment'}</label>
                                        <input type="text" readonly='readonly' id="input_discount_from" value="" style='width: 200px; text-align: center;'>
                                        <br>
                                        <label class="control-label label-br">{l s='To date' mod='mpadvpayment'}</label>
                                        <input type="text" readonly='readonly' id="input_discount_to" value="" style='width: 200px; text-align: center;'>
                                        <br>
                                    </div>
                                    <!-- **************************
                                         *** TABLE LIST PRODUCT ***
                                        ***************************
                                    -->
                                    <p class='panel-heading' style="margin-top: 20px;">
                                        <img src='../modules/mpadvpayment/views/img/table.png' alt='table'>
                                        {l s='Discount options' mod='mpadvpayment'}
                                    </p>            
                                                
                                    <div style="float: left; margin-right: 20px;">
                                        <table class="table" id="table_discounts_products" style='display: block; overflow-y: auto; height: 10em;'>
                                            <thead>
                                                <tr>
                                                    <th style='text-align: right;'><input type='checkbox' id='input_checkbox_discount_products'></th>
                                                    <th>{l s='Types' mod='mpadvpayment'}</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div> 
                                    <br style='clear: both;'>
                                                
                                    <p class='panel-heading' style="margin-top: 20px;">
                                        <img src='../modules/mpadvpayment/views/img/tools.png' alt='table'>
                                        {l s='Discount options' mod='mpadvpayment'}
                                    </p>  
                                    
                                    <div>
                                        <button type="button" value="1" id="submit_add_discount" name="submit_addl_discount" class="btn btn-default">
                                            <i class="icon-plus-sign"></i> 
                                            {l s='Add discount' mod='mpadvpayment'}
                                        </button> 
                                    </div>    
                                </div>
                            </div>        
                        </div>
                                    
                    </div>
                </div>
                <div id="tabs-3">
                    <!-- *********************************
                         ** ATTRIBUTE COMBINATION ITEMS **
                         ********************************* -->
                    <div class="form-wrapper">
                        <div class="form-group">
                            <div id="tabs-combinations">
                                <ul>
                                    <li><a href="#tabs-combinations-1"><i class='icon-table'></i> {l s='Manage Combinations' mod='mpadvpayment'}</a></li>
                                    <li><a href="#tabs-combinations-2"><i class='icon-plus-circle'></i> {l s='Add new combinations' mod='mpadvpayment'}</a></li>                  
                                </ul>
                                <div id="tabs-combinations-1">
                                    <!-- ***************************
                                         *** MANAGE COMBINATIONS ***
                                         ***************************
                                    -->
                                    <div>
                                        <p class='panel-heading' style="margin-top: 20px;">
                                            <img src='../modules/mpadvpayment/views/img/attribute.png' alt='Config'>
                                            {l s='Product combinations' mod='mpadvpayment'}
                                        </p>
                                        <br>
                                        <p id='total_combinations_rows'>{l s='Total rows:' mod='mpadvpayment'}</p>
                                        <table class='table' id='table_combinations' style='display: block; overflow-y: auto; height: 25em;'>
                                            <thead>
                                                <tr>
                                                    <th>id</th>
                                                    <th>{l s='Attribute' mod='mpadvpayment'}</th>
                                                    <th>{l s='id Product' mod='mpadvpayment'}</th>
                                                    <th>{l s='Product' mod='mpadvpayment'}</th>
                                                    <th>{l s='Reference' mod='mpadvpayment'}</th>
                                                    <th>{l s='EAN13' mod='mpadvpayment'}</th>
                                                    <th>{l s='Price' mod='mpadvpayment'}</th>
                                                    <th>{l s='Quantity' mod='mpadvpayment'}</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                    <div>
                                        <div style="float: left; margin-right: 10px; border-right: 1px solid #eeeeee;">
                                            <p class='panel-heading' style="margin-top: 20px;">
                                                <img src='../modules/mpadvpayment/views/img/table.png' alt='table'>
                                                {l s='Product List' mod='mpadvpayment'}
                                            </p> 

                                            <div>
                                                <div style="float: left; margin-right: 20px;">
                                                    <table class="table" id="table_combinations_products" style='display: block; overflow-y: auto; height: 10em;'>
                                                        <thead>
                                                            <tr>
                                                                <th></th>
                                                                <th>{l s='Products' mod='mpadvpayment'}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="float: left; padding-left: 10px;">
                                            <p class='panel-heading' style="margin-top: 20px;">
                                                <img src='../modules/mpadvpayment/views/img/tools.png' alt='table'>
                                                {l s='Manage options' mod='mpadvpayment'}
                                            </p>  

                                            <div>
                                                <button type="button" value="1" id="submit_check_all_combination" class="btn btn-default">
                                                    <i class="icon-check"></i> 
                                                    {l s='Check all' mod='mpadvpayment'}
                                                </button> 
                                                <button type="button" value="1" id="submit_uncheck_all_combination" class="btn btn-default">
                                                    <i class="icon-check-empty"></i> 
                                                    {l s='Uncheck all' mod='mpadvpayment'}
                                                </button> 
                                                <button type="button" value="1" id="submit_delete_combination" class="btn btn-default">
                                                    <i class="icon-trash"></i> 
                                                    {l s='Delete selected' mod='mpadvpayment'}
                                                </button> 
                                            </div>
                                        </div>
                                    </div>
                                    <br style='clear: both;'>  
                                </div>
                                <br style='clear: both;'>    
                                <div id="tabs-combinations-2">
                                    <!-- ************************
                                         *** ADD COMBINATIONS ***
                                         ************************
                                    -->
                                    <div>
                                        <div style="display: inline-block; width: 32%; float: left; border-right: 1px solid #EEEEEE; padding-left: 10px;">
                                            <p class='panel-heading' style="margin-top: 20px;">
                                                <img src='../modules/mpadvpayment/views/img/attribute.png' alt='Config'>
                                                {l s='Attribute list' mod='mpadvpayment'}
                                                <select id="input_select_attribute" style="width: auto; display: inline-block; margin-left: 30px;">

                                                </select>
                                            <br>
                                            </p>
                                            <table class="table" id="table_list_attributes" style='display: block; overflow-y: auto; height: 20em; width: 98%;'>
                                                <thead>
                                                    <tr>
                                                        <th style='text-align: center;'><input type='checkbox' id='input_checkbox_list_attributes'></th>
                                                        <th style="display: none;"></th>
                                                        <th>{l s='Attribute' mod='mpadvpayment'}</th>
                                                        <th style="display: none;"></th>
                                                        <th>{l s='Value' mod='mpadvpayment'}</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                            <br>
                                            <div>
                                                <button type="button" value="1" id="submit_check_all_attribute_combination" name="submit_check_all_attribute_combination" class="btn btn-default">
                                                    <i class="icon-check"></i> 
                                                    {l s='Check all' mod='mpadvpayment'}
                                                </button> 
                                                <button type="button" value="1" id="submit_uncheck_all_attribute_combination" name="submit_uncheck_all_attribute_combination" class="btn btn-default">
                                                    <i class="icon-check-empty"></i> 
                                                    {l s='Uncheck all' mod='mpadvpayment'}
                                                </button> 
                                                <button type="button" value="1" id="submit_add_attribute_combination" name="submit_add_attribute_combination" class="btn btn-default">
                                                    <i class="icon-plus-circle"></i> 
                                                    {l s='Add selected' mod='mpadvpayment'}
                                                </button>
                                            </div>
                                        </div>
                                        <div style="display: inline-block; width: 32%; float: left; border-right: 1px solid #EEEEEE; padding-left: 10px;">
                                            <p class='panel-heading' style="margin-top: 20px;">
                                                <img src='../modules/mpadvpayment/views/img/attribute.png' alt='Config'>
                                                {l s='Attribute combinations' mod='mpadvpayment'}
                                            </p>
                                            <table class="table" id="table_list_attribute_combinations" style='display: block; overflow-y: auto; height: 20em; width: 98%;'>
                                                <thead>
                                                    <tr>
                                                        <th style='text-align: right;'><input type='checkbox' id='input_checkbox_list_attribute_combination'></th>
                                                        <th>{l s='Attribute' mod='mpadvpayment'}</th>
                                                        <th style="display: none;"></th>
                                                        <th>{l s='Value' mod='mpadvpayment'}</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                            
                                        </div>
                                        <div style="display: inline-block; width: 32%; float: left; padding-left: 10px;">            
                                            <p class='panel-heading' style="margin-top: 20px;">
                                                <img src='../modules/mpadvpayment/views/img/attribute.png' alt='Config'>
                                                {l s='List Products' mod='mpadvpayment'}
                                            </p>
                                            <table class="table" id="table_list_products_combinations" style='display: block; overflow-y: auto; height: 20em; width: 98%;'>
                                                <thead>
                                                    <tr>
                                                        <th style='text-align: right;'><input type='checkbox' id='input_checkbox_product_combinations'></th>
                                                        <th>{l s='Product' mod='mpadvpayment'}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                            <br>
                                            <div>
                                                <button type="button" value="1" id="submit_clear_attribute_combination" class="btn btn-default">
                                                    <i class="icon-remove-circle"></i> 
                                                    {l s='Clear combinations' mod='mpadvpayment'}
                                                </button>
                                                <button type="button" value="1" id="submit_create_combination" name="submit_create_combination" class="btn btn-default">
                                                    <i class="icon-code-fork"></i> 
                                                    {l s='Create combinations' mod='mpadvpayment'}
                                                </button>
                                            </div>
                                        </div>
                                        <br style="clear: both;">
                                    </div>
                                    <br style="clear: both;">
                                    <div>
                                        <!--
                                        ***********************
                                        ** COMBINATION TABLE **
                                        ***********************
                                        -->
                                        <p class='panel-heading' style="margin-top: 20px;">
                                            <img src='../modules/mpadvpayment/views/img/attribute.png' alt='Config'>
                                            {l s='list combinations' mod='mpadvpayment'}
                                        </p>
                                        <table class="table" id="table_list_combinations" style='display: block; overflow-y: auto; height: 20em; width: 98%;'>
                                            <thead>
                                                <tr>
                                                    <th>{l s='Attributes' mod='mpadvpayment'}</th>
                                                    <th>{l s='Product' mod='mpadvpayment'}</th>
                                                    <th>{l s='Reference' mod='mpadvpayment'}</th>
                                                    <th>{l s='Supplier reference' mod='mpadvpayment'}</th>
                                                    <th>{l s='Location' mod='mpadvpayment'}</th>
                                                    <th>{l s='EAN13' mod='mpadvpayment'}</th>
                                                    <th>{l s='UPC' mod='mpadvpayment'}</th>
                                                    <th>{l s='Wholesale price' mod='mpadvpayment'}</th>
                                                    <th>{l s='Price' mod='mpadvpayment'}</th>
                                                    <th>{l s='Ecotax' mod='mpadvpayment'}</th>
                                                    <th>{l s='Quantity' mod='mpadvpayment'}</th>
                                                    <th>{l s='Weight' mod='mpadvpayment'}</th>
                                                    <th>{l s='Unit price' mod='mpadvpayment'}</th>
                                                    <th>{l s='Default' mod='mpadvpayment'}</th>
                                                    <th>{l s='Minimum quantity' mod='mpadvpayment'}</th>
                                                    <th>{l s='Available from' mod='mpadvpayment'}</th>
                                                    <th>{l s='Tax included' mod='mpadvpayment'}</th>
                                                    <th>{l s='Actions' mod='mpadvpayment'}</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                        <br>
                                        <div class='panel-footer'>
                                            <input type='file' style='display: none;' id='input_pattern' accept='*.pat'>
                                            <button type="button" value="1" id="submit_load_table_pattern" class="btn btn-default pull-right">
                                                <i class="process-icon-upload"></i> 
                                                {l s='Load pattern' mod='mpadvpayment'}
                                            </button>
                                            <button type="button" value="1" id="submit_save_table_pattern" class="btn btn-default pull-right">
                                                <i class="process-icon-download"></i> 
                                                {l s='Save pattern' mod='mpadvpayment'}
                                            </button>
                                            <button type="button" value="1" id="submit_clear_table_list_combination" class="btn btn-default pull-right">
                                                <i class="process-icon-refresh"></i> 
                                                {l s='Clear combinations' mod='mpadvpayment'}
                                            </button>
                                            <button type="button" value="1" id="submit_save_table_list_combination" class="btn btn-default pull-right">
                                                <i class="process-icon-save"></i> 
                                                {l s='Save combinations' mod='mpadvpayment'}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                    
        <div class='panel-footer'>
            
        </div>
                                
    </div>
</form>
<div id='cover-wait-operations'></div>

<script type="text/javascript">
    $(document).ready(function(){
        $("#tabs").tabs();
        $("#tabs-discounts").tabs();
        $("#tabs-combinations").tabs();
    });
</script>