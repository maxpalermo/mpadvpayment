{*
* 2007-2016 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*  Modified by Massimiliano Palermo <info@mpsoft.it> 
*}
<table id="total-tab" width="100%">

	<tr>
		<td class="grey" width="70%">
			{l s='Total Products' pdf='true'}
		</td>
		<td class="white" width="30%">
			{displayPrice currency=$order->id_currency price=$footer.products_before_discounts_tax_excl}
		</td>
	</tr>

	{if $footer.product_discounts_tax_excl > 0}

		<tr>
			<td class="grey" width="70%">
				{l s='Total Discounts' pdf='true'}
			</td>
			<td class="white" width="30%">
				- {displayPrice currency=$order->id_currency price=$footer.product_discounts_tax_excl}
			</td>
		</tr>

	{/if}
	{if !$order->isVirtual()}
	<tr>
		<td class="grey" width="70%">
			{l s='Shipping Cost' pdf='true'}
		</td>
		<td class="white" width="30%">
			{if $footer.shipping_tax_excl > 0}
				{displayPrice currency=$order->id_currency price=$footer.shipping_tax_excl}
			{else}
				{l s='Free Shipping' pdf='true'}
			{/if}
		</td>
	</tr>
	{/if}
        
        {if !empty($footer.fee_tax_excl)}
	<tr>
		<td class="grey" width="70%">
			{l s='Fee Cost' pdf='true'}
		</td>
		<td class="white" width="30%">
			{displayPrice currency=$order->id_currency price=$footer.fee_tax_excl}
		</td>
	</tr>
	{/if}
        
        {if !empty($footer.discount_tax_excl)}
	<tr>
		<td class="grey" width="70%">
			{l s='Order discount' pdf='true'}
		</td>
		<td class="white" width="30%">
			{displayPrice currency=$order->id_currency price=-$footer.discount_tax_excl}
		</td>
	</tr>
	{/if}

	{if $footer.wrapping_tax_excl > 0}
		<tr>
			<td class="grey">
				{l s='Wrapping Cost' pdf='true'}
			</td>
			<td class="white">{displayPrice currency=$order->id_currency price=$footer.wrapping_tax_excl}</td>
		</tr>
	{/if}

	<tr class="bold">
		<td class="grey">
			{l s='Total (Tax excl.)' pdf='true'}
		</td>
		<td class="white">
                    {assign var=total_tax_excl value={$fee_summary->getTotal_paid_tax_excl()}}
                    {displayPrice currency=$order->id_currency price=$total_tax_excl}
		</td>
	</tr>
	<tr class="bold">
		<td class="grey">
			{l s='Total Tax' pdf='true'}
		</td>
		<td class="white">
                    {assign var=total_taxes value={$fee_summary->getTotal_paid() - $fee_summary->getTotal_paid_tax_excl()}}
                    {displayPrice currency=$order->id_currency price=$total_taxes}
		</td>
	</tr>
	<tr class="bold">
		<td class="grey">
			{l s='Total' pdf='true'}
		</td>
		<td class="white">
                    {assign var=total_tax_excl value={$fee_summary->getTotal_paid()}}
                    {displayPrice currency=$order->id_currency price=$total_tax_excl}
		</td>
	</tr>
        {assign var=fee_tax_excl value=$fee_summary->getFee_tax_excl()}
        {assign var=fee_tax      value=$fee_summary->getFee()-$fee_summary->getFee_tax_excl()}
        <tr class="bold">
		<td class="grey">
                    {if $fee_tax_excl<0}
			{l s='Payment method discount' pdf='true'}
                    {else}
                        {l s='Payment method fee' pdf='true'}
                    {/if}
		</td>
		<td class="white">
                    {displayPrice currency=$order->id_currency price=$fee_tax_excl}
		</td>
	</tr>
        <tr class="bold">
		<td class="grey">
                    {if $fee_tax_excl<0}
			{l s='Taxes on discount' pdf='true'}
                    {else}
                        {l s='Taxes on fee' pdf='true'}
                    {/if}
		</td>
		<td class="white">
                    {assign var=fee_tax value=$fee_summary->getFee()-$fee_summary->getFee_tax_excl()}
                    {displayPrice currency=$order->id_currency price=$fee_tax}
		</td>
	</tr>
	<tr class="bold big">
		<td class="grey">
			{l s='Total document' pdf='true'}
		</td>
		<td class="white">
                    {assign var=total_document value=$fee_summary->getTotal_document_tax_incl()}
                    {displayPrice currency=$order->id_currency price=$total_document}
		</td>
	</tr>
</table>
                