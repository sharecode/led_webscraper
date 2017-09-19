<?php
/*****************/
	//param
$pricemax = 2500000;
/*****************/	
include("simple_html_dom.php");
include("province.php");
include("sharelib.php");
$update = isset($_GET["update"])?$_GET["update"]:0;
$pid = isset($_GET["id"])?$_GET["id"]:'10';	
if($update == 1){
	generateData($pid);
	header( "location: http://www.example.com/view.php?id=".$pid );
	exit(0);
}
$header  = "\n".'<link rel="stylesheet" type="text/css" href="/asset/datatables.min.css"/> ';
$header .= "\n".'<script type="text/javascript" src="/asset/datatables.min.js"></script>';
echo htmlHeader($header);
$feed_file = 'asset/asset.inc';

function genDropdownProvince($arrProvince, $id = '10'){
	echo '<select name="province" class="pcat" style="float:right">';
	foreach($arrProvince as $province){
		if($id == $province['id']){
			$selected = ' selected';
		}else{
			$selected = '';
		}
		 echo '<option value="'.$province['id'].'" class="pname"'.$selected.'>'.$province['th'].'</option>';
	}
	echo '</select>';
}

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

function generateData($id){
	$path = 'asset/asset'.$id.'.inc';
	if(file_exists($path)){
		//unlink($path);
	}
		$conn = db();
		$q = 'SELECT 
		`asset`.`id`,
		`law_suit_no`,
		`law_suit_year`, 
		`type`.`th` as `type_th`, 
		FORMAT(`size400w`,0) 	as w400, 
		FORMAT(`size100w`,0) 	as w100, 
		FORMAT(`sizew`,0) as w, 
		FORMAT(`estimated_price`,0) as estimated_price, 
		`deed_no`,	
		`tumbon` as tumbon_th, 
		`amphur`.`th` as amphur_th, 
		`province`.`th` as province_th, 
		`land_owner`,
		`jod`,
		`jay`,
		`law_owner`, 
		`cort`, 
		`bond`, 
		`debt`, 
		DATE_FORMAT(`date1`, "%d/%m/%Y") as date1, 
		`date1_status`, 
		DATE_FORMAT(`date2`, "%d/%m/%Y") as date2,  
		`date2_status`, 
		DATE_FORMAT(`date3`, "%d/%m/%Y") as date3,  
		`date3_status`, 
		DATE_FORMAT(`date4`, "%d/%m/%Y") as date4,  
		`date4_status`, 
		DATE_FORMAT(`date5`, "%d/%m/%Y") as date5,  
		`date5_status`, 
		DATE_FORMAT(`date6`, "%d/%m/%Y") as date6, 
		`date6_status`, 
		DATE_FORMAT(`date7`, "%d/%m/%Y") as date7, 
		`date7_status`,
		DATE_FORMAT(`date8`, "%d/%m/%Y") as date8, 
		`date8_status`,		
		DATE_FORMAT(`last_seen`, "%d/%m/%Y %T") as last_seen
		FROM `asset`
		LEFT JOIN `amphur` ON `amphur`.`id` = `asset`.`amphur_id` 
		LEFT JOIN `type` ON `type`.`id` = `asset`.`type_id` 
		LEFT JOIN `province` ON `amphur`.`province_id` = `province`.`id`
		WHERE `enable` = 1 AND pid ='.;		
		$result = $conn->query($q);
		if ($conn->affected_rows > 0){
			while($row = $result->fetch_assoc()){
				$ar[] = implode("</td>\n\t\t<td>",$row);
			}
			$allar = implode("</td>\n\t</tr>\n\t<tr>\n\t\t<td>", $ar);
		}
		$allar = "<tbody>\n\t<td>".$allar."</td>\n\t</ttbody>";
		$result = file_put_contents('asset/asset.inc',$allar);
		return $result;
	}else{
		return true;
	}
}
if(generateData($update) === false){
	echo 'data corupt!!!';
}else{
	genDropdownProvince($allProvince, $pid);
?>
<table id="tblasset" class="display" cellspacing="0" width="100%">
        <thead>
            <tr>
                <?php
				$header = '
				<th>id</th>
				<th>เลขคดีแดง</th>
				<th>ปี</th>
				<th>ประเภท</th>
				<th>ไร่</th>
				<th>งาน</th>
				<th>ตรว</th>
				<th>ราคาประเมิน</th>
				<th>เลขโฉนด</th>
				<th>ตำบล</th>
				<th>อำเภอ</th>
				<th>จังหวัด</th>
				<th>เจ้าของที่</th>
				<th>โจทย์</th>
				<th>จำเลย</th>
				<th>เจ้าของสำนวน</th>
				<th>ศาล</th>
				<th>เงื่อนไขประกัน</th>
				<th>ภาระจำนอง</th>
				<th>ประมูลรอบ1</th>
				<th>สถานะ1</th>
				<th>ประมูลรอบ2</th>
				<th>สถานะ2</th>
				<th>ประมูลรอบ3</th>
				<th>สถานะ3</th>
				<th>ประมูลรอบ4</th>
				<th>สถานะ4</th>
				<th>ประมูลรอบ5</th>
				<th>สถานะ5</th>
				<th>ประมูลรอบ6</th>
				<th>สถานะ6</th>
				<th>ประมูลรอบ7</th>
				<th>สถานะ7</th>
				<th>ประมูลรอบ8</th>
				<th>สถานะ8</th>
				<th>สถานะล่าสุด</th>
				';
				echo $header;
				?>
            </tr>
        </thead>
<?php
		include($feed_file);
?>
        <tfoot>
            <tr>
               <?php
				echo $header;
				?>
            </tr>
        </tfoot>
    </table>
<?php
if (file_exists($feed_file)) {	
	$modtime = date("d/m/Y H:i:s", filemtime($feed_file));
	$modtime_ago = date("Y-m-d H:i:s.", filemtime($feed_file));
	$modtime_ago = time_elapsed_string($modtime_ago, true);
    echo "\n\r<br/>Page was last modified: " .$modtime.'('.$modtime_ago.') <a href="?update=1">regenerate now</a> <a href="getLinkContent.CRON.php?console" target="_blank">add data</a>';
}
}
$footer = '
<script type="text/javascript">
$(document).ready(function() {
    $("#tblasset").DataTable();
} );
</script>';
echo htmlFooter($footer);
?>