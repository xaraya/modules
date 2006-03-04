<?php
/**
 * Xaraya Daily Delicious
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage dailydelicious
 * @author John Cox
*/
function dailydelicious_init()
{
    xarModSetVar('dailydelicious', 'user', 'UserName');
    xarModSetVar('dailydelicious', 'pass', 'Password');
    xarModSetVar('dailydelicious', 'importpubtype', 1);
    xarModSetVar('dailydelicious', 'defaultstatus', 0);
    xarModSetVar('dailydelicious', 'title', 'This Week\'s Del.icio.us bookmarks');
    xarRegisterMask('DailyDelicious','All','dailydelicious','All','All','ACCESS_ADMIN');
    return true;
}
function dailydelicious_delete()
{
    xarModDelAllVars('dailydelicious');
    xarRemoveMasks('dailydelicious');
    xarRemoveInstances('dailydelicious');
    return true;
}
?>