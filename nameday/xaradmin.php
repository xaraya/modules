<?php // File: $Id$
// ----------------------------------------------------------------------
// POSTNUKE Content Management System
// Copyright (C) 2001 by the PostNuke Development Team.
// http://www.postnuke.com/
// ----------------------------------------------------------------------
// Based on:
// PHP-NUKE Web Portal System - http://phpnuke.org/
// Thatware - http://thatware.org/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// Original Author of file: Volodymyr Metenchuk (voll@yahoo.com, http://postnuke.solidno.ru)
// Purpose of file:
// ----------------------------------------------------------------------

function nameday_admin_main()
{
    if(!(pnSecAuthAction(0, 'nameday::', '::', ACCESS_EDIT))) {
	$output->Text(_NAMEDAY_EDITNOAUTH);
        return $output->GetOutput();
    }

    $output = new pnHTML();

    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->Text(nameday_adminmenu());
    $output->SetInputMode(_PNH_PARSEINPUT);

    return $output->GetOutput();
}

/**
 * Default
 */
function nameday_adminmenu()
{
    $output = new pnHTML();

    $authid = pnSecGenAuthKey();

    if(!(pnSecAuthAction(0, 'nameday::', '::', ACCESS_EDIT))) {
	$output->Text(_NAMEDAY_EDITNOAUTH);
        return $output->GetOutput();
    }

    $output->Title(_NAMEDAY_ADMIN);
    $output->Text(pnGetStatusMsg());
    $output->Linebreak(2);

    for ($i=1;$i<=31;$i++) $daylist[] = array('id' => $i,'name' => $i);
    for ($i=1;$i<=12;$i++) $monlist[] = array('id' => $i,'name' => $i);

    if(pnSecAuthAction(0, 'nameday::', '::', ACCESS_ADD)) {
        $output->TableStart(_NAMEDAY_NAMEDAYADD);
        $output->TableRowStart();
        $output->TableColStart();
        $output->FormStart(pnModURL('nameday', 'admin', 'add'));
        $output->FormHidden('authid', $authid);

        $output->BoldText(''._NAMEDAY_DAY.': ');
        $output->FormSelectMultiple('did', $daylist, 0, 1, '');

        $output->BoldText(''._NAMEDAY_MONTH.': ');
        $output->FormSelectMultiple('mid', $monlist, 0, 1, '');

        $output->LineBreak(2);
        $output->BoldText(''._NAMEDAY_LANGUAGE.': ');

        $langlist = pnLangGetList();
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
        $languages[] = array('id' => '', 'name' => _NAMEDAY_ALL);
        foreach ($langlist as $k => $v) {
            $languages[] = array('id' => $k, 'name' => $v);
        }
        $output->FormSelectMultiple('ndlanguage', $languages, 0, 1, '');
        $output->LineBreak(2);

        $output->BoldText(''._NAMEDAY_NAMESLIST.': ');
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

    $output->TableStart(_NAMEDAY_MAINT);
    $output->TableRowStart();
    $output->TableColStart();
    $output->FormStart(pnModURL('nameday', 'admin', 'editday'));
    $output->FormHidden('authid', $authid);

    $output->SetInputMode(_PNH_VERBATIMINPUT);

    $output->Text(""._NAMEDAY_DAY.": ");
    $output->FormSelectMultiple('did', $daylist, 0, 1, '');

    $output->Text(""._NAMEDAY_MONTH.": ");
    $output->FormSelectMultiple('mid', $monlist, 0, 1, '');

    $output->FormSubmit(_NAMEDAY_EDIT);
    $output->FormEnd();
    $output->TableColEnd();
    $output->TableRowEnd();
    $output->TableRowStart();
    $output->TableColStart();
    $output->URL(pnModURL('nameday', 'admin', 'display',
                           array('page' => 1, 'authid' => $authid)), _NAMEDAY_MODIFY);
    $output->TableColEnd();
    $output->TableRowEnd();
    $output->TableEnd();

    $output->TableRowStart();
    $output->TableColStart();
    $output->URL(pnModURL('nameday', 'admin', 'addlist',array()), _NAMEDAY_ADDLIST);
    $output->TableColEnd();
    $output->TableRowEnd();

    $output->LineBreak();

    return $output->GetOutput();
}

/**
 * Generate nameday listing for display
 */
function nameday_admin_display()
{
    $output = new pnHTML();

    $authid = pnSecGenAuthKey();

    if(!(pnSecAuthAction(0, 'nameday::', '::', ACCESS_READ))) {
	$output->Text(_NAMEDAY_NOAUTH);
        return $output->GetOutput();
    }
    $output->Text(_NAMEDAY_CURRENT);

    $columnHeaders = array(_NAMEDAY_DAY,_NAMEDAY_MONTH,
                           _NAMEDAY_NAMESLIST,_NAMEDAY_LANGUAGE,_NAMEDAY_ACTION);

    $output->TableStart('', $columnHeaders, 1);

    if(!pnModAPILoad('nameday', 'admin')) {
	$output->Text(_NAMEDAY_APILOADFAILED);
	return $output->GetOutput();
    }
    $namedaylist = pnModAPIFunc('nameday', 'admin', 'display');

    if($namedaylist == false) {
	$output->Text(_NAMEDAY_NONAMEDAYS);
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
            'authid' => $authid)),_NAMEDAY_EDITACTION);
        }
        if(pnSecAuthAction(0, 'nameday::', "$nameday1[content]::$nameday1[ndid]", ACCESS_DELETE)) {
            $actions[] = $output->URL(pnModURL('nameday', 'admin', 'delete', 
            array('ndid' => $nameday1['ndid'], 'did' => $nameday1['did'], 'mid' => $nameday1['mid'],
            'content' => $nameday1['content'], 'ndlanguage' => $nameday1['ndlanguage'],
            'authid' => $authid)),_NAMEDAY_DELETEACTION);
        }
        $output->SetOutputMode(_PNH_KEEPOUTPUT);
        $actions = join(' | ', $actions);
        if (empty($nameday1['ndlanguage'])) {
            $nameday1['ndlanguage'] = _NAMEDAY_ALL;
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

/**
 * Add new nameday to database.
 */
function nameday_admin_add()
{
    list($did, $mid, $content, $ndlanguage) = 
    pnVarCleanFromInput('did', 'mid', 'content', 'ndlanguage');

    if(!pnSecConfirmAuthKey()) {
	pnSessionSetVar('errormsg', _NAMEDAY_BADAUTHKEY);
	pnRedirect(pnModURL('nameday', 'admin', 'main'));
	return true;
    }
    if(!pnModAPILoad('nameday', 'admin')) {
	$output->Text(_NAMEDAY_APILOADFAILED);
	return $output->GetOutput();
    }

    if(pnModAPIFunc('nameday',
		    'admin',
                    'add',
		    array('did' => $did, 
                          'mid' => $mid, 
                          'content' => $content, 
                          'ndlanguage' => $ndlanguage))) {
	pnSessionSetVar('statusmsg', _NAMEDAY_ADDSUCCESS);
    }
    pnRedirect(pnModURL('nameday', 'admin', 'main'));

    return true;
}

/**
 * Add nameday list to database.
 */
function nameday_admin_addlist()
{
    list($confirm) = pnVarCleanFromInput('confirm');

    if (empty($confirm)) {

        $output = new pnHTML();

        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->Title(_NAMEDAY_LOADLISTFROMFILE);
	$output->SetInputMode(_PNH_PARSEINPUT);
        $output->ConfirmAction(_NAMEDAY_LOADLIST,
                               pnModURL('nameday','admin','addlist'),
                               _NAMEDAY_CANCEL,
                               pnModURL('nameday','admin','main'),
                               array());
        return $output->GetOutput();
    }
    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', _NAMEDAY_BADAUTHKEY);
        pnRedirect(pnModURL('nameday', 'admin', 'main'));
        return true;
    }

    if(!pnModAPILoad('nameday', 'admin')) {
	$output->Text(_NAMEDAY_APILOADFAILED);
	return $output->GetOutput();
    }

    if(pnModAPIFunc('nameday',
		    'admin',
                    'addlist',
		    array())) {
	pnSessionSetVar('statusmsg', _NAMEDAY_ADDSUCCESS);
    }
    pnRedirect(pnModURL('nameday', 'admin', 'main'));

    return true;
}

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
        $output->Title(_NAMEDAY_DEL);
	$output->SetInputMode(_PNH_PARSEINPUT);
        $output->ConfirmAction(_NAMEDAY_DEL,
                               pnModURL('nameday','admin','delete'),
                               _NAMEDAY_CANCEL,
                               pnModURL('nameday','admin','display'),
                               array('ndid' => $ndid, 'did' => $did, 'mid' => $mid,
                               'content' => $content,
                               'ndlanguage' => $ndlanguage));
        return $output->GetOutput();
    }
    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', _NAMEDAY_BADAUTHKEY);
        pnRedirect(pnModURL('nameday', 'admin', 'display'));
        return true;
    }
    if(!pnModAPILoad('nameday', 'admin')) {
	$output->Text(_NAMEDAY_APILOADFAILED);
	return $output->GetOutput();
    }
    if (pnModAPIFunc('nameday',
                     'admin',
                     'delete',
                     array('ndid' => $ndid, 'did' => $did, 'mid' => $mid,
                     'content' => $content, 'ndlanguage' => $ndlanguage))) {
        pnSessionSetVar('statusmsg', _NAMEDAY_DELETED);
    }
    pnRedirect(pnModURL('nameday', 'admin', 'main'));

    return true;
}

/**
 * Edit nameday
 */
function nameday_admin_edit()
{
    list($ndid, $did, $mid, $content, $ndlanguage) = 
    pnVarCleanFromInput('ndid', 'did', 'mid', 'content', 'ndlanguage');

    $output = new pnHTML();

    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', _NAMEDAY_BADAUTHKEY);
        pnRedirect(pnModURL('nameday', 'admin', 'display'));
        return true;
    }
    if(!pnModAPILoad('nameday', 'admin', 'edit')) {
	$output->Text(_NAMEDAY_APILOADFAILED);
	return $output->GetOutput();
    }
    $namedaylist = pnModAPIFunc('nameday',
                              'admin',
                              'edit',
                              array('ndid' => $ndid, 'did' => $did, 'mid' => $mid,
                              'content' => $content, 'ndlanguage' => $ndlanguage));

    if($namedaylist == false) {
	$output->Text(_NAMEDAY_NONAMEDAYS);
	return $output->GetOutput();
    }
    $authid = pnSecGenAuthKey();

    $output->TableStart(_NAMEDAY_EDIT);

    foreach($namedaylist as $nameday1) {

	$output->FormStart(pnModURL('nameday', 'admin', 'update'));
    	$output->FormHidden('authid', $authid);
	$output->FormHidden('ndid', $ndid);
    	$output->LineBreak();

        $output->SetInputMode(_PNH_VERBATIMINPUT);

        $output->BoldText(''._NAMEDAY_DAY.': ');
        for ($i=1;$i<=31;$i++) $daylist[] = array('id' => $i,'name' => $i);
        $output->FormSelectMultiple('did', $daylist, 0, 1, pnVarPrepForDisplay($did));

        $output->BoldText(''._NAMEDAY_MONTH.': ');
        for ($i=1;$i<=12;$i++) $monlist[] = array('id' => $i,'name' => $i);
        $output->FormSelectMultiple('mid', $monlist, 0, 1, pnVarPrepForDisplay($mid));

    	$output->LineBreak(2);
        $output->BoldText(''._NAMEDAY_LANGUAGE.': ');

        $langlist = pnLangGetList();
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
        $languages[] = array('id' => '', 'name' => _NAMEDAY_ALL);
        foreach ($langlist as $k => $v) {
            $languages[] = array('id' => $k, 'name' => $v);
        }
        $output->FormSelectMultiple('ndlanguage', $languages, 0, 1, $ndlanguage);
        $output->LineBreak(2);
        
        $output->BoldText(_NAMEDAY_NAMESLIST);
    	$output->LineBreak();
    	$output->FormTextArea('content',$nameday1['content'], 10, 60);
    	$output->LineBreak();
    	$output->FormSubmit(_NAMEDAY_SAVECHANGES);
    	$output->FormEnd();
        $output->SetInputMode(_PNH_PARSEINPUT);
    }
    $output->TableEnd();

    return $output->GetOutput();
}

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
            _NAMEDAY_SAVE, pnModURL('nameday','admin','update'),
            _NAMEDAY_CANCEL, pnModURL('nameday','admin','display'),
            array('ndid' => $ndid, 'did' => $did, 'mid' => $mid,
                  'content' => $content,'ndlanguage' => $ndlanguage));
        return $output->GetOutput();
    }
    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', _NAMEDAY_BADAUTHKEY);
        pnRedirect(pnModURL('nameday', 'admin', 'main'));
        return true;
    }
    if(!pnModAPILoad('nameday', 'admin')) {
	$output->Text(_NAMEDAY_APILOADFAILED);
	return $output->GetOutput();
    }
    if (pnModAPIFunc('nameday',
                     'admin',
                     'update',
                     array('ndid' => $ndid, 'did' => $did, 'mid' => $mid,
                     'content' => $content, 'ndlanguage' => $ndlanguage))) {

        pnSessionSetVar('statusmsg', _NAMEDAY_UPDATESUCCESS);
    }
    pnRedirect(pnModURL('nameday', 'admin', 'main'));

    return true;
}

function nameday_admin_editday()
{
    list($did,$mid) = pnVarCleanFromInput('did','mid');

    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', _NAMEDAY_BADAUTHKEY);
        pnRedirect(pnModURL('nameday', 'admin', 'display'));
        return true;
    }

    $output = new pnHTML();

    if(!(pnSecAuthAction(0, 'nameday::', '::', ACCESS_READ))) {
	$output->Text(_NAMEDAY_NOAUTH);
        return $output->GetOutput();
    }
    $output->Text(_NAMEDAY_CURRENT);

    $columnHeaders = array(_NAMEDAY_DAY,_NAMEDAY_MONTH,_NAMEDAY_NAMESLIST,
                           _NAMEDAY_LANGUAGE,_NAMEDAY_ACTION);

    $authid = pnSecGenAuthKey();

    $output->TableStart('', $columnHeaders, 1);

    if(!pnModAPILoad('nameday', 'admin')) {
	$output->Text(_NAMEDAY_APILOADFAILED);
	return $output->GetOutput();
    }

    $namedaylist = pnModAPIFunc('nameday',
                              'admin',
                              'editday',
                              array('did' => $did, 'mid' => $mid));
    
    if($namedaylist == false) {
	$output->Text(_NAMEDAY_NONAMEDAYS);
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
            'authid' => $authid)),_NAMEDAY_EDITACTION);
        }
        if(pnSecAuthAction(0, 'nameday::', "$nameday1[content]::$nameday1[ndid]", ACCESS_DELETE)) {
            $actions[] = $output->URL(pnModURL('nameday', 'admin', 'delete', 
            array('ndid' => $nameday1['ndid'], 'did' => $nameday1['did'], 'mid' => $nameday1['mid'],
            'content' => $nameday1['content'], 'ndlanguage' => $nameday1['ndlanguage'],
            'authid' => $authid)),_NAMEDAY_DELETEACTION);
        }
        $output->SetOutputMode(_PNH_KEEPOUTPUT);
        $actions = join(' | ', $actions);
        if (empty($nameday1['ndlanguage'])) {
            $nameday1['ndlanguage'] = _NAMEDAY_ALL;
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
