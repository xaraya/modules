<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
function xproject_admin_modifyconfig()
{
    //xarModLoad('xproject','user');
    $data = xarModAPIFunc('xproject','admin','menu');

    if (!xarSecurityCheck('AdminXProject', 0)) {
        return;
    }

    $data['authid'] = xarSecGenAuthKey();

    $data['categories'] = xarModAPIFunc('addressbook','util','getitems',array('tablename'=>'categories'));
    $data['itemsperpage'] = xarModGetVar('xproject', 'itemsperpage');
    $data['staffcategory'] = xarModGetVar('xproject', 'staffcategory');
    $data['clientcategory'] = xarModGetVar('xproject', 'clientcategory');
    $data['websiteprojecttype'] = xarModGetVar('xproject', 'websiteprojecttype');
    $data['draftstatus'] = xarModGetVar('xproject', 'draftstatus');
    $data['activestatus'] = xarModGetVar('xproject', 'activestatus');
    $data['archivestatus'] = xarModGetVar('xproject', 'archivestatus');
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update Configuration'));

    $hooks = xarModCallHooks('module', 'modifyconfig', 'xproject',
                       array('module' => 'xproject'));
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
