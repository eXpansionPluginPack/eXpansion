how to install:

put this folder to your webserver, apt-get install node.js 
or get the latest from http://nodejs.org/

edit config.ini to point your host where you run the node.js server
set secret to something you only know;

on shell start the server 
./node server.js

for manialive plugin:
manialive.plugins[] = "eXpansion\WebLink"
ManiaLivePlugins\eXpansion\WebLink\Config.url = "http://yourserver:8888"
ManiaLivePlugins\eXpansion\WebLink\Config.secret = "secret" 

