<?php

/**
 * Validate many types
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage xmlrpcvalidatorapi
 * @author Marcel van der Boom <marcel@xaraya.com>
*/


function xmlrpcvalidatorapi_userapi_manytypes($args)
{
    extract($args);
    return new xmlrpcresp(new xmlrpcval(array(
                                              $msg->getParam(0),
                                              $msg->getParam(1),
                                              $msg->getParam(2),
                                              $msg->getParam(3),
                                              $msg->getParam(4),
                                              $msg->getParam(5)),
                                        "array"));
}
?>