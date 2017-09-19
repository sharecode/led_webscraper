<?php
session_start();
include("functions.inc.php");
$id=$_GET['id']; /* id and region parameters are only required for Oodle listings so you can ignore this */
$region=$_SESSION["readmin_settings"]["defaultcountry"];
$latlng=explode(",",$_GET['latlng']);
$margs[0]=getMarkerInfo($latlng[0],$latlng[1],$id,$region);
$margs[1]=$id;
$margs[2]=$region;
$margs[3]=$latlng;
$markerData=call_plugin("getMarkerInfo",$margs);
print $_GET['callback'].'('.$markerData[0].')';

?>