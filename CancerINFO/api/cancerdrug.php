<?php 
	require_once("../lib/mysql.php");
	if(isset($_GET['cancer_name'])){
		$cancer = $_GET['cancer_name'];
		$cancerdrug['FDA approved'] = cancerdrug($cancer);
		$cancerdrug['drug from cell-line testing'] = ccl($cancer);
		echo json_encode($cancerdrug,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
	}
	function cancerdrug($cancer){
		$db = new mysql("CGF","conn","utf8");
		$db -> query("SELECT * FROM cancer_drug WHERE Conditions_oncotree = '".$cancer."'");
		$cancerdrug = array();
		while($t = $db->fetch_assoc()){
			$cancerdrug[] = $t;	
		}
		ksort($cancerdrug);
		return $cancerdrug;
	}
	function ccl($cancer){
		$db = new mysql("CGF","conn","utf8");
		$db -> query('select * from GDSC where ONCOTREE = "'.$cancer.'"');
		$ccl = array();
		while($t = $db->fetch_assoc()){
			array_push($ccl,$t);	
		}
		return $ccl;	
	}
?>          