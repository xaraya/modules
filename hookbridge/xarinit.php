<?php 

/**
 * Hook Bridge
 *
 * @copyright   by Michael Cortez
 * @license     GPL (http://www.gnu.org/licenses/gpl.html)
 * @author      Michael Cortez
 * @link        
 *
 * @package     Xaraya eXtensible Management System
 * @subpackage  Hook Bridge
 * @version     $Id$
 *
 */

/**
 * initialise the module.  This function is only ever called once during the
 * lifetime of a particular module instance
 */
function hookbridge_init()
{

    
    /*
     * Module Variable for ShortURLSupport!
     */
    xarModSetVar(
        'hookbridge'
        ,'SupportShortURLs'
        ,0 );
    

    if (!xarModRegisterHook(
            'module'
            ,'modifyconfig'
            ,'GUI'
            ,'hookbridge'
            ,'hook'
            ,'module_modifyconfig' ))
        {
        return false;
        }
    if (!xarModRegisterHook(
            'module'
            ,'remove'
            ,'API'
            ,'hookbridge'
            ,'hook'
            ,'module_remove' ))
        {
        return false;
        }
    if (!xarModRegisterHook(
            'module'
            ,'updateconfig'
            ,'API'
            ,'hookbridge'
            ,'hook'
            ,'module_updateconfig' ))
        {
        return false;
        }


    if (!xarModRegisterHook(
            'item'
            ,'transform'
            ,'API'
            ,'hookbridge'
            ,'hook'
            ,'item_transformoutput' ))
        {
        return false;
        }
    if (!xarModRegisterHook(
            'item'
            ,'transform-input'
            ,'API'
            ,'hookbridge'
            ,'hook'
            ,'item_transforminput' ))
        {
        return false;
        }


    if (!xarModRegisterHook(
            'item'
            ,'display'
            ,'GUI'
            ,'hookbridge'
            ,'hook'
            ,'item_display' ))
        {
        return false;
        }
    if (!xarModRegisterHook(
            'item'
            ,'new'
            ,'GUI'
            ,'hookbridge'
            ,'hook'
            ,'item_new' ))
        {
        return false;
        }
    if (!xarModRegisterHook(
            'item'
            ,'delete'
            ,'API'
            ,'hookbridge'
            ,'hook'
            ,'item_delete' ))
        {
        return false;
        }
    if (!xarModRegisterHook(
            'item'
            ,'update'
            ,'API'
            ,'hookbridge'
            ,'hook'
            ,'item_update' ))
        {
        return false;
        }
    if (!xarModRegisterHook(
            'item'
            ,'create'
            ,'API'
            ,'hookbridge'
            ,'hook'
            ,'item_create' ))
        {
        return false;
        }

    if (!xarModRegisterHook(
            'item'
            ,'modify'
            ,'GUI'
            ,'hookbridge'
            ,'hook'
            ,'item_modify' ))
        {
        return false;
        }


    if (!xarModRegisterHook(
            'item'
            ,'search'
            ,'GUI'
            ,'hookbridge'
            ,'hook'
            ,'search' ))
        {
        return false;
        }


    /*
     * REGISTER MASKS
     */
    
    // for module access
    xarRegisterMask( 'Readhookbridge' ,'All' ,'hookbridge' ,'All' ,'All', 'ACCESS_READ' );
    xarRegisterMask( 'Viewhookbridge' ,'All' ,'hookbridge' ,'All' ,'All', 'ACCESS_OVERVIEW' );
    xarRegisterMask( 'Deletehookbridge' ,'All' ,'hookbridge' ,'All' ,'All', 'ACCESS_DELETE' );
    xarRegisterMask( 'Edithookbridge' ,'All' ,'hookbridge' ,'All' ,'All', 'ACCESS_EDIT' );
    xarRegisterMask( 'Addhookbridge' ,'All' ,'hookbridge' ,'All' ,'All', 'ACCESS_ADD' );
    xarRegisterMask( 'Adminhookbridge' ,'All' ,'hookbridge' ,'All' ,'All', 'ACCESS_ADMIN' );



    // Initialisation successful
    return true;
}

/**
 * Remove the module instance from the xaraya installation.
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance.
 */
function hookbridge_delete()
{
    /*
     * REMOVE MODULE VARS
     */
    if ( !xarModDelAllVars( 'hookbridge' ) )
        return;

    

    /*
     * REMOVE MASKS AND INSTANCES
     */
    xarRemoveMasks( 'hookbridge' );
    xarRemoveInstances( 'hookbridge' );

    

    // Deletion successful
    return true;
}



/**
 * upgrade the module from an older version.
 * This function can be called multiple times
 */
function hookbridge_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {

        // TODO // IMPLEMENT YOUR UPGRADES

        default:
            // TODO // throw appropriate exception
            return false;
    }

    // Update successful
    return true;
}

/*
 * END OF FILE
 */
?>
