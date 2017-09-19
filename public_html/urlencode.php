<?php
$aumpherBkk = array(
	"คลองเตย",
	"คลองสาน",
	"คลองสามวา",
	"คันนายาว",
	"จตุจักร",
	"จอมทอง",
	"เจียระดับ",
	"ดอนเมือง",
	"ดินแดง",
	"ดุสิต",
	"ตลิ่งชัน",
	"ทวีวัฒนา",
	"ทุ่งครุ",
	"ธนบุรี",
	"บางกอกน้อย",
	"บางกอกใหญ่",
	"บางกะปิ",
	"บางขุนเทียน",
	"บางเขน",
	"บางคอแหลม",
	"บางแค",
	"บางซื่อ",
	"บางนา",
	"บางบอน",
	"บางพลัด",
	"บางรัก",
	"บึงกุ่ม",
	"ปทุมวัน",
	"ประแจจีน",
	"ประเวศ",
	"ป้อมปราบศัตรูพ่าย",
	"พญาไท",
	"พระโขนง",
	"พระนคร",
	"ภาษีเจริญ",
	"มีนบุรี",
	"มีนบุรี (เมือง)",
	"มีนบุรี(คลองสอง)",
	"มีนบุรี(เมือง)",
	"มีนบุรี(แสนแสบ)",
	"ยานนาวา",
	"ราชเทวี",
	"ราษฎร์บูรณะ",
	"ลาดกระบัง",
	"ลาดกระบัง(แสนแสบ)",
	"ลาดพร้าว",
	"ลาดพร้าว(บางกะปิ)",
	"วังทองหลาง",
	"วัฒนา",
	"สวนหลวง",
	"สะพานสูง",
	"สัมพันธวงศ์",
	"สาทร",
	"สาธร",
	"สายไหม",
	"แสนแสบ",
	"หนองแขม",
	"หนองแขม (ภาษีเจริญ)",
	"หนองจอก",
	"หนองจอก(เจียระดับ)",
	"หลักสี่",
	"ห้วยขวาง"
	);
foreach($aumpherBkk as $a){
	$c = iconv('utf-8','windows-874',$a);
	//$c = mb_convert_encoding($a, "ASCII", "utf-8");
	echo $a."<br/>\n";
	echo 'urlencode(iconv("utf-8","windows-874",$a)) 	= '.urlencode($c)."<br/>\n";
	echo 'urldecode(urlencode(iconv("utf-8","windows-874",$a))) = '.urldecode(urlencode($c))."<br/>\n";
	echo 'urlencode($a) 								= '.urlencode($a)."<br/>\n";
	echo 'utf8_decode($a) 								= '.utf8_decode($a)."<br/>\n";	
	echo 'urlencode(utf8_decode($a))				    = '.urlencode(utf8_decode($a))."<br/>\n";
	echo 'urlencode($a) 								= '.urlencode($a)."<br/>\n";
	echo 'utf8_encode($a) 								= '.utf8_encode($a)."<br/>\n";
	echo 'urlencode(utf8_encode($a)) 					= '.urlencode(utf8_encode($a))."<br/><br/>\n";
}