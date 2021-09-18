<?php
/**
 * Scheduler Module
 *
 * @package modules
 * @subpackage scheduler module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/189.html
 * @author mikespub
 */
/**
 * Overview function that displays the standard Overview page
 * @author jojodee <jojodee@xaraya.com>
 * @return mixed
 */
function scheduler_admin_overview()
{
    /* Security Check */
    if (!xarSecurity::check('AdminScheduler', 0)) {
        return;
    }

    $data=[];

    /* if there is a separate overview function return data to it
     * else just call the main function that displays the overview
     */

    return xarTpl::module('scheduler', 'admin', 'main', $data, 'main');
}
