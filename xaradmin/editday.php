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

function nameday_admin_editday()
{
    list($did,$mid) = pnVarCleanFromInput('did','mid');

    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', xarML('No authorisation to carry out operation'));
        pnRedirect(pnModURL('nameday', 'admin', 'display'));
        return true;
    }

    $output = new pnHTML();

    if(!(pnSecAuthAction(0, 'nameday::', '::', ACCESS_READ))) {
    $output->Text(xarML('Not authorised to access nameday'));
        return $output->GetOutput();
    }
    $output->Text(xarML('Current nameday'));

    $columnHeaders = array(xarML('Day'),xarML('Month'),xarML('Names List'),
                           xarML('Language'),xarML('Action'));

    $authid = pnSecGenAuthKey();

    $output->TableStart('', $columnHeaders, 1);

    if(!pnModAPILoad('nameday', 'admin')) {
    $output->Text(xarML('Unable to load API.'));
    return $output->GetOutput();
    }

    $namedaylist = pnModAPIFunc('nameday',
                              'admin',
                              'editday',
                              array('did' => $did, 'mid' => $mid));
    
    if($namedaylist == false) {
    $output->Text(xarML('No nameday Found.'));
        // if no nameday found, end the table or the footer gets pulled up the page.
    $output->TableEnd();
    return $output->GetOutput();
    }

    foreach($namedaylist as $nameday1) {
    $actions = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);

        if(pnSecAuthAction(0, 'nameday::', "$nameday1[content]::$nameday1[ndid]", ACCESS_EDIT)) {
            $actions[] = $output->URL(pnModURL('nameday', 'admin', 'edit', 
            array('ndid' => $nameday1['ndid'], 'did' => $nameday1['did'], 'mid' => $nameday1['mid'],
            'content' => $nameday1['content'], 'ndlanguage' => $nameday1['ndlanguage'],
            'authid' => $authid)),xarML('Edit'));
        }
        if(pnSecAuthAction(0, 'nameday::', "$nameday1[content]::$nameday1[ndid]", ACCESS_DELETE)) {
            $actions[] = $output->URL(pnModURL('nameday', 'admin', 'delete', 
            array('ndid' => $nameday1['ndid'], 'did' => $nameday1['did'], 'mid' => $nameday1['mid'],
            'content' => $nameday1['content'], 'ndlanguage' => $nameday1['ndlanguage'],
            'authid' => $authid)),xarML('Delete'));
        }
        $output->SetOutputMode(_PNH_KEEPOUTPUT);
        $actions = join(' | ', $actions);
        if (empty($nameday1['ndlanguage'])) {
            $nameday1['ndlanguage'] = xarML('All');
        }
        $row = array(pnVarPrepForDisplay($nameday1['did']),
            pnVarPrepForDisplay($nameday1['mid']),
            pnVarPrepForDisplay(nl2br($nameday1['content'])),
            pnVarPrepForDisplay($nameday1['ndlanguage']),
            $actions);
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->TableAddRow($row, 'CENTER');
        $output->SetInputMode(_PNH_PARSEINPUT);
    }

    $output->TableEnd();
    return $output->GetOutput();
}
?>