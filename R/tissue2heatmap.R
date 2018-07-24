library(pheatmap)
da<-read.delim("/Users/cx/Desktop/CancerInfo/enrichment/test1.txt",header=T)
row.names(da)<-da$ID
#col.names(da)<-
da_new<-da[-1] #去除第一列的表头
mat_da<-as.matrix(da_new)
#heatmap(mat_da,cexCol = 0.8, cexRow = 0.8, scale = "row")
tt_heatmap <- pheatmap(mat_da,cellwidth = 10, cellheight = 10,fontsize=10, fontsize_row=10,cluster_cols = FALSE,cutree_row = 15,scale = "row",color=colorRampPalette(rev(c("red","white","blue")))(102))
#tt_heatmap <- pheatmap(mat_da,cellwidth = 10, cellheight = 10,fontsize=10, fontsize_row=10,cluster_cols = FALSE,cutree_row = 10,scale = "row",color=colorRampPalette(c("navy", "white", "firebrick3"))(50))

library(pheatmap)
da<-read.delim("/Users/cx/Desktop/CancerInfo/enrichment/heatmap1.txt",header=T)
#da<-read.delim("/Users/cx/Desktop/CancerInfo/enrichment/test1.txt",header=T)
row.names(da)<-da$ID
#col.names(da)<-
da_new<-da[-1] #去除第一列的表头
mat_da<-as.matrix(da_new)
#heatmap(mat_da,cexCol = 0.8, cexRow = 0.8, scale = "row")
#tt_heatmap <- pheatmap(mat_da,cellwidth = 10, cellheight = 10,fontsize=10, fontsize_row=10,cluster_cols = FALSE,cutree_row = 15,scale = "row",color=colorRampPalette(rev(c("red","white","blue")))(102))
tt_heatmap <- pheatmap(mat_da,cellwidth = 8, cellheight = 8,fontsize=8, fontsize_row=8,cluster_cols = FALSE,cluster_rows = FALSE,color=colorRampPalette(c("white", "red"))(110))
