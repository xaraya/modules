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

    $contexts = $GLOBALS['MLS']->getContexts();
    $regexstring = "";
    $i=0;
    foreach($contexts as $context) {
        if ($i>0) $regexstring .= "|";
        $regexstring .= $context->getName();
        $i++;
    }
    $regexstring = 'regexp:/^(' . $regexstring . ')$/';
//    if (!xarVarFetch('subtype', 'regexp:/^(file|core|templates|templateincludes|templateblocks|blocks|admin|adminapi|user|userapi)$/', $subtype)) return;
    if (!xarVarFetch('subtype', $regexstring, $subtype)) return;
    if (!xarVarFetch('subname', 'str:1:', $subname)) return;

    $args = array();
    $args['subtype'] = $subtype;
    $args['subname'] = $subname;
    $entries = xarModAPIFunc('translations','admin','getcontextentries',$args);

    $dnType = xarSessionGetVar('translations_dnType');
    $dnName = xarSessionGetVar('translations_dnName');

    $tplData = $entries;
    $action = xarModURL('translations', 'admin', 'translate_update', array('subtype'=>$subtype, 'subname'=>$subname, 'numEntries'=>$entries['numEntries'], 'numKeyEntries'=>$entries['numKeyEntries']));
    $tplData['action'] = $action;

    $opbar = translations_create_opbar(TRANSLATE);
    $trabar = translations_create_trabar($subtype,$subname);
    $druidbar = translations_create_translate_druidbar(TRANSLATE);

    $tplData = array_merge($tplData, $opbar, $trabar, $druidbar);
    $tplData['dnType'] = translations__dnType2Name($dnType);

    xarTplAddStyleLink('translations', 'translate_subtype');
    return $tplData;
}

?>
