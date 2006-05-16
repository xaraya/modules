<?php
/**
 * Table definition function
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage window
 * @link http://xaraya.com/index.php/release/3002.html
 * @author Marc Lutolf
 */
/**
 * Table definition function
 *
 * @access private
 * @return array
 */
function window_xartables()
{
    $xartables = array();

    $xartables['window'] = xarDBGetSiteTablePrefix() . '_window';

    return $xartables;
}
?>