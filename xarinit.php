<?php
/**
 * Initialisation functions for translations
 *
 * @package modules
 * @copyright (C) 2003-2009 by the Xaraya Development Team.
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
    xarModSetVar('translations', 'work_backend_type', 'xml');
    xarModSetVar('translations', 'release_backend_type', 'php');
    xarModSetVar('translations', 'archiver_path', '/bin/tar');
    xarModSetVar('translations', 'archiver_flags', 'czf %f %d');

    xarRegisterMask('AdminTranslations', 'All', 'translations', 'All', 'All', 'ACCESS_ADMIN');

    // Remaining init steps are done in _upgrade() to avoid duplicate code.
    return translations_upgrade('0.1.0');
}

/**
 * upgrade the translations module from an old version
 */
function translations_upgrade($oldversion)
{
    switch($oldversion){
        case '0.1.0':
            xarModSetVar('translations', 'showcontext', 0);
            xarModSetVar('translations', 'maxcodelines', 5);
        case '0.1.1':
            xarRegisterMask('ReadTranslations', 'All', 'translations', 'All', 'All', 'ACCESS_READ');
        case '0.1.2':
            xarModSetVar('translations', 'maxreferences', 0);
        case '0.1.3':
            // Template data and templates admin-menu, admin-nav change
            xarModSetVar('translations', 'confirmskelsgen', 1);
        case '0.2.0':
    }
    return true;
}

/**
 * delete the translations module
 */
function translations_delete()
{
    xarModDelVar('translations', 'work_backend_type');
    xarModDelVar('translations', 'release_backend_type');
    xarModDelVar('translations', 'archiver_path');
    xarModDelVar('translations', 'archiver_flags');

    xarModDelVar('translations', 'showcontext');
    xarModDelVar('translations', 'maxreferences');
    xarModDelVar('translations', 'maxcodelines');
    xarModDelVar('translations', 'confirmskelsgen');

    xarRemoveMasks('translations');
    xarRemoveInstances('translations');

    return true;
}

?>