<?php

/**
 * File: $Id$
 *
 * Choose a theme page generation
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_admin_choose_a_theme()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if (!($themelist = xarModAPIFunc('themes','admin','GetThemeList',array('filter' => array('State' => XARTHEME_STATE_ANY))))) return;

    $tplData = translations_create_choose_a_theme_druidbar(CHOOSE);
    $tplData['themelist'] = $themelist;
    return $tplData;
}

?>