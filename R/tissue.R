rt1 <- read.csv("/Users/cx/Desktop/fs.txt",sep = "\t",head=T);
feq=c(tissues$Freq[tissues$Var1==cc[1]],tissues$Freq[tissues$Var1==cc[2]],tissues$Freq[tissues$Var1==cc[3]],tissues$Freq[tissues$Var1==cc[4]],tissues$Freq[tissues$Var1==cc[5]],tissues$Freq[tissues$Var1==cc[6]],tissues$Freq[tissues$Var1==cc[7]],tissues$Freq[tissues$Var1==cc[8]],tissues$Freq[tissues$Var1==cc[9]],tissues$Freq[tissues$Var1==cc[10]],tissues$Freq[tissues$Var1==cc[11]],tissues$Freq[tissues$Var1==cc[12]],tissues$Freq[tissues$Var1==cc[13]],tissues$Freq[tissues$Var1==cc[14]],tissues$Freq[tissues$Var1==cc[15]],tissues$Freq[tissues$Var1==cc[16]],tissues$Freq[tissues$Var1==cc[17]],tissues$Freq[tissues$Var1==cc[18]],tissues$Freq[tissues$Var1==cc[19]],tissues$Freq[tissues$Var1==cc[20]],tissues$Freq[tissues$Var1==cc[21]],tissues$Freq[tissues$Var1==cc[22]],tissues$Freq[tissues$Var1==cc[23]],tissues$Freq[tissues$Var1==cc[24]],tissues$Freq[tissues$Var1==cc[25]],tissues$Freq[tissues$Var1==cc[26]],tissues$Freq[tissues$Var1==cc[27]],tissues$Freq[tissues$Var1==cc[28]],tissues$Freq[tissues$Var1==cc[29]])
for(i in 1:8663){
  vv=as.numeric(rt1[i,4:32]/feq)
  me=sum(vv)/29
  sd=sd(vv)
  for(j in 1:29){
    #rt1[i,3+j]= pt(vv[j],df=29,ncp=abs(vv[j]-me)*sqrt(29)/sd)  
    #rt1[i,3+j]= (vv[j]-me)*sqrt(29)/sd
    zz=as.numeric((vv[j]-me)*sqrt(29)/sd)
    if(!is.na(zz) && zz > 0) {
      xx = pt(vv[j],df=29,ncp=abs(vv[j]-me)*sqrt(29)/sd)
      if(xx<0.05){
        rt1[i,3+j]=xx
        }
      else
      {
          rt1[i,3+j]=0
          }
    }
    else{
      rt1[i,3+j]=0
    }
  }
}
#write.csv(x = rt1,sep = "\t",file = "/Users/cx/Desktop/fs_pvalue.csv")
#write.csv(x = rt1,sep = "\t",file = "/Users/cx/Desktop/fs_zscore.csv")
write.csv(x = rt1,sep = "\t",file = "/Users/cx/Desktop/fs_pvalue2.csv")
for(i in names(all.biomarkers)){
  s
  writeData(wb,i,all.biomarkers."i")
  }
