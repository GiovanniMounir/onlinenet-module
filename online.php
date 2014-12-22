<?php
/*
Copyright (c) 2014 Verelox
Developed by Giovanni Mounir (@GiovanniMounir on GitHub), James Daniel(@daeneeil on GitHub) and Erikku Nakahara(yam0r1). Not affiliated with online.net.
This is an open source project available under the MIT license.

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.
*/
function bytesToSize($bytes, $precision = 2)
{  
//Converts bytes to a human readable size. Copied from stackoverflow

    $kilobyte = 1024;
    $megabyte = $kilobyte * 1024;
    $gigabyte = $megabyte * 1024;
    $terabyte = $gigabyte * 1024;
   
    if (($bytes >= 0) && ($bytes < $kilobyte)) {
        return $bytes . ' B';
 
    } elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
        return round($bytes / $kilobyte, $precision) . ' KB';
 
    } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
        return round($bytes / $megabyte, $precision) . ' MB';
 
    } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
        return round($bytes / $gigabyte, $precision) . ' GB';
 
    } elseif ($bytes >= $terabyte) {
        return round($bytes / $terabyte, $precision) . ' TB';
    } else {
        return $bytes . ' B';
    }
}

function online_TerminateAccount($params) {
//This is the code for the "terminate" button command
    $server = $params["server"]; //Check if the service is linked to a server
    $serverid = $params["serverusername"]; //Get the server ID from the username field
    $token = $params["serveraccesshash"]; //This is online.net's API private token for the user owning the dedicated server, retrieve it from server access hash field
	if ($server) //Proceed if linked to a server
	{
        call_online_api($token, 'POST', '/server/boot/rescue/'.$params["serverusername"],null,array('image'=>'ubuntu-12.04_amd64')); //Boots into rescue mode
	}
//Add an entry to the todo list field to notify the administrators (optional and can be removed)
$table = "tbltodolist";
$values = array("title"=>"ONLINE.NET - Service Termination","description"=>"Service ID # " . $params["serviceid"] ." requires termination.","status"=>"Pending");
$newid = insert_query($table,$values);
//End add an entry

return "success"; //Mission complete
}

function online_SuspendAccount($params) {
//This is the code for the "suspend" button command
    $server = $params["server"]; //Check if the service is linked to a server
    $serverid = $params["serverusername"]; //Get the server ID from the username field
    $token = $params["serveraccesshash"]; //This is online.net's API private token for the user owning the dedicated server, retrieve it from server access hash field
	if ($server) //Proceed if linked to a server
	{
        call_online_api($token, 'POST', '/server/boot/rescue/'.$params["serverusername"],null,array('image'=>'ubuntu-12.04_amd64')); //Boots into rescue mode
	}
	//Add an entry to the todo list field to notify the administrators (optional and can be removed)
$table = "tbltodolist";
$values = array("title"=>"ONLINE.NET - Service Suspension","description"=>"Service ID # " . $params["serviceid"] ." was suspended.","status"=>"Pending");
$newid = insert_query($table,$values);
//End add an entry

    return "success"; //Mission complete
}

function online_UnsuspendAccount($params) {
//This is the code for the "unsuspend" button command
    $server = $params["server"]; //Check if the service is linked to a server
    $serverid = $params["serverusername"]; //Get the server ID from the username field
    $token = $params["serveraccesshash"]; //This is online.net's API private token for the user owning the dedicated server, retrieve it from server access hash field
    if ($server) //Proceed if linked to a server
	{
	    call_online_api($token, 'POST', '/server/boot/normal/'.$params["serverusername"],null,array('server_id'=>$params["serverusername"])); //Boots into normal mode
	}
	return "success"; //Mission complete
}

function online_ClientArea($params) {
//This is the code for the client area. We will get the data from the API and pass to the client area.
$token = $params["serveraccesshash"]; //This is online.net's API private token for the user owning the dedicated server, retrieve it from server access hash field

// -- start state --
if ($_GET['b'] == "state" || empty($_GET['b'])) //Proceed if the selected menu item is State or there is no selected menu item
{
$serverinfo = json_decode(call_online_api($token, 'GET', '/server/'.$params["serverusername"])); //Retrieve the infortmation we have for this server ID from the API (Power State, OS, etc)
$rescueinfo = json_decode(call_online_api($token, 'GET', '/server/rescue_images/'.$params["serverusername"])); //Retrieve the rescue images available for this server ID from the API (ubuntu, Windows PE, etc)
$decodedinfo = (array) $serverinfo; //Convert the retrieved server data to an array: The API passes it as an stdObject
$osinfo = (array) $decodedinfo["os"]; //Convert the retrieved rescue images data to an array: The API passes it as an stdObject
$rescuecreds = (array) $decodedinfo["rescue_credentials"]; //Get the rescue credentials from the server info, if any

if (!empty($_POST['rescue_image'])) //Proceed if the user requested to start a new rescue session
{
    $rescuedetails = (array) json_decode(call_online_api($token, 'POST', '/server/boot/rescue/'.$params["serverusername"],null,array('image'=>$_POST['rescue_image']))); //Start a rescue session with the selected image
    $message = "<div class='alert-message success'><p>Your server has successfully booted into rescue mode.<a href='#' class='close'>&times;</a></p></div>"; //Show this message
}
else if (!empty($_POST['normal_mode'])) //Proceed if the user requested to boot into normal mode
{
    call_online_api($token, 'POST', '/server/boot/normal/'.$params["serverusername"],null,array('server_id'=>$params["serverusername"])); //Boots into normal mode
    $message = "<div class='alert-message success'><p>Your server has successfully booted into normal mode.<a href='#' class='close'>&times;</a></p></div>"; //Show this message
}
else if (!empty($_POST['hostname'])) //Proceed if the user requested to change their hostname
{
    if (call_online_api($token, 'PUT', '/server/'.$params["serverusername"],null,array('hostname'=>$_POST['hostname'])) == "true") //Proceed if the requested hostname is accepted
	{
        $message = "<div class='alert-message success'><p>Your hostname has been successfully updated.<a href='#' class='close'>&times;</a></p></div>"; //Show this message as a result of success
	}
	else
	{
	    $message = "<div class='alert-message error'><p>Hostname can only contain alphanumeric characters and hyphens but it cannot start or end with a hyphen.<a href='#' class='close'>&times;</a></p></div>"; //The hostname is not accepted, show this message
	}
}
else if (!empty($_POST['reboot'])) //Proceed if the user requested to reboot their server
{
    if (call_online_api($token, 'POST', '/server/reboot/'.$params["serverusername"])) //Send the reboot command then proceed if the command succeeded
	{
        $message = "<div class='alert-message success'><p>The server is being rebooted.<a href='#' class='close'>&times;</a></p></div>"; //Show this message as a result of success
	}
	else
	{
	    $message = "<div class='alert-message error'><p>Something went wrong. The operation was cancelled.<a href='#' class='close'>&times;</a></p></div>"; //Show this message, the command failed
	}
}

//Resynchronize - retrieve the same data again
$serverinfo = json_decode(call_online_api($token, 'GET', '/server/'.$params["serverusername"]));
$rescueinfo = json_decode(call_online_api($token, 'GET', '/server/rescue_images/'.$params["serverusername"]));
$decodedinfo = (array) $serverinfo;
$osinfo = (array) $decodedinfo["os"];
$rescuecreds = (array) $decodedinfo["rescue_credentials"];
//End resynchronize

if(file_exists("modules/servers/online/img/".$osinfo["name"].".png")) //Proceed if there's a PNG image for the operating system name
{
    $osimage = "<img style='vertical-align: middle; margin-left:5px;' src='/modules/servers/online/img/".$osinfo["name"] .".png'></img> "; //Show the image in the client area
}
$hostname = "<h3>Change your hostname</h3><hr style='width:50%;'><p>Please enter your new hostname: <form method='post' class='form-horizontal'><input type='text' value='".$decodedinfo['hostname']."' name='hostname'></input><br><br><input type='submit' value='Change' class='btn btn-primary'></input> <a class='btn' href='/clientarea.php?action=productdetails&id=".$params['serviceid']."&b=state'>Cancel</a></form></p><hr style='width:50%;'>"; //This is visible when a hostname change is requested - it's always passed to the client area, but visible only when requested

if ($decodedinfo["boot_mode"] == "normal") //Proceed only if the current state is normal boot
{
    $rescue = "<h3>Boot into rescue mode</h3><hr style='width:50%;'><p>Please select the rescue image you would like to boot into: <form method='post' class='form-horizontal'><select style='width:100%' name='rescue_image'>";
    foreach($rescueinfo as $value) //Retrieve the available rescue images and show them into a dropdown field
	{
	    //Replace the values with a human readable values
        $hvalue = str_replace("-", " ", $value);
        $hvalue = str_replace("_", " ", $hvalue);
        $hvalue = str_replace("winpe", "Windows PE", $hvalue);
        $hvalue = str_replace("ubuntu", "Ubuntu", $hvalue);
		//Append the human readable valuable to the dropdown field
        $rescue .= "<option value='".$value."'>".$hvalue."</option>";
    }
    $rescue .= "</select><br><br><input style='margin-left:5px;' type='submit' value='Boot' class='btn btn-primary'></input> <a class='btn' href='/clientarea.php?action=productdetails&id=".$params['serviceid']."&b=state'>Cancel</a></form></p><hr style='width:50%;'>"; //Give the user the option to submit the form
}
else //Proceed if the current state is not normal boot
{
    $rescue = "<h3>Boot into rescue mode</h3><hr style='width:50%;'><p>The system is now running under a rescue image.</p><br><form method='post'><input type='submit' name='normal_mode' class='btn btn-primary' value='Boot into normal mode'></input> <a class='btn' href='/clientarea.php?action=productdetails&id=".$params['serviceid']."&b=state'>Cancel</a></form><hr style='width:50%;'>"; //Notify the user that a rescue session is already running
}

$state = "<p><b>Operating System</b>: " . $osimage . ucfirst($osinfo["name"]) . " " . $osinfo["version"] . "</p>"; //Append the operating system name and version
$state .= "<p><b>Hostname</b>: " . $decodedinfo["hostname"]; //Append the hostname

if ($_GET['c'] != "hostname") //Proceed if the user did not request to change their hostname
{
    $state .= " <a class='btn btn-mini' href='/clientarea.php?action=productdetails&id=". $params['serviceid'] . "&b=state&c=hostname'>Change</a></p>"; //Show a change button next to the hostname
}
else //Proceed if the user requested to change their hostname
{
    $state .= "</p>"; //End the paragraph: don't show a button next to the hostname
}
$state .= "<p><b>Boot Mode</b>: " . ucfirst($decodedinfo["boot_mode"]); //Append the boot mode: whether it's normal or rescue

if ($decodedinfo["boot_mode"] == "normal" && $_GET['c'] != "rescue") //Proceed if the boot mode is normal and the user did not request to boot into rescue
{
    $state .= " <a class='btn btn-mini' href='/clientarea.php?action=productdetails&id=". $params['serviceid'] . "&b=state&c=rescue'>Rescue</a></p>"; //Show a rescue button next to the mode
    $state .= "<p><b>Last Reboot</b>: " . str_replace(".000Z", "", str_replace("T", " ", $decodedinfo["last_reboot"])); //Show when the server was last reooted
    $state .= "<br><br><p><form method='post'><input type='submit' name='reboot' class='btn btn-danger' value='Reboot'></input></form></p>"; //Give the user the option to reboot their server
}
else if ($decodedinfo["boot_mode"] == "rescue") //Proceed if the boot mode is rescue
{
    if ($rescuecreds["protocol"] == "vnc") //Proceed if the user has selected a Windows image for the rescue mode
    {
        $port = "<p><b>Port</b>: VNC</p>"; //Show that the port is not 22, but VNC (I don't have a clue which port that is, probably 5900/5901)
    }
    else
	{
        $port = "<p><b>Port</b>: 22</p><p><b>Username</b>: " . $rescuecreds["login"] . "</p>"; //Show port 22 if it's not VNC, and show the username (VNC doesn't need a username)
    }
    $state .="<br><br><h3>&mdash; Rescue SSH Details &mdash;</h3><br><p><b>IP Address</b>: " . $rescuecreds["ip"] . "</p>".$port."<p><b>Password</b>: ". $rescuecreds["password"] ."</p><br><b>Note</b>: Please use the command <code>sudo su</code> to gain access to the root account after logging into the system with the above details.<form method='post'><br><input type='submit' name='normal_mode' class='btn btn-primary' value='Boot into normal mode'></input></form>"; //Show the rescue credentials and give the option to boot into normal mode
}
}

// --- end state ---
 
// --- start remote ---
if ($_GET['b'] == "remote") //Proceed if the selected menu item is remote
{ 
$serverinfo = json_decode(call_online_api($token, 'GET', '/server/'.$params["serverusername"])); //Get the server info
$decodedinfo = (array) $serverinfo; //Convert to an array
$bmcinfo = (array) $decodedinfo["bmc"]; $session = $bmcinfo["session_key"]; //Retrieve the bmc session key, if there's any

if (!empty($_POST['remoteip'])) //Proceed if the user has requested to start a remote session
{
    if (empty($session)) //Proceed if there's no remote session running
    {
        $remotesession = json_decode(call_online_api($token, 'POST', '/server/bmc/session', null, array('server_id'=>$params['serverusername'], 'ip' => $_POST['remoteip']))); //Send the command to the api to start the session
        $message = "<div class='alert-message success'><p>The remote session is being opened.<a href='#' class='close'>&times;</a></p></div>"; //Show this message as a result of success
    }
    else
    {
        $message = "<div class='alert-message error'><p>There is a remote session already open.<a href='#' class='close'>&times;</a></p></div>"; //Show this message if there's a remote session already open
    }
}
 
if (!empty($session) && !empty($_POST['removers'])) //Proceed if the user has requested to delete the session and there's one already running
{
    call_online_api($token, 'DELETE', '/server/bmc/session/'.$session); //Delete the session
    $message = "<div class='alert-message success'><p>The remote session was destroyed.<a href='#' class='close'>&times;</a></p></div>"; //Show this message
}

//Resynchronize - retrieve the data again
$serverinfo = json_decode(call_online_api($token, 'GET', '/server/'.$params["serverusername"]));
$decodedinfo = (array) $serverinfo;
$bmcinfo = (array) $decodedinfo["bmc"]; $session = $bmcinfo["session_key"];
//End resynchronize

if (empty($session)) //Show these if there's no session running
{
    $remote = "<p>There are no remote sessions currently opened.</p><br>";
    $remote .= "<p>To start a remote session, please enter the IP address which will be granted the rights to access the remote console: </p><form method='post' class='form-horizontal'><input type='text' placeholder='IP Address' name='remoteip' value='".htmlentities(strip_tags($_SERVER['REMOTE_ADDR']))."'></input><br><br><input type='submit' class='btn btn-primary' value='Start a session'></input> <a class='btn' href='/clientarea.php?action=productdetails&id=".$params['serviceid']."&b=remote'>Cancel</a></form><br>";
    $remote .= "<p style='color:#6F6F6F'><i>If you aren't sure, leave the current value and connect directly.</i></p>";
}
else if (!empty($session)) //Otherwise, show the remote session credentials
{
    $remotecinfo = (array) json_decode(call_online_api($token, 'GET', '/server/bmc/session/'.$session)); //Retrieve the remote session credentials
    if (empty($remotecinfo['login'])) //Proceed if there's a session open, but there's no details available
    {
        $remote = "<p><i class='fa fa-refresh fa-spin'></i> Your credentials are being created. This can take a few seconds.</p><br>";
        $remote .= "<form method='post'><input type='submit' name='removers' value='Cancel' class='btn'></input></form>";
        $remote .= '<meta http-equiv="refresh" content="20">';
    }
    else //Proceed if there's a session open, and the remtoe session details are available
    {
        $remote = "<p>There's a remote session currently opened.</p>";
        $remote .= "<br><p><b>URL</b>: ".$remotecinfo['url']."</p><p><b>Username</b>: ".$remotecinfo['login']."</p><p><b>Password</b>: ".$remotecinfo['password']."</p><p><b>Expires</b>: ".str_replace(".000Z", "", str_replace("T", " ", $remotecinfo["expiration"])) ."</p>";
        $remote .= "<br><form method='post'><input type='submit' name='removers' value='Delete' class='btn btn-danger'></input></form>";
    }
}
}
// --- end remote --- 
 
// -- start network --
 
if ($_GET['b'] == "network"){ 

$serverinfo = (array) json_decode(call_online_api($token, 'GET', '/server/'.$params["serverusername"])); //Retrieve the server details
$net = (array) $serverinfo["ip"]; //Get the network details
 
if ($_POST['reverse'] && !empty($_SESSION['ipreverse'])) //Proceed if the user requested to edit a reverse, and there's an IP selected
{
    if (json_decode(call_online_api($token, 'POST', '/server/ip/edit', null, array(
	 "address" => $_SESSION['ipreverse'],
	 "reverse" => $_POST['reverse']))) === true) //Send the command to the api, proceed if succeeded
    {
        $message = "<div class='alert-message success'><p>The reverse was successfully updated.<a href='#' class='close'>&times;</a></p></div>"; //Show this message as a result of success
        unset($_SESSION['ipreverse']); //Remove the selected IP from the session (this is a server value, so that the user can not hijack it)
    }
    else
    {
        $message = "<div class='alert-message error'><p>The domain is either invalid or it doesn't point to this IP address.<a href='#' class='close'>&times;</a></p></div>"; //Show this if the command failed or the reverse is not acceptable
    }
}
else if ($_POST['vmactype'] && !empty($_SESSION['ipmac'])) //Proceed if requested to generate a virtual MAC
{
    $request =json_decode(call_online_api($token, 'POST', '/server/failover/generateMac', null, array(
	 "address" => $_SESSION['ipmac'],
	 "type" => $_POST['vmactype']))); //Forward the command to the API
    $message = "<div class='alert-message success'><p>The MAC address was successfully generated.<a href='#' class='close'>&times;</a></p></div>"; //Show this message as a result of success
    unset($_SESSION['ipmac']); //Remove the selected IP from the session
}
else if (!empty($_POST['ipfrom']) && !empty($_SESSION['ipmac'])) //Proceed if the user has requested to copy a MAC address from an existing IP failover to another IP failover
{
    $valid = false; //This is a variable which will be used later to validate if the user owns the IP they want to copy its MAC address
    for ($i = 0; $i < count($net); $i++) //Loop through the data
	{
        $ipnetwork = (array)$net[$i]; //Convert to array
        if ($ipnetwork['address'] == $_POST['ipfrom']) //Proceed if the user owns the IP they want to copy its MAC address
		{ 
            $valid = true; //Set valid to true
            break; //Stop looping, we found what we wanted
        }
    }
    if ($valid) //Proceed if valid (the user owns the IP they want to copy its MAC address)
    {
        $request = json_decode(call_online_api($token, 'POST', '/server/failover/duplicateMac', null, array(
	    "target" => $_SESSION['ipmac'],
	    "address" => $_POST['ipfrom']))); //Send the command to the api
        if ($request === true) //Proceed if the command succeeded
        {
            $message = "<div class='alert-message success'><p>The MAC address was successfully duplicated.<a href='#' class='close'>&times;</a></p></div>";
            unset($_SESSION['ipmac']);
        }
        else //Show the error if it did not
        {
            $error = (array) $request;
            $message = "<div class='alert-message error'><p>".$error['error'].".<a href='#' class='close'>&times;</a></p></div>";
        }
    }
    else //Show the following message if the user does not own this IP address
    {
     $message = "<div class='alert-message error'><p>The source IP address is not owned by this server.<a href='#' class='close'>&times;</a></p></div>";
    }
}
else if ($_POST['removevmac'] && !empty($_SESSION['ipmac'])) //Proceed if a request to remove the virtual mac was initiated
{
    if (json_decode(call_online_api($token, 'POST', '/server/failover/deleteMac', null, array(
    "address" => $_SESSION['ipmac']))) === true) //Proceed if the command to delete the virutal mac succeeded
    {
        $message = "<div class='alert-message success'><p>The MAC address was successfully removed.<a href='#' class='close'>&times;</a></p></div>"; //Show this message
        unset($_SESSION['ipmac']);
    }
}

//End of requests - retrieve the server data and get the network data
    $serverinfo = (array) json_decode(call_online_api($token, 'GET', '/server/'.$params["serverusername"]));
    $net = (array) $serverinfo["ip"];
//End of retrieve data

    $valid = false; //Set valid to false. This will be used to validate if the user owns the IP they want to modify their reverse
    $currentreverse = "";
    if ($_GET['c'] == "editreverse" && !empty($_GET['ip'])) //Proceed if the user clicked on the edit reverse button
    {
        for ($i = 0; $i < count($net); $i++) //Loop through the network data
		{
            $ipnetwork = (array)$net[$i]; //Convert to array
            if ($ipnetwork['address'] == $_GET['ip']) //Proceed if the user owns this IP address
            {			
                $currentreverse = $ipnetwork['reverse']; //Get the current reverse for this IP address
                $valid = true; //Set valid to true
                break; //Stop looping, we found what we wanted
            }
        }
        if ($valid) //Proceed if the user owns this IP address
		{
            $_SESSION['ipreverse'] = $_GET['ip']; //Save the ipreverse for the POST request - we don't want this to be hijacked, so we don't use the "hidden" fields (notice the quotes)
            $network .= "<h3>Edit reverses</h3><hr style='width:50%;'><p>Please enter the new reverse for <b>". htmlentities($_GET['ip']) ."</b>: <form method='post' class='form-horizontal'><input type='text' value='" . $currentreverse."' name='reverse'></input><br><br><input type='submit' value='Change' class='btn btn-primary'></input> <a class='btn' href='/clientarea.php?action=productdetails&id=".$params['serviceid']."&b=network'>Cancel</a></form></p><hr style='width:50%;'>";
		}
    }
	
$valid = false; //Set valid to false. We will use this later to verify if the user owns the IP address they want to modify its MAC address
$currentmac = ""; //This is a value which will be used to assign the current MAC address of the IP the user wants to modify if the user owns the IP address
if ($_GET['c'] == "editmac" && !empty($_GET['ip'])) //Proceed if a request to edit the MAC address was initiated
{
    for ($i = 0; $i < count($net); $i++) //Loop through the network data
	{
        $ipnetwork = (array)$net[$i]; //Convert to arary
        if ($ipnetwork['address'] == $_GET['ip']) //Proceed if the user owns this IP address
		{ 
            $currentmac = $ipnetwork['mac']; //Save the current mac value, we will use it later when outputting the form
            $valid = true; //Set valid to true
            break; //Stop looping, we found what we wanted
        }
    }
    if ($valid) //Proceed if the user owns this IP address
    {
        $_SESSION['ipmac'] = $_GET['ip']; //Save the IP address
        if (empty($currentmac)) //Proceed if the MAC address is empty (i.e no mac address)
		{
            $network .= "<h3>Edit virtual MAC</h3><hr style='width:50%;'><p>You are editing the virutal MAC for <b>". htmlentities(strip_tags($_SESSION['ipmac'])) ."</b>. There's no virtual MAC associated with this IP address yet. To generate a new virtual MAC, please select its type:</p> <form method='post' class='form-horizontal'> <select name='vmactype' style='width:100%'><option value='vmware'>VMWare</option><option value='xen'>XEN</option><option value='kvm'>KVM</option></select><br><br><input type='submit' value='Generate' class='btn btn-primary'></input> ";
            $list = ""; //This is the variable which we will use to insert the other extra failover IPs, if any (excluding this one). This will be used for MAC duplication purposes.
            for ($i = 0; $i < count($net); $i++) //Loop through the network data
			{
                $ipnetwork = (array)$net[$i]; //Convert to array
                if ($ipnetwork['type'] == "failover" && $ipnetwork['address'] != $_SESSION['ipmac']) //Proceed if the IP address is not the current and if the IP address is a failover
				{
                    $list .= "<option value='".$ipnetwork['address']."'>".$ipnetwork['address']."</option>"; //Add the IP address to the list variable
                }
            }
            if (!empty($list)) //Proceed if the list variable is not empty (if there's any extra IP failovers excluding this one)
            {
                $network .="</form><br><p>or duplicate an existing virtual MAC:</p><form method='post' class='form-horizontal'>Duplicate from: <select name='ipfrom'><br><br>".$list."</select><br><br><input type='submit' class='btn btn-primary' value='Duplicate'></input> "; //Give the user the option to duplicate the MAC address from these extra IP addresses
            }
            $network .= "<a class='btn' href='/clientarea.php?action=productdetails&id=".$params['serviceid']."&b=network'>Cancel</a></form><hr style='width:50%;'>"; //Close the form and give the option to cancel or hide the interface
        }
        else //Proceed if the current virtual MAC is not empty - give the user the option to remove the current instead
        {
            $network .= "<h3>Edit virtual MAC</h3><hr style='width:50%;'><p>You are editing the virutal MAC for <b>". htmlentities(strip_tags($_SESSION['ipmac'])) ."</b> which is ". htmlentities(strip_tags($currentmac)) . ":</p> <form method='post' class='form-horizontal'><br><input type='submit' value='Remove this virtual MAC' name='removevmac' class='btn btn-danger'></input> </form><hr style='width:50%;'>";
        }
    }
}
 
$network .= "
    <table class='table'>
        <thead>
            <tr>
                <th>IP Address</th>
                <th>Type</th>
                <th>Reverse</th>
                <th>MAC Address</th>
            </tr>
        </thead>
        <tbody>
          "; //Initialize a table - we will use it to put the IPs we have with their information
for ($i = 0; $i < count($net); $i++) //Loop through the network data
{
    $index = (array)$net[$i]; //Convert to array
    if (empty($index['reverse'])) //Proceed if the reverse of the IP is empty - there's no reverse: give the user the option to add a new reverse
    {
        $index['reverse'] = "<a href='/clientarea.php?action=productdetails&id=".$params['serviceid']."&b=network&c=editreverse&ip=".$index['address']."'>Add reverse</a>";
    }
    else //Otherwise, proceed if it's not empty - give the user the option to modify the current reverse 
    {
        $index['reverse'] .= " <a href='/clientarea.php?action=productdetails&id=".$params['serviceid']."&b=network&c=editreverse&ip=".$index['address']."'> Edit</a>";
 
    }
    if ($index['type'] == 'failover') //Proceed if the IP is a failover
    {
        if (empty($index['mac'])) //Proceed if there's no MAC - give the option to add a virutal MAC 
        {
            $index['mac'] = "<a href='/clientarea.php?action=productdetails&id=".$params['serviceid']."&b=network&c=editmac&ip=".$index['address']."'>Add virtual MAC</a>";
        }
        else //PRoceed if there's a MAC address assigned to this IP - give the option to edit the current MAC
	    {
            $index['mac'] .= " <a href='/clientarea.php?action=productdetails&id=".$params['serviceid']."&b=network&c=editmac&ip=".$index['address']."'>Edit</a>";
        }
    }
    $network .= "<tr><td>". $index['address']."</td><td>".strtoupper(str_replace("failover", "extra", $index['type']))."</td><td>".$index['reverse']."</td><td>".$index['mac']."</td></tr>"; //Output the information into a table row
}
		  
$network .= "
        </tbody>
    </table>
"; //Close the table
}
 // -- end network -- 
 
 // -- start raid --
if ($_GET['b'] == "raid") //Proceed if the selected menu item is RAID
{
    $raidsupport = false; //This will be the value we use to check wheter RAID is supported for this service or not 
    $serverinfo = json_decode(call_online_api($token, 'GET', '/server/'.$params["serverusername"])); //Get the server data from the api
    $decodedinfo = (array) $serverinfo;	//Convert the data to an array
    $currentarrayinfo = (array) $decodedinfo['drive_arrays']; //Read the drive_arrays value from the converted array
    $currentraidlevel = " - "; //This will be used to retrieve the current raid level later, or will be left as it is if no raid level is detected
    if (count($currentarrayinfo) > 0) //Proceed if the array is not empty
    {
        $currentarray = (array) $currentarrayinfo[0]; //Convert the first index to an array (all of the indexes have the same values; the first index is enough. We don't have to loop
        $currentraidlevel = $currentarray['raid_level']; //Set the current raid level to the retrieved information
    }
    $disks = (array) $decodedinfo['disks']; //Get the disks attached to this server
    $refs = (array) $disks[0]; //Convert the first index to array - this is the API url. We will use it to retrieve the actual disks data
    $hwdiskinfo = (array) json_decode(call_online_api($token, 'GET', str_replace("/api/v1", "", $refs['$ref']))); //Retrieve the actual disks data
    $raid_controller = (array)$hwdiskinfo['raid_controller']; //Get the raid controller data and convert to array
    $supportedraidinfo = (array) json_decode(call_online_api($token, 'GET', str_replace("/api/v1","",$raid_controller['$ref']))); //Forward the $ref item from the raid controller data (which is an API url) and retrieve its data

    $raidinfo = (array) $supportedraidinfo; //Conver to an array (I apologize for the long procedure)
    $raidlevelinfo = (array) $raidinfo['supported_raid_levels']; //Retrieve the supported raid level information
    if (count($raidlevelinfo) > 0) //Proceed if the information array is not empty
    {
        $raid .= "Supported RAID levels are: "; //Append the supported raid levels; we will retrieve them by looping through the information array
    }
    for ($i = 0; $i < count($raidlevelinfo); $i++) //Loop through the information array
    {
	    $raidsupport = true; //RAID is supported
        $raidlevel = (array) $raidlevelinfo[$i]; //Convert the index to array
		if ($i != count($raidlevelinfo) - 1) //Proceed if it's not the last item - this is done to add a neat comma after the outputted information, unless if it's the last item, then we don't add a comma, or it would look bad.
		{
            $raid .= "<b>" . $raidlevel['raid_level'] . "</b>, "; //Append the supported raid level in bold
	    }
		else
		{
		    $raid .= "<b>" . $raidlevel['raid_level'] . "</b>"; //Output the final item, no need to insert a comma here    
		}
	}
 
    $raid .= "
 <p>Please contact support to change your RAID level.</p><br>
    <table class='table'>
        <thead>
            <tr>
                <th>ID</th>
                <th>Connector</th>
                <th>Type</th>
                <th>Capacity</th>
				<th>RAID</th>
            </tr>
        </thead>
        <tbody>
          "; //Inform the user to contact support if they wanted to make RAID level changes and then initialize a table

    for ($i = 0; $i < count($disks); $i++) //Loop through the connected disks array
	{
        $refs = (array) $disks[$i]; //Convert the index to array
        $hwdiskinfo = (array) json_decode(call_online_api($token, 'GET', str_replace("/api/v1", "", $refs['$ref']))); //Retrieve information for this disk
        if ($hwdiskinfo['capacity'] == "0") //Proceed if the disk is not actually a disk
        {
            $hwdiskinfo['capacity'] = " - "; //Put this instead of 0; 0 will not look very good.
        }
        else
		{
            $hwdiskinfo['capacity'] = bytesToSize($hwdiskinfo['capacity'] * 1048576); //Convert the size to bytes, and then convert it to a human readable size
		}
		//Output the information to the user
        $raid .= "<tr><td>". $hwdiskinfo['id']."</td><td>".$hwdiskinfo['connector']."</td><td>".$hwdiskinfo['type']."</td><td>".$hwdiskinfo['capacity']."</td><td>".$currentraidlevel."</td></tr>";
    }
    $raid .= "
        </tbody>
    </table>
"; //End the table
if (!$raidsupport) //Proceed if there's no raid support
{
    $raid = "<br><p>RAID is not supported for this service.</p>"; //Inform the user that there's no RAID support for this service
}
}
 // -- end raid --
 
 return array(
        'vars' => array(
            'srvid' => $params['serviceid'],
			'state' => $state,
			'hostname' => $hostname,
			'rescue' => $rescue,
			'message' => $message,
			'remote' => $remote,
			'network' => $network,
			'raid' => $raid
        ),
    ); //Return the values to the client area.
}


//Optional buttons

function online_AdminLink($params) {
//This is a handy extra button for the administrator so that by one click they can visit the main control panel for the server
	$code = "<a target='_blank' href='https://console.online.net/en/server/state/".$params['serverusername']."' class='btn'>Console</a>";
	return $code;
}

function online_reboot($params) {
//This is an extra function that will be used to give both the user and the administrator an extra button to reboot the server. This is visible under "Management Actions" for the user and under Products/Services for the administrator
    if (call_online_api($token, 'POST', '/server/reboot/'.$params["serverusername"])) //Proceed if the command succeded
	{
        $message = "success"; //WHMCS uses the value 'success' to indicate that the command succeeded. We are going to forward this to WHMCS so that the administrator gets a success message.
	}
	else //Something went wrong. Notify the administrator
	{
	$message = "Something went wrong. The operation was cancelled.";
	}
	return $message;
}


function online_ClientAreaCustomButtonArray()
{
    $buttonarray = array(
	 "Reboot" => "reboot"
	); //This is used to indicate that the client area should have a button with the name "Reboot" under Management Actions
	return $buttonarray;
}

function online_AdminCustomButtonArray()
{
    $buttonarray = array(
	 "Reboot" => "reboot"
	); //This is for the administrator; so that the administrator would have a button available to reboot the server
	return $buttonarray;
}

function call_online_api($token, $http_method, $endpoint, $get = array(), $post = array())
{
//This is the main thing. We use this function to forward the commands to the online.net API. This was copied from online.net 

    if (!empty($get)) {
        $endpoint .= '?' . http_build_query($get);
    }
 
    $call = curl_init();
    curl_setopt($call, CURLOPT_URL, 'https://api.online.net/api/v1' . $endpoint);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
	curl_setopt($call, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token, 'X-Pretty-JSON: 1'));
    curl_setopt($call, CURLOPT_RETURNTRANSFER, true);
	
 
    if ($http_method == 'POST') {
        curl_setopt($call, CURLOPT_POST, true);
        curl_setopt($call, CURLOPT_POSTFIELDS, http_build_query($post));
    }
	else
	{
        curl_setopt($call, CURLOPT_POST, true);
        curl_setopt($call, CURLOPT_CUSTOMREQUEST, $http_method); //This is not part of the copied script from online.net. This was added for custom requests required by online.net such as PUT and DELETE
        curl_setopt($call, CURLOPT_POSTFIELDS, http_build_query($post));
	}
    return curl_exec($call);
}
