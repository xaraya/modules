<?php
/**
 * Helpdesk Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Helpdesk Module
 * @link http://www.abraisontechnoloy.com/
 * @author Brian McGilligan <brianmcgilligan@gmail.com>
 */
/**
 *  Selects a representative to assign to.
 *
 *  @author Brian McGilligan
 *  @param $args['reps'] - Assignable reps.
 *  @return uid of rep ticket needs to be assigned to
 */
function helpdesk_userapi_assignto($args)
{
    extract($args);
    if( count($reps) == 0 ){ return 0; }
    mt_srand((double)microtime()*100);
    $index = mt_rand(0, (sizeof($reps) - 1));
    return $reps[$index];
}
?>
