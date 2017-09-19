<?php
date_default_timezone_set("Asia/Bangkok");
//Script calculate timer
$timer_start = microtime(true);
//what this script for ??
//1.update asset only changed and not SOLD
//2.get URL link asset ONE asset per run
//
//result 
//1. update asset.sold to 1 when found asset sold 
//2. update asset information and notify us 
include("sharelib.php");
$getRemainAssetUpdate = getRemainAssetUpdate();

//direct acess this page 
//__FILE__?console
$postUrl = '';
$postID = 0;
if(isset($_GET["id"]) && $_GET["id"] > 0){
	$assetId = (int)$_GET["id"] + 0;	
	$link = getObjAssetById($assetId);
	$newData = ripAssetlink($link);
	if($link && $newData){
		ob_start();
		updateAsset($link, $newData);
		header( "location: http://example.com/map/index.php?ptype=viewFullListing&reid=".$newData->id );
		ob_end_clean();
		exit(0);
	}	
}elseif( isset($_GET["console"]) && empty($_GET["console"]) ){
	$link = newObjAsset();
	$newData  = false;
    $postID = 0;
	
	//get Data from url
	if(isset($_POST["url"])){
		$postUrl = setCdnUrl($_POST["url"]);
        $postID = (int)$_POST["url"];
        
		$validLink = false;
		if(validLedLink($postUrl) ){            
			$link = getObjAssetByurl($postUrl);
            $link->url = $postUrl; //??? wtf why url was lost from db ????       
			$validLink = true;
		}elseif(checkIdExist($postID)){
			$link = getObjAssetById($postID);
            $link->url = $postUrl; //??? wtf why url was lost from db ????   
			$validLink = true;
		}	
		
		if($validLink){            
			if($link === false){
				//this is not an error
				//incase we found new asset by URL
				//and remember we cannot get new asset via id
				$proxy  = setCdnUrl($postUrl);
				if(!empty($proxy)){
					$link->id  = genAssetID($proxy);
					$link->url = setCdnUrl($proxy);	
				}					
			}
            //printr($link);
			$newData = ripAssetlink($link);
			//printr($newData);
			$showPOIasset = '';
			if($newData === false AND checkIdExist($link->id)){
				$notifyText = 'Invalid input';
				removeAssetFromDB($link);
			}else{
                //printr($link);
                //printr($newData);
				updateAsset($link, $newData);
				$showPOIasset = showPOIasset($newData);
				//$notifyText  = "Asset URL =".$newData->url."<br/>";
				//$notifyText .= "Asset ID  =".$newData->id."<br/>";
			}
		}
	}
		
	//show menu
	echo htmlHeader();
	?>
	<style>
	body{margin-top:40px;}
	#poi{position: absolute;    right: 20px;    top: 50px;    padding: 0;    background: #fff;    border: #ccc solid 1px;text-align: right;}
	#poi img{border: 1px #ccc solid;}
	#main {width: 100%;margin: 0 auto;background-color: #eee;text-align: left;top: 0;left: 0;}
	.simple {width: 100%;    padding: 2px;    margin: 2px auto;    -webkit-box-sizing: content-box;    -moz-box-sizing: content-box;    position: fixed;    box-sizing: content-box;    background: #fff;
    border: 1px #ccc solid;    text-align: right;    margin-right: 10px;    padding-right: 10px;    right: 0;    top: 0;}
	.dim{color:#ccc;}
	.input{width: 60%;    padding: 5px;    font-size: 13px;}
	.btn{width: 100px;padding:5px;font-size: 13px;}
	.notifybox{margin-top: 0;}
	.value{width: 500px;overflow: auto;float: left;white-space: nowrap;}
	.header{color: white;    background: #116929;}
	table#info {border-collapse: collapse;}
	table#info th, table#info td {text-align: left;padding: 5px;}
	table#info tr:nth-child(even){background-color: #f2f2f2}
	td.key {text-align: right;}
	a.unlink{text-decoration:none;color:white;}
	</style>
	<script>
	function loadDoc() {
	  var xhttp = new XMLHttpRequest();
	  var source = "getRandomURL.php";
	  xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById("url").value = this.responseText;
		}
	  };
	  
	  xhttp.open("GET", source, true);
	  xhttp.send();
	}
	function empty() {
		var x;
		x = document.getElementById("url").value;
		if (x == "") {
			return false;
		};
	}
	</script>
	<div id="main">
		<form action="getLinkContent.CRON.php?console" method="post" class="simple">			
			<div class="box">ASSET ID/URL:<input type="text" name="url" value="" class="input" id="url">
				<input type="submit" value="Submit" onClick="return empty()" class="btn btn-success">&nbsp;&nbsp;<button type="button" class="btn btn-info" onclick="loadDoc()">Get random url</button>
			</div>
		</form> 
	</div>
	<br/>
	<?php
	//prepare data is_object 
	//and check is empty URL ? we not allow url error here
	$currentlink = getObjAssetByurl($link->url); //Check final data when no any object return
    //printr($link->url);
	if($currentlink !== false){
		if(is_object($newData) === false){		
			$arrNewdata  = (array)$link;
		}else{
			$arrNewdata  = (array)$newData;
			$arrNewdata["first_seen"] = $currentlink->first_seen;
			$arrNewdata["last_seen"]  = $currentlink->last_seen;
			$arrNewdata["last_update"] = $currentlink->last_update;
			$arrNewdata["next_update"] = $currentlink->next_update;
			$arrNewdata["sold"]  = $currentlink->sold;
			$arrNewdata["enable"] = $currentlink->enable;
		}	
		$arrOlddata   = (array)$link;
		$newKey = array_keys($arrNewdata);
		$oldKey  = array_keys($arrOlddata);
		$keys     = array_unique(array_merge($newKey, $oldKey));
		if(($k = array_search('url', $keys)) !== false){
			unset($keys[$k]);
		}
		$resultTable  = '<table id="url">';
		$resultTable .= '<tr class="header"><td>Before Update</td><td colspan="2">'.$arrNewdata["url"].'</td></tr>';	
		$resultTable .= '<tr class="header"><td>Current Data</td><td colspan="2"><a href="'.$arrOlddata["url"].'" target="_blank" class="unlink">'.$arrOlddata["url"].'</a></td></tr>';	
		$resultTable .= '<table>';
		$resultTable .= '<table id="info">';	
		$resultTable .= '<tr class="header"><td>key</td><td>Before Update</td><td>Current Data</td></tr>';	
		foreach($keys as $key){
			$resultTable .= '<tr>';
			$resultTable .= '<td class="key">'.$key.'</td>';
			$resultTable .= '<td><span class="value">'.$arrOlddata[$key].'</span></td>';
			if($arrOlddata[$key] == $arrNewdata[$key]){
				$resultTable .= '<td class="dim">no change</td>'; //no change
			}else{
				$resultTable .= '<td style="color:blue">'.$arrNewdata[$key].'</td>'; //has change
			}
			$resultTable .= '</tr>';
		}
		//debug
		//$resultTable .= '<tr><td>'.print_r($keys).'</td><td>'.print_r($oldKey).'</td><td>'.print_r($newKey).'</td></tr>';
		$resultTable .= '</table>';
		$resultTable .= $showPOIasset;
	}else{
		if(!empty($postUrl)){echo 'bad link';}
	}
	if(!empty($arrNewdata) AND !empty($arrOlddata)){
		if(!empty($notifyText)){
			?>
			<table class="notifybox"><tr><td colspan="2" style="white-space: nowrap;">
			<?php echo $notifyText; ?>
			</td></tr></table>
			<?php
		}
		echo $resultTable;
		echo htmlFooter();
	}
}else{	
	/*****************/
	//param
	$fectLimit = 50;
	$fectTimeLimit = 400; //second
	/*****************/	
	
	$newData = newObjAsset();
	$link = newObjAsset();
	$conn = db();
	$currentDateTime = date("Y-m-d H:i:s");
	//check all asset was update atleast one time?
	//every asset has deed_no
	//if is null assume it never update
	//need update altest once ....
	//$q = "SELECT *  FROM `asset` WHERE `asset`.`deed_no` IS NULL  ORDER BY  `asset`.`last_update` DESC  LIMIT ".$fectLimit;

	//1st class
	//bangkok nontaburi + samotprakarnnnnnn
	echo "อัพเดต ทรัพย์ ใน กทม,สมุทรปราการ และ นนทบุรี  ราคาไม่เกิน 2.5ล้านบาท...<br/>";
	$q = "SELECT *  FROM `asset` WHERE `asset`.`deed_no` IS NULL  AND `asset`.`amphur_id` < 102  AND `asset`.`next_update` < \"".$currentDateTime."\" ORDER BY `asset`.`amphur_id` ASC LIMIT ".$fectLimit;
	$result = $conn->query($q);	
	//echo $conn->affected_rows;
	if ($conn->affected_rows < 20){
		echo "เนื่องจาก ไม่มีทรัพย์ ใน กทม, สมุทรปราการ และ นนทบุรี เหลือน้อยมาก จึง ค้นหาใหม่ด้วย  ทรัพย์ขนาดใหญ่กว่า 5 ไร่ ...<br/>";
		//2nd class other not update
		$q = "SELECT *  FROM `asset` WHERE `asset`.`enable` = 1 AND `asset`.`size400w` > 5 AND `asset`.`next_update` < \"".$currentDateTime."\" ORDER BY `asset`.`size400w` DESC,`asset`.`size100w` DESC,`asset`.`sizew` DESC LIMIT ".$fectLimit;
		$result = $conn->query($q);
		//echo '$q:'.$q."<br/>";	
		if ($conn->affected_rows < 20){
			/*
			$conn->affected_rows  Return Values ¶
			> 0 is result
			0 is zero result
			-1 is invalid query
			*/
			//ok we update all asset 
			//let's start normal queqe
			$q = 'SELECT *'.  
				 'FROM `asset` '.
				 'where `asset`.`enable` = 1 AND (`asset`.`next_update` < "'.$currentDateTime.'" OR `asset`.`jod` IS NULL OR  `asset`.`jod` = "" OR `asset`.`publish` = "1970-01-01")  ORDER BY `asset`.`next_update` ASC '.
				 'LIMIT '.$fectLimit;
			echo "เนื่องจาก ทรัพย์เหลือ ตามเงื่อนไข ดังกล่าว เหลือน้อยมาก จึง ค้นหาทรัพย์ที่เหลือทั้งหมด อัพเดตใหม่อีกครั้ง...<br/>";
			$result = $conn->query($q);
		}
	}
	echo '$q:'.$q."<br/>";	
	echo 'จำนวนคิวรอดำเนินการ : '.$conn->affected_rows."<hr/>";
	if ($conn->affected_rows > 0){
		$notifyText = '';
		$updateCount = 0;
		$updatebadlink = 0;
		$fectTimeUsage = 0;
		while(($link = $result->fetch_object()) AND ($fectTimeLimit > $fectTimeUsage)){
			//check conflik gen id
			//by gennew id and check with old id
			//dbug($link);
			$tmpId = genAssetID($link->url);
			//echo '$link->id : '. $link->id . '<br/>';
			//echo '$tmpId : '. $tmpId. '<br/>';
			if(!empty($link->id) AND ($link->id > 1) AND ($tmpId > 0) AND !empty($tmpId) AND $tmpId <> $link->id) {
				//1.check idexist
				if(checkIdExist($tmpId)){
					//new id exist just delete old 
					$qDelete = 'DELETE FROM `asset` WHERE id = '.$link->id;
					echo 'Delete wrong id :<span style="color:red">'.$link->id.'</span> from database Real ID :'.$tmpId. '<br/>';
					$conn->query($qDelete);
				}else{
					//this is new id and never exist 
					//update them
					$qUpdate = 'UPDATE `asset` SET `id`= '.$tmpId.' WHERE id = '.$link->id;
					echo 'Replace wrong id <span style="color:red">'.$link->id.'</span> with real ID:'.$tmpId. '<br/>';
					$conn->query($qUpdate);
				}
				//coution !!!				
				$link->id = $tmpId;
			}
			if(!empty($link->id) AND ($link->id > 1) AND ($tmpId == $link->id)){
				//echo '$link->id : '.$link->id . '<br/>';
				$link = getObjAssetByid($link->id);	
				//echo '<pre>';
				//print_r($link);
				//echo '</pre>';
				if($link !== false){
					$newData = ripAssetlink($link);
				}
				//echo '<pre>';
				//print_r($newData);
				//echo '</pre>';
				if(!validLedLink($link->url) OR $newData === false){
					removeAssetFromDB($link);
					$updatebadlink++;
				}else{					
					//$remainAssetneedUpdate = getRemainAssetUpdate();
					if($newData !== 0){
						updateAsset($link, $newData);
						$updateCount++;
						//$notifyText = 'Update asset success id = <a href="http://example.com/map/index.php?ptype=viewFullListing&reid='.$newData->id.'" target="_blank">'.$newData->id .'</a>';
						//dailyError($notifyText);
					}					
				}
			}
			$fect_end = microtime(true);
			$fectTimeUsage = $fectTimeUsage + ($fect_end - $timer_start);
		}
		
		echo "เวลาที่ใช้ไป  คือ ".$fectTimeUsage.'วินาที สำหรับการดึงข้อมูล จำนวน '.$updateCount."รายการ".PHP_EOL; 
		if($updateCount > 0){
			$showecho  = "Updated asset <b>".$updateCount."</b> of ".number_format($getRemainAssetUpdate).PHP_EOL;
			$showecho .= "Remove badlink  <b>".$updatebadlink."</b>". PHP_EOL;
			echo '<hr/>';
			echo $showecho;
			dailyUpdate($showecho, 'getLink Update Report');
		}
	}else{
		$timer_end = microtime(true);
		$execution_time = $timer_end - $timer_start;
		$notifyMail  = "ตอนนี้การอัพเดต จำนวนสินทรัพทย์ ไม่มี หลงเหลืออยู่แล้ว ควรลด ปริมาณการอัพเดต  ". __FILE__ ." ลง ".PHP_EOL;
		$notifyMail .= "เวลาที่ใช้ไป  คือ ".$execution_time.'วินาที สำหรับการดึงข้อมูล จำนวน '.$fectLimit."รายการ".PHP_EOL;
		dailyError($notifyMail);	
	}
}