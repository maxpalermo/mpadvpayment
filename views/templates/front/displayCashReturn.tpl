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

{capture name=path}
    {l s='Cash payment' mod='mpadvpayment'}
{/capture}

<div class="box">
	<p class="cheque-indent">
		<strong class="dark">{l s='Your order on %s is complete.' sprintf=$shop_name mod='mpadvpayment'}</strong>
	</p>

    <div>
        <div class="panel panel-default">
            <div class='panel-heading'>
                <i class="icon-home"></i>
                {l s='Cash details:' mod='mpadvpayment'}
            </div>  
            <div class='form-wrapper' style='padding: 20px;'>
                <strong>{l s='An email was be sent to your address with these details.' mod='mpadvpayment'}</strong>
            </div>
        </div>
        <br>
    </div>   
    <p>
        {l s='If you have questions, comments or concerns, please contact our' mod='mpadvpayment'} 
        {l s='expert customer support team.' mod='mpadvpayment'}
        <a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}" style='margin-left: 20px;'>
            <i class='icon-3x icon-comments'></i>
        </a>
    </p>
    <br style='clear: both;'>
</div>
<p class="cart_navigation exclusive">
	<a class="button-exclusive btn btn-default" href="{$link->getPageLink('history')|escape:'html':'UTF-8'}"><i class="icon-chevron-left"></i>{l s='Go to order history page' mod='mpadvpayment'}</a>
</p>