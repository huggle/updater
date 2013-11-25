<?

class client
{
    public $client_os = 'unknown';
    public $client_version = 'unknown';
    private $failed = false;

    static function ShowEr($e)
    {
        echo "<error>ERROR: $e</error>\n";
        $failed = true;
    }

    function Failed()
    {
        return $this->failed;
    }

    function IsObsolete()
    {
        if ($this->client_version == "3.0.0.2") { return true; }
        return ($this->client_version != "3.0.0.1");
    }

    function __construct()
    {
        if (!isset($_GET['os']))
        {
            client::ShowEr("System must be defined");
            return;
        }
        if (!isset($_GET['version']))
        {
            client::ShowEr("Version must be defined");
            return;
        }
        $this->client_version = $_GET['version'];
        $this->client_os = $_GET['os'];
    }
}


echo "<?xml version=\"1.0\"?>\n\n<update>\n";
$c = new client;
if (!$c->Failed())
{
    if ($c->IsObsolete())
    {
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
            } else
            {
                echo "<error>No data for your version</error>\n";
            }
        }
    }
}
echo "</update>";

