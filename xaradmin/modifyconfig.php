<?php
/**
 * XTask Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xTasks Module
 * @link http://xaraya.com/index.php/release/704.html
 * @author St.Ego
 */
function xtasks_admin_modifyconfig()
{
    //xarModLoad('xtasks','user');
    $data = xarModAPIFunc('xtasks','admin','menu');

    if (!xarSecurityCheck('AdminXTask', 0)) {
        return;
    }

    $data['authid'] = xarSecGenAuthKey('xtasks');
    
    $data['dateformat'] = xarModGetVar('xtasks', 'dateformat'); // int
    $data['maxdone'] = xarModGetVar('xtasks', 'maxdone');
    $data['refreshmain'] = xarModGetVar('xtasks', 'refreshmain');
    $data['showextraasterisk'] = xarModGetVar('xtasks','showextraasterisk');
    $data['showlinenumbers'] = xarModGetVar('xtasks','showlinenumbers');
    $data['showpercent'] = xarModGetVar('xtasks','showpercent');
    $data['showpriority'] = xarModGetVar('xtasks','showpriority');
    $data['todoheading'] = xarModGetVar('xtasks', 'todoheading');
    $data['itemsperpage'] = xarModGetVar('xtasks', 'itemsperpage');
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update Configuration'));

    $hooks = xarModCallHooks('module', 'modifyconfig', 'xtasks',
                       array('module' => 'xtasks'));
    if (empty($hooks)) {
        $data['hooks'] = array('categories' => xarML('You can assign base categories by enabling the categories hooks for this module'));
    } else {
        $data['hooks'] = $hooks;

         /* You can use the output from individual hooks in your template too, e.g. with
         * $hooks['categories'], $hooks['dynamicdata'], $hooks['keywords'] etc.
         */
        $data['hookoutput'] = $hooks;
    }
    return $data;
}

?>
