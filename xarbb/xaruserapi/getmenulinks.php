<?php
/**
 * File: $Id$
 * 
 * Standard function to retrieve menu links
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
function xarbb_userapi_getmenulinks()
{

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>
