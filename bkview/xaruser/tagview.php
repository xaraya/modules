<?php

/**
 * File: $Id$
 *
 * tag view for bkview module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

include_once("modules/bkview/xarincludes/bk.class.php");

function bkview_user_tagview($args)
{
    xarVarFetch('repoid','id',$repoid);
    xarVarFetch('range','str::',$range,NULL, XARVAR_NOT_REQUIRED);
    xarVarFetch('user','str::',$user,'',XARVAR_NOT_REQUIRED);
    extract($args);

    $params = array('repoid' => $repoid,
                    'range' => $range,
                    'user' => $user,
                    'taggedonly' => true);
                    
    return xarModFunc('bkview','user','csetview',$params);
}
