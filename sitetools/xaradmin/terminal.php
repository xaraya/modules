<?php

function sitetools_admin_terminal()
{
    if(!xarVarFetch('term_input','str::',$term_input,'')) return;

   if (!xarSecurityCheck('AdminSiteTools')) return;
    $output = array();
    if($term_input != '') {
        // Pass verbatim to database;
        $dbconn =& xarDBGetConn();
        $result =& $dbconn->Execute($term_input);
        if(!$result) {
            $error = xarCurrentError();
            $output[] = array("Error" => $error->getShort());
            xarErrorFree();
        } else {
            if(is_object($result)) {
                while(!$result->EOF) {
                    $row = $result->GetRowAssoc(true);
                    $output[] = $row;
                    $result->MoveNext();
                }
            } else {
                $output[] = array(xarML("Success"));
            }
        }
    }
     
    //$data['term_output'] = print_r($output,true);
    $data['term_output'] = $output;
    $data['term_input'] = $term_input;
    return $data;
    return array();
}

?>
