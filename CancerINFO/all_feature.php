<?php
	ini_set('memory_limit', '128000M');
	require_once("lib/mysql.php");
	$db = new mysql("CGF","conn","utf8"); 
    include_once('lib/config.php');
	$smarty -> assign("title","Search Tool");
	$tis = array('Adrenal Gland (ADRENAL_GLAND)','Lymph (LYMPH)','Pancreas (PANCREAS)','Blood (BLOOD)','Esophagus/Stomach (STOMACH)','Other (OTHER)','Head and Neck (HEAD_NECK)','Lung (LUNG)','Soft Tissue (SOFT_TISSUE)','Bowel (BOWEL)','CNS/Brain (BRAIN)','Thyroid (THYROID)','Skin (SKIN)','Biliary Tract (BILIARY_TRACT)','Bladder/Urinary Tract (BLADDER)','Breast (BREAST)','Cervix (CERVIX)','Bone (BONE)','Uterus (UTERUS)','Ovary/Fallopian Tube (OVARY)','Liver (LIVER)','Vulva/Vagina (VULVA)','Peripheral Nervous System (PNS)','Testis (TESTIS)','Kidney (KIDNEY)','Penis (PENIS)','Peritoneum (PERITONEUM)','Pleura (PLEURA)','Prostate (PROSTATE)','Eye (EYE)','Thymus (THYMUS)');
	$tissues = array();	
	foreach($tis as $ti){
		$tissue = $ti;
		$db -> query("SELECT * FROM cancer WHERE primary_site = '".$tissue."'");
			
		while($t = $db->fetch_array()){
			$tissues[$t['cancer_oncotree_name']]['level'] = $t['level'];
			$tissues[$t['cancer_oncotree_name']]['nci'] = $t['nci_id'];
			$tissues[$t['cancer_oncotree_name']]['umis'] = $t['umis_id'];
		}
	}
	$cgas = array();
	$ctgas = array();
	$myfile1 = fopen("/Users/cx/Desktop/cga.txt", "w") or die("Unable to open file!");
	$myfile = fopen("/Users/cx/Desktop/ctga.txt", "w") or die("Unable to open file!");


	foreach($tissues as $cancer => $type){	
		$cgas = array_merge($cgas,cga($cancer));
		$ctgas = array_merge($ctgas,ctga($cancer));	
	}
	$level_status = level_status();
	fwrite($myfile1,"Mutation\tEvidence Type\tEvidence Level\tSignificance\tSupport (PMID)\n");
	foreach($cgas as $cgak=>$cgav){
    	fwrite($myfile1,$cgav['element_alteration_detail']."\t".$cgav['element_alteration_cancer_evidence_type']."\t".$level_status['eadci'][strtoupper($cgav['element_alteration_cancer_evidence_level'])]."\t".$cgav['element_alteration_cancer_evidence_clinical_significance']."\t".$cgav['element_alteration_cancer_evidence_support']."\n");
	}
	fclose($myfile1);
	fwrite($myfile,"Mutation\tDrug Name\tDrug Family\tEvidence Type\tEvidence Level\tSignificance\tSupport (PMID)\n");
	foreach($ctgas as $ctgak=>$ctgav){
		fwrite($myfile,$ctgav['element_alteration_detail']."\t".$ctgav['element_alteration_cancer_drugs']."\t".$ctgav['element_alteration_cancer_drug_family']."\t".$ctgav['element_alteration_cancer_drug_evidence_type']."\t".$level_status['eaci'][strtoupper($ctgav['element_alteration_cancer_drug_evidence_level'])]."\t".$ctgav['element_alteration_cancer_drug_evidence_clinical_significance']."\t".$ctgav['element_alteration_cancer_drug_evidence_support']."\n");
	}
	fclose($myfile);
		
	function cg($cancer){
		$db = new mysql("CGF","conn","utf8");
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
	
	function level_status(){
		$db = new mysql("CGF","conn","utf8");
		$db -> query('SELECT * FROM level_status');
		$level=array();
		while($t=$db->fetch_array()){
			$level[$t['db']][strtoupper($t['original_level'])] = $t['level'];
		}
		return $level;
	}
	
	function cga($cancer){
		$db = new mysql("CGF","conn","utf8");
		$db -> query('SELECT * FROM element_alteration_cancer_interpretation WHERE (element_alteration_oncotree_type LIKE "%;'.$cancer.'" or element_alteration_oncotree_type LIKE "'.$cancer.';%" or element_alteration_oncotree_type LIKE "'.$cancer.'" or element_alteration_oncotree_type LIKE "%;'.$cancer.';%")');
		$cga = array();
		while($t = $db->fetch_array()){
			array_push($cga,$t);	
		}
		return $cga;
	}
	
	function ctga($cancer){
		$db = new mysql("CGF","conn","utf8");
		$db -> query('SELECT * FROM element_alteration_drug_cancer_interpretation WHERE (element_alteration_cancer_oncotree_type LIKE "%;'.$cancer.'" or element_alteration_cancer_oncotree_type LIKE "'.$cancer.';%" or element_alteration_cancer_oncotree_type LIKE "'.$cancer.'" or element_alteration_cancer_oncotree_type LIKE "%;'.$cancer.';%")');
		$ctga = array();
		while($t = $db->fetch_array()){
			array_push($ctga,$t);	
		}
		return $ctga;
	}
	
	function geneinfo($gene){
		$db = new mysql("CGF","conn","utf8");
		$db -> query("SELECT * FROM gene_info WHERE gene_symbol = '".$gene."'");
		$geneinfo = array();
		while($t = $db->fetch_array()){
			$geneinfo[$t['gene_symbol']][$t['type']][$t['des']]=1;	
		}
		return $geneinfo;
	}
	
	function feature($gene){
		$db = new mysql("CGF","conn","utf8");
		$jss=array();
		
		$db ->query("SELECT cnv_detail,cnv_content FROM feature_cnv WHERE cnv_symbol = '".$gene."'");
		while($t = $db->fetch_array()){
			$jss[$t[0]][$t[1]] = "CNV";
		}
		
		$db ->query("SELECT snv_detail,snv_content FROM feature_snv WHERE snv_symbol = '".$gene."'");
		while($t = $db->fetch_array()){
			$jss[$t[0]][$t[1]] = "SNV";
		}
		
		$db ->query("SELECT sv_detail,sv_content FROM feature_sv WHERE sv_symbol = '".$gene."'");
		while($t = $db->fetch_array()){
			$jss[$t[0]][$t[1]] = "SV";
		}
		
		$db ->query("SELECT met_detail,met_content FROM feature_methylation WHERE met_symbol = '".$gene."'");
		while($t = $db->fetch_array()){
			$jss[$t[0]][$t[1]] = "MET";
		}
		
		$db ->query("SELECT ex_detail,ex_content FROM feature_expression WHERE ex_symbol = '".$gene."'");
		while($t = $db->fetch_array()){
			$jss[$t[0]][$t[1]] = "EX";
		}
		
		$db ->query("SELECT phos_detail,phos_content FROM feature_phos WHERE phos_symbol = '".$gene."'");
		while($t = $db->fetch_array()){
			$jss[$t[0]][$t[1]] = "PHOS";
		}
		return $jss;
	}
	function site(){
		$db = new mysql("CGF","conn","utf8");
		
		$db ->query("SELECT * FROM primary_site");
		while($t = $db->fetch_array()){
			$site[$t[0]] = $t[1];
		}	
		return $site;
	}
	
	function findcase($db,$gene,$content,$table_name,$site,$primary_site){
		$sample_info = array();
		if($table_name === "SNV"){
			
			$db ->query("SELECT * FROM CosmicSample INNER JOIN (SELECT * FROM CosmicMutantExport WHERE Gene_name = '".$gene."' AND Mutation_AA = 'p.".$content."') b on CosmicSample.sample_id = b.ID_sample");
			$samples = array();
			
			while($t = $db->fetch_array()){
				if($site == $primary_site[$t["Primary_site"]]){
					array_push($samples,$t);
				}
				if($site == ""){
					array_push($samples,$t);
				}
			}
		}
		
		if($table_name === "CNV"){
			
			if(strcasecmp($content,"amplification")===0){
				$content ="gain";	
			}
			
			if(strcasecmp($content,"deletion")===0){
				$content ="loss";	
			}
			#$db ->query("SELECT ID_SAMPLE,Primary_site FROM CosmicCompleteCNA WHERE Gene_name = '".$gene."' AND MUT_TYPE = '".$content."'");
			$db -> query("SELECT * FROM CosmicSample INNER JOIN (SELECT * FROM CosmicCompleteCNA WHERE Gene_name = '".$gene."' AND MUT_TYPE = '".$content."') b on CosmicSample.sample_id = b.ID_SAMPLE");
			$samples = array();
			while($t = $db->fetch_array()){
				if($site == $primary_site[$t["Primary_site"]]){
					array_push($samples,$t);
				}
				if($site == ""){
					array_push($samples,$t);
				}
			}
		}
		
		if($table_name == "SV"){

			$genes = explode("-",$content);
			
			if($genes[1]==="."){
				$db ->query("SELECT * FROM CosmicSample INNER JOIN (SELECT * FROM CosmicFusionExport WHERE Translocation_Name LIKE '%".$genes[0]."{%') b ON CosmicSample.sample_id = b.Sample_ID");
			}
			else{
				$db ->query("SELECT * FROM CosmicSample INNER JOIN (SELECT * FROM CosmicFusionExport WHERE Translocation_Name LIKE '%".$genes[0]."{%' AND Translocation_Name LIKE '%".$genes[1]."{%') b ON CosmicSample.sample_id = b.Sample_ID");	
			}
			$samples = array();
			
			while($t = $db->fetch_array()){
				if($site == $primary_site[$t["Primary_site"]]){
					array_push($samples,$t);
				}
				if($site == ""){
					array_push($samples,$t);
				}
			}
		}
		
		if($table_name == "EX"){
			if(preg_match('/overexpression/i',$content)){
				$content ="over";	
			}
			
			if(preg_match('/underexpression/i',$content)){
				$content ="under";	
			}
			
			if(preg_match('/^expression$/i',$content)){
				$content ="normal";	
			}
			
			$db ->query("SELECT * FROM CosmicSample INNER JOIN (SELECT * FROM CosmicCompleteGeneExpression WHERE GENE_NAME = '".$gene."' AND REGULATION = '".$content."') b ON CosmicSample.sample_id = b.SAMPLE_ID");	
			$samples = array();
			while($t = $db->fetch_array()){
				if($site == $primary_site[$t["Primary_site"]]){
					array_push($samples,$t);
				}
				
				if($site == ""){
					array_push($samples,$t);
				}
			}
		}
//		$flag = array_filter($samples);
//		
//		if(!empty($flag)){
	array_filter($samples);
	return $samples;
//		}
//		else{
//			return "no data!";	
//		}
	}
?>          