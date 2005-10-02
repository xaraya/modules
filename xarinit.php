<?php
/**
 * File: $Id$
 *
 * Xaraya Magpie
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage magpie
 * @author John Cox
*/
function magpie_init()
{
    xarRegisterMask('ViewMagpie','All','magpie','All','All','ACCESS_OVERVIEW');
    return true;
}
function magpie_delete()
{
    xarModDelAllVars('magpie');
    xarRemoveMasks('magpie');
    xarRemoveInstances('magpie');
    return true;
}
?>