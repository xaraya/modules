<?php
/* Telnet Script v 1.1.xar -- 
// based on telnet rfc

Poorly translated from French
by David Taylor http://www.phatcom.net

v1.0 Original script by Marc Ennaji 
as found on http://www.phpbuilder.com

Usage:

//Set the host and prompt variables:
$host = "whatever.you.decide";
$port = "23"; //telnet
$prompt = "% "; //bsd user prompt
$username = "whatever\'s";
$password = "clever";
$log_dir = 'telnet_logs" // or '$log_dir/this_month'
$my_wish = "your command";

//declare the object
$run = new telnet;
$run->set_host($host); 
$run->set_prompt($prompt);
$run->set_log_dir($log_dir); // defaults to directory 'log/this_month'
//connect
$run->connect(null); 
//read/write combo's
$run->read("ogin: "); 
$run->write("$username"); 
$run->read("word: "); 
$run->write("$password"); 
//or have it display everything up to a predefined prompt
$run->wait_for_prompt();
$run->write("$my_wish"); 
$run->wait_for_prompt(); 
//hangup
$run->disconnect();
*/

define ("TELNET_ERROR", 0);
define ("TELNET_OK", 1);
define ("TELNET_ASK_CONFIRMATION", 2);
//set this to what your device says for the more screen
define ("GET_CONFIRMATION", "[More? <ret>=next entry, <sp>=next page, <^C>=abort]");

Class telnet 
{ 
    var $socket= NULL;
    var $host;
    var $port;
    var $get_error = '';
    var $error_content = '';
    var $prompt = "\$ ";
    var $log = NULL;  // file handle
    var $log_dir= 'modules/xartelnet/logs'; 
    var $log_file_name = '';
    var $timeout = '30';
    var $debug = '';
    var $add_html_to_newline = '1';

    function set_defaults() {
	$this->host = xarModGetVar('xartelnet','host');
	$this->debug = xarModGetVar('xartelnet','debug');	
        $this->port = xarModGetVar('xartelnet','port');
        $this->prompt = xarModGetVar('xartelnet','prompt');	
	$this->add_html_to_newline = xarModGetVar('xartelnet','add_html_to_newline');
	$this->timeout = xarModGetVar('xartelnet','timeout'); // in seconds
    }
	
		
    //------------------------------------------------------------------------
    //returns socket handle on success, 0 on failure
      
    function connect($old_socket) 
    {
	if(empty($this->host) || empty($this->port)) $this->set_defaults();
	
	if(!$old_socket) {
	        $this->socket = fsockopen($this->host,$this->port); 
	} else {
		$this->socket = $old_socket;
		//echo"Resuming previous connection";
        	return TELNET_OK; 
	}
	$this->get_error =  "Connecting ...  ";	        
	if (! $this->socket)
	{ 
            //$this->get_error .= "Failed!<br>\n Could not establish a connection. Error code : " . $php_errormsg . "\n<br>";
	    $this->get_error .= "Failed!<br>\n Could not establish a connection.\n<br>";
            $this->get_error .= "If error code is Success, then the IP is wrong : $this->host\n<br>";	    
            //echo $this->get_last_error();
	    return TELNET_ERROR;
	}
	
	socket_set_timeout($this->socket,$this->timeout,0);
	$this->get_error .= "Connected!\n<br>";
	//echo $this->get_last_error();
	return $this->socket;
    }

    //------------------------------------------------------------------------
    //read the characters on socket
    
    function read($phrase) 
    { 
        $NULL = chr(0); 
	$IAC = chr(255);  // Interpreted As Command
    	$buf = ''; 

	if (! $this->socket)
	{
	    $this->get_error = "Connection not open.\n<br>";
	    echo $this->get_last_error();
	    return TELNET_ERROR;
	}
	    $count = 1;
	while (1) 
	{ 
//	    $count = 1;
	    $count++;
	    
            $c = $this->getc(); 

            if ($c === false) // reading characters on socket 
	    {
	        if ($this->error_content($buf))  // detecting if remote host gave error
	       	    return TELNET_ERROR;
		if($count < 5) {
		    continue;
		} else {
		    
		$this->get_error = "\nSearch Error : '" . $phrase . "', was not found in the content recieved : '" . $buf . "' ($count cycles)\n";
		//echo $this->get_last_error();
		//echo"found an error!";
		$this->log_it($this->get_error);
   		return TELNET_ERROR;
		}
	    }

            if ($c == $NULL || $c == "\021") 
            	continue; 
 
            if ($c == $IAC)  // Interpreted As Command
	    {
	    	$c = $this->getc(); 

        	if ($c != $IAC) // the 'true' character is doubled for the difference of the IAC ??
	    	    if (! $this->set_telnet_options($c))
		    	return TELNET_ERROR;
		    else
		    	continue;
	    }

	    //$buf is the complete conversation, parse it to get some valuable info
	    $buf .= $c;
	    
	    //prints the characters to the browser.
	    //echo "$c"; 
	    
	    // let the user know it has a confirmation request
	    if (substr($buf,strlen($buf)-strlen(GET_CONFIRMATION)) == GET_CONFIRMATION)
	    {
		$this->log_it($this->get_last_line($buf));
		$this->write(" ");
		//return TELNET_ASK_CONFIRMATION;
	    }
            
	    if ((substr($buf,strlen($buf)-strlen($phrase))) == $phrase)
	    {  
	        // chain has been worked ??
	        $this->log_it($this->get_last_line($buf));

	        //uncomment this for debugging the results.		
		//echo"<br>$this->get_last_line($buf)<br>";
	        if ($this->error_content($buf)) {
	       	   return TELNET_ERROR;
	        } else {
		    //this changes the new line to html
		    //maybe make this configurable?
		    if($this->add_html_to_newline == '1') {
	        	if(eregi("\n",$buf)) {			
			    $buf = str_replace("\n","<br>",$buf);	    	   
			}	
		    }
	           //return TELNET_OK;
		   return $buf;
		}

    	    }
	}
    }

    //------------------------------------------------------------------------
    //read a character on the socket
    
    function getc()
    {
    	return fgetc($this->socket);
    }

    //------------------------------------------------------------------------
    //negociate telnet options
    
    function set_telnet_options($commande)
    {
         //do the bare minimun options, no line buffering or nothing!
	 //send one character at a time.
	 //what this does is answer WONT to DO or DONT requests
	 //and DONT to WILL or WONT requests
	 //IAC tells the remote side it Interpreted A Command and is sending a response

        $IAC = chr(255); 
        $DONT = chr(254); 
        $DO = chr(253); 
        $WONT = chr(252); 	    
        $WILL = chr(251); 

    	if (($commande == $DO) || ($commande == $DONT)) 
    	{ 
            $opt = $this->getc();  
            //echo "wont ".ord($opt)."\n"; //debugging purposes
            fwrite($this->socket,$IAC.$WONT.$opt); 
        } 
    	else if (($commande == $WILL) || ($commande == $WONT)) 
    	{ 
            $opt = fgetc($this->socket);  
            //echo "dont ".ord($opt)."\n"; //debugging purposes
            fwrite($this->socket,$IAC.$DONT.$opt); 
        } else 
    	{ 
            $this->get_error .= "Error : unknown command ".ord($commande)."\n"; 
            return false;
        } 
	return true;
    }
     
    //------------------------------------------------------------------------
    //writes to the socket
    
    function write($buffer, $log_value = "", $end_of_line = true) 
    {			     
	if (! $this->socket)
	{
	    $this->get_error = "Socket not open.\n<br>";
	    return TELNET_ERROR;
	}

	if ($end_of_line)
	    $buffer .= "\n";

        if (fwrite($this->socket,$buffer) < 0)
	{
	    $this->get_error = "Error occurred on socket.\n<br>";
	    return TELNET_ERROR;
	}

	if ($log_value != "")  // hides sensitive info (like passwords...)
	    $buffer = $log_value . "\n";

	if (! $end_of_line)  // in the log (but not on the socket) everything including end of line character
	    $buffer .= "\n";

       	$this->log_it("> " .$buffer); 

	return TELNET_OK;
    } 

    //------------------------------------------------------------------------
    // disconnect the socket
    
    function disconnect() 
    {                    
        if ($this->socket) 
	{
            if (! fclose($this->socket))
	    {
	    	$this->get_error = "Error closing the connection\n<br>";
		return TELNET_ERROR;
	    }
	    $this->socket = NULL; 
	}
	$this->set_log(false,"");

        return TELNET_OK;
    }
    
    //------------------------------------------------------------------------
    //set the error variables
    
    function error_content($buf) 
    {   
	//make these configurable in modifyconfig
    	$error_message[] = "nvalid";		// Invalid input, ...
	$error_message[] = "o specified";     // No specified atm, ...
	$error_message[] = "nknown";	        // Unknown profile, ...
	$error_message[] = "o such file or directory"; // invalid command
	$error_message[] = "llegal";		// illegal file name, ...

	foreach ($error_message as $error)
	{
	    if (strpos ($buf, $error) === false)
	    	continue;

	    	// an error is detected
   	    $this->get_error =  "An error message has been detected on the remote host : " . 
	    			   "<BR><BR>" . $this->get_last_line($buf,"<BR>") . "<BR>";

	    return true;
	}
	return false;
    }

    //------------------------------------------------------------------------
    //reads everything up to the prompt

    function wait_for_prompt()
    {
        return $this->read($this->prompt);
    }

    //------------------------------------------------------------------------
    //set the prompt for whatever host

    function set_prompt($s) { $this->prompt = $s; return TELNET_OK; }
    //------------------------------------------------------------------------
    //set remote computer to connect to

    function set_host($s) { $this->host = $s;}

    //------------------------------------------------------------------------
    //set the port to connect to

    function set_port($s) { $this->port = $s;}

    //------------------------------------------------------------------------
    //set the timeout interval

    function set_timeout($s) { $this->timeout = $s;}

    //------------------------------------------------------------------------
    //set the log directory

    function set_log_dir($s) { $this->log_dir = $s;}
    //------------------------------------------------------------------------
    // echo last error

    function get_last_error() { return $this->get_error; }

    //------------------------------------------------------------------------
    //create the logging stuff

    function set_log($create_log, $action) 
    { 
        if ($this->log && $create_log)  
	    return TELNET_OK;
	  
        if ($create_log)   
	{	
	    if(!$this->log_dir) {
	    	$this->log_dir =  "log/" . date("m");
	    }
	    if (! file_exists($this->log_dir)) // make sure this directory exists in the same directory as this script
	    {
	    	if (mkdir($this->log_dir, 0700) === false)
            	{
             	    $this->get_error = "Cannot create log file directory " .  $this->log_dir;
             	    return TELNET_ERROR;
            	}
	    }
            $this->log_file_name =    date("m-d") . "_" .
                                      $_SERVER['REMOTE_ADDR']
                                      . ".log";
		
//	    $this->log_file_name = 	date("d") . "_" . 
//	    				date("H:i:s") . "_" . 
//					$action . "_" . 
//					$HTTP_SERVER_VARS["PHP_AUTH_USER"]
//	    				. ".log"; 

            $this->log = fopen($this->log_dir . "/" . $this->log_file_name,"a");
                
            if (empty($this->log))
            {
             	$this->get_error = "Cannot open log file " . $this->log_file_name;
             	return TELNET_ERROR;
            }
	    $this->log_it("----------------------------------------------\n");
	    $this->log_it("Start of Log for " . $_SERVER['REMOTE_ADDR'] . "\n");
	    $this->log_it("Telnet connection : " . $this->host . ", port " . $this->port . "\n");
      	    $this->log_it("Date : " . date("d-m-Y").  "  " . date("H:i:s") . "\n");
     	    $this->log_it("Action : " . $action . "\n");
	    $this->log_it("----------------------------------------------\n");
	    return TELNET_OK;
	}
	else
	{
	    if ($this->log) 
	    {
	    	$this->log_it("----------------------------------------------\n");
	    	$this->log_it("End of Log\n");
	    	fflush($this->log);
	        if (! fclose($this->log))
	        {
	       	    $this->get_error = "error closing log.\n";
		    return TELNET_ERROR;
		}
		$this->log = NULL; 
	    }
	    return TELNET_OK;
	}
    }

    //------------------------------------------------------------------------
    // log it to a file

    function log_it($s)
    {
    	if ($this->log) {
	    fwrite($this->log, $s);  
	}
	else
	{
	    $this->set_log(true,$s);
	}
    }
    //------------------------------------------------------------------------
    //removes the telnet echo

    function get_last_line($s, $separator="\n")
    
    {
        // removes the echo of your typed command

        $lines = split("\n",$s);
	$results = "";
	$first_line = true;
        
        while(list($key, $data) = each($lines))
        {
	    if ($first_line)
	    	$first_line = false;
	    else
	        if ($data != "")
	    	    $results .= $data . $separator;
        }
	$results == substr($results,strlen($results)-1); // removes the character of last line.

	return $results;
    }
    
    //------------------------------------------------------------------------


}   //End
?>