<?php
/**
 * Displays standard Overview page
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Overlib Module
 * @link http://xaraya.com/index.php/release/4.html
 */
/**
 * Overview function that displays the standard Overview page
 *
 */
function overlib_admin_overview()
{
   /* Security Check */

    $data=array();

    /* if there is a separate overview function return data to it
     * else just call the main function that displays the overview
     */

    return xarTplModule('overlib', 'admin', 'main', $data,'main');
}

?>
