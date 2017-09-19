<?php
//all show error
error_reporting( E_ALL );
date_default_timezone_set("Asia/Bangkok");
setlocale(LC_ALL, "th_TH.874");

// Check connection
function db() {
    static $conn;
    $host = '127.0.0.1';
    $db_name = 'addinth_c';
    $user = 'addinth_c';
    $pass = '9hcc1pPxXlLiLiiLlTa';	
    if ($conn===NULL){
        $conn = mysqli_connect ($host, $user, $pass, $db_name);	
    }
        
    if ($conn->connect_error){
	die("Connection failed: " . $conn->connect_error);
    }else{
	$conn->set_charset("utf8");
	return $conn;
    }
    
    return null;	
}

function dbMAP() {
    static $connMAP;
    $host = '127.0.0.1';
    $db_name="addinth_map";
    $user="addinth_map";
    $pass="mX+a4,5g;d{d";
	
    if ($connMAP===NULL){
        $connMAP = mysqli_connect ($host, $user, $pass, $db_name);	
    }
        
    if ($connMAP->connect_error){
	die("Connection failed: " . $connMAP->connect_error);
    }else{
	$connMAP->set_charset("utf8");
	return $connMAP;
    }
    
    return null;	
}

function sprintf_array($format, $arr){ 
    return call_user_func_array('sprintf', array_merge((array)$format, $arr)); 
} 

//take time and collect
function dailyError($data, $subject =''){
	return dailyUpdateMail($data, 'error', $subject);
}

function dailyUpdate($data, $subject = ''){
	return dailyUpdateMail($data, 'update', $subject);
}

//instant mail
function nowError($data, $subject =''){
	return notifyError($data, $subject);
}

function nowUpdate($data, $subject = ''){
	return notifyUpdate($data, $subject);
}

function dailyUpdateMail($data = '', $type = 'update', $subject = ''){
	//$type = update or error
	if(!empty($data)){
		//int value
		$currentTime = date("d/m/Y H:i:s");
		$fileCaller = '';
		$debugBacktrace  = debug_backtrace();
		if(isset($debugBacktrace[1]['file'])){
			$arrPath = explode('/', $debugBacktrace[1]['file']);
			$currentFileCalls = array_pop($arrPath);
			$arrCurrentFileCall = explode('.', $currentFileCalls);
			$currentFileCall = strtolower($arrCurrentFileCall[0]);			
		}else{
			$currentFileCall = 'undefine';
		}
		if($type == 'update'){
			$type = 'update';
			$filename = 'notifyq/'.$type.'-'.$currentFileCall.'.log';
			$timeLimit  = strtotime('+300 minutes');
			$filesizeLimit = 10000; //10k
		}else{ //error
			$type = 'error';
			$filename = 'notifyq/'.$type.'-'.$currentFileCall.'.log';
			$timeLimit  = strtotime('+300 minutes');
			$filesizeLimit = 10000; //10k
		}

		//prepare data
		//$data  = nl2br($data); watching result!!!
		if(empty($subject)){
			$tdTop = '<td style="vertical-align: top;background: #fff;padding: 5px;" rowspan="2">'.$data.'</td>';
			$rowBottom = '<tr><td></td></tr>';
		}else{
			$tdTop = '<td align="left" style="border:1px #ccc solid;padding: 5px;">'.$subject.'</td>';
			$rowBottom = '<tr><td></td><td style="vertical-align: top;background: #fff;padding: 5px;">'.$data.'</td></tr>';
		}
		$timesAndSubject = '<tr><td style="border: #ccc solid 1px;background: orange;width:144px;text-align:center;padding: 5px;">'.$currentTime.'</td>'.$tdTop.'</tr>';
		$data  = $timesAndSubject.$rowBottom;
		$data  = '<table style="width:100%;border:1px #ccc solid;background-color:#eee;">'.$data.'</table>';
		$data  = $data. PHP_EOL;

		//save data to file
		file_put_contents($filename, $data, FILE_APPEND);
		$filesize = filesize($filename);

		//get data from filename and fire mail	
		if(filectime($filename) > $timeLimit OR $filesize > $filesizeLimit AND $filesize > 0){
			$notifyData = file_get_contents($filename);
			//$notifyData = nl2br($notifyData); watching result!!!
			if($type == 'update'){
				/*DO NOT CHANGE THIS*/ 
				notifyUpdate($notifyData, '󾆶'.$currentFileCall.' '.$type); //don't false change dailyUpdate -> notifyUpdate lol
			}else{
				/*DO NOT CHANGE THIS*/ 
				notifyError($notifyData, '󾟹'.$currentFileCall.' '.$type); //don't false change dailyError -> notifyError lol warn again
			}
			if (file_exists($filename) && $filename <> '') {
				$newName = $filename.'.old.'.time();
				rename($filename, $newName);
			}else{
				/*DO NOT CHANGE THIS*/ 
				notifyError('critical Error'.$filename.' is missing'); //don't false change dailyError -> notifyError lol warn again and again
			}
		}
		return true;
	}else{
		return false;
	}	
}

function newObjAsset(){
	$objAsset = new stdClass();
	$objAsset->id = NULL;
	$objAsset->pageid =  NULL;
	$objAsset->page_id =  NULL;
	$objAsset->url =  NULL;
	$objAsset->sale_order_main =  NULL;
	$objAsset->sale_order_sub =  NULL;
	$objAsset->law_suit_no =  NULL;
	$objAsset->law_suit_year =  NULL;
	$objAsset->type_id =  NULL;
	$objAsset->size400w =  NULL;
	$objAsset->size100w =  NULL;
	$objAsset->sizew =  NULL;
	$objAsset->estimated_price = NULL;
	$objAsset->tumbon =  NULL;
	$objAsset->amphur_id =  NULL;
	$objAsset->deed_no =  NULL;
	$objAsset->land_owner =  NULL;
	$objAsset->addrno =  NULL;
	$objAsset->cort =  NULL;
	$objAsset->bond =  NULL;
	$objAsset->conditions =  NULL;
	$objAsset->date1 =  NULL;
	$objAsset->date1_status =  NULL;
	$objAsset->date2 =  NULL;
	$objAsset->date2_status =  NULL;
	$objAsset->date3 =  NULL;
	$objAsset->date3_status =  NULL;
	$objAsset->date4 =  NULL;
	$objAsset->date4_status =  NULL;
	$objAsset->date5 =  NULL;
	$objAsset->date5_status =  NULL;
	$objAsset->date6 =  NULL;
	$objAsset->date6_status =  NULL;
	$objAsset->date7 =  NULL;
	$objAsset->date7_status =  NULL;
	$objAsset->date8 =  NULL;
	$objAsset->date8_status =  NULL;
	$objAsset->debt =  NULL;
	$objAsset->price1 =  NULL;
	$objAsset->price2 =  NULL;
	$objAsset->price3 =  NULL;
	$objAsset->price4 =  NULL;
	$objAsset->price5 =  NULL;
	$objAsset->publish =  NULL;
	$objAsset->kadenee =  NULL;
	$objAsset->jod =  NULL;
	$objAsset->jay =  NULL;
	$objAsset->law_owner =  NULL;
	$objAsset->image1 =  NULL;
	$objAsset->image2 =  NULL;
	$objAsset->map =  NULL;
	$objAsset->first_seen =  NULL;
	$objAsset->last_seen =  NULL;
	$objAsset->last_update =  NULL;
	$objAsset->next_update =  NULL;
	$objAsset->sold =  NULL;
	$objAsset->enable =  NULL;
	//extra property
	$objAsset->tid =  NULL;
	$objAsset->ten =  NULL;
	$objAsset->tth =  NULL;
	$objAsset->tencode =  NULL;
	$objAsset->aid =  NULL;
	$objAsset->aen =  NULL;
	$objAsset->ath =  NULL;
	$objAsset->aencode =  NULL;
	$objAsset->pid =  NULL;
	$objAsset->pen =  NULL;
	$objAsset->pth =  NULL;
	$objAsset->pencode =  NULL;
	return $objAsset;
}

function getRemainAssetUpdate(){
	$conn = db();
	$dbNow = date("Y-m-d H:i:s");
	$q = 'SELECT COUNT(*) as pagecount
		FROM asset
		WHERE `asset`.`enable` = 1 AND (`asset`.`next_update` < "'.$dbNow.'" OR `asset`.`jod` IS NULL OR  `asset`.`jod` = "" OR `asset`.`publish` = "1970-01-01")';
		//echo $q;
	$result = $conn->query($q);
	if ($conn->affected_rows > 0){
		$row = $result->fetch_object();
		return $row->pagecount;
	}else{
		return 0;
	}
}

function getHardOrder($html){
	$html = explode('-',$html);
	if(isset($html[0]) AND isset($html[1])){
		$orderMain 	= substr($html[0], -3); //max 3 digit
		$order[] 	= getHardInt($orderMain);
		$orderSub  	= substr($html[1], 0, 3);//max 3 digit
		$order[]  	= getHardInt($orderSub);
	}else{
		$order[0] = 0;
		$order[1] = 0;
	}	
	return $order; //(main, sub)
}

function removeAssetFromDB($link = ''){
	$conn = db();
	$now = date("Y-m-d H:i:s");
	if($link->id > 0 AND $link->id <> ''){
		$deleteBadLink = 'UPDATE `asset` SET enable = 0, next_update="2018-01-01 00:00:00", last_seen="'.$now.'", last_update="'.$now.'" WHERE id = '.$link->id;
		//echo '<b>$deleteBadLink:</b> '.$deleteBadLink."<br/>";
		$result = $conn->query($deleteBadLink);
		$badlink = str_replace('1038529402.rsc.cdn77.org', 'asset.led.go.th', $link->url);
		$txtError = 'Disabled bad link from database url = <a href="'.$badlink.'">'.$badlink.'</a>';
		dailyError($txtError, 'Recheck! before permanent delete '.date("d/m/Y"));
		echo '<b>'.$txtError."</b><br/>";
		return true;
	}else{
		return false;
	}	
}

function buildUpdateQuery($table, $arrUpdate, $where){
	if(!empty($arrUpdate)){
		$updateQ  = 'UPDATE '.$table. PHP_EOL;
		$updateQ .= "SET ". PHP_EOL;

		foreach($arrUpdate as  $f){
			$key		= $f['update'];
			$value	= $f['newvalue'];
			$type	= $f['type'];
			switch($type){
				case 'text':
				case    't':
				case 'date':
				case    'd':
				case 'datetime':
				case   'dt':
				case 'time':
					$value = '"'.$value.'"';
					break;
				default:
					$value = int($value);
			}
			if($key <> ''){
				$q[] = ' '.$key.' = '.$value.' ';
			}			
		}		
		$updateQ .= implode(", ", $q);		
		$updateQ .= PHP_EOL .$where;
	}else{
		$updateQ = false;
	}
	return $updateQ;
}

function buildMapDescription($assetId, $image = false){	
	return buildUpdateReport($assetId, false);
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
				//wait 2 secound and do again
				//sleep(2);
				 $arrayL = addressDecode($address);
				 $Lat = $arrayL['la'];
				 $Lon = $arrayL['lo'];
			 }else{
				 $Lat = 0;
				 $Lon = 0;
				 $notifyError = __FILE__ ." update Map ไวไปแล้ว  ลด ครอนลง".PHP_EOL;
				 $notifyError .= "ที่อยูที่ไม่สามารถ ถอดรหัส คือ ".$address . PHP_EOL;
				 dailyError($notifyError, "ตรวจพบ การถอดรกัส พิกัดผิดพลาด");
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

function updateMap($arrAssetId){
	$now = date("Y-m-d H:i:s");
	$conn = db(); //asset db
	$connMAP = dbMAP(); //map db
	if(sizeof($arrAssetId) > 1){
		$assetId =  implode(',', array_map('intval', $arrAssetId));
		$assetId = ' IN ('. $assetId .')';
	}elseif(sizeof($arrAssetId) == 1 && !is_array($arrAssetId)){
		$assetId = ' = '.intval($arrAssetId);
	}elseif(sizeof($arrAssetId) == 1 && is_array($arrAssetId)){
		$assetId = ' = '.intval($arrAssetId[0]);
	}else{
		return false;
	}
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
		WHERE asset.`id` ' .$assetId;
	//echo $q.'<br/>';
	$result = $conn->query($q);
	if ($conn->affected_rows > 0){
		while($row = $result->fetch_object()){
			//find retype text
			switch ($row->tid) {
				case 1:
					$retype = 'land';//LAND
				break;
				case 2:
					$retype = 'home';//HOME
				break;
				case 3:
					$retype = 'condo';//CONDO
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
				$listAverageprice_num = $row->estimated_price/$listAreanum;
				$listAverageprice = number_format(ceil($row->estimated_price/$listAreanum))."บาท/ตรว.";
			}else{
				$listAverageprice = 0;
                $listAverageprice_num = 0;
			}
			
			$listDescription  = 'ทรัพย์ประเภท'.$row->tth." ".$listAverageprice . PHP_EOL;
			$listDescription .= "ขนาดพื้นที่".$listArea . PHP_EOL;
			$listDescription .= "เลขคดี ".$row->law_suit_no."/".$row->law_suit_year . PHP_EOL;
			$listDescription .= $row->conditions;
			$listDescription = nl2br($listDescription);
			
			//arear filter		
			$listAreaSizeFilter = 1;
			switch ($listAreanum){
				case $listAreanum > 8000:
					$listAreaSizeFilter = 6; //20rai
				break;
				case $listAreanum > 4000:
					$listAreaSizeFilter = 5;//10rai
				break;
				case $listAreanum > 800:
					$listAreaSizeFilter = 4;//2rai
				break;
				case $listAreanum > 150:
					$listAreaSizeFilter = 3;//150w
				break;
				case $listAreanum > 50:
					$listAreaSizeFilter = 2;//50w
				break;
				default:
					$listAreaSizeFilter = 1;
				break;
			}

			$listAreaCostFilter = 1;
			switch ($listAverageprice_num){
				case $listAverageprice_num > 9000:
					$listAreaCostFilter = 6; //90,000b/w++
				break;
				case $listAverageprice_num > 3600:
					$listAreaCostFilter = 5;//36,000-90,000b/w
				break;
				case $listAverageprice_num > 7200:
					$listAreaCostFilter = 4;//7,200-36,000b/w
				break;
				case $listAverageprice_num > 1200:
					$listAreaCostFilter = 3;//1,200-7,200b/w
				break;
				case $listAverageprice_num > 200:
					$listAreaCostFilter = 2;//200-1,200b/w
				break;
				default:
					$listAreaCostFilter = 1; //น้อยกว่า 200b/w
				break;
			}
			//debt
			/*
			ปลอดจำนอง ซื้อเท่าไหร่ จ่ายเท่านั้น เป็นบ้านติดแบงค์
			ปลอดภาระผูกพัน ซื้อเท่าไหร่ จ่ายเท่านั้น เป็นบ้านไม่ติดแบงค์
			การจำนองติดไป   ราคาซื้อ + ยอดหนี้เก่า
			*/
			if($row->debt == "การจำนองติดไป"){
				$classification = 'Wanted';
			}elseif($row->debt == "ปลอดการจำนอง"){
				$classification = 'Available';
			}else{
				//ปลอดภาระผูกพัน ดีสุด
				$classification = 'Sale';
			}
			$headline = $row->tth." ".$listAverageprice;
			$listing = newObjAsset();
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
			$listing->bathrooms	= $listAreaCostFilter;
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
			$listing->dttm	= $row->next_update;
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
			$listing->listing_expire = "permanent";
			$listing->flag			= 0;
			$listing->featured_till = '0000-00-00 00:00:00';
			$listing->listing_type 	= 1;
			$lumsumsale = substr_count($listing->description, 'ขายรวม');
			
			if(!empty($row->nkadenee)){
				$listing->featured_till = $listing->dttm;
				$listing->listing_type 	= 2;
				$listing->headline	= 'ประมูลพิเศษ '.$listing->headline;
			}
			
			if($lumsumsale > 0){
				$listing->featured_till = $listing->dttm;
				$listing->listing_type 	= 2;
				$listing->headline	= 'ขายรวม'.$listing->headline;
			}
			
			if($now < $row->date1 && $now < $row->date2 && $now < $row->date3 && $now < $row->date4 && $now< $row->date5 && $now < $row->date6){
				$listing->headline = $listing->headline. ' นัด1 '.$row->cort.' '.date("d/m/Y",strtotime($row->date1));
			}elseif($now < $row->date2 && $now < $row->date3 && $now < $row->date4 && $now< $row->date5 && $now < $row->date6){
				$listing->headline = $listing->headline. ' นัด2 '.$row->cort.' '.date("d/m/Y",strtotime($row->date2));
			}elseif($now < $row->date3 && $now < $row->date4 && $now< $row->date5 && $now < $row->date6){
				$listing->headline = $listing->headline. ' นัด3 '.$row->cort.' '.date("d/m/Y",strtotime($row->date3));
			}elseif($now < $row->date4 && $now< $row->date5 && $now < $row->date6){
				$listing->headline = $listing->headline. ' นัด4 '.$row->cort.' '.date("d/m/Y",strtotime($row->date4));
			}elseif($now< $row->date5 && $now < $row->date6){
				$listing->headline = $listing->headline. ' นัด5 '.$row->cort.' '.date("d/m/Y",strtotime($row->date5));
				if(empty($row->date7)){
					$listing->listing_type 	= 2;
					$listing->featured_till = date("Y-m-d H:i:s",strtotime($row->date6));
				}
			}elseif($now < $row->date6){
				$listing->headline = $listing->headline. ' นัด6 '.$row->cort.' '.date("d/m/Y",strtotime($row->date6));
			}elseif($now < $row->date7){
				$listing->headline = $listing->headline. ' นัด7'.$row->cort.' '.date("d/m/Y",strtotime($row->date7));
				$listing->listing_type 	= 2;
				$listing->featured_till = date("Y-m-d H:i:s",strtotime($row->date7));
			}
			
			$listing->addreseforedecode = $row->tumbon." ".$row->ath." จังหวัด".$row->pth." ประเทศไทย";
			$listing->listAreatext = showWnumberReport($row->size400w, $row->size100w, $row->sizew, true);
			$listing->mailReport = buildUpdateReport($row->id);
			$listings[] = $listing;
			unset($listing);
		}
		//print_r($listings);
		$updateCountNum  = 0;
		$updateEcho = '';
		$newEcho = '';
		$newCountNum  = 0;
		$numbering = '';
		$updateNums = sizeof($listings);
		foreach($listings as $l){
			$qMAP = "SELECT latitude,longitude,dttm,dttm_modified FROM listing WHERE id = ".$l->id;
			$result = $connMAP->query($qMAP);
			if ($connMAP->affected_rows == 1){
				$row = $result->fetch_object();
				if($row->latitude <> 0 AND $row->longitude<> 0){
					$latlon['la'] = $row->latitude; //get lat lon from db
					$latlon['lo'] = $row->longitude;
					$old_dttm =  'Last update is '.date("d/m/Y H:i:s",strtotime($row->dttm_modified));
				}
			}else{
				$old_dttm =  'This is new map ';
				$latlon = addressDecode($l->addreseforedecode); //do real geodecode
			}
			//incase update
			//almost map need update
			$qUpdateMap = 'UPDATE listing SET  
				`retype` = "'.$l->retype.'",
				`price` =  '.$l->price.',
				`city` = "'.$l->city.'", 
				`state` =  "'.$l->state.'", 
				`description` =  "'.$l->description.'",
				`relistingby`= "'.$l->relistingby.'",
				`resize`= '.$l->resize.',
				`bedrooms`= '.$l->bedrooms.',
				`bathrooms`= '.$l->bathrooms.',
				`listing_type`= '.$l->listing_type.',
				`contact_name`= "'.$l->contact_name.'", 
				`contact_phone`=  "'.$l->contact_phone.'", 
				`contact_website`= "'.$l->contact_website.'",
				`show_image`= "'.$l->show_image.'",
				`pictures`= "'.$l->pictures.'", 
				`dttm`= "'.$l->dttm.'",
				`dttm_modified`=  "'.$l->dttm_modified.'", 
				`address`=  "'.$l->address.'", 
				`apt`= "'.$l->apt.'", 
				`classification`=  "'.$l->classification.'",
				`featured_till`=  "'.$l->featured_till.'",
				`headline`=  "'.$l->headline.'"
				WHERE `id` = '.$l->id;
			//echo $qUpdateMap;
			$connMAP->query($qUpdateMap);
			if($connMAP->affected_rows > 0){
				//sucess update map
				//echo 'do update';
				$updateCountNum++;
				if($updateNums > 1){
					$numbering = str_pad($updateCountNum, 3, 0, STR_PAD_LEFT);
				}
				$updateEcho = '<a style="display:inline-block;width: 140px;" href="http://example.com/map/index.php?ptype=viewFullListing&reid='.$l->id.'" target="_blank"> Map id : '. $l->id.' </a> '.$old_dttm.' > next update is '.date("d/m/Y H:i:s", strtotime($l->dttm));
				$updateEcho .= '<span style="display:inline-block;width: 400px;">'.$numbering. '&nbsp;Updated '.$l->state."=>".$l->city."</span>";
			}else{
				//fail update asset assume is new map 
				//we are testing on next query
				/*if(!empty($connMAP->error)){
					printf("Error update map: %s<br/>", $connMAP->error);
				}*/
				//try add map
				$qNewMap = 'INSERT INTO `listing` 
					(`id`, `user_id`, `retype`, `subtype`, `price`, `city`, `state`, `country`, `description`, `bedrooms`, `bathrooms`, `relistingby`, `builtin`, `resize`, `contact_name`, `contact_phone`, `contact_email`, `contact_website`, `contact_address`, `show_image`, `pictures`, `ip`, `dttm`, `dttm_modified`, `address`, `apt`, `postal`, `classification`, `headline`, `cats`, `dogs`, `smoking`, `useremail`, `permanent`, `latitude`, `longitude`, `listing_type`, `listing_expire`, `flag`, `featured_till`) VALUES 
					('.$l->id.', '.$l->user_id.', "'.$l->retype.'", "'.$l->subtype.'", '.$l->price.', "'.$l->city.'", "'.$l->state.'", "'.$l->country.'", "'.$l->description.'", "'.$l->bedrooms.'", "'.$l->bathrooms.'", "'.$l->relistingby.'", "'.$l->builtin.'", '.$l->resize.', "'.$l->contact_name.'", "'.$l->contact_phone.'", "'.$l->contact_email.'", "'.$l->contact_website.'", "'.$l->contact_address.'", "'.$l->show_image.'", "'.$l->pictures.'", "'.$l->ip.'", "'.$l->dttm.'", "'.$l->dttm_modified.'", "'.$l->address.'", "'.$l->apt.'", "'.$l->postal.'", "'.$l->classification.'", "'.$l->headline.'", "'.$l->cats.'", "'.$l->dogs.'", "'.$l->smoking.'", "'.$l->useremail.'", '.$l->permanent.', '.$latlon['la'].', '.$latlon['lo'].', '.$l->listing_type.', "'.$l->listing_expire.'", '.$l->flag.', "'.$l->featured_till.'")';
				if($connMAP->query($qNewMap)){
					//echo 'do new';
					$newCountNum++;
					if($updateNums > 1){
						$numbering = str_pad($newCountNum,3,0,STR_PAD_LEFT);
					}
					$newEcho  = '<a style="display:inline-block;width: 140px;" href="http://example.com/map/index.php?ptype=viewFullListing&reid='.$l->id.'" target="_blank"> Map id : '. $l->id.' </a> '.$old_dttm.' > next update is '.date("d/m/Y H:i:s", strtotime($l->dttm));					
					$newEcho .= '<span style="display:inline-block;width: 400px;">'.$numbering. '&nbsp;new map!! '.$l->state."=>".$l->city."</span>";
				}else{
					//fail again
					if(!empty($connMAP->error)){
						printf("Error add new map: %s<br/>", $connMAP->error);
					}
				}
			}
		}
		echo $updateEcho;
		echo $newEcho;
		if(($updateCountNum + $newCountNum) > 0){
			//do cache file time stamp
			$file = 'maplastupdate.txt';
			$timestamp = date("Y-m-d H:i:s");
			file_put_contents($file, $timestamp);	
		}
	}else{
		echo "no have any result asset.`id` " .$assetId."<br/>";
		return false;
	}
} //end function

	function showPOIasset($objAsset){
	if(is_object($objAsset)){
		$assetData  = '<table id="poi">';
		$assetData .= '<tr><td><a href="http://example.com/map/index.php?ptype=viewFullListing&reid='.$objAsset->id.'" target="_blank">ID#'.$objAsset->id."</a></td></tr>";
		$assetData .= '<tr><td>'.$objAsset->tth."  ".$objAsset->tumbon."/".$objAsset->ath."/".$objAsset->pth."</td></tr>";
		$assetData .= '<tr><td>'.showWnumberReport($objAsset->size400w, $objAsset->size100w, $objAsset->sizew).'('.number_format($objAsset->estimated_price/showWnumberReport($objAsset->size400w, $objAsset->size100w, $objAsset->sizew, false)).'บาท/ตรว.) </td></tr>';
		$assetData .= '<tr><td> ราคา '.showSimplePrice($objAsset->estimated_price)." โดย ".$objAsset->debt."</td></tr>";
		if(!empty($objAsset->image1)){
			$img = $objAsset->image1;
		}elseif(!empty($objAsset->image2)){
			$img = $objAsset->image2;
		}elseif(!empty($objAsset->map)){
			$img = $objAsset->map;
		}
		if(isset($img)){
			$assetData .= '<tr><td><img src="'.$img.'" width="400px"></td></tr>';
		}
		$assetData  .= "</table>";
	}else{
		$assetData = null;
	}
	return $assetData;
	}

function checkIdExist($id){
	$conn = db();
	$id = intval($id);
	$result = $conn->query("SELECT 1 FROM asset WHERE id=".$id." LIMIT 1");
	//Return Values
	//Returns FALSE on failure. 
	//For successful SELECT, SHOW, DESCRIBE or EXPLAIN queries mysqli_query() will return a mysqli_result object. 
	//For other successful queries mysqli_query() will return TRUE.
	if ($conn->affected_rows == 1){
		return true;
	} else {
		return false;
	}
}


function checkIdNotExist($id){
	return !checkIdExist($id);
}

function reserveAssetId($id){
    $id = intval($id);
    if($id > 0){
        $conn = db();	
        $conn->query('INSERT INTO asset (`id`) VALUES ('.$id.');');	
    }	
}

function validLedLink($txtURL = ''){
	//validate link
	//http://asset.led.go.th/newbid/asset_open.asp?law_suit_no=%bc%ba.6355&law_suit_year=2557&deed_no=989&addrno=-
	$countParamURLmain 	= substr_count($txtURL, 'asset.led.go.th');
	$countParamURLcdn 	= substr_count($txtURL, '1038529402.rsc.cdn77.org');
	$countParamLSN 		= substr_count($txtURL, 'law_suit_no');
	$countParamLSY 		= substr_count($txtURL, 'law_suit_year');
    $countParamDeedno 	= substr_count($txtURL, 'deed_no');    
	$countParamAddrno 	= substr_count($txtURL, 'addrno');
	$validurl = $countParamURLmain + $countParamURLcdn; //1
	if(($countParamLSN ==1) AND ($countParamLSY == 1) AND ($countParamDeedno == 1) AND ($countParamAddrno == 1) AND ($validurl == 1)){
		return true;
	}else{
		return false;
	}
}

function validLedId($txt){
	//id is numeric
	return is_numeric($txt);
}

function check_url($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);

    return $headers['http_code'];
}

function dbug($data = ''){
	$start_var = 'Inspecting:';
	
	$return  = "=[[".$start_var."]]";
	$lenghtStart = 80 - strlen($return);
	$dash1 = '';
	for($i = 0; $i < $lenghtStart; $i++){
		$dash1 .= '=';
	}
	$return  = "=[[".$start_var."]]".$dash1. PHP_EOL;
	ob_start();
	print_r($data);
	$return .= ob_get_contents();
	ob_end_clean();
	$end_var  = "=[[end]]=";
	$lenghtEnd = 80 - strlen($end_var);
	$dash2 = '';
	for($i = 0; $i < $lenghtEnd; $i++){
		$dash2 .= '=';
	}
	$return .= PHP_EOL .$end_var."".$dash2. PHP_EOL;
	echo $return;
}

function printr($data = ''){
	echo PHP_EOL .'<pre>';
    print_r($data);
    echo '</pre>'. PHP_EOL;
}


function makeFolder($dirName, $rights = 0755){
    $dirs = explode('/', $dirName);
    $dir='';
    foreach ($dirs as $part) {
        $dir.=$part.'/';
        if (!is_dir($dir) && strlen($dir)>0){
			mkdir($dir, $rights);
		}  
    }
	return is_dir($dirName)?true:false;
}

function between($src, $start = '', $end = '' ,$startIndex = 1){
	if($start == '' OR $end == ''){
		$return = $src;
	}else{
		$txt = explode($start,$src);
		if(isset($txt[$startIndex]) and !empty($txt[$startIndex])){
			$txt2 = explode($end,$txt[$startIndex]);
		}else{
			$return = '';
		}
		if(isset($txt2[0])){
			$return = trim($txt2[0]);
		}else{
			$return = '';
		}		
	}
	return $return;
}
  
function file_get_contents_curl($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
	if(rand(1,10) > 5){
		curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
	}else{
		curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36');
	}
    $cookiefile = 'tmp/cookie.txt';
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiefile);
    curl_setopt($ch, CURLOPT_ENCODING , "gzip");
    //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $data = curl_exec($ch); 
    //$content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch); 
    return $data;    
}

function stripInvisible($stringHtml){
	$stringHtml = trim(preg_replace('/\s+/', ' ', $stringHtml));
	return trim(preg_replace("/&#?[a-z0-9]{2,8};/i","",$stringHtml)); 	
}

function domGetPageResult($objHtml){
	$pageResult = $objHtml->find('/html/body/table[3]/tbody/tr/td[1]/table[2]/tbody/tr/td[2]',0)->outertext;
	$pageResult = stripInvisible(between($pageResult,'1/','</div>'));
	if(is_null($pageResult) OR !is_int($pageResult)){
		return false;
	}else{
		return $pageResult;
	}
}

function hardCheckEmptyPage($txtHtml){
	$verifytext1 =  iconv("utf8","windows-874","ไม่พบข้อมูล");
	$verifytext2 =  "ไม่พบข้อมูล";
	
	$start = '<!-- BEGIN TABLE_ROW_TEMPLATE -->';
	$end = '<!-- END TABLE_ROW_TEMPLATE -->';
	$someTxt = between($txtHtml,$start,$end);
	
	//doLog($someTxt);
	$start = '#0000FF>';
	$end = '</font>';
	$resultTxt = between($someTxt,$start,$end);
	
	$pos1 = strpos($verifytext1, $someTxt);
	$pos2 = strpos($verifytext2, $someTxt);
	$pos3 = strpos($verifytext1, $resultTxt);
	$pos4 = strpos($verifytext2, $resultTxt);
	if ($pos1 === false AND $pos2 === false AND $pos3 === false AND $pos4 === false){
		//not found text
		return false;
	}else{
		return true;
	}
}

/*function hardGetNumResult($txtHtml){
	$start = '<font color="#FFFF99">';
	$end = '</font>';
	$numResult = between($txtHtml,$start,$end);
	$numResult = stripInvisible($numResult);
	$numResult = intval($numResult); 
	if($numResult > 600){$numResult = 0;}
	return $numResult;
}*/
function hardGetNumResult($txtHtml){
	$inEncoding = 'windows-874';
	$startword = iconv('utf-8', $inEncoding, 'พบ');
	$endword = iconv('utf-8', $inEncoding, 'รายการ');
	$numResult = BetweenWord($txtHtml, $startword , $endword, 0, 0, 'R', 'L');	
	$numResult = str_replace("&nbsp;",'',$numResult);
	$numResult = str_replace(' color="#0000FF"','',$numResult);
	$numResult = str_replace(' color="#FFFF99"','',$numResult);
	$numResult = stripAndTrim($numResult);
	return $numResult;
}

function domGetNumResult($objHtml){
	$numResult = $objHtml->find('/html/body/table[3]/tbody/tr/td[1]/table[2]/tbody/tr/td[2]/div/font',0)->plaintext;
	$numResult = stripInvisible($numResult);
	if(is_int($numResult)){
		return $numResult;
	}else{
		return false;
	}
}
	
function domGetProvinceTh($objHtml){
	$province = $objHtml->find('/html/body/table[3]/tbody/tr/td[2]/form/table/tbody/tr[2]/td[2]/table[1]/tbody/tr[6]/td[2]/p/font',0)->plaintext;
	$province = stripInvisible($province);
	if(strlen($province)){
		return $province;
	}else{
		return false;
	}
}

function updateAsset($link, $newData){
	$conn = db();
	if($newData === 0){
		//echo 'web site offline or server down we need wait abount 60min for next update<br/>';
		$q = 'UPDATE asset SET next_update =  ADDTIME(next_update, "1:00:00")';
		$conn->query($q);
		//echo $q.'<br/>';
	}else{
		$anyUpdate = 0;
		$newData->id = genAssetID($newData->url);
        //check id exist
        if(!checkIdExist($newData->id)){
            //add new id
            reserveAssetId($newData->id);
        }
		//echo $newData->id;
		$return = 0;
		$currentDatetime 	= date("Y-m-d H:i:s");
		$updateNexttime 	= date("Y-m-d H:i:s", strtotime(getNextUpdate($newData, $link->last_update)));
		//init value 
		//this value will change that match conditions
		$newData->first_seen 	= $currentDatetime;
		$newData->last_update 	= $currentDatetime;
		$newData->last_seen 		= $currentDatetime;
		$newData->next_update  = $updateNexttime;
		//saveAssetDB
		$updateQ[] = q('id', 					$conn->real_escape_string($link->id), 				$conn->real_escape_string($newData->id), 		'n');
		$updateQ[] = q('page_id', 			$conn->real_escape_string($link->page_id), 		$conn->real_escape_string($newData->page_id), 	'n');
		$updateQ[] = q('url', 					$conn->real_escape_string($link->url), 				$conn->real_escape_string($newData->url), 		't');
		$updateQ[] = q('sale_order_main',$conn->real_escape_string($link->sale_order_main), 	$conn->real_escape_string($newData->sale_order_main),	'n');
		$updateQ[] = q('sale_order_sub', 	$conn->real_escape_string($link->sale_order_sub), 		$conn->real_escape_string($newData->sale_order_sub),	'n'); 
		$updateQ[] = q('law_suit_no', 		$conn->real_escape_string($link->law_suit_no), $conn->real_escape_string($newData->law_suit_no),		't'); 
		$updateQ[] = q('law_suit_year', 	$conn->real_escape_string($link->law_suit_year), 			$conn->real_escape_string($newData->law_suit_year),	'n'); 
		$updateQ[] = q('type_id', 			$conn->real_escape_string($link->type_id), 		$conn->real_escape_string($newData->type_id), 	'n'); 
		$updateQ[] = q('size400w', 			$conn->real_escape_string($link->size400w), 	$conn->real_escape_string($newData->size400w), 'n'); 
		$updateQ[] = q('size100w', 			$conn->real_escape_string($link->size100w), 	$conn->real_escape_string($newData->size100w), 'n'); 
		$updateQ[] = q('sizew',				$conn->real_escape_string($link->sizew), 		$conn->real_escape_string($newData->sizew), 	'n'); 
		$updateQ[] = q('estimated_price',$conn->real_escape_string($link->estimated_price), $conn->real_escape_string($newData->estimated_price),	'n');		
		$updateQ[] = q('tumbon', 			$conn->real_escape_string($link->tumbon), 		$conn->real_escape_string($newData->tumbon), 	't');
		$updateQ[] = q('amphur_id', 		$conn->real_escape_string($link->amphur_id), 	$conn->real_escape_string($newData->amphur_id),'n');
		$updateQ[] = q('deed_no', 			$conn->real_escape_string($link->deed_no), 	$conn->real_escape_string($newData->deed_no), 	't');
		$updateQ[] = q('land_owner', 		$conn->real_escape_string($link->land_owner), $conn->real_escape_string($newData->land_owner),'t');
		$updateQ[] = q('addrno', 			$conn->real_escape_string($link->addrno), 		$conn->real_escape_string($newData->addrno), 	't');
		$updateQ[] = q('cort', 				$conn->real_escape_string($link->cort), 			$conn->real_escape_string($newData->cort), 	't');
		$updateQ[] = q('bond', 				$conn->real_escape_string($link->bond), 			$conn->real_escape_string($newData->bond), 	'n');
		$updateQ[] = q('conditions', 		$conn->real_escape_string($link->conditions), 		$conn->real_escape_string($newData->conditions),'t');
		$updateQ[] = q('date1', 				$conn->real_escape_string($link->date1), 			$conn->real_escape_string($newData->date1), 	'd');
		$updateQ[] = q('date1_status', 	$conn->real_escape_string($link->date1_status), 	$conn->real_escape_string($newData->date1_status),'t');
		$updateQ[] = q('date2', 				$conn->real_escape_string($link->date2), 			$conn->real_escape_string($newData->date2), 	'd');
		$updateQ[] = q('date2_status', 	$conn->real_escape_string($link->date2_status), 	$conn->real_escape_string($newData->date2_status), 	't');
		$updateQ[] = q('date3', 				$conn->real_escape_string($link->date3), 			$conn->real_escape_string($newData->date3), 	'd');
		$updateQ[] = q('date3_status', 	$conn->real_escape_string($link->date3_status), 	$conn->real_escape_string($newData->date3_status),		't');
		$updateQ[] = q('date4', 				$conn->real_escape_string($link->date4), 			$conn->real_escape_string($newData->date4), 	'd');
		$updateQ[] = q('date4_status', 	$conn->real_escape_string($link->date4_status), 	$conn->real_escape_string($newData->date4_status),		't');
		$updateQ[] = q('date5', 				$conn->real_escape_string($link->date5), 			$conn->real_escape_string($newData->date5), 	'd');
		$updateQ[] = q('date5_status', 	$conn->real_escape_string($link->date5_status), 	$conn->real_escape_string($newData->date5_status), 	't');
		$updateQ[] = q('date6', 				$conn->real_escape_string($link->date6), 			$conn->real_escape_string($newData->date6), 	'd');
		$updateQ[] = q('date6_status', 	$conn->real_escape_string($link->date6_status), 	$conn->real_escape_string($newData->date6_status), 	't');
		$updateQ[] = q('date7', 				$conn->real_escape_string($link->date7), 			$conn->real_escape_string($newData->date7), 	'd');
		$updateQ[] = q('date7_status', 	$conn->real_escape_string($link->date7_status), 	$conn->real_escape_string($newData->date7_status), 	't');
		$updateQ[] = q('date8', 				$conn->real_escape_string($link->date8), 			$conn->real_escape_string($newData->date8), 	'd');
		$updateQ[] = q('date8_status', 	$conn->real_escape_string($link->date8_status), 	$conn->real_escape_string($newData->date8_status), 	't');
		$updateQ[] = q('debt', 				$conn->real_escape_string($link->debt), 				$conn->real_escape_string($newData->debt), 	't');
		$updateQ[] = q('price1', 				$conn->real_escape_string($link->price1), 			$conn->real_escape_string($newData->price1), 	't');
		$updateQ[] = q('price2', 				$conn->real_escape_string($link->price2), 			$conn->real_escape_string($newData->price2), 	't');
		$updateQ[] = q('price3', 				$conn->real_escape_string($link->price3), 			$conn->real_escape_string($newData->price3), 	't');
		$updateQ[] = q('price4', 				$conn->real_escape_string($link->price4), 			$conn->real_escape_string($newData->price4), 	't');
		$updateQ[] = q('price5', 				$conn->real_escape_string($link->price5), 			$conn->real_escape_string($newData->price5), 	't');
		$updateQ[] = q('publish', 			$conn->real_escape_string($link->publish), 			$conn->real_escape_string($newData->publish), 	'd');
		$updateQ[] = q('kadenee',			$conn->real_escape_string($link->kadenee),			$conn->real_escape_string($newData->kadenee), 	't');
		$updateQ[] = q('jod', 					$conn->real_escape_string($link->jod), 				$conn->real_escape_string($newData->jod),	't');
		$updateQ[] = q('jay', 					$conn->real_escape_string($link->jay), 				$conn->real_escape_string($newData->jay), 	't');
		$updateQ[] = q('law_owner', 		$conn->real_escape_string($link->law_owner), 		$conn->real_escape_string($newData->law_owner), 't');
		$updateQ[] = q('image1', 			$conn->real_escape_string($link->image1), 			$conn->real_escape_string($newData->image1), 	't');
		$updateQ[] = q('image2', 			$conn->real_escape_string($link->image2), 			$conn->real_escape_string($newData->image2), 	't');
		$updateQ[] = q('map', 				$conn->real_escape_string($link->map), 				$conn->real_escape_string($newData->map), 		't');
		$updateQ[] = q('sold', 				$conn->real_escape_string($link->sold), 				$conn->real_escape_string($newData->sold), 	'n');

		//Filter empty array
		$updateQ = array_filter($updateQ);
		//Counted updated
		$anyUpdate = sizeof($updateQ);
		
		if(empty($link->first_seen) OR $link->first_seen == "2011-11-11 00:00:00" OR $link->first_seen == "0000-00-00 00:00:00"){
			$updateQ[] = array('update'=>"first_seen", 'oldvalue'=>$link->first_seen, 'newvalue'=>$newData->first_seen, 'type'=>'dt');
		}
		$updateQ[] = q('enable', $link->enable, 1, 'n');
		if($anyUpdate > 0){		
			//prepare data into DB
			$updateQ[] = array('update'=>"last_update", 'oldvalue'=>$link->last_update, 'newvalue'=>$newData->last_update, 'type'=>'dt');
		}	
		$updateQ[] = array('update'=>"last_seen", 'oldvalue'=>$link->last_seen, 'newvalue'=>$newData->last_seen, 'type'=>'dt');
		$updateQ[] = array('update'=>"next_update", 'oldvalue'=>$link->next_update, 'newvalue'=>$newData->next_update, 'type'=>'dt');
		
		$updateTable = 'asset';
		$where = 'WHERE id = '.$newData->id;
 		//Filter empty array again
		$updateQ = array_filter($updateQ);       
		$q = buildUpdateQuery($updateTable, $updateQ, $where);
		//echo $q. PHP_EOL;
		if($q !== false){
			$result = $conn->query($q);
			//printr($q);
			if ($conn->affected_rows > 0){
				echo updateMap($link->id);
				//nomore report
				if($anyUpdate > 0){
					$notifyChange = buildUpdateDiffReport($link, $newData);
					if($notifyChange !== false){
						//instant
						if(isset($newData->new)){
							$notifySubject = '󾔗󾬐󾬐NEW ASSET󾬐󾬐󾔗 '. $newData->tth .' > '.$newData->pth;
							nowUpdate($notifyChange, $notifySubject);
						}else{
							$notifySubject = ' Update '. $link->tth .' > '.$link->pth;
							//nowUpdate($notifyChange, $notifySubject);
						}
					}
					//dailyUpdate(buildUpdateReport($newData->id, $updateQ),'รายงาน การปรับปรุงข้อมูลของสินทรัพย์ เข้าฐานข้อมูลหลัก');
					//echo "Asset add or update success ".date("d/m/Y H:i:s")."<br/>";
					//echo "This asset do a update ".$anyUpdate."feilds<br/>".$newData->url."<br/><br/>";
					//notify update
					//$notifyAssetInfo = notifyAssetInfo($newData);
					//dailyUpdate($notifyAssetInfo, "update ".$newData->tth." > ".$newData->pth);
					//echo buildUpdateReport($newData->id, $updateQ);
					echo "<b>Asset ID: </b> ".$newData->id." has an update ".$anyUpdate."feilds <b>last update: </b> ".date("d/m/Y H:i:s", strtotime($link->last_update))." and <b>next update: </b>".date("d/m/Y H:i:s", strtotime($newData->next_update))."<br/>";
				}else{
					echo "<b>Asset ID: </b> ".$newData->id."  no has any update <b>last update: </b> ".date("d/m/Y H:i:s", strtotime($link->last_update))." and <b>next update: </b>".date("d/m/Y H:i:s", strtotime($newData->next_update))."<br/>";
				}		
			}
			
			if($anyUpdate > 0 AND $result === false){	
				$notifytxt  = 'fails query in saveAssetDB:'.$q. PHP_EOL;
				$notifytxt .= 'URL :'.$newData->url. PHP_EOL;
				dailyError($notifytxt, 'fails query in saveAssetDB');
			}
		}
	} //end web offline
}

function ripAssetlink($link){
	//dbug($link);
	$asset = clone $link;
	$html  = file_get_contents_curl($link->url);
	
	$thisPageOffLine = 0;
	$needle = 'Website is offline'; //offline type1
	$thisPageOffLine = substr_count($html, $needle);
 	$needle = 'Service Unavailable';//offline type2
	$thisPageOffLine = $thisPageOffLine + substr_count($html, $needle); 
	if($thisPageOffLine > 0){
		//test on another proxy
		$proxyUrl = Base32::encode($link->url);
		$proxyHost = 'http://issetc.16mb.com/zyro/0.php';
		//file_get_contents_curl($proxyHost);
		$proxyUrl = $proxyHost.'?'.$proxyUrl;
		$html  = file_get_contents_curl($proxyUrl);
		if(!empty($html)){
			$thisPageOffLine = 0; //got content, not permanent offline
		}
	}
	$thisPageIsDeath = 0;
	//check is error Microsoft VBScript runtime error
	$isVbError1 = substr_count($html, '800a0009'); //death word
	$isVbError2 = substr_count($html, 'vbscript'); //death word
	$thisPageIsDeath = $isVbError1 + $isVbError2;	
	if(($thisPageIsDeath > 0) OR empty($html) === true){
		//removeAssetFromDB($link);
		return false;
	}elseif($thisPageOffLine > 0){
		//just skip and wait ;
		//we set wait time in heder of function updateAsset()
		return 0;
	}else{
		$html = iconv("windows-874", "utf-8", $html);
		$html = str_replace('TABLE','table', $html);
		$html = str_replace('<IMG','<img', $html);
		$html = str_replace('TD>','td>', $html);
		$html = str_replace('TR>','tr>', $html);
		
		//extract data phase2
		//rip almost garbage
		$html  = removeHTMLComment($html);
		$mainTable = BetweenWord($html, '<table', '</table>', 2, 4, 'L', 'R', 'C');
		
		//kadenee something special ???
		$kadenee = BetweenWord($html, 'คดีนี้', '</td>', 0, '+0', 'L', 'L', 'C');
		$kadenee = BetweenWord($kadenee, 'คดีนี้', 'ราคาเริ่มต้น', 0, '+0', 'L', 'L', 'C');
		$kadenee = str_replace('&nbsp', '', $kadenee);
		$asset->kadenee = stripAndTrim($kadenee);
		
		//deed type
		//ฉโนด, สำเนาฉโนด, นส3, นส3 ก. and many more
		$deedTypeHtml = BetweenWord($html, 'เนื้อที่ตาม', '</td>', 0, '+0', 'R', 'L', 'C');
		$deedType = BetweenWord($deedTypeHtml, '>', '<', 0, '+0', 'R', 'L', 'C');
		if(sizeof($deedType) > 0){
			$deedType = stripAndTrim($deedType);
			$deedType = $deedType.' ';
		}else{
			$deedType = '';
		}
		
		//law_owner
		$law_owner = BetweenWord($html, 'เจ้าของสำนวน', '</td>', 0, '+0', 'R', 'L', 'C');
		$asset->law_owner = stripAndTrim($law_owner);

		//map + reinit from
		$mapTable  = BetweenWord($mainTable, '<table', '</table>', 4, 3, 'L', 'R', 'C');	
		$mainTable = BetweenWord($mainTable, '<table', '</table>', 4, 3, 'L', 'R', 'R');
		$mapURL    = getMap($mapTable);
		if($mapURL === false){
			dailyError('fails on extract map from :'.$mapTable. PHP_EOL .'URL:'.$link->url);
		}else{
			$asset->map = $mapURL;
		}
		
		//image + reinit from
		$imagesTable = BetweenWord($mainTable, '<table', '</table>', 3, 3, 'L', 'R', 'C');	
		$mainTable   = BetweenWord($mainTable, '<table', '</table>', 3, 3, 'L', 'R', 'R');
		$imagesURL   = getImages($imagesTable);
		if($imagesURL === false){
			dailyError('fails on extract image from :'.$imagesTable. PHP_EOL .'URL:'.$link->url);
		}else{
			if(isset( $imagesURL[0]) AND !empty( $imagesURL[0])){
				$asset->image1 = $imagesURL[0];
			}
			if(isset( $imagesURL[1]) AND !empty( $imagesURL[1])){
				$asset->image2 = $imagesURL[1];
			}
		}
		
		//remove some garbage from othet tabke left
		$mainTable = BetweenWord($mainTable, '<table', '</table>', 0, 2, 'L', 'R', 'C');
		
		//calendar + reinit from
		$calTable  = BetweenWord($mainTable, '<table', '</table>', 2, 1, 'L', 'R', 'C');
		$mainTable = BetweenWord($mainTable, '<table', '</table>', 2, 1, 'L', 'R', 'R');
		$cal = getCals($calTable);

		$asset->sold = 0; //we wish they not sold...
		$asset->enable = 1; //by default
		if($cal === false){
			dailyError('fails on extract calenda from :'.$calTable. PHP_EOL .'URL:'.$link->url);
		}else{
			$i = 0;$j = 1;
			for($i = 0; $i<8; $i++){
				$j  = $i+1;
				$dA = 'date'.$j;
				$dS = 'date'.$j.'_status';
				if(!empty($cal[$i]['date'])){
					$asset->$dA = $cal[$i]['date'];
					$asset->$dS = $cal[$i]['status'];
					if(trim($cal[$i]['status']) == 'ขายได้' OR trim($cal[$i]['status']) == 'ถอนการยึด'){
						$asset->sold = 1;
						$asset->enable = 0;
					}
				}else{
					$asset->$dA = '';
					$asset->$dS = '';
				}
			}
		}
		
		//details + reinit from
		$detailsTable = BetweenWord($mainTable, '<table', '</table>', 1, 0, 'L', 'R', 'C');	
		$details = getDetails($detailsTable);
		if($details === false){
			dailyError('fails on extract jod && jay from URL:'.$link->url);
		}else{
			$asset->jod = $details['jod'];
			$asset->jay = $details['jay'];
		}
		
		$mainTable = BetweenWord($mainTable, '<table', '</table>', 1, 0, 'L', 'R', 'R');
		
		$ass = getMosts($mainTable);
		if($ass === false){
			dailyError('fails on extract almosts data from URL:'.$link->url);
		}else{
			$asset->sale_order_main = $ass['sale_order_main'];
			$asset->sale_order_sub = $ass['sale_order_sub'];	
			$asset->deed_no = $deedType.$ass['deed_no'];
			$asset->land_owner = $ass['land_owner'];
			$asset->addrno = $ass['addrno'];
			$asset->cort = $ass['cort'];
			$asset->bond = $ass['bond'];
			$asset->conditions = $ass['conditions'];
			$asset->debt = $ass['debt'];
			$asset->price1 = $ass['price1'];
			$asset->price2 = $ass['price2'];
			$asset->price3 = $ass['price3'];
			$asset->price4 = $ass['price4'];
			$asset->price5 = $ass['price5'];		
			$asset->publish = date("Y-m-d", strtotime($ass['publish']));
		}
		return $asset;
	}	
}

function getCals($html){
	$count = 1;
	while($count <> 0){
		$pattern = array(
			'/((bgcolor|color)|class|style)="[^>]*"/im', 
			'/<!--(.*?)-->/im', 
			'/(width)="([0-9]+)"/im',
			'/(border)="([0-9]+)"/im',
			'/align="(left|right|center)"/'
		); 
		$html = preg_replace($pattern, '', $html, -1, $count);
	}
	$html = str_replace(' ','',$html);
	$html = str_replace(' ','',$html);
	$html = str_replace('<font >','<font>',$html);
	$html = str_replace('<td >','<td>',$html);
	$num_of_row = substr_count($html, '</tr>');
	$trIndex = $num_of_row - 1;
	$TRs = BetweenWord($html, '<tr>', '</tr>', 0, $trIndex, 'L', 'R');	
	//$TRs = explode('<font>',$TRs); //6 pices
	//array_shift($TRs);
	if(!empty($TRs)){
		for($i=0; $i<8; $i++){				
			if($trIndex < $i ){
				$cal[]  = array('date'=>'', 'status'=>'');
			}else{
				$j = 2*$i +1;	
				$date   = changeDateB2Y(BetweenWord($TRs, '<font>', '</font>', $i, $i, 'R', 'L'));
				$status = cleanText(BetweenWord($TRs, '<td>', '</td>', $j, $j, 'R', 'L'));
				$cal[]  = array('date'=>$date, 'status'=>$status);
			}
		}
	}else{
		return false;
	}
	return $cal;	
}

function cleanText($html = ''){
	if(!empty($html)){
		$html = stripAndTrim($html);
	}return $html;
}

function changeDateB2Y($date = '99/99/9999'){
	// d/m/B ->/d/m/Y -> Y-m-d
	$date = stripAndTrim($date);
	$numA = substr_count($date, '/');
	$numB = substr_count($date, '-');
	if($numA == 2){
		$seperator = '/';
	}elseif($numB == 2){
		$seperator = '-';
	}else{
		//unknow date format
		return $date;
	}
	$d = explode($seperator, $date);
	$d[2] = intval($d[2]);
	if($d[2] > 2400){
		$d[2] = $d[2]-543;
		$tmp = $d;
		$d[0] =  $tmp[2];
		$d[1] =  $tmp[1];
		$d[2] =  $tmp[0];
		$date = implode('-',$d);
	}else{
		$date = date("Y-m-d", strtotime($date));
	}	
    return $date;
}

function getDetails($html){
	$jod = BetweenWord($html,'<u>','</u>',0,0);
	$jod = stripAndTrim($jod);
	$jay = BetweenWord($html,'<u>','</u>',1,1);
	$jay = stripAndTrim($jay);
	if(empty($jod) OR empty($jay)){
		return false;
	}else{
		return array("jod"=>$jod,"jay"=>$jay);
	}
}

function getImages($html){
	//todo some asset has 1,2 or no image	
	//need check by count '<img'; before rip them off
	//$html = str_replace('newbid/img/null_aSset_pic.jpg', '', $html);
	$html = str_replace('./img/null_aSset_pic.jpg', 'NOIMAGESAVALIABLE.gif', $html);
	$html = str_replace('/img/null_aSset_pic.jpg', 'NOIMAGESAVALIABLE.gif', $html);
	//$html = str_replace('null_aSset_pic.jpg', '', $html);
	$html = str_replace('.jpg.', '.jpg', $html);
	//$html = str_replace('"', '', $html);
	//$html = str_replace("'", '', $html);	
	$isImageexist = substr_count($html, 'src=');
	if($isImageexist > 0){
		//find img each in each td 
		$tdNum = substr_count($html, '<td');
		for($col = 0;$col < $tdNum;$col++){
			$tdCols[] = BetweenWord($html, '<td', '</td>', $col, $col, 'L', 'R');
		}
		foreach($tdCols as $tdCol){
			//find image in collumn
			$images[] = BetweenWord($tdCol, 'src="', '" width="200"', 0, 0, 'R', 'L');
		}
	}
	
	if(isset($images)){
		//check images
		foreach($images as $image){
			$image = str_replace(" ",'%20',$image);
			//$image = trim($image);
			$file_extension = explode('.',$image);
			$last = sizeof($file_extension)-1;
			$file_extensionChecker = $file_extension[$last];
			$file_extensionChecker = substr($file_extensionChecker, 0, 3);
			$file_extensionChecker = strtolower($file_extensionChecker);
			if($file_extensionChecker == 'jpg' OR $file_extensionChecker == 'png' OR $file_extensionChecker == 'gif' OR $file_extensionChecker == 'bmp'){	
				if($image == 'NOIMAGESAVALIABLE.gif'){
					$image = 'http://example.com/map/images/image_not_available.gif';
				}elseif($image !== false){
					//$image = "http://asset.led.go.th".$image;	
					$image = "http://1038529402.rsc.cdn77.org/".$image;
				}
				$imageURL[] = $image;
			}
		}
		if(!isset($imageURL[0])){	
			$imageURL[0] = '';
		}
		if(!isset($imageURL[1])){	
			$imageURL[1] = '';
		}
		return $imageURL;
	}
}

function doLog($txt){
	return file_put_contents('error_log', date("d/m/Y H:i:s")."\t".$txt.PHP_EOL, FILE_APPEND | LOCK_EX);
}

function htmlHeader($header = ''){
	return '<html>
	<head>
	<title>LED Scarper.</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<style>
	a:hover,a:active{color:#4CAF50;}
	table.asset{width:100%;}
	table.asset td{text-align:center;}
	</style>'.
	$header.'
	</head>
	<body>
	';
}

function showPriceReport($txt){
	if($txt > 0){
		return number_format($txt)."บาท";
	}else{
		return $txt;
	}
}

function buildURLfromLedTR($urlParam, $link = 'http://1038529402.rsc.cdn77.org/'){
	$urlParam = strstr($urlParam,'asset_open.asp');
	$urlParam = str_replace(' ','SPACERESERVE',$urlParam); //preverve space
	$urlParam = str_replace(',','COMMARESERVE',$urlParam); //preverve comma
	$urlParam = trim($urlParam,"'");
	$urlParams = explode('&', $urlParam);
	$paramAll = '';
	foreach($urlParams as $assetParam)
	{
		$tmpKey = explode('=',$assetParam);
		if(sizeof($tmpKey) == 2)
		{
			$key = urlencode(iconv("utf-8","windows-874",$tmpKey[1]));
			$key = str_replace('%29', ')', $key);
			$key = str_replace('%28', '(', $key);
			$key = str_replace('%2B', '%20', $key);
			$key = str_replace('%25', '%', $key);
			$param = '&'.trim($tmpKey[0]).'='.$key;
			$paramAll .= $param;
		}else{
			dailyError('function splitParam has error:'.$urlParam);
		}	
	}
	$paramAll = substr($paramAll, 1);
	//set prevervkey back
	$paramAll = str_replace('SPACERESERVE','%20',$paramAll); //preverve space
	$paramAll = str_replace('COMMARESERVE',',',$paramAll); //preverve comma
	return $link.$paramAll;
}

function htmlFooter($footer = ''){
	return '
	</body>'.
	$footer.'
</html>';
}

function htmlRefresh(){
	echo htmlHeader();
	echo '<script type="text/javascript">location.reload(true);</script>';
	echo htmlFooter();
}

function ledencode($string){	
	/*
	clean to led encode
	1.
	UPDATE `amphur`
	SET    
	`amphur`.`encode` = replace(`amphur`.`encode`, '%25', '%'),
	`amphur`.`encode` = replace(`amphur`.`encode`, '%2B', '%20'),
	`amphur`.`encode` = replace(`amphur`.`encode`, '%28', '('),
	`amphur`.`encode` = replace(`amphur`.`encode`, '%29', ')')
	2.
	UPDATE `province`
	SET    
	`province`.`encode` = replace(`province`.`encode`, '%25', '%'),
	`province`.`encode` = replace(`province`.`encode`, '%2B', '%20'),
	`province`.`encode` = replace(`province`.`encode`, '%28', '('),
	`province`.`encode` = replace(`province`.`encode`, '%29', ')')
	*/
	$string = urlencode(iconv('utf-8','windows-874', $string));
	$string = urlencode($string);
	return $string;
}

function encodeUtf8ToLed($string){
	$string = urlencode(iconv('utf-8','windows-874', $string));
	//$string = urlencode($string);
	return $string;
}

function leddecode($string){
	$string = urldecode($string);
	return $string;
}

function removeHTMLComment($html = '') {
	$html = preg_replace('/<!--(.|\s)*?-->/', '', $html);
	return $html;
}

//temporary disable
/*function removeHTMLTdDivFont($html = '') {
	$html = str_replace('<td', '<div', $html); 	//change td to div
	$html = str_replace('</td', '</div', $html); //change td to div
	$html = strip_tags($html, '<font><div>'); 	//remove color	
	return $html;
}*/

function stripAndTrim($html = '') {
	$html = strip_tags($html);
	$html = trim($html);
	return $html;
}

function findTxtInTxt($needle, $allTxt){
	$lastPos  = 0;
	$index = 0; //programming index start from 0
	while (($lastPos = strpos($allTxt, $needle, $lastPos))!== false) {		
		$positions[] = array(
						"before"=>$lastPos - strlen($needle),
						"after"=>$lastPos,
						"index"=>$index
						);
		$index++;
		$lastPos = $lastPos + strlen($needle);
	}
	return $positions;
}

function showSimplePrice($numPrice){
	switch ($numPrice) {
		case $numPrice > 999999:
			$simplePrice = number_format($numPrice/1000000, 1).'ล้าน';
			break;
		case $numPrice > 99999:
			$simplePrice = number_format($numPrice/100000).'แสน';
			if($simplePrice == 10){
				$simplePrice = '1ล้าน';
			}
			break;
		default:
			$simplePrice = number_format($numPrice);
			break;
	}
	return $simplePrice;
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

function notifyAssetInfo($objAsset, $images = true){	
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
	$objAsset->id = genAssetID($objAsset->url);
	if(isset($objAsset->changeflag)){
		$reportQ .= mailTableHeader();	
		$reportQ .= mailRow("<b>รายการเปลี่ยนแปลง</b>",$objAsset->changeflag);
		$reportQ .= mailTableFooter();	
	}	
	$reportQ .= mailTableHeader();
	$reportQ .= mailRow("<b>ประเภท</b>",$objAsset->tth);
	$reportQ .= mailRow("<b>ที่ตั้ง</b>",$tumponText.$amphurText.$provinceText);
    $areaNum = showWnumberReport($objAsset->size400w, $objAsset->size100w, $objAsset->sizew, false);
    $areaText = showWnumberReport($objAsset->size400w, $objAsset->size100w, $objAsset->sizew, true);
    if($areaNum > 0){
        $areaAverageText = "&nbsp;&nbsp;&nbsp;&nbsp;<b>ราคาต่อตรว</b>&nbsp;&nbsp;".number_format(ceil($objAsset->estimated_price/$areaNum)).'บาท/ตรว.';
    }else{
        $areaAverageText = '';
    }
	$reportQ .= mailRow("<b>พื้นที่</b>", $areaText );
	$reportQ .= mailRow("<b>ราคาประเมิน</b>",showPriceReport($objAsset->estimated_price).$areaAverageText);
	if(!empty($objAsset->kadenee)){
		$reportQ .= mailRow("<b>หมายเหตุ</b>", $objAsset->kadenee);	
	}
	$reportQ .= mailTableFooter();
	$reportQ .= mailTableHeader();
	$reportQ .= mailRow('<b style="float:right">ราคาประเมินของสำนักประเมินราคาทรัพย์ กรมธนารักษ์ </b>', '', showPriceReport($objAsset->price1));
	$reportQ .= mailRow('<b style="float:right">ราคาประเมินของผู้เชี่ยวชาญการประเมินราคา </b>', '', showPriceReport($objAsset->price2));
	$reportQ .= mailRow('<b style="float:right">ราคาประเมินของเจ้าพนักงานบังคับคดี </b>', '', showPriceReport($objAsset->price3));
	$reportQ .= mailRow('<b style="float:right">ราคาที่กำหนดโดยคณะกรรมการกำหนดราคาทรัพย์ </b>', '', showPriceReport($objAsset->price4));
	$reportQ .= mailRow('<b style="float:right">ราคาประเมินของเจ้าพนักงานประเมินจำนวน </b>', '', showPriceReport($objAsset->price5));	
	$reportQ .= mailTableFooter();
	$reportQ .= mailTableHeader();
	$reportQ .= mailRow("<b>เลขโฉนด</b>",$objAsset->deed_no.'   <b>ผู้ครอบครองกรรมสิทธิ์ทรัพย์ </b>'. $objAsset->land_owner);
	$reportQ .= mailRow("<b>ลำดับขาย</b>",$objAsset->sale_order_main .'-'.$objAsset->sale_order_sub);
	$reportQ .= mailRow("<b>รหัสทรัพย์</b>",'MAP#'.$objAsset->id.' '.$objAsset->law_suit_no.'/'.$objAsset->law_suit_year);
	$reportQ .= mailRow("<b>โจทย์</b>", $objAsset->jod);
	$reportQ .= mailRow("<b>จำเลย</b>", $objAsset->jay);
	$reportQ .= mailRow("<b>เจ้าของคดี</b>", $objAsset->law_owner);
	$reportQ .= mailRow("<b>พบทรัพย์ครั้งแรก</b>", showDateTimeReport($objAsset->first_seen));
	$reportQ .= mailRow("<b>อัพเดตครั้งนี้</b>", showDateTimeReport($objAsset->last_update));
	$reportQ .= mailRow("<b>อัพเดตครั้งต่อไป</b>", showDateTimeReport($objAsset->next_update));
	$reportQ .= mailTableFooter();
	$reportQ .= mailTableHeader();	
	/*$arrLink = array(
					$objAsset->url,
					'LED Link',
					'http://example.com/map/index.php?ptype=viewFullListing&amp;reid='.$objAsset->id,
					'MAP#'.$objAsset->id,
					getProxyUrl('http://asset.led.go.th/report_new/reports.asp?ALAW_SUIT_NO='.intSuit($objAsset->law_suit_no).'&amp;ALAW_SUIT_YEAR='.intSuit($objAsset->law_suit_year)),
					'Sale Report',
					getProxyUrl('http://asset.led.go.th/newbid/asset_search_law_suit.asp?search_law_suit_no='.encodeUtf8ToLed($objAsset->law_suit_no).'&amp;search_law_suit_year='.intSuit($objAsset->law_suit_year).'&amp;search_law_court_name='.encodeUtf8ToLed($objAsset->cort).'&amp;search_owner_suit_name=&amp;search_person1=&amp;search_person2='),
					'Search Asset'
					);*/
	$arrLink = array(
					$objAsset->url,
					'LED Link',
					'http://example.com/map/index.php?ptype=viewFullListing&amp;reid='.$objAsset->id,
					'MAP#'.$objAsset->id,
					getProxyUrl('http://asset.led.go.th/report_new/reports.asp?ALAW_SUIT_NO='.intSuit($objAsset->law_suit_no).'&amp;ALAW_SUIT_YEAR='.intSuit($objAsset->law_suit_year)),
					'Sale Report',
					getProxyUrl('http://asset.led.go.th/newbid/asset_search_law_suit.asp?search_law_suit_no='.encodeUtf8ToLed($objAsset->law_suit_no).'&amp;search_law_suit_year='.intSuit($objAsset->law_suit_year).'&amp;search_law_court_name=&amp;search_owner_suit_name=&amp;search_person1=&amp;search_person2='),
					'Search Asset'
					);
	$linkText = sprintf_array('<a href="%s" target="_blank" class="bt led btn btn-success btn-xs">%s</a> <a href="%s" target="_blank" class="bt aid  btn btn-success btn-xs">%s</a> <a href="%s" target="_blank" class="bt sreport  btn btn-success btn-xs">%s</a> <a href="%s" target="_blank" class="bt sreport  btn btn-success btn-xs">%s</a>', $arrLink);
	$reportQ .= mailRow($linkText, '', '');
	$reportQ .= mailTableFooter();
	$reportQ .= mailTableHeader();
	$reportQ .= mailRow("<b>เงื่อนไขผู้เข้าสู้ราคา</b>", $objAsset->conditions);
	$reportQ .= mailRow("<b>จะทำการขายโดย  </b>",$objAsset->debt);
	$reportQ .= mailRow("<b>นัดที่ 1 </b> ", showDateReport($objAsset->date1), $objAsset->date1_status);
	$reportQ .= mailRow("<b>นัดที่ 2  </b> ", showDateReport($objAsset->date2), $objAsset->date2_status);
	$reportQ .= mailRow("<b>นัดที่ 3  </b> ", showDateReport($objAsset->date3), $objAsset->date3_status);
	$reportQ .= mailRow("<b>นัดที่ 4   </b>", showDateReport($objAsset->date4), $objAsset->date4_status);
	$reportQ .= mailRow("<b>นัดที่ 5   </b>", showDateReport($objAsset->date5), $objAsset->date5_status);
	$reportQ .= mailRow("<b>นัดที่ 6   </b>", showDateReport($objAsset->date6), $objAsset->date6_status);
	if($objAsset->date7 <> '0000-00-00' AND !empty($objAsset->date7)){
		$reportQ .= mailRow("<b>นัดที่ 7   </b>", showDateReport($objAsset->date7), $objAsset->date7_status);
	}
	if($objAsset->date8 <> '0000-00-00' AND !empty($objAsset->date8)){
		$reportQ .= mailRow("<b>นัดที่ 8   </b>", showDateReport($objAsset->date8), $objAsset->date8_status);
	}
	$reportQ .= mailRow("<b>วันที่ประกาศขึ้นเว็บ </b>", showDateReport($objAsset->publish));
	$reportQ .= mailTableFooter();
	
	if($images === true){
		$reportQ .= mailTableHeader();
		if(!empty($objAsset->image1) OR !empty($objAsset->image2) OR !empty($objAsset->map)){
			$reportQ .= mailRow('<b>ภาพทรัพย์ </b>');
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

//update queqe
function q($feildname, $oldvalue = '', $newvalue = '', $type = 't'){
	$q = 0;
	if(!empty($oldvalue)){
		$oldvalue = trim($oldvalue);
		$oldvalue = str_replace("'",'',$oldvalue);
		$oldvalue = str_replace('"','',$oldvalue);
	}
	if(!empty($newvalue)){
		$newvalue = trim($newvalue);
		$newvalue = str_replace("'",'',$newvalue);
		$newvalue = str_replace('"','',$newvalue);
	}
	$update = '';
	switch($type){
		case 'number':
		case 'n':
			$return_type = 'n';
			if(($oldvalue <> $newvalue) AND $newvalue <> ''){
				$update = int($newvalue);
				$q = 1;				
			}
			break;	
		case 'text':
		case 't':
			$return_type = 't';
			if(($oldvalue <> $newvalue) AND $newvalue <> ''){
				$update = $newvalue;
				$q = 1;
			}
			break;	
		case 'date':
		case 'd':
			$return_type = 'd';
			$oldvalue = date("Y-m-d", strtotime($oldvalue));
			$newvalue = date("Y-m-d", strtotime($newvalue));
			if(($oldvalue <> $newvalue) AND $newvalue <> ''){
				$update = $newvalue;
				$q = 1;
			}
			break;
		case 'datetime':
		case 'dt':
		case 'time':
			$return_type = 'dt';
			$oldvalue = date("Y-m-d H:i:s", strtotime($oldvalue));
			$newvalue = date("Y-m-d H:i:s", strtotime($newvalue));
			if(($oldvalue <> $newvalue) AND $newvalue <> ''){
				$update = $newvalue;
				$q = 1;
			}
			break;
	}
	if($q == 1){
		$result = array('update'=>$feildname, 'oldvalue'=>$oldvalue, 'newvalue'=>$update, 'type'=> $return_type);
		return  $result;
	}
}

function getMap($html){
	$isMapexist = substr_count($html, 'mapopen');
	if($isMapexist == 1){
		$html = str_replace('"', '', $html);
		$html = str_replace("'", '', $html);
		$html = str_replace(" ", '%20', $html);	
		$html = str_replace('.jpg.', '.jpg', $html);
		$URL = between($html, 'open(', ',mapopen');
		//check images
		$file_extension = substr($URL, -3, 3);
		$file_extension = strtolower($file_extension);
		if($file_extension == 'jpg' OR $file_extension == 'png' OR $file_extension == 'bmp' OR $file_extension == 'gif'){		
			$URL = "1038529402.rsc.cdn77.org/".$URL;
			$URL = str_replace('///', '/', $URL);
			$URL = str_replace('//', '/', $URL);
			$URL = 'http://'.$URL;
			$return = $URL;
		}else{			
			$return = false;
		}
	}else{
		$return = ''; //this asset no have Map
	}
	return $return;
}

function getMosts($html){
	$fullHtml = $html;
	$count = 1;
	while($count <> 0){
		$pattern = array(
			'/((bgcolor|color)|class|style)="[^>]*"/im', 
			'/<!--(.*?)-->/im', 
			'/(width)="([0-9]+)"/im',
			'/(border)="([0-9]+)"/im',
			'/align="(left|right|center)"/'
		); 
		$html = preg_replace($pattern, '', $html, -1, $count);
	}
	
	$html = str_replace(' ','',$html);
	$html = str_replace('"','',$html);
	$html = str_replace("'",'',$html);
	$html = str_replace(' ','',$html);
	$html = str_replace('<font >','<font>',$html);
	$html = str_replace('<td >','<td>',$html);
	
	///orders
	//$orders = BetweenWord($html,'<font>','</font>',0,0,'R','L');
	$htmlOrders = BetweenWord($html, '<tr>', '</tr>', 0, 0, 'R', 'L');
	
	//print_r($htmlOrders);
	$htmlOrders = BetweenWord($htmlOrders, '<td', '</td>', 0, 0, 'R', 'R');
	$htmlOrders = BetweenWord($htmlOrders, '>', '</td>', 0, 0, 'R', 'L');
	$htmlOrders = str_replace('&nbsp;', ' ', $htmlOrders);
	$htmlOrders = stripAndTrim($htmlOrders);
	$orders 	= explode('ลำดับที่', $htmlOrders);
	if(isset($orders[1])){
		$orders = trim($orders[1]);
		$orders = explode('-', $orders);
		if(!isset($orders[1])){
			/*dailyError("Has uncommon order in asset html:".PHP_EOL.$html.PHP_EOL,'notify from '. __FILE__ .' line'. __LINE__);*/
			$orders[0] = 0;
			$orders[1] = 0;
		}
	}else{
		$orders = getHardOrder($htmlOrders);
	}
	$ass['sale_order_main'] = int($orders[0]);
	$ass['sale_order_sub']  = int($orders[1]);
	
	//deed no
	$htmlDeed 	= BetweenWord($html,'<tr>','</tr>',4,4,'R','L');
	$htmlDeed 	= BetweenWord($htmlDeed,'<td','</td>',0,0,'R','R');
	$htmlDeed 	= BetweenWord($htmlDeed,'>','</td>',0,0,'R','L');
	$htmlDeed 	= str_replace('&nbsp;',' ',$htmlDeed);
	$htmlDeed 	= stripAndTrim($htmlDeed);
	$deed_no 		= explode('น.3ก',$htmlDeed);
	if(isset($deed_no[1])){
		//array('ที่ดินโฉนด/', '34341')
		$ass['deed_no'] = $deed_no[1];
	}else{		
		$deed_no 	= str_replace('น.ส3/น.3ก',' ',$htmlDeed);
		$ass['deed_no'] = $deed_no;
		/*$notifyError = "Has uncommon deed_no in asset html".PHP_EOL;
		$notifyError .= "Full Html".PHP_EOL;
		$notifyError .= $fullHtml.PHP_EOL;
		$notifyError .= "After extract into deed_no part".PHP_EOL;
		$notifyError .= $html;
		dailyError($notifyError, 'Found uncommon deed_no in asset html');*/
	}
	
	//land owner
	//old medthod doubt??
	$landOwner1 = substr_count($html, 'ผู้ถือกรรมสิทธิ์');
	$landOwner2 = substr_count($html, 'ผู้ถือสิทธิครอบครอง');
	$landOwner3 = substr_count($html, 'ผู้ถือ');
	if($landOwner1 == 1){
		$ass['land_owner'] = BetweenWord($html, 'มีชื่อ', 'ผู้ถือกรรมสิทธิ์', 0, 0, 'R', 'L'); //ผู้ถือกรรมสิทธิ์ && ผู้ถือสิทธิครอบครอง
	}elseif($landOwner2 == 1){
		$ass['land_owner'] = BetweenWord($html, 'มีชื่อ', 'ผู้ถือสิทธิครอบครอง', 0, 0, 'R', 'L'); //ผู้ถือกรรมสิทธิ์ && ผู้ถือสิทธิครอบครอง
	}elseif($landOwner3 == 1){
		$ass['land_owner'] = BetweenWord($html, 'มีชื่อ', 'ผู้ถือ', 0, 0, 'R', 'L'); //ผู้ถือกรรมสิทธิ์ && ผู้ถือสิทธิครอบครอง		
	}else{
		//no land owner
		$ass['land_owner'] =  '-';
	}
	$ass['land_owner'] = str_replace('เป็น', '', $ass['land_owner']);
	$ass['land_owner'] = stripAndTrim($ass['land_owner']);
	
	//new medthod
	/*
	$land_owner = BetweenWord($html, 'มีชื่อ', 'ผู้ถือ', 0, '+0', 'R', 'L'); 
	$land_owner = str_replace('เป็น', '', $land_owner);
	$ass['land_owner']	= stripAndTrim($land_owner);
	*/
	
	//address no
	$htmlAddrno = $html;	
	$addrnoCutString = 'เลขที่';
	$addrnoCount= substr_count($htmlAddrno, $addrnoCutString);
	if($addrnoCount <> 1){
		$addrnoCutString = '>เลขที่';
		$addrnoCount= substr_count($htmlAddrno, $addrnoCutString);
	}elseif($addrnoCount <> 1){
		$addrnoCutString = 'เลขที่<';
		$addrnoCount= substr_count($htmlAddrno, $addrnoCutString);
	}
	
	if($addrnoCount == 1){
		$htmlAddrno = BetweenWord($htmlAddrno, $addrnoCutString, '</table', 0, 0, 'L', 'L');
		$htmlAddrno = BetweenWord($htmlAddrno, $addrnoCutString, '</tr', 0, 0, 'R', 'L');
		$htmlAddrno = str_replace('&nbsp;', ' ', $htmlAddrno);
		$ass['addrno'] = stripAndTrim($htmlAddrno);	
	}else{
		//not found
		$ass['addrno'] = '-';
	}

	//cort	
	$htmlSarn = $html;	
	$sarnCutString = 'ศาล';
	$sarnCount= substr_count($htmlSarn, $sarnCutString);
	if($sarnCount <> 1){
		$sarnCutString = '>ศาล';
		$sarnCount= substr_count($htmlSarn, $sarnCutString);
	}elseif($sarnCount <> 1){
		$sarnCutString = 'ศาล<';
		$sarnCount= substr_count($htmlSarn, $sarnCutString);
	}
	
	if($sarnCount == 1){
		$htmlSarn = BetweenWord($htmlSarn, $sarnCutString, '</table', 0, 0, 'L', 'L');
		$htmlSarn = BetweenWord($htmlSarn, $sarnCutString, '</td', 0, 0, 'R', 'L');
		$htmlSarn = str_replace('&nbsp;', ' ', $htmlSarn);
		$ass['cort'] = stripAndTrim($htmlSarn);	
	}else{
		//not found
		$ass['cort'] = '-';
	}
	
	//pre mix
	$htmlMix = BetweenWord($html, 'เงื่อนไขผู้เข้าสู้ราคา', 'วันที่ประกาศขึ้นเว็บ', 0, 0, 'L', 'L');
	//extra
	$htmlExtra = BetweenWord($html, 'หมายเหตุ', '</table>', 0, 0, 'L', 'L');
	if(substr_count($htmlExtra, '</font>') > 1){
		$extraChunks = explode('</font>',$htmlExtra);
		//remove first element
		array_shift($extraChunks);
	}
	
	$extra = '';
	if(sizeof($extraChunks) >= 1){	
		foreach($extraChunks as $extraChunk){
			$extraChunk  = str_replace('&nbsp', ' ', $extraChunk);
			$extraChunk  = stripAndTrim($extraChunk);
			if(strlen($extraChunk) > 10){
				$extra .= '<br/>'.stripAndTrim($extraChunk);
			}			
		}
	}
	
	//condition
	$htmlCondition = BetweenWord($htmlMix, 'เงื่อนไขผู้เข้าสู้ราคา', '<tr>', 0, 0, 'L', 'L');	
	$condition = BetweenWord($htmlCondition, 'เงื่อนไขผู้เข้าสู้ราคา', '</td>', 0, 0, 'R', 'L');
	$condition = str_replace('<b>', '', $condition);
	$condition = str_replace('</b>', '', $condition);	
	$condition = str_replace('<font>', '', $condition);
	$condition = str_replace('</font>', '', $condition);
	$condition = str_replace('จำนวน', 'จำนวน ', $condition);
	$condition = str_replace('.00บาท', 'บาท', $condition);
	$ass['conditions'] = stripInvisible($condition).$extra;
	
	//bond
	$bonds = BetweenWord($htmlCondition, '<font>', '</font>', 0, 0, 'R', 'L');	
	$bonds = stripInvisible($bonds);
	$ass['bond'] = cleanPrice($bonds);	
	
	//prepare for next
	
	$htmlMix = BetweenWord($htmlMix,'<tr>', '</tr>', 1, 7, 'L', 'R');
	//debt && price 1-5
	//method-1
	
		$htmlFix = str_replace('จะทำการขายโดย', '', $htmlMix);
		$htmlFix = str_replace('<font>', '', $htmlFix);
		$htmlFix = str_replace('</font>', '', $htmlFix);
		$htmlFix = str_replace('colspan=3', '', $htmlFix);
		$htmlFix = str_replace('color=#ff0000', '', $htmlFix);
		$htmlFix = str_replace('color=#FF0000', '', $htmlFix);
	
		$ass['debt']    = BetweenWord($htmlFix,'<td','</td>',0,0,'R','R');		
		$ass['debt']    = str_replace('&nbsp', ' ', $ass['debt']);
		$ass['debt']    = str_replace('.00', '', $ass['debt']);
		$ass['debt']    = trim(stripInvisible(BetweenWord($ass['debt'],'>','</td>',0,0,'R','L')));
		
		$ass['price1']  = BetweenWord($htmlFix,'<td','</td>',1,1,'R','R');
		$ass['price1']  = cleanPrice(BetweenWord($ass['price1'],'>','</td>',0,0,'R','L'));
		
		$ass['price2']  = BetweenWord($htmlFix,'<td','</td>',2,2,'R','R');
		$ass['price2']  = cleanPrice(BetweenWord($ass['price2'],'>','</td>',0,0,'R','L'));
		
		$ass['price3']  = BetweenWord($htmlFix,'<td','</td>',3,3,'R','R');
		$ass['price3']  = cleanPrice(BetweenWord($ass['price3'],'>','</td>',0,0,'R','L'));
		
		$ass['price4']  = BetweenWord($htmlFix,'<td','</td>',4,4,'R','R');
		$ass['price4']  = cleanPrice(BetweenWord($ass['price4'],'>','</td>',0,0,'R','L'));
		
		$ass['price5']  = BetweenWord($htmlFix,'<td','</td>',5,5,'R','R');
		$ass['price5']  = cleanPrice(BetweenWord($ass['price5'],'>','</td>',0,0,'R','L'));
	
	$htmlPublish = BetweenWord($html, 'วันที่ประกาศขึ้นเว็บ', '</table>', 0, 0, 'L', 'L');
	$htmlPublish = BetweenWord($htmlPublish, 'วันที่ประกาศขึ้นเว็บ', '</td>', 0, 0, 'R', 'L');
	$htmlPublish = str_replace('<b>', '', $htmlPublish);
	$htmlPublish = str_replace('</b>', '', $htmlPublish);
	$htmlPublish = str_replace('<font>', '', $htmlPublish);
	$htmlPublish = str_replace('</font>', '', $htmlPublish);
	$htmlPublish = str_replace('&nbsp;', ' ', $htmlPublish);
	$htmlPublish = stripAndTrim($htmlPublish);	
	$ass['publish'] = changeDateB2Y($htmlPublish);	
	
	return $ass;
}

function cleanPrice($txtPrice = 'ไม่มี'){
	$txtPrice 	  = str_replace(',','',$txtPrice);
	$nothas 	  = substr_count($txtPrice, 'ไม่มี');
	$totsaniyom = substr_count($txtPrice, '.');
	if($nothas == 1 OR empty($txtPrice)){
		$txtPrice = 'ไม่มี';
	}elseif($totsaniyom == 1){
		$txtPrice = strtolower($txtPrice);
		$txtPrice = str_replace('<font>', '', $txtPrice);
		$txtPrice = str_replace('</font>', '', $txtPrice);
		$txtPrice = str_replace('colspan=3', '', $txtPrice);
		$txtPrice = str_replace('color=#ff0000', '', $txtPrice);
		$txtPrice = stripAndTrim($txtPrice);
		$Price = explode('.',$txtPrice);		
		if(isset($Price[0])){	
			$txtPrice = int($Price[0]); //super hardcode rip all char
		}
	}
	return $txtPrice;
}

function buildUpdateReport($assetId, $images = true){	
	$objAsset = getObjAsset($assetId);
	return notifyAssetInfo($objAsset, $images);
}

function buildUpdateDiffReport($objOld, $objNew){
	$objNew = getObjAssetByid($objNew->id); //get current status from fresh data
	$arrObjOld  = (array)$objOld;	
	$arrObjNew = (array)$objNew;
	$newKey = array_keys($arrObjNew);
	$oldKey  = array_keys($arrObjOld);
	$keys     = array_unique(array_merge($newKey, $oldKey));
	$changekey = array();
	$arrPrice = array("estimated_price", "price1", "price2", "price3", "price4", "price5", "debt");
	$arrTextPrice = array("conditions", "debt");
	$arrImage = array("image1", "image2", "map");
	$arrDate = array("date1", "date2", "date3", "date4","date5", "date6", "date7", "date8", "publish");
	$arrDateTime = array("first_seen", "last_seen", "next_update", "last_update" ); //change visual diff but not notify change
	foreach($keys as $key){
		if(isset($arrObjOld[$key])){
			$oldValue = $arrObjOld[$key];
		}else{
			$oldValue = '';
		}
		if(isset($arrObjNew[$key])){
			$newValue = $arrObjNew[$key];
		}else{
			$newValue = '';
		}
			
		if(!in_array($key, $arrImage)){
			if(in_array($key, $arrTextPrice)){
				//compare only number
				$newValue = preg_replace("/[^0-9,.]/", "",  $newValue);
				$oldValue = preg_replace("/[^0-9,.]/", "", $oldValue);				
			}		

			if(in_array($key, $arrDate)){
				if(!empty($newValue)){
					$newValue = date("d/m/Y", strtotime($newValue));
				}else{
					$newValue =  '';
				}
				if(!empty($oldValue)){
					$oldValue = date("d/m/Y", strtotime($oldValue));
				}else{
					$oldValue =  '';
				}
			}
			
			if(in_array($key, $arrDateTime)){
				if(!empty($newValue)){
					$newValue = date("d/m/Y H:i:s", strtotime($newValue));
				}else{
					$newValue =  '';
				}
				if(!empty($oldValue)){
					$oldValue = date("d/m/Y H:i:s", strtotime($oldValue));
				}else{
					$oldValue =  '';
				}
			}

			if(in_array($key, $arrPrice)){
				//compare only number				
				$newValue = preg_replace("/[^0-9,.]/", "",  $newValue);
				$newValue = str_replace(',', '',  $newValue);
				$newValue = (int)$newValue;
				$oldValue = preg_replace("/[^0-9,.]/", "", $oldValue);
				$oldValue = str_replace(',', '',  $oldValue);
				$oldValue = (int)$oldValue;
				if(empty($oldValue) OR $oldValue == "-" OR $oldValue == '0'){
					$oldValue = 'ไม่มี';
				}elseif($oldValue > 0 && is_numeric($oldValue)){
					$oldValue = number_format($oldValue)."บาท";					
				}
				if(empty($newValue) OR $newValue == "-" OR $newValue == '0'){
					$newValue = 'ไม่มี';
				}elseif($newValue > 0 && is_numeric($newValue)){
					$newValue = number_format($newValue)."บาท";					
				}
			}

			if($oldValue == '' OR $oldValue == "-"){
				$oldValue = '(blank)';
			}

			if($newValue == '' OR $newValue == "-"){
				$newValue = '(blank)';
			}
			if($oldValue <> $newValue){
				if(in_array($key, $arrTextPrice)){
					//break long word
					$br = '<br/>';
					$oldValue = $arrObjOld[$key];
					$newValue = $arrObjNew[$key];
					if($oldValue == '' OR $oldValue == "-"){
						$oldValue = '(blank)';
					}

					if($newValue == '' OR $newValue == "-"){
						$newValue = '(blank)';
					}
				}else{
					$br = '';
				}

				$objNew->$key = "<small style=\"color:red;text-decoration:line-through\">". $oldValue."</small>".$br."<b style=\"color:green\">". $newValue ."</b>";
				if(!in_array($key, $arrDateTime)){
					$changekey[] = $key; //got change
				}				
			}
		}
	}
	if($arrObjNew['enable'] == 0){
		unset($changekey);
		$changekey[] = '<b style="color:red;text-decoration:underline">This asset was remove and disable</b>'; //got change
	}
	if(sizeof($changekey) > 0){
		$objNew->changeflag = implode(', ', $changekey);
		return notifyAssetInfo($objNew);
	}else{
		return false;
	}
}

function notifyUpdate($html ,$subject = ''){
	if(empty($subject)){
		$subject = 'New update notify';
	}
	$to      = 'someone@gmail.com';//change this
	//$to        = 'someone_else@gmail.com';
	// Headers
	$headers[] = 'From: Digital Asset Management System <mailer@localhost>';
	$headers[] = 'X-Mailer: PHP/' . phpversion();
	$headers[] = "MIME-Version: 1.0";
	$headers[] = "Content-Type: text/html; charset=utf-8";
	$message[] = '<html><body class="body1">';
	$message[] = '<table cellpadding="2" cellspacing="1" border="1"  class="table1">';
	$message[] = '<tr class="tr1">';
	$message[] = '<td class="td1">';
	$message[] = nl2br($html);
	$message[] = '</td>';
	$message[] = '</tr>';
	$message[] = "</table>";
	$message[] = '<table cellpadding="2" cellspacing="1" border="1" class="table2">';
	$message[] = '<tr class="tr2">';
	$message[] = '	<td class="td2">';
	$message[] = '		<a href="http://dolwms.dol.go.th/tvwebp/" class="bt layout btn btn-success btn-xs">land layout</a>';
	$message[] = '	</td>';
	$message[] = '	<td class="td3">';
	$message[] = '		<a href="http://property.treasury.go.th/pvmwebsite/index.asp" class="bt lve btn btn-success btn-xs">Land value estimate</a>';
	$message[] = '	</td>';
	$message[] = '	<td class="td4">';
	$message[] = '		<a href="http://asset.led.go.th/report_new/reports.asp" class="bt led btn btn-success btn-xs">LED Sale report</a>';
	$message[] = '	</td>';
	$message[] = '	<td class="td5">';
	$message[] = '		<a href="https://www.google.co.th/maps" class="bt google btn btn-success btn-xs">Google map</a>';
	$message[] = '	</td>';	
	$message[] = '	<td class="td6">';
	$message[] = '		<a href="http://example.com/getLinkContent.CRON.php?console" class="bt linkchecker btn btn-success btn-xs">Link Checker</a>';
	$message[] = '	</td>';	
	$message[] = '</tr>';
	$message[] = "</table>";
	$message[] = "</body></html>";	
	mail($to, $subject, implode($message), implode(PHP_EOL, $headers));
}

function notifyError($txt ,$subject = ''){
	if(empty($subject)){
		$subject = 'Error notify';
	}	
	$to      = 'someone@gmail.com'; //change this
	// Headers
	$headers[] = 'From: Digital Asset Management System <mailer@localhost>';
	$headers[] = 'X-Mailer: PHP/' . phpversion();
	$headers[] = "MIME-Version: 1.0";
	$headers[] = "Content-Type: text/html; charset=utf-8";
	$message[] = '<html><body class="body1">';
	$message[] = '<table cellpadding="2" cellspacing="1" border="1"  class="table1">';
	$message[] = '<tr class="tr1">';
	$message[] = '<td class="td1">';
	$message[] = nl2br($txt);
	$message[] = '</td>';
	$message[] = '</tr>';
	$message[] = "</table>";
	$message[] = '<table cellpadding="2" cellspacing="1" border="1" class="table2">';
	$message[] = '<tr class="tr2">';
	$message[] = '	<td class="td2">';
	$message[] = '		<a href="http://dolwms.dol.go.th/tvwebp/" class="bt btn btn-success layout btn-xs">land layout</a>';
	$message[] = '	</td>';
	$message[] = '	<td class="td3">';
	$message[] = '		<a href="http://property.treasury.go.th/pvmwebsite/index.asp" class="bt lve btn btn-success btn-xs">Land value estimate</a>';
	$message[] = '	</td>';
	$message[] = '	<td class="td4">';
	$message[] = '		<a href="http://asset.led.go.th/report_new/reports.asp" class="bt led btn btn-success btn-xs">LED Sale report</a>';
	$message[] = '	</td>';
	$message[] = '	<td class="td5">';
	$message[] = '		<a href="https://www.google.co.th/maps" class="bt google btn-xs">Google map</a>';
	$message[] = '	</td>';	
	$message[] = '	<td class="td6">';
	$message[] = '		<a href="http://example.com/getLinkContent.CRON.php?console" class="bt btn btn-success linkchecker btn-xs">Link Checker</a>';
	$message[] = '	</td>';	
	$message[] = '	<td class="td7">';
	$message[] = '		<a href="http://example.com/map/" class="bt btn btn-success linkchecker btn-xs">Asset Map</a>';
	$message[] = '	</td>';
	$message[] = '	<td class="td8">';
	$message[] = '		<a href="http://example.com/view.php" class="bt btn btn-success linkchecker btn-xs">Asset Text</a>';
	$message[] = '	</td>';	
	$message[] = '</tr>';
	$message[] = "</table>";
	$message[] = "</body></html>";
	mail($to, $subject, implode($message), implode(PHP_EOL, $headers));
}

//old method deplicate
/*function genAssetID($url = ''){
	$url = strtolower($url);
	$url = setCdnUrl($url);
	if(!empty($url)){		
		return crc32($url);
	}else{
		return false;
	}	
}*/

function genAssetID($url = ''){
    //init
    $return  = false;
    $url = strtolower($url);
    $url = setCdnUrl($url);
    if(!empty($url)){
        $url = explode('?', $url);    
        if(isset($url[1])){
            $params = str_replace('&amp;', '&', $url[1]);
            $params = explode('&', $params);
            foreach($params as $param){
                $keys = explode('=', $param);
                if(sizeof($keys) == 2){
                    $assets[$keys[0]] =  $keys[1];
                }
            }
        }
        //law_suit_no, law_suit_year, deed_no, addrno
        if(isset($assets['law_suit_no']) && $assets['law_suit_no'] <> '' && isset($assets['law_suit_year']) && $assets['law_suit_year'] <> '' && isset($assets['deed_no']) && $assets['deed_no'] <> '' && isset($assets['addrno']) && $assets['addrno'] <> ''){
            $asset = $assets['law_suit_no'].$assets['law_suit_year'].$assets['deed_no'].$assets['addrno'];
            $return = crc32($asset);
        }      
	}
    return $return;
}

function genHash($id = ''){
	if(!empty($id)){
		$hasher = crc32($id);
		$hasher = crc32(date("Y-m-d H:i")+$hasher);
		return $hasher;
	}else{
		return 0;
	}	
}

function chkHash($hash = '', $id = ''){
	if(!empty($id) AND !empty($hash)){
		$hasher = crc32($id);
		//-1 min
		$hasher1 = crc32(date("Y-m-d H:i",strtotime("-1 minutes"))+$hasher);
		//0 min
		$hasher2 = crc32(date("Y-m-d H:i")+$hasher);
		//+1 min
		$hasher3 = crc32(date("Y-m-d H:i",strtotime("+1 minutes"))+$hasher);
		if($hash == $hasher1 OR $hash == $hasher2 OR $hash == $hasher3){
			return true;
		}		
	}else{
		return false;
	}	
}

function strpos_r($haystack, $needle){
    if(strlen($needle) > strlen($haystack))
        trigger_error(sprintf("%s: length of argument 2 must be <= argument 1", __FUNCTION__), E_USER_WARNING);

	$chkHay = $haystack;
    $seeks = array();
    while($seek = strrpos($haystack, $needle))
    {
        array_push($seeks, $seek);
        $haystack = substr($haystack, 0, $seek);
    }
	$myst = substr($chkHay, 0, strlen($needle));
	if($myst == $needle){
		array_push($seeks, 0);
	}
	$seeks = array_reverse($seeks);
    return $seeks;
}

//$end_index = number or (+)number
//$cutMode = 'M' = map
//$cutMode = 'R' = rip 
//$cutMode = 'C' = cut 
//$startMode && $endMode 'L','R'
function BetweenWord($haystack, $startword, $endword, $start_index = 0, $end_index = 0, $startMode = 'L', $endMode = 'R', $cutMode = 'C'){
	$lenStartword = strlen($startword);
	$lenEndword   = strlen($endword);
	$lenHaystack  = strlen($haystack);
	$return = '';
	if($end_index[0] == '+'){
		$end_index_relative = substr($end_index, 1) +0; //convert to int
		$end_index = findAbsoluteIndex($haystack, $startword, $endword, $start_index, $end_index_relative, $startMode, $endMode);
	}
	if($lenStartword > $lenHaystack OR $lenEndword > $lenHaystack){
		$return = '';//__LINE__;
	}else{
		$arrStart = strpos_r($haystack, $startword);
		$arrEnd   = strpos_r($haystack, $endword);		
		if(!isset($arrStart[$start_index]) OR !isset($arrEnd[$end_index])){
			return '';
		}
		$startMode   = strtoupper($startMode);
		switch ($startMode) {
			case 'L': //start left
				$s = $arrStart[$start_index];		
				break;
			case 'R': //start right
				$s = $arrStart[$start_index] + $lenStartword;
				break;
			default:
				$return = '';//__LINE__;
		}
		$endMode   = strtoupper($endMode);
		switch ($endMode) {
			case 'L': //end left
				$e = $arrEnd[$end_index];				
				break;
			case 'R': //end right
				$e = $arrEnd[$end_index] + $lenEndword;
				break;
			default:
				$return = '';//__LINE__;
		}
		$cutMode   = strtoupper($cutMode);
		if(($return !== false) AND ($e > $s)){
			switch ($cutMode) {
				case 'C':				
					$return = substr($haystack, $s, $e - $s);
					break;
				case 'M': //creat string map
					$i = 0;
					foreach($arrStart as $aPos){
						if($i == $start_index){
							$pre = '[[';
							$sup = ']]';
						}else{
							$pre = '[';
							$sup = ']';
						}
						$mapIndex[$aPos]  = $pre.$i.$sup;
						$mapPos[$aPos]    = $pre.$aPos.$sup;
						$mapWord[$aPos]   = $pre.'o'.$sup;
						$i++;
					}
					$j = 0;
					foreach($arrEnd as $bPos){
						if($j == $end_index){
							$pre = '{{';
							$sup = '}}';
						}else{
							$pre = '{';
							$sup = '}';
						}
						$mapIndex[$bPos]  = $pre.$j.$sup;
						$mapPos[$bPos]    = $pre.$bPos.$sup;
						$mapWord[$bPos]   = $pre.'x'.$sup;					
						$j++;
					}
					$return = "<pre>INDEX===========================================".PHP_EOL;
					$returnMapIndex = "[n] = start ,{n} = end".PHP_EOL;		
					ksort($mapIndex); 
					foreach($mapIndex as $v){
						$returnMapIndex .= ' '.$v.' ';
					}
					$return .= $returnMapIndex.PHP_EOL;
					
					$return .= "WORD===========================================".PHP_EOL;					
					$returnMapWord = "[o] = start ,{x} = end".PHP_EOL;
					ksort($mapWord); 
					foreach($mapWord as $v){
						$returnMapWord .= ' '.$v.' ';
					}
					$return .= $returnMapWord.PHP_EOL;
					
					$return .= "POSITION===========================================".PHP_EOL;
					
					$returnMapPos = "[position] = start, {position} = end".PHP_EOL;
					ksort($mapPos); 
					foreach($mapPos as $v){
						$returnMapPos .= ' '.$v.' ';
					}
					$return .= $returnMapPos.PHP_EOL;
					$return .= "===========================================".PHP_EOL;
					$return .= "AGUMENT===========================================".PHP_EOL;
					$return .= "\tStart word = ".$startMode.PHP_EOL;					
					$return .= "\tEnd word = ".$endMode.PHP_EOL;
					$return .= "\tStart index = ".$start_index.PHP_EOL;
					$return .= "\tEnd index = ".$end_index.PHP_EOL;					
					$return .= "\tMap word = ".$cutMode.PHP_EOL;
					
					$return .= "Found===========================================".PHP_EOL;
					$return .= "\tFound \"".$startword.'" = '.sizeof($arrStart).PHP_EOL;
					$return .= "\tFound \"".$endword.'" = '.sizeof($arrEnd).PHP_EOL;
					$return .= "===========================================EOF</pre>";
					break;
				case 'R': //remove between word from haystack
					$before = substr($haystack, 0, $s);
					$after 	= substr($haystack, $e, $lenHaystack - $e);
					$return = $before.$after;
					break;
				default:
					$return = '';//__LINE__;
			}
		}else{
			$return = '';//__LINE__;
		}
	}
	return $return;	
}


function arrStrpos($haystack, $needle , $side = 'L'){
	$lastPos = 0;
	$positions = array();
	while (($lastPos = strpos($haystack, $needle, $lastPos))!== false) {
		if($side == 'L' OR $side == 'l' ){
			$positions[] = $lastPos;
			$lastPos = $lastPos + strlen($needle);
		}else{ //$side == 'r'
			$lastPos = $lastPos + strlen($needle);
			$positions[] = $lastPos;	
		}
	}
	return $positions;
}
//$end_index = number **this is relative from start word
//$cutMode = 'M' = map
//$cutMode = 'R' = rip 
//$cutMode = 'C' = cut 
//$startMode && $endMode 'L','R'
function findAbsoluteIndex($haystack, $startword, $endword, $start_index = 0, $end_index_relative = 0, $startMode = 'L', $endMode = 'R'){
	$eIndex = $end_index_relative;
	$sIndex = $start_index;
	$arrStart = arrStrpos($haystack, $startword, $startMode);
	
	if(!isset($arrStart[$sIndex])){
		return '';
	}
	$trashHaystack = substr($haystack, 0, $arrStart[$sIndex]);
	$trashIndex = substr_count($trashHaystack, $endword);
	$haystack = substr($haystack, $arrStart[$sIndex]);
	$arrEnd   = arrStrpos($haystack, $endword, $endMode);	
	if(!isset($arrEnd[$eIndex])){
		return '';
	}
	$end_index_relative = $trashIndex + $eIndex;

	return $end_index_relative;	
}

function CutWord($src, $start = '', $end = '', $startIndex = 0, $endIndex = 0, $startCutSide = 'L', $endCutSide = 'R', $position='abs' ){
	if($start == '' OR $end == ''){
		//left or right mode
		$return = $src;
	}else{
		$txt = explode($start,$src);
		if(isset($txt[$startIndex]) and !empty($txt[$startIndex])){
			$txt2 = explode($end,$txt[$startIndex]);
		}else{
			$return = '';
		}
		if(isset($txt2[0])){
			$return = trim($txt2[0]);
		}else{
			$return = '';
		}		
	}
	return $return;
}

function getObjAsset($assetId){
	$assetId = intval($assetId);
	$conn = db();
	$q = 'SELECT *,
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
		WHERE asset.id ='.$assetId;
	$result = $conn->query($q);
	if ($conn->affected_rows == 1){
		$row = $result->fetch_object();
		$row->id = $assetId;
		return $row;
	}else{
		return false;
	}
}

function getObjAssetByid($assetId){
	return getObjAsset($assetId);
}

function getObjAssetByurl($assetURL){
	$assetId = genAssetID($assetURL);
	return getObjAsset($assetId);
}

function setCdnUrl($url){
	$url = strtolower($url);	
	$url = str_replace("asset.led.go.th", "1038529402.rsc.cdn77.org", $url);
	$url = str_replace("/newbid2/", "/newbid/", $url);
	if(substr_count($url, 'cdn77') > 0){ //always proxy
		return $url;
	}else{
		return '';
	}	
}

function showDateReport($date ,$format ="d/m/Y"){
	$craftDate = strtotime($date);
	if($craftDate !== false){
		$date = date($format, $craftDate);
	}
	return $date;
}

function showDateTimeReport($date){
	return showDateReport($date ,$format ="d/m/Y H:i:s");
}

function getSecreatKey(){
	$keyUrl = 'http://asset.led.go.th/newbid/asset_search_map.asp';
	$html = file_get_contents_curl($keyUrl);
	$seckey = BetweenWord($html, '#FF0000;"><b>' , '</b>', 0, '+0', 'R', 'L', 'C');
	$seckey = int($seckey);
	if($seckey >= 100000 AND $seckey <= 999999){
		return $seckey;
	}else{
		return false;
	}
}


function showImageReport($imgURL){
	if(!empty($imgURL)){
		$imgURL = str_replace('.rsz.io', '', $imgURL);
		$return = '<img src="'.$imgURL.'" alt="asset image" style="max-width:600px" class="img" style="border:1px #ccc solid;padding:5px;">';	
	}else{
		$return = $imgURL;
	}
	return $return;
}

//convert anything to int
function int($txt){
	$txt = str_replace(',', '', $txt);
	$atxt = explode('.',$txt);
	if(sizeof($atxt) <= 2){
		$returnNumber = '';
		foreach($atxt as $a){
			$returnNumber[] = intval(preg_replace('/[^0-9]+/', '', $a), 10);
		}
		$returnNumberText = implode('.', $returnNumber);
	}else{
		$returnNumberText = $txt;
	}	
	return $returnNumberText;
}

function intSuit($txt){
	$txt = str_replace(',', '', $txt);
	$txt = str_replace('.', '', $txt);
	$returnNumber[] = intval(preg_replace('/[^0-9]+/', '', $txt), 10);
	if(isset($returnNumber[0])){
		$returnNumberText = $returnNumber[0];
	}else{
		$returnNumberText = $txt;
	}
	return $returnNumberText;
}

//convert anything to int
//alias function of int
function getHardInt($txt){
	return int($txt);
}

/*
$dates = array
(
    '0'=> "2013-02-18 05:14:54",
    '1'=> "2013-02-12 01:44:03",
    '2'=> "2013-02-05 16:25:07",
    '3'=> "2013-01-29 02:00:15",
    '4'=> "2013-01-27 18:33:45"
);
find_closest($dates, "2013-02-18 05:14:55");
*/

function getNextUpdate($objAsset, $lastupdate = "2016-01-01 00:00:00"){
	$minimum = 86400; //maximun is  update once per day
	$updateChalenge[] = time() +90*86400; //dummy1
	$updateChalenge[] = time() +90*86400 +1;//dummy2
	if(!empty($objAsset)){
		$date1 = (isset($objAsset->date1) AND strtotime($objAsset->date1) > 0)?$objAsset->date1:'2015-01-13';
		$date2 = (isset($objAsset->date2) AND strtotime($objAsset->date2) > 0)?$objAsset->date2:'2015-01-13';
		$date3 = (isset($objAsset->date3) AND strtotime($objAsset->date3) > 0)?$objAsset->date3:'2015-01-13';
		$date4 = (isset($objAsset->date4) AND strtotime($objAsset->date4) > 0)?$objAsset->date4:'2015-01-13';
		$date5 = (isset($objAsset->date5) AND strtotime($objAsset->date5) > 0)?$objAsset->date5:'2015-01-13';
		$date6 = (isset($objAsset->date6) AND strtotime($objAsset->date6) > 0)?$objAsset->date6:'2015-01-13';
		$date7 = (isset($objAsset->date7) AND !empty($objAsset->date7))?$objAsset->date7:'2015-01-13';
		$date8 = (isset($objAsset->date8) AND !empty($objAsset->date8))?$objAsset->date8:'2015-01-13';
		$date9 = (isset($objAsset->date9) AND !empty($objAsset->date9))?$objAsset->date9:'2015-01-13';
		$date10= (isset($objAsset->date10) AND !empty($objAsset->date10))?$objAsset->date10:'2015-01-13';
		$dates = array(
				$date1,
                date("Y-m-d", strtotime($date1." tomorrow")),
                date("Y-m-d", strtotime($date1." yesterday")),
				$date2,
                date("Y-m-d", strtotime($date2." tomorrow")),
                date("Y-m-d", strtotime($date2." yesterday")),
				$date3,
                date("Y-m-d", strtotime($date3." tomorrow")),
                date("Y-m-d", strtotime($date3." yesterday")),
				$date4,
                date("Y-m-d", strtotime($date4." tomorrow")),
                date("Y-m-d", strtotime($date4." yesterday")),
				$date5,
                date("Y-m-d", strtotime($date5." tomorrow")),
                date("Y-m-d", strtotime($date5." yesterday")),
				$date6,
                date("Y-m-d", strtotime($date6." tomorrow")),
                date("Y-m-d", strtotime($date6." yesterday")),
				$date7,
                date("Y-m-d", strtotime($date7." tomorrow")),
                date("Y-m-d", strtotime($date7." yesterday")),
				$date8,
                date("Y-m-d", strtotime($date8." tomorrow")),
                date("Y-m-d", strtotime($date8." yesterday")),
				$date9,
                date("Y-m-d", strtotime($date9." tomorrow")),
                date("Y-m-d", strtotime($date9." yesterday")),
				$date10,
                date("Y-m-d", strtotime($date10." tomorrow")),
                date("Y-m-d", strtotime($date10." yesterday"))
                );
		foreach($dates as $uDate){
			if(!empty($uDate) AND $uDate <> '0000-00-00'){
				$updateDate[] = $uDate;
			}
		}
		
		$lastupdateTimestamp = strtotime($lastupdate);
		$currentTimestamp = time();
		foreach($updateDate as $day){
            //big random second
            $randomSecond = rand(1, 86400);
            $secondRemain = strtotime($day) - 86400 + $randomSecond;
            if($secondRemain > $currentTimestamp + $minimum AND  $secondRemain > $lastupdateTimestamp){
                $updateChalenge[] = $secondRemain;
            }
		}
		$dateSelected = min($updateChalenge);  
		$nextUpdateTime = date("Y-m-d H:i:s", $dateSelected);
	}else{
		$nextUpdateTime = date("Y-m-d H:i:s", strtotime("+90 days"));
	}
	
	return $nextUpdateTime;
}

//
function mailRow($txt1, $txt2 = '', $txt3 = ''){
	$txt1 = nl2br($txt1);
	$txt2 = nl2br($txt2);
	$txt3 = nl2br($txt3);
	$return  = '<tr class="trmix">';
	if($txt2 <> '' AND $txt3 <> ''){
		$return .= '<td align="right" class="tdleft">'.$txt1."</td>";
		$return .= '<td class="tdmid">'.$txt2.'</td>'; 
		$return .= '<td class="tdright">'.$txt3.'</td>'; 
	}elseif($txt2 == '' AND $txt3 == ''){
		$return .= '<td colspan="3" class="tdcolspan3">'.$txt1."</td>";
	}elseif($txt2 == ''){
		$return .= '<td colspan="2" class="tdcolspan2">'.$txt1."</td>";
		$return .= '<td>'.$txt3.'</td>';		
	}else{
		$return .= '<td align="right" class="tdright">'.$txt1."</td>";
		$return .= '<td colspan="2" align="left"  class="tdleftt">'.$txt2.'</td>';
	}
	$return .= "</tr>";
	return $return;
}

function mailTableHeader(){
	return  '<table class="table tblc">';
}

function mailTableFooter(){
	return "</table>";
}

function getAssetCalendar($day = 0, $month = 0, $year = 0, $tid = 2, $pid = 0, $aid = 0, $nudtee = 0){
    $conn = db();	
	$tid = intval($tid);
	$currentDate = date("Y-m-d");
	/*
	1//land//ที่ดิน//001
	2//home//บ้าน//003
	3//condo//ห้องชุด//002
	*/
	switch ($tid) {
		case 1:
			$tid = ' AND type.id = 1 ';
			break;
		case 2:
			$tid = ' AND type.id = 2 ';
			break;
		case 3:
			$tid = ' AND type.id = 3 ';
			break;
		default: //land
			$tid = ' AND type.id = 1 ';
			break;
	}
	
	$nudtee = intval($nudtee);
	if(empty($nudtee)){
		$nudteeQuery  = '';
	}else{
		if(empty($day)){
			$currentYear = date("Y");
			$currentMonth = date("m");
			if($year == $currentYear AND $month == $currentMonth ){
				$date1 = $currentDate;
			}else{
				$date1 = date("Y-m-d", strtotime($year.'-'.$month.'-1')); //1st day of the month
			}
			$date2 = date("Y-m-t", strtotime($year.'-'.$month.'-1')); //t is last day of the month
			switch ($nudtee) {
			case 1:	
			case 2:
			case 3:
			case 4:
			case 5:
			case 6:
			case 7:
			case 8:
				$nudteeQuery = ' AND `asset`.`date'.$nudtee.'` BETWEEN "'.$date1.'" AND "'.$date2.'" ';
				break;
			default:
				$nudteeQuery = '';
				break;
			}
		}else{
			switch ($nudtee) {
			case 1:
				$nudteeQuery = ' AND date1 >= "'.$currentDate.'" ';
				break;
			case 2:
				$nudteeQuery = ' AND date1 <= "'.$currentDate.'" AND date2 >= "'.$currentDate.'" ';
				break;
			case 3:
				$nudteeQuery = ' AND date2 <= "'.$currentDate.'"  AND date3 >= "'.$currentDate.'" ';
				break;
			case 4:
				$nudteeQuery = ' AND date3 <= "'.$currentDate.'" AND date4 >= "'.$currentDate.'" ';
				break;
			case 5:
				$nudteeQuery = ' AND date4 <= "'.$currentDate.'"  AND date5 >= "'.$currentDate.'" ';
				break;
			case 6:
				$nudteeQuery = ' AND date5 <= "'.$currentDate.'" AND date6 >= "'.$currentDate.'" ';
				break;
			case 7:
				$nudteeQuery = ' AND date6 <= "'.$currentDate.'"  AND date7 >= "'.$currentDate.'" ';
				break;
			case 8:
				$nudteeQuery = ' AND date7 <= "'.$currentDate.'"  AND date8 >= "'.$currentDate.'" ';
				break;
			default:
				$nudteeQuery = '';
				break;
			}
		}
	}
	
	if(empty($day)){
		$date1 = date("Y-m-d", strtotime($year.'-'.$month.'-1')); //1st day of the month
		$date2 = date("Y-m-t", strtotime($year.'-'.$month.'-1')); //t is last day of the month
		$dates = ' AND ( (date1 BETWEEN "'.$date1.'" AND "'.$date2.'" AND date1 >"'.$currentDate.'") OR
								(date2 BETWEEN "'.$date1.'" AND "'.$date2.'" AND date2 >"'.$currentDate.'") OR
								(date3 BETWEEN "'.$date1.'" AND "'.$date2.'" AND date3 >"'.$currentDate.'") OR
								(date4 BETWEEN "'.$date1.'" AND "'.$date2.'" AND date4 >"'.$currentDate.'") OR
								(date5 BETWEEN "'.$date1.'" AND "'.$date2.'" AND date5 >"'.$currentDate.'") OR
								(date6 BETWEEN "'.$date1.'" AND "'.$date2.'" AND date6 >"'.$currentDate.'") OR
								(date7 BETWEEN "'.$date1.'" AND "'.$date2.'" AND date7 >"'.$currentDate.'") OR
								(date8 BETWEEN "'.$date1.'" AND "'.$date2.'" AND date8 >"'.$currentDate.'") )';
	}else{
		$date = date("Y-m-d", strtotime($year.'-'.$month.'-'.$day));
		$dates = ' AND (date1 = "'.$date.'" OR  date2 = "'.$date.'" OR  date3 = "'.$date.'" OR  date4 = "'.$date.'" OR  date5 = "'.$date.'" OR  date6 = "'.$date.'" OR  date7 = "'.$date.'" OR  date8 = "'.$date.'") ';
	}
	
	$pid = intval($pid);
	if($pid > 0){
		$pid = ' AND province.id ='.$pid.' ';
	}else{
		$pid = '';
	}
	
	$aid = intval($aid);
	if($aid > 0){
		$aid = ' AND `asset`.`amphur_id` ='.$aid.' ';
		$pid = ''; //filter on aid is enuff
	}else{
		$aid = '';
	}
	$orderby = ' ORDER BY  `asset`.`date8` ASC ,  `asset`.`date7` ASC ,  `asset`.`date6` ASC ,  `asset`.`date5` ASC ,  `asset`.`date4` ASC ,  `asset`.`date3` ASC ,  `asset`.`date2` ASC ,  `asset`.`date1` ASC ';
	
	//$limit = ' LIMIT 50';
	$limit = '';
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
		WHERE enable = 1 ' .$tid.$nudteeQuery.$pid.$aid.$dates.$orderby.$limit;
	//echo $q;
	$result = $conn->query($q);
	if ($conn->affected_rows > 0){
		while($row = $result->fetch_object()){
			$rows[$row->id] = $row;
		}
		return $rows;
	}else{
		return false;
	}	
}
/**
* Encode in Base32 based on RFC 4648.
* Requires 20% more space than base64  
* Great for case-insensitive filesystems like Windows and URL's  (except for = char which can be excluded using the pad option for urls)
*
* @package default
* @author Bryan Ruiz
**/
class Base32 {

   private static $map = array(
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', //  7
        'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', // 15
        'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', // 23
        'Y', 'Z', '2', '3', '4', '5', '6', '7', // 31
        '='  // padding char
    );
    
   private static $flippedMap = array(
        'A'=>'0', 'B'=>'1', 'C'=>'2', 'D'=>'3', 'E'=>'4', 'F'=>'5', 'G'=>'6', 'H'=>'7',
        'I'=>'8', 'J'=>'9', 'K'=>'10', 'L'=>'11', 'M'=>'12', 'N'=>'13', 'O'=>'14', 'P'=>'15',
        'Q'=>'16', 'R'=>'17', 'S'=>'18', 'T'=>'19', 'U'=>'20', 'V'=>'21', 'W'=>'22', 'X'=>'23',
        'Y'=>'24', 'Z'=>'25', '2'=>'26', '3'=>'27', '4'=>'28', '5'=>'29', '6'=>'30', '7'=>'31'
    );
    
    /**
     *    Use padding false when encoding for urls
     *
     * @return base32 encoded string
     * @author Bryan Ruiz
     **/
    public static function encode($input, $padding = true) {
        if(empty($input)) return "";
        $input = str_split($input);
        $binaryString = "";
        for($i = 0; $i < count($input); $i++) {
            $binaryString .= str_pad(base_convert(ord($input[$i]), 10, 2), 8, '0', STR_PAD_LEFT);
        }
        $fiveBitBinaryArray = str_split($binaryString, 5);
        $base32 = "";
        $i=0;
        while($i < count($fiveBitBinaryArray)) {    
            $base32 .= self::$map[base_convert(str_pad($fiveBitBinaryArray[$i], 5,'0'), 2, 10)];
            $i++;
        }
        if($padding && ($x = strlen($binaryString) % 40) != 0) {
            if($x == 8) $base32 .= str_repeat(self::$map[32], 6);
            else if($x == 16) $base32 .= str_repeat(self::$map[32], 4);
            else if($x == 24) $base32 .= str_repeat(self::$map[32], 3);
            else if($x == 32) $base32 .= self::$map[32];
        }
        return $base32;
    }
    
    public static function decode($input) {
        if(empty($input)) return;
        $paddingCharCount = substr_count($input, self::$map[32]);
        $allowedValues = array(6,4,3,1,0);
        if(!in_array($paddingCharCount, $allowedValues)) return false;
        for($i=0; $i<4; $i++){ 
            if($paddingCharCount == $allowedValues[$i] && 
                substr($input, -($allowedValues[$i])) != str_repeat(self::$map[32], $allowedValues[$i])) return false;
        }
        $input = str_replace('=','', $input);
        $input = str_split($input);
        $binaryString = "";
        for($i=0; $i < count($input); $i = $i+8) {
            $x = "";
            if(!in_array($input[$i], self::$map)) return false;
            for($j=0; $j < 8; $j++) {
                $x .= str_pad(base_convert(@self::$flippedMap[@$input[$i + $j]], 10, 2), 5, '0', STR_PAD_LEFT);
            }
            $eightBits = str_split($x, 8);
            for($z = 0; $z < count($eightBits); $z++) {
                $binaryString .= ( ($y = chr(base_convert($eightBits[$z], 2, 10))) || ord($y) == 48 ) ? $y:"";
            }
        }
        return $binaryString;
    }
}

/*function getProxyUrl($url){
	return 'http://issetc.16mb.com/zyro/0.php?'.Base32::encode($url);
}*/

function getProxyUrl($url){
	//return 'http://www.luciscoffeebar.net/go.php?'.$url;
	return  $url;
}