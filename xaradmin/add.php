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
 * Add new nameday to database.
 */
function nameday_admin_add()
{
    list($did, $mid, $content, $ndlanguage) = 
    pnVarCleanFromInput('did', 'mid', 'content', 'ndlanguage');

    if(!pnSecConfirmAuthKey()) {
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
                    'add',
            array('did' => $did, 
                          'mid' => $mid, 
                          'content' => $content, 
                          'ndlanguage' => $ndlanguage))) {
    pnSessionSetVar('statusmsg', xarML('Nameday Added Successfully.'));
    }
    pnRedirect(pnModURL('nameday', 'admin', 'main'));

    return true;
}
?>