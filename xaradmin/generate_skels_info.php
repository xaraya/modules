<?php
/**
 * Generate skels information
 *
 * @package modules
 * @copyright (C) 2003-2009 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_admin_generate_skels_info()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if (!xarVarFetch('dnType','int',$dnType)) return;
    if (!xarVarFetch('dnName','str:1:',$dnName)) return;
    if (!xarVarFetch('extid','int',$extid)) return;

    $druidbar = translations_create_druidbar(GENSKELS, $dnType, $dnName, $extid);
    $opbar = translations_create_opbar(GEN_SKELS, $dnType, $dnName, $extid);
    $tplData = array_merge($druidbar, $opbar);
    $tplData['dnType'] = $dnType;

    if ($dnType == XARMLS_DNTYPE_CORE) $dnTypeText = 'core';
    elseif ($dnType == XARMLS_DNTYPE_THEME) $dnTypeText = 'theme';
    elseif ($dnType == XARMLS_DNTYPE_MODULE) $dnTypeText = 'module';
    else $dnTypeText = '';
    $tplData['dnTypeText'] = $dnTypeText;

    $tplData['dnName'] = $dnName;
    $tplData['extid'] = $extid;
    $tplData['confirmskelsgen'] = xarModGetVar('translations', 'confirmskelsgen');
    $tplData['authid']          = xarSecGenAuthKey();
    $tplData['redirecturl']     = xarModURL('translations', 'admin','generate_skels_result',
                                    array('dnType' => $dnType,
                                          'dnName' => $dnName,
                                          'extid'  => $extid,
                                          'authid' => $tplData['authid']));
    return $tplData;
}

?>