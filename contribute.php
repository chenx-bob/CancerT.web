<?php
	include_once('lib/config.php');
	$smarty -> assign("title","Contribute");
	$smarty -> assign("st",$_GET);
	$smarty -> display("contribute.html");
?>