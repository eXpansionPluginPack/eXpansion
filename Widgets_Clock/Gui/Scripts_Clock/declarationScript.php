declare CMlLabel lbl_clock <=> (Page.GetFirstChild("clock") as CMlLabel);
declare playerLabel = (Page.GetFirstChild("nbPlayer") as CMlLabel);
declare specLabel = (Page.GetFirstChild("nbSpec") as CMlLabel);
declare serverLogin = "<?=$this->serverLogin?>";
declare eXp_lastClockUpdate = 0;