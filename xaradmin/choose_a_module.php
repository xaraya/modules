<?php
/**
 * Choose a module page generation
 *
 * @package modules
 * @copyright (C) copyright-placeholder
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

    $installed = xarModAPIFunc('modules', 'admin', 'getlist', array('filter' => array('State' => XARMOD_STATE_INSTALLED)));
    if (!isset($installed)) return;
    $uninstalled = xarModAPIFunc('modules', 'admin', 'getlist', array('filter' => array('State' => XARMOD_STATE_UNINITIALISED)));
    if (!isset($uninstalled)) return;

    $modlist1 = array();
    foreach($uninstalled as $term) $modlist1[$term['name']] = $term;
    $modlist2 = array();
    foreach($installed as $term) $modlist2[$term['name']] = $term;
    $modlist = array_merge($modlist1,$modlist2);
    ksort($modlist);

    $tplData = translations_create_druidbar(CHOOSE, XARMLS_DNTYPE_MODULE, '', 0);
    $tplData['modlist'] = $modlist;
    $tplData['dnType'] = XARMLS_DNTYPE_MODULE;
    return $tplData;
}

?>