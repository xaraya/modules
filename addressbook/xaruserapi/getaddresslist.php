<?php
/**
 * File: $Id: xaradminapi.php,v 1.3 2003/06/30 04:37:08 garrett Exp $
 *
 * AddressBook user getAddressList
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */

/**
 * Retrieves the list of address
 *
 * @return mixed
 */
function AddressBook_userapi_getAddressList($args) {

    extract($args);

    // Get the menu values
    $menuValues = xarModAPIFunc(__ADDRESSBOOK__,'user','getMenuValues');
    foreach ($menuValues as $key=>$value) {
        $data[$key] = $value;
    }

    // SQL Query
    list($dbconn) = xarDBGetConn();
    $xarTables = xarDBGetTables();
    $address_table = $xarTables['addressbook_address'];

    // Note Searchorder
    if ($data['sortview'] != 1) {
        $sort_1 = xarModGetVar(__ADDRESSBOOK__, 'sortorder_1');
        $data['sql'] = "SELECT *, CONCAT($sort_1) AS listname FROM $address_table";
    }
    else {
        $sort_2 = xarModGetVar(__ADDRESSBOOK__, 'sortorder_2');
        $data['sql'] = "SELECT *, CONCAT($sort_2) AS listname FROM $address_table";
    }

    // Get user id
    if (xarUserIsLoggedIn()) { $user_id = xarUserGetVar('uid');}
    else {$user_id = 0;}

    // Private Contacts only
    // if globalprotect, only records of the user are shown
    if (((xarModGetVar(__ADDRESSBOOK__, 'globalprotect'))==1) && (!xarSecurityCheck('EditAddressBook',0))) {
        $data['sql'] .= " WHERE (user_id=$user_id)";
    }
    else {
        // if private = 1, show only private records
        if ($data['menuprivate'] == 1) {
            // Admins always see all records
            if (xarSecurityCheck('EditAddressBook',0)) {
                $data['sql'] .= " WHERE (user_id=$user_id)";
            }
            else {
                $data['sql'] .= " WHERE (user_id=$user_id AND private = 1)";
            }
        }
        else {
            // Admins always see all records
            if (xarSecurityCheck('EditAddressBook',0)) {
                $data['sql'] .= " WHERE (user_id IS NOT NULL)";
            }
            else {
                // if private = 0, show all records
                $data['sql'] .= " WHERE ((private = 0) OR (user_id=$user_id AND private = 1))";
            }
        }
    }

    // Filter Categories
    if ($data['catview']) {
        $data['sql'] .= " AND (cat_id = ".$data['catview'].")";
    }

    // A-Z
    if ($data['all'] == 0) {
        if ($data['sortview'] != 1) {
            $sortCols = explode(',',xarModGetVar(__ADDRESSBOOK__, 'sortorder_1'));
        }
        else {
            $sortCols = explode(',',xarModGetVar(__ADDRESSBOOK__, 'sortorder_2'));
        }
        if ($sortCols[0] == 'sortname') {
            if ($data['char']) { $data['sql'] .= " AND (sortname LIKE '".$data['char']."%')"; }
            else { $data['sql'] .= " AND (sortname LIKE 'A%')"; }
        }
        else {
            if ($sortCols[0] == 'sortcompany') {
                if ($data['char']) { $data['sql'] .= " AND (sortcompany LIKE '".$data['char']."%')"; }
                else { $data['sql'] .= " AND (sortcompany LIKE 'A%')"; }
            }
            else {
                if ($data['char']) { $data['sql'] .= " AND (".$sortCols[0]." LIKE '".$data['char']."%')"; }
                else { $data['sql'] .= " AND (".$sortCols[0]." LIKE 'A%')"; }
            }
        }
    }

    // Search
    if ($data['formSearch']) {
        $data['sql'] .= " AND (lname LIKE '%".$data['formSearch']."%'
                  OR fname LIKE '%".$data['formSearch']."%'
                  OR company LIKE '%".$data['formSearch']."%'
                  OR title LIKE '%".$data['formSearch']."%'
                  OR city LIKE '%".$data['formSearch']."%'
                  OR address_1 LIKE '%".$data['formSearch']."%'
                  OR address_2 LIKE '%".$data['formSearch']."%'
                  OR zip LIKE '%".$data['formSearch']."%'
                  OR country LIKE '%".$data['formSearch']."%'
                  OR state LIKE '%".$data['formSearch']."%'
                  OR note LIKE '%".$data['formSearch']."%'
                  OR contact_1 LIKE '%".$data['formSearch']."%'
                  OR contact_2 LIKE '%".$data['formSearch']."%'
                  OR contact_3 LIKE '%".$data['formSearch']."%'
                  OR contact_4 LIKE '%".$data['formSearch']."%'
                  OR contact_5 LIKE '%".$data['formSearch']."%')";

//        $cus_fields = xarModAPIFunc(__ADDRESSBOOK__,'user','customFieldInformation',array('id'=>''));
        $custFields = xarModAPIFunc(__ADDRESSBOOK__,'user','getCustomFieldInfo',array('flag'=>_AB_CUST_ALLFIELDINFO));
        foreach($cusFields as $cusField) {
            if ((!strstr($custFieled['type'],_AB_CUST_TEST_LB)) && (!strstr($custField['type'],_AB_CUST_TEST_HR))) {
                if (strstr($custField['type'],_AB_CUST_TEST_STRING)) {
                    $data['sql'] .= " OR ".$custField['colName']." LIKE '%".$data['formSearch']."%'";
                }
            }
        }
    }

    // Sort
    $data['sql'] .= " ORDER BY listname ASC";

    if (!$data['total']) {
        $numRec =& $dbconn->Execute($data['sql']);
        $data['total'] = $numRec->RecordCount();
        $data['page'] = 1;
        $numRec->Close();
    }

    if (!$data['total']) {
        xarExceptionSet(XAR_USER_EXCEPTION, _AB_ERR_INFO, new abUserException(_AB_NORECORDS)); //gehDEBUG
    }

    $items = xarModGetVar(__ADDRESSBOOK__, 'itemsperpage');
    $result =& $dbconn->Execute($data['sql']);

    if ($dbconn->ErrorNo() != 0) {
        xarExceptionSet(XAR_USER_EXCEPTION, _AB_ERR_ERROR, new abUserException("sql = ".$data['sql']));
    }

    //Show Result

    // A-Z Navigation
    /**
     * These vars are not displayed / do not need to go in $data
     */
    $selChar = ((isset($data['char'])) ? $data['char'] : '');
    $numPages = (($data['total']/$items)+1);

    $data['azLinks'] = array();
    if ($data['all']==0) {
        $numPages = (($data['total']/$items)+1);
        for($i=65;$i<=90;$i++) {
            $azLink = '';
            $char = chr($i);
            $params = array('authid'=>xarSecGenAuthKey(),
                            'sortview'=>$data['sortview'],
                            'catview'=>$data['catview'],
                            'menuprivate'=>$data['menuprivate'],
                            'all'=>$data['all'],
                            'char'=>$char);

            $pageURL = xarModURL(__ADDRESSBOOK__,'user','main',$params);
            if ($i != 65) {
                $azLink .= ' | ';
            }
            if ($char == $selChar) {
                $azLink .= '<b><u>'.$char.'</u></b>';
            } else {
                $azLink .= "<a href=\"".$pageURL."\">".$char."</a>";
            }
            $data['azLinks'][]['azLink'] = $azLink;
        }
    }
    // END A-Z Navigation

    // No Records found!
    if ($data['total'] < 1) {
        return $data;
    }

    if ($data['sortview'] != 1) {
        $data['headers'] = xarModAPIFunc(__ADDRESSBOOK__,'user','getListHeader',array('sort'=>1));
//geh        $output->Text('<b>'.xarVarPrepHTMLDisplay($headers[0]).'</b>');
    }
    else {
        $data['headers'] = xarModAPIFunc(__ADDRESSBOOK__,'user','getListHeader',array('sort'=>2));
//geh        $output->Text('<b>'.xarVarPrepHTMLDisplay($headers[0]).'</b>');
    }


    // Retrieve all records and format as needed for display. The 'searchResults' var is temp
    // only and 'displayRows' is used be the template to display the data
    for (; !$result->EOF; $result->MoveNext()) {
        list($id,$cat_id,$prefix,$lname,$fname,$sortname,$title,$company,$sortcompany,$img,$zip,$city,$address_1,$address_2,$state,$country,$contact_1,$contact_2,$contact_3,$contact_4,$contact_5,$c_label_1,$c_label_2,$c_label_3,$c_label_4,$c_label_5,$c_main,$custom_1,$custom_2,$custom_3,$custom_4,$note,$user,$private,$date,$listname) = $result->fields;
        $displayRow = array();
        $data['searchResults'][] = array ('id'          => $id
                                         ,'cat_id'      => $cat_id
                                         ,'prefix'      => $prefix
                                         ,'lname'       => $lname
                                         ,'fname'       => $fname
                                         ,'sortname'    => $sortname
                                         ,'title'       => $title
                                         ,'company'     => $company
                                         ,'sortcompany' => $sortcompany
                                         ,'img'         => $img
                                         ,'zip'         => $zip
                                         ,'city'        => $city
                                         ,'address_1'   => $address_1
                                         ,'address_2'   => $address_2
                                         ,'state'       => $state
                                         ,'country'     => $country
                                         ,'contact_1'   => $contact_1
                                         ,'contact_2'   => $contact_2
                                         ,'contact_3'   => $contact_3
                                         ,'contact_4'   => $contact_4
                                         ,'contact_5'   => $contact_5
                                         ,'c_label_1'   => $c_label_1
                                         ,'c_label_2'   => $c_label_2
                                         ,'c_label_3'   => $c_label_3
                                         ,'c_label_4'   => $c_label_4
                                         ,'c_label_5'   => $c_label_5
                                         ,'c_main'      => $c_main
                                         ,'custom_1'    => $custom_1
                                         ,'custom_2'    => $custom_2
                                         ,'custom_3'    => $custom_3
                                         ,'custom_4'    => $custom_4
                                         ,'note'        => $note
                                         ,'user'        => $user
                                         ,'private'     => $private
                                         ,'date'        => $date
                                         ,'listname'    => $listname);

        /* not sure what this does gehDEBUG
//        $cus_fields = xarModAPIFunc(__ADDRESSBOOK__,'user','customFieldInformation',array('id'=>$id));
        $i=1;
        foreach($cus_fields as $cus) {
            if ($cus['type']=='date default NULL') {
                $cus['value'] = xarModAPIFunc(__ADDRESSBOOK__,'user','stamp2date',array('idate'=>$cus['value']));
            }
            $the_name = 'custom_'.$i;
            $$the_name = $cus['value'];
            $i++;
        } */

        /*
         * Step 1
         */
        if ($data['sortview'] != 1) {
            $sortCols = explode(',',xarModGetVar(__ADDRESSBOOK__, 'sortorder_1'));
        }
        else {
            $sortCols = explode(',',xarModGetVar(__ADDRESSBOOK__, 'sortorder_2'));
        }

        /*
         * Step 2
         */
        if ($sortCols[0] == 'sortname') {
            if (xarModGetVar(__ADDRESSBOOK__, 'name_order')==1) {
                if ((!empty($fname)) && (!empty($lname))) {
                    $displayRow[] = xarVarPrepHTMLDisplay($fname).' '.xarVarPrepHTMLDisplay($lname);
                } else {
                    $displayRow[] = xarVarPrepHTMLDisplay($fname).xarVarPrepHTMLDisplay($lname);
                }
            }
            else {
                if ((!empty($lname)) && (!empty($fname))) {
                    $displayRow[] = xarVarPrepHTMLDisplay($lname).', '.xarVarPrepHTMLDisplay($fname);
                } else {
                    $displayRow[] = xarVarPrepHTMLDisplay($lname).xarVarPrepHTMLDisplay($fname);
                }
            }
        }
        else {
            if ($sortCols[0] == 'sortcompany') {
                $displayRow[] = xarVarPrepHTMLDisplay($company);
            } else {
                $displayRow[] = xarVarPrepHTMLDisplay($$sortCols[0]);
            }
        }

        /*
         * Step 3
         */
        if ($sortCols[1] == 'sortname') {
            if (xarModGetVar(__ADDRESSBOOK__, 'name_order')==1) {
                if ((!empty($fname)) && (!empty($lname))) {
                    $displayRow[] = xarVarPrepHTMLDisplay($fname).' '.xarVarPrepHTMLDisplay($lname);
                } else {
                    $displayRow[] = xarVarPrepHTMLDisplay($fname).xarVarPrepHTMLDisplay($lname);
                }
            }
            else {
                if ((!empty($lname)) && (!empty($fname))) {
                    $displayRow[] = xarVarPrepHTMLDisplay($lname).', '.xarVarPrepHTMLDisplay($fname);
                } else {
                    $displayRow[] = xarVarPrepHTMLDisplay($lname).xarVarPrepHTMLDisplay($fname);
                }
            }
        }
        else {
            if ($sortCols[1] == 'sortcompany') {
                if (!empty($company)) {
                    $displayRow[] = xarVarPrepHTMLDisplay($company);
                } else {
                    $displayRow[] = '&nbsp;';
                }
            } else {
                $displayRow[] = xarVarPrepHTMLDisplay($$sortCols[1]);
            }
        }

        // Format Contact information
        switch($c_main) {
            case 0:
                if(!xarModAPIFunc(__ADDRESSBOOK__,'user','is_email',array('email'=>$contact_1))) {
                    if(!xarModAPIFunc(__ADDRESSBOOK__,'user','is_url',array('url'=>$contact_1))) {
                        if (!empty($contact_1)) {
                            $displayRow[] = xarVarPrepHTMLDisplay($contact_1);
                        } else {
                            $displayRow[] = '&nbsp;';
                        }
                    }
                    else {
                        $displayRow[] = '<a href="'.xarVarPrepHTMLDisplay($contact_1).'" target="_blank">'.xarVarPrepHTMLDisplay($contact_1).'</a>';
                    }
                }
                else {
                    $displayRow[] = '<a href="mailto:'.xarVarPrepHTMLDisplay($contact_1).'">'.xarVarPrepHTMLDisplay($contact_1).'</a>';
                }
                break;
            case 1:
                if(!xarModAPIFunc(__ADDRESSBOOK__,'user','is_email',array('email'=>$contact_2))) {
                    if(!xarModAPIFunc(__ADDRESSBOOK__,'user','is_url',array('url'=>$contact_2))) {
                        if (!empty($contact_2)) {
                            $displayRow[] = xarVarPrepHTMLDisplay($contact_2);
                        } else {
                            $displayRow[] = '&nbsp;';
                        }
                    }
                    else {
                        $displayRow[] = '<a href="'.xarVarPrepHTMLDisplay($contact_2).'">'.xarVarPrepHTMLDisplay($contact_2).'</a>';
                    }
                }
                else {
                    $displayRow[] = '<a href="mailto:'.xarVarPrepHTMLDisplay($contact_2).'">'.xarVarPrepHTMLDisplay($contact_2).'</a>';
                }
                break;
            case 2:
                if(!xarModAPIFunc(__ADDRESSBOOK__,'user','is_email',array('email'=>$contact_3))) {
                    if(!xarModAPIFunc(__ADDRESSBOOK__,'user','is_url',array('url'=>$contact_3))) {
                        if (!empty($contact_3)) {
                            $displayRow[] = xarVarPrepHTMLDisplay($contact_3);
                        } else {
                            $displayRow[] = '&nbsp;';
                        }
                    }
                    else {
                        $displayRow[] = '<a href="'.xarVarPrepHTMLDisplay($contact_3).'">'.xarVarPrepHTMLDisplay($contact_3).'</a>';
                    }
                }
                else {
                    $displayRow[] = '<a href="mailto:'.xarVarPrepHTMLDisplay($contact_3).'">'.xarVarPrepHTMLDisplay($contact_3).'</a>';
                }
                break;
            case 3:
                if(!xarModAPIFunc(__ADDRESSBOOK__,'user','is_email',array('email'=>$contact_4))) {
                    if(!xarModAPIFunc(__ADDRESSBOOK__,'user','is_url',array('url'=>$contact_4))) {
                        if (!empty($contact_4)) {
                            $displayRow[] = xarVarPrepHTMLDisplay($contact_4);
                        } else {
                            $displayRow[] = '&nbsp;';
                        }
                    }
                    else {
                        $displayRow[] = '<a href="'.xarVarPrepHTMLDisplay($contact_4).'">'.xarVarPrepHTMLDisplay($contact_4).'</a>';
                    }
                }
                else {
                    $displayRow[] = '<a href="mailto:'.xarVarPrepHTMLDisplay($contact_4).'">'.xarVarPrepHTMLDisplay($contact_4).'</a>';
                }
                break;
            case 4:
                if(!xarModAPIFunc(__ADDRESSBOOK__,'user','is_email',array('email'=>$contact_5))) {
                    if(!xarModAPIFunc(__ADDRESSBOOK__,'user','is_url',array('url'=>$contact_5))) {
                        if (!empty($contact_5)) {
                            $displayRow[] = xarVarPrepHTMLDisplay($contact_5);
                        } else {
                            $displayRow[] = '&nbsp;';
                        }
                    }
                    else {
                        $displayRow[] = '<a href="'.xarVarPrepHTMLDisplay($contact_5).'">'.xarVarPrepHTMLDisplay($contact_5).'</a>';
                    }
                }
                else {
                    $displayRow[] = '<a href="mailto:'.xarVarPrepHTMLDisplay($contact_5).'">'.xarVarPrepHTMLDisplay($contact_5).'</a>';
                }
                break;
            default:
                if(!xarModAPIFunc(__ADDRESSBOOK__,'user','is_email',array('email'=>$contact_1))) {
                    if(!xarModAPIFunc(__ADDRESSBOOK__,'user','is_url',array('url'=>$contact_1))) {
                        if (!empty($contact_1)) {
                            $displayRow[] = xarVarPrepHTMLDisplay($contact_1);
                        } else {
                            $displayRow[] = '&nbsp;';
                        }
                    }
                    else {
                        $displayRow[] = '<a href="'.xarVarPrepHTMLDisplay($contact_1).'">'.xarVarPrepHTMLDisplay($contact_1).'</a>';
                    }
                }
                else {
                    $displayRow[] = '<a href="mailto:'.xarVarPrepHTMLDisplay($contact_1).'">'.xarVarPrepHTMLDisplay($contact_1).'</a>';
                }
                break;

        } // END switch

        $detailargs=array('id'=>$id,
                        'formcall'=>'edit',
                        'authid'=>xarSecGenAuthKey(),
                        'catview'=>$data['catview'],
                        'sortview'=>$data['sortview'],
                        'formSearch'=>urlencode($data['formSearch']),
                        'all'=>$data['all'],
                        'menuprivate'=>$data['menuprivate'],
                        'total'=>$data['total'],
                        'page'=>$data['page'],
                        'char'=>$selChar);

        $data['displayRows'][] = array ('displayRow' => $displayRow
                                       ,'detailURL' => xarModURL(__ADDRESSBOOK__,'user','viewdetail',$detailargs)
                                       ,'detailTXT' => xarVarPrepHTMLDisplay(_AB_LABEL_SHOWDETAIL)
                                       ,'deleteURL' => xarModURL(__ADDRESSBOOK__,'user','confirmdelete',$detailargs)
                                       ,'deleteTXT' => xarVarPrepHTMLDisplay(_AB_LABEL_DELETE)
                                       ,'editURL'   => xarModURL(__ADDRESSBOOK__,'user','insertedit',$detailargs)
                                       ,'editTXT'   => xarVarPrepHTMLDisplay(_AB_LABEL_EDIT)
                                       ,'accessLevel'=> array('option'=>'edit')
                                        );
    } // END for

    $numPages = (($data['total']/$items)+1);
    for($i=1;$i<$numPages;$i++) {
        if ($data['all']==0) {
            $params = array('authid'=>xarSecGenAuthKey(),
                            'sortview'=>$data['sortview'],
                            'catview'=>$data['catview'],
                            'menuprivate'=>$data['menuprivate'],
                            'all'=>$data['all'],
                            'formSearch'=>$formSearch,
                            'total'=>$data['total'],
                            'page'=>$i,
                            'char'=>$selChar);
        }
        else {
            $params = array('authid'=>xarSecGenAuthKey(),
                'sortview'=>$data['sortview'],
                'catview'=>$data['catview'],
                'menuprivate'=>$data['menuprivate'],
                'all'=>$data['all'],
                'formSearch'=>$data['formSearch'], //gehDEBUG - good place to test exception handling
                'total'=>$data['total'],
                'page'=>$i);
        }
        $data['pageNav'][]  = array ('pageURL' => xarModURL(__ADDRESSBOOK__,'user','viewall',$params)
                                    ,'pageNum' => $i
                                    ,'absolutePage' => $result->AbsolutePage());
    } // END for

    return $data;

} // END getAddressList

?>