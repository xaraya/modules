<?php

// Handler for a data item from a previous result set
function reports_userapi_dataitemtag($args)
{
    extract($args);
    xarLogVariable('args in handler',$args);

    $code = $dataset."->fields['$name']";
    return $code;    
}

?>