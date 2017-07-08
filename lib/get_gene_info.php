<?php
echo gene_info();

function gene_info(){
	include_once('lib/config.php');
		
		$db -> query("SELECT * FROM element_cancer_interpretation");
		//$db -> query("SELECT * FROM gene_info");
		$gene = array();
		while($t = $db->fetch_array()){
			$gene[$t['element_symbol']]['gene'] = "";
			$gene[$t['element_symbol']]['gene'] = $t['element_symbol'];
			$gene[$t['element_symbol']]['role'] = "";
			$gene[$t['element_symbol']]['role'] = $t['element_role_in_cancer'];
			$gene[$t['element_symbol']]['gene_in_cancer'] = "No";
			if(!empty($t['element_cancer_oncotree_gene'])){
				$gene[$t['element_symbol']]['gene_in_cancer'] = "Yes";
			}
			$gene[$t['element_symbol']]['gene_alteration_in_cancer'] = "No";
			if(!empty($t['element_cancer_oncotree_gene_alteration'])){
				$gene[$t['element_symbol']]['gene_alteration_in_cancer'] = "Yes";
			}
			$gene[$t['element_symbol']]['gene_alteration_in_cancer_treatment'] = "No";
			if(!empty($t['element_cancer_oncotree_gene_alteration_drug'])){
				$gene[$t['element_symbol']]['gene_alteration_in_cancer_treatment'] = "Yes";
			}
		}
		$geneinfo = array();
		$db -> query("SELECT * FROM gene_info");
		while($t = $db->fetch_array()){
			$geneinfo[$t['gene_symbol']][$t['type']]=$t['des'];
		}
		
		$genes = array_keys($gene);
		ksort($genes);
		$json = array();
		foreach($genes as $g){
			  $gene_info = array();
			  if(isset($geneinfo[$g]['name'])){
			  	$gene_info['name'] = $geneinfo[$g]['name'];
			  }
			  else{
				$gene_info['name'] = "";  
			  }
			  if(isset($geneinfo[$g]['ensembl_id'])){
			  	$gene_info['ensembl_id'] = $geneinfo[$g]['ensembl_id'];
			  }
			  else{
				$gene_info['ensembl_id'] = "";  
			  }
			  if(isset($geneinfo[$g]['entrezgene_id'])){
			  	$gene_info['entrezgene_id'] = $geneinfo[$g]['entrezgene_id'];
			  }
			  else{
				$gene_info['entrezgene_id'] = "";  
			  }
			  $gene_info['gene'] = $gene[$g]['gene'];
			  $gene_info['role'] = $gene[$g]['role'];
			  $gene_info['gene_in_cancer'] = $gene[$g]['gene_in_cancer'];
			  $gene_info['gene_alteration_in_cancer'] = $gene[$g]['gene_alteration_in_cancer'];
			  $gene_info['gene_alteration_in_cancer_treatment'] = $gene[$g]['gene_alteration_in_cancer_treatment'];
			  
			  array_push($json,json_encode($gene_info));
		}
		$js = '['.join(",",$json).']';
		return $js;
	}
?>