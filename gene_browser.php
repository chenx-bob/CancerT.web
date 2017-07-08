<?php 
    include_once('lib/config.php');
	require_once("lib/mysql.php");
	
	$db -> query("SELECT DISTINCT element_symbol FROM element_cancer_interpretation");
	while($t = $db->fetch_array()){
		$f = $t[0][0];
		$genes[strtoupper($f)][$t[0]] = 1;	
	}
	ksort($genes);
	$smarty -> assign("title","Gene Browser");
	if(isset($_GET['start'])){
		$start = $_GET['start'];
		$gene = $_GET['gene'];
		$start_genes = array_keys($genes[strtoupper($start)]);
		unset($genes[$start]);
		
		# 左侧导航显示
		$smarty -> assign("start",$start);
		$smarty -> assign("genes",$genes);
		$smarty -> assign("start_genes",$start_genes);
		
		# 右侧内容显示 tab1
		$smarty -> assign("gene",$gene);	
		$geneinfo = geneinfo($gene);
		$smarty -> assign("summary","");
		$smarty -> assign("name","");
		$smarty -> assign("entrezgene_id","");
		$smarty -> assign("ensembl_id","");
		$smarty -> assign("alias","");
		$smarty -> assign("chr","");
		$smarty -> assign("s","");
		$smarty -> assign("e","");
		$smarty -> assign("strand","");
		if(isset($geneinfo[$gene]["summary"])){
			$summary = array_keys($geneinfo[$gene]["summary"]);
			$smarty -> assign("summary",$summary[0]);
		}
		if(isset($geneinfo[$gene]["name"])){
			$name = array_keys($geneinfo[$gene]["name"]);
			$smarty -> assign("name",$name[0]);
		}
		if(isset($geneinfo[$gene]["entrezgene_id"])){
			$entrezgene_id = array_keys($geneinfo[$gene]["entrezgene_id"]);
			$smarty -> assign("entrezgene_id",$entrezgene_id[0]);
		}
		if(isset($geneinfo[$gene]["alias"])){
			$alias = array_keys($geneinfo[$gene]["alias"]);
			$smarty -> assign("alias",$alias[0]);
		}
		if(isset($geneinfo[$gene]["ensembl_id"])){
			$ensembl_id = array_keys($geneinfo[$gene]["ensembl_id"]);
			$smarty -> assign("ensembl_id",$ensembl_id[0]);
		}
		if(isset($geneinfo[$gene]["chr"])){
			$chr = array_keys($geneinfo[$gene]["chr"]);
			$smarty -> assign("chr",$chr[0]);
		}
		if(isset($geneinfo[$gene]["start"])){
			$start = array_keys($geneinfo[$gene]["start"]);
			$smarty -> assign("s",$start[0]);
		}
		if(isset($geneinfo[$gene]["end"])){
			$end = array_keys($geneinfo[$gene]["end"]);
			$smarty -> assign("e",$end[0]);
		}
		if(isset($geneinfo[$gene]["strand"])){
			$strand = array_keys($geneinfo[$gene]["strand"]);
			$smarty -> assign("strand",$strand[0]);
		}
		$interpro = array();
		$go = array();
		$path = array();
		
		# GO CC
		if(isset($geneinfo[$gene]["GO CC"])){
			$gocc = array_keys($geneinfo[$gene]["GO CC"]);
			foreach($gocc as $cc){
				$t = explode(';',$cc);
				array_push($go,array("type"=>"GO CC","id"=>$t[1],"content"=>$t[0],"level"=>$t[2]));	
			}
		}
		# GO BP
		if(isset($geneinfo[$gene]["GO BP"])){
			$x = array_keys($geneinfo[$gene]["GO CC"]);
			foreach($x as $cc){
				$t = explode(';',$cc);
				array_push($go,array("type"=>"GO BP","id"=>$t[1],"content"=>$t[0],"level"=>$t[2]));	
			}
		}
		# GO MF
		if(isset($geneinfo[$gene]["GO MF"])){
			$x = array_keys($geneinfo[$gene]["GO MF"]);
			foreach($x as $cc){
				$t = explode(';',$cc);
				array_push($go,array("type"=>"GO MF","id"=>$t[1],"content"=>$t[0],"level"=>$t[2]));	
			}
		}
		# kegg
		if(isset($geneinfo[$gene]["kegg"])){
			$x = array_keys($geneinfo[$gene]["kegg"]);
			foreach($x as $cc){
				$t = explode(';',$cc);
				array_push($path,array("type"=>"KEGG","id"=>$t[0],"content"=>$t[1]));	
			}
		}
		# pid
		if(isset($geneinfo[$gene]["pid"])){
			$x = array_keys($geneinfo[$gene]["pid"]);
			foreach($x as $cc){
				$t = explode(';',$cc);
				array_push($path,array("type"=>"PID","id"=>$t[0],"content"=>$t[1]));	
			}
		}
		#netpath
		if(isset($geneinfo[$gene]["netpath"])){
			$x = array_keys($geneinfo[$gene]["netpath"]);
			foreach($x as $cc){
				$t = explode(';',$cc);
				array_push($path,array("type"=>"NETPATH","id"=>$t[0],"content"=>$t[1]));	
			}
		}
		#reactome
		if(isset($geneinfo[$gene]["reactome"])){
			$x = array_keys($geneinfo[$gene]["reactome"]);
			foreach($x as $cc){
				$t = explode(';',$cc);
				array_push($path,array("type"=>"REACTOME","id"=>$t[0],"content"=>$t[1]));	
			}
		}
		#smpdb
		if(isset($geneinfo[$gene]["smpdb"])){
			$x = array_keys($geneinfo[$gene]["smpdb"]);
			foreach($x as $cc){
				$t = explode(';',$cc);
				array_push($path,array("type"=>"SMPDB","id"=>$t[0],"content"=>$t[1]));	
			}
		}
		#wikipathways
		if(isset($geneinfo[$gene]["wikipathways"])){
			$x = array_keys($geneinfo[$gene]["wikipathways"]);
			foreach($x as $cc){
				$t = explode(';',$cc);
				array_push($path,array("type"=>"WIKIPATHWAYS","id"=>$t[0],"content"=>$t[1]));	
			}
		}
		#biocarta
		if(isset($geneinfo[$gene]["biocarta"])){
			$x = array_keys($geneinfo[$gene]["biocarta"]);
			foreach($x as $cc){
				$t = explode(';',$cc);
				array_push($path,array("type"=>"BIOCARTA","id"=>$t[0],"content"=>$t[1]));	
			}
		}
		#interpro
		if(isset($geneinfo[$gene]["interpro"])){
			$x = array_keys($geneinfo[$gene]["interpro"]);
			foreach($x as $cc){
				$t = explode(';',$cc);
				array_push($interpro,array("type"=>"INTERPRO","id"=>$t[1],"content"=>$t[0],"level"=>$t[2]));	
			}
		}
		#pharmgkb
		if(isset($geneinfo[$gene]["pharmgkb"])){
			$x = array_keys($geneinfo[$gene]["pharmgkb"]);
			foreach($x as $cc){
				$t = explode(';',$cc);
				array_push($path,array("type"=>"PHARMGKB","id"=>$t[0],"content"=>$t[1]));	
			}
		}
		#humancyc
		if(isset($geneinfo[$gene]["humancyc"])){
			$x = array_keys($geneinfo[$gene]["humancyc"]);
			foreach($x as $cc){
				$t = explode(';',$cc);
				array_push($path,array("type"=>"HUMANCYC","id"=>$t[0],"content"=>$t[1]));	
			}
		}
		
		$smarty -> assign("interpro",$interpro);
		$smarty -> assign("go",$go);
		$smarty -> assign("pathway",$path);
		
		# 显示在tab2
		$gic = GIC($gene);
		$smarty -> assign("gic",$gic);
		$gaic = GAIC($gene);
		$smarty -> assign("gaic",$gaic);
		$gaict = GAICT($gene);
		$smarty -> assign("gaict",$gaict);
		$smarty -> assign("level_status",level_status());
		$smarty -> display("gene_browser-3.html");
	}
	else{
		$smarty -> assign("genes",$genes);
		$smarty -> display("gene_browser-1.html");
	}
	//function go(){
//		return '['.json_encode(array("type"=>"GO MF","id"=>"A","content"=>"aaaaaa","level"=>"1234")).']';	
//	}
	function geneinfo($gene){
		
		$db -> query("SELECT * FROM gene_info WHERE gene_symbol = '".$gene."'");
		$n = 0;
		$geneinfo = array();
		while($t = $db->fetch_array()){
			$geneinfo[$t['gene_symbol']][$t['type']][$t['des']]=1;	
		}
		return $geneinfo;
	}
	
	function GIC($gene){
		
		$db -> query('SELECT * FROM element_cancer_interpretation WHERE element_symbol ="'.$gene.'"');
		$t = $db->fetch_array();
		
		return $t;
	}
	function GAIC($gene){
		
		$db -> query("SELECT a.* FROM element_alteration_cancer_interpretation a INNER JOIN ((SELECT phos_detail FROM feature_phos WHERE phos_symbol = '".$gene."') UNION (SELECT snv_detail FROM feature_snv  WHERE snv_symbol = '".$gene."') UNION (SELECT sv_detail FROM feature_sv  WHERE sv_symbol = '".$gene."') UNION (SELECT cnv_detail FROM feature_cnv  WHERE cnv_symbol = '".$gene."') UNION (SELECT ex_detail FROM feature_expression  WHERE ex_symbol = '".$gene."') UNION (SELECT met_detail FROM feature_methylation  WHERE met_symbol = '".$gene."')) b ON a.element_alteration_detail = b.phos_detail");
		$gaic = array();
		while($t = $db->fetch_array()){
			array_push($gaic,$t);	
		}
		return $gaic;
	}
	function GAICT($gene){
		
		$db -> query("SELECT a.* FROM element_alteration_drug_cancer_interpretation a INNER JOIN ((SELECT phos_detail FROM feature_phos WHERE phos_symbol = '".$gene."') UNION (SELECT snv_detail FROM feature_snv  WHERE snv_symbol = '".$gene."') UNION (SELECT sv_detail FROM feature_sv  WHERE sv_symbol = '".$gene."') UNION (SELECT cnv_detail FROM feature_cnv  WHERE cnv_symbol = '".$gene."') UNION (SELECT ex_detail FROM feature_expression  WHERE ex_symbol = '".$gene."') UNION (SELECT met_detail FROM feature_methylation  WHERE met_symbol = '".$gene."')) b ON a.element_alteration_detail = b.phos_detail");
		$gaict = array();
		while($t = $db->fetch_array()){
			array_push($gaict,$t);	
		}
		return $gaict;
	}
	function level_status(){
		
		$db -> query('SELECT * FROM level_status');
		$level=array();
		while($t=$db->fetch_array()){
			$level[$t['db']][strtoupper($t['original_level'])] = $t['level'];
		}
		return $level;
	}
?>          