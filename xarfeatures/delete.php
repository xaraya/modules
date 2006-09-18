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
function xproject_features_delete($args)
{
    extract($args);

    if (!xarVarFetch('featureid', 'id', $featureid)) return;
    if (!xarVarFetch('objectid', 'isset', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'isset', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $featureid = $objectid;
    }

    if (!xarModAPILoad('xproject', 'user')) return;

    $item = xarModAPIFunc('xproject',
                         'features',
                         'get',
                         array('featureid' => $featureid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;


    if (!xarSecurityCheck('DeleteXProject', 1, 'Item', "$item[project_name]:All:$item[projectid]")) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'xproject', xarVarPrepForDisplay($projectid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    if (empty($confirm)) {

        $projectinfo = xarModAPIFunc('xproject',
                              'user',
                              'get',
                              array('projectid' => $item['projectid']));

        xarModLoad('xproject','user');
        $data = xarModAPIFunc('xproject','admin','menu');

        $data['featureid'] = $featureid;
        $data['projectinfo'] = $projectinfo;

        $data['feature_name'] = xarVarPrepForDisplay($item['feature_name']);
        $data['confirmbutton'] = xarML('Confirm');

        $data['authid'] = xarSecGenAuthKey();

        return $data;
    }
    if (!xarSecConfirmAuthKey()) return;
    if (!xarModAPIFunc('xproject',
                     'features',
                     'delete',
                     array('featureid' => $featureid))) {
        return;
    }
    xarSessionSetVar('statusmsg', xarML('Feature Deleted'));

    xarResponseRedirect(xarModURL('xproject', 'admin', 'display', array('projectid' => $item['projectid'], 'mode' => "features")));

    return true;
}

?>
