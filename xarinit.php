<?php
/**
 * Initialisation of xorba module
 *
 * @package modules
 * @subpackage xorba
 * @copyright The Digital Development Foundation, 2006
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @author Marcel van der Boom <mrb@hsdev.com>
**/

function xorba_init($args = array())
{
    return xorba_upgrade('0.0.0');
}

function xorba_activate($args = array())
{
    return true;
}

function xorba_upgrade($oldVersion)
{
    switch($oldVersion)
    {
        case '0.0.0':
            // Initial installation
        case '1.0.0':
            // Management client came in, create the object to hold the
            // servers we want to manage
            $objDef = 'modules/xorba/xardata/xorba_servers-def.xml';
            $objectid = xarModAPIFunc('dynamicdata','util','import', array('file' => $objDef, 'keepitemid' => true));
            if(!$objectid) return;
        case '1.1.0':
            // Current version
            break;
        default:
            // Should never happen
            return false;
    }
    return true;
}

function xorba_delete($args = array())
{
    return true;
}
?>
