<?php

/**
 * File: $Id$
 *
 * Updating bkview configuration
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage module name
 * @link  link to where more info can be found
 * @author author name <author@email> (this tag means responsible person)
*/

function bkview_admin_updateconfig() {
    xarModCallHooks('module','updateconfig','bkview',array());
    xarResponseRedirect(xarModUrl('bkview','admin','modifyconfig'));
    return true; 
}