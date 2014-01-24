declare CMlLabel mapName <=> (Page.GetFirstChild("mapName") as CMlLabel);
declare CMlLabel authorName <=> (Page.GetFirstChild("authorName") as CMlLabel);
declare CMlLabel authorTime <=> (Page.GetFirstChild("authorTime") as CMlLabel);
declare CMlQuad  authorZone <=> (Page.GetFirstChild("authorZone") as CMlQuad);

mapName.SetText(Map.MapName);
authorName.SetText(Map.AuthorNickName);
    if (Map.AuthorZoneIconUrl != "") {
        authorZone.ChangeImageUrl(Map.AuthorZoneIconUrl);
    }
authorTime.SetText(Map.ObjectiveTextAuthor);

log("icon: " ^ Map.AuthorZoneIconUrl);
log("Mapzone: " ^Map.AuthorZonePath);