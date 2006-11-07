<?php
/**
 * PHPlot Module - initialization functions
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage PHPlot Module
 * @link http://xaraya.com/index.php/release/818.html
 * @author PHPlot Module Development Team
 */
/**
 * Initialise the module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance. It holds all the installation routines and sets the variables used
 * by this module. This function is the place to create you database structure and define
 * the privileges your module uses.
 *
 * @author PHPlot Module Development Team
 * @param none
 * @return bool true on success of installation
 */
function phplot_init()
{
    /* Set up an initial value for a module variable. Note that all module
     * variables should be initialised with some value in this way rather
     * than just left blank, this helps the user-side code and means that
     * there doesn't need to be a check to see if the variable is set in
     * the rest of the code as it always will be
     */
    xarModSetVar('phplot', 'bold', 0);
    xarModSetVar('phplot', 'itemsperpage', 10);
    /* If your module supports short URLs, the website administrator should
     * be able to turn it on or off in your module administration.
     * Use the standard module var name for short url support.
     */
    xarModSetVar('phplot', 'SupportShortURLs', 0);

    xarRegisterMask('ViewPHPlot',   'All', 'phplot', 'Item', 'All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadPHPlot',   'All', 'phplot', 'Item', 'All', 'ACCESS_READ');
    xarRegisterMask('EditPHPlot',   'All', 'phplot', 'Item', 'All', 'ACCESS_EDIT');
    xarRegisterMask('AddPHPlot',    'All', 'phplot', 'Item', 'All', 'ACCESS_ADD');
    xarRegisterMask('DeletePHPlot', 'All', 'phplot', 'Item', 'All', 'ACCESS_DELETE');
    xarRegisterMask('AdminPHPlot',  'All', 'phplot', 'Item', 'All', 'ACCESS_ADMIN');

    /* This init function brings our module to version 1.0, run the upgrades for the rest of the initialisation */
    return phplot_upgrade('0.1.0');
}

/**
 * Upgrade the module from an old version
 *
 * This function can be called multiple times. It holds all the routines for each version
 * of the module that are necessary to upgrade to a new version. It is very important to keep the
 * initialisation and the upgrade compatible with eachother.
 *
 * @author PHPlot Module Development Team
 * @param string oldversion. This function takes the old version that is currently stored in the module db
 * @return bool true on succes of upgrade
 * @throws mixed This function can throw all sorts of errors, depending on the functions present
                 Currently it can raise database errors.
 */
function phplot_upgrade($oldversion)
{
    switch ($oldversion) {
        case '0.1.0':
            /* Code to upgrade from version 1.0.0 goes here */
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
 * @author PHPlot Module Development Team
 * @param none
 * @return bool true on succes of deletion
 */
function phplot_delete()
{
    /* Remove any module aliases before deleting module vars
     * This Assumes one module alias in this case
     */
    $aliasname = xarModGetVar('phplot','aliasname');
    $isalias = xarModGetAlias($aliasname);
    if (isset($isalias) && ($isalias =='phplot')){
        xarModDelAlias($aliasname,'phplot');
    }

    /* Delete any module variables */
    xarModDelAllVars('phplot');

    /* Remove Masks and Instances
     * These functions remove all the registered masks and instances of a module
     * from the database. This is not strictly necessary, but it's good housekeeping.
     */
    xarRemoveMasks('phplot');
    xarRemoveInstances('phplot');

    /* Deletion successful*/
    return true;
}
?>
