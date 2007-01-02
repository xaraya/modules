<?php

function xproject_features_update($args)
{
    extract($args);

    if (!xarVarFetch('featureid', 'id', $featureid)) return;
    if (!xarVarFetch('feature_name', 'str:1:', $feature_name, $feature_name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('projectid', 'id', $projectid, $projectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('importance', 'int::', $importance, $importance, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('details', 'str::', $details, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tech_notes', 'html:basic', $tech_notes, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_approved', 'str::', $date_approved, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_available', 'str::', $date_available, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;

    if(!xarModAPIFunc('xproject',
                    'features',
                    'update',
                    array('featureid'        => $featureid,
                        'feature_name'         => $feature_name,
                        'importance'        => $importance,
                        'details'            => $details,
                        'tech_notes'        => $tech_notes,
                        'date_approved'        => $date_approved,
                        'date_available'    => $date_available))) {
        return;
    }

    xarSessionSetVar('statusmsg', xarML('Feature Updated'));

    xarResponseRedirect(xarModURL('xproject', 'admin', 'display', array('projectid' => $projectid, 'mode' => "features")) ."#features");

    return true;
}

?>