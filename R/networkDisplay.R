library(STRINGdb)
string_db <- STRINGdb$new( version="10", species=9606,score_threshold=0, input_directory="" )
x = read.table("/Users/cx/Desktop/CancerInfo/enrichment/tissue_gene.txt")
uni = array(unique(x$V1))
#pdf(file=paste("cancer_network.pdf"))
dev.new(width=50,height=250)
par(mfrow = c(10, 3))
for(i in uni){
  y=as.data.frame(x[x$V1==i,2])
  colnames(y)="gene"
  example1_mapped <- string_db$map(y,"gene", removeUnmappedRows = TRUE)
  hits <- example1_mapped$STRING_id
  #pdf(file=paste(i,".pdf"))
  if(length(hits)>400){
    string_db$plot_network( hits[1:400])
  }
  else{
    string_db$plot_network( hits )
  }
  #dev.off()
}
dev.off()
