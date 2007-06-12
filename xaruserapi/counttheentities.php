<?php

/**
 * Count the entities validation
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage xmlrpcvalidator api
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function xmlrpcvalidatorapi_userapi_counttheentities($args)
{
    extract($args);
    $sno=$msg->getParam(0);
    $str=$sno->scalarval();
    $gt=0; $lt=0; $ap=0; $qu=0; $amp=0;
    for($i=0; $i<strlen($str); $i++) {
        $c=substr($str, $i, 1);
        switch($c) {
        case ">":
            $gt++;
            break;
        case "<":
            $lt++;
            break;
        case "\"":
            $qu++;
            break;
        case "'":
            $ap++;
            break;
        case "&":
            $amp++;
            break;
        default:
            break;
        }
    }
    return new xmlrpcresp(new xmlrpcval(array("ctLeftAngleBrackets" =>
                                              new xmlrpcval($lt, "int"),
                                              "ctRightAngleBrackets" =>
                                              new xmlrpcval($gt, "int"),
                                              "ctAmpersands" =>
                                              new xmlrpcval($amp, "int"),
                                              "ctApostrophes" =>
                                              new xmlrpcval($ap, "int"),
                                              "ctQuotes" =>
                                              new xmlrpcval($qu, "int")),
                                        "struct"));
}
?>