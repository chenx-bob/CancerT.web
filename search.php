<?php

	require_once("lib/mysql.php");
	$db = new mysql("192.168.6.102","root","rlibs402","CGF","conn","utf8"); 
    include_once('lib/config.php');
	$smarty -> assign("title","Search Tool");
	
	if(isset($_GET['searchtype'])){
		$searchType = $_GET['searchtype'];
		$smarty -> assign("searchType",$searchType);
		switch ($searchType){
			case "tissue":
				
				if(isset($_GET['tissue'])){
					$tissue = $_GET['tissue'];
					$smarty -> assign("tissue",$tissue);
					$db -> query("SELECT * FROM cancer WHERE primary_site = '".$tissue."'");
					$tissues = array();		
					while($t = $db->fetch_array()){
						$tissues[$t['cancer_oncotree_name']]['level'] = $t['level'];
						$tissues[$t['cancer_oncotree_name']]['nci'] = $t['nci_id'];
						$tissues[$t['cancer_oncotree_name']]['umis'] = $t['umis_id'];
					}
					$cgs = array();
					$cgas = array();
					$ctgas = array();
					$sat = array();
					foreach($tissues as $cancer => $type){
						$cg = cg($cancer);
						$gene_num = count($cg);
						array_push($sat,array("cancer"=>$cancer,"level"=>$tissues[$cancer]["level"],"nci"=>$tissues[$cancer]["nci"],"umis"=>$tissues[$cancer]["umis"],"num"=>$gene_num));	
						$cgs = array_merge($cgs,$cg);
						$cgas = array_merge($cgas,cga($cancer));
						$ctgas = array_merge($ctgas,ctga($cancer));	
					}
					$smarty -> assign("type","tissue");
					$smarty -> assign("sat",$sat);
					$smarty -> assign("cg",$cgs);
					$smarty -> assign("cga",$cgas);
					$smarty -> assign("ctga",$ctgas);
				}
				
				if(isset($_GET['tumor'])){
					$cancer = $_GET['tumor'];
					$tissue = $_GET['tissue'];
					$smarty -> assign("type","cancer");
					$smarty -> assign("cancer",$cancer);
					$smarty -> assign("tissue",$tissue);
					$smarty -> assign("cg",cg($cancer));
					$smarty -> assign("cga",cga($cancer));
					$smarty -> assign("ctga",ctga($cancer));
				}
				
				if(isset($_GET['gene'])){
					$cancer = $_GET['tumor'];
					$tissue = $_GET['tissue'];
					$gene = $_GET['gene'];
					$smarty -> assign("type","gene");
					$smarty -> assign("cancer",$cancer);
					$smarty -> assign("tissue",$tissue);
					$smarty -> assign("gene",$gene);
					$smarty -> assign("cg",cg($cancer));
					$smarty -> assign("cga",cga($cancer));
					$smarty -> assign("ctga",ctga($cancer));
					$smarty -> assign("feature",feature($gene));
					
				}
				$smarty -> assign("level_status",level_status());
				$smarty -> display("search-result.html");
				break;
				
			case "case":
				$primary_site = site();
				$db_case = new mysql("192.168.6.102","root","rlibs402","COSMIC","conn","utf8");
				$sampleinfo =array();
				if(isset($_GET['variant']) && !isset($_GET['tissues'])){
					$gene = $_GET['gene'];
					$jss = feature($gene);
					$variant_detail = $_GET['variant'];
					$smarty -> assign("gene",$gene);
					$smarty -> assign("variant",$variant_detail);
					$contents = array_keys($jss[$variant_detail]);
					foreach($contents as $content){
						$table = $jss[$variant_detail][$content];
						$case = findcase($db_case,$gene,$content,$table,"",$primary_site);
						$sampleinfo=array_merge($sampleinfo,$case);
					}
					$smarty -> assign("sampleinfo",$sampleinfo);
				}
				
				if(isset($_GET['tissues']) && isset($_GET['variant'])){
					$tissue = $_GET['tissues'];
					$gene = $_GET['gene'];
					$jss = feature($gene);
					$variant_detail = $_GET['variant'];
					$smarty -> assign("gene",$gene);
					$smarty -> assign("variant",$variant_detail);
					$smarty -> assign("tissue",$tissue);
					$contents = array_keys($jss[$variant_detail]);
					foreach($contents as $content){
						$table = $jss[$variant_detail][$content];
						$case=array();
						$case = findcase($db_case,$gene,$content,$table,$tissue,$primary_site);
						$sampleinfo=array_merge($sampleinfo,$case);
					}
					$smarty -> assign("sampleinfo",$sampleinfo);
					
				}
				//print_r($sampleinfo);
				$smarty -> display("search-result.html");
				break;	
		}
		
	}
	else{
		$smarty -> display("search.html");
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
	
	function level_status(){
		$db = new mysql("192.168.6.102","root","rlibs402","CGF","conn","utf8");
		$db -> query('SELECT * FROM level_status');
		$level=array();
		while($t=$db->fetch_array()){
			$level[$t['db']][strtoupper($t['original_level'])] = $t['level'];
		}
		return $level;
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
	
	function feature($gene){
		$db = new mysql("192.168.6.102","root","rlibs402","CGF","conn","utf8");
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
		$db = new mysql("192.168.6.102","root","rlibs402","CGF","conn","utf8");
		
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
			echo $content."<br>";
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