<?php

function labAccounting_ledgersapi_getlongchart()
{
    $chartlist = pnModAPIFunc('labAccounting',
                            'ledgers',
                            'getchartselect');
    foreach($chartlist as $chartitem) {
        $options[$chartitem['id']] = $chartitem['name'];
    }
    
    return $options;
}

?>