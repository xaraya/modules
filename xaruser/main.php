<?php
/**
 * Scheduler module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Scheduler Module
 * @link http://xaraya.com/index.php/release/189.html
 * @author mikespub
 */
/**
 * the main user function - only used for external triggers
 * @param  $args ['itemid'] job id (optional)
 */
function scheduler_user_main()
{
    if (!xarVarFetch('itemid', 'id', $itemid,'',XARVAR_NOT_REQUIRED)) return;

	$args = array();

    if(!empty($itemid)) {
		$args['itemid'] = $itemid;
    } else {
    	$args['trigger'] = 1;
    }

/*
    if (!empty($lastrun) && $lastrun > $now - ((60*5)-1) )  // Make sure it's been at least five minutes
    {
        $diff = time() - $lastrun;
        return xarML('Last run was #(1) minutes #(2) seconds ago', intval($diff / 60), $diff % 60);
    }
*/

    $output = xarModAPIFunc('scheduler','user','runjobs', $args);

// TODO: dump exceptions ?
    return $output;
}

?>
