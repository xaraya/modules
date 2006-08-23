<?php
/**
 * Auth namespace bean 
 *
 * The 'server' and 'auth' Bean are the two system Beans of the objectserver.
 * We expose them as Beans because we can not call the server directly, but the
 * methods 'identify' and 'listObjects' require methods to run inside the server
 * itself. (and because we can not have multiple inheritance in PHP)
 * We do NOT expose these Beans inside one Bean because we want to control
 * access to them separately (i.e. some identities may be able to use the 
 * Server Bean, but not the Auth Bean, meaning those identities can only use 
 * the server in an anonymous way.) Would we implement the two Beans into one
 * class we would lose that ability.
 *
 * @copyright HS-Development BV, 2006
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://hsdev.com
 * @author Marcel van der Boom <mrb@hsdev.com>
**/
class Bean_Auth extends PHP_Bean 
{
    public $namespace = 'auth';

    /**
     * Let client identify itself (or change identity)
     *
     * @access public
     * @param  string
     * @param  string
     * @return bool true on recognized identities
    **/
    function identify($user = 'anonymous', $pass ='anonymous')
    {
        return $this->server->identify($user, $pass);
    }
}
?>