<?php 
    include_once('lib/config.php');
	require_once("lib/mysql.php");
	$smarty -> assign("title","Help");
	$smarty -> assign("st",$_GET);
	$smarty -> assign("genelist",pct_genelist());
	if(isset($_GET['gene'])){
		$smarty -> assign("gene",$_GET['gene']);
		$smarty -> assign("pct_drug",pct_drug($_GET['gene']));
		$smarty -> assign("pct_gene",pct_gene($_GET['gene']));
	}
	$smarty -> assign("drugs",drug());
	$smarty -> display("help.html");
	
	
	function drug(){
		$db = new mysql("192.168.6.102","root","rlibs402","CGF","conn","utf8");
		$db -> query('select * from Drug');
		$drugs = array();
		while($t = $db->fetch_array()){
			array_push($drugs,$t);	
		}
		return $drugs;
	}
	
	function pct_drug($gene){
		$db = new mysql("192.168.6.102","root","rlibs402","CGF","conn","utf8");
		$db -> query('select * from pct_therapy_drug where gene="'.$gene.'"');
		$pct_drug = array();
		while($t = $db->fetch_array()){
			array_push($pct_drug,$t);	
		}
		return $pct_drug;
	}
	function pct_gene($gene){
		$db = new mysql("192.168.6.102","root","rlibs402","CGF","conn","utf8");
		$db -> query('select * from pct_therapy where gene="'.$gene.'"');
		$pct_gene = array();
		$pct_gene = $db->fetch_array();
		return $pct_gene;
	}
	
	function pct_genelist(){
		$db = new mysql("192.168.6.102","root","rlibs402","CGF","conn","utf8");
		$db -> query('select * from pct_therapy_drug');
		$genelist = array();
		while($t = $db->fetch_array()){
			$genelist[$t['gene']] = 1;	
		}
		return $genelist;
	}
?>          