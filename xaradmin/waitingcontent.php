<?php
/**
 * Uploads Module
 *
 * @package modules
 * @subpackage uploads module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/666
 * @author Uploads Module Development Team
 */

/**
 * Waiting content hook
 *
 * display waiting content as a hook
 * @since 19 Feb 2008
 * @return array count of the files in 'submitted' status
 */
function uploads_admin_waitingcontent()
{
    // Get count of files in submitted state
    unset($count_submitted);
    $count_submitted = xarMod::apiFunc(
        'uploads',
        'user',
        'db_count',
        ['fileStatus' => 1]
    );
    $data['count_submitted'] = $count_submitted;
    return $data;
}
