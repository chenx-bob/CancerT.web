library(ggplot2)  
tissues = read.table("/Users/cx/Desktop/seer.tsv",header=T,sep="\t")  
t = data.frame(tissues)
# 画图  
p = ggplot(tissues,aes(Lifetime_Risk,five_years_surviving))  
p=p + geom_point()  
# 改变点的大小  
p=p + geom_point(aes(size=surviving_time))  
# 四维数据的展示
pbubble = p + geom_point(aes(size=surviving_time,color=tissue)) 
# 自定义渐变颜色  
#pbubble =pbubble+ scale_colour_gradient(low="green",high="red")  
# 绘制pathway富集散点图  Tumorigenesis tissue statistics from SEER database
pr = pbubble + labs(color="Tissue Name",size="Survival Time (year)",  
       x="Lifetime Risk (%)",y="5-years Survival Rate (%)",title="")  
# 改变图片的样式（主题）  
pr=pr + theme_bw()  
pr + coord_fixed(ratio = 0.17)
