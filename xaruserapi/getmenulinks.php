<?php
/**
 * Standard function to get main menu links
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage kupu Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

function kupu_userapi_getmenulinks()
{
    if (empty($menulinks)){
        $menulinks = '';
    }
    return $menulinks;
}

?>