<?php
/**
 * XORBA server operations
 *
 * @package modules
 * @subpackage xorba
 * @copyright The Digital Development Foundation, 2006-08-19
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @param  string  $op Operation (start|stop|restart)
 * @return integer Return code (0= ok, <> 0 = errorcode)
 * @author Marcel van der Boom <mrb@hsdev.com>
 **/
function xorba_cliapi_server($args = array())
{
    extract($args);
    switch ($op)
    {
        case 'start'  :
            chdir('modules/xorba/xarclass/phpbeans');
            error_reporting(0); // Thank you PEAR :-(
            include 'daemon.php';
        case 'stop'   :
        case 'restart':
            fwrite(STDOUT,"$op server here\n");
            break;
        default:
            fwrite(STDERR,"Error: unknown operation ($op)");
    }
    return 1; // Generic failure
}
?>