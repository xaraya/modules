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
 * Default
 */
function nameday_admin_main()
{
    if(!(pnSecAuthAction(0, 'nameday::', '::', ACCESS_EDIT))) {
    $output->Text(xarML('Not authorised to edit nameday'));
        return $output->GetOutput();
    }

    $output = new pnHTML();

    $authid = pnSecGenAuthKey();

    if(!(pnSecAuthAction(0, 'nameday::', '::', ACCESS_EDIT))) {
    $output->Text(xarML('Not authorised to edit nameday'));
        return $output->GetOutput();
    }

    $output->Title(xarML('Nameday Administration'));
    $output->Text(pnGetStatusMsg());
    $output->Linebreak(2);

    for ($i=1;$i<=31;$i++) $daylist[] = array('id' => $i,'name' => $i);
    for ($i=1;$i<=12;$i++) $monlist[] = array('id' => $i,'name' => $i);

    if(pnSecAuthAction(0, 'nameday::', '::', ACCESS_ADD)) {
        $output->TableStart(xarML('Add new nameday'));
        $output->TableRowStart();
        $output->TableColStart();
        $output->FormStart(pnModURL('nameday', 'admin', 'add'));
        $output->FormHidden('authid', $authid);

        $output->BoldText(''.xarML('Day').': ');
        $output->FormSelectMultiple('did', $daylist, 0, 1, '');

        $output->BoldText(''.xarML('Month').': ');
        $output->FormSelectMultiple('mid', $monlist, 0, 1, '');

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
        $output->FormSelectMultiple('ndlanguage', $languages, 0, 1, '');
        $output->LineBreak(2);

        $output->BoldText(''.xarML('Names List').': ');
        $output->SetInputMode(_PNH_PARSEINPUT);
        $output->LineBreak();
        $output->FormTextArea('content','', 10, 60);
        $output->LineBreak(2);
        $output->FormSubmit();
        $output->FormEnd();
        $output->TableColEnd();
        $output->TableRowEnd();
        $output->TableEnd();
        $output->LineBreak();
    }

    $output->TableStart(xarML('Nameday maintenance (Edit/Delete):'));
    $output->TableRowStart();
    $output->TableColStart();
    $output->FormStart(pnModURL('nameday', 'admin', 'editday'));
    $output->FormHidden('authid', $authid);

    $output->SetInputMode(_PNH_VERBATIMINPUT);

    $output->Text("".xarML('Day').": ");
    $output->FormSelectMultiple('did', $daylist, 0, 1, '');

    $output->Text("".xarML('Month').": ");
    $output->FormSelectMultiple('mid', $monlist, 0, 1, '');

    $output->FormSubmit(xarML('Edit nameday'));
    $output->FormEnd();
    $output->TableColEnd();
    $output->TableRowEnd();
    $output->TableRowStart();
    $output->TableColStart();
    $output->URL(pnModURL('nameday', 'admin', 'display',
                           array('page' => 1, 'authid' => $authid)), xarML('Modify nameday'));
    $output->TableColEnd();
    $output->TableRowEnd();
    $output->TableEnd();

    $output->TableRowStart();
    $output->TableColStart();
    $output->URL(pnModURL('nameday', 'admin', 'addlist',array()), xarML('Add namedays for this language'));
    $output->TableColEnd();
    $output->TableRowEnd();

    $output->LineBreak();

    return $output->GetOutput();
}
?>