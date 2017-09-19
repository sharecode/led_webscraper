<?php
include("simple_html_dom.php");
include("sharelib.php");
include("db.php");

function getAndSavePageContent($AllPage, $overwrite = 0)
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
	$page 		= intval($AllPage->page);
	$saveFile = 'asset/'.$pen.'/'.$aen.'/'.$ten.'/page'.$page.'.html';
	if(!file_exists($saveFile) OR filesize($saveFile) == 0 OR filesize($saveFile) == NULL OR $overwrite == 1){
		$url = 'http://asset.led.go.th/newbid/asset_search_province.asp?search_asset_type_id='.$tencode.'&search_tumbol=&search_ampur='.$aencode.'&search_province='.$pencode.'&search_sub_province=&search_price_begin=&search_price_end=&search_bid_date=&page='.$page;
		echo $url."<br/>\n";
		if($overwrite == 1){
			unlink($saveFile);	
		}
		$html_content = file_get_contents_curl($url);		
		//$html_content = iconv("UTF-8", "windows-874", $html_content);
		//$html_content = str_replace('windows-874','utf-8',$html_content);
		file_put_contents($saveFile, $html_content);
		//debug
		//echo $saveFile." ".$saveFileHash."<br/>";
		//echo $hashFile." ".$hashValue."<br/>";
	}
	if(file_exists($saveFile) AND filesize($saveFile) > 0){
		$page--;
		return true;
	}else{
		return false;
	}
}

function isDataUpdate()
{
	$conn = db();
	$q = 'SELECT 
		page.id,
		page.page as amount,
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
		WHERE page.changed = 1 
		ORDER BY page.last_update DESC LIMIT 3';
	$result = $conn->query($q);
	if ($conn->affected_rows > 0){
		while($row = $result->fetch_object()){
			$AllPage[] = $row;
		}
	}
	return $AllPage;
}

function updateDBPage($pageID, $pageNo){
	if($pageNo > 0 ){
		
	}else{
		//no page left
	}
}

$province = array(
			"surat-thani"=>"สุราษฎร์ธานี"
			); 
$provinceEng = array_keys($province);
$assetTypeNum = array_keys($assetType);
$html_content = '';
$html = new simple_html_dom();

echo htmlHeader();
foreach($provinceEng as $p){
	foreach($assetTypeNum as $t){
		$isGet = getContent($t,$p,$province[$p]);
		if($isGet === false){
			//force update again
			$isGet = getContent($t,$p,$province[$p],1);
			echo $assetType[$t]." >".$province[$p]." updating.<br/>\n";
		}
		$updated = isDataUpdate($t,$p,$province[$p]);
		if($updated){
			$isGet = getContent($t,$p,$province[$p]);
			//load file from cache
			$content = getRaw('asset/'.$p.'/'.$t.'/raw.html');
			$html->load($content);
			echo "\t&nbsp;&nbsp;&nbsp;".$assetType[$t]." >".$province[$p]." updated.<br/>\n";
		}else{
			getContent($t,$p,$province[$p],1);
			$updated = isDataUpdate($t,$p,$province[$p]);
			if(!$updated){
				echo "\t&nbsp;&nbsp;&nbsp;".$assetType[$t]." >".$province[$p]." updated <b>Fail</b>.<br/>\n";
			}else{				
				$content = getRaw('asset/'.$p.'/'.$t.'/raw.html');
				$html->load($content);
			}
		}
		$isPut = createAssetFile($t,$p,$html);
		if($updated AND $isPut){			
			echo "\t&nbsp;&nbsp;&nbsp;".$assetType[$t]." >".$province[$p]." Fect success.<br/>\n";
		}else{
			echo "\t&nbsp;&nbsp;&nbsp;".$assetType[$t]." >".$province[$p]." Fect fail.<br/>\n";
		}
	}
}
echo htmlFooter();