<?php
/**
 * Overview for Uploads
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads
 * @link http://xaraya.com/index.php/release/666.html
 */

/**
 * Overview displays standard Overview page
 */
function uploads_admin_overview()
{
    $data=array();
    //just return to main function that displays the overview
    return xarTplModule('uploads', 'admin', 'main', $data, 'main');
}

?>