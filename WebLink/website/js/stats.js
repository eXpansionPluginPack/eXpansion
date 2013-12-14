var parse = MPStyle.Parser;
var serverData = [];

var bestTime = -1;

if (window.io == undefined) {
    $('#rankingList').append("<div class='alert'><h1>Backend is offline!</h1></div>");
}
else {
    var socket = io.connect(serverAddress);
		
    socket.on('connect', function () {
	socket.emit("getInfos");
    });
    socket.on('onDedicatedData', function (obj) {
	console.log(obj);
		  
	if (obj.type == "serverData") {
	    serverData = obj.data;
	    syncServerDatas();
	}
	
	if (obj.type == "map") {
	    serverData.map = obj.data;
	}
	
	if (obj.type == "rankings") {
	    serverData.rankings = obj.data;
	}
	
	if (obj.type == "gameinfos") {
	    serverData.gameinfos = obj.data; 
	}
	
	if (obj.type == "roundFinish") {
	    serverData.roundFinish = obj.data;
	    
	}
	if (obj.type == "server") {
	    serverData.server = obj.data;
	}
	if (obj.type == "players") {
	    serverData.players = obj.data;
	}
	
	if (obj.type == "spectators") {
	    serverData.spectators = obj.data;
	}

    });	
    socket.on('onDedicatedEvent', function (msg) {
	console.log(msg);
	switch (msg.event) {
	    case "onPlayerChat":
		doPlayerChat(msg.data);
		break;
	    case "onPlayerDisconnect":
		var login = msg.data.login;
		sendChat("Player "+ login +'$z$s$000 Disconnected!');
		syncPlayers();
		break;
	    case "onPlayerConnect":
		syncPlayers();
		var login = msg.data.login;
		sendChat("Player "+ serverData.players[login].nickName +'$z$s$000 Connected!');
		break;
	    case "onBeginMap":
		syncServerDatas();
		sendChat("Notice: a map begins");
		break;
	    case "onBeginRound":
		syncServerDatas();
		sendChat("Notice: a round begins");
		break;
	    case "onEndMap":
		syncServerDatas();
		sendChat("Notice: a map ends");
		break;
	    case "onEndRound":
		syncServerDatas();
		sendChat("Notice: a round ends");
		break;	
	    case "onPlayerFinish":
		syncRanks();
		break;	
	}
    });
		
    socket.on('webDisconnect', function (msg) {
	$("#webCount").html(msg.usersCount);
    });
 
    socket.on('webConnect', function (msg) {
	$("#webCount").html(msg.usersCount);
    });
 
    socket.on('webCount', function (msg) {
	$("#webCount").html(msg.usersCount);
    });
		

    socket.on('disconnect', function () {
	$('#rankingList').html("<div class='dmessage'><h1 class='alert'>Disconnected from backend</h1></div>");
    });
}

function syncServerDatas() {
    syncMap();
    syncPlayers();
    syncRanks();
    $("#servername").html(parse.toHTML(serverData.server.name));
}
	    
function syncMap() {
    $("#map").html(parse.toHTML(serverData.map.name + "$000 - " + serverData.map.author));
}
	  
function syncRanks() 
{
    var text = "<ul>";
    var i = 1;
    for (index in serverData.rankings) {
	var player = serverData.rankings[index];
	if (player.rank > 0 ) {
	    console.log("success");
	    var time = player.bestTime;
	    var info = 0;
	    switch (serverData.gameinfos.gameMode) {
		case 2: // ta
		    if (player.score !== null) time = player.score;
		    info = TMtoMS(time);
		    break;
                case 3:
		case 5: // cup
		    info = player.score;
		    break;
	    }
	   
	    text += "<li class='rankingItem'><table>\n\
<tr>\n\
<td class='rankingIndex'>"+i+"</td><td nowrap class='rankingPlayer'>"+parse.toHTML(player.nickName)+"</td><td><div class='rankingScore'>"+info+"</div></td></tr></table></li>";
	}
	if (player.rank == 0 ) {
	    text += "<li class='rankingItem'><table>\n\
<tr>\n\
<td class='rankingIndex'>"+i+"</td><td nowrap class='rankingPlayer'>"+parse.toHTML(player.nickName)+"</td><td><div class='rankingScore'>0</div></td></tr></table></li>";
	}
	i++;
    }	      
    text += "</ol>";
    $("#rankingList").html(text);
}

function doPlayerChat(chat) {
    var outText = parse.toHTML("$s" +chat.nickName + "$z$s$ff0 " + chat.text);
    var message = outText + "<br/>";
    $('#chat').append(message);
    $('#chat').scrollTop($('#chat')[0].scrollHeight);
}

function sendChat(text) {
    var outText = parse.toHTML("$z$s$000" + text);
    var message = outText + "<br/>";
    $('#chat').append(message);
    $('#chat').scrollTop($('#chat')[0].scrollHeight);
}	    
	    
function syncPlayers() {
    var text = "<ol type='1'>";
    for (login in serverData.players) {
	player = serverData.players[login];
	text += "<li><div class='player'>"+parse.toHTML(player.nickName)+"</div></li>";
    }
    for (login in serverData.spectators) {
	player = serverData.spectators[login];
	text += "<li>(s)"+parse.toHTML(player.nickName)+"</li>";	 
    }
    text += "</ol>";
    $("#playerList").html(text);
}

function z(number, znum) {    
    if (number < 10) {
	return "0"+number;
    }
    return ""+ number;
}
	
function TMtoMS(time) {
    var cent = z(time % 1000);
    time = Math.floor(time / 1000);
    var sec = z(time % 60);
    var min = z(Math.floor(time / 60));
    return min + ':' +sec+'.'+cent;
}

function urldecode(url) {
    return decodeURIComponent(url);
}