<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
/**
 * View all groups
 *
 * @author MichelV
 */
function xproject_groups_view()
{
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;

    if (!xarSecurityCheck('ViewXProject', 1, 'groups')) return;

    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('xproject', 'groups', 'countitems'),
        xarModURL('xproject', 'groups', 'view', array('startnum' => '%%')),
        xarModGetVar('xproject', 'itemsperpage'));
    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing
     */
    
    $data = array();

    $groups = xarModAPIFunc('xproject',
               'groups',
               'getall',
               array('startnum' => $startnum,
                     'numitems' => xarModGetVar('xproject','itemsperpage')));

    $tableHead = array(xarML('Team'), _OPTION);

    $output->TableStart('', $tableHead, 1);

    foreach($groups as $group) {

        $actions = array();
        $output->SetOutputMode(_XH_RETURNOUTPUT);
    
        if (xarSecAuthAction(0, 'Groups::', "$group[name]::$group[gid]", ACCESS_EDIT)) {
            $grouxaramedisplay = $output->URL(xarModURL('xproject',
                                                   'groups',
                                                   'viewgroup', array('gid'   => $group['gid'],
                                                          'gname' => $group['name'])), xarVarPrepForDisplay($group['name']));
        } else {
            $grouxaramedisplay = $output->Text(xarVarPrepForDisplay($group['name']));
        }
    
        if (xarSecAuthAction(0, 'Groups::', "$group[name]::$group[gid]", ACCESS_EDIT)) {
            $actions[] = $output->URL(xarModURL('xproject',
                               'groups',
                               'modifygroup', array('gid'   => $group['gid'],
                                        'gname' => $group['name'])), xarML('Rename group'));
    
        }
        if (xarSecAuthAction(0, 'Groups::', "$group[name]::$group[gid]", ACCESS_DELETE)) {
            $actions[] = $output->URL(xarModURL('xproject',
                               'groups',
                               'deletegroup', array('gid'    => $group['gid'],
                                        'gname'  => $group['name'],
                                        'authid' => xarSecGenAuthKey())), _DELETE);
    }
    return $data;
}
?>