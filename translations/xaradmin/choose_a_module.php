<?php

/**
 * File: $Id$
 *
 * Choose a module page generation
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_admin_choose_a_module()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if (!($modlist = xarModAPIFunc('modules', 'admin', 'GetList', array('filter' => array('State' => XARMOD_STATE_ANY))))) return;

    $tplData = translations_create_choose_a_module_druidbar(CHOOSE);
    $tplData['modlist'] = $modlist;
    return $tplData;
}

?>