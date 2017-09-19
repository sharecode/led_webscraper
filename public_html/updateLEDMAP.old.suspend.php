<?php
$timer_start = microtime(true);
include("sharelib.php");
$connMAP = dbMAP(); // map db
$conn = db(); //asset db

function buildUpdateReport($assetId, $images = true){	
	$objAsset = getObjAsset($assetId);
	$reportQ  = '';
	//pid 1 = bkk hardcode
	if($objAsset->pen == 'bangkok'){
		$tumponText = 'แขวง'.$objAsset->tumbon.' ';
		$amphurText = ' เขต'.$objAsset->ath.' ';
		$provinceText = '';
	}else{
		$tumponText = 'ตำบล'.$objAsset->tumbon.' ';
		$amphurText = ' อำเภอ'.$objAsset->ath.' ';
		$provinceText = 'จังหวัด'.$objAsset->pth.' ';
	}
	
	$reportQ  = mailTableHeader();	
	$reportQ .= mailRow("ประเภท",$objAsset->tth);
	$reportQ .= mailRow("ที่ตั้ง",$tumponText.$amphurText.$provinceText);	
	$reportQ .= mailRow("พื้นที่",showWnumberReport($objAsset->size400w, $objAsset->size100w, $objAsset->sizew, true));
	$reportQ .= mailRow("ราคาประเมิน",number_format($objAsset->estimated_price)."บาท");
	$reportQ .= mailRow("การติดจำนอง",$objAsset->debt);
	$reportQ .= mailTableFooter();
	$reportQ .= mailTableHeader();
	$reportQ .= mailRow("เงื่อนไขผู้เข้าสู้ราคา",$objAsset->conditions);
	$reportQ .= mailRow("นัดที่ 1  ",showPramulDateReport($objAsset->date1) ,$objAsset->date1_status);
	$reportQ .= mailRow("นัดที่ 2   ",showPramulDateReport($objAsset->date2) ,$objAsset->date2_status);
	$reportQ .= mailRow("นัดที่ 3   ",showPramulDateReport($objAsset->date3) ,$objAsset->date3_status);
	$reportQ .= mailRow("นัดที่ 4   ",showPramulDateReport($objAsset->date4) ,$objAsset->date4_status);
	$reportQ .= mailRow("นัดที่ 5   ",showPramulDateReport($objAsset->date5) ,$objAsset->date5_status);
	$reportQ .= mailRow("นัดที่ 6   ",showPramulDateReport($objAsset->date6) ,$objAsset->date6_status);
	if($objAsset->date7 <> '0000-00-00' AND $objAsset->date7 <> ''){
		$reportQ .= mailRow("นัดที่ 7   ",showPramulDateReport($objAsset->date7) ,$objAsset->date7_status);
	}
	if($objAsset->date8 <> '0000-00-00' AND $objAsset->date8 <> ''){
		$reportQ .= mailRow("นัดที่ 8   ", showPramulDateReport($objAsset->date8), $objAsset->date8_status);
	}
	$reportQ .= mailTableFooter();
	$reportQ .= mailTableHeader();
	$reportQ .= mailRow("เลขโฉนด",$objAsset->deed_no.'   เจ้าของชื่อ '. $objAsset->land_owner);
	$reportQ .= mailRow("ลำดับขาย",$objAsset->sale_order_main .'-'.$objAsset->sale_order_sub);
	$reportQ .= mailRow("รหัสทรัพย์",'MAP#'.$assetId.' '.$objAsset->law_suit_no.'/'.$objAsset->law_suit_year);
	$reportQ .= mailRow("พบทรัพย์ครั้งแรก", date("d/m/Y H:i:s", strtotime($objAsset->first_seen)));	
	$reportQ .= mailRow("อัพเดตครั้งต่อไป", date("d/m/Y H:i:s", strtotime($objAsset->next_update)));	
	$reportQ .= mailRow("ลิ้ง ที่เกี่ยวข้อง",'<a href="'.$objAsset->url.'" target="_blank" class="bt led btn btn-success">LED Link</a> <a href="http://example.com/map/index.php?ptype=viewFullListing&reid='.$assetId.'" target="_blank" class="bt aid  btn btn-success">MAP#'.$assetId.'</a> <a href="http://asset.led.go.th/report_new/reports.asp?ALAW_SUIT_NO='.int($objAsset->law_suit_no).'&ALAW_SUIT_YEAR='.int($objAsset->law_suit_year).'" target="_blank" class="bt sreport  btn btn-success">Sale Report</a>');
	$reportQ .= mailTableFooter();
	
	if($images === true){
		$reportQ .= mailTableHeader();
		if(!empty($objAsset->image1) OR !empty($objAsset->image2) OR !empty($objAsset->map)){
			$reportQ .= mailRow('ภาพทรัพย์ ');
			if(!empty($objAsset->image1)){
				$reportQ .= mailRow(showImageReport($objAsset->image1));
			}			
			if(!empty($objAsset->image2)){
				$reportQ .= mailRow(showImageReport($objAsset->image2));
			}			
			if(!empty($objAsset->map)){
				$reportQ .= mailRow(showImageReport($objAsset->map));
			}			
		}
		$reportQ .= mailTableFooter();
	}
	return $reportQ;
}

function buildMapDescription($assetId, $image = false){	
	return buildUpdateReport($assetId, false);
}

function showWnumberReport($size400w, $size100w, $sizew , $text = true){
	//listing size
	$listArea = '';
	if($size400w > 0){
		$listArea .= ' '.$size400w.'ไร่';			
	}else{
		$listArea .= ' - ไร่';
	}
	if($size100w > 0){
		$listArea .= ' '.$size100w.'งาน';
	}else{
		$listArea .= ' - งาน';
	}
	if($sizew > 0){
		$listArea .= ' '.$sizew.'ตรว.';
	}else{
		$listArea .= ' - ตรว';
	}
	if($text === false){
		$listArea = $size400w*400 + $size100w*100 + $sizew;
	}	
	return $listArea;
}

function addressDecode($address){
	$address = str_replace("-",'',$address);
	$address = trim($address);
	if(!empty($address)){
		$encodeAddress = urlencode($address);
		$request_url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$encodeAddress."&key=AIzaSyBtPLlkp5cJ80-KK8V8lLK3y1BVbFK9nT8";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $request_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$json = json_decode(curl_exec($ch));	
		$status = $json->status;
		if ($status == "OK") {
			  $Lat = $json->results[0]->geometry->location->lat;
			  $Lon = $json->results[0]->geometry->location->lng;
		}else{
			 $newaddress = explode(' ', $address);
			 array_shift($newaddress);
			 $address = implode(' ', $newaddress);
			 $address = trim($address);
			 if($address <> ''){
				 sleep(2);//wait 2 secound and do again
				 $arrayL = addressDecode($address);
				 $Lat = $arrayL['la'];
				 $Lon = $arrayL['lo'];
			 }else{
				 $Lat = 0;
				 $Lon = 0;
				 $notifyError = __FILE__ ." update Map ไวไปแล้ว  ลด ครอนลง\r\n";
				 $notifyError .= "ที่อยูที่ไม่สามารถ ถอดรหัส คือ ".$address."\r\n";
				 notifyError($notifyError, "ตรวจพบ การถอดรกัส พิกัดผิดพลาด");
			 }		
		}
		curl_close($ch);
	}else{
		$Lat = 0;
		$Lon = 0;
	}
	$arrayL['la'] = $Lat;
	$arrayL['lo'] = $Lon;
	return $arrayL;
}


$qMAP = "SELECT id FROM listing WHERE 1";
$result = $connMAP->query($qMAP);
//echo $qMAP."\r\n";
while($row = $result->fetch_object()){
	$allListID[] = $row->id;
}
//echo 'size '.sizeof($allListID)."\r\n";

if(sizeof($allListID)==0){
	$qLimit = ' LIMIT 10';
}else{
	$qLimit = '';
}

$q = 'SELECT id	FROM asset WHERE (`enable` = 1) AND (`deed_no` IS NOT NULL) AND (`estimated_price` <= 2500000) AND (`sold` = 0) '.$qLimit;
$result = $conn->query($q);
//echo $q."\r\n";
while($row = $result->fetch_object()){
	$allAssetID[] = $row->id;
}
//echo 'size '.sizeof($allAssetID)."\r\n";

//print_r($allListID);
//print_r($allAssetID);
if(sizeof($allListID) == 0){
	$TargetID = $allAssetID;	
}else{
	$TargetID = array_diff($allAssetID, $allListID);
}

$TargetID[] = 1;
		
$q = 'SELECT 
	asset.*,	
	asset.page_id as pageid,
	type.id as tid,
	type.en as ten,
	type.th as tth,
	type.encode as tencode,
	amphur.id as aid,
	amphur.en as aen,
	amphur.th as ath,
	amphur.encode as aencode,
	province.id as pid,
	province.en as pen,
	province.th as pth,
	province.encode as pencode
	FROM asset 
	LEFT JOIN type ON asset.type_id = type.id
	LEFT JOIN amphur ON asset.amphur_id = amphur.id
	LEFT JOIN province ON amphur.province_id = province.id
	WHERE asset.`id` IN (' . implode(',', array_map('intval', $TargetID)) . ')	LIMIT 20';
$result = $conn->query($q);
echo '$q: '.$q."<br/>";
if ($conn->affected_rows > 0){
	while($row = $result->fetch_object()){
		
		//find retype text
		switch ($row->tid) {
			case 1:
				$retype = 'Commercial';//LAND
			break;
			case 2:
				$retype = 'Residential';//HOME
			break;
		}
		
		//listing size text
		$listArea = showWnumberReport($row->size400w, $row->size100w, $row->sizew, true);
		
		//listing size number
		$listAreanum = showWnumberReport($row->size400w, $row->size100w, $row->sizew, false);
		
		//image
		$listImage = '';
		if($row->image1 <> ""){
			$listImage .= $row->image1.'::';
		}
		if($row->image2 <> ""){
			$listImage .= $row->image2.'::';
		}
		if($row->map <> ""){
			$listImage .= $row->map.'::';
		}
		if($listImage == ''){
			$showimage = 'no';
		}else{
			$showimage = NULL;
		}
		
		$listAddress = "";
		if($row->pid == 10){
			$listAddress = " แขวง".$row->tumbon." เขต".$row->ath." จังหวัด".$row->pth;
		}else{
			$listAddress = " ตำบล".$row->tumbon." อำเภอ".$row->ath." จังหวัด".$row->pth;
		}
		$listAddress = trim($listAddress);
		if($listAreanum >0){
			$listAverageprice = number_format(ceil($row->estimated_price/$listAreanum))."บาท/ตรว.";
		}else{
			$listAverageprice = 0;
		}
		
		$listDescription  = 'ทรัพย์ประเภท'.$row->tth." ".$listAverageprice."\r\n";
		$listDescription .= "ขนาดพื้นที่".$listArea."\r\n";
		$listDescription .= "เลขคดี ".$row->law_suit_no."/".$row->law_suit_year."\r\n";
		$listDescription .= $row->conditions;
		$listDescription = nl2br($listDescription);
		
		//arear filter		
		$listAreaSizeFilter = 1;
		switch ($listAreanum){
			case $listAreanum > 800:
				$listAreaSizeFilter = 6;
			break;
			case $listAreanum > 400:
				$listAreaSizeFilter = 5;
			break;
			case $listAreanum > 200:
				$listAreaSizeFilter = 4;
			break;
			case $listAreanum > 100:
				$listAreaSizeFilter = 3;
			break;
			case $listAreanum > 50:
				$listAreaSizeFilter = 2;
			break;
			default:
				$listAreaSizeFilter = 1;
			break;
		}
		//debt
		/*
		ปลอดจำนอง ซื้อเท่าไหร่ จ่ายเท่านั้น เป็นบ้านติดแบงค์
		ปลอดภาระผูกพัน ซื้อเท่าไหร่ จ่ายเท่านั้น เป็นบ้านไม่ติดแบงค์
		การจำนองติดไป   อันนี้ ซื้อเท่าไหร่ ยังต้องไป รวมกับยอด หนี้เก่าผ่อนต่อ
		*/
		if($row->debt == "การจำนองติดไป"){
			$classification = 'Wanted';
		}elseif($row->debt == "ปลอดการจำนอง"){
			$classification = 'Available';
		}else{
			//ปลอดภาระผูกพัน ดีสุด
			$classification = 'Sale';
		}
		
		$headline = $row->tth." ".$listAverageprice." ".$listAddress;
		$listing = new stdClass();
		$listing->id  		= $row->id;
		$listing->user_id 	= 1;
		$listing->retype	= $retype;
		$listing->subtype	= "";
		$listing->price		= $row->estimated_price;
		$listing->city		= $row->ath;
		$listing->state		= $row->pth;
		$listing->country	= "ไทย";	
		$listing->description = addslashes(buildMapDescription($row->id, false));
		$listing->bedrooms	= $listAreaSizeFilter;
		$listing->bathrooms	= "";
		$listing->relistingby = "owner";
		$listing->builtin	= "";
		$listing->resize	= $listAreanum;
		$listing->contact_name 	= $row->land_owner;
		$listing->contact_phone = "";
		$listing->contact_email = "";
		$listing->contact_website = $row->url;	
		$listing->contact_address = "";
		$listing->show_image 	= $showimage;
		$listing->pictures	= $listImage;
		$listing->ip		= "";
		$listing->dttm		= $row->first_seen;
		$listing->dttm_modified = $row->last_update;
		$listing->address	= $listAddress;
		$listing->apt		= $row->addrno;
		$listing->postal	= "";
		$listing->classification = $classification;
		$listing->headline	= $headline;
		$listing->cats		= "";
		$listing->dogs		= "";
		$listing->smoking	= "";
		$listing->useremail	= 0;
		$listing->permanent	= 1;
		$listing->latitude	= 0;
		$listing->longitude	= 0;
		$listing->listing_type 	= 1;
		$listing->listing_expire = "";
		$listing->flag			= 0;
		$listing->featured_till = '0000-00-00 00:00:00';
		$listing->addreseforedecode = $row->tumbon." ".$row->ath." จังหวัด".$row->pth." ประเทศไทย";
		$listing->listAreatext = showWnumberReport($row->size400w, $row->size100w, $row->sizew, true);
		$listing->mailReport = buildUpdateReport($row->id);
		$listings[] = $listing;
		unset($listing);
	}
	//print_r($listings);
	foreach($listings as $l){
		$select = "SELECT id FROM listing WHERE id = ".$l->id;
		$connMAP->query($select);
		$action = $connMAP->affected_rows;
		if ($action == 0){
			//new listing need do insert
			//decode location			
			$latlon = addressDecode($l->addreseforedecode);
			if($latlon['la'] <> 0 AND $latlon['lo']<> 0)
			{
				$qInsertAssetMap = 'INSERT INTO `listing`(`id`, `user_id`, `retype`, `subtype`, `price`, `city`, `state`, `country`, `description`, `bedrooms`, `bathrooms`, `relistingby`, `builtin`, `resize`, `contact_name`, `contact_phone`, `contact_email`, `contact_website`, `contact_address`, `show_image`, `pictures`, `ip`, `dttm`, `dttm_modified`, `address`, `apt`, `postal`, `classification`, `headline`, `cats`, `dogs`, `smoking`, `useremail`, `permanent`, `latitude`, `longitude`, `listing_type`, `listing_expire`, `flag`, `featured_till`) VALUES ('.$l->id.', '.$l->user_id.', "'.$l->retype.'", "'.$l->subtype.'", '.$l->price.', "'.$l->city.'", "'.$l->state.'", "'.$l->country.'", "'.$l->description.'", "'.$l->bedrooms.'", "'.$l->bathrooms.'", "'.$l->relistingby.'", "'.$l->builtin.'", '.$l->resize.', "'.$l->contact_name.'", "'.$l->contact_phone.'", "'.$l->contact_email.'", "'.$l->contact_website.'", "'.$l->contact_address.'", "'.$l->show_image.'", "'.$l->pictures.'", "'.$l->ip.'", "'.$l->dttm.'", "'.$l->dttm_modified.'", "'.$l->address.'", "'.$l->apt.'", "'.$l->postal.'", "'.$l->classification.'", "'.$l->headline.'", "'.$l->cats.'", "'.$l->dogs.'", "'.$l->smoking.'", "'.$l->useremail.'", '.$l->permanent.', '.$latlon['la'].', '.$latlon['lo'].', '.$l->listing_type.', "'.$l->listing_expire.'", '.$l->flag.', "'.$l->featured_till.'")';
				//print_r($qInsertAssetMap);
				//echo "\r\n";
				if($result = $connMAP->query($qInsertAssetMap)){
					$esttxtprice = $l->price/1000000;
					if($esttxtprice > 1){
						$esttxtprice = number_format($esttxtprice,1).'ล้าน';
					}else{
						$esttxtprice = $esttxtprice*10;
						if($esttxtprice > 1){
							$esttxtprice = number_format($esttxtprice,1).'แสน';
						}else{
							$esttxtprice = floor($esttxtprice*100).'หมึ่น';
						}
					}
					//echo "New listing update in > ".$l->state."=>".$l->city." added\r\n";
					//echo "add".$l->state."\r\n";					
					if($result === false){
						$timer_end = microtime(true);
						$execution_time = $timer_end - $timer_start;
						$notifyMail  = "ตอนนี้การอัพเดต DB->MAP มีความผิดพลาด หรือ ไม่มี หลงเหลืออยู่แล้ว กรุณาตรวจสอบ ".__FILE__." \r\n";
						$notifyMail .= "เวลาที่ใช้ไป  คือ ".$execution_time.'วินาที'; 
						notifyError($notifyMail);
						echo nl2br($notifyMail);
					}else{
						//no fail hooorayy let's notify
						//temporary disable too many update ...
						//dbug($qInsertAssetMap);
						//no more
						//notifyUpdate($l->mailReport, "New listing update in ".$l->state);
						echo nl2br($l->mailReport);
					}
				}
			}
		}elseif($action == 1){
			//old listing need do update
			//but we decite not update wate time.
		}else{
			notifyError('queqe i fail on :'.$select."\r\n\r\n\r\n ehen update asset id = ".$l->id);
		}		
	}
}else{
	echo "no have any result<br/>";
	return "false";
}
?>																			