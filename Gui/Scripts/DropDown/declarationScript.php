declare CMlFrame Frame<?= $this->dropdownIndex ?> <=> (Page.GetFirstChild("<?= $win->name ?>f") as CMlFrame);
declare CMlLabel Label<?= $this->dropdownIndex ?> <=> (Page.GetFirstChild("<?= $this->name ?>l") as CMlLabel);
declare CMlEntry Output<?= $this->dropdownIndex ?> <=> (Page.GetFirstChild("<?= $this->name ?>e") as CMlEntry);
Frame<?= $dropdownIndex ?>.Hide();