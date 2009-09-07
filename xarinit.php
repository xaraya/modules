<?php
/**
 * Initialisation functions for translations
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
 * initialise the translations module
 */
function translations_init()
{
    xarModVars::set('translations', 'work_backend_type', 'xml');
    xarModVars::set('translations', 'release_backend_type', 'php');
    xarModVars::set('translations', 'archiver_path', '/bin/tar');
    xarModVars::set('translations', 'archiver_flags', 'czf %f %d');

    xarModVars::set('translations', 'showcontext', 0);
    xarModVars::set('translations', 'maxreferences', 5);
    xarModVars::set('translations', 'maxcodelines', 5);

    xarRegisterMask('ReadTranslations', 'All', 'translations', 'All', 'All', 'ACCESS_READ');
    xarRegisterMask('AdminTranslations', 'All', 'translations', 'All', 'All', 'ACCESS_ADMIN');

    return true;
}

/**
 * upgrade the translations module from an old version
 */
function translations_upgrade($oldversion)
{
    switch($oldversion){
        case '2.0.0':
    }
    return true;
}

/**
 * delete the translations module
 */
function translations_delete()
{
    xarModVars::delete_all('translations');

    // Remove Masks and Instances
    xarRemoveMasks('translations');
    xarRemoveInstances('translations');

    return true;
}

?>