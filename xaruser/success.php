<?php
 /**
 * @package modules
 * @copyright (C) 2002-2010 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage amazonfps
 * @link http://xaraya.com/index.php/release/eid/1169
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * The default success page (if no other success page is set in amazonfps_userapi_cbui)
 */
function amazonfps_user_success()
{
 
    if (!xarSecurityCheck('AddAmazonFPS',0)) {
        return;
    }
     
    return xarTplModule('amazonfps','user','success');

}

?>