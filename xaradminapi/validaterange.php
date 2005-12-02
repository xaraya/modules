<?php
/**
* Validate the range variable
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
 * validate range
 */
function ebulletin_adminapi_validaterange($args)
{
    extract($args);

    $dates = xarModAPIFunc('ebulletin', 'admin', 'dates');

    // validate vars
    $invalid = array();
    if (empty($args)) {
        $invalid[] = 'range';
    }
    if (!isset($numsago) || !is_array($numsago)) {
        $invalid[] = 'numsago';
    }
    if (!isset($unitsago) || !is_string($unitsago)) {
        $invalid[] = 'unitsago';
    }
    if (!isset($numsfromnow) || !is_array($numsfromnow)) {
        $invalid[] = 'numsfromnow';
    }
    if (!isset($unitsfromnow) || !is_string($unitsfromnow)) {
        $invalid[] = 'unitsfromnow';
    }

    // make sure numsago is before numsfromnow
    if (strtotime("$numsago $unitsago") > strtotime("$numsfromnow $unitsfromnow")) {
        $invalid[] = 'beginning interval occurs after ending interval';
    }

    if (count($invalid) > 0) return;

    // if we made it this far, we're okay
    return true;
}

?>