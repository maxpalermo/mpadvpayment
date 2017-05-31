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
<table>
<tr id='total-fee'>
    <td class="text-right"><strong>{$fee_display}</strong></td>
    <td class="amount text-right nowrap">
            <strong>{displayPrice price=$fee_price}</strong>
    </td>
    <td class="partial_refund_fields current-edit" style="display:none;"></td>
</tr>
</table>

<strong id='total_order_new'>{displayPrice price=$fee_total}</strong>
    
<script type="text/javascript">
    $(document).ready(function(){
        console.log ('detach');
        var $element = $('#total-fee').detach();
        var $total   = $('#total_order_new').detach()
        $('#total_order').prev().after($element);
        $('#total_order').find('td:nth-child(2)').html($total);
    });
</script>