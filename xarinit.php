<?php
/**
 * This is the ie7 module.
 *
 * @package ie7
 * @copyright (C) 2004 by Ninth Avenue Software Pty Ltd
 * @link http://www.ninthave.net
 * @author Roger Keays <roger.keays@ninthave.net>
 */

/**
 * Initialise the module.
 */
function ie7_init()
{
    /* don't enable by default cause we can break sites easily */
    xarModSetVar('ie7', 'enabled', false);

    /* security masks */
    xarRegisterMask('AdminIE7', 'All', 'ie7', 'All', 'All',
       'ACCESS_ADMIN');

    return ie7_upgrade('0.7.2');
}


/**
 * Upgrade the module.
 */
function ie7_upgrade($version)
{
    switch ($version) {

        case '0.7.2':
            
    }
    return true;
}


/** 
 * Remove the module.
 */
function ie7_delete()
{
    /* delete module vars */
    xarModDelAllVars('ie7');
    xarRemoveMasks('ie7');
    return true;
}
?>
