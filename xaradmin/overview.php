<?php
/**
 * Articles module Overview
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/**
 * Overview displays standard Overview page
 */
function articles_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('EditArticles',0)) return;

    $data=array();
    
    /* if there is a separate overview function return data to it
     * else just call the main function that usually displays the overview 
     */

    return xarTplModule('articles', 'admin', 'main', $data,'main');
}

?>