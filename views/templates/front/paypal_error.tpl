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
<style>
    #table_summary th
    {
        text-align: center;
        background-color: #DFDCDC;
        font-weight: bold;
        text-shadow: 1px 1px 1px #cccccc;
    }
    #table_summary tbody td:nth-child(1)
    {
        text-align: center;
        width: 72px;
    }
    #table_summary tbody td:nth-child(2)
    {
        text-align: left;
        padding-left: 5px;
    }
    #table_summary tbody td:nth-child(3)
    {
        text-align: right;
        padding-right: 5px;
        width: 128px;
    }
    #table_summary tbody td:nth-child(4)
    {
        text-align: right;
        padding-right: 5px;
        width: 64px;
    }
    #table_summary tbody td:nth-child(5)
    {
        text-align: right;
        padding-right: 5px;
        width: 128px;
    }
    #table_summary tbody td:nth-child(6)
    {
        text-align: right;
        padding-right: 5px;
        width: 64px;
    }
</style>


{capture name=path}
    {l s='Paypal payment' mod='mpadvpayment'}
{/capture}

<form class='defaultForm form-horizontal' action='' method='POST'>
    <div class="panel panel-default">
        <div class='panel-heading'>
            <i class="icon-warning"></i>
            {$paypal_error_message}
        </div>
    </div>
        <pre>
            FUNCTION:
            {$function}

            PARAMS:
            {$paypal_params|print_r}

            ERROR:
            {$paypal_error|print_r}
        </pre>
        
    <br>
    <p class="cart_navigation clearfix" id="cart_navigation">
        <a
            class="button-exclusive btn btn-default"
            href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}">
            <i class="icon-chevron-left"></i>{l s='Other payment methods' mod='mpadvpayment'}
        </a>
    </p>
</form>