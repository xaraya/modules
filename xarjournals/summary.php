<?php

function labaccounting_journals_summary($args) {

    extract($args);


    $data = xarModAPIFunc('xtasks','admin','menu');

    if (!xarSecurityCheck('AdminXTask', 1, 'Item', "All:All:All")) {
        return;
    }
    
    $journals = xarModAPIFunc('labaccounting','journals','getall');
    
    $data['journals'] = $journals;
    
    return $data;
}

?>