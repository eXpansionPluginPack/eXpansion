//Test
declare mainWindow <=> Page.GetFirstChild("Frame");
declare isMinimized = <?= $this->isMinimized ?>;                                          
declare lastAction = Now;
declare autoCloseTimeout = <?= $this->autoCloseTimeout ?>;
declare positionMin = <?= $this->posXMin ?>.0;
declare positionMax = <?= $this->posXMax ?>.0;
mainWindow.PosnX = <?= $this->posX ?>.0;
