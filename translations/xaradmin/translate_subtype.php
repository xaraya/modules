<?php

/**
 * File: $Id$
 *
 * Translate on screen for subtypes of a module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_admin_translate_subtype()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    // FIXME voll context validation
    //$contexts = Load all contexts types;
    //$regexstring = "";
    //$i=0;
    //foreach($contexts as $context) {
    //    if ($i>0) $regexstring .= "|";
    //    $regexstring .= context_get_Name();
    //    $i++;
    //}
    //$regexstring = 'regexp:/^(' . $regexstring . ')$/';
    //if (!xarVarFetch('subtype', $regexstring, $subtype)) return;

    // FIXME voll do we use subtype,subname really?
    if (!xarVarFetch('defaultcontext', 'str:1:', $defaultcontext)) {
        if (!xarVarFetch('subtype', 'str:1:', $subtype)) return;
        if (!xarVarFetch('subname', 'str:1:', $subname)) return;
    } else {
        list($subtype1,$subtype2,$subname) = explode(':',$defaultcontext);
        $subtype = $subtype1.':'.$subtype2;
    }

    $args = array();
    $args['subtype'] = $subtype;
    $args['subname'] = $subname;
    $entries = xarModAPIFunc('translations','admin','getcontextentries',$args);

    $dnType = xarSessionGetVar('translations_dnType');
    $dnName = xarSessionGetVar('translations_dnName');

    $tplData = $entries;
    $action = xarModURL('translations', 'admin', 'translate_update', array('subtype'=>$subtype, 'subname'=>$subname, 'numEntries'=>$entries['numEntries'], 'numKeyEntries'=>$entries['numKeyEntries'], 'numEmptyEntries'=>$entries['numEmptyEntries'], 'numEmptyKeyEntries'=>$entries['numEmptyKeyEntries']));
    $tplData['action'] = $action;

    $opbar = translations_create_opbar(TRANSLATE);
    $trabar = translations_create_trabar($subtype,$subname);
    $druidbar = translations_create_translate_druidbar(TRAN, $dnType);
    $tplData = array_merge($tplData, $opbar, $trabar, $druidbar);
    $tplData['dnType'] = translations__dnType2Name($dnType);

    return $tplData;
}

?>