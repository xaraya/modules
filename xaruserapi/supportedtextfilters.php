<?php

function moveabletype_userapi_supportedTextFilters($args)
{
    // NOT supported yet, return an empty response according to the spec
    $out = xarModAPIFunc('xmlrpcserver','user','createresponse',
                         array('module'  => 'moveabletype',
                               'command' => 'supportedtextfilters',
                               'params'  => array())
                         );
    return $out;
    
}

?>