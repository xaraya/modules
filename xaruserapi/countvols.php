<?php
/**
 * Count volumes in the encyclopedia
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

function encyclopedia_userapi_countvols()
{
    return xarModAPIFunc('categories', 'user', 'countcats',
                        array('cat' => xarModGetVar('encyclopedia','volumes')));
}

?>