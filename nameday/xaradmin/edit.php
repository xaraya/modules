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
 * Edit nameday
 */
function nameday_admin_edit()
{
    list($ndid, $did, $mid, $content, $ndlanguage) = 
    pnVarCleanFromInput('ndid', 'did', 'mid', 'content', 'ndlanguage');

    $output = new pnHTML();

    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', xarML('No authorisation to carry out operation'));
        pnRedirect(pnModURL('nameday', 'admin', 'display'));
        return true;
    }
    if(!pnModAPILoad('nameday', 'admin', 'edit')) {
    $output->Text(xarML('Unable to load API.'));
    return $output->GetOutput();
    }
    $namedaylist = pnModAPIFunc('nameday',
                              'admin',
                              'edit',
                              array('ndid' => $ndid, 'did' => $did, 'mid' => $mid,
                              'content' => $content, 'ndlanguage' => $ndlanguage));

    if($namedaylist == false) {
    $output->Text(xarML('No nameday Found.'));
    return $output->GetOutput();
    }
    $authid = pnSecGenAuthKey();

    $output->TableStart(xarML('Edit nameday'));

    foreach($namedaylist as $nameday1) {

    $output->FormStart(pnModURL('nameday', 'admin', 'update'));
        $output->FormHidden('authid', $authid);
    $output->FormHidden('ndid', $ndid);
        $output->LineBreak();

        $output->SetInputMode(_PNH_VERBATIMINPUT);

        $output->BoldText(''.xarML('Day').': ');
        for ($i=1;$i<=31;$i++) $daylist[] = array('id' => $i,'name' => $i);
        $output->FormSelectMultiple('did', $daylist, 0, 1, pnVarPrepForDisplay($did));

        $output->BoldText(''.xarML('Month').': ');
        for ($i=1;$i<=12;$i++) $monlist[] = array('id' => $i,'name' => $i);
        $output->FormSelectMultiple('mid', $monlist, 0, 1, pnVarPrepForDisplay($mid));

        $output->LineBreak(2);
        $output->BoldText(''.xarML('Language').': ');

        $langlist = xarLocaleGetList(array('lang'=>'en'));
// TODO: figure out how to get the list of *available* languages
/*
        $lang = languagelist();
        $handle = opendir('language');
        while ($f = readdir($handle))
        {
            if (is_dir("language/$f") && (!empty($lang[$f])))
            {
                $langlist[$f] = $lang[$f];
            }
        }
*/
        asort($langlist);
        $languages = array();
        $languages[] = array('id' => '', 'name' => xarML('All'));
        foreach ($langlist as $k => $v) {
            $languages[] = array('id' => $k, 'name' => $v);
        }
        $output->FormSelectMultiple('ndlanguage', $languages, 0, 1, $ndlanguage);
        $output->LineBreak(2);
        
        $output->BoldText(xarML('Names List'));
        $output->LineBreak();
        $output->FormTextArea('content',$nameday1['content'], 10, 60);
        $output->LineBreak();
        $output->FormSubmit(xarML('Save Modification?'));
        $output->FormEnd();
        $output->SetInputMode(_PNH_PARSEINPUT);
    }
    $output->TableEnd();

    return $output->GetOutput();
}
?>