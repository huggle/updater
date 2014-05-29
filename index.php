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

    public static function Latest()
    {
        return "3.0.0";
    }

    function Failed()
    {
        return $this->failed;
    }

    function IsObsolete()
    {
        return ($this->client_version != self::Latest());
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
        echo "<obsolete>" . client::Latest() . "</obsolete>\n";
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
}
echo "</update>";

