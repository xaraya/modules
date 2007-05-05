<?php
/**
 * Create a new category
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Categories module
 */
/**
 * new - generic wrapper to create a new item
 * Takes no parameters
 *
 * @author Marc Lutolf
 */
function categories_admin_new()
{
    return xarModFunc('categories', 'admin', 'modifycat');
}
?>