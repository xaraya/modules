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
 * Add nameday list to database.
 */
function nameday_admin_addlist()
{
    list($confirm) = pnVarCleanFromInput('confirm');

    if (empty($confirm)) {

        $output = new pnHTML();

        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->Title(xarML('Load names list from file'));
    $output->SetInputMode(_PNH_PARSEINPUT);
        $output->ConfirmAction(xarML('Load'),
                               pnModURL('nameday','admin','addlist'),
                               xarML('Cancel'),
                               pnModURL('nameday','admin','main'),
                               array());
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

    if(pnModAPIFunc('nameday',
            'admin',
                    'addlist',
            array())) {
    pnSessionSetVar('statusmsg', xarML('Nameday Added Successfully.'));
    }
    pnRedirect(pnModURL('nameday', 'admin', 'main'));

    return true;
}
?>