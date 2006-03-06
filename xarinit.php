<?php
/**
 * Xaraya Google Search
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Google Search Module
 * @link http://xaraya.com/index.php/release/809.html
 * @author John Cox
 */
/**
 * Initialise the googlesearch module
 *
 * @return bool
 * @raise DATABASE_ERROR
 */
function googlesearch_init()
{
    // Set up module variables
    xarModSetVar('googlesearch', 'license-key', 'Enter your license key');
    xarModSetVar('googlesearch', 'maxQueries', '1000');
    xarModSetVar('googlesearch', 'queryCount', '0');
    xarModSetVar('googlesearch', 'queryCountDay', mktime(0,0,0, date('m'), date('d'), date('Y')));
    xarModSetVar('googlesearch', 'cacheRetrievedPages', serialize(array()));
    xarModSetVar('googlesearch', 'cachePageIndex', '1');
    xarModSetVar('googlesearch', 'cacheRemoteURL', 'http://');
    xarModSetVar('googlesearch', 'cachePageFilter', '1');
    xarModSetVar('googlesearch', 'cachePageDataFilter', '0');

    @mkdir('var/cache/google');
    touch('var/cache/google/CACHEKEYS');

    // Register Hooks
    if (!xarModRegisterHook('item', 'search', 'GUI', 'googlesearch', 'user', 'search')) return false;

    // Register Masks
    xarRegisterMask('Overviewgooglesearch','All','googlesearch','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('Admingooglesearch','All','googlesearch','All','All','ACCESS_ADMIN');
    return true;
}


/**
 * upgrade the googlesearch module from an old version
 * This function can be called multiple times
 *
 * @returns bool
 */
function googlesearch_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
        case '1.0.0':
          @mkdir('var/cache/google');
          touch('var/cache/google/CACHEKEYS');
          xarModSetVar('googlesearch', 'maxQueries', '1000');
          xarModSetVar('googlesearch', 'queryCount', '0');
          xarModSetVar('googlesearch', 'queryCountDay', mktime(0,0,0, date('m'), date('d'), date('Y')));
          xarModSetVar('googlesearch', 'cacheRetrievedPages', serialize(array()));
          xarModSetVar('googlesearch', 'cachePageIndex', '1');
          xarModSetVar('googlesearch', 'cacheRemoteURL', 'http://');
          xarModSetVar('googlesearch', 'cachePageFilter', '1');
          xarModSetVar('googlesearch', 'cachePageDataFilter', '0');
          break;

        case '1.1.0':
          // code to upgrade from 1.1.0 goes here
          break;
    }
    // Update successful
    return true;
}

/**
 * Delete the googlesearch module
 *
 * @returns bool
 */
function googlesearch_delete()
{
    // Delete Vars
    xarModDelVar('googlesearch', 'license-key');
    xarModDelVar('googlesearch', 'maxQueries');
    xarModDelVar('googlesearch', 'queryCount');
    xarModDelVar('googlesearch', 'queryCountDay');
    xarModDelVar('googlesearch', 'cacheRetrievedPages');
    xarModDelVar('googlesearch', 'cachePageIndex');
    xarModDelVar('googlesearch', 'cacheRemoteURL');
    xarModDelVar('googlesearch', 'cachePageFilter');
    xarModDelVar('googlesearch', 'cachePageDataFilter');


    if (!xarModUnRegisterHook('item', 'search', 'GUI', 'googlesearch', 'user', 'search')) return false;

    // Remove Masks and Instances
    xarRemoveMasks('googlesearch');
    xarRemoveInstances('googlesearch');
    return true;
}
?>