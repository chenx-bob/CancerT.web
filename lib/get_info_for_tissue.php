<?php
require_once("mysql.php");
$db = new mysql("192.168.6.102","root","rlibs402","CGF","conn","utf8");
$type = $_GET['type'];
//$type = "tissue_type";
switch($type){
	case "tissue_type":
		$db -> query("SELECT primary_site FROM cancer");		
		while($t = $db->fetch_array()){
			$js[$t[0]] = $t[0];
		}
		$sjs = array_unique($js);
		ksort($sjs);
		$json = json_encode($sjs);
		echo $json;
		break;
	case "tumor_type":
		$tissue_type = $_GET['tissue'];
		$db -> query("SELECT cancer_oncotree_name FROM cancer WHERE primary_site = '".$tissue_type."'");
		while($ti = $db->fetch_array()){
			$jsi[$ti[0]] = $ti[0];
		}
		$sjsi = array_unique($jsi);
		ksort($sjsi);
		$jsoni = json_encode($sjsi);
		echo $jsoni;
		//return $jsoni;
		break;
	case "gene_type":
		$tumor_type = $_GET['tumor'];
		$db -> query('SELECT element_symbol,element_cancer_oncotree_type FROM element_cancer_interpretation WHERE (element_cancer_oncotree_gene LIKE "%;'.$tumor_type.'" or element_cancer_oncotree_gene LIKE "'.$tumor_type.';%" or element_cancer_oncotree_gene LIKE "'.$tumor_type.'" or element_cancer_oncotree_gene LIKE "%;'.$tumor_type.';%")');
		while($ta = $db->fetch_array()){
			$s = split(';',$ta[1]);
			if(in_array($tumor_type,$s)){
				$jsa[$ta[0]] = $ta[0];
			}
		}
		$sjsa = array_unique($jsa);
		ksort($sjsa);
		$jsona = json_encode($sjsa);
		echo $jsona;
		//return $jsona;
		break;
}

?>