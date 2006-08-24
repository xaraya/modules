<?php
// PEAR dependency here
@php::import('Net.Server');
php::import('Net.Server.Handler');

php::import('server.objectstore');

interface IObjectServer 
{
    function             onStart();
    function           onConnect($clientId = 0);
    function       onReceiveData($clientId = 0, $data = '');
    function             onClose($clientId = 0);
    function          onShutdown();
    function onConnectionRefused($clientId = 0); // only for sequential?
}

// @todo probably split this off to a separate file
class BeanIterator extends DirectoryIterator
{
    private $conf = array();
    
    public function __construct(&$conf)
    {
        $this->conf = $conf['beans'];
        parent::__construct($this->conf['location']);
    }
    
    public function getSuffix()
    {
        $filename = $this->GetFilename();
        $extension = strrpos($filename, ".", 1);
        if ($extension != false)
            return strtolower(substr($filename, $extension, strlen($filename) - $extension));
        else
            return "";
    }
    
    public function isBeanFile()
    {
        return(
                !$this->isDir() and 
                !$this->isDot() and 
                true //$this->getSuffix() == $this->conf['suffix']
        );
    }
}

/**
 * The request handler of the object server.
 */
class ObjectServer extends Net_Server_Handler implements IObjectServer, IObjectStore
{
    // @todo investigate visibility of these
    // Registry of objects we expose
    public $register = array(); 
    // Map identies to client IDs when they connect/use the service
    public $identity = array();

    // Store the starttime of the server.
    public $uptime   = '';
        
    // The object store
    private $store     = null;
    // The transport service
    private $transport = null;
    // Server configuration
    private $conf      = array();
    // Access rules object
    public $access     = null;
    
    // @todo dont hardcode the prefix.
    static $prefix  = 'Bean_';
    
    function __construct(&$conf, ObjectStore &$store, AccessRules $access)
    {
        // Set the object store
        $this->store =& $store;
        
        // Provide sensible defaults for the transport
        if(!isset($conf['server']['port']))     
            $conf['server']['port'] = 3834;
        if(!isset($conf['server']['bindaddress']))
             $conf['server']['bindaddress'] = 'localhost';
        if(!isset($conf['server']['type']))
            $conf['server']['type'] = 'sequential';
        @$this->transport =& Net_Server::create(
            $conf['server']['type'], 
            $conf['server']['bindaddress'], 
            $conf['server']['port']
        );
        $this->transport->setCallbackObject($this);
        
        // Set the configuration object
        $this->conf =& $conf;
        
        // Activate the access rules
        $this->access =& $access;
    }

    function start()
    {
        return $this->transport->start();
    }
    
    /**
     * Startup callback handler.
     * 
     * @todo probably split off the bean registering
     * @todo make it filename independent, but only if it's 100% reliable
     */
    function onStart() 
    {
        $this->uptime = date('Y-m-d H:i:s');

        // Read config to see what beans we can expect
        $beanPrefix = $this->conf['beans']['prefix'];
        $beanBase   = 'PHP_Bean';
        // in conf beanlocation is probably absolute
        $beans = new BeanIterator($this->conf);
        
        fwrite(STDOUT,"Registering beans: ");
        foreach($beans as $bean)
        {
            $file   = $bean->getFileName();
            $suffix = $bean->getSuffix();
            
            if(!$bean->isBeanFile())
                continue;
                    
            // Import the bean into the server
            include_once($bean->getPathName());
            
            // @todo make this less or not dependent on the file details
            $class = $beanPrefix . basename($file,$suffix);
            if(!is_subclass_of($class,$beanBase) or !class_exists($class))
                continue;
                
            // Create the server side Bean 
            $tmp = new $class($this);
            $this->register[$tmp->namespace] =& $tmp;
            fwrite(STDOUT,$tmp->namespace . ' ');
            unset($tmp);
        }
        fwrite(STDOUT,"\n");
    }

    /**
     * New connection callback handler.
     *
     * @param    integer
     */
    function onConnect($clientId = 0) 
    {
        // (Try to) Authorize as anonymous
        $this->authorize($clientId);
    }

    /**
     * Request callback handler.
     *
     * @param    integer
     * @param    string
     */
    function onReceiveData($clientId = 0, $data = '') 
    {
        // See what we got
        $request = $this->parseRequest($data);

        if($request['object'] == 'quit') 
        {
            $this->sendData($clientId, 'goodbye');
            // @todo can we use $this->transport here?
            $this->_server->closeConnection($clientId);
            return;
        }

        // If we dont know this identity yet, (try to) authorize it
        // @todo is this ever true? we authorize on connect to anonymous already
        if(!$this->identity[$clientId]) {
            echo "Authorizing: " . $this->identity[$clientId] ."\n";
            $this->authorize($clientId);
        }
            
        $oar = $this->access->ObjectRules($this->identity[$clientId]);
        if(!$oar->canUse($request['object']))
        {
            $this->sendData(
                $clientId, 
                new ObjectServerException(
                    "User '" . $this->identity[$clientId] .
                    "' does not have permission for object '" . 
                    $request['object'] . "'",-1
                )
            );
            $this->log($clientId, $data, $request);
            return;
        }

        if(!isset($this->register[$request['object']])) 
        {
            $this->sendData(
                $clientId, 
                new ObjectServerException('Unknown Object',-1)
            );
            $this->log($clientId, $data, $request);
            return;
        }

        if(!$this->register[$request['object']]->hasMethod($request['method'])) 
        {
            $this->sendData(
                $clientId, 
                new ObjectServerException(
                    "Unsupported Method '" . $request['method'],-1
                )
            );
            $this->log($clientId, $data, $request);
            return;
        }

        // Set the client id, so the beans can reach it
        $this->clientId = $clientId;
        
        // Run the requested method
        $this->sendData(
            $clientId,
            call_user_func_array(
                array(&$this->register[$request['object']], $request['method']),
                $request['parameters']
            )
        );
        $this->log($clientId, $data, $request);
    }

    /**
     * Closing connection callback handler.
     *
     * @param    integer
     */
    function onClose($clientId = 0) 
    {
        // Remove the identity
        unset($this->identity[$clientId]);
    }
    
    /**
     *  Identify a user based on username and password
     * 
     *  System method for the Auth Bean
    **/
    public function identify($user,$pass)
    {
        // User unknown or pass mismatch
        $identity = $this->access->IdentityRules($user);
        if(!$identity->canUse($pass))
            return false;
        
        // Is user allowed on this connection?
        $info = $this->getClientInfo($this->clientId);
        if(
            $info['host'] == '127.0.0.1' || 
            $info['host'] == $this->conf['server']['bindaddress']
        ) $info['host'] = 'localhost';
        
        $identity = $this->access->HostRules($user);
        if(!$identity->canUse($info['host']))
            return false;
        
        // Seems ok, set the identity
        $this->identity[$this->clientId] = $user;
        return true;
    }
    
    /**
     * List the objects available to the current identity
     * 
     * System method for the Server Bean
     *
    **/
    public function listObjects()
    {
        $identity = $this->access->ObjectRules($this->identity[$this->clientId]);
        $perms = array();
        foreach(array_keys($this->register) as $object)
        {
            if($identity->canUse($object))
                $perms[] = $object;
        }
        return $perms;
    }

    /**
     * Authenticates users against the server.
     *
     * @param    integer
     * @param    string
     * @param    string
    **/
    private function authorize($clientId, $user = 'anonymous', $pass = 'anonymous') 
    {
        $this->clientId = $clientId;
        // Identify this user
        if(!$this->identify($user,$pass)) 
        {
            $this->sendData(
                $clientId, 
                new ObjectServerException("Authentication failed for user '$user'",-1)
            );
            return;
        }
        $this->sendData($clientId, 'welcome');
        
        // Save the identity of the user for this client ID
        $this->identity[$clientId] = $user;
        return true;
    }
  
    /**
     * Sends client responses.
     *
     * @param    integer
     * @param    mixed
    **/
    private function sendData($clientId, $response) 
    {
        $this->response =& $response;
        $toSend = $this->serialize($response);
        $this->_server->sendData($clientId, $toSend,$this->conf['server']['debug']);
    }
    
    /**
     * Parses client requests.
     *
     * @param    string
     * @return    array
    **/
    private function parseRequest($data) 
    {
        // Treat it like an url like:
        // 1. operation
        // 2. object/method
        // 3. object/method?param1=value1&param2=value2 etc.
        $request = parse_url(trim($data));
        $parts = explode('/',$request['path']);
        if(!isset($parts[1]))
            $request['object'] = $parts[0];
        else
        {
            list($request['object'], $request['method']) = $parts;
            $request['parameters'] = array();
            if(isset($request['query'])) 
                parse_str($request['query'], $request['parameters']);
            assert('is_array($request[parameters]);');
        }
        return $request;
    }
    
    /**
     * Serializes response messages.
     *
     * @param    mixed
     * @param    string
    **/
    private function serialize($response) 
    {
        return serialize($response) . "\r\n";
    }

    /**
     * Retrieves client info.
     *
     * @param    integer
     * @param    array
    **/
    private function getClientInfo($clientId) 
    {
        return $this->_server->getClientInfo($clientId);
    }
    
    /**
     * Logs server activity.
     *
     * @param    integer
     * @param    string
     * @param    array
    **/
    function log($clientId, $data, $request) 
    {
        // @todo this doesnt belong here (use a more generic logging mechanism)
        return true;
        // @todo dont use globals
        global $log;

        $client = $this->getClientInfo($clientId);

        $log->log(array(
            'username'       => $this->identity[$clientId],
            'remote_addr'    => $client['host'],
            'raw_request'    => $data,
            'request_object' => $request['object'],
            'request_method' => $request['method'],
            'request_params' => $request['parameters'],
            'response' => $this->response,
        ));
    }
    
    /* ObjectStore interface satisfaction */
    function store(&$object = null)
    {
        return $this->store->store($object);
    }
    
    function &fetch($id)
    {
        $object = $this->store->fetch($id);
        return $object;
    }
    
    function  delete($id)
    {
        return $this->store->delete($id);
    }
}

?>