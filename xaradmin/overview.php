<?php
/**
 * Display module overview
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * Overview displays standard Overview page
 *
 * @returns array xarTplModule with $data containing template data
 * @return array containing the menulinks for the overview item on the main manu
 * @since 14 Oct 2005
 */
function labAccounting_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('AdminDynExample',0)) return;

    $data = xarModAPIFunc('labAccounting','admin','menu');

    /* if there is a separate overview function return data to it
     * else just call the main function that usually displays the overview 
     */

    return xarTplModule('labAccounting', 'admin', 'main', $data,'main');
}

?>