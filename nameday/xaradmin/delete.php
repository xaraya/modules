<?php
/**
 * File: $Id$
 *
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage nameday
 * @author Volodymyr Metenchuk (http://www.xaraya.ru)
 */

/**
 * Delete selected nameday
 */
function nameday_admin_delete()
{
    list($ndid, $did, $mid, $content, $ndlanguage, $confirm) = 
    pnVarCleanFromInput('ndid', 'did', 'mid', 'content', 'ndlanguage', 'confirm');

    if (empty($confirm)) {

        $output = new pnHTML();

        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->Title(xarML('Delete This Nameday?'));
    $output->SetInputMode(_PNH_PARSEINPUT);
        $output->ConfirmAction(xarML('Delete This Nameday?'),
                               pnModURL('nameday','admin','delete'),
                               xarML('Cancel'),
                               pnModURL('nameday','admin','display'),
                               array('ndid' => $ndid, 'did' => $did, 'mid' => $mid,
                               'content' => $content,
                               'ndlanguage' => $ndlanguage));
        return $output->GetOutput();
    }
    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', xarML('No authorisation to carry out operation'));
        pnRedirect(pnModURL('nameday', 'admin', 'display'));
        return true;
    }
    if(!pnModAPILoad('nameday', 'admin')) {
    $output->Text(xarML('Unable to load API.'));
    return $output->GetOutput();
    }
    if (pnModAPIFunc('nameday',
                     'admin',
                     'delete',
                     array('ndid' => $ndid, 'did' => $did, 'mid' => $mid,
                     'content' => $content, 'ndlanguage' => $ndlanguage))) {
        pnSessionSetVar('statusmsg', xarML('Nameday Deleted.'));
    }
    pnRedirect(pnModURL('nameday', 'admin', 'main'));

    return true;
}
?>