<?php
/**
 * JpGraph Module - initialization functions
 *
 * @package modules
 * @copyright (C) 2006-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage JpGraph Module
 * @link http://xaraya.com/index.php/release/819.html
 * @author JpGraph Module Development Team
 */
/**
 * Initialise the module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance. It holds all the installation routines and sets the variables used
 * by this module. This function is the place to create you database structure and define
 * the privileges your module uses.
 *
 * @author JpGraph Module Development Team
 * @param none
 * @return bool true on success of installation
 */
function jpgraph_init()
{
    // Test for the present extension
    if(!extension_loaded('gd')) {
        return false;
    }

    // Create the var dir
   // mkdir('/var/jpgraph_cache',0777);

    /* Set up the version number for jpgraph
     */
    xarModSetVar('jpgraph', 'jpgraphversion', '2.1.4');
    xarModSetVar('jpgraph', 'cachedirectory', '/var/jpgraph/jpgraph_cache/');

    xarModSetVar('jpgraph', 'csimcachedirectory', '/var/jpgraph/csimcache/');
    xarModSetVar('jpgraph', 'csimcachehttpdirectory', '/var/jpgraph/csimcache/');

    xarModSetVar('jpgraph', 'cachetimeout', 0);

    xarModSetVar('jpgraph', 'usecache', 1);
    xarModSetVar('jpgraph', 'readcache', 1);
    xarModSetVar('jpgraph', 'window_width', 800);
    xarModSetVar('jpgraph', 'window_height', 600);



    xarModSetVar('jpgraph', 'graph_type',   'line');
    xarModSetVar('jpgraph', 'graph_title',  'JpGraph');
    xarModSetVar('jpgraph', 'graph_xtitle', 'X title');
    xarModSetVar('jpgraph', 'graph_ytitle', 'Y title');
    xarModSetVar('jpgraph', 'graph_shadow',  false);
    xarModSetVar('jpgraph', 'margin_left',   60);
    xarModSetVar('jpgraph', 'margin_right',  20);
    xarModSetVar('jpgraph', 'margin_top',    30);
    xarModSetVar('jpgraph', 'margin_bottom', 50);

    xarModSetVar('jpgraph', 'plot_legend',  'Your legend');
    xarModSetVar('jpgraph', 'plot_color',   'blue');
    xarModSetVar('jpgraph', 'plot_weight',  1);
    xarModSetVar('jpgraph', 'plot_style',   'solid');
    xarModSetVar('jpgraph', 'plot_stepstyle', false);

    xarModSetVar('jpgraph', 'value_show',   false);
    xarModSetVar('jpgraph', 'value_color',  'red');
    xarModSetVar('jpgraph', 'value_format', '%0.0f');

    xarModSetVar('jpgraph', 'xaxis_color',  'black');
    xarModSetVar('jpgraph', 'xaxis_weight', 2);
    xarModSetVar('jpgraph', 'yaxis_color',  'black');
    xarModSetVar('jpgraph', 'yaxis_weight', 2);

    xarModSetVar('jpgraph', 'mark_type',    'MARK_UTRIANGLE');

    xarModSetVar('jpgraph', 'itemsperpage', 20);
    xarModSetVar('jpgraph', 'SupportShortURLs', 0);

    /*
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     * These masks are used in the module for the security checks
     */
    xarRegisterMask('ViewJpGraph',   'All', 'jpgraph', 'Item', 'All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadJpGraph',   'All', 'jpgraph', 'Item', 'All', 'ACCESS_READ');
    xarRegisterMask('EditJpGraph',   'All', 'jpgraph', 'Item', 'All', 'ACCESS_EDIT');
    xarRegisterMask('AddJpGraph',    'All', 'jpgraph', 'Item', 'All', 'ACCESS_ADD');
    xarRegisterMask('DeleteJpGraph', 'All', 'jpgraph', 'Item', 'All', 'ACCESS_DELETE');
    xarRegisterMask('AdminJpGraph',  'All', 'jpgraph', 'Item', 'All', 'ACCESS_ADMIN');

    /* This init function brings our module to version 1.0, run the upgrades for the rest of the initialisation */
    return jpgraph_upgrade('0.3.1');
}

/**
 * Upgrade the module from an old version
 *
 * This function can be called multiple times. It holds all the routines for each version
 * of the module that are necessary to upgrade to a new version. It is very important to keep the
 * initialisation and the upgrade compatible with eachother.
 *
 * @author JpGraph Module Development Team
 * @param string oldversion. This function takes the old version that is currently stored in the module db
 * @return bool true on succes of upgrade
 * @throws mixed This function can throw all sorts of errors, depending on the functions present
                 Currently it can raise database errors.
 */
function jpgraph_upgrade($oldversion)
{
    /* Upgrade dependent on old version number */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    switch ($oldversion) {
        case '0.1.0':
            xarModSetVar('jpgraph', 'ttfdirectory', 'modules/jpgraph/xarinclude/ttf/');
        case '0.2.0':
            xarModSetVar('jpgraph', 'graphic_error', true);
            xarModSetVar('jpgraph', 'jpgraphversion', '2.1.4');
        case '0.3.0':
            break;
    }
    /* Update successful */
    return true;
}

/**
 * Delete the module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * @author JpGraph Module Development Team
 * @param none
 * @return bool true on succes of deletion
 */
function jpgraph_delete()
{
    /* Remove any module aliases before deleting module vars
     * This Assumes one module alias in this case
     */
    $aliasname = xarModGetVar('jpgraph','aliasname');
    $isalias = xarModGetAlias($aliasname);
    if (isset($isalias) && ($isalias =='jpgraph')){
        xarModDelAlias($aliasname,'jpgraph');
    }

    /* Delete any module variables */
    xarModDelAllVars('jpgraph');

    /* Remove Masks and Instances
     * These functions remove all the registered masks and instances of a module
     * from the database. This is not strictly necessary, but it's good housekeeping.
     */
    xarRemoveMasks('jpgraph');

    /* Category deletion?
     *
     * Categories can be used in more than one module.
     * The categories originally created for this module could also have been used
     * for other modules. If we delete the categories then we must be sure that
     * no other modules are currently using them.
     */

    /* Deletion successful*/
    return true;
}
?>
