<?php


/**
 * File: $Id$
 *
 * Short description of purpose of file
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/


/**
 * Automatic menulinks for admin panels
 *
 * @author  Marcel van der Boom <marcel@xaraya.com>
 * @param   param1 type Description of parameter 1
 * @param   param2 type Description of parameter 2
 * @return  type to return description
 * @throws  list of exception identifiers which can be thrown
 * @todo    list of things which must be done to comply to relevant RFC
*/
function bkview_adminapi_getmenulinks() 
{

    $menulinks[] = Array('url'   => xarModURL('bkview',
                                              'admin',
                                              'view'),
                         'title' => xarML('View registered repositories'),
                         'label' => xarML('View repositories'));

    $menulinks[] = Array('url'   => xarModURL('bkview',
                                              'admin',
                                              'new'),
                         'title' => xarML('Register a new repository'),
                         'label' => xarML('Add repository'));
    $menulinks[] = Array('url'   => xarModURL('bkview',
                                              'admin',
                                              'modifyconfig'),
                         'title' => xarML('Confgure module bkview'),
                         'label' => xarML('Modify config'));

    return $menulinks;
}
  
?>
