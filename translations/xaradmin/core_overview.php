<?php

/**
 * File: $Id$
 *
 * Core overview page generation
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_admin_core_overview()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    xarSessionSetVar('translations_dnName', 'xaraya');

    $tplData = translations_create_opbar(OVERVIEW);
    $tplData['verNum'] = XARCORE_VERSION_NUM;
    $tplData['verId'] = XARCORE_VERSION_ID;
    $tplData['verSub'] = XARCORE_VERSION_SUB;
    return $tplData;
}

?>