<?php
$disableAutoClose = 'False';
if (isset($this->disableAutoClose))
    $disableAutoClose = 'True';
?>

declare mainWindow <=> Page.GetFirstChild("Frame");
declare isMinimized = <?php echo $this->isMinimized ?>;                                          
declare isAnimation = False;                                          

declare lastAction = Now;
declare autoCloseTimeout = <?php echo $this->autoCloseTimeout ?>;
declare disableAutoClose = <?php echo $disableAutoClose ?>;
declare positionMin = <?php echo $this->posXMin ?>.0;
declare positionMax = <?php echo $this->posXMax ?>.0;
mainWindow.RelativePosition.X = <?php echo $this->posX ?>.0;

if(autoCloseTimeout <= 0){
    disableAutoClose = True;
}
