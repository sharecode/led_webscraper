<?php
header("content-type: application/json");
include("functions.inc.php");
print $_GET['callback'].'('.getMapListingTest($_GET['id']).')';

?>