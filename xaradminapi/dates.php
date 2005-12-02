<?php
/**
* Get dates for drop-down menus
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
 * generate array of dates for drop-down menus
 */
function ebulletin_adminapi_dates($args)
{
    extract($args);

    // validate vars
    $invalid = array();
    if (isset($max) && (!is_int($max) || $max < 1)) {
        $invalid[] = 'max';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'dates', 'eBulletin');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // numbers
    if (empty($max)) $max = 6;
    $dates['num'] = array();
    for ($i=0; $i<=$max; $i++) {
        $dates['num'][] = $i;
    }

    // units and signs
    $dates['unit'] = array('day', 'week', 'month', 'year');
    $dates['sign'] = array('ago', 'from now');

    // default range
    $dates['default'] = array('start' => array('num' => 3,
                                               'unit' => 'day',
                                               'sign' => 'ago'),
                              'end' => array('num' => 4,
                                             'unit' => 'day',
                                             'sign' => 'from now'));

    return $dates;
}

?>