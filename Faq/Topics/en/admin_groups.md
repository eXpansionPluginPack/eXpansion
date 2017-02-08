Admin Groups
[Back to admin](admin.md)<br>

#Admin Groups
    Admin groups is a plugin that will intercept all admin commands in order
    to check if a user has required privileges to use that command. The plug-in
    will also allow you to manage all your admins and their permissions easily.
    You don't need to modify complicated config files or commands you
    just use it like you would use any other desktop application.

    To be able to use this you first need to be admin yourself, the easiest way to
    do this is to add yourself to the admin list of `manialive` itself.
    Just add this line in the config:

```
    manialive.admins[] = 'your login'
```

    Once an admin just start manialive and join your server. You may access the admin
    groups window with `right click` anywhere on the screen -> `Server Main` in the
    menu then click `Admin Groups` or use the following command: `/admin groups`


    At the Admin Groups page you can see a list of different Admin Groups.
    `Master Admin` group can't be deleted, it is the group that has all the permissions,
    and the group where all ManiaLive admins are automatically added.


## Adding Admins
    Just click on `Players` on the `Admin Groups` of the group into which you want to add
    a player. A new page with the player list will appear. Write the login of the user on the input
    box on top and click `Add`.

    You have added an Admin to that group. It should appear in the list below. If the player
    is connected to the server you might also just click on select and select him in the new
    window that opens.

## Adding Groups
    On `Admin Groups` just fill up the input box on top with the name of the group and then
    click on `Add`.

    You have created a new group.
    `Notice!` New groups has no permissions by default.

## Inheriting permissions
    Let's know say you want to create a new group `VIP Members` to whom you want to give
    the same permissions as a `Operator`. You may redefine the permissions manually of use
    the inheritance. On the `Admin Groups page` click on `Inheritance `of the `VIP Members`
    group you have created. It will open a new window. On this window just search for the
    `Operator` group and check the checkbox.

    You can now close the window it is done, the group will inherit the permissions
    of the other group.

## Changing permissions
    After a while you have observed that the `VIP Members` abuses their right to mute people;
    you would like to remove this permission from them. On the `Admin Groups page` click on
    permissions of `VIP Members`. Search for the player mute permission.

    First remove the check on the `inherit` checkbox then be sure the `permission` checkbox
    is unchecked. You are done. All other permissions will still be inherited by Operator group
    but not the player mute permission.

# Configuration options

    this plugin will generate automatically admins configuration file at
    `config\<serverlogin>_admins.ini`

    please don't edit it manually.

    You may change this path in your server configuration page and share multiple
    setting between servers
