<?php

class client
{
    public $client_os = 'unknown';
    public $client_version = 'unknown';
    public $beta = false;
    private $failed = false;

    static function ShowEr($e)
    {
        echo "<error>ERROR: $e</error>\n";
        $failed = true;
    }

    public static function Latest()
    {
        return "3.0.1";
    }
    
    public static function LatestBeta()
    {
    	return "3.0.2";
    }
    

    function Failed()
    {
        return $this->failed;
    }
    
    function IsObsolete()
    {
        return version_compare ( $this->client_version , $this->getNewVersion() , '<' );
    }
    
    function getNewVersion()
    {
    	/* here can you change the logic to return the latest version based on e.g. os */
    	if($this->beta){
    		return self::LatestBeta();
    	} else {
    		return self::Latest();
    	}
    }

    function __construct()
    {
        if (!isset($_GET['os']))
        {
            client::ShowEr("System must be defined");
            $this->failed = true;
            return;
        }
        if (!isset($_GET['version']))
        {
            client::ShowEr("Version must be defined");
            $this->failed = true;
            return;
        }
        $this->client_version = preg_replace('/[^a-zA-Z0-9-_\.]/','', $_GET['version']);
        $this->client_os = preg_replace('/[^a-zA-Z0-9-_\.]/','', $_GET['os']);
        
        if(isset($_GET['beta']))
        {
        	$this->beta = true;
        }
    }
}

$c = new client;

header("Content-type: text/xml");
echo "<?xml version=\"1.0\"?>\n";
echo "<update>\n";
if (!$c->Failed())
{
    if ($c->IsObsolete())
    {
        echo "<obsolete>" . $c->getNewVersion() . "</obsolete>\n"; // TODO: migrate away from that
        echo "<newversion>" . $c->getNewVersion() . "</newversion>\n";
        if($c->beta){
        	include ("includes/beta.xml");
        } else {
	        // let's check a definition for this system
	        $file = "includes/" . $c->client_os . "_" . $c->client_version . ".xml";
	        if (file_exists($file))
	        {
	            include ($file);
	        } else
	        {
	            $file = "includes/" . "none_" . $c->client_version . ".xml";
	            if (file_exists($file))
	            {
	                include ($file);
	            } else if (file_exists("includes/unknown.xml"))
	            {
	                include ("includes/unknown.xml");
	            } else
	            {
	                echo "<error>No data for your version</error>\n";
	            }
	        }
        }
    } else {
    	echo "<nonewversion />\n";
    }
}
echo "</update>";

