library('caret')
library('klaR')
library('pROC')
my<-read.csv(file=file.choose(),header = F,col.names = 1:26244)
t1 <- t(data.frame(my))
mydata <- as.data.frame(t1,row.names=F)
str(mydata)
summary(mydata)
set.seed(12)
index <- sample(1:nrow(mydata), size = 0.75*nrow(mydata))
train <- mydata[index,]
test <- mydata[-index,]
prop.table(table(mydata$V1))
prop.table(table(train$V1))
prop.table(table(test$V1))
#构建rfe函数的控制参数(使用随机森林函数和10重交叉验证抽样方法，并抽取5组样本)
rfeControls_rf <- rfeControl(functions = rfFuncs,method = 'cv',repeats = 5)
#使用rfe函数进行特征选择
fs_nb <- rfe(x = train[,-1],y = train[,1],sizes = seq(4,ncol(train[,-1]),100),rfeControl = rfeControls_rf)
# 结果显示，21个变量中，只需要选择6个变量即可
plot(fs_nb, type = c('g','o'))
# 所需要选择的变量是：
fs_nb$optVariables
# 接下来，我们就针对这6个变量，使用朴素贝叶斯算法进行建模和预测
vars <- c('V1',fs_nb$optVariables)
# 使用klaR包中的NaiveBayes函数构建朴素贝叶斯算法
fit <- NaiveBayes(type ~ ., data = train[,vars])
# 预测
pred <- predict(fit, newdata = test[,vars][,-1])
# 构建混淆矩阵
freq <- table(pred$class, test[,1])
# 模型的准确率
accuracy <- sum(diag(freq))/sum(freq)
# 模型的AUC值
modelroc <- roc(as.integer(iris[,5]),as.integer(c))
# 绘制ROC曲线
plot(modelroc, print.auc = TRUE, auc.polygon = TRUE, grid = c(0.1,0.2), grid.col = c('green','red'),max.auc.polygon = TRUE, auc.polygon.col = 'steelblue')
