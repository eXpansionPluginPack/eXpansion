foreach (Event in PendingEvents) {
    if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "CopyJoinLink") {
        declare CMlLabel label <=> (Event.Control as CMlLabel);
        label.Value = "$0D0Done";
        CopyServerLinkToClipBoard();
    }
}		  
