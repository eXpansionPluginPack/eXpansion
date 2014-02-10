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
        // external loop stuff
        <?=  $this->wLoop ?>
				
	yield;	
    }
}
//--> </script>