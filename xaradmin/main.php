<?php
/**
 * Main admin GUI entry point
 *
 * @package modules
 * @subpackage xorba
 * @copyright The Digital Development Foundation, 2006
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @author Marcel van der Boom <mrb@hsdev.com>
**/

function xorba_admin_main($args)
{
    $data = array();
    xarVarFetch('section','str:',$data['section'],'what',XARVAR_NOT_REQUIRED);
    // Nothing here yet, template handles it.
    return $data;
}
?>