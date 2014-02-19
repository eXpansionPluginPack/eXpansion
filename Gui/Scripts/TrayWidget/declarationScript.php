//Test
declare mainWindow <=> Page.GetFirstChild("Frame");
declare isMinimized = <?php echo $this->isMinimized ?>;                                          
declare isAnimation = False;                                          

declare lastAction = Now;
declare autoCloseTimeout = <?php echo $this->autoCloseTimeout ?>;
declare positionMin = <?php echo $this->posXMin ?>.0;
declare positionMax = <?php echo $this->posXMax ?>.0;
mainWindow.PosnX = <?php echo $this->posX ?>.0;
