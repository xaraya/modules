<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Marco Canini
// Purpose of file:  Initialisation functions for translations
// ----------------------------------------------------------------------

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

    return true;
}

/**
 * upgrade the translations module from an old version
 */
function translations_upgrade($oldversion)
{
    switch($oldversion){
        case '0.01':
            break;
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


    // Remove Masks and Instances
    xarRemoveMasks('translations');
    xarRemoveInstances('translations');

    return true;
}

?>