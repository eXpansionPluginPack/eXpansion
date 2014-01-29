declare CMlLabel Label <=> (Page.GetFirstChild("Label") as CMlLabel);                
declare CMlLabel Cp <=> (Page.GetFirstChild("Cp") as CMlLabel);
declare Integer[] Checkpoints = <?= $this->checkpoints ?>;
declare Integer curCp = 0;
declare Integer totalCp = <?= $this->totalCp ?>;
declare Boolean lapRace = <?= $this->lapRace ?>;