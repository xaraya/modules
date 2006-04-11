<?php
/**
 * Modify  comment module configuration
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
*/
include_once('modules/comments/xarincludes/defines.php');
/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function comments_admin_modifyconfig()
{
     $editstamp=xarModGetVar('comments','editstamp');
    $output['editstamp']       = !isset($editstamp) ? 1 :$editstamp;

    // Security Check
    if(!xarSecurityCheck('Comments-Admin'))
        return;
    $numstats       = xarModGetVar('comments','numstats');
    $rssnumitems    = xarModGetVar('comments','rssnumitems');
    if (empty($rssnumitems)) {
        xarModSetVar('comments', 'rssnumitems', 25);
    }
    if (empty($numstats)) {
        xarModSetVar('comments', 'numstats', 100);
    }

    //check for comments hook in case it's set independently elsewhere
    if (xarModIsHooked('comments', 'roles')) {
        xarModSetVar('comments','usersetrendering',true);
    } else {
        xarModSetVar('comments','usersetrendering',false);
    }

    $output['authid'] = xarSecGenAuthKey();
    $output['hooks'] = xarModCallHooks('module', 'modifyconfig', 'comments',
                                       array('module' => 'comments'));
    return $output;
}
?>
