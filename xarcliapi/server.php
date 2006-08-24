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
            // Bring the phpbeans package in scope
            ini_set('include_path',ini_get('include_path').':modules/xorba/xarclass/phpbeans');
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