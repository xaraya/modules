<?php
/**
 * Publications module Overview
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * Overview displays standard Overview page
 */
function publications_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('EditPublications',0)) return;

    $data=array();
    
    /* if there is a separate overview function return data to it
     * else just call the main function that usually displays the overview 
     */

    return xarTplModule('publications', 'admin', 'main', $data,'main');
}

?>