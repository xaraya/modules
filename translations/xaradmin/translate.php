<?php

/**
 * File: $Id$
 *
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

    $tran_type = xarSessionGetVar('translations_dnType');
    $opbar = translations_create_opbar(TRANSLATE);
    $trabar = translations_create_trabar('', '');
    $druidbar = translations_create_translate_druidbar(TRAN, $tran_type);
    $tplData = array_merge($opbar, $trabar, $druidbar);

    return $tplData;
}

?>