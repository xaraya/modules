<?php
/**
 * Waiting content hook
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
/**
 * display waiting content as a hook
 * @since 19 Feb 2008
 * @return array count of the files in 'submitted' status
 */
function uploads_admin_waitingcontent()
{
    // Get count of files in submitted state
    unset($count_submitted);
    $count_submitted = xarModAPIFunc('uploads', 'user', 'db_count',
                          array('fileStatus' => 1));
    $data['count_submitted'] = $count_submitted;
    return $data;
}
?>