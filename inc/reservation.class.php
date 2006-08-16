<?php
/*
* @version $Id$
 ----------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2006 by the INDEPNET Development Team.
 
 http://indepnet.net/   http://glpi-project.org
 ----------------------------------------------------------------------

 LICENSE

	This file is part of GLPI.

    GLPI is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    GLPI is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with GLPI; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 ------------------------------------------------------------------------
*/

// ----------------------------------------------------------------------
// Original Author of file: Julien Dombre
// Purpose of file:
// ----------------------------------------------------------------------

 
// CLASSES Reservation_Item and Reservation_Resa

class ReservationItem extends CommonDBTM {

	var $obj = NULL;	

	function ReservationItem () {
		$this->table="glpi_reservation_item";
		$this->type=-1;
	}

	function getfromDB ($ID) {
		global $db;
		// Make new database object and fill variables
		$query = "SELECT * FROM glpi_reservation_item WHERE (ID = '$ID')";
		if ($result = $db->query($query)) {
			$data = $db->fetch_array($result);
			foreach ($data as $key => $val) {
				$this->fields[$key] = $val;
			}
		if (!isset($this->fields["device_type"]))			
		return false;
			switch ($this->fields["device_type"]){
			case COMPUTER_TYPE :
				$this->obj=new Computer;
				break;
			case NETWORKING_TYPE :
				$this->obj=new Netdevice;
				break;
			case PRINTER_TYPE :
				$this->obj=new Printer;
				break;
			case MONITOR_TYPE : 
				$this->obj= new Monitor;	
				break;
			case PERIPHERAL_TYPE : 
				$this->obj= new Peripheral;	
				break;				
			case PHONE_TYPE : 
				$this->obj= new Phone;	
				break;					
			case SOFTWARE_TYPE : 
				$this->obj= new Software;	
				break;					
			}
			if ($this->obj!=NULL)
			return $this->obj->getfromDB($this->fields["id_device"]);
			else return false;
			
		} else {
			return false;
		}
	}
	function getType (){
		global $lang;
		
		switch ($this->fields["device_type"]){
			case COMPUTER_TYPE :
				return $lang["computers"][44];
				break;
			case NETWORKING_TYPE :
				return $lang["networking"][12];
				break;
			case PRINTER_TYPE :
				return $lang["printers"][4];
				break;
			case MONITOR_TYPE : 
				return $lang["monitors"][4];
				break;
			case PERIPHERAL_TYPE : 
				if (isset($this->obj->fields["type"])&&$this->obj->fields["type"]!=0)
					return getDropdownName("glpi_type_peripherals",$this->obj->fields["type"]);
				else	return $lang["peripherals"][4];

				return $lang["peripherals"][4];
				break;				
			case SOFTWARE_TYPE : 
				return $lang["software"][10];
				break;
			case PHONE_TYPE : 
				return $lang["phones"][4];
				break;
			
			}
	
	}
	function getName(){
		if (isset($this->obj->fields["name"])&&$this->obj->fields["name"]!="")
	return $this->obj->fields["name"];
	else return "N/A";
	}

	function getLocation(){
		if (isset($this->obj->fields["location"])&&$this->obj->fields["location"]!="")
	return getTreeValueCompleteName("glpi_dropdown_locations",$this->obj->fields["location"]);
	else return "N/A";
	}
	
	function getLink(){
	
		global $cfg_glpi;
		$out="";	
		switch ($this->fields["device_type"]){
			case COMPUTER_TYPE :
				$out= "<a href=\"".$cfg_glpi["root_doc"]."/front/computer.form.php?ID=".$this->fields["id_device"]."\">".$this->getName();
				if ($cfg_glpi["view_ID"]) $out.= " (".$this->fields["id_device"].")";
				$out.= "</a>";
				break;
			case PHONE_TYPE : 
				$out= "<a href=\"".$cfg_glpi["root_doc"]."/front/phone.form.php?ID=".$this->fields["id_device"]."\">".$this->getName();
				if ($cfg_glpi["view_ID"]) $out.= " (".$this->fields["id_device"].")";
				$out.= "</a>";
				break;				
			case NETWORKING_TYPE :
				$out= "<a href=\"".$cfg_glpi["root_doc"]."/front/networking.form.php?ID=".$this->fields["id_device"]."\">".$this->getName();
				if ($cfg_glpi["view_ID"]) $out.= " (".$this->fields["id_device"].")";
				$out.= "</a>";
				break;
			case PRINTER_TYPE :
				$out= "<a href=\"".$cfg_glpi["root_doc"]."/front/printer.form.php?ID=".$this->fields["id_device"]."\">".$this->getName();
				if ($cfg_glpi["view_ID"]) $out.= " (".$this->fields["id_device"].")";
				$out.= "</a>";
				break;
			case MONITOR_TYPE : 
				$out= "<a href=\"".$cfg_glpi["root_doc"]."/front/monitor.form.php?ID=".$this->fields["id_device"]."\">".$this->getName();
				if ($cfg_glpi["view_ID"]) $out.= " (".$this->fields["id_device"].")";
				$out.= "</a>";
				break;
			case PERIPHERAL_TYPE : 
				$out= "<a href=\"".$cfg_glpi["root_doc"]."/front/peripheral.form.php?ID=".$this->fields["id_device"]."\">".$this->getName();
				if ($cfg_glpi["view_ID"]) $out.= " (".$this->fields["id_device"].")";
				$out.= "</a>";
				break;	
			case SOFTWARE_TYPE : 
				$out= "<a href=\"".$cfg_glpi["root_doc"]."/front/software.form.php?ID=".$this->fields["id_device"]."\">".$this->getName();
				if ($cfg_glpi["view_ID"]) $out.= " (".$this->fields["id_device"].")";
				$out.= "</a>";
				break;								
			case PHONE_TYPE : 
				$out= "<a href=\"".$cfg_glpi["root_doc"]."/front/phone.form.php?ID=".$this->fields["id_device"]."\">".$this->getName();
				if ($cfg_glpi["view_ID"]) $out.= " (".$this->fields["id_device"].")";
				$out.= "</a>";
				break;	
			}
	return $out;
	
	}
	

	function cleanDBonPurge($ID) {

		global $db;

		$query2 = "DELETE FROM glpi_reservation_resa WHERE (id_item = '$ID')";
		$result2 = $db->query($query2);
	}
	
}

class ReservationResa extends CommonDBTM {

	function ReservationResa () {
		$this->table="glpi_reservation_resa";
		$this->type=-1;
	}

	function pre_deleteItem($ID) {
		global $cfg_glpi;
		if ($this->getfromDB($ID))
		if (isset($this->fields["id_user"])&&($this->fields["id_user"]==$_SESSION["glpiID"]||haveRight("reservation_central","w"))){
			// Processing Email
			if ($cfg_glpi["mailing"]){
				$mail = new MailingResa($this,"delete");
				$mail->send();
			}

		}
	}


	function update($input,$target,$item){
		global $lang,$cfg_glpi;
		// Update a printer in the database

		$this->getFromDB($input["ID"]);

		list($begin_year,$begin_month,$begin_day)=split("-",$input["begin_date"]);
		list($end_year,$end_month,$end_day)=split("-",$input["end_date"]);

		list($begin_hour,$begin_min)=split(":",$input["begin_hour"]);
		list($end_hour,$end_min)=split(":",$input["end_hour"]);
		$input["begin"]=date("Y-m-d H:i:00",mktime($begin_hour,$begin_min,0,$begin_month,$begin_day,$begin_year));
		$input["end"]=date("Y-m-d H:i:00",mktime($end_hour,$end_min,0,$end_month,$end_day,$end_year));


		// Get all flags and fill with 0 if unchecked in form
		foreach ($this->fields as $key => $val) {
			if (eregi("\.*flag\.*",$key)) {
				if (!isset($input[$key])) {
					$input[$key]=0;
				}
			}
		}	

		// Fill the update-array with changes
		$x=0;
		foreach ($input as $key => $val) {
			if (array_key_exists($key,$this->fields) && $this->fields[$key] != $input[$key]) {
				$this->fields[$key] = $input[$key];
				$updates[$x] = $key;
				$x++;
			}
		}

		if (!$this->test_valid_date()){
			$this->displayError("date",$item,$target);
			return false;
		}
	
		if ($this->is_reserved()){
			$this->displayError("is_res",$item,$target);
			return false;
		}
	
	
		if (isset($updates)){
			$this->updateInDB($updates);
			// Processing Email
			if ($cfg_glpi["mailing"]){
				$mail = new MailingResa($this,"update");
				$mail->send();
			}
		}
		return true;
	}

	function add($input,$target,$ok=true){
		global $cfg_glpi;
		// Add a Reservation
		if ($ok){

  			// set new date.
   			$this->fields["id_item"] = $input["id_item"];
   			$this->fields["comment"] = $input["comment"];
   			$this->fields["id_user"] = $input["id_user"];
   			$this->fields["begin"] = $input["begin_date"]." ".$input["begin_hour"].":00";
			$this->fields["end"] = $input["end_date"]." ".$input["end_hour"].":00";

			if (!$this->test_valid_date()){
				$this->displayError("date",$input["id_item"],$target);
				return false;
			}
	
			if ($this->is_reserved()){
				$this->displayError("is_res",$input["id_item"],$target);
				return false;
			}

			if ($input["id_user"]>0)
			if ($this->addToDB()){
				// Processing Email
				if ($cfg_glpi["mailing"]){
					$mail = new MailingResa($this,"new");
					$mail->send();
				}
				return true;
			} else return false;
		}
	}


	// SPECIFIC FUNCTIONS
	
	function is_reserved(){
		global $db;
		if (!isset($this->fields["id_item"])||empty($this->fields["id_item"]))
		return true;
		
		// When modify a reservation do not itself take into account 
		$ID_where="";
		if(isset($this->fields["ID"]))
		$ID_where=" (ID <> '".$this->fields["ID"]."') AND ";
		
		$query = "SELECT * FROM glpi_reservation_resa".
		" WHERE $ID_where (id_item = '".$this->fields["id_item"]."') AND ( ('".$this->fields["begin"]."' < begin AND '".$this->fields["end"]."' > begin) OR ('".$this->fields["begin"]."' < end AND '".$this->fields["end"]."' >= end) OR ('".$this->fields["begin"]."' >= begin AND '".$this->fields["end"]."' < end))";
//		echo $query."<br>";
		if ($result=$db->query($query)){
			return ($db->numrows($result)>0);
		}
		return true;
		}
	function test_valid_date(){
		return (strtotime($this->fields["begin"])<strtotime($this->fields["end"]));
		}

	function displayError($type,$ID,$target){
		global $HTMLRel,$lang;
		
		echo "<br><div align='center'>";
		switch ($type){
			case "date":
			 echo $lang["reservation"][19];
			break;
			case "is_res":
			 echo $lang["reservation"][18];
			break;
			default :
				echo "Erreur Inconnue";
			break;
		}
		echo "<br><a href='".$target."?show=resa&amp;ID=$ID'>".$lang["reservation"][20]."</a>";
		echo "</div>";
		}
	function textDescription($format="text"){
		global $lang;
		
		$ci=new ReservationItem();
		$ci->getFromDB($this->fields["id_item"]);		
		
		$u=new User();
		$u->getFromDB($this->fields["id_user"]);
		$content="";

		if($format=="html"){
			$content= "<html><head> <style type=\"text/css\">";
			$content.=".description{ color: inherit; background: #ebebeb; border-style: solid; border-color: #8d8d8d; border-width: 0px 1px 1px 0px; }";
			$content.=" </style></head><body>";
			$content.="<span style='color:#8B8C8F; font-weight:bold;  text-decoration:underline; '>".$lang["common"][37].":</span> ".$u->getName()."<br>";
			$content.="<span style='color:#8B8C8F; font-weight:bold;  text-decoration:underline; '>".$lang["mailing"][7].":</span> ".$ci->getName()."<br>";
			$content.="<span style='color:#8B8C8F; font-weight:bold;  text-decoration:underline; '>".$lang["search"][8].":</span> ".convDateTime($this->fields["begin"])."<br>";
			$content.="<span style='color:#8B8C8F; font-weight:bold;  text-decoration:underline; '>".$lang["search"][9].":</span> ".convDateTime($this->fields["end"])."<br>";
			$content.="<span style='color:#8B8C8F; font-weight:bold;  text-decoration:underline; '>".$lang["common"][25].":</span> ".$this->fields["comment"]."<br>";
		} else { // text format
			$content.=$lang["mailing"][1]."\n";
        	        $content.=$lang["common"][37].": ".$u->getName()."\n";
                	$content.=$lang["mailing"][7].": ".$ci->getName()."\n";
                	$content.=$lang["search"][8].": ".convDateTime($this->fields["begin"])."\n";
                	$content.=$lang["search"][9].": ".convDateTime($this->fields["end"])."\n";
                	$content.=$lang["common"][25].": ".$this->fields["comment"]."\n";
                	$content.=$lang["mailing"][1]."\n";
		}
		return $content;
		
	}

}


?>
