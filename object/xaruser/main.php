<?php
/**
 * Object main user function
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage object
 * @author mikespub
 */
/**
 * the main user function
 *
 * This function acts as the interface for the traditional module function-oriented
 * approach of Xaraya, and can probably be dropped later on, e.g. when Xaraya accepts
 * [object=...&method=...] as entry point next to the current [module=...&func=...].
 *
 * Note: this is not supported until after Xaraya 1.0, i.e. subject to incompatible
 *       changes without warning :-)
 */
function object_user_main($args = array())
{
    // get a dynamic object interface
    $interface = xarModAPIFunc('dynamicdata','user','interface',
                               array(// the main templates for the GUI are in this module
                                     'urlmodule' => 'object',
                                     // specify some number of items if you like
                                     'numitems' => xarModGetVar('object','itemsperpage'),
                                     // specify some default object here
                                     //'object' => 'sample',

                                     // TODO: the following GUI functions are available in code
                                     //'functions' => array(),
                                     // the object templates are in some other module
                                     //'tplmodule' => 'dynamicdata',
                                     // use a different sub-class for the dynamic object [interface]
                                     //'classname' => 'My_Object_Interface', // or 'My_Object'
                                    ));

    // let the interface handle the rest
    return $interface->handle($args);
}

?>
