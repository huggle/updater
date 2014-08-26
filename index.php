<?php

// This is a core file for huggle updater

//This program is free software: you can redistribute it and/or modify
//it under the terms of the GNU General Public License as published by
//the Free Software Foundation, either version 3 of the License, or
//(at your option) any later version.

//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.

require("client.php");

$c = new client();

// we log these for statistics so that we have some unreliable information regarding
// huggle usage :)
$ip = "";
if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
else
    $ip = $_SERVER['REMOTE_ADDR'];
$log = date('m/d/Y h:i:s a', time()) . ": update check, IP hash: " . md5($ip) . " version: " . $c->client_version . " os: " . $c->client_os . "\n";
file_put_contents("updates.txt", $log, FILE_APPEND);

header("Content-type: text/xml");
echo "<?xml version=\"1.0\"?>\n";
echo "<update>\n";
if (!$c->Failed()) {
    if ($c->IsObsolete()) {
        /*
         * chain for checking for xml-File, first fit
         * 1) beta (if beta version different from main -> there is a beta)
         * 2) os + client_version match
         * 3) client_version match
         * 4) os match
         * 5) remaining: unknown.xml
         */
        echo "<obsolete>" . $c->getNewVersion() . "</obsolete>\n"; // <obsolete> should contain new version number
        if ($c->beta && Client::LatestBeta() !== Client::Latest()) {
            include ("includes/beta.xml");
        } else {
            // let's check a definition for this system
            $file = "includes/" . $c->client_os . "_" . $c->client_version . ".xml";
            if (file_exists($file)) {
                // os + old client_version match
                include ($file);
            } else  if (file_exists("includes/" . "none_" . $c->client_version . ".xml")){
                // old client_version match
                include ("includes/" . "none_" . $c->client_version . ".xml");

            } else if(file_exists("includes/" . $c->client_os . ".xml")){
                // os match
                include ("includes/" . $c->client_os . ".xml");

            } else if (file_exists("includes/unknown.xml")) {
            	// no known match
                include ("includes/unknown.xml");

            } else {
                echo "<error>No data for your version</error>\n";
            }
        }
    } else {
        echo "<nonewversion />\n";
    }
} else {
    echo $c->getErrorMsg();
}
echo "</update>";

