<?php
/**
 * Site Tools SQL Terminal
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitetools Module
 * @link http://xaraya.com/index.php/release/887.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

 /* A simple sql terminal for Sitetools
  * @author jojodee
  * @author Marcel van der Boom - supplied original script on which this is based
  */
function sitetools_admin_terminal()
{
    if(!xarVarFetch('term_input','str::',$term_input,'')) return;

   if (!xarSecurityCheck('AdminSiteTools')) return;
    $output = array();
    if($term_input != '') {
        /* Pass verbatim to database; */
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

    /* $data['term_output'] = print_r($output,true); */
    $data['term_output'] = $output;
    $data['term_input'] = $term_input;
    return $data;
    return array();
}

?>