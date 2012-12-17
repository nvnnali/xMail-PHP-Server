xMail - PHP Server
===================

This is the xMail PHP server implementation. The server contains all the required files to run your own server for xMail.

xMail, it's developers, and all it's associated members are not liable for any damage caused. Although the code is bug tested, some things can still go wrong. 

You are able to edit any file you choose, however that puts your stability at risk. Use with caution. xMail, and anyone involved, will not take the blame if something goes wrong after you've edited the server.

Setup steps are below the folder breakdown. If you have any issues, please contact me through the IRC (#turt2live on EsperNet)

Intermediate PHP/SQL/Website knowledge is required in order to make this work. If you want a private server and do not have the skills needed, contact me in the IRC (#turt2live on EsperNet) and leave a message. I may not be there right away, if you need to leave, PM me your email so I can contact you later.

Folder Breakdown
-----------------

#### /api

Holds various function files for the snail mail and regular mail server.

#### /config

Contains the configuration file(s) for xMail. EDIT THESE BEFORE RUNNING THE SERVER LIVE.

#### /cron

Contains cron jobs that should be run (controls snail mail and general cleanup). Instructions are in the README inside.

#### /inc

Holds various function files for the snail mail and regular server mail

#### /mail

The 'server mail' server files. index.php is the core, everything else is related to that file.

#### /setup

Files, such as the xMail.sql file, that are used in setup.

#### /snail

The 'snail mail' server files. index.php is the core, everything else is related to that file.

#### /tools

Contains various tools for playing with xMail. Mostly statistics.

How to setup a working xMail PHP Server
---------------------------------------

1. Extract all the files in the ZIP archive for the xMail PHP server to a local folder (like 'Desktop/xMail')
2. Open the config.php found in the /config folder
3. Edit the values as needed
4. Save the file
5. Upload the entire folder contents ('Desktop/xMail') to your website
6. (OPTIONAL) Setup subdomains to point to 'xmail.yourwebsite.com'
7. Create the xMail server database using the xMail.sql file provided in the setup folder
8. Create and schedule the cron jobs as defined in /cron/README.txt 
9. Update the xMail-Plugin configuration (see below)
10. Resolve any errors


How to configure xMail-Plugin to work with your new server
----------------------------------------------------------

1. Shutdown your CraftBukkit server
2. Open the xMail configuration
3. Under 'server', change 'host' to your web domain. The default is 'xmail.turt2live.com'.
4. If you renamed the 'mail' or 'snail' folders in the PHP server, make sure to update 'mail-url' and 'snail-mail-url' under 'server' to the respective names.
	This means that if you changed '/mail' to '/lolmail', 'mail-url' would then be 'lolmail'.
5. Save the xMail configuration
6. Start the server
7. Resolve any errors

This is all too complicated! What can I do?
-------------------------------------------

Contact me in my IRC channel (#turt2live on EsperNet). I may not respond right away, but leave your message anyways. If you decide you do not want to wait, PM me an email to reach you at.

Web Interface?
--------------

Soon. It will be basic.