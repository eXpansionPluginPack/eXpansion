for(i, <?= $this->min ?>, <?= $this->max ?>){
    if(Page.GetFirstChild("Desc_Icon_"^i) != Null){
        Page.GetFirstChild("Desc_Icon_"^i).Hide(); 
    }
}
