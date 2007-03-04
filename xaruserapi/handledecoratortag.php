<?php
/**
 * File: $Id$
 *
 * Handle Decorator Tag
 *
 * @package unassigned
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage calendar
 * @link  http://xaraya.simiansynapse.com
 * @author Roger Raymond <roger@xaraya.com>
 */

/**
 * Handle Decorator Tag
 *
 *
 * @access  public / private / protected
 * @param   object object
 * @param   string decorator
 * @param   string name
 * @return  php code for the template
 * @todo    add validation
 * @todo    make sure it really works (roger?)
 */
function calendar_userapi_handledecoratortag($args = array())
{
    //static $called;
    extract($args); unset($args);
    $output = '';

    $class = 'Calendar_Decorator_'.$decorator;
    if(!class_exists($class)) {
        $output .=  "require_once('" . CALENDAR_ROOT."Decorator/". $decorator . ".php'); ";
    }
    $output .= $name.' = new '.$class.'('.$object.'); ';

    return $output;
}
?>