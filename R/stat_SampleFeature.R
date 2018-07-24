library(ggplot2)
library(devtools)
library(easyGgplot2)
rt <- read.table("/Users/chenxiang/Desktop/1.txt",sep = "\t",head=FALSE);
colnames(rt) = c("sample","tissue","num","num1")
tissues=data.frame(table(rt$tissue))
n=1
status=c()
ti=c()
p=c()
for(tis in tissues$Var1){
  ti = c(ti,rep(tis,times=5))
  status = c(status,c("0","1","2","3","4"))
  
  print(tis)
  tissues$ave1[tissues$Var1==tis]=(sum(rt$num[which(rt$tissue==tis)])/tissues$Freq[tissues$Var1==tis])-1
  tissues$ave2[tissues$Var1==tis]=(sum(rt$num1[which(rt$tissue==tis)])/tissues$Freq[tissues$Var1==tis])-1
  x=data.frame(table(rt$num[rt$tissue==tis])) ##特定组织中特证数目的表统计
  p1 = sum(x$Freq[x$Var1==1])/tissues$Freq[tissues$Var1==tis]
  p2 = sum(x$Freq[x$Var1==2])/tissues$Freq[tissues$Var1==tis]
  p3 = sum(x$Freq[x$Var1==3])/tissues$Freq[tissues$Var1==tis]
  p4 = sum(x$Freq[x$Var1==4])/tissues$Freq[tissues$Var1==tis]
  pe4 = 1-p1-p2-p3-p4
  p = c(p,c(p1,p2,p3,p4,pe4))
}
stack=data.frame("tissue" = ti,"status" = status,"p" = p)
stack$tissue=sapply(stack$tissue,function(x) gsub(".*\\(","",gsub("\\).*","",x)))
tissues$Var1=sapply(tissues$Var1,function(x) gsub(".*\\(","",gsub("\\).*","",x)))
fq = ggplot(data=tissues, aes(x=Var1, y=Freq)) + geom_bar(stat="identity",fill = "red",colour = "black") + theme_bw()+ theme(axis.text.x =element_blank(),axis.ticks.x=element_blank(),axis.title.y = element_text(margin = margin(r=5,unit = "pt"))) +labs(x = "", y = "Total") 
ave1 = ggplot(data=tissues, aes(x=Var1, y=ave1)) + geom_bar(stat="identity",fill = "red",colour = "black") + theme_bw() + theme(axis.text.x =element_blank(),axis.ticks.x=element_blank(),axis.title.y = element_text(margin = margin(r=26,unit = "pt")))+labs(x = "", y = "Mutation")
ave2 = ggplot(data=tissues, aes(x=Var1, y=ave2)) + geom_bar(stat="identity",fill = "red",colour = "black") + theme_bw() + theme(axis.text.x =element_blank(),axis.ticks.x=element_blank(),axis.title.y = element_text(margin = margin(r=23,unit = "pt")))+labs(x = "" ,y = "Gene")
pw = ggplot(data=stack, aes(x=tissue, y=p,fill=status)) + geom_bar(stat="identity",position = position_stack(reverse = TRUE)) + theme_bw()+theme(axis.text.x = element_text(face = "bold", vjust = 1, hjust = 1, angle = 45),axis.ticks.x=element_blank(),axis.title.y = element_text(margin = margin(r=12,unit = "pt")),legend.position = "bottom",legend.margin=margin(t=-18,unit = "pt")) +labs(x = "", y = "Sample (%)")
ggplot2.multiplot(fq,ave1,ave2,pw, cols=1)
