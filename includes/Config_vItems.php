<?php
$V_Items =
	'{
	"VirtualItems":{
	   	"deaglegold_plan":[
			{
			"variable":"deaglegold_plan",
			"displayName":"Bauplan: Deagle gold"
			}
	 	]
	}
 }';

$Config_vItems = json_decode($V_Items, true);
$Config_vItems = $Config_vItems["VirtualItems"];

?>