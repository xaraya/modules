<?php

/**
 * File: $Id$
 *
 * Start translation process
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/


/**
 * Entry point for beginning a translation
 *
 * A somewhat longer description of the function which may be 
 * multiple lines, can contain examples.
 *
 * @access  public
 * @return  array template data
*/
function translations_admin_start()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    //    $tplData['locales'] = xarLocaleGetList(array('charset'=>'utf-8'));
    $tplData['locales'] = xarLocaleGetList(array());
    $tplData['working_locale'] = translations_working_locale();

    return $tplData;
}