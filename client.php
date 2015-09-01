<?php
//This program is free software: you can redistribute it and/or modify
//it under the terms of the GNU General Public License as published by
//the Free Software Foundation, either version 3 of the License, or
//(at your option) any later version.

//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.

class client
{
    public $client_os = 'unknown';
    public $client_version = 'unknown';
    public $client_lang = 'unknown';
    public $client_test = false;
    public $beta = false;
    private $failed = false;
    private $errmsg = "";

    public static function Latest()
    {
        return "3.1.15";
    }

    public static function LatestMac()
    {
    	// return self::Latest();
    	return "3.1.15";
    }

    public static function LatestBeta()
    {
    	return self::Latest(); // no beta atm
    }

    function Failed()
    {
        return $this->failed;
    }

    private function setError($e)
    {
        $this->errmsg = "<error>ERROR: $e</error>\n";
        $this->failed = true;
    }

    function getErrorMsg()
    {
        return $this->errmsg;
    }

    function IsObsolete()
    {
        if ($this->client_test)
            return true;
        if ($this->client_os == "huggle-devs")
            return false;
        return version_compare($this->client_version, $this->getNewVersion(), '<');
    }

    function getNewVersion()
    {
        // here can you change the logic to return the latest version based on e.g. os
        if ($this->beta)
            return self::LatestBeta();
        else if ($this->client_os == 'mac')
            return self::LatestMac();
        return self::Latest();
    }

    function __construct()
    {
        if (!isset($_GET['os'])) {
            $this->setError("System must be defined");
            return;
        }
        if (!isset($_GET['version'])) {
            $this->setError("Version must be defined");
            return;
        }
        if (isset($_GET['test']))
            $this->client_test = true;
        if (isset($_GET['language']))
            $this->client_lang = $_GET['language'];
        $this->client_version = preg_replace('/[^a-zA-Z0-9-_\.]/', '', $_GET['version']);
        $this->client_os = preg_replace('/[^a-zA-Z0-9-_\.]/', '', $_GET['os']);

        if (isset($_GET['notifybeta'])) {
            $this->beta = true;
        }
    }
}

