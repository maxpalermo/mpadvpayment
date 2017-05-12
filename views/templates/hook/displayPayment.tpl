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

<div class="row">
    {if isset($activeModules['cash']) && $activeModules['cash']}
    <div class="col-xs-12">
        <div class="payment_block_module">
            <a href="{$link->getModuleLink('mpadvpayment','cash')|escape:'html'}" class="mpadvpayment">
                {$cash_summary}
            </a>
        </div>
    </div>
    {/if}
    {if isset($activeModules['bankwire']) && $activeModules['bankwire']}
    <div class="col-xs-12">
        <div class="payment_block_module">
            <a href="{$link->getModuleLink('mpadvpayment','bankwire')|escape:'html'}" class="mpadvpayment">
                {$bankwire_summary}
            </a>
            
        </div>
    </div>
    {/if}
    {if isset($activeModules['paypal']) && $activeModules['paypal']}
    <div class="col-xs-12">
        <div class="payment_block_module">
            <a href="javascript:$('#mp_advpayment_paypal').submit()" class="mpadvpayment" id="paypal_process_payment_">
                {$paypal_summary}
            </a>
        </div>
        <form id="mp_advpayment_paypal" action='{$controllerURL|escape:'htmlall':'UTF-8'}' data-ajax='false' method="post">
            <input type="hidden" name="cancelURL" value="{$cancelURL|escape:'htmlall':'UTF-8'}" />
            <input type="hidden" name="returnURL" value="{$returnURL|escape:'htmlall':'UTF-8'}" />
            <input type="hidden" name="total_pay" value="{$total_pay|escape:'htmlall':'UTF-8'}" />
            <input type="hidden" name="method"    value="{$action|escape:'htmlall':'UTF-8'}" />
        </form>
    </div>
    {/if}
    {if isset($activeModules['paypal pro']) && $activeModules['paypal pro']}
    <div class="col-xs-12">
        <div class="payment_block_module">
            <a href="{$link->getModuleLink('mpadvpayment','card')|escape:'html'}" class="mpadvpayment" id="card_process_payment_">
                <div style='display: inline-block;'>
                    {$paypal_summary}
                </div>
            </a>
        </div>
    </div>
    {/if}
</div>
{assign var=test value=true}
{if $test}            
<div class="panel panel-advice panel-info">
    <div class="panel-heading">
        <i class="icon-2x icon-date"></i>
        <span style="color: #0066CC; text-shadow: 1px 1px 1px #aaaaaa;">
            CLASS SUMMARY
        </span>
    </div>
    <br>
    <div class="panel-body">
        <pre>
        {$classSummary|@print_r}
        </pre>
    </div>
</div>
{/if}
