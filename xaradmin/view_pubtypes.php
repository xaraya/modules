<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author mikespub
 */
/**
 * manage publication types (all-in-one function for now)
 */
sys::import('xaraya.structures.query');

function publications_admin_view_pubtypes()
{
    if (!xarSecurityCheck('AdminPublications')) return;

    // Return the template variables defined in this function
    return array();
}

?>