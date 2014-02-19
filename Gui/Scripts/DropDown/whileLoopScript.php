foreach (Event in PendingEvents) {
    if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "<?php echo $this->name ?>l") { 
            Frame<?php echo $this->dropdownIndex ?>.Show();
    } else {
           if (Event.Type == CMlEvent::Type::MouseClick) {
                Frame<?php echo $this->dropdownIndex ?>.Hide();
           }
    }
    <?php
    $x = 0;
    foreach ($this->values as $item) {
        ?>
        if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "<?php echo $this->name . $x ?>") {                           
                      Label<?php echo $this->dropdownIndex ?>.Value = "<?php echo $item ?>";
                      Output<?php echo $this->dropdownIndex ?>.Value = "<?php echo $x ?>";
                      Frame<?php echo $this->dropdownIndex ?>.Hide();
        } 
        <?php
        $x++;
    } ?>
}