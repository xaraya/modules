<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Comments Module
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 *  Analyzes a celko based table for inconsistencies in the celko
 *  left/right values
 *
 *  @author Carl P. Corliss
 *  @access public
 *  @param  array  $data  The data array to check for inconsistencies
 *  @returns array An array of data containing the total records, total root nodes,
 *                 and, if there are inconsistencies (holes in the celko model),
 *                 it returns a percent amount of the inconsistency.
 */

function comments_adminapi_celko_analyze( $args )
{

}

?>
