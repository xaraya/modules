<?php
// Include client lib
include_once('beanclient.php');

$client = null;
$obj    = '';

while(true) 
{
    echo 'phpbeans> ';
    $input = explode(' ', trim(fgets(STDIN)));
    //fwrite(STDOUT,"Input: '" .  $input[0] . "' on object '$obj'\n");
    if(empty($input[0]))
        continue;
    switch($input[0]) 
    {
        case 'quit':
            if($client->connected) 
                $client->disconnect();
            echo "Goodbye.\n";
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
                echo "Connected.\n";
            } catch(Exception $e) 
            {
                echo 'Error: '.$e->getMessage()."\n";
            }
            break;
        case 'use':
            if(!$client->connected)
            {
                echo "You need to connect first\n";
                break;
            }
            $obj = $input[1];
            
            try 
            {
                $methods = $client->call($obj . '/objectInfo');
                if(!is_array($methods)) 
                {
                    echo 'Error: ' . $client->error . "\n";
                    break;
                }
                echo "Using object $input[1].\n";
            } catch(Exception $e) 
            {
                echo 'Error: '.$e->getMessage() . "\n";
            }
            break;
        case 'log':
            print_r($client->log);
            break;
        default:
            if(!$client->connected) 
            {
                echo "Unknown command.  Try 'help' for command list.\n";
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

            fwrite(STDOUT, "CALL: $call\n");
            try 
            {
                $res = $client->call($call);
            } catch(Exception $e) 
            {
                echo 'Error: ' . $e->getMessage() . "\n";
                break;
            }
            if($res === null) 
                echo "null\n";
            elseif(is_bool($res)) 
                echo ($res ? "true\n" : "false\n"); 
            elseif(is_numeric($res) || is_string($res)) 
                echo $res . "\n";
            else 
                print_r($res);
    } // switch
} // while true
?>