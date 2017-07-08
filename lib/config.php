<?php 
include_once("./smarty/Smarty.class.php");
$smarty = new Smarty();
$smarty->template_dir = "./templates/";
$smarty->compile_dir = "./templates_c/";
$smarty->left_delimiter = '{';
$smarty->right_delimiter = '}';
//
//require_once("mysql.php");
//$db = new mysql("192.168.6.102","root","rlibs402","CGF","conn","utf8");
?>