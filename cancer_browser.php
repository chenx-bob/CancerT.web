<?php 
    include_once('lib/config.php');
	require_once("lib/mysql.php");
	$db = new mysql("192.168.6.102","root","rlibs402","CGF","conn","utf8");
	
	$db -> query("SELECT * FROM cancer");
	$tissues = array();		
	while($t = $db->fetch_array()){
		$tissues[$t['cancer_oncotree_name']]['site'] = $t['primary_site'];
		$tissues[$t['cancer_oncotree_name']]['level'] = $t['level'];
		$tissues[$t['cancer_oncotree_name']]['nci'] = $t['nci_id'];
		$tissues[$t['cancer_oncotree_name']]['umis'] = $t['umis_id'];
	}
	$db -> query('SELECT * FROM element_cancer_interpretation');
	$tissue_cancers =array();
	$cancer_gene =array();
	$tissue_cancer =array();
	while($t = $db->fetch_array()){
		$cancers = explode(';',$t['element_cancer_oncotree_type']);
		foreach($cancers as $cancer){
			if(!empty($cancer)){
			  $tissue_cancer[$tissues[$cancer]['site']][$cancer]['level'] = "";
			  $tissue_cancer[$tissues[$cancer]['site']][$cancer]['nci'] = "";
			  $tissue_cancer[$tissues[$cancer]['site']][$cancer]['umis'] = "";
			  if(isset($tissues[$cancer]['level'])){
				  $tissue_cancer[$tissues[$cancer]['site']][$cancer]['level'] = $tissues[$cancer]['level'];
			  }
			  if(isset($tissues[$cancer]['nci'])){
				  $tissue_cancer[$tissues[$cancer]['site']][$cancer]['nci'] = $tissues[$cancer]['nci'];
			  }
			  if(isset($tissues[$cancer]['umis'])){
				  $tissue_cancer[$tissues[$cancer]['site']][$cancer]['umis'] = $tissues[$cancer]['umis'];
			  }
			  $tissue_cancers[$tissues[$cancer]['site']][$cancer] = 1;
			  $cancer_gene[$cancer][$t['element_symbol']] = 1;
			}
		}
	}
	ksort($tissue_cancers);
	$smarty -> assign("title","Cancer Browser");
	$tcg =array();
	foreach($tissue_cancer as $tissue=>$cancers){
		foreach($cancers as $cancer => $type){
			array_push($tcg,array("tissue"=>$tissue,"cancer"=>$cancer,"umis"=>$tissue_cancer[$tissue][$cancer]['umis'],"nci"=>$tissue_cancer[$tissue][$cancer]['nci'],"level"=>$tissue_cancer[$tissue][$cancer]['level']));	
		}	
	}
	ksort($tcg);
	$smarty -> assign("tcg",$tcg);
	if(isset($_GET['cancer'])){
		$cancer = $_GET['cancer'];
		$tissue = $_GET['tissue'];
		$cancers = array_keys($tissue_cancers[$tissue]);
		unset($tissue_cancers[$tissue]);
		ksort($cancers);
		$smarty -> assign("can",$cancer);
		$smarty -> assign("cancers",$cancers);
		$smarty -> assign("tissue",$tissue);
		$smarty -> assign("tissue_cancers",$tissue_cancers);
		
		$smarty -> assign("cg",cg($cancer));
		$smarty -> assign("cga",cga($cancer));
		$smarty -> assign("ctga",ctga($cancer));
		
		$smarty -> assign("cancerdrug",cancerdrug($cancer));
		$smarty -> assign("level_status",level_status());
		$smarty -> display("cancer_browser-2.html");
	}
	else{
		$smarty -> assign("tissue_cancers",$tissue_cancers);
		$smarty -> display("cancer_browser-1.html");
	}
	function cg($cancer){
		$db = new mysql("192.168.6.102","root","rlibs402","CGF","conn","utf8");
		$db -> query('SELECT element_symbol,element_role_in_cancer FROM element_cancer_interpretation WHERE (element_cancer_oncotree_type LIKE "%;'.$cancer.'" or element_cancer_oncotree_type LIKE "'.$cancer.';%" or element_cancer_oncotree_type LIKE "'.$cancer.'" or element_cancer_oncotree_type LIKE "%;'.$cancer.';%")');
		$gene = array();
		while($t=$db->fetch_array()){
			$geneinfo = geneinfo($t['element_symbol']);	
			$name=array("");$entrezgene_id=array("");$alias=array("");$ensembl_id=array("");
			if(isset($geneinfo[$t['element_symbol']]["name"])){
				$name = array_keys($geneinfo[$t['element_symbol']]["name"]);
			}
			if(isset($geneinfo[$t['element_symbol']]["entrezgene_id"])){
				$entrezgene_id = array_keys($geneinfo[$t['element_symbol']]["entrezgene_id"]);
			}
			if(isset($geneinfo[$t['element_symbol']]["alias"])){
				$alias = array_keys($geneinfo[$t['element_symbol']]["alias"]);
			}
			if(isset($geneinfo[$t['element_symbol']]["ensembl_id"])){
				$ensembl_id = array_keys($geneinfo[$t['element_symbol']]["ensembl_id"]);
			}
			array_push($gene,array("name"=>$name[0],"alias"=>$alias[0],"entrezgene_id"=>$entrezgene_id[0],"ensembl_id"=>$ensembl_id[0],"symbol"=>$t['element_symbol'],"role"=>$t['element_role_in_cancer']));
		}
		return $gene;
	}
	function cga($cancer){
		$db = new mysql("192.168.6.102","root","rlibs402","CGF","conn","utf8");
		$db -> query('SELECT * FROM element_alteration_cancer_interpretation WHERE (element_alteration_oncotree_type LIKE "%;'.$cancer.'" or element_alteration_oncotree_type LIKE "'.$cancer.';%" or element_alteration_oncotree_type LIKE "'.$cancer.'" or element_alteration_oncotree_type LIKE "%;'.$cancer.';%")');
		$cga = array();
		while($t = $db->fetch_array()){
			array_push($cga,$t);	
		}
		return $cga;
	}
	function ctga($cancer){
		$db = new mysql("192.168.6.102","root","rlibs402","CGF","conn","utf8");
		$db -> query('SELECT * FROM element_alteration_drug_cancer_interpretation WHERE (element_alteration_cancer_oncotree_type LIKE "%;'.$cancer.'" or element_alteration_cancer_oncotree_type LIKE "'.$cancer.';%" or element_alteration_cancer_oncotree_type LIKE "'.$cancer.'" or element_alteration_cancer_oncotree_type LIKE "%;'.$cancer.';%")');
		$ctga = array();
		while($t = $db->fetch_array()){
			array_push($ctga,$t);	
		}
		return $ctga;
	}
	function geneinfo($gene){
		$db = new mysql("192.168.6.102","root","rlibs402","CGF","conn","utf8");
		$db -> query("SELECT * FROM gene_info WHERE gene_symbol = '".$gene."'");
		$geneinfo = array();
		while($t = $db->fetch_array()){
			$geneinfo[$t['gene_symbol']][$t['type']][$t['des']]=1;	
		}
		return $geneinfo;
	}
	function cancerdrug($cancer){
		$db = new mysql("192.168.6.102","root","rlibs402","CGF","conn","utf8");
		$db -> query("SELECT * FROM cancer_drug WHERE Conditions_oncotree = '".$cancer."'");
		$cancerdrug = array();
		while($t = $db->fetch_array()){
			array_push($cancerdrug,$t);	
		}
		return $cancerdrug;
	}
	function level_status(){
		$db = new mysql("192.168.6.102","root","rlibs402","CGF","conn","utf8");
		$db -> query('SELECT * FROM level_status');
		$level=array();
		while($t=$db->fetch_array()){
			$level[$t['db']][strtoupper($t['original_level'])] = $t['level'];
		}
		return $level;
	}
?>          