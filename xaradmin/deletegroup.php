<?php
/**
 * Surveys delete a group
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys
 * @author Surveys module development team
 */
/*
 * Delete a group
 *
 * Long Description [OPTIONAL one or more lines]
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param string $confirm Confirm
 * @param int    $gid group ID
 *                      Identing long comments               [OPTIONAL A REQURIED]
 *
 * @return int  type and name returned                       [OPTIONAL A REQURIED]
 *
 * @throws      exceptionclass  [description]                [OPTIONAL A REQURIED]
 *
 * @access      public                                       [OPTIONAL A REQURIED]
 * @static                                                   [OPTIONAL]
 * @link       link to a reference                           [OPTIONAL]
 * @see        anothersample(), someotherlinke [reference to other function, class] [OPTIONAL]
 * @since      [Date of first inclusion long date format ]   [REQURIED]
 * @deprecated Deprecated [release version here]             [AS REQUIRED]
 */


function surveys_admin_deletegroup()
{
    if (!xarVarFetch('gid', 'int:1:', $gid)) return;
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    // Security check
    if (!xarSecurityCheck('EditSurvey', 1, 'Survey', 'All')) {
        // No privilege for editing survey structures.
        return false;
    }

    // Check for confirmation
    if (empty($confirm)) {
        // Get group information
        $group = xarModAPIFunc(
            'surveys', 'user', 'getgroups',
            array('gid' => $gid)
        );

        if (empty($group)) {
            $msg = xarML('The group to be deleted does not exist');
            xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
            return;
        }
        $group = reset($group['items']);

        $data = array('gid' => $gid, 'name' => $group['group_name']);
        $data['nolabel'] = xarML('No');
        $data['yeslabel'] = xarML('Yes');
        $data['authkey'] = xarSecGenAuthKey();

        // Return output
        return xarTplModule('surveys', 'admin', 'deletegroup', $data);
    }


    // Confirm Auth Key
    if (!xarSecConfirmAuthKey()) return;

    // Pass to API
    if (!xarModAPIFunc(
        'surveys', 'admin', 'deletegroup',
        array('gid' => $gid))
    ) return;

    xarResponseRedirect(xarModURL('surveys', 'admin', 'viewgroups', array()));

    return true;
}

?>
