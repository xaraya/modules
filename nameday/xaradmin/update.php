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
 * Called from nameday_admin_edit.
 * Confirm update of nameday first.
 * On confirmation, load API and
 * update.
 */
function nameday_admin_update()
{
    list($ndid, $did, $mid, $content, $ndlanguage, $confirm) = 
    pnVarCleanFromInput('ndid', 'did', 'mid', 'content', 'ndlanguage', 'confirm');

    if (empty($confirm)) {

        $output = new pnHTML();

        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->Title('Update nameday');
    $output->SetInputMode(_PNH_PARSEINPUT);
        $output->ConfirmAction(
            xarML('Save'), pnModURL('nameday','admin','update'),
            xarML('Cancel'), pnModURL('nameday','admin','display'),
            array('ndid' => $ndid, 'did' => $did, 'mid' => $mid,
                  'content' => $content,'ndlanguage' => $ndlanguage));
        return $output->GetOutput();
    }
    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', xarML('No authorisation to carry out operation'));
        pnRedirect(pnModURL('nameday', 'admin', 'main'));
        return true;
    }
    if(!pnModAPILoad('nameday', 'admin')) {
    $output->Text(xarML('Unable to load API.'));
    return $output->GetOutput();
    }
    if (pnModAPIFunc('nameday',
                     'admin',
                     'update',
                     array('ndid' => $ndid, 'did' => $did, 'mid' => $mid,
                     'content' => $content, 'ndlanguage' => $ndlanguage))) {

        pnSessionSetVar('statusmsg', xarML('Nameday Successfully Updated.'));
    }
    pnRedirect(pnModURL('nameday', 'admin', 'main'));

    return true;
}
?>