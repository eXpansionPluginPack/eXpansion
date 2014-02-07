<script><!--
#Include "TextLib" as TextLib
#Include "MathLib" as MathLib

//Function definitions
<?= $this->scriptLib ?>

main () {
     // external declares
     <?= $this->dDeclares ?>
			 
     // external declares ends
     while(True) {
	
	if (!PageIsVisible || InputPlayer == Null) {
	    yield;
	    continue;
	}
        // external loop stuff
        <?=  $this->wLoop ?>
				
	yield;	
    }
}
--></script>