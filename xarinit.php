<?php
/**
 * File: $Id: s.xaradmin.php 1.28 03/02/08 17:38:40-05:00 John.Cox@mcnabb. $
 *
 * Figlet
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage figlet module
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