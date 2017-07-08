<?php
require_once("mysql.php");
$db = new mysql("192.168.6.102","root","rlibs402","CGF","conn","utf8");
$type = $_GET['type'];
//$type = "gene_type";
switch($type){
	case "gene_type":
		$db -> query("SELECT element_symbol FROM element_cancer_interpretation");		
		while($t = $db->fetch_array()){
			$js[$t[0]] = $t[0];
		}
		$sjs = array_unique($js);
		ksort($sjs);
		$json = json_encode($sjs);
		echo $json;
		break;
	case "tissue_type":
		$db -> query("SELECT primary_site_oncotree FROM primary_site");
		while($t = $db->fetch_array()){
			$js[$t[0]] = $t[0];
		}
		
		$sjs = array_unique($js);
		ksort($sjs);
		$jsoni = json_encode($sjs);
		echo $jsoni;
		//return $jsoni;
		break;
	case "variant_type":
		$gene_type = $_GET['gene_type'];
		$js =array();
		$db -> query("SELECT snv_detail,snv_content FROM feature_snv WHERE snv_symbol = '".$gene_type."'");
		while($t = $db->fetch_array()){
			$js[$t[0]] = $t[0];
		}
		$db -> query("SELECT cnv_detail,cnv_content FROM feature_cnv WHERE cnv_symbol = '".$gene_type."'");
		while($t = $db->fetch_array()){
			$js[$t[0]] = $t[0];
		}
		$db -> query("SELECT sv_detail,sv_content FROM feature_sv WHERE sv_symbol = '".$gene_type."'");
		while($t = $db->fetch_array()){
			$js[$t[0]] = $t[0];
		}
		$db -> query("SELECT ex_detail,ex_content FROM feature_expression WHERE ex_symbol = '".$gene_type."'");
		while($t = $db->fetch_array()){
			$js[$t[0]] = $t[0];
		}
//		$db -> query("SELECT phos_content FROM feature_phos WHERE phos_symbol = '".$gene_type."'");
//		while($t = $db->fetch_array()){
//			$js[$t[0]] = $t[0];
//		}
//		$db -> query("SELECT met_content FROM feature_methylation WHERE met_symbol = '".$gene_type."'");
//		while($t = $db->fetch_array()){
//			$js[$t[0]] = $t[0];
//		}
		$sjs = array_unique($js);
		ksort($sjs);
		$jsona = json_encode($sjs);
		echo $jsona;
		break;
}

?>