<?php
include("sharelib.php");
$conn = db();
$connMAP = dbMAP();
$notifyError = '';
$currentDate = date("Y-m-d");
$deleteDate = date("Y-m-d", strtotime("-30 days"));
$currentDateTime = date("Y-m-d H:i:s");

//[[1]]
//EXPIRED ASSET
$delExpired = 'DELETE FROM `asset` WHERE 
	`date1` < "'.$deleteDate.'" AND
	`date2` < "'.$deleteDate.'" AND 
	`date3` < "'.$deleteDate.'" AND 
	`date4` < "'.$deleteDate.'" AND 
	`date5` < "'.$deleteDate.'" AND 
	`date6` < "'.$deleteDate.'" AND 
	(`date7` < "'.$deleteDate.'" OR `date7` IS NULL OR `date7` = "") AND 
	(`date8` < "'.$deleteDate.'" OR `date8` IS NULL OR `date8` = "")';
//echo $delExpired.'<hr/>';
$result = $conn->query($delExpired);
if ($conn->affected_rows > 0){
	$notifyError .= 'มีสินทรัพย์ ที่โดนลบออกจากระบบ เนื่องจาก หมดอายุจำนวน '.$conn->affected_rows."รายการ\r\n";
	$notifyError .= "รายการลบ\r\n";
	/*$delExpiredCount = 0;
	while($row = $result->fetch_object()){
		$delExpiredCount++;
		$notifyError .= $delExpiredCount.'/delExpiredCountSUM '.$row->url."\r\n";
	}
	$notifyError = str_replace('delExpiredCountSUM', $delExpiredCount, $notifyError);
	$notifyError .= "\r\n";*/
}

//[[2]]
//OVERPRICE ASSET OR WRONG CONFIG
//disabled
/*$q = 'UPDATE `asset` SET  enable = 0 WHERE `estimated_price` > 2500000 AND`enable` = 1' ;
$result = $conn->query($q);
if ($conn->affected_rows > 0){
	$notifyError .= 'มีสินทรัพย์  โดนปิดการอัพเดต เนื่องจาก ราคาเกินกว่า 2.5ล้าน จำนวน'.$conn->affected_rows."รายการ\r\n";
}*/

//[[3]]
//SOLD OBJECT
$q = "UPDATE  `asset`  SET `enable` = 0
WHERE  `date1_status` LIKE  '%ขายได้%'
	OR  `date2_status` LIKE  '%ขายได้%'
	OR  `date3_status` LIKE  '%ขายได้'
	OR  `date4_status` LIKE  '%ขายได้%'
	OR  `date5_status` LIKE  '%ขายได้%'
	OR  `date6_status` LIKE  '%ขายได้%'
	OR  `date7_status` LIKE  '%ขายได้%'
	OR  `date8_status` LIKE  '%ขายได้%' AND `enable` = 1";
$conn->query($q);
if ($conn->affected_rows > 0){
	$notifyError .= "มีสินทรัพย์ ขายได้ จำนวน" .$conn->affected_rows. "รายการ\r\n";
}

//WITHDRAW OBJECT
$q = "UPDATE  `asset`  SET `enable` = 0
WHERE  (`date1_status` LIKE  '%ถอนการยึด%' OR `date1_status` LIKE  '%งดขาย%')
	AND  (`date2_status` LIKE  '%ถอนการยึด%' OR `date2_status` LIKE  '%งดขาย%')
	AND  (`date3_status` LIKE  '%ถอนการยึด%' OR `date3_status` LIKE  '%งดขาย%')
	AND  (`date4_status` LIKE  '%ถอนการยึด%' OR `date4_status` LIKE  '%งดขาย%')
	AND  (`date5_status` LIKE  '%ถอนการยึด%' OR `date5_status` LIKE  '%งดขาย%')
	OR  `date6_status` LIKE  '%ถอนการยึด%' OR `date6_status` LIKE  '%งดขาย%'
    AND `enable` = 1";
$conn->query($q);
if ($conn->affected_rows > 0){
	$notifyError .= "มีสินทรัพย์ ถอนการยึด จำนวน" .$conn->affected_rows. "รายการ\r\n";
}

//[[4]]
//REMOVE EXPIRE MAP FROM LISTING
//[[4.1]]
//find all current Map id
$qMAP = "SELECT `id` FROM `listing` WHERE 1";
$result = $connMAP->query($qMAP);
//echo $qMAP."\r\n";
//generate map count
$mapCount = 0;
while($row = $result->fetch_object()){
	$allListID[] = $row->id;
	$mapCount++;
}

//find all current Map Outdated id
//MAP->dttm = next_update;
$qMAPoutdate = 'SELECT count(*) as `outdatenum` FROM `listing` WHERE `dttm` < "'.$currentDateTime.'"';
$result = $connMAP->query($qMAPoutdate);
//echo $qMAPoutdate."\r\n";
$row = $result->fetch_object();
$outdateMap = $row->outdatenum;

//[[4.2]]
//find all current Asset id
$q = 'SELECT `id` FROM `asset` WHERE `enable` = 1';
$result = $conn->query($q);
//echo $q."\r\n";
//generate asset count
$assetCount =0;
while($row = $result->fetch_object()){
	$allAssetID[] = $row->id;
	$assetCount++;
}

//[[4.3]]
//http://php.net/manual/en/function.array-diff.php
//Returns an array containing all the entries from "$allListID" that are not present in any of the other arrays.
$beRemoveListingID = array_diff($allListID, $allAssetID);
$deletedCount = 0;
foreach($beRemoveListingID as $beDelatedID){
	if($beDelatedID <> '' and $beDelatedID > 0){
		$deletedListing = "DELETE FROM `listing` WHERE `id` =".$beDelatedID;
		$connMAP->query($deletedListing);
		//echo '$q:'.$q."<br/>";
		$mapCount--;
		$deletedCount++;
	}	
}

//[[5]]
//write counting stat when has map any deleted
if ($deletedCount > 0){
	$file = 'maplastupdate.txt';
	$data = date("Y-m-d H:i:s");
	file_put_contents($file, $data);
	$notifyError .= 'มีแผนที่ หมดอายุจำนวน '.$deletedCount."รายการ\r\n";
}

if( !empty($notifyError) ){
	echo "แจ้งอัพเดตรายการสินทรัพย์ ขายได้ , ถอนการยึด หรือ หมดอายุ<br/>";
	echo nl2br($notifyError);
	dailyError( $notifyError, "แจ้งอัพเดตรายการสินทรัพย์ ขายได้ , ถอนการยึด หรือ หมดอายุ" );
	//echo $notifyError;
}else{
	echo "ไม่มีรายการอัพเดต ขายได้ , ถอนการยึด หรือ หมดอายุ<br/>";
}