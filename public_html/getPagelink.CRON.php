<?php
date_default_timezone_set("Asia/Bangkok");
//Script calculate timer
$timer_start = microtime(true);
//what this script for ??
//1.find number of page for each aumphur for each type /land and /home 
//2.find number of asset for each aumphur for each type /land and /home 
//
//result 
//1. store number of asset and page in tbl.page
include("simple_html_dom.php");
include("sharelib.php");

function getNumPageContent($overwrite = 0)
{
	/*****************/
	//param
	$pricemax = 3000000;
	$condopricemax = 1500000;
	$exceptPid = 10; //bangkok
	$arrExceptAid = array(33, 30, 50, 23); //33พระโขนง	30ประเวศ	50สวนหลวง	23บางนา	
	$exceptPricemax = '';	
	/*****************/
	
	$conn = db();
	$html = new simple_html_dom();	
	$dbNow = date("Y-m-d H:i:s");
	$q = 'SELECT 
		page.id,
		page.last_update,
		ifnull(page.asset,0) as asset_amount,
		ifnull(page.page,0) as amount,
		page.next_update,
		page.changed,
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
		FROM page
		LEFT JOIN type ON page.type_id = type.id
		LEFT JOIN province ON page.province_id = province.id
		LEFT JOIN amphur ON page.amphur_id = amphur.id AND province.id = amphur.province_id
		WHERE `page`.`next_update` < "'.$dbNow.'" 
		ORDER BY `page`.`next_update` ASC LIMIT 10';
	echo 'Current query:'.$q."<hr/>";
	$result = $conn->query($q);
	if ($conn->affected_rows > 0){
		while($row = $result->fetch_object()){
			$assets[] = $row;
		}
		foreach($assets as $page){
			$now = date("Y-m-d H:i:s");
			
			$saveFilePath1 = 'asset/'.$page->pen.'/'.$page->aen;
			$saveFilePath2 = 'asset/'.$page->pen.'/'.$page->aen.'/'.$page->ten;
			
			if (!file_exists($saveFilePath1) AND !empty($saveFilePath1)) {
				makeFolder($saveFilePath1, 0755);
			}
			if (!file_exists($saveFilePath2) AND !empty($saveFilePath2)) {
				makeFolder($saveFilePath2, 0755);
			}
			
			$saveFile = 'asset/'.$page->pen.'/'.$page->aen.'/'.$page->ten.'/raw.html';
			$OldsaveFile = 'asset/'.$page->pen.'/'.$page->aen.'/'.$page->ten.'/oldraw.html';
			
			$verifyURLPath = 'http://example.com/'.$saveFile;
			$oldURLPath = 'http://example.com/'.$OldsaveFile;
			// Fect content from URL
			//$url = 'http://asset.led.go.th/newbid/asset_search_province.asp?search_asset_type_id='.$page->tencode.'&search_tumbol=&search_ampur='.$page->aencode.'&search_province='.$page->pencode.'&search_sub_province=&search_price_begin=&search_price_end=&search_bid_date=&page=1';	
			
			if(in_array($page->aid,$arrExceptAid) && ($page->pid == $exceptPid)){
				$pricemax = $exceptPricemax;
			}
			if($page->tid == 3){ //condo price overide again
				$pricemax = $condopricemax;
			}
			$url = 'http://1038529402.rsc.cdn77.org/newbid/asset_search_province.asp?search_asset_type_id='.$page->tencode.'&search_tumbol=&search_ampur='.$page->aencode.'&search_province='.$page->pencode.'&search_sub_province=&search_price_begin=&search_price_end='.$pricemax.'&search_bid_date=&page=1';
			$html_content = file_get_contents_curl($url);
			
			//we use asset count for best predict update and change
			$numResult = hardGetNumResult($html_content);
			if($numResult <= 0){
				$numResult  = 0;
				$pageResult = 0;
			}else{
				$pageResult = ceil($numResult/50);
			}
			$changed = 0;
			$numResultChanged = 0;
			$pageNotUpdate = getRemainpageUpdate();
			/*$allAssetCount = 2054;
			$cronTime = 10;
			$pageLimit = 5;*/
			$delay = 5760; //5760mins = 4 day
			$update_delay = ceil($delay / ($page->changed + 1));
			$update_delay = ceil($update_delay/2);
			$update_delay = $update_delay + rand(1, $update_delay);
			$update_delay = "+".$update_delay." minutes";
			$currentDateTime = date("Y-m-d H:i:s");
			$randomNextDateTime = date("Y-m-d H:i:s", strtotime($update_delay));
            /*if(($numResult <> $page->asset_amount OR $numResult <= 50) AND $numResult > 0 AND $pageResult > 0){*/
			if(($numResult <> $page->asset_amount) AND ($numResult > 0) AND ($pageResult > 0)){
				//save file
				if(file_exists($saveFile) AND $saveFile <> ''){
					if(file_exists($OldsaveFile) AND $OldsaveFile <> ''){
						unlink($OldsaveFile);
					}
					rename($saveFile, $OldsaveFile);
				}
				file_put_contents($saveFile, $html_content);
				//save database
				//tbl.page_q
				/*$q = 'INSERT INTO page_q (page_id, page, last_update) 
				VALUES ('.$page->id.', '.$pageResult.', "'.$currentDateTime.'") 
				ON DUPLICATE KEY 
				UPDATE page = '.$pageResult.',last_update = "'.$currentDateTime.'"';
				$result = $conn->query($q);
				if ($result !== true){
					$notifyError  = mailTableHeader();
					$notifyError .= mailRow('fails query in getPageQ.CRON');
					$notifyError .= mailRow('query :',$q);
					$notifyError .= mailTableFooter();
					dailyError($notifyError);
				}*/
				//tbl.page
				$changed = $page->changed + 1;
				if($changed >= 1){
					//no nore changed > 1
					//changed is 0 or 1
					$changed = 1;
				}
				$numResultChanged = abs($page->asset_amount - $numResult);
				$q = 'UPDATE page
					SET 
					asset ='.$numResult.', 
					page  ='.$pageResult.', 
					changed = '.$changed.',
					last_update = "'.$currentDateTime.'",
					next_update = "'.$randomNextDateTime.'"  
					WHERE id = '.$page->id;
			}else{
				$q = 'UPDATE page SET 
					last_update ="'.$currentDateTime.'", next_update ="'.$randomNextDateTime.'"
					WHERE id = '.$page->id;
			}
			$conn->query($q);
			echo 'Last update['.date("d/m/Y H:i:s",strtotime($page->last_update)).'] Now=>'.$q."<br/>";
			if($numResultChanged > 0){	
				$return = mailTableHeader();
				$return .= mailRow("ที่ตั้ง ", $page->pth."> ".$page->ath);
				$return .= mailRow("ประเภท ", $page->tth);
				$return .= mailRow('จำนวนที่เปลี่ยนแปลง ', $page->asset_amount.' เปลี่ยนเป็น '.$numResult);
				$return .= mailRow('ID หน้ารวมสินทรัพย์ : ', $page->id);	
				$return .= mailRow('<a href="'.$url.'">ลิ้งต้นฉบับ</a>','<a href="'.$verifyURLPath.'">ลิ้งตรวจสอบ</a>','<a href="'.$oldURLPath.'">ข้อมูลเก่า</a>');
				$return .= mailTableFooter();
				$return .= "รายงานฉบับนี้ แสดงความเปลี่ยนแปลง ภาพรวมของ จำนวนสินทรัพทย์ ".PHP_EOL;
				$return .= "ประเภท [ที่ดิน] และ  [ที่ดินพร้อมสิ่งปลูกสร้าง] ที่มีช่วงราคาไม่เกิน 2.5ล้านบาท".PHP_EOL;
				$return .= "สำหรับรายการสินทรัพย์ ในแต่ละรายการ ที่ตรวจพบ ระบบจะดำเนินการติดตาม".PHP_EOL;
				$return .= "ความเปลี่ยนแปลง พร้อมแจ้งความเปลี่ยนแปลง โดยอัตโนมัติ จนกว่าสินทรัพทย์จะขายได้ หรือ ถอนการยึด".PHP_EOL;
			}else{
				$return  = 0;
			}
		}
	}else{
		//no any result update
		$return = false;
	}
	return $return;
}

function getRemainpageUpdate(){
	$conn = db();
	$dbNow = date("Y-m-d H:i:s");
	$q = 'SELECT COUNT(*) as pagecount
		FROM page
		WHERE `page`.`next_update` < "'.$dbNow.'"';
		//echo $q;
	$result = $conn->query($q);
	if ($conn->affected_rows > 0){
		$row = $result->fetch_object();
		return $row->pagecount;
	}else{
		return 0;
	}
}

$result = getNumPageContent();
$pageNotUpdate = getRemainpageUpdate();
$timer_end = microtime(true);
$execution_time = $timer_end - $timer_start;
if($result > 0){
	//no fail hooorayy let's notify
	$notifyMail = $result;
	dailyUpdate($result, 'แจ้งเตือน จำนวนสินทรัพย์เปลี่ยนแปลง');
}elseif($pageNotUpdate > 0){
	$notifyMail  = "จำนวนหน้าที่เหลืออยู่ รอการอัพเดต จำนวน ".$pageNotUpdate." หน้า <br/>";	
	//$notifyMail .= "ระบบอัพเดตจากไฟล์   ". __FILE__ .PHP_EOL;
	$notifyMail .= "เวลาที่ใช้ไป  คือ ".$execution_time.'วินาที'; 
	dailyError($notifyMail, 'gatPageLink report');
}else{
	$notifyMail  = "ตอนนี้การอัพเดต จำนวนสินทรัพทย์ ไม่มี หลงเหลืออยู่แล้ว <br/>";
	//$notifyMail .= "ถ้าพบข้อความนี้บ่อยๆ ควร ลดปริมาณการอัพเดต  ". __FILE__ ." ลง ".PHP_EOL;
	$notifyMail .= "เวลาที่ใช้ไป  คือ ".$execution_time.'วินาที'; 
	dailyError($notifyMail, 'gatPageLink report');
}
echo nl2br($notifyMail);