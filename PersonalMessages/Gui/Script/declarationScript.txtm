<?php
$start = 'False';
if (isset($this->startEdition))
    $start = 'True';
?>


declare Text sendAction = "<?php echo $this->sendAction ?>";
declare Boolean startEdition = <?php echo $start ?>;
declare CMlEntry inputBox <=> (Page.GetFirstChild("messagebox") as CMlEntry);
declare Boolean pmStatus for UI = True;

if (!pmStatus) {
    mainWindow.RelativePosition.X = -3.0;
}

isMinimized = pmStatus;

if (startEdition) {    
    inputBox.StartEdition();
}
