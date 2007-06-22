<?php
/*
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Recommend Module
 */
/**
 * the main administration function
 */
function recommend_admin_main()
{
    /* Security Check */
    if (!xarSecurityCheck('EditRecommend')) return;

        xarResponseRedirect(xarModURL('recommend', 'admin', 'modifyconfig'));

    /* success */
    return true;
}

?>