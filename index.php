<?php

class client
{
    public $client_os = 'unknown';
    public $client_version = 'unknown';
    public $beta = false;
    private $failed = false;
    private $errmsg = "";

    public static function Latest()
    {
        return "3.1.0";
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
        if ($this->client_os == "huggle-devs")
            return false;
        return version_compare($this->client_version, $this->getNewVersion(), '<');
    }

    function getNewVersion()
    {
        // here can you change the logic to return the latest version based on e.g. os
        if ($this->beta) {
            return self::LatestBeta();
        } else {
            return self::Latest();
        }
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
        $this->client_version = preg_replace('/[^a-zA-Z0-9-_\.]/', '', $_GET['version']);
        $this->client_os = preg_replace('/[^a-zA-Z0-9-_\.]/', '', $_GET['os']);

        if (isset($_GET['notifybeta'])) {
            $this->beta = true;
        }
    }
}

$c = new client();

header("Content-type: text/xml");
echo "<?xml version=\"1.0\"?>\n";
echo "<update>\n";
if (!$c->Failed()) {
    if ($c->IsObsolete()) {
        /*
         * chain for checking for xml-File, first fit
         * 1) beta
         * 2) os + client_version match
         * 3) client_version match
         * 4) os match
         * 5) remaining: unknown.xml
         */
        echo "<obsolete>" . $c->getNewVersion() . "</obsolete>\n"; // <obsolete> should contain new version number
        if ($c->beta) {
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

