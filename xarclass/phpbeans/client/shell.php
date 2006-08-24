<?php
// Include client lib
include_once('beanclient.php');

$client = null;
$obj    = '';

while(true) 
{
    prompt('phpbeans');
    $input = explode(' ', trim(fgets(STDIN)));
    //fwrite(STDOUT,"Input: '" .  $input[0] . "' on object '$obj'\n");
    if(empty($input[0]))
        continue;
    switch($input[0]) 
    {
        case 'quit':
        case 'exit':
        case 'disconnect':
            if($client->connected) 
                $client->disconnect();
            m("Goodbye");
            break 2;
        case 'connect':
            if($client->connected) 
                $client->disconnect();
            if(!isset($input[1])) 
                $input[1] = 'localhost';
            if(!isset($input[2])) 
                $input[2] = 3843;
            
            try 
            {
                $client = new PHP_Bean_Client($input[1], $input[2]);
                $client->logging = true;
                $client->connect();
                m("Connected");
            } catch(Exception $e) 
            {
                e($e->getMessage());
            }
            break;
        case 'use':
            if(!$client->connected)
            {
                e("You need to connect first");
                break;
            }
            $obj = $input[1];
            
            try 
            {
                $methods = $client->call($obj . '/objectInfo');
                if(!is_array($methods)) 
                {
                    e($client->error);
                    break;
                }
                m("Using object $input[1]");
            } catch(Exception $e) 
            {
                e($e->getMessage());
            }
            break;
        case 'log':
            m(print_r($client->log,true));
            break;
        default:
            if(!$client->connected) 
            {
                e("Unknown command");
                break;
            }

            if(strpos($input[0], '/') === false) 
            {
                $call = $obj . '/' . $input[0];
                $method = $input[0];
            } else 
            {
                $call = $input[0];
                list($obj, $method) = split('/', $input[0]);
            }

            array_shift($input);

            if(count($input) > 0) 
            {
                $sep = '?';
                $c = 0;
                while(count($input) > 0) 
                {
                    $val = array_shift($input);
                    if(strpos($val, '=') === false) 
                    {
                        $p = @array_keys($methods[$method]['parameters']);
                        $val = $p[$c] . '=' . $val;
                    }
                    $call .= $sep . $val;
                    $sep = '&';
                    $c++;
                }
            }

            m("CALL: $call");
            try 
            {
                $res = $client->call($call);
            } catch(Exception $e) 
            {
                e($e->getMessage());
                break;
            }
            if($res === null) 
                m("null");
            elseif(is_bool($res)) 
                m(($res ? "true" : "false")); 
            elseif(is_numeric($res) || is_string($res)) 
                m($res);
            else 
                m(print_r($res,true));
    } // switch
} // while true

// info msg
function m($msg)
{
    fwrite(STDOUT,"$msg\n");
}

// error msg
function e($msg)
{
    fwrite(STDERR,"ERROR: $msg\n");
}

// set the prompt 
function prompt($msg)
{
    fwrite(STDOUT,$msg . '> ');
}
?>