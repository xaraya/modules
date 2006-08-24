<?php
/**
 * PHP Bean client base package.  Allows PHP scripts to communicate with
 * servers implementing the PHP Bean specification, such as the Sitellite
 * Object Server.  For more info, see:
 *
 * http://www.phpbeans.com/
 *
 * <code>
 * <?php
 *   include 'beanclient.php';
 *
 *   // connect to the server
 *   try 
 *   {
 *     $client = new PHP_Bean_Client('localhost', 3843, 2);
 *     $client->connect();
 * 
 *     // authenticate yourself
 *     $auth = $client->getObject('auth');
 *     $auth->identify($user,$pass);
 *
 *     // call a method using the literal query syntax
 *     $res = $client->call('server/uptime');
 *     echo 'Server up since: ' . $res;
 *
 *     // or use a local object to alias a server-side one
 *     $server =& $client->getObject('server');
 *     echo 'Server up since: ' . $server->uptime();
 *
 *     // and finally, disconnect
 *     $client->disconnect();
 *   } catch (Exception $e) 
 *   {
 *     // If anything went wrong along the way, die with the error message
 *     die($e->getMessage());
 *   }
 * ? >
 * </code>
 *
 * Notes:
 *  The original code was adapted for php 5 by Marcel van der Boom <marcel@hsdev.com>
 *  The @version which was below was therefore removed..
 * Changelog of these changes:
 *  - generic php5 adaptations, constructor, class constants etc.
 *  - use Exceptions instead of custom error class. (this changes the interface!)
 *  - 
 *
 * @package Database
 * @author John Luxford <lux@simian.ca>
 * @copyright Copyright (C) 2004, Simian Systems Inc.
 * @license http://www.opensource.org/licenses/lgpl-license.php
 * @access public
**/

/* 
    Class to be able to instantiate exception objects raised on server locally 
    The name of this class must match the name of the class used at the server
    side of things.
*/
class ObjectServerException extends Exception
{
}

// Our own exceptions 
class BeanClientException extends Exception
{
}

interface IPHP_Bean_Client
{
    function __construct($server = 'localhost', $port = 3843, $timeout = 15);
    function connect();
    function disconnect();
    function &getObject($name); // $obj = $client->getObject('name') type calling
    function call($request);    // $res = $client->call('object/method?param=value') type calling    
}

class PHP_Bean_Client implements IPHP_Bean_Client
{
    /**
     * Socket connection resource.
    **/
    private $connection;
    public  $connected;

    /**
     * Server name or IP address.
    **/
    public $server = 'localhost';

    /**
     * Server port.
    **/
    public $port = 3843;

    /**
     * Socket timeout for connection and requests.
    **/
    public $timeout = 15;

    /**
     * Log of communication between the client and the server.
    **/
    public $log = array ();

    /**
     * Whether to keep a log of the communication or not.
    **/
    public $logging = false;

    /**
     * Constructor method.
     *
     * @param string
     * @param int
     * @param int
    **/
    public function __construct($server = 'localhost', $port = 3843, $timeout = 15) 
    {
        $this->server  = $server;
        $this->port    = $port;
        $this->timeout = $timeout;
    }

    /**
     * Connects to the server.
     *
     * @return boolean true on success
     * @throws BeanClientException
    **/
    public function connect() 
    {
        $err = ''; $errno = 0;
        $this->connection = fsockopen(
            $this->server, $this->port,
            $errno,  $err,
            $this->timeout
        );
        if(!$this->connection) 
            throw new BeanClientException($err, $errno);

        // parseResponse will raise an exception if we get a server exception back
        $response = $this->parseResponse($this->getResponse());
        $this->connected = true;
        return true;
    }

    /**
     * Disconnects from the server.
    **/
    public function disconnect() 
    {
        if($this->logging) 
            $this->log[] = "SEND: quit\r\n";
        
        fputs($this->connection, "quit\r\n");
        $response = $this->getResponse();
        fclose($this->connection);
        $this->connected = false;
    }
    
    /**
     * Creates a local object that mimicks the specified server-side object.
     *
     * @param string
     * @return object
    **/
    public function &getObject($name) 
    {
        // This excepts when it cant complete
        $methods = $this->call($name . '/objectInfo');

        // Construct the local class, based on the server class.
        // The name is prefixed and it has a flattened methodlist 
        // i.e. it recursively went through the server objects and added all
        // the methods. Since on the the actual call, the server object is called,
        // the resolvement is not of our concern here. (hence the 'mimick')
        
        $class = 'PHP_Bean_' . $name;
        if(!class_exists($class))
        {
            $code  = "<?php\n\nclass $class ";
            $code .= "{\n";
            $code .= '    private $client = null;'."\n\n";
            $code .= '    function __construct(&$client) ';
            $code .= '    {' . "\n";
            $code .= '        $this->client =& $client;' . "\n";
            $code .= "    }\n\n";

            foreach($methods as $method => $info) 
            {
                // We dont want the server constructor, since we tie it to the
                // client here. (it should not have been delivered to us in the
                // first place, but we check anyways)
                if($method == '__construct') 
                    continue;
            
                // Reconstruct the class, and make all the methods call the server
                // through out protocol.
                // NOTE: this is the PHP specific part where we make the client
                //       capable of PHP notation locally, while this bit translates
                //       it into real protocol calls.
                // @todo make sure that *if* the Bean method declares a default
                // we can get to it here to set in the parameters.
                $code .= "    function $method(";
                if(is_array($info['parameters']) and !empty($info['parameters'])) 
                {
                    $i=1;
                    foreach($info['parameters'] as $param => $paramInfo)
                    {
                        $code .= '$'.$param;
                        if(isset($paramInfo['default']))
                        {
                            $code .= '='.$paramInfo['default'];
                        }
                        if($i != count($info['parameters'])) 
                            $code .= ",";
                        $i++;
                    }
                }
                    
                $code .= ") {\n";
                $code .= "        \$res = \$this->client->call('$name/$method";
                if(is_array($info['parameters'])) 
                {
                    $sep = '?';
                    foreach($info['parameters'] as $param => $paramInfo) 
                    {
                        $code .= "$sep'.\$this->client->makeStr('$param',\$$param).'";
                        $sep = '&';
                    }
                }
                $code .= "');\n";
                $code .= "        if(\$res instanceof Exception) throw \$res;\n";
                $code .= "        return \$res;\n";
                $code .= "    }\n\n";
            }

            $code .= "}\n\n?>";

            //echo $code;
            //die();

            // @todo dont do eval here like we do for template compiling, use the same logic.
            ob_start();
            if(eval('?>'.$code) === false) 
            {
                $this->error = ob_get_contents();
                echo $this->error;
                ob_end_clean();
                $false = false;
                return $false;
            }
            ob_end_clean();
        }
        // Give the object to the client for use.
        $obj = new $class($this);
        return $obj;
    }

    /**
     * Calls a method on a remote object.
     *
     * @param string
     * @return mixed
    **/
    function call($request) 
    {
        if($this->logging) 
            $this->log[] = "SEND: $request\r\n";
        
        fputs($this->connection, "$request\r\n");
        $response = $this->parseResponse($this->getResponse());
        return $response;
    }

    /**
     * Fetches the response from the server.  This is called automatically by
     * the call() method.
     *
     * @return string
    **/
    function getResponse() 
    {
        $response = '';
        while(true) 
        {
            $resp = fread($this->connection,4096);
            $response .= $resp;
            if($this->logging) 
                $this->log[] = 'RECV: ' . $resp;
            
            // @todo i find matching on \r\n a bit risky \n is known to break,
            // why should \r\n be safe?
            if(strpos($resp, "\r\n") !== false) 
                break;
        }
        return trim($response);
    }

    /**
     * Unserializes the response from the server.  Called automatically by the
     * call() method.
     *
     * @param string
     * @return mixed
    **/
    private function parseResponse($resp) 
    {
        $obj = unserialize($resp);
        // If we got an exception back, throw it, the server throws only exceptions
        // which are of type Exception or ObjectServerException
        if($obj instanceof ObjectServerException)
            throw $obj;
        
        return $obj;
    }

    /**
     * Makes a properly formatted key/value pair ready for inclusion in a
     * method call.
     *
     * @param    string
     * @param    string
     * @return    string
    **/
    public function makeStr($name, $value='') 
    {
        if(is_object($value)) 
            $value = (array) $value;
        
        if(is_array($value)) 
        {
            $str = ''; $sep = '';
            foreach($value as $k => $v) 
            {
                $str .= $sep . $name . '[' . $k . ']=' . urlencode($v);
                $sep = '&';
            }
            return $str;
        }
        return $name . '=' . urlencode($value);
    }
}
?>