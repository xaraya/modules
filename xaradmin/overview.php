<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Recommend Module
 */

/**
 * The main administration function
 * @author jojodee
 */
function recommend_admin_overview()
{
    if (!xarSecurityCheck('EditRecommend')) return;

    $data=array();

    return xarTplModule('recommend', 'admin', 'main', $data,'main');
}

?>