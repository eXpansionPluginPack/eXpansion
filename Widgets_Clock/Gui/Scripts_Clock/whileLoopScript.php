
if( (Now - eXp_lastClockUpdate) >= 1000 ){
    lbl_clock.SetText(""^TextLib::SubString(CurrentLocalDateText, 11, 2)^":"^TextLib::SubString(CurrentLocalDateText, 14, 2)^":"^TextLib::SubString(CurrentLocalDateText, 17, 2));

    declare nbSpec = 0;
    declare nbPlayer = 0;
    foreach (Player in Players) {
        if(Player.Login != serverLogin){
            if(!Player.RequestsSpectate){
                //log(Player.Login^"Is Player");
                nbPlayer += 1;
            }else{
                //log(Player.Login^"Is Spec");
                nbSpec += 1;
            }
        }
    } 
    serverName.SetText(""^CurrentServerName);    
    playerLabel.SetText(""^nbPlayer);
    specLabel.SetText(""^nbSpec);
    eXp_lastClockUpdate = Now;
}