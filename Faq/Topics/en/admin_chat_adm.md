Chat commands
[Back to admin](admin_chat.md)<br>

##Admin commands: /adm `command` _parameter_

# Map related
    ###/adm `shuffle`
    makes map list to randomize

    ###/adm `skip`
	forces to go next map instantly

    ###/adm `prev`
	forces to go previous map instantly

    ###/adm `replay`
	forces to replay map after it ends

	###/adm `replay`
    forces map to restart instantly

    ###/adm `er`
    forces endround on rounds-based modes (has 3sec delay)

    ###/adm `add` {mxId}
	adds map from mx, {mxId} can be numeric value or "this"

	###/adm `trash` this
    remove current map from playlist and delete the map file

	###/adm `remove` this
    remove current map from playlist, keep file

# Records related
	###/adm `delrec` {login}
	deletes all records by login, has confirm dialog

	###/adm `saverec`
    saves instantly all records to database

# Player Related commands
    ###/adm `ignore` {login}
	ignores players chat, he is able to see chat, but can't write

	###/adm `unignore` {login}
	unignores player, so he can chat again

	###/adm `kick` {login}
	remove player from server

	###/adm `ban` {login}
	opens a ban window to ban a player from the server

	###/adm `unban` {login}
	unbans a player

	###/adm `getbanlist`
	shows banlist

	###/adm `cleanbanlist`
	clears banlist

	###/adm `black` {login}
	permanently ban player

	###/adm `unblack` {login}
	remove permanent ban

	###/adm `getblacklist`
	shows blacklist

	###/adm `cleanblacklist`
	cleans blacklist

#Server Related
    ###/adm `mode` {mode}
	{mode} can be one of: ta, rounds, team, cup, laps

	###/adm `name` "{new name}"
	gives server a new name

	###/adm `comment` "{new comment}"
	add new comment

	###/adm `pass` "{new pass}"
	set new password for server

	###/adm `spectpass` "{new pass}"
	set new spectator pass for server

	###/adm `maxplayers` {number}
	set maximum players count

	###/adm `maxspec` {number}
	set maximum spectator count

	###/adm `chattime` {mm:ss}
	set podium time

	###/adm `sethideserver` {boolean}
	set server hidden

	###/adm `setmapdownload` {boolean}
	set allow map download from menu

# Other
    ###/admin `update`
    autoupdate eXpansion

	###/adm `stop` exp
	stop expansion

	###/adm `stop` dedicated
    stop dedicated

    ###/adm `netstat`
    opens netstats window, displays ping and other infos

    ###/adm `cancel`
    cancels current vote

	###/adm `planets`
	shows planets amount at server login}

	###/adm `pay` {login} {amount}
	pays login amount

    ###/admin `server`
    open control panel window

    ###/admin `plugins`
    open pluginsmanager window

    ###/admin `settings`
    open eXpansion settings window

