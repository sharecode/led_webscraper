<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link href="calendar.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
function changeValue() {
	var e = document.getElementById("retype");
	var reTypeValue = e.options[e.selectedIndex].value;
	var e = document.getElementById("repid");
	var rePidValue = e.options[e.selectedIndex].value;
	var e = document.getElementById("reaid");
	var reAidValue = e.options[e.selectedIndex].value;
	var e = document.getElementById("ren");
	var reNValue = e.options[e.selectedIndex].value;
	<?php
	$getYear =  isset($_GET["year"])?'&year='.intval($_GET["year"]):'';
	$getMonth =  isset($_GET["month"])?'&month='.intval($_GET["month"]):'';
	$getDay =  isset($_GET["d"])?'&d='.intval($_GET["d"]):'';
	?>
    window.location = "calendar.php?type="+ reTypeValue + "&pid="+ rePidValue +"&aid="+ reAidValue +"&n="+ reNValue + "<?php echo $getYear.$getMonth.$getDay; ?>";
};
</script>
</head>
<body>
<?php
include 'calendar.inc.php';
$calendar = new Calendar();
echo $calendar->show();
?>
</body>
</html>