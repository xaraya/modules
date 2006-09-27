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
function xproject_features_create($args)
{
    extract($args);

    if (!xarVarFetch('feature_name', 'str:1:', $feature_name, $feature_name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('projectid', 'id', $projectid, $projectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('importance', 'int::', $importance, $importance, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('details', 'str::', $details, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tech_notes', 'str::', $tech_notes, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_approved', 'str::', $date_approved, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_available', 'str::', $date_available, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;

    $featureid = xarModAPIFunc('xproject',
                        'features',
                        'create',
                        array('feature_name'     => $feature_name,
                            'projectid'         => $projectid,
                            'importance'        => $importance,
                            'details'            => $details,
                            'tech_notes'        => $tech_notes,
                            'importance'        => $importance,
                            'date_approved'        => $date_approved,
                            'date_available'    => $date_available));


    if (!isset($featureid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    xarSessionSetVar('statusmsg', xarMLByKey('PROJECTCREATED'));

    xarResponseRedirect(xarModURL('xproject', 'admin', 'display', array('projectid' => $projectid, 'mode' => "features")));

    return true;
}

?>
