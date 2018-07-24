#!/usr/bin/Rscript

library(org.Hs.eg.db)
library(GSEABase)
library(GOstats)
library(GO.db)
library(topGO)
library(Category)

DEfile = "/Users/cx/Desktop/CancerInfo/enrichment/tissue_gene.txt"
resPath = "/Users/cx/Desktop/CancerInfo/enrichment/GO"
dir.create(resPath)
geneDE = read.table(DEfile, sep = "\t", header = F)
tissue = unique(geneDE[, 1])

gene = geneDE[, 2]
gene = as.character(gene)
go = c("BP", "MF", "CC")

goAnn = get("org.Hs.egGO")
universe = Lkeys(goAnn)
entrezIDs = mget(gene, org.Hs.egSYMBOL2EG, ifnotfound = NA)
entrezIDs = as.character(entrezIDs)

data = term = c()
pdf(paste(resPath, "goDAG.pdf", sep = "/"),
    width = 7,
    height = 7)
for (i in 1:length(go)) {
  params = new(
    "GOHyperGParams",
    geneIds = entrezIDs,
    universeGeneIds = universe,
    annotation = "org.Hs.eg.db",
    ontology = go[i],
    pvalueCutoff = 0.01,
    conditional = FALSE,
    testDirection = "over"
  )
  over <- hyperGTest(params)
  goid = geneIdsByCategory(over)
  glist = sapply(goid, function(.ids) {
    .sym = mget(.ids, envir = org.Hs.egSYMBOL, ifnotfound = NA)
    .sym[is.na(.sym)] = .ids[is.na(.sym)]
    paste(.sym, collapse = ";")
  })
  
  if (identical(go[i], "BP")) {
    bp = summary(over)
    bp$Symbols = glist[as.character(bp$GOBPID)]
    
  }
  if (identical(go[i], "MF")) {
    bp = summary(over)
    bp$Symbols = glist[as.character(bp$GOMFID)]
    
  }
  if (identical(go[i], "CC")) {
    bp = summary(over)
    bp$Symbols = glist[as.character(bp$GOCCID)]
    
  }
  data = c(data, bp[, 5][1:5])
  term = c(term, bp[, 7][1:5])
  str = paste(go[i], "GOenrichOut.csv", sep = "_")
  goresPath = paste(resPath, str, sep = "/")
  write.csv(bp, file = goresPath)
  
  all_genes = factor(as.integer(universe %in% entrezIDs))
  names(all_genes) = entrezIDs
  GOdata <- new(
    "topGOdata",
    ontology = go[i],
    allGenes = all_genes,
    geneSel = function(p) {
      p < 1
    },
    description = "GO analysis of ALL data: Differential Expression ",
    annot = annFUN.org,
    mapping = "org.Hs.eg.db",
    ID = "Entrez"
  )
  resultFisher <-
    runTest(GOdata, algorithm = "classic", statistic = "fisher")
  GenTable(GOdata, classicFisher = resultFisher, topNodes = 10)
  
  showSigOfNodes(GOdata,
                 score(resultFisher),
                 firstSigNodes = 5,
                 useInfo = 'all')
  par(mar = c(2.1, 2.1, 2.1, 2.1), cex = 0.8)
  printGraph(
    GOdata,
    resultFisher,
    firstSigNodes = 5,
    useInfo = "all",
    pdfSW = TRUE
  )
  titleStr = paste("GO", go[i], "DAG analysis")
  title(main = titleStr)
}
dev.off()

#plot
pdf(paste(resPath, "GO.pdf", sep = "/"),
    width = 7,
    height = 7)
maxdata = max(data) + 50
labs = term
cols = rep(c("steelblue", "mediumturquoise", "sandybrown"), each = 5)
par(mar = c(25, 20, 4.1, 2.1), cex = 0.8)
barplot(
  data,
  col = cols,
  ylim = c(0, maxdata),
  width = 1,
  space = 1,
  ylab = "Gene Number",
  las = 1,
  main = "GO enrichment"
)
text(
  x = seq(1.5, 29.5, by = 2),
  y = -1,
  srt = 45,
  adj = 1,
  labels = labs,
  xpd = TRUE
)
legend(
  "topright",
  inset = .05,
  c(
    "Biological process",
    "Molecular function",
    "Cellular component"
  ),
  fill = c("steelblue", "mediumturquoise", "sandybrown"),
  horiz = FALSE
)
abline(h = 0)
dev.off()


### tissue part
for (t in tissue) {
  data = term = c()
  pdf(paste(resPath, paste(t, "_goDAG.pdf"), sep = "/"),
      width = 7,
      height = 7)
  geneTissue = geneDE[which(geneDE$V1 == t), 2]
  geneTissue = as.character(geneTissue)
  TissueEntrezIDs = mget(geneTissue, org.Hs.egSYMBOL2EG, ifnotfound = NA)
  TissueEntrezIDs = as.character(TissueEntrezIDs)
  for (i in 1:length(go)) {
    params = new(
      "GOHyperGParams",
      geneIds = TissueEntrezIDs,
      universeGeneIds = entrezIDs,
      annotation = "org.Hs.eg.db",
      ontology = go[i],
      pvalueCutoff = 0.01,
      conditional = FALSE,
      testDirection = "over"
    )
    over <- hyperGTest(params)
    goid = geneIdsByCategory(over)
    glist = sapply(goid, function(.ids) {
      .sym = mget(.ids, envir = org.Hs.egSYMBOL, ifnotfound = NA)
      .sym[is.na(.sym)] = .ids[is.na(.sym)]
      paste(.sym, collapse = ";")
    })
    
    if (identical(go[i], "BP")) {
      bp = summary(over)
      bp$Symbols = glist[as.character(bp$GOBPID)]
      
    }
    if (identical(go[i], "MF")) {
      bp = summary(over)
      bp$Symbols = glist[as.character(bp$GOMFID)]
      
    }
    if (identical(go[i], "CC")) {
      bp = summary(over)
      bp$Symbols = glist[as.character(bp$GOCCID)]
      
    }
    data = c(data, bp[, 5][1:5])
    term = c(term, bp[, 7][1:5])
    str = paste(t, go[i], "GOenrichOut.csv", sep = "_")
    goresPath = paste(resPath, str, sep = "/")
    write.csv(bp, file = goresPath)
    
    all_genes = factor(as.integer(entrezIDs %in% TissueEntrezIDs))
    names(all_genes) = TissueEntrezIDs
    GOdata <- new(
      "topGOdata",
      ontology = go[i],
      allGenes = all_genes,
      geneSel = function(p) {
        p < 1
      },
      description = "GO analysis of ALL data: Differential Expression ",
      annot = annFUN.org,
      mapping = "org.Hs.eg.db",
      ID = "Entrez"
    )
    resultFisher <-
      runTest(GOdata, algorithm = "classic", statistic = "fisher")
    GenTable(GOdata, classicFisher = resultFisher, topNodes = 10)
    
    showSigOfNodes(GOdata,
                   score(resultFisher),
                   firstSigNodes = 5,
                   useInfo = 'all')
    par(mar = c(2.1, 2.1, 2.1, 2.1), cex = 0.8)
    printGraph(
      GOdata,
      resultFisher,
      firstSigNodes = 5,
      useInfo = "all",
      pdfSW = TRUE
    )
    titleStr = paste("GO", go[i], "DAG analysis")
    title(main = titleStr)
  }
  dev.off()
  
  #plot
  if (length(data) > 0) {
    pdf(paste(resPath, paste(t, "_GO.pdf"), sep = "/"),
        width = 7,
        height = 7)
    
    labs = term
    labs = labs[complete.cases(data)]
    cols = rep(c("steelblue", "mediumturquoise", "sandybrown"), each = 5)
    cols = cols[complete.cases(data)]
    data = data[complete.cases(data)]
    maxdata = max(data) + 50
    x1 = seq(1.5,29.5,by=2)
    x1 = x1[1:length(data)]
    par(mar = c(25, 20, 4.1, 2.1), cex = 0.8)
    barplot(
      data,
      col = cols,
      ylim = c(0, maxdata),
      width = 1,
      space = 1,
      ylab = "Gene Number",
      las = 1,
      main = "GO enrichment"
    )
    text(
      x = x1,
      y = -1,
      srt = 45,
      adj = 1,
      labels = labs,
      xpd = TRUE
    )
    legend(
      "topright",
      inset = .05,
      c(
        "Biological process",
        "Molecular function",
        "Cellular component"
      ),
      fill = c("steelblue", "mediumturquoise", "sandybrown"),
      horiz = FALSE
    )
    abline(h = 0)
    dev.off()
  }
}
