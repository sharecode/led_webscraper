<?php
$pricemax = 2500000;
include("sharelib.php");
$Now = date("Y-m-d H:i:s");
$conn = db();
/*
1.order by random
$randomID = mt_rand(46878, 4294822900);
$q = "SELECT *  FROM `asset` WHERE `asset`.`deed_no` IS NULL  AND `asset`.`id` < ".$randomID." ORDER BY `asset`.`id` DESC  LIMIT 1";

2. order by asset size big->small
$q = "SELECT *  FROM `asset` WHERE `asset`.`deed_no` IS NULL  AND `asset`.`enable` = 1 ORDER BY `asset`.`size400w` DESC,`asset`.`size100w` DESC,`asset`.`sizew` DESC LIMIT 1";

3.  The cheapest asset (cost/w) 
$q = "SELECT *,`asset`.`estimated_price`/(`asset`.`size400w`*4+`asset`.`size100w`*100+`asset`.`sizew`) as asset_cost_per_w FROM `asset` WHERE `asset`.`deed_no` IS NULL AND `asset`.`enable` = 1 ORDER BY asset_cost_per_w asc LIMIT 1";

4.  The expensivest asset (cost/w) 
$q = "SELECT *,`asset`.`estimated_price`/(`asset`.`size400w`*4+`asset`.`size100w`*100+`asset`.`sizew`) as asset_cost_per_w FROM `asset` WHERE `asset`.`deed_no` IS NULL AND `asset`.`enable` = 1 ORDER BY asset_cost_per_w desc LIMIT 1";*/

//5.  missing image 
$q = "SELECT * FROM `asset` WHERE `asset`.`enable` = 1 AND `asset`.`image1` = '' AND `asset`.`image2` = '' AND `asset`.`map` = '' ORDER BY `asset`.`next_update` ASC  LIMIT 1";

/*6.  normal update asset 
$q = 'SELECT * FROM `asset` WHERE `asset`.`enable` = 1 AND `asset`.`next_update` < "'.$Now.'"  ORDER BY `asset`.`pid` asc LIMIT 1';*/

$result = $conn->query($q);
if ($conn->affected_rows == 1){
	$row = $result->fetch_object();
	return $row->url;
}else{
	return false;
}