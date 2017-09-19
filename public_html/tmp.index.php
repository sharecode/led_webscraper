<?php
include("simple_html_dom.php");
include("sharelib.php");
include("assettype.php"); //array $assetType
include("province.php"); //array $province
include("amphur.php"); //array $amphur

function getContent($assetType,$provinceEN,$provinceTH,$overwrite = 0)
{
	$saveFile = 'asset/'.$provinceEN.'/'.$assetType.'/raw.html';
	$hashFile = 'asset/'.$provinceEN.'/'.$assetType.'/hash.html';
	if(!file_exists($saveFile) OR filesize($saveFile) == 0 OR filesize($saveFile) == NULL OR $overwrite == 1){
		// Create DOM from URL or file
	//	$provinceTH = iconv("windows-874","UTF-8", $provinceTH);
		$provinceTH = iconv("UTF-8", "windows-874", $provinceTH);
		$provinceTH = urlencode($provinceTH);
		$url = 'http://asset.led.go.th/newbid/asset_search_province.asp?search_asset_type_id='.$assetType.'&search_tumbol=&search_ampur=&search_province='.$provinceTH.'&search_sub_province=&search_price_begin=&search_price_end=&search_bid_date=';
		echo $url."<br/>\n";
		if($overwrite == 1){
			unlink($saveFile);
			unlink($hashFile);			
		}
		$html_content = file_get_contents_curl($url);		
		//$html_content = iconv("UTF-8", "windows-874", $html_content);
		//$html_content = str_replace('windows-874','utf-8',$html_content);
		file_put_contents($saveFile, $html_content);
		$hashValue = hash('md5', $html_content);
		file_put_contents($hashFile, $hashValue);
		//debug
		//echo $saveFile." ".$saveFileHash."<br/>";
		//echo $hashFile." ".$hashValue."<br/>";
	}
	if(file_exists($saveFile) AND filesize($saveFile) > 0){
		return true;
	}else{
		return false;
	}
}

function getRaw($path)
{
	if(file_exists($path)){
		$html_content = file_get_contents($path);
		//$html_content = iconv("UTF-8", "windows-874", $html_content);
		//$html_content = str_replace('windows-874','utf-8',$html_content);		
		return $html_content;
	}else{
		return false;
	}
}

function isDataUpdate($assetType,$provinceEN,$provinceTH)
{
	$saveFile = 'asset/'.$provinceEN.'/'.$assetType.'/raw.html';
	$hashFile = 'asset/'.$provinceEN.'/'.$assetType.'/hash.html';
	if(!file_exists($saveFile) OR filesize($saveFile) == 0 OR filesize($saveFile) == NULL){
		$update = false;
	}
	if(!file_exists($hashFile) OR filesize($hashFile) == 0 OR filesize($hashFile) == NULL){
		$update = false;
	}
	$saveFileHash = hash_file('md5', $saveFile);
	$hashFileHash = file_get_contents($hashFile);
	//debug
	//echo $saveFile." ".$saveFileHash."<br/>";
	//echo $hashFile." ".$hashFileHash."<br/>";
	if($saveFileHash == $hashFileHash){
		$update = true;
	}else{
		$update = false;
	}
	return $update;
}

function ledHeader($objHtml)
{
	$tmpHtml = $objHtml->find('table', 3)->plaintext;
	//$tmpHtml = iconv('windows-874','utf-8',$tmpHtml);
	$tmpHtml = stripInvisible($tmpHtml);
	return $tmpHtml;
}

function ledTable($objHtml)
{	//body > table:nth-child(4) > tbody > tr > td:nth-child(1) > table:nth-child(1)
	$tmpHTML1 = $objHtml->find('table', 4)->find('table', 1);
	$tmpHTML2 = $tmpHTML1->find('tr[onclick]');
	$htmlTable = '
	<table class="asset">'."
	<tr>
		<td>ลำดับที่ การขาย </td>\n
		<td>หมายเลขคดี </td>\n
		<td>ประเภททรัพย์ </td>\n
		<td>ไร่ (400ตรว.)</td><td>งาน (100ตรว.)</td><td>ตารางวา. </td>\n
		<td>ราคาประเมิน </td>\n
		<td>ตำบล </td>\n
		<td>อำเภอ </td>\n
		<td>จังหวัด </td>\n
	</tr>\n";
	$trHtml = '';
	foreach($tmpHTML2 as $tr) 
	{
		$trHtml = '<tr>';
		$quote = 'single';
		$betweenWord = between($tr->outertext,"window.open('","')");
		if($betweenWord == ''){
			$betweenWordTMP = between($tr->outertext,'window.open("','")');
			$quote = 'double';
		}
		if($quote == 'single'){
			$urlParam = between($betweenWord, 'asset_open.asp?',"',");
		}else{
			$urlParam = between($betweenWord, 'asset_open.asp?','",');
		}
		
		$urlParam = explode('&', $urlParam);
		$paramAll = '';
		foreach($urlParam as $assetParam)
		{
			$tmpKey = explode('=',$assetParam);
			if(sizeof($tmpKey) == 2)
			{
				$param = '&'.trim($tmpKey[0]).'='.rawurlencode(trim($tmpKey[1]));
				$paramAll .= $param;
			}else{
				die($assetParam);
			}	
		}
		$paramAll = substr($paramAll, 1);

		$row = $tr->find('td');
		$tdHtml = '';
		$numTd = 0;
		foreach($row as $td)
		{
			$numTd++;
			$assetDetails = trim($td->plaintext);
			//$assetDetails = iconv('windows-874','utf-8',$assetDetails);
			$assetDetails = stripInvisible($assetDetails);
			if($numTd == 2){
				//LED Cort number
				$tdHtml = $tdHtml.'    <td><a href="http://asset.led.go.th/newbid/asset_open.asp?'.$paramAll.'" target="_blank">'.$assetDetails."</a></td>\n";
			}else{
				$tdHtml = $tdHtml.'    <td>'.$assetDetails."</td>\n";
			}			
		}
		$trHtml .= "\n".$tdHtml."</tr>\n";
		$htmlTable .= $trHtml;
	}
	$htmlTable .= '</table>';
	return $htmlTable;
}

function ledNavigator($objHtml)
{
	$pageResult = $objHtml->find('/html/body/table[3]/tbody/tr/td[1]/table[2]/tbody/tr/td[2]',0)->outertext;
	$pageResult = stripInvisible(between($pageResult,'1/','</div>'));
	$numResult = $objHtml->find('/html/body/table[3]/tbody/tr/td[1]/table[2]/tbody/tr/td[2]/div/font',0)->plaintext;
	$numResult = stripInvisible($numResult);
	//verify
	$verResult = ceil($numResult/50);
	if($pageResult >= 1){
		if($verResult != $pageResult){
			//somthing wrong
			echo ('amount='.$numResult.'<br/>');
			echo ('page  ='.$pageResult.'<br/>');
			echo ('verify='.$verResult.'<br/>');
			die('page navigator malfunction');
		}else{
			//pass
			$navLink = $objHtml->find('/html/body/table[3]/tbody/tr/td[1]/table[2]/tbody/tr/td[3]/div/a[1]',0)->outertext;
		}
	}
	return $navLink;
}



function createAssetFile($assetType, $provinceEN, $objHtml){
	$assetsFile = 'asset/'.$provinceEN.'/'.$assetType.'/assets.html';
	$content  = '';
	$content .= htmlHeader();
	$content .= ledHeader($objHtml);
	$content .= ledTable($objHtml);
	$content .= ledNavigator($objHtml);
	$content .= htmlFooter();
	return file_put_contents($assetsFile, $content);	
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