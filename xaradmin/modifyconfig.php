<?php
/**
 * Ephemerids
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Ephemerids Module
 * @link http://xaraya.com/index.php/release/15.html
 * @author Volodymyr Metenchuk
 */
/**
 * modify configuration
 */
function ephemerids_admin_modifyconfig()
{
    // Security Check
    if(!xarSecurityCheck('AdminEphemerids')) return;
    $data['authid'] = xarSecGenAuthKey();
    return $data;
}
?>