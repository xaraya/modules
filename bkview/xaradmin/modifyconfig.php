<?php

/**
 * File: $Id$
 *
 * Configuration screen
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage module name
 * @link  link to where more info can be found
 * @author author name <author@email> (this tag means responsible person)
*/


function bkview_admin_modifyconfig() {
    $data = array();
    
    $hooks = xarModCallHooks('module','modifyconfig','bkview',array());
    $data['pageinfo'] = xarML('BKview configuration');
    $data['hooks'] = $hooks;
    $data['updatelabel']= xarML('Update');
    $data['formaction'] = xarModUrl('bkview','admin','updateconfig');
    return $data;
}