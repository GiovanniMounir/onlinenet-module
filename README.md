License
================

This is a WHMCS module for ONLINE.NET resellers that gives the client the ability to control their dedicated servers and automates the termination and suspension of the dedicated servers assigned to the client.

This work is available under the MIT license; developed for Verelox by Giovanni Mounir and James Daniel. We are not affiliated, associated, authorized, endorsed by, or in any way officially connected with ONLINE.NET.

Features
================

1. Management of the server state: displays the operating system, boot mode and last reboot time. Allows the user to change the boot mode to rescue mode and use a particular rescue image.
2. Management of the server network: displays the assigned IPs to the network including the PUBLIC, PRIVATE and FAILOVER. An option is also granted for the client to modify the IPs reverse or modify the virtual MAC by generating a new one or copying an already existing one from a particular failover IP
3. Control over remote console: allows the client to start a new remote (BMC) session and gives the client the option to grant the session to a specific IP address
4. Displays RAID configuration: displays the RAID configuration of the dedicated server disks to the client and sorts the supported RAID levels

Installation
================

To install the module, click [here](https://github.com/Verelox/onlinenet-module/archive/master.zip) to download the latest release in a zip format, unzip it and upload the compressed files to your server at <code>WHMCS_MAIN_DIRECTORY/modules/servers/online/</code> where <code>WHMCS_MAIN_DIRECTORY</code> is your WHMCS directory and <code>/modules/servers/online/</code> is the folder you should create. The folder is case sensitive.

Configuration
================

After uploading the files required for the module to operate, open your administration panel and go to <b>Setup > Products/Services > Servers</b> then add a new server. Under the username field, while adding the server, insert the server's ONLINE.NET ID (example: 68828) and your private access token under the server access hash field. You can get your private access token from https://console.online.net/en/api/access.

Support
================

If you notice bugs or something wrong, you may feel free to report the issue by clicking [here](https://github.com/Verelox/onlinenet-module/issues/new). You may follow the same link if you would like to suggest a feature, too!
