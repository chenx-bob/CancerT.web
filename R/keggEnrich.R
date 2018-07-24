#!/usr/bin/Rscripts

library("org.Hs.eg.db")
library("GSEABase")
library("GOstats")
library("Category")
args = commandArgs()
DEfile = "/Users/cx/Desktop/CancerInfo/enrichment/tissue_gene.txt"
resPath = "/Users/cx/Desktop/CancerInfo/enrichment/KEGG"

keggfile = paste(resPath, "keggHmOut.csv", sep = "/")

if (file.exists(resPath) == FALSE)
  dir.create(resPath)
#read data from file
geneDE = read.table(DEfile, sep = "\t", header = F)
gene = geneDE[, 2]
gene = as.character(gene)
#change to entrezIDs
entrezIDs = mget(gene, org.Hs.egSYMBOL2EG, ifnotfound = NA)
entrezIDs = as.character(entrezIDs)
tissue = unique(geneDE[, 1])


keggAnn = get("org.Hs.egPATH")
kegguniverse = Lkeys(keggAnn)
keggparams = new(
  "KEGGHyperGParams",
  geneIds = entrezIDs,
  universeGeneIds = kegguniverse,
  annotation = "org.Hs.eg.db",
  categoryName = "KEGG",
  pvalueCutoff = 0.05,
  testDirection = "over"
)
over = hyperGTest(keggparams)
kegg = summary(over)
glist = geneIdsByCategory(over)
glist = sapply(glist, function(.ids) {
  .sym = mget(.ids, envir = org.Hs.egSYMBOL, ifnotfound = NA)
  .sym[is.na(.sym)] = .ids[is.na(.sym)]
  paste(.sym, collapse = ";")
})
kegg$Symbols = glist[as.character(kegg$KEGGID)]
write.csv(kegg, file = keggfile)

##plot
pdf(paste(resPath, "kegg.pdf", sep = "/"),
    width = 7,
    height = 7)
data = kegg[, 5][1:10]
maxdata = max(data) + 5
labs = kegg[, 7][1:10]
par(mar = c(14, 5, 4.1, 2.1), cex = 0.8)
barplot(
  data,
  col = "steelblue",
  ylim = c(0, maxdata),
  width = 1,
  space = 1,
  ylab = "Gene Number",
  las = 1,
  main = "KEGG enrichment"
)
text(
  x = seq(1.5, 19.5, by = 2),
  y = -0.2,
  srt = 45,
  adj = 1,
  labels = labs,
  xpd = TRUE
)
abline(h = 0)
dev.off()

#kegg enrichment analysis
for (t in tissue) {
  data = term = c()
  geneTissue = geneDE[which(geneDE$V1 == t), 2]
  geneTissue = as.character(geneTissue)
  TissueEntrezIDs = mget(geneTissue, org.Hs.egSYMBOL2EG, ifnotfound = NA)
  TissueEntrezIDs = as.character(TissueEntrezIDs)
  keggparams = new(
    "KEGGHyperGParams",
    geneIds = TissueEntrezIDs,
    universeGeneIds = entrezIDs,
    annotation = "org.Hs.eg.db",
    categoryName = "KEGG",
    pvalueCutoff = 0.05,
    testDirection = "over"
  )
  over = hyperGTest(keggparams)
  kegg = summary(over)
  glist = geneIdsByCategory(over)
  glist = sapply(glist, function(.ids) {
    .sym = mget(.ids, envir = org.Hs.egSYMBOL, ifnotfound = NA)
    .sym[is.na(.sym)] = .ids[is.na(.sym)]
    paste(.sym, collapse = ";")
  })
  keggfile = paste(resPath, paste(t, "_keggHmOut.csv"), sep = "/")
  kegg$Symbols = glist[as.character(kegg$KEGGID)]
  write.csv(kegg, file = keggfile)
  
  ##plot
  if (length(data) > 0) {
    pdf(paste(resPath, paste(t, "_kegg.pdf"), sep = "/"),
        width = 7,
        height = 7)
    data = kegg[, 5][1:10]
    labs = kegg[, 7][1:10]
    labs = labs[complete.cases(data)]
    data = data[complete.cases(data)]
    maxdata = max(data) + 5
    x1 = seq(1.5, 19.5, by = 2)
    x1 = x1[1:length(data)]
    
    par(mar = c(14, 5, 4.1, 2.1), cex = 0.8)
    barplot(
      data,
      col = "steelblue",
      ylim = c(0, maxdata),
      width = 1,
      space = 1,
      ylab = "Gene Number",
      las = 1,
      main = "KEGG enrichment"
    )
    text(
      x = x1,
      y = -0.2,
      srt = 45,
      adj = 1,
      labels = labs,
      xpd = TRUE
    )
    abline(h = 0)
    dev.off()
  }
}
