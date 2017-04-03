<?php
/**
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2016 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

require_once(_PS_TOOL_DIR_.'tcpdf/config/lang/eng.php');
require_once(_PS_TOOL_DIR_.'tcpdf/tcpdf.php');
class AdminMpBartoliniExportController extends ModuleAdminController {

	public function __construct()
	{
		$this->bootstrap = true;
		$this->context = Context::getContext();

		$id_order = (int)Tools::getValue('id_order');
		$ready = (int)Tools::getValue('ready');
		$baseLayout = (int)Tools::getValue('label');
		$isLogo = (int)Tools::getValue('logo');
		$showDate = (int)Tools::getValue('date');
		$showOnumber = (int)Tools::getValue('onumber');
		$showSlip = (int)Tools::getValue('slip');
		$showWeight = (int)Tools::getValue('dnumber');
		$pLabel = ($ready == 1) ? $this->l('Re-generate') : $this->l('Generate');
		
		parent::__construct();
	}

	public function initToolbar()
	{
		parent::initToolbar();
		unset($this->toolbar_btn['new']);
	}

	public function postProcess()
	{
		if (Tools::isSubmit('MP_BARTOLINI_EXPORT_SUBMIT'))
		{
			$order_id = (int)Tools::getValue('current_order_id');
			$order_state = (int)Tools::getValue('ORDER_STATE');
			$order = new Order($order_id);
			if ($order_state > 0)
				$order->setCurrentState($order_state);

			Tools::redirectAdmin($this->context->link->getAdminLink('AdminMpBartoliniExport').
                                '&id_order='.(int)$order_id.
                                '&ready=1&logo='.Tools::getValue('ORDER_LOGO').
                                '&label='.Tools::getValue('LABEL_SIZE').
                                '&date='.Tools::getValue('ORDER_DATE').
                                '&onumber='.Tools::getValue('ORDER_NUMBER').
                                '&dnumber='.Tools::getValue('ORDER_DEL_NUMBER').
                                '&os='.Tools::getValue('ORDER_STATE').'&slip='.Tools::getValue('SLIP_NUMBER'));
		}
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addJS(_PS_MODULE_DIR_.'printlabelspro/views/js/label.js');
	}
        
        public function initContent() 
        {    

            parent::initContent();
            //Get lang
            $lang_id = $this->context->language->id;
            //Get state list
            $db = Db::getInstance();
            $query = new DbQuery();
            $query
                    ->select("osl.id_order_state")
                    ->select("osl.name")
                    ->from("order_state_lang","osl")
                    ->innerJoin("order_state","os","os.id_order_state=osl.id_order_state")
                    ->where("os.deleted=0")
                    ->where("osl.id_lang=$lang_id")
                    ->orderBy("osl.name");
            $sqlStates = $db->executeS($query);
            
            //Get fields
            if (Tools::isSubmit('submit_form') || Tools::isSubmit('submit_csv') || Tools::isSubmit('submit_xls'))
            {
                $stateSelected = Tools::getValue("optStatus",0);
                $dateStart = Tools::getValue("date_start");
                $dateEnd = Tools::getValue("date_end");
                $txtCli = Tools::getValue("txtCodCli",0);
                $txtPO = Tools::getValue("txtCodPO",0);
                $txtColli = Tools::getValue("txtColli",0);
                $txtPeso = Tools::getValue("txtPeso",0);
                $isSubmit = TRUE;
                $dateObj = new DateTime();
                $date = $dateObj->format("d/m/Y");
                
                Configuration::updateValue("MP_BARTOLINI_EXPORT_ID_CUSTOMER",$txtCli);
                Configuration::updateValue("MP_BARTOLINI_EXPORT_ID_PO",$txtPO);
                Configuration::updateValue("MP_BARTOLINI_EXPORT_COLLI",$txtColli);
                Configuration::updateValue("MP_BARTOLINI_EXPORT_PESO",$txtPeso);
                
                //Get Orders
                $queryDate = "select STR_TO_DATE('$dateStart','%d/%m/%Y') as D1, STR_TO_DATE('$dateEnd','%d/%m/%Y') as D2";
                $sqlDate = $db->getRow($queryDate);
                
                $queryOrders = new DbQueryCore();
                $queryOrders
                        ->select("id_order")
                        ->from("orders")
                        ->where("date_add between '" . $sqlDate["D1"] . "' and '" . $sqlDate["D2"] . "'")
                        ->where("current_state = $stateSelected")
                        ->orderBy("id_order");
                $sqlOrders = $db->executeS($queryOrders);
                $orders = [];
                $csv[]=[
                            "VABCCM",
                            "VABLNP",
                            "VABCBO",
                            "VABCTR",
                            "VABRSD",
                            "VABRD2",
                            "VABIND",
                            "VABCAD",
                            "VABLOD",
                            "VABPRD",
                            "VABNCL",
                            "VABPKB",
                            "VABCAS",
                            "VABTRC",
                            "VABEMD",
                            "VABCEL",
                            "VABRMN",
                            "VABNOT",
                        ];
                foreach($sqlOrders as $idOrder)
                {
                    $orderObj = new Order($idOrder["id_order"]);
                    $addressObj = new Address($orderObj->id_address_delivery,$lang_id);
                    $stateObj = new StateCore($addressObj->id_state);
                    $customerObj = new CustomerCore($addressObj->id_customer);
                    
                    $order=[];
                    $order["codCli"] = $txtCli;
                    $order["codPO"] = $txtPO;
                    $order["codBolla"]="";
                    $order["CRT"] = "0";
                    $order["id_customer"] = $addressObj->id_customer;
                    $order["company"] = $addressObj->company;
                    $order["firstname"] = $addressObj->firstname;
                    $order["lastname"] = $addressObj->lastname;
                    $order["address1"] = $addressObj->address1;
                    $order["address2"] = $addressObj->address2;
                    $order["postcode"] = $addressObj->postcode;
                    $order["city"] = strtoupper($addressObj->city);
                    $order["state"] = $stateObj->iso_code;
                    $order["colli"] = $txtColli;
                    $order["peso"] = $txtPeso;
                    if(strpos($orderObj->module,"cash")!=-1)
                    {
                        $order["cash"]=  number_format($orderObj->total_paid, 2, ",", ".") ;
                        $order["codBolla"]="4";
                    }
                    else
                    {
                        $order["cash"]="";
                        $order["codBolla"]="1";
                    }
                    $order["phone"] = $addressObj->phone;
                    $order["email"] = strtolower($customerObj->email);
                    $order["phone_mobile"] = $addressObj->phone_mobile;
                    $order["reference"] = $orderObj->reference;
                    $order["other"] = str_replace(["\n","\r","\n\r"]," ",$addressObj->other);
                    $orders[] = $order;
                    
                    if(Tools::isSubmit("submit_csv"))
                    {
                        $lineCsv = $order;
                        $lineCsv["firstname"] = $lineCsv["firstname"] . " " . $lineCsv["lastname"];
                        $lineCsv["address1"] = $lineCsv["address1"] . " " . $lineCsv["address2"];
                        $lineCsv["company"] = strtoupper($lineCsv["company"]);
                        $lineCsv["firstname"] = strtoupper($lineCsv["firstname"]);
                        unset($lineCsv["lastname"]);
                        unset($lineCsv["id_customer"]);
                        unset($lineCsv["address2"]);
                        $csv[] = $lineCsv;
                    }
                }
            }
            else
            {
                $stateSelected=0;
                $dateStart="";
                $dateEnd="";
                $queryOrders="";
                $sqlOrders=[];
                $orders=[];
                $txtCli = Configuration::get("MP_BARTOLINI_EXPORT_ID_CUSTOMER");
                $txtPO = Configuration::get("MP_BARTOLINI_EXPORT_ID_PO");
                $txtColli = Configuration::get("MP_BARTOLINI_EXPORT_COLLI");
                $txtPeso = Configuration::get("MP_BARTOLINI_EXPORT_PESO");
                $isSubmit = FALSE;
                $date = "";
            }
            
            if(Tools::isSubmit("submit_csv"))
            {
                $file = dirname(__FILE__) 
                . DIRECTORY_SEPARATOR 
                . ".." 
                . DIRECTORY_SEPARATOR 
                . ".." 
                . DIRECTORY_SEPARATOR 
                . "download" 
                . DIRECTORY_SEPARATOR
                . date("YmdHis") . ".csv";
                
                $csv_export = "";
                foreach($csv as $line)
                {
                    $csv_export .= implode("\t",$line) . "\n";
                }
                
                $handle = fopen($file,"w");
                $file_content = $csv_export;
                fwrite($handle, $file_content);
                fclose($handle);
                chmod($file, 0775);
                
                header('Content-Description: File Transfer');
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename='.basename($file));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                ob_clean();
                flush();
                readfile($file);
                exit();
            }
            
            $smarty = $this->context->smarty;
            $smarty->assign([
                "sqlStates"=>$sqlStates,
                "sqlOrders"=>$sqlOrders,
                "query"=>$query,
                "queryOrders"=>$queryOrders,
                "orders"=>$orders,
                "orderObj"=>new Order(90),
                "addressObj"=>new Address(10),
                "selectedState"=>$stateSelected,
                "dateStart"=>$dateStart,
                "dateEnd"=>$dateEnd,
                "isSubmit"=>$isSubmit,
                "txtCodCli"=>$txtCli,
                "txtCodPO"=>$txtPO,
                "txtColli"=>$txtColli,
                "txtPeso"=>$txtPeso,
                "date"=>$date,
                    
            ]);
            $content = $smarty->fetch(_PS_MODULE_DIR_ . 'mpbartoliniexport/views/templates/admin/export_page.tpl');
            $this->context->smarty->assign(array('content' => $this->content . $content));
        } 
}
