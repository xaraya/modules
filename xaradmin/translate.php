<?php
/**
 * Translations page generation
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_admin_translate()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if (!xarVarFetch('dnType','int',$dnType)) return;
    if (!xarVarFetch('dnName','str:1:',$dnName)) return;
    if (!xarVarFetch('extid','int',$extid)) return;

    $opbar = translations_create_opbar(TRANSLATE, $dnType, $dnName, $extid);
    $trabar = translations_create_trabar($dnType, $dnName, $extid, '', '');
    $druidbar = translations_create_druidbar(TRAN, $dnType, $dnName, $extid);
    $tplData = array_merge($opbar, $trabar, $druidbar);

    $tplData['dnType'] = $dnType;
    $tplData['dnName'] = $dnName;
    $tplData['extid'] = $extid;

    return $tplData;
}

?>