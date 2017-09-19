<?php
date_default_timezone_set("Asia/Bangkok");
//Script calculate timer
$timer_start = microtime(true);
//what this script for ??
//1.get URL link asset from update Query
//
//result 
//1. store link -> page
//2. reduct amont of page in page_q
include("sharelib.php");

function getRemainpageQUpdate(){
	$conn = db();
	$q = 'SELECT ifnull(SUM( PAGE ),0) AS pagecount, 
	ifnull(COUNT( * ),0) AS pagegroup
	FROM  `page_q` 
	WHERE  `page_q`.`page` >0';
		//echo $q;
	$result = $conn->query($q);
	if ($conn->affected_rows > 0){
		$row = $result->fetch_object();
		return $row;
	}else{
		return array('pagegroup'=>0, 'pagecount'=>0);
	}
}

function getThePage(){
	/*****************/
	//param
	$pricemax = 3000000;
	$condopricemax = 1500000;
	$exceptPid = 10; //bangkok
	$arrExceptAid = array(33, 30, 50, 23); //33พระโขนง	30ประเวศ	50สวนหลวง	23บางนา	
	$exceptPricemax = '';
	/*****************/
		
	$conn = db();
	$q = 'SELECT * FROM `page_q` WHERE page > 0 ORDER BY  `page_q`.`last_update` ASC LIMIT 1';
	$result = $conn->query($q);
	$page = new stdClass();
	if ($conn->affected_rows > 0){
		$row = $result->fetch_object();
		$page_id = $row->page_id;
		$page_no = $row->page;
		echo 'We are scarping page->id = '.$page_id.'<br/>';
		$q = 'SELECT 
			page.id as id,
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
			WHERE page.id = '.$page_id.' LIMIT 1';
		$result = $conn->query($q);
		if ($conn->affected_rows == 1){
			$page = $result->fetch_object();
			echo 'This page is '.$page->tth.'>'.$page->pth.'>'.$page->ath.' on pages on '.$page_no.'<br/>';
			$page->page_no = $page_no;
			if($page_no > 1){
				$pageparam = '&page='.$page_no;
			}else{
				$pageparam = '';
			}
			
			if(in_array($page->aid,$arrExceptAid) && $page->pid == $exceptPid){
				$pricemax = $exceptPricemax;
			}
			if($page->tid == 3){ //condo price overide again
				$pricemax = $condopricemax;
			}
			/*$page->url = 'http://asset.led.go.th/newbid/asset_search_province.asp?search_asset_type_id='.$page->tencode.'&search_tumbol=&search_ampur='.$page->aencode.'&search_province='.$page->pencode.'&search_sub_province=&search_price_begin=&search_price_end=&search_bid_date=&page='.$page->page_no;*/
			$page->url = 'http://1038529402.rsc.cdn77.org/newbid/asset_search_province.asp?search_asset_type_id='.$page->tencode.'&search_tumbol=&search_ampur='.$page->aencode.'&search_province='.$page->pencode.'&search_sub_province=&search_price_begin=&search_price_end='.$pricemax.'&search_bid_date='.$pageparam;
			echo $page->url.'<hr/>';
		}else{
			dailyError('Query get zero result on q:'.$q);
		}
	}else{
		$pageCount = 0;
		$pageNumCount = 0;
		$q = 'SELECT id, page FROM page WHERE changed > 0';
		$result = $conn->query($q);
		if ($conn->affected_rows > 0){
			while($linkQ = $result->fetch_object()){
				$qc = 'INSERT INTO page_q (page_id, page) 
				VALUES ('.$linkQ->id.', '.$linkQ->page.') 
				ON DUPLICATE KEY 
				UPDATE page = '.$linkQ->page;
				$conn->query($qc);
				
				$pageCount++;
				$pageNumCount += $linkQ->page;
				
				$qb = 'UPDATE page
				SET changed = changed - 1
				WHERE id = '.$linkQ->id;
				$conn->query($qb);
			}
			dailyError('ตอนนี้ตาราง เพจ คิว ว่างแล้ว กำลังจะเริ่มรอบใหม่ โดย มีเพจที่ รออยู่ทั้งหมด '.$pageCount.' กลุ่ม จำนวนเพจรวมทั้งหมด '.$pageNumCount.'เพจ','ตาราง เพจ คิว ว่างแล้ว');
		}
		$page = false;
	}
	return $page;
}

function getPageContent($page){
	$return = new stdClass();
	$id 		= $page->id;
	$tth 		= $page->tth;	
	$ten 		= $page->ten;
	$tencode 	= $page->tencode;
	$pth 		= $page->pth;
	$pen 		= $page->pen;
	$pencode 	= $page->pencode;
	$ath 		= $page->ath;
	$aen 		= $page->aen;
	$aencode 	= $page->aencode;
	$page_no 	= intval($page->page_no);
	$url 		= $page->url;
	$saveFile = 'asset/'.$pen.'/'.$aen.'/'.$ten.'/page'.$page_no.'.html';
	$OldsaveFile = 'asset/'.$pen.'/'.$aen.'/'.$ten.'/pageOld'.$page_no.'.html';
	$html_content = file_get_contents_curl($url);
	
	$found_error_content = 0;

	$needle = 'Website is offline';
	$cdnError = substr_count($html_content, $needle); 
	$isVbError1 = substr_count($html_content, '800a0009'); //death word but this is main page and (should) never death
	$isVbError2 = substr_count($html_content, 'vbscript'); //death word...(should) never death
	$found_error_content = $isVbError1 + $isVbError2 + $cdnError;	
	if($found_error_content == 0){	
		if(file_exists($saveFile) AND $saveFile <> ''){
			if(file_exists($OldsaveFile) AND $OldsaveFile <> ''){
				unlink($OldsaveFile);	
			}
			rename($saveFile, $OldsaveFile);
		}
		file_put_contents($saveFile, $html_content);
		if(file_exists($saveFile) AND filesize($saveFile) > 0){
			$return->page_no = $page_no;
			$return->savefile = $saveFile;
			$return->status  = true;
		}else{
			$return->status = false;
		}
	}else{
		//bad content or error
		$return->status = false;
	}	
	return $return;
}

function hardRipLedTable($html){
	$needle = 'ไม่พบข้อมูล';
	$link = false;
	$notFinish = 0;
	$notFinish = substr_count($html, $needle); 
	$html 	= iconv('windows-874','utf-8', $html);
	//find did we already fect empty or finish page?	
	//do check it again
	$notFinish = substr_count($html, $needle) + $notFinish; 
	if($notFinish === 0){
		$start 	= '<!-- BEGIN TABLE_ROW_TEMPLATE -->';
		$end	= '<!-- END TABLE_ROW_TEMPLATE -->';
		
		//$html = between($html,$start,$end);
		$html 	= BetweenWord($html, $start, $end, 0, 0, "R", "L");
		$html	= strtolower($html);	
		$html	= removeHTMLComment($html);
		$delete_this = array(
				'onmouseover="style.cursor=\'hand\'; javascript:style.backgroundcolor=\'#cbfa65\'"',
				'onmouseout="javascript:style.backgroundcolor=\'#ffffff\'"',
				',null',
				",'width=740",
				',height=600',
				',status=yes',
				',toolbar=no',
				',menubar=yes',
				',location=no',
				',scrollbars=yes',
				",resizable=yes'",
				'return false;',
				'&nbsp;'		
			);
		$html = str_replace($delete_this, '', $html);
		//print_r($html);
		preg_match_all("/(\'.*\')/", $html, $links);
		//print_r($links);
		//doLog($html);
		//$baselink = 'http://asset.led.go.th/newbid/';
		$baselink = 'http://1038529402.rsc.cdn77.org/newbid/';
		foreach($links[0] as $alink){
			//start extract link
			$url = buildURLfromLedTR($alink, $baselink);
			if($url != $baselink){
				$link_url[] = $url;
			}
		}
		
		$count = 1;
		while($count <> 0){
			$pattern = array(
				'/((bgcolor|color)|class|style)="[^>]*"/im', 
				'/<!--(.*?)-->/im', 
				'/(width)="([0-9]+)"/im',
				'/(border)="([0-9]+)"/im',
				'/align="(left|right|center)"/',
				'/<font>/im',
				'/<div>/im',
				'/<\/font>/im',
				'/<\/div>/im',
				'/\s+/'		
				); 
			$html = preg_replace($pattern, '', $html, -1, $count);
		}
		//print_r($link_url);
		if(isset($link_url)){	
			$link = getArrayTable($html, $link_url);
		}else{
			$tmpfilename = 'tmp/tmp'.time().'.html';
			file_put_contents($tmpfilename,$html);
			dailyError('Founf invalid resource in html start here http://example.com/'.$tmpfilename, 'Function hardRipLedTable($html) in getLinklist.CRON.php has error');
			$link = false;
		}
	}else{
		$link = true;
	}
	//check data match to url ?
	//multiple by 10 element
	//print_r($link);
	return $link;
}

function getArrayTable($htmlTable, $urls){
	$htmlTable	= strtolower($htmlTable); //double check
	$row 		= substr_count($htmlTable, '</tr>'); //count row
	$collumn 	= substr_count($htmlTable, '</td>'); //count collumn
	if($collumn == $row * 10 AND $row > 0){
		//known pattern table
		$valid = 1;
	}else{
		//unknown pattern need recognize
		$valid = 0;	
	}
	if($valid == 1){
		for($r = 0; $r < $row; $r++){			
			$rowStart 	= '<tr>';
			$rowEnd 	= '</tr>';
			$htmlTableR	= BetweenWord($htmlTable, $rowStart, $rowEnd, $r, $r, 'R', 'L');
			$colStart 	= '<td>';
			$colEnd 	= '</td>';
			$c	= 0; //start add data
			$order		= BetweenWord($htmlTableR, $colStart, $colEnd, $c, $c, 'R','L');$c++;
			$cort_no	= BetweenWord($htmlTableR, $colStart, $colEnd, $c, $c, 'R','L');$c++;
			$type		= BetweenWord($htmlTableR, $colStart, $colEnd, $c, $c, 'R','L');$c++;
			$w400		= BetweenWord($htmlTableR, $colStart, $colEnd, $c, $c, 'R','L');$c++;
			$w100		= BetweenWord($htmlTableR, $colStart, $colEnd, $c, $c, 'R','L');$c++;
			$w			= BetweenWord($htmlTableR, $colStart, $colEnd, $c, $c, 'R','L');$c++;
			$bid_price	= BetweenWord($htmlTableR, $colStart, $colEnd, $c, $c, 'R','L');$c++;
			$tumbon	= BetweenWord($htmlTableR, $colStart, $colEnd, $c, $c, 'R','L');$c++;
			$amphur	= BetweenWord($htmlTableR, $colStart, $colEnd, $c, $c, 'R','L');$c++;
			$province	= BetweenWord($htmlTableR, $colStart, $colEnd, $c, $c, 'R','L');$c=0; //only 1-10 collum only ...
			$assetEnable = 1; //1 is enable
			$table[] = array(					
					"order"	 =>$order,
					"cort_no"=>$cort_no,
					"type"	 =>$type,
					"w400"	 =>$w400,
					"w100"	 =>$w100,
					"w"		 =>$w,
					"bid_price"=>$bid_price,
					"tumbon" =>$tumbon,
					"amphur" =>$amphur,
					"province"=>$province,
					"url" 	 =>$urls[$r],
					"enable" =>$assetEnable
					);
		}
		$return = $table;
	}else{	
		$return = false;
		dailyError('Function getArrayTable return invalid value:'.$htmlTable, 'Unknown pattern need recognize was found ');
	}
	return $return;
}
/*
function getArrayTable($htmlTable, $urls){
	$htmlTable	= strtolower($htmlTable); //double check
	$e 	 		= preg_split('/(<[^>]*[^\/]>)/i', $htmlTable, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	$row 		= substr_count($htmlTable, '</tr>'); 
    $e_num 		= sizeof($e);
	$num_row	= $e_num / 32;
	$valid	 	= $e_num % 32;
	$link_num 	= sizeof($urls);
	//32 element is 1 row ... how we know this ???
	//sample here http://pastebin.com/raw/pPcBBHeQ
	//from this html http://pastebin.com/raw/pF50NdgM
	
	if($valid == 0 AND $link_num == $num_row AND $e_num > 0){
		for($r = 0; $r < $num_row; $r++){
			$valid = 0; //re init validator
			$valid += $e[$r*32+0]=='<tr>'?0:1;
			$valid += $e[$r*32+1]=='<td>'?0:1;
			$order 	= $e[$r*32+2];
			$valid += $e[$r*32+3]=='</td>'?0:1;
			$valid += $e[$r*32+4]=='<td>'?0:1;
			$cort_no= $e[$r*32+5];
			$valid += $e[$r*32+6]=='</td>'?0:1;
			$valid += $e[$r*32+7]=='<td>'?0:1;
			$type 	= $e[$r*32+8];
			$valid += $e[$r*32+9]=='</td>'?0:1;
			$valid += $e[$r*32+10]=='<td>'?0:1;
			$w400 	= $e[$r*32+11];
			$valid += $e[$r*32+12]=='</td>'?0:1;
			$valid += $e[$r*32+13]=='<td>'?0:1;
			$w100 	= $e[$r*32+14];
			$valid += $e[$r*32+15]=='</td>'?0:1;
			$valid += $e[$r*32+16]=='<td>'?0:1;
			$w 		= $e[$r*32+17];
			$valid += $e[$r*32+18]=='</td>'?0:1;
			$valid += $e[$r*32+19]=='<td>'?0:1;
			$bid_price=$e[$r*32+20];
			$valid += $e[$r*32+21]=='</td>'?0:1;
			$valid += $e[$r*32+22]=='<td>'?0:1;
			$tumbon = $e[$r*32+23];
			$valid += $e[$r*32+24]=='</td>'?0:1;
			$valid += $e[$r*32+25]=='<td>'?0:1;
			$amphur = $e[$r*32+26];
			$valid += $e[$r*32+27]=='</td>'?0:1;
			$valid += $e[$r*32+28]=='<td>'?0:1;
			$province=$e[$r*32+29];
			$valid += $e[$r*32+30]=='</td>'?0:1;
			$valid += $e[$r*32+31]=='</tr>'?0:1;
			$table[] = array(					
					"order"	 =>$order,
					"cort_no"=>$cort_no,
					"type"	 =>$type,
					"w400"	 =>$w400,
					"w100"	 =>$w100,
					"w"		 =>$w,
					"bid_price"=>$bid_price,
					"tumbon" =>$tumbon,
					"amphur" =>$amphur,
					"province"=>$province,
					"url" 	 =>$urls[$r],
					"valid"  => $valid
					);
		}
		
		if($valid == 0){
			$return = $table;
		}else{
			$return = false;
		}
	}else{
		$return = false;
	}
	if($return === false){dailyError('Function getArrayTable return invalid value:',$e);}
	return $return;
}
*/

function updatePageQDB($pageID, $updatePageLeft){
	$conn = db();
	if($updatePageLeft <= 0){
		$updatePageLeft = 0;
	}
	$q = 'UPDATE page_q
		SET 
		page = '.$updatePageLeft.',
		last_update = "'.date("Y-m-d H:i:s").'"  
		WHERE page_id = '.$pageID;
	$conn->query($q);
	echo '<hr/>Success updatePageLeft in page Q <br/>Statement is $q :  '.$q.'<hr/>';
	//no need anymore 
	//we update change in function getThePage.
	//once when all pageQ was update
	//after getThePage fect and not found anyq
	//they will pull change number to tbl.pageQ
	//and decrease changed one by one
	/*if($updatePageLeft == 0){
		//change update piority by one
		$q = 'UPDATE page SET changed = changed - 1	WHERE id = '.$pageID;
		$conn->query($q);		
	}*/
	//dailyUpdate('Sucess fect one page from :'.$q);
}

//table reference
/*$ar = array(					
	"order"	 =>$order,
	"cort_no"=>$cort_no,
	"type"	 =>$type,
	"w400"	 =>$w400,
	"w100"	 =>$w100,
	"w"		 =>$w,
	"bid_price"=>$bid_price,
	"tumbon" =>$tumbon,
	"amphur" =>$amphur,
	"province"=>$province,
	"url" 	 =>$urls,
	"valid"  => $valid
)*/
function saveLinkDB($objPage, $saveFile){
	/*****************/
	//param
	$pricemax = 2500000;	
	/*****************/
	$conn = db();
	$objSS = newObjAsset();
	$newData = newObjAsset();
	$return = false;
	$html_content = file_get_contents($saveFile, FILE_USE_INCLUDE_PATH);
	$ars = hardRipLedTable($html_content); //assets resource	
	//print_r($ars);
	if($ars !== true){	
		if(sizeof($ars) > 0 AND is_array($ars)){
			//$updateTime = "+".rand(60,2880)." mins"; //1-48hrs delays
			$return = true;
			$sucessaddCount = 0;
			//$i=0;
			foreach($ars as $ar){
				//echo 'add'.$i;
				//$i++;
				//saveAssetDB
				$objSS->id 			= genAssetID($ar["url"]);				
				$objSS->page_id 	= $objPage->id;
				$objSS->url			= strtolower($ar["url"]);
				$objSS->first_seen	= date("Y-m-d H:i:s");
				$objSS->last_seen	= date("Y-m-d H:i:s");
				$objSS->last_update = date("Y-m-d H:i:s");
				$objSS->next_update = date("Y-m-d H:i:s");
				$orders 			= explode('-',$ar["order"]);
				if(isset($orders[0])){
					$objSS->sale_order_main = intval($orders[0]);
				}else{
					$objSS->sale_order_main = 0;
				}				
				if(isset($orders[1])){
					$objSS->sale_order_sub = intval($orders[1]);
				}else{
					$objSS->sale_order_sub = 0;
				}
				$cort_nos 			= explode('/',$ar["cort_no"]);
				$objSS->law_suit_no 	= trim($cort_nos[0]);
				$objSS->law_suit_year 	= intval($cort_nos[1]);
				$objSS->type_id			= $objPage->tid;
				$objSS->size400w		= $ar["w400"];
				$objSS->size100w		= $ar["w100"];
				$objSS->sizew			= $ar["w"];
				$int_data_rc= str_replace(',','',$ar["bid_price"]);
				$objSS->estimated_price = (int)$int_data_rc;		
				$objSS->tumbon 			= $ar["tumbon"];	
				$objSS->amphur_id 		= $objPage->aid;				
				$objSS->tumbon 			= $ar["tumbon"];
				$objSS->enable 			= $ar["enable"];
				//print_r($objSS);
				
				//check new asset ?
				if(checkIdNotExist($objSS->id)){
					$foundNewAsset = true;
				}else{
					$foundNewAsset = false;
				}
				//check exist in db?
				//id is KEY
				$q = 'INSERT INTO asset (
					id, 
					page_id, 
					url, 
					first_seen, 
					last_seen, 
					last_update, 
					next_update, 
					sale_order_main,
					sale_order_sub,
					law_suit_no,
					law_suit_year,
					type_id,
					size400w,
					size100w,
					sizew,
					estimated_price,
					tumbon,
					amphur_id,
					enable
					)
					VALUES (
					'.$objSS->id.',
					'.$objSS->page_id.', 
					"'.$objSS->url.'", 
					"'.$objSS->first_seen.'",
					"'.$objSS->last_seen.'",
					"'.$objSS->last_update.'",
					"'.$objSS->next_update.'",
					'.$objSS->sale_order_main.',
					'.$objSS->sale_order_sub.',
					"'.$objSS->law_suit_no.'",
					'.$objSS->law_suit_year.',
					'.$objSS->type_id.',
					"'.$objSS->size400w.'",
					"'.$objSS->size100w.'",
					"'.$objSS->sizew.'",
					'.$objSS->estimated_price.',
					"'.$objSS->tumbon.'",
					'.$objSS->amphur_id.',
					'.$objSS->enable.'
					)
				ON DUPLICATE KEY UPDATE 
					url = "'.$objSS->url.'",
                    page_id = "'.$objSS->page_id.'",
					law_suit_no = "'.$objSS->law_suit_no.'",
					law_suit_year = '.$objSS->law_suit_year.',
					size400w = '.$objSS->size400w.',
					size100w ='.$objSS->size100w.',
					sizew = '.$objSS->sizew.',
					type_id = '.$objSS->type_id.',
					tumbon = "'.$objSS->tumbon.'",
					amphur_id = '.$objSS->amphur_id.',	
					last_seen = "'.$objSS->last_seen.'",
					sale_order_main = '.$objSS->sale_order_main.',
					sale_order_sub	= '.$objSS->sale_order_sub.',
					estimated_price	= '.$objSS->estimated_price.',
					enable	= '.$objSS->enable;
				$result = $conn->query($q);
				if ($result === false){
					$notifyerror = 'fails query in saveAssetDB:'.$q;
					dailyError($notifyerror);
					echo $notifyerror.'<br/>';
					$return = false;
				}else{
					$sucessaddCount++;
					if($foundNewAsset){
						$newData = ripAssetlink($objSS);
						if(!empty($newData)){
							echo "new asset found id : ".$objSS->id." url: ".$objSS->url."<br/>";
							$newData->new = $foundNewAsset;
							$newData->tth = $objPage->tth;
							$newData->pth = $objPage->pth;
							updateAsset($objSS, $newData);		
							echo updateMap($objSS->id);
						}else{
							nowError('id:'.$objSS->id.'<br/>savefile:'.$saveFile.'<br/>url:'.$objSS->url, 'Fails add '.$objPage->tth.' > '.$objPage->pth);
						}										
						//$notifyAssetInfo = notifyAssetInfo($newData);
						//nowUpdate($notifyAssetInfo, "New ".$objPage->tth." > ".$objPage->pth);
					}
				}
			}
			if($sucessaddCount > 0){
				echo 'Success add link '.$sucessaddCount.'links into DB<br/>';
			}			
		}
	}else{
		$return = true;
	}		
	return $return;
}

$objPage = getThePage();
if($objPage !== false){
	$getP = getPageContent($objPage);
	if($getP->status === false){
		$notifyerror = 'error when fect url:'.$objPage->url;
		dailyError($notifyerror);
		echo $notifyerror.'<br/>';
	}else{
		//print_r($objPage);
		//print_r($getP->savefile);
		if(saveLinkDB($objPage, $getP->savefile)){
			//success save data to DB			
			$updatePageLeft = $getP->page_no - 1;
			updatePageQDB($objPage->id, $updatePageLeft);
			$pageQNotUpdate = getRemainpageQUpdate();
			$notifyMail  = 'จำนวนหน้า ที่เหลืออยู่คิว รอการดึงลิ้ง จำนวน  '.$pageQNotUpdate->pagegroup.' กลุ่ม จำนวนเพจรวมทั้งหมด '.$pageQNotUpdate->pagecount."เพจ ".PHP_EOL;	
			$notifyMail .= "ระบบอัพเดตจากไฟล์   ". __FILE__ . PHP_EOL;
			//finish capture time
			$timer_end = microtime(true);
			$execution_time = $timer_end - $timer_start;
			$notifyMail .= "เวลาที่ใช้ไป  คือ ".$execution_time.'วินาที'; 
			dailyError($notifyMail, 'gatLinkList report');			
			echo nl2br($notifyMail);
		}else{
			//some time error brcause CDN77 was fect invalid page and web site was offline ...bammmm we do it again lol
			$notifyerror = 'saveLinkDB fails on $getP->savefile = http://example.com/'.$getP->savefile;
			dailyError($notifyerror);
			echo $notifyerror.'<br/>';
		}
	}
}else{
	$timer_end = microtime(true);
	$execution_time = $timer_end - $timer_start;
	$notifyMail  = "ตอนนี้สินทรัพย์  ในคิว ทั้งหมด ได้ทำการดึงข้อมูลเบื้องต้นครบถ้วนแล้ว ควรลด ปริมาณการอัพเดต  ". __FILE__ ." ลง". PHP_EOL;
	$notifyMail .= "เวลาที่ใช้ไป  คือ ".$execution_time."วินาที".PHP_EOL; 
	dailyError($notifyMail, 'gatLinkList report');
	echo nl2br($notifyMail);
}