library(ctc)
library(gplots)
library(dendextend)
library(graphics)
library(grDevices)
library(amap)
library(multiClust)


# Load the gene expression matrix
#data.exprs <- input_file(input = "/Users/cx/Desktop/CancerInfo/enrichment/tissueEnrich.txt")

data.exprs <- input_file(input="/Users/cx/Desktop/CancerInfo/enrichment/tissueEnrich.txt")
gene_num <- number_probes(input = "/Users/cx/Desktop/CancerInfo/enrichment/tissueEnrich.txt", 
                          data.exp=data.exprs, Fixed=NULL,
                          Percent=30, Poly=NULL, Adaptive=NULL, cutoff=NULL)
ranked.exprs <- probe_ranking(
  input = "/Users/cx/Desktop/CancerInfo/enrichment/tissueEnrich.txt",
  probe_number = gene_num,
  probe_num_selection = "Fixed_Probe_Num",
  data.exp = data.exprs,
  method = "SD_Rank"
)
cluster_num <- number_clusters(data.exp = data.exprs,
                               Fixed = NULL,
                               gap_statistic = TRUE)
# Call the cluster_analysis function
hclust_analysis <- cluster_analysis(
  sel.exp = ranked.exprs,
  cluster_type = "HClust",
  seed = NULL,
  distance = "euclidean",
  linkage_type = "ward.D2",
  gene_distance = "correlation",
  num_clusters = cluster_num,
  data_name = "CGF1",
  probe_rank = "SD_Rank",
  probe_num_selection = "Fixed_Probe_Num",
  cluster_num_selection = "Fixed_Clust_Num"
)
