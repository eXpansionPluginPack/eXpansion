if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "<?= $this->name ?>l") { 
        Frame<?= $this->dropdownIndex ?>.Show();
} else {
       if (Event.Type == CMlEvent::Type::MouseClick) {
            Frame<?= $this->dropdownIndex ?>.Hide();
       }
}
<?php
$x = 0;
foreach ($this->values as $item) {
    ?>
    if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "<?= $this->name . $x ?>") {                           
                  Label<?= $this->dropdownIndex ?>.Value = "<?= $item ?>";
                  Output<?= $this->dropdownIndex ?>.Value = "<?= $x ?>";
                  Frame<?= $this->dropdownIndex ?>.Hide();
    } 
    <?php
    $x++;
}