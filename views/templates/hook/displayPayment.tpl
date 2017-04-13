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
            <a href="{$link->getModuleLink('mpadvpayment','cash')|escape:'html'}" class="mpadvpayment mpadvpayment_cash">
                <div style='display: inline-block;'>
                    {$cash_summary}
                </div>
            </a>
        </div>
    </div>
    {/if}
    {if isset($activeModules['bankwire']) && $activeModules['bankwire']}
    <div class="col-xs-12">
        <div class="payment_block_module">
            <a href="{$link->getModuleLink('mpadvpayment','bankwire')|escape:'html'}" class="mpadvpayment mpadvpayment_bankwire">
                <div style='display: inline-block;'>
                    {$bankwire_summary}
                </div>
            </a>
            
        </div>
    </div>
    {/if}
    {if isset($activeModules['paypal']) && $activeModules['paypal']}
    <div class="col-xs-12">
        <div class="payment_block_module">
            <a href="{$link->getModuleLink('mpadvpayment','paypal')|escape:'html'}" class="mpadvpayment mpadvpayment_paypal">
                <div style='display: inline-block;'>
                    {$paypal_summary}
                </div>
            </a>
        </div>
    </div>
    {/if}
</div>

