<?php
/**
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

    if (!xarVarFetch('dnType','int',$dnType)) return;
    if (!xarVarFetch('dnName','str:1:',$dnName)) return;
    if (!xarVarFetch('extid','int',$extid)) return;

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
    $args['dntype'] = $dnType;
    $args['dnname'] = $dnName;
    $args['subtype'] = $subtype;
    $args['subname'] = $subname;
    $entries = xarMod::apiFunc('translations','admin','getcontextentries',$args);

    $args = array();
    $args['dntype'] = $dnType;
    $args['dnname'] = $dnName;
    $args['subtype'] = 'modules:';
    $args['subname'] = 'fuzzy';
    $fuzzyEntries = xarMod::apiFunc('translations','admin','getcontextentries',$args);

    $entries['fuzzyEntries'] = $fuzzyEntries['entries'];
    $entries['fuzzyNumEntries'] = $fuzzyEntries['numEntries'];
    $entries['fuzzyNumEmptyEntries'] = $fuzzyEntries['numEmptyEntries'];
    $entries['fuzzyKeyEntries'] = $fuzzyEntries['keyEntries'];
    $entries['fuzzyNumKeyEntries'] = $fuzzyEntries['numKeyEntries'];
    $entries['fuzzyNumEmptyKeyEntries'] = $fuzzyEntries['numEmptyKeyEntries'];

    $tplData = $entries;
    $action = xarModURL('translations', 'admin', 'translate_update', array('subtype'=>$subtype, 'subname'=>$subname, 'numEntries'=>$entries['numEntries'], 'numKeyEntries'=>$entries['numKeyEntries'], 'numEmptyEntries'=>$entries['numEmptyEntries'], 'numEmptyKeyEntries'=>$entries['numEmptyKeyEntries']));
    $tplData['action'] = $action;

    $opbar = translations_create_opbar(TRANSLATE, $dnType, $dnName, $extid);
    $trabar = translations_create_trabar($dnType, $dnName, $extid, $subtype,$subname);
    $druidbar = translations_create_druidbar(TRAN, $dnType, $dnName, $extid);
    $tplData = array_merge($tplData, $opbar, $trabar, $druidbar);
    $tplData['dnType'] = $dnType;

    if ($dnType == XARMLS_DNTYPE_CORE) $dnTypeText = 'core';
    elseif ($dnType == XARMLS_DNTYPE_THEME) $dnTypeText = 'theme';
    elseif ($dnType == XARMLS_DNTYPE_MODULE) $dnTypeText = 'module';
    else $dnTypeText = '';
    $tplData['dnTypeText'] = $dnTypeText;

    $tplData['dnName'] = $dnName;
    $tplData['extid'] = $extid;

    return $tplData;
}

?>