<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * manage publication types (all-in-one function for now)
 */
sys::import('xaraya.structures.query');

function publications_admin_viewpubtypes()
{
    if (!xarSecurityCheck('AdminPublications')) return;

    // Return the template variables defined in this function
    return array();
}

?>
