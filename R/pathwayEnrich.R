DEfile = "/Users/cx/Desktop/CancerInfo/enrichment/tissue_gene.txt"
geneDE = read.table(DEfile, sep = "\t", header = F)
gene = geneDE[, 2]
gene = as.character(gene)
entrezIDs = mget(gene, org.Hs.egSYMBOL2EG, ifnotfound = NA)
entrezIDs1 = unique(as.character(entrezIDs))
mat=matrix(x,nrow=1,ncol=length(entrezIDs1),dimnames=list('',entrezIDs1))
pv.out <- pathview(gene.data = mat[1,], pathway.id = "05200", species = "hsa", out.suffix = "cancer.pathway", kegg.native = T, same.layer = F,plot.col.key = FALSE,res=300)
