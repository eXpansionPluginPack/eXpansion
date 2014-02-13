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
	yield;	
        
	// external loop stuff
        <?=  $this->wLoop ?>
    }
}
//--> </script>

