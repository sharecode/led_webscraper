<?php
include("sharelib.php");
$connMAP = dbMAP(); // map db
$conn = db(); //asset db
function addressDecode($address){
	
	$address = urlencode($address);
	$request_url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$address."&key=AIzaSyBtPLlkp5cJ80-KK8V8lLK3y1BVbFK9nT8";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $request_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$json = json_decode(curl_exec($ch));	
	$status = $json->status;
	if ($status == "OK") {
		  $Lat = $json->results[0]->geometry->location->lat;
		  $Lon = $json->results[0]->geometry->location->lng;
	}else{
		 $Lat = 13.7;
		 $Lon = 100.7; //somewhere in thaialnad???
		 notifyError(__FILE__ ."update Map ไวไปแล้ว  ลด ครอนลง");
	}
	curl_close($ch);
	$arrayL['la'] = $Lat;
	$arrayL['lo'] = $Lon;
	return $arrayL;
}


$qMAP = "SELECT id FROM listing";
$result = $connMAP->query($qMAP);
while($row = $result->fetch_object()){
	$allListID[] = $row->id;
}

$q = 'SELECT id	FROM asset WHERE `deed_no` IS NOT NULL AND `land_owner` IS NOT NULL AND `sold` = 0';
$result = $conn->query($q);
while($row = $result->fetch_object()){
	$allAssetID[] = $row->id;
}
$TargetID = array_diff($allAssetID,$allListID);
		
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
	WHERE
	`deed_no` IS NOT NULL AND `land_owner` IS NOT NULL AND `sold` = 0 AND asset.`id` IN (' . implode(',', array_map('intval', $TargetID)) . ')
	LIMIT 10';
$result = $conn->query($q);;
if ($conn->affected_rows > 0){
	while($row = $result->fetch_object()){
		
		//find retype text
		switch ($row->tid) {
			case 1:
			$retype = 'Commercial';//LAND
			case 2:
			$retype = 'Residential';//HOME
		}
		
		//listing size
		$listArea = '';
		if($row->size400w > 0){
			$listArea .= ' '.$row->size400w.'ไร่';			
		}else{
			$listArea .= ' - ไร่';
		}
		if($row->size100w > 0){
			$listArea .= ' '.$row->size100w.'งาน';
		}else{
			$listArea .= ' - งาน';
		}
		if($row->sizew > 0){
			$listArea .= ' '.$row->sizew.'ตรว.';
		}else{
			$listArea .= ' - ตรว';
		}
		$listAreanum = $row->size400w*400 + $row->size100w*100 + $row->sizew;
		
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
		$listAverageprice = number_format(ceil($row->estimated_price/$listAreanum))."บาท/ตรว.";
		
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
		$listing->description = addslashes($listDescription);
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
		$listings[] = $listing;
		unset($listing);
	}

	
	foreach($listings as $l){
		$select = "SELECT id FROM listing WHERE id = ".$l->id;
		$connMAP->query($select);
		$action = $connMAP->affected_rows;
		if ($action == 0){
			//new listing need do insert
			//decode location			
			$latlon = addressDecode($l->addreseforedecode);
			$q = 'INSERT INTO `listing`(`id`, `user_id`, `retype`, `subtype`, `price`, `city`, `state`, `country`, `description`, `bedrooms`, `bathrooms`, `relistingby`, `builtin`, `resize`, `contact_name`, `contact_phone`, `contact_email`, `contact_website`, `contact_address`, `show_image`, `pictures`, `ip`, `dttm`, `dttm_modified`, `address`, `apt`, `postal`, `classification`, `headline`, `cats`, `dogs`, `smoking`, `useremail`, `permanent`, `latitude`, `longitude`, `listing_type`, `listing_expire`, `flag`, `featured_till`) VALUES ('.$l->id.', '.$l->user_id.', "'.$l->retype.'", "'.$l->subtype.'", '.$l->price.', "'.$l->city.'", "'.$l->state.'", "'.$l->country.'", "'.$l->description.'", "'.$l->bedrooms.'", "'.$l->bathrooms.'", "'.$l->relistingby.'", "'.$l->builtin.'", '.$l->resize.', "'.$l->contact_name.'", "'.$l->contact_phone.'", "'.$l->contact_email.'", "'.$l->contact_website.'", "'.$l->contact_address.'", "'.$l->show_image.'", "'.$l->pictures.'", "'.$l->ip.'", "'.$l->dttm.'", "'.$l->dttm_modified.'", "'.$l->address.'", "'.$l->apt.'", "'.$l->postal.'", "'.$l->classification.'", "'.$l->headline.'", "'.$l->cats.'", "'.$l->dogs.'", "'.$l->smoking.'", "'.$l->useremail.'", '.$l->permanent.', '.$latlon['la'].', '.$latlon['lo'].', '.$l->listing_type.', "'.$l->listing_expire.'", '.$l->flag.', "'.$l->featured_till.'")';
			//echo $q;
			$connMAP->query($q);
		}elseif($action == 1){
			//old listing need do update
			//but we decite not update wate time.
		}else{
			notifyError('queqe i fail on :'.$select."\r\n\r\n\r\n ehen update asset id = ".$l->id);
		}		
	}
}else{
	return "false";
}
?>																			