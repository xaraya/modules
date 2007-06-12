<?php

/**
 * Return the dispatch map for the validation api
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage module name
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * Construct the description map
 *
 */
function xmlrpcvalidatorapi_userapi_getdmap()
{
    // Data types for xmlrpc
    $dataTypes = xarModAPIFunc('xmlrpcserver','user','getdatatypes');
    extract($dataTypes);

    $v1_arrayOfStructs_sig=array(array($xmlrpcInt, $xmlrpcArray));
    $v1_arrayOfStructs_doc='This handler takes a single parameter, an array of structs,
each of which contains at least three elements named moe, &lt;larry&gt; and curly, all &lt;i4&gt;s.
Your handler must add all the struct elements named curly and return the result.';


    $v1_easyStruct_sig=array(array($xmlrpcInt, $xmlrpcStruct));
    $v1_easyStruct_doc='This handler takes a single parameter, a struct, containing at
least three elements named moe, larry and curly, all &lt;i4&gt;s.
Your handler must add the three numbers and return the result.';

    $v1_echoStruct_sig=array(array($xmlrpcStruct, $xmlrpcStruct));
    $v1_echoStruct_doc='This handler takes a single parameter, a struct.
Your handler must return the struct.';


    $v1_manyTypes_sig=array(array($xmlrpcArray, $xmlrpcI4, $xmlrpcBoolean,
                                  $xmlrpcString, $xmlrpcDouble, $xmlrpcDateTime,
                                  $xmlrpcBase64));
    $v1_manyTypes_doc='This handler takes six parameters, and returns an array
containing all the parameters.';

    $v1_moderateSizeArrayCheck_sig=array(array($xmlrpcString, $xmlrpcArray));
    $v1_moderateSizeArrayCheck_doc='This handler takes a single parameter,
which is an array containing between 100 and 200 elements. Each of the items is a string,
your handler must return a string containing the concatenated text of the first and last elements.';

    $v1_simpleStructReturn_sig=array(array($xmlrpcStruct, $xmlrpcI4));
    $v1_simpleStructReturn_doc='This handler takes one parameter, and returns a struct
containing three elements, times10, times100 and times1000, the result of multiplying
the number by 10, 100 and 1000.';

    $v1_nestedStruct_sig=array(array($xmlrpcInt, $xmlrpcStruct));
    $v1_nestedStruct_doc='This handler takes a single parameter, a struct, that models
a daily calendar. At the top level, there is one struct for each year. Each year is
broken down into months, and months into days. Most of the days are empty in the struct
you receive, but the entry for April 1, 2000 contains a least three elements named moe,
larry and curly, all &lt;i4&gt;s. Your handler must add the three numbers and return the
result.';

    $v1_countTheEntities_sig=array(array($xmlrpcStruct, $xmlrpcString));
    $v1_countTheEntities_doc='This handler takes a single parameter, a string,
that contains any number of predefined entities, namely &lt;, &gt;, &amp; \'
and ". Your handler must return a struct that contains five fields, all numbers:
ctLeftAngleBrackets, ctRightAngleBrackets, ctAmpersands, ctApostrophes, ctQuotes.';


    $_xmlrpc_validator1_dmap= array(
                                    "validator1.arrayOfStructsTest" =>
                                    array("function" => "xmlrpcvalidatorapi_userapi_arrayofstructs",
                                          "signature" => $v1_arrayOfStructs_sig,
                                          "docstring" => $v1_arrayOfStructs_doc),

                                    "validator1.easyStructTest" =>
                                    array("function" => "xmlrpcvalidatorapi_userapi_easystruct",
                                          "signature" => $v1_easyStruct_sig,
                                          "docstring" => $v1_easyStruct_doc),

                                    "validator1.echoStructTest" =>
                                    array("function" => "xmlrpcvalidatorapi_userapi_echostruct",
                                          "signature" => $v1_echoStruct_sig,
                                          "docstring" => $v1_echoStruct_doc),

                                    "validator1.manyTypesTest" =>
                                    array("function" => "xmlrpcvalidatorapi_userapi_manytypes",
                                          "signature" => $v1_manyTypes_sig,
                                          "docstring" => $v1_manyTypes_doc),

                                    "validator1.moderateSizeArrayCheck" =>
                                    array("function" => "xmlrpcvalidatorapi_userapi_moderatesizearraycheck",
                                          "signature" => $v1_moderateSizeArrayCheck_sig,
                                          "docstring" => $v1_moderateSizeArrayCheck_doc),
                                    "validator1.simpleStructReturnTest" =>
                                    array("function" => "xmlrpcvalidatorapi_userapi_simplestructreturn",
                                          "signature" => $v1_simpleStructReturn_sig,
                                          "docstring" => $v1_simpleStructReturn_doc),

                                    "validator1.nestedStructTest" =>
                                    array("function" => "xmlrpcvalidatorapi_userapi_nestedstruct",
                                          "signature" => $v1_nestedStruct_sig,
                                          "docstring" => $v1_nestedStruct_doc),

                                    "validator1.countTheEntities" =>
                                    array("function" => "xmlrpcvalidatorapi_userapi_counttheentities",
                                          "signature" => $v1_countTheEntities_sig,
                                          "docstring" => $v1_countTheEntities_doc)
                                    );
    return $_xmlrpc_validator1_dmap;
}
?>