<?php
include "sharelib.php";
/**
*@author Xu Ding
*@email thedilab@gmail.com
*@website http://www.StarTutorial.com
**/
class Calendar {
	/**
	* Constructor
	*/
	public function __construct(){
		$this->naviHref = htmlentities($_SERVER['PHP_SELF']);
	}
	/********************* PROPERTY ********************/
	private $dayLabels = array("Mon","Tue","Wed","Thu","Fri","Sat","Sun");
	private $currentYear=0;
	private $currentMonth=0;
	private $currentDay=0;
	private $currentDate=null;
	private $daysInMonth=0;
	private $naviHref= null;
	private $currentType = 1;
	private $currentPid = 10;
	private $currentAid = 0;
	private $currentN = 1;
	private $currentADay = 0;
	/********************* PUBLIC **********************/
	/**
	  * print out the calendar
	  */
	public function show() {
		$year = null;  
		$month = null;	
		$type = null;
		$pid = null;
		$aid = null;
		$n = null;
		$d = null;

		if(isset($_GET['year']) && !empty($_GET['year'])){
			$year = $_GET['year'];  
		}else if(null==$year){
			$year = date("Y",time());   
		}
		if(isset($_GET['month']) && !empty($_GET['month'])){
			$month = $_GET['month'];  
		}else if(null==$month){
			$month = date("m",time());  
		} 
		if(isset($_GET['d']) && !empty($_GET['d'])){
			$d = $_GET['d'];  
		}else if(null==$d){
			$d = 0;  
		} 
		
		if(isset($_GET['type']) && !empty($_GET['type'])){
			$type = $_GET['type'];  
		}else if(null==$type){
			$type = 1;  
		} 
		
		if(null==$pid&&isset($_GET['pid'])){
			$pid = $_GET['pid'];  
		}else if(null==$pid){
			$pid = 0;  
		}
		
		if(null==$aid&&isset($_GET['aid'])){
			$aid = $_GET['aid'];  
		}else if(null==$aid){
			$aid = 0;  
		} 
		
		if(null==$n&&isset($_GET['n'])){
			$n = $_GET['n'];  
		}else if(null==$n){
			$n = 0;  
		} 

		$this->currentType = $type;
		$this->currentPid = $pid;
		$this->currentAid = $aid;
		$this->currentN = $n;
		$this->currentYear=$year;  
		$this->currentMonth=$month;  
		$this->currentADay=$d;  
		$this->daysInMonth=$this->_daysInMonth($month, $year); 
		$date = date("Y-m-d", strtotime($this->currentYear.'-'.$this->currentMonth.'-'.$this->currentADay));

		$content='<div id="calendar">'.
		'<div class="box">'.
		$this->_createNavi().
		'</div>'.
		'<div class="box-content">'.
		'<ul class="label">'.$this->_createLabels().'</ul>'; 
		$content.='<div class="clear"></div>';
		$content.='<ul class="dates">';  
	 
		$weeksInMonth = $this->_weeksInMonth($month, $year);
		// Create weeks in a month
		for( $i=0; $i<$weeksInMonth; $i++ ){ 
			//Create days in a week
			for($j=1;$j<=7;$j++){
				$content.=$this->_showDay($i*7+$j);
			}
		}
		$content .='</ul>';
		$content .='<div class="clear"></div>';
		$content .='</div>';	
		$content .='</div>';
		$content .='<div id="details"><div class="box">&nbsp;<h3 class="assetheader">รายละเอียดสินทรัพย์</h3><a href="javascript:history.back();" class="bacllink">Back</a>
</div>';
		$content .='<div class="box-content">'.$this->_getAssetCalendar($d, $month, $year, $type, $pid, $aid, $n).'</div></div>';
		return $content; 
	}

	private function _thisMonth($date){
		$currentMonth = date("m", strtotime($date));
		if ($currentMonth == $this->currentMonth){
			return true;
		}else{
			return false;
		}
	}

	private function _getAssetCalendar($day, $month, $year, $type, $pid, $aid, $n){
		$day = intval($day);
		$month = intval($month);
		$year = intval($year);
		$type = intval($type);
		$pid = intval($pid);
		$aid = intval($aid);
		$n = intval($n);
		$arrAssetObj = getAssetCalendar($day, $month, $year, $type, $pid, $aid, $n);
		$pDetail = '';
		$aDetail = '';
		foreach($arrAssetObj  as $objAsset){
			$assetDetailsText = '';
			$areaNum = showWnumberReport($objAsset->size400w, $objAsset->size100w, $objAsset->sizew, false);
			$areaText = showWnumberReport($objAsset->size400w, $objAsset->size100w, $objAsset->sizew, true);
			if($areaNum > 0){
				$areaAverageText = "&nbsp;&nbsp;&nbsp;&nbsp;<b>ราคาต่อตรว</b>&nbsp;&nbsp;".number_format(ceil($objAsset->estimated_price/$areaNum)).'บาท/ตรว.';
			}else{
				$areaAverageText = '';
			}
			if($n >=1 AND $n <= 8){
				$nTexts[] = $n;
			}elseif($n == 0){
				if(isset($objAsset->date8) AND $this->_thisMonth($objAsset->date8)){
					$nTexts[] = 8;
				}
				if(isset($objAsset->date7) AND $this->_thisMonth($objAsset->date7)){
					$nTexts[] = 7;
				}
				if(isset($objAsset->date6) AND $this->_thisMonth($objAsset->date6)){
					$nTexts[] = 6;
				}
				if(isset($objAsset->date5) AND $this->_thisMonth($objAsset->date5)){
					$nTexts[] = 5;
				}
				if(isset($objAsset->date4) AND $this->_thisMonth($objAsset->date4)){
					$nTexts[] = 4;
				}
				if(isset($objAsset->date3) AND $this->_thisMonth($objAsset->date3)){
					$nTexts[] = 3;
				}
				if(isset($objAsset->date2) AND $this->_thisMonth($objAsset->date2)){
					$nTexts[] = 2;
				}
				if(isset($objAsset->date1) AND $this->_thisMonth($objAsset->date1)){
					$nTexts[] = 1;
				}
			}
			foreach($nTexts as $nText){
				$dateText = 'date'.$nText;
				$assetDate = date("d/m/Y", strtotime($objAsset->$dateText));
				$assetDay = date("d", strtotime($objAsset->$dateText));
				$linkContext  = '<span class="s1">นัดที่'.$nText.'</span>';
				$linkContext .= '<span class="s2">'.$assetDate.'</span>';
				$linkContext .= '<span class="s3">'.$objAsset->tth.'</span>';
				$linkContext .= '<span class="s4">'.$objAsset->pth.'/'.$objAsset->ath.'/'.$objAsset->tumbon.'</span>';
				$linkContext .= '<span class="s5">'.$areaText.'</span>';
				$linkContext .= '<span class="s6">'.$areaAverageText.'</span>';
				if(($day > 0 AND ($assetDay == $day)) OR (empty($day))){
					$assetDetailsText = '<a href="http://example.com/map/index.php?ptype=viewFullListing&reid='.$objAsset->id.'" target="_blank" class="asset">'.$linkContext.'</a>';
					$assetDetails[] = $assetDetailsText;
				}
			}
			unset($nTexts);
			
			if(($pid == 0) AND ($aid == 0) AND !empty($assetDetailsText)){
				$param = '&type='.$type.'&aid=0&pid='.$objAsset->pid.'&n='.$n.'&d='.$day;
				if(empty($assetProvinceDetails[$objAsset->pid]['count'])){
					$assetProvinceDetails[$objAsset->pid]['count'] = 1;
					$assetProvinceDetails[$objAsset->pid]['link'] = '<a class="plink" href="'.$this->naviHref.'?month='.$this->currentMonth.'&year='.$this->currentYear.$param.'">'.$objAsset->pth.'(1)</a>';
				}else{
					$assetProvinceDetails[$objAsset->pid]['count']++;
					$assetProvinceDetails[$objAsset->pid]['link'] = '<a class="plink" href="'.$this->naviHref.'?month='.$this->currentMonth.'&year='.$this->currentYear.$param.'">'.$objAsset->pth.'('.$assetProvinceDetails[$objAsset->pid]['count'].')</a>';
				}
			}
			if($pid > 0 AND !empty($assetDetailsText)){
				$param = '&type='.$type.'&aid='.$objAsset->aid.'&pid='.$objAsset->pid.'&n='.$n.'&d='.$day;
				if(empty($assetAumphurDetails[$objAsset->aid]['count'])){
					$assetAumphurDetails[$objAsset->aid]['count'] = 1;
					$assetAumphurDetails[$objAsset->aid]['link'] = '<a class="alink" href="'.$this->naviHref.'?month='.$this->currentMonth.'&year='.$this->currentYear.$param.'">'.$objAsset->ath.'(1)</a>';
				}else{
					$assetAumphurDetails[$objAsset->aid]['count']++;
					$assetAumphurDetails[$objAsset->aid]['link'] = '<a class="alink" href="'.$this->naviHref.'?month='.$this->currentMonth.'&year='.$this->currentYear.$param.'">'.$objAsset->ath.'('.$assetAumphurDetails[$objAsset->aid]['count'].')</a>';
				}
			}
		}
		if(isset($assetProvinceDetails) AND sizeof($assetProvinceDetails) > 0){
		$pDetail = '';
			foreach($assetProvinceDetails as $assetProvinceDetail){
				$pDetail .= $assetProvinceDetail['link'];
			}
			$pDetail .= '<br/ style="clear:both">';
		}else{
			$pDetail = '';
		}
		if(isset($assetAumphurDetails) AND sizeof($assetAumphurDetails) > 0){
		$aDetail = '';
			foreach($assetAumphurDetails as $assetAumphurDetail){
				$aDetail .= $assetAumphurDetail['link'];
			}
			$aDetail .= '<br/ style="clear:both">';
		}else{
			$aDetail = '';
		}		
		if(sizeof($assetDetails) > 0){
			$assetDetail = implode(' ',$assetDetails);
		}else{
			$assetDetail = '';
		}
		if(isset($assetProvinceDetails) AND sizeof($assetProvinceDetails) > 0){
			return $pDetail.$aDetail;
		}else{
			return $pDetail.$aDetail.$assetDetail;
		}
		
	}

	private function _showType($currentType = 1){
		$select[1] = '';
		$select[2] = '';
		$select[3] = '';
		$select[$currentType] = ' selected="selected"';

		$showType  = '<select name="type" id="retype" onchange="changeValue()">';
		$showType .= '<option value="2"'.$select[2].'>บ้าน</option>';
		$showType .= '<option value="1"'.$select[1].'>ที่ดิน</option>';
		$showType .= '<option value="3"'.$select[3].'>คอนโด</option>';	
		$showType .= '</select>';
		return $showType;
	}

	private function _showPid($currentPid = 10){	
		$conn = db();
		$q = 'SELECT * FROM  `province` ORDER BY `th`';
		$result = $conn->query($q);
		$showPid  = '<select name="pid" id="repid" onchange="changeValue()">';
		$showPid .= '<option value="0">──────────</option>';
		while($row = $result->fetch_object()){
			if($row->id == $currentPid){
				$showPid .= '<option value="'.$row->id.'" selected="selected">'.$row->th.'</option>';
			}else{
				$showPid .= '<option value="'.$row->id.'">'.$row->th.'</option>';
			}
		}
		$showPid .= '</select>';
		return $showPid;
	}

	private function _showAid($currentPid = 10, $currentAid = 30){	
		$conn = db();
		$q = 'SELECT * FROM  `amphur` WHERE `amphur`.`province_id` = '.$currentPid.' ORDER BY `th`';
		$result = $conn->query($q);
		$showAid  = '<select name="aid" id="reaid" onchange="changeValue()">';
		$showAid .= '<option value="0">ทุกอำเภอ</option>';
		while($row = $result->fetch_object()){
			if($row->id == $currentAid){
				$showAid .= '<option value="'.$row->id.'" selected="selected">'.$row->th.'</option>';
			}else{
				$showAid .= '<option value="'.$row->id.'">'.$row->th.'</option>';
			}
		}
		$showAid .= '</select>';
		return $showAid;
	}

	private function _showN($currentN = 0){	
		$select[0] = '';
		$select[1] = '';
		$select[2] = '';
		$select[3] = '';
		$select[4] = '';
		$select[5] = '';
		$select[6] = '';
		$select[7] = '';
		$select[8] = '';
		$select[$currentN] = ' selected="selected"';

		$showN  = '<select name="n" id="ren" onchange="changeValue()">';
		$showN .= '<option value="0"'.$select[0].'>ทุกนัด</option>';
		$showN .= '<option value="1"'.$select[1].'>นัดที่ 1</option>';
		$showN .= '<option value="2"'.$select[2].'>นัดที่ 2</option>';
		$showN .= '<option value="3"'.$select[3].'>นัดที่ 3</option>';
		$showN .= '<option value="4"'.$select[4].'>นัดที่ 4</option>';
		$showN .= '<option value="5"'.$select[5].'>นัดที่*5</option>';
		$showN .= '<option value="6"'.$select[6].'>นัดที่ 6</option>';
		$showN .= '<option value="7"'.$select[7].'>นัดที่*7</option>';
		$showN .= '<option value="8"'.$select[8].'>นัดที่ 8</option>';
		$showN .= '</select>';
		return $showN;
	}
  
	/********************* PRIVATE **********************/
	/**
	* create the li element for ul
	*/
	private function _showDay($cellNumber){
		if($this->currentDay==0){ 
			$firstDayOfTheWeek = date('N',strtotime($this->currentYear.'-'.$this->currentMonth.'-01'));
			if(intval($cellNumber) == intval($firstDayOfTheWeek)){ 
				$this->currentDay=1; 
			}
		}
	  
		if( ($this->currentDay!=0)&&($this->currentDay<=$this->daysInMonth) ){ 
			$this->currentDate = date('Y-m-d',strtotime($this->currentYear.'-'.$this->currentMonth.'-'.($this->currentDay))); 
			$cellContent = $this->_createNavi($this->currentDay);
			$this->currentDay++; 
		}else{
			$this->currentDate =null;
			$cellContent=null;
		}  
		return '<li id="li-'.$this->currentDate.'" class="'.($cellNumber%7==1?' start ':($cellNumber%7==0?' end ':' ')).($cellContent==null?'mask':'').'">'.$cellContent.'</li>';
	}

	/**
	* create navigation
	*/
	private function _createNavi($selectDay = 0){  
		$nextMonth = $this->currentMonth==12?1:intval($this->currentMonth)+1;
		$nextYear = $this->currentMonth==12?intval($this->currentYear)+1:$this->currentYear;  
		$preMonth = $this->currentMonth==1?12:intval($this->currentMonth)-1;  
		$preYear = $this->currentMonth==1?intval($this->currentYear)-1:$this->currentYear; 
		$type = $this->currentType;
		$pid = $this->currentPid;
		$aid = $this->currentAid;
		if($selectDay <> 0){
			$d = $selectDay;
			$n = $this->currentN;
			$param = '&type='.$type.'&aid='.$aid.'&pid='.$pid.'&n='.$n.'&d='.$d;
			if($d ==  $this->currentADay){
				$activeDayCss = ' active';
			}else{
				$activeDayCss = '';
			}
			return '<a class="caldate'.$activeDayCss.'" href="'.$this->naviHref.'?month='.$this->currentMonth.'&year='.$this->currentYear.$param.'">'.$d.'</a>';	
		}else{
			$d = 0;
			$n = $this->currentN;
			$param = '&type='.$type.'&aid='.$aid.'&pid='.$pid.'&n='.$n.'&d='.$d;
			 return  '<div class="header">'.
						'<a class="prev" href="'.$this->naviHref.'?month='.$preMonth.'&year='.$preYear.$param.'">Prev</a>'.
						'<span class="title">'.$this->_showType($type).$this->_showN($n).date('Y M',strtotime($this->currentYear.'-'.$this->currentMonth.'-1')).' '.$this->_showPid($pid).$this->_showAid($pid, $aid).'</span>'.
						'<a class="next" href="'.$this->naviHref.'?month='.$nextMonth.'&year='.$nextYear.$param.'">Next</a>'.
						'</div>';
		}
	}
  
	/**
	* create calendar week labels
	*/
	private function _createLabels(){  
		$content='';  
		foreach($this->dayLabels as $index=>$label){ 
			$content.='<li class="'.($label==6?'end title':'start title').' title">'.$label.'</li>';
		} 
	return $content;
	}



	/**
	* calculate number of weeks in a particular month
	*/
	private function _weeksInMonth($month=null,$year=null){  
		if( null==($year) ) {
			$year = date("Y",time());
		}

		if(null==($month)) {
			$month = date("m",time());
		}  
		// find number of days in this month
		$daysInMonths = $this->_daysInMonth($month,$year);  
		$numOfweeks = ($daysInMonths%7==0?0:1) + intval($daysInMonths/7);  
		$monthEndingDay= date('N',strtotime($year.'-'.$month.'-'.$daysInMonths));  
		$monthStartDay = date('N',strtotime($year.'-'.$month.'-01'));  
		if($monthEndingDay<$monthStartDay){ 
			$numOfweeks++;  
		}  
		return $numOfweeks;
	}

	/**
	* calculate number of days in a particular month
	*/
	private function _daysInMonth($month=null,$year=null){
		if(null==($year)){
		  $year = date("Y",time());
		 }
		 if(null==($month)){
			 $month = date("m",time());
		 }
		 return date('t',strtotime($year.'-'.$month.'-01'));
	}
	
	private function _dbug(){  
		echo '<table class="dbug">';
		echo '<tr><td class="head">Debug</td><td>Object Value</td><td>Link Value</td></tr>';
		echo '<tr><td>Selected Day</td><td>'.$this->currentADay.'</td><td>'.$_GET["d"].'</td></tr>';
		echo '<tr><td>Calendar Day</td><td>'.$this->currentDay.'</td><td></td></tr>';
		echo '<tr><td>Month</td><td>'.$this->currentMonth.'</td><td>'.$_GET["month"].'</td></tr>';
		echo '<tr><td>Year</td><td>'.$this->currentYear.'</td><td>'.$_GET["year"].'</td></tr>';
		echo '<tr><td>Type</td><td>'.$this->currentType.'</td><td>'.$_GET["type"].'</td></tr>';
		echo '<tr><td>Aid</td><td>'.$this->currentAid.'</td><td>'.$_GET["aid"].'</td></tr>';
		echo '<tr><td>Pid</td><td>'.$this->currentPid.'</td><td>'.$_GET["pid"].'</td></tr>';
		echo '<tr><td>N</td><td>'.$this->currentN.'</td><td>'.$_GET["n"].'</td></tr>';
		echo '</table>';
	}
}