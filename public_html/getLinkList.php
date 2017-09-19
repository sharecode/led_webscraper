<?php
include("sharelib.php");
include("db.php");

function getThePage(){
	$conn = db();
	$q = 'SELECT * FROM `page_q` LIMIT 1';
	$result = $conn->query($q);
	$page = new stdClass();
	if ($conn->affected_rows > 0){
		$row = $result->fetch_object();
		$page_id = $row->page_id;
		$page_no = $row->page;
	
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
			$page->page_no = $page_no;
			$page->url = 'http://asset.led.go.th/newbid/asset_search_province.asp?search_asset_type_id='.$page->tencode.'&search_tumbol=&search_ampur='.$page->aencode.'&search_province='.$page->pencode.'&search_sub_province=&search_price_begin=&search_price_end=&search_bid_date=&page='.$page->page_no;
		}else{
			doLog('Query get zero result on q:'.$q);
		}
	}else{
		doLog('no update Q in DB fx.getThePage file.getLinkList.php');
	}
	return $page;
}

function getPageContent($page, $overwrite = 0)
{
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
	$saveFile = './asset/'.$pen.'/'.$aen.'/'.$ten.'/page'.$page_no.'.html';
	if(!file_exists($saveFile) OR filesize($saveFile) == 0 OR filesize($saveFile) == NULL OR $overwrite == 1){
		if($overwrite == 1){
			unlink($saveFile);	
		}
		$html_content = file_get_contents_curl($url);
		file_put_contents($saveFile, $html_content);
	}
	if(file_exists($saveFile) AND filesize($saveFile) > 0){
		$return->page_no = $page_no;
		$return->savefile= $saveFile;
		$return->status  = true;
	}else{
		$return->status = false;
	}
	return $return;
}

function hardRipLedTable($html)
{
	$html 	= iconv('windows-874','utf-8', $html);
	$start 	= '<!-- BEGIN TABLE_ROW_TEMPLATE -->';
	$end	= '<!-- END TABLE_ROW_TEMPLATE -->';
	$html 	= between($html,$start,$end);
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
	preg_match_all("/(\'.*\')/", $html, $links);
	//doLog($html);
	$baselink = 'http://asset.led.go.th/newbid/';
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
	$link = getArrayTable($html, $link_url);
	//check data match to url ?
	//multiple by 10 element
	return $link;
}

function getArrayTable($htmlTable, $urls)
{
	$htmlTable	= strtolower($htmlTable); //double check
	$e 	 		= preg_split('/(<[^>]*[^\/]>)/i', $htmlTable, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    $e_num 		= sizeof($e);
	$num_row	= $e_num / 32;
	$valid	 	= $e_num % 32;
	$link_num 	= sizeof($urls);
	//32 element is 1 row ... how we know this ???
	//sample here http://pastebin.com/raw/pPcBBHeQ
	//from this html http://pastebin.com/raw/pF50NdgM
	if($valid == 0 AND $link_num == $num_row){
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
	if($return === false){doLog('Function getArrayTable return invalid value:',$e);}
	return $return;
}

function updatePageQDB($pageID, $pageResult)
{
	$conn = db();
	if($pageResult > 0){
		$q = 'UPDATE page_q
			SET 
			page = '.$pageResult.',
			last_update="'.date("Y-m-d H:i:s").'"  
			WHERE page_id = '.$pageID;
		$conn->query($q);
		return true;		
	}else{
		$q = 'DELETE FROM page_q WHERE page_id = '.$pageID;
		$conn->query($q);
		return false;	
	}
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
function saveLinkDB($objPage, $htmlPath)
{
	$conn = db();
	$objSS = new stdClass();
	$html_content = file_get_contents($htmlPath, FILE_USE_INCLUDE_PATH);
	$ars = hardRipLedTable($html_content); //assets resource
	$updateTime = "+".rand(60,2880)." mins"; //1-48hrs delays
	$return = true;
	foreach($ars as $ar){
		//saveAssetDB
		if($ar["valid"] == 0){
			$objSS->id 			= genAssetID($ar["url"]);	
			$objSS->page_id 	= $objPage->id;
			$objSS->url			= strtolower($ar["url"]);
			$objSS->first_seen	= date("Y-m-d H:i:s");
			$objSS->last_seen	= date("Y-m-d H:i:s");
			$objSS->last_update = date("Y-m-d H:i:s");
			$objSS->next_update = date("Y-m-d H:i:s", strtotime($updateTime));
			$orders 	= explode('-',$ar["order"]);
			$objSS->sale_order_main = intval($orders[0]);
			$objSS->sale_order_sub 	= intval($orders[1]);
			$cort_nos 	= explode('/',$ar["cort_no"]);
			$objSS->law_suit_no 	= trim($cort_nos[0]);
			$objSS->law_suit_year 	= intval($cort_nos[1]);
			$objSS->type_id			= $objPage->tid;
			$objSS->size400w		= $ar["w400"];
			$objSS->size100w		= $ar["w100"];
			$objSS->sizew			= $ar["w"];
			$int_data_rc= str_replace(',','',$ar["bid_price"]);
			$objSS->estimated_price = $int_data_rc;		
			$objSS->tumbon 			= $ar["tumbon"];	
			$objSS->amphur_id 		= $objPage->aid;
			$objSS->tumbon 			= $ar["tumbon"];
			
			//check exist in db?
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
				amphur_id
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
				'.$objSS->amphur_id.'
				)
			ON DUPLICATE KEY UPDATE 
				last_seen 	= "'.$objSS->last_seen.'",
				sale_order_main = '.$objSS->sale_order_main.',
				sale_order_sub	= '.$objSS->sale_order_sub.',
				estimated_price	= '.$objSS->estimated_price;
			$result = $conn->query($q);
			if ($conn->affected_rows == 0){
				doLog('fails query in saveAssetDB:'.$q);
				$return = false;
			}
		}else{
			doLog('found not valis fect page:'.$ar["url"]);
			$return = false;
		}		
	}	
	return $return;
}

$objPage = getThePage();
$getPageContentResult = getPageContent($objPage);
$getP = $getPageContentResult;
if($getP->status === false){
	dolog('error when fect url:'.$objPage->url);
}else{
	if(saveLinkDB($objPage, $getP->savefile)){
		//success save data to DB
		$getP->page_no--;
		if(updatePageQDB($objPage->id, $getP->page_no)){
			$notify  = "Page update\r\n";
			$notify .= $objPage->pth.">".$objPage->ath.">".$objPage->tth."\r\n".$objPage->url."\r\n";
			notifyUpdate($notify);
		}
	}else{
		doLog('saveLinkDB fails on $getP->savefile = '.$getP->savefile);
	}
}


//test code
/*function testgetPageContent($AllPage, $overwrite = 0)
{
	$id 		= $AllPage->id;
	$tth 		= $AllPage->tth;	
	$ten 		= $AllPage->ten;
	$tencode 	= $AllPage->tencode;
	$pth 		= $AllPage->pth;	
	$pen 		= $AllPage->pen;
	$pencode 	= $AllPage->pencode;
	$ath 		= $AllPage->ath;
	$aen 		= $AllPage->aen;
	$aencode 	= $AllPage->aencode;
	$page_no 	= intval($AllPage->page_no);
	$url 		= $AllPage->url;
	$html_content = file_get_contents_curl($url);
	return $html_content;
}

$page = getThePage();
$content = getPageContent($page);
$content = file_get_contents('./asset/bangkok/khlong-sam-wa/land/page1.html', FILE_USE_INCLUDE_PATH);
$c = hardRipLedTable($content);
print_r($c);*/