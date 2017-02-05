# Admin guide

Here is few chat commands you might find useful.
Good to know is, about 90% of XAseco and FAST commands are supported. There is few new commands also.
Default chat commands can be found at [[chat commands list|admin-chatcommands]].

To restart or stop running eXpansion:<br>
`/adm manialive restart` <br>
`/adm manialive stop`<br>
or even faster:<br>
`/adm exp res`<br>
`/adm exp stop`<br>

to stop running dedicated & exp:<br>
`/adm stop dedicated`<br>
or<br>
`/adm stop dedi`<br>

Fastly open config windows:

`/adm server` opens server management window<br>
`/adm settings` opens eXpansion config settings <br>
`/adm plugins` opens plugin manager<br>
`/adm groups` opens admin groups<br>

## First steps after installation
Here is a few useful tips to make server administration easier for you.

As currently there is no way to fetch infos on dedicated server files using dedicated API, the files needs to be configured manually in order to auto-save the changed values for next server restart.
type `/adm settings` -->  click tab `Config files`

fill in your `match settings` file and `dedicated_cfg.txt` file and the `server settings` file.

> the `Server setting`-files are very powerful, as all expansion settings are saved & loaded from them, this means
> also active plugins. There is already defined some for, like match or minimal configs for plugins.

## Enabling Dedimania 
1. type `/adm plugins` or use menu to access plugins
2. Scroll down or search for dedimania
3. Click the wrench icon next to the plugin and fill in your settings
4. Start the plugin
5. Scroll down to widgets, 
6. Enable the dedimania widget

> If you wish to have more traditional widget look, configure the widget and select "use legacy layout"!
