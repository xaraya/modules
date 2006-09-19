<?php
/**
 * xTasks Module - Project ToDo management module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xTasks Module
 * @link http://xaraya.com/index.php/release/704.html
 * @author St.Ego
 */
function xtasks_user_settings()
{
    $data = xarModAPIFunc('xtasks','admin','menu');

    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('My Settings')));
    xarModSetVar('xtasks', 'show_owner', false);
    xarModSetVar('xtasks', 'show_project', false);
    xarModSetVar('xtasks', 'show_client', false);
    xarModSetVar('xtasks', 'show_importance', false);
    xarModSetVar('xtasks', 'show_priority', false);
    xarModSetVar('xtasks', 'show_age', false);
    xarModSetVar('xtasks', 'show_pctcomplete', false);
    xarModSetVar('xtasks', 'show_planned_dates', false);
    xarModSetVar('xtasks', 'show_actual_dates', false);
    xarModSetVar('xtasks', 'show_hours', false);

    $data['submitlabel'] = xarML('Submit');
    $data['uid'] = xarUserGetVar('uid');
    return $data;
}

?>