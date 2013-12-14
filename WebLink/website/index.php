<?php
$config = (object)parse_ini_file("config.ini");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">

	<title>Trackmania2 LiveStats</title>
	<link href="dist/css/bootstrap.css" rel="stylesheet">
	<style>
	    #chat {
		background-color: #ddd;
		border: 1px solid #bbb;
		border-radius: 4px;
		padding: 0.5em;
		overflow-y: scroll;
		height: 7em;

	    }
	    .alert {
		background: red;
		color: yellow;
		font-weight: bold;

	    }
	    nav li h1 {
		margin: 0;
		padding:0;
		padding-top: 0.75em;
		padding-right: 1em;
		font-size: 14pt;
	    }
	    .navbar-fixed-bottom {
		margin: 0 auto;
	    }

	    @media (min-width: 992px) {
		.navbar-fixed-bottom {
		    margin: 0 auto;
		    max-width: 970px;
		}
	    }
	    @media (min-width: 1200px) {
		.navbar-fixed-bottom {
		    margin: 0 auto;
		    max-width: 1170px;
		}
	    }

	    .main {


	    }

	    .sidebar {

	    }

	    #playerList {
		height: 200px;
		background-color: #ddd;
		border: 1px solid #bbb;
		border-radius: 4px;
		padding: 0.5em;
	    }

	    #playerList ol {
		list-style-type: numeric;
		color: black;

	    }

	    #playerList li {

	    }

	    .player {	
		position: relative;
		display: inline-block;
		background: #ccc;
		padding-left: 0.5em;
		border: 1px solid #bbb;
		width: 100%;
		height: 1.5em;
		top: 0.25em;
		overflow: hidden;
	    }

	    #rankingsList li {
		padding: 0;
		margin: 0;
	    }
	    #rankingList ul {
		list-style: none;
		padding: 0;
		margin: 0;
	    }

	    .rankingItem table {
		height: 45px;
		background: #ddd;
	    }

	    .rankingIndex {
		background: black;
		color: white;
		font-size: 14pt;
		text-align: center;
		width: 45px;
	    }

	    .rankingPlayer {
		font-size: 14pt;
		padding-left: 1em;
		width: 200px;
	    }
	    .rankingScore {
		width: 100px;

		text-align: center;
		font-size: 14pt;
	    }



	</style>
    </head>
    <body>
	<div class="container">


	    <nav class="navbar navbar-default" role="navigation">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
		    <div id="servername" class="navbar-brand"></div>
		</div>

		<ul class="nav navbar-nav navbar-right">
		    <li><h1 id="map"></h1></li>
		</ul>
	    </nav>
	    <div class="col-sm-9 main">
		<div class="row">
		    <div id="rankingList"></div>
		</div>
	    </div>
	    <div class="col-sm-3 sidebar">
		<h3>WebSpectators = <span id="webCount"></span></h3>

		<h3>Players</h3>
		<div id="playerList"></div>
	    </div>


	</div>
	<div class="navbar navbar-default navbar-fixed-bottom">
	    <div id="chat">

	    </div>
	</div>
	<script>
	    var serverAddress = "<?php echo $config->host.":".$config->port; ?>";
	</script>
	<script src="http://<?php echo $config->host.":".$config->port; ?>/socket.io/socket.io.js"></script>
	<script src="js/jquery.js"></script>
	<script src="dist/js/bootstrap.min.js"></script>
	<script src="js/mp-style-parser.js"></script>
	<script src="js/stats.js"></script>
    </body>
</html>