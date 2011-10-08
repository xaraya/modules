<?php
/**
 * Figlet Module
 *
 * @package modules
 * @subpackage figlet module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Lucas Baltes, John Cox
 */

function figlet_init()
{
    // Set up module hooks
    if (!xarModRegisterHook('item',
                           'transformfig',
                           'API',
                           'figlet',
                           'user',
                           'transform')) return;

    // Set up module variable
    xarModVars::set('figlet', 'defaultfont', 'standard.flf');

    //Register Mask
    xarRegisterMask('ReadFiglet','All','figlet','All','All','ACCESS_READ');
    xarRegisterMask('AdminFiglet','All','figlet','All','All','ACCESS_ADMIN');

    return true;
}

function figlet_upgrade($oldversion)
{
    return true;
}

function figlet_delete()
{

    // Remove module hooks
    if (!xarModUnregisterHook('item',
                             'transformfig',
                             'API',
                             'autolinks',
                             'user',
                             'transform')) return;

    // Set up module variable
    xarModDelVar('figlet', 'defaultfont');

    // Remove Masks and Instances
    xarRemoveMasks('Figlet');
    xarRemoveInstances('Figlet');

    return true;
}

?>