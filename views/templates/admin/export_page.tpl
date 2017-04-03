{*
* 2007-2015 PrestaShop
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
*}
<style>
    .block_content label
    {
        width: 10em !important;
    }
    select.field
    {
        width: 20em;
        height: 3em;
        display: inline-block;
        margin-left: 10px;
    }
    input[type="text"].field
    {
        width: 20em;
        height: 3em;
        display: inline-block;
        margin-left: 10px;
    }
    .date_field
    {
        width: 20em !important;
        height: 3em !important;
        display: inline-block !important;
        text-align: center;
        margin-left: 10px !important;
    }
    .span_container
    {
        display: block;
        margin-bottom: 10px;
    }
    .table-data
    {
        border-collapse: collapse;
        width: 100%;
    }
    .table-data th
    {
        height: 2em;
        padding: 5px;
        text-align: center;
        font-weight: bold;
        border: 1px solid #aaaaaa;
        background-color: #fdf5ce;
    }
    .table-data td
    {
        width: 2em;
        border: 1px solid #aaaaaa;
        padding: 5px;
    }
    .table-data tr:nth-child(odd)
    {
        background-color: #dfdfdf;
    }
    .table-data tr:hover
    {
        cursor: pointer;
        background-color: #fdf5ce;
    }
    .table-data td:nth-child(1)
    {
        text-align: center;
    }
    .table-data td:nth-child(2)
    {
        text-align: right;
    }
    .table-data td:nth-child(3)
    {
        text-align: right;
    }
    .table-data td:nth-child(4)
    {
        text-align: right;
    }
    .table-data td:nth-child(5)
    {
        text-align: center;
    }
    .table-data td:nth-child(6)
    {
        text-align: left;
    }
    .table-data td:nth-child(7)
    {
        text-align: left;
    }
    .table-data td:nth-child(8)
    {
        text-align: left;
    }
    .table-data td:nth-child(9)
    {
        text-align: right;
    }
    .table-data td:nth-child(10)
    {
        text-align: left;
    }
    .table-data td:nth-child(11)
    {
        text-align: center;
    }
    .table-data td:nth-child(12)
    {
        text-align: right;
    }
    .table-data td:nth-child(13)
    {
        text-align: right;
    }
    .table-data td:nth-child(14)
    {
        text-align: right;
    }
    .table-data td:nth-child(15)
    {
        text-align: right;
    }
    .table-data td:nth-child(16)
    {
        text-align: left;
    }
    .table-data td:nth-child(17)
    {
        text-align: right;
    }
    .table-data td:nth-child(18)
    {
        text-align: left;
    }
    .table-data td:nth-child(19)
    {
        text-align: left;
    }
    .submit-btn
    {
        width: 10em;
        height: 3em;
        text-align: center;
        font-size: 14px !important;
        border-radius: 5px;
        background-color: #dfdfdf;
        text-shadow: 1px 1px 1px #cccccc;
        border: 1px solid #aaaaaa;
        margin-top: 10px;
        margin-bottom: 10px;
    }
    .submit-btn:hover
    {
        color: #006fa8;
        font-weight: bold;
        background-position-x: 11px;
    }
    .icon-go
    {
        background-image: url("../modules/mpbartoliniexport/img/go.png");
        background-repeat: no-repeat;
        background-position-x: 10px;
        background-position-y: 5px;
    }
    .icon-csv
    {
        background-image: url("../modules/mpbartoliniexport/img/csv.png");
        background-repeat: no-repeat;
        background-position-x: 10px;
        background-position-y: 5px;
    }
    .icon-xls
    {
        background-image: url("../modules/mpbartoliniexport/img/excel.png");
        background-repeat: no-repeat;
        background-position-x: 10px;
        background-position-y: 5px;
    }
</style>

<!-- Block mymodule -->
<form method="POST">
<div id="MP_BARTOLINI_EXPORT_DIV" class="block">
    <div class="block_content">
            <fieldset>
                <legend>{l s="BARTOLINI EXPORT"}</legend>
                <input type="hidden" name="hasFields" value="1">
                <span class="span_container">
                    <label>{l s="Select status"}</label>
                    <select id="optStatus" name="optStatus" class="field">
                        {foreach $sqlStates as $option}
                            <option value="{$option.id_order_state}" {if $selectedState eq $option.id_order_state}selected="selected"{/if}>{$option.name}</option>
                        {/foreach}
                    </select>
                </span>
                <span class="span_container">
                    <label>Codice Cliente</label>
                    <input type="text" class="field" name="txtCodCli" value="{$txtCodCli}">
                </span>
                <span class="span_container">
                    <label>Codice P.O.</label>
                    <input type="text" class="field" name="txtCodPO" value="{$txtCodPO}">
                </span>
                <span class="span_container">
                    <label>Colli</label>
                    <input type="text" class="field" name="txtColli" value="{$txtColli}">
                </span>
                <span class="span_container">
                    <label>Peso</label>
                    <input type="text" class="field" name="txtPeso" value="{$txtPeso}">
                </span>
                <span class="span_container">
                    <label>Data inizio</label>
                    <input type="text" id="date_start" name="date_start" class="date_field" value="{$dateStart}">
                </span>
                <span class="span_container">
                    <label>Data fine</label>
                    <input type="text" id="date_end" name="date_end" class="date_field" value="{$dateEnd}">
                </span>
                <input type="submit" name="submit_form" class="submit-btn icon-go" value="{l s='INVIA'}">
            </fieldset>
    </div>
    {if $isSubmit}
        <div>
            <hr style="border-bottom: 1px solid #bbbbbb;">
            <input type="submit" name="submit_csv" class="submit-btn icon-csv" value="CSV">
            <input type="submit" name="submit_xls" class="submit-btn icon-xls" value="EXCEL">
            <table class="table-bordered table-data">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Cod Mitt.</th>
                        <th>Cod. OP.</th>
                        <th>Cod. Bolla</th>
                        <th>CRT</th>
                        <th>Ragione Sociale</th>
                        <th>Nome e cognome</th>
                        <th>Indirizzo</th>
                        <th>Cap</th>
                        <th>Citt&agrave;</th>
                        <th>Prov</th>
                        <th>Colli</th>
                        <th>Peso</th>
                        <th>C/ASS</th>
                        <th>Telefono</th>
                        <th>Email</th>
                        <th>Cellulare</th>
                        <th>Rif. Ordine</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $orders as $order}
                        <tr>
                            <td>{$date}</td>
                            <td>{$order.codCli}</td>
                            <td>{$order.codPO}</td>
                            <td>{$order.codBolla}</td>
                            <td>{$order.CRT}</td>
                            <td>{$order.company}</td>
                            <td>{$order.firstname} {$order.lastname}</td>
                            <td>{$order.address1} {$order.address2}</td>
                            <td>{$order.postcode}</td>
                            <td>{$order.city}</td>
                            <td>{$order.state}</td>
                            <td>{$txtColli}</td>
                            <td>{$txtPeso}</td>
                            <td>{$order.cash}</td>
                            <td>{$order.phone}</td>
                            <td>{$order.email}</td>
                            <td>{$order.phone_mobile}</td>
                            <td>{$order.reference}</td>
                            <td>{$order.other}</td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    {/if}
</div>
</form>
    
<!-- /Block mymodule -->

<script type="text/javascript">
    $(document).ready(function() 
    {
        $("#date_start").datepicker();
        $("#date_end").datepicker();
    });
</script>