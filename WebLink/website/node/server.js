/* Config */

var fs = require('fs')
  , ini = require('ini')

var config = ini.parse(fs.readFileSync('../config.ini', 'utf-8'))
console.log(config);


/******************************************/
var express =  require('express');
var app = express()
, http = require('http')
, server = http.createServer(app)
, io = require('socket.io').listen(server)
, url = require('url');

server.listen(parseInt(config.port));

/** @var array */
var serverData = [];
var webUsers = 0;

app.get('/onDedicatedEvent', function (req, res) {
    var atob = require('atob');
    var query = url.parse(req.url, true).query;
    try {
	var outData = JSON.parse(atob(decodeURIComponent(query.data)));
	
	if (outData.secret == config.secret) {
	    io.sockets.emit("onDedicatedEvent", {
		event: outData.event,
		data: outData.data
	    });
	    res.send(200);
	}
	else {
	    res.send(404);
	}
    } catch (err) {
	res.send(404);
    }
});

app.get('/onDedicatedData', function (req, res) {
    var atob = require('atob');
    var query = url.parse(req.url, true).query;
    try {
	var outData = JSON.parse(atob(decodeURIComponent(query.data)));
	
	if (outData.secret == secret) {
	    if (outData.type == "serverData") {
		serverData = outData.data;
	    }
	    if (outData.type == "map") {
		serverData.map = outData.data;
	    }
	    if (outData.type == "players") {
		serverData.players = outData.data;
	    }
	    if (outData.type == "spectators") {
		serverData.spectators = outData.data;
	    }
	    if (outData.type == "rankings") {
		serverData.rankings = outData.data;
	    }
	    if (outData.type == "roundFinish") {
		serverData.roundFinish = outData.data;
	    }	 
	    if (outData.type == "server") {
		var serverdata = outData.data;
		serverdata.password ="* will not show *";
		serverdata.specpassword = "* will not show *";
		serverdata.refpassword = "* will not show *";
		serverData.server = outData.data;
	    }	 
	    
	    io.sockets.emit("onDedicatedData", {
		type: outData.type,
		data: outData.data
	    });
	    res.send(200);
	}
	else {
	    res.send(404);
	}
    } catch (err) {
	res.send(404);
    }
});



io.sockets.on('connection', function (socket) {
    webUsers++;
    io.sockets.emit('webConnect', {
	usersCount: webUsers
    });
    
    socket.on('getInfos', function (from, msg) {
	socket.emit("onDedicatedData", {
	    type: "serverData",
	    data: serverData
	});
	io.sockets.emit('webCount', {
	    usersCount: webUsers
	});
    });

    socket.on('disconnect', function () {
	webUsers--;
	io.sockets.emit('webDisconnect', {
	    usersCount: webUsers
	});
    });
});