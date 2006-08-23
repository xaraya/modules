<?php
/**
 * Process a xorba command line request
 *
 *
 * @return int exitcode to gateway script
 * @todo what do we do with security here?
 */
function xorba_cliapi_process($args)
{
    xarLogMessage("XORBA: processing cli request");
    extract($args);
    assert('$argc > 0 && $argv[1] == "xorba"; /* Wrong call to xorba_cli_process handler */');

    // TODO: Guess ;-)
    if(isset($argv[2]) && $argv[2]=='-u') $user = $argv[3];
    if(isset($argv[4]) && $argv[4]=='-p') $pass = $argv[5];
    if(!isset($user) or !isset($pass)) return xorba_usage();
    if(!xarUserLogin($user,$pass))     return xorba_fatal("Authentication failed");
    if(!isset($argv[6])) return xorba_usage();
    switch ($argv[6])
    {
        case 'server':
            if(!isset($argv[7])) return xorba_usage();
            switch ($argv[7])
            {
                case 'start'  :
                case 'stop'   :
                case 'restart':
                    return xarModApiFunc('xorba','cli','server',array('op'=> $argv[7]));
                    break;  
                default:
                    return xorba_usage();
            }
            break;
        case 'shell':
            fwrite(STDOUT, "Start a shell here\n");
            break;
        default:
            return xorba_usage();
    }
    // Once we got here, stuff is ok
    return 0;
}

function xorba_fatal($msg)
{
    fwrite(STDERR,'ERROR: '. $msg."\n");
    return 1;
}

function xorba_usage()
{
    fwrite(STDERR,
"Usage: xorba -u <user> -p<pass> [xorba cmdline]

    The xorba cmdline has the following syntax:

        server (start|stop|restart) - XORBA server start/stop/restart.
        shell                       - start a XORBA client shell (mostly for debugging)
\n");
    return 1;
}
?>
