<?php
/**
 * AddressBook user getAddressList
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {http://www.gnu.org/licenses/gpl.html}
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
function addressbook_userapi_getAddressList($args)
{

    extract($args);

    // Get the menu values
    $menuValues = xarModAPIFunc('addressbook','user','getmenuvalues');
    foreach ($menuValues as $key=>$value) {
        $output[$key] = $value;
    }

    // SQL Query
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();
    $address_table = $xarTables['addressbook_address'];

    // Note Searchorder
    $output['sql'] = "SELECT * FROM $address_table";
//    if ($output['sortview'] != 1) {
//        $sort_1 = xarModGetVar('addressbook', 'sortorder_1');
//        $output['sql'] = "SELECT *, CONCAT($sort_1) AS listname FROM $address_table";
//    }
//    else {
//        $sort_2 = xarModGetVar('addressbook', 'sortorder_2');
//        $output['sql'] = "SELECT *, CONCAT($sort_2) AS listname FROM $address_table";
//    }

    // Get user id
    if (xarUserIsLoggedIn()) { $user_id = xarUserGetVar('uid');}
    else {$user_id = 0;}
    $output['user_id'] = $user_id;

    // Private Contacts only
    // if globalprotect, only records of the user are shown
    if (((xarModGetVar('addressbook', 'globalprotect'))==1) && (!xarSecurityCheck('EditAddressBook',0))) {
        $output['sql'] .= " WHERE (user_id=$user_id)";
    }
    else {
        // if private = 1, show only private records
        if ($output['menuprivate'] == 1) {
            // Admins always see all records
            if (xarSecurityCheck('EditAddressBook',0)) {
                $output['sql'] .= " WHERE (user_id=$user_id)";
            }
            else {
                $output['sql'] .= " WHERE (user_id=$user_id AND private = 1)";
            }
        }
        else {
            // Admins always see all records
            if (xarSecurityCheck('EditAddressBook',0)) {
                $output['sql'] .= " WHERE (user_id IS NOT NULL)";
            }
            else {
                // if private = 0, show all records
                $output['sql'] .= " WHERE ((private = 0) OR (user_id=$user_id AND private = 1))";
            }
        }
    }

    // Filter Categories
    if ($output['catview']) {
        $output['sql'] .= " AND (cat_id = ".$output['catview'].")";
    }

    // A-Z
    if ($output['all'] == 0) {
        if ($output['sortview'] != 1) {
            $sortCols = explode(',',xarModGetVar('addressbook', 'sortorder_1'));
        }
        else {
            $sortCols = explode(',',xarModGetVar('addressbook', 'sortorder_2'));
        }
        if ($sortCols[0] == 'sortname') {
            if ($output['char']) {
                if (strcasecmp($output['char'],'a')) {
                    $output['sql'] .= " AND (sortname LIKE '".$output['char']."%')";
                } else {
                    $output['sql'] .= " AND ((sortname < 'b') OR (sortname > 'z'))";
                }
            } else {
                $output['sql'] .= " AND ((sortname < 'b') OR (sortname > 'z'))";
            }
        }
        else {
            if ($sortCols[0] == 'sortcompany') {
                if ($output['char']) {
                    if (strcasecmp($output['char'],'a')) {
                        $output['sql'] .= " AND (sortcompany LIKE '".$output['char']."%')";
                    } else {
                        $output['sql'] .= " AND ((sortcompany < 'b') OR (sortcompany > 'Z'))";
                    }
                } else {
                    $output['sql'] .= " AND ((sortcompany < 'b') OR (sortcompany > 'Z'))";
                }
            }
            else {
                if ($output['char']) { $output['sql'] .= " AND (".$sortCols[0]." LIKE '".$output['char']."%')"; }
                else { $output['sql'] .= " AND (".$sortCols[0]." LIKE 'A%')"; }
            }
        }
    }

    // Retrieve all the custom fields, we use this throughout.
    $custFields = xarModAPIFunc('addressbook','user','getcustfieldinfo',array('flag'=>_AB_CUST_ALLFIELDINFO));

    // Search
    if ($output['formSearch']) {
        $output['sql'] .= " AND (lname LIKE '%".$output['formSearch']."%'
                  OR fname LIKE '%".$output['formSearch']."%'
                  OR company LIKE '%".$output['formSearch']."%'
                  OR title LIKE '%".$output['formSearch']."%'
                  OR city LIKE '%".$output['formSearch']."%'
                  OR address_1 LIKE '%".$output['formSearch']."%'
                  OR address_2 LIKE '%".$output['formSearch']."%'
                  OR zip LIKE '%".$output['formSearch']."%'
                  OR country LIKE '%".$output['formSearch']."%'
                  OR state LIKE '%".$output['formSearch']."%'
                  OR note LIKE '%".$output['formSearch']."%'
                  OR contact_1 LIKE '%".$output['formSearch']."%'
                  OR contact_2 LIKE '%".$output['formSearch']."%'
                  OR contact_3 LIKE '%".$output['formSearch']."%'
                  OR contact_4 LIKE '%".$output['formSearch']."%'
                  OR contact_5 LIKE '%".$output['formSearch']."%')";

        foreach($custFields as $custField) {
            if ((!strstr($custField['custType'],_AB_CUSTOM_BLANKLINE)) && (!strstr($custField['custType'],_AB_CUSTOM_HORIZ_RULE))) {
                if (strstr($custField['custType'],_AB_CUST_TEST_STRING)) {
                    $output['sql'] .= " OR ".$custField['colName']." LIKE '%".$output['formSearch']."%'";
                }
            }
        }
    }

    // Sort
    if ($output['sortview'] != 1) {
        $sortCols = explode(",",xarModGetVar('addressbook', 'sortorder_1'));
    }
    else {
        $sortCols = explode(",",xarModGetVar('addressbook', 'sortorder_2'));
    }
    $output['sql'] .= " ORDER BY ";
    foreach ($sortCols as $sortCol) {
        $output['sql'] .= $sortCol.",";
    }
    $output['sql'] = rtrim($output['sql'],",");
    $output['sql'] .= " ASC";

    if (!$output['total']) {
        $numRec =& $dbconn->Execute($output['sql']);
        $output['total'] = $numRec->RecordCount();
        $output['page'] = 1;
        $numRec->Close();
    }

    if (!$output['total']) {
        xarErrorSet(XAR_USER_EXCEPTION, _AB_ERR_INFO, new abUserException(xarML('There are no records to show in this view'))); //gehDEBUG
    }

    $items = xarModGetVar('addressbook', 'itemsperpage');
    $result =& $dbconn->PageExecute($output['sql'],$items,$output['page']);

    if ($dbconn->ErrorNo() != 0) {
        xarErrorSet(XAR_USER_EXCEPTION, _AB_ERR_ERROR, new abUserException("sql = ".$output['sql']));
    }

    //Show Result

    // A-Z Navigation
    /**
     * These vars are not displayed / do not need to go in $output
     */
    $selChar = ((isset($output['char'])) ? $output['char'] : '');
    $numPages = (($output['total']/$items)+1);

    $output['azLinks'] = array();
    if ($output['all']==0) {
        $numPages = (($output['total']/$items)+1);
        for($i=65;$i<=90;$i++) {
            $azLink = '';
            $char = chr($i);
            $params = array('authid'=>xarSecGenAuthKey(),
                            'sortview'=>$output['sortview'],
                            'catview'=>$output['catview'],
                            'menuprivate'=>$output['menuprivate'],
                            'all'=>$output['all'],
                            'char'=>$char);

            $pageURL = xarModURL('addressbook','user','main',$params);
            if ($i != 65) {
                $azLink .= ' | ';
            }
            if ($char == $selChar) {
                $azLink .= '<b><u>'.$char.'</u></b>';
            } else {
                $azLink .= "<a href=\"".$pageURL."\">".$char."</a>";
            }
            $output['azLinks'][]['azLink'] = $azLink;
        }
    }
    // END A-Z Navigation

    // No Records found!
    if ($output['total'] < 1) {
        return $output;
    }

    /**
     * Get the title of each column to be displayed.
     */
    if ($output['sortview'] != 1) {
        $output['headers'] = xarModAPIFunc('addressbook','user','getlistheader',array('sort'=>1));
    }
    else {
        $output['headers'] = xarModAPIFunc('addressbook','user','getlistheader',array('sort'=>2));
    }

    /**
     * Get the prefix decodes if we are to display them
     */
    $prefixes = array();
    if (xarModGetVar('addressbook', 'display_prefix')) {
        $prefixes = xarModAPIFunc('addressbook','util','getitems',array('tablename'=>'prefixes'));
    }

    $abData = array('id'            => ''
                   ,'cat_id'        => ''
                   ,'prefix'        => ''
                   ,'lname'         => ''
                   ,'fname'         => ''
                   ,'sortname'      => ''
                   ,'title'         => ''
                   ,'company'       => ''
                   ,'sortcompany'   => ''
                   ,'img'           => ''
                   ,'zip'           => ''
                   ,'city'          => ''
                   ,'address_1'     => ''
                   ,'address_2'     => ''
                   ,'state'         => ''
                   ,'country'       => ''
                   ,'contact_1'     => ''
                   ,'contact_2'     => ''
                   ,'contact_3'     => ''
                   ,'contact_4'     => ''
                   ,'contact_5'     => ''
                   ,'c_label_1'     => ''
                   ,'c_label_2'     => ''
                   ,'c_label_3'     => ''
                   ,'c_label_4'     => ''
                   ,'c_label_5'     => ''
                   ,'c_main'        => ''
                   ,'custom_1'      => ''
                   ,'custom_2'      => ''
                   ,'custom_3'      => ''
                   ,'custom_4'      => ''
                   ,'note'          => ''
                   ,'user'          => ''
                   ,'private'       => ''
                   ,'last_updt'     => '');


    foreach($custFields as $custField) {
        $abData[$custField['colName']] = '';
    }


    // Retrieve all records and format as needed for display. The 'searchResults' var is temp
    // only and 'displayRows' is used be the template to display the data
    for (; !$result->EOF; $result->MoveNext()) {
        $index = 0;
        foreach ($abData as $key=>$value) {
            $abData[$key] = $result->fields[$index++];
        }
        // Only doing this step because I'm too lazy to change all the variable refs to the $abData. I use it as a temp holding place to build in all the custom
        // fields
        extract ($abData);

        $displayRow = array();
        $output['searchResults'][] = $abData;

        /* not sure what this does gehDEBUG
//        $cus_fields = xarModAPIFunc('addressbook','user','customfieldinformation',array('id'=>$id));
        $i=1;
        foreach($cus_fields as $cus) {
            if ($cus['type']=='date default NULL') {
                $cus['value'] = xarModAPIFunc('addressbook','user','stamp2date',array('idate'=>$cus['value']));
            }
            $the_name = 'custom_'.$i;
            $$the_name = $cus['value'];
            $i++;
        } */

        /*
         * Step 1
         */
        if ($output['sortview'] != 1) {
            $sortCols = explode(',',xarModGetVar('addressbook', 'sortorder_1'));
        }
        else {
            $sortCols = explode(',',xarModGetVar('addressbook', 'sortorder_2'));
        }

        /*
         * Step 2
         */

        $displayName = '';
        if ($sortCols[0] == 'sortname') {
            if ((!empty($fname) && !empty($lname)) ||
                (!empty($fname) || !empty($lname))) {
                if (xarModGetVar('addressbook', 'name_order')==_AB_NO_FIRST_LAST) {
                    if (!empty($prefixes) && $prefix > 0) {
                        $displayName .= $prefixes[$prefix-1]['name'].' ';
                    }
                    $displayName .= xarVarPrepHTMLDisplay($fname).' '.xarVarPrepHTMLDisplay($lname);
                } else {
                    if (!empty($lname)) {
                        $displayName .= xarVarPrepHTMLDisplay($lname).', ';
                    }
                    if (!empty($prefixes) && $prefix > 0) {
                        $displayName .= $prefixes[$prefix-1]['name'].' ';
                    }
                    $displayName .= xarVarPrepHTMLDisplay($fname);
                }
            }
            else {
                if (!empty($company)) {
                    $displayName .= xarVarPrepHTMLDisplay($company);
                }
            }
        }
        else {
            if ($sortCols[0] == 'sortcompany') {
                $displayName .= xarVarPrepHTMLDisplay($company);
            } else {
                $displayName .= xarVarPrepHTMLDisplay($$sortCols[0]);
            }
        }
        
        $displayName = '<a href="'
                      .xarModURL('addressbook','user','viewdetail',array('id'=>$id))
                      .'">'.$displayName.'</a>';

        $displayRow[] = trim($displayName,",");

        /*
         * Step 3
         */
        if ($sortCols[1] == 'sortname') {
            if (xarModGetVar('addressbook', 'name_order')==1) {
                if ((!empty($fname)) && (!empty($lname))) {
                    $displayRow[] = '<a href="'
                                  .xarModURL('addressbook','user','viewdetail',array('id'=>$id))
                                  .'">'.xarVarPrepHTMLDisplay($fname).' '.xarVarPrepHTMLDisplay($lname).'</a>';
                } else {
                    $displayRow[] = '<a href="'
                                  .xarModURL('addressbook','user','viewdetail',array('id'=>$id))
                                  .'">'.xarVarPrepHTMLDisplay($fname).xarVarPrepHTMLDisplay($lname).'</a>';
                }
            }
            else {
                if ((!empty($lname)) && (!empty($fname))) {
                    $displayRow[] = '<a href="'
                                  .xarModURL('addressbook','user','viewdetail',array('id'=>$id))
                                  .'">'.xarVarPrepHTMLDisplay($lname).', '.xarVarPrepHTMLDisplay($fname).'</a>';
                } else {
                    $displayRow[] = '<a href="'
                                  .xarModURL('addressbook','user','viewdetail',array('id'=>$id))
                                  .'">'.xarVarPrepHTMLDisplay($lname).xarVarPrepHTMLDisplay($fname).'</a>';
                }
            }
        }
        else {
            if ($sortCols[1] == 'sortcompany') {
                if (!empty($company)) {
                    $displayRow[] = '<a href="'
                                  .xarModURL('addressbook','user','viewdetail',array('id'=>$id))
                                  .'">'.xarVarPrepHTMLDisplay($company).'</a>';
                } else {
                    $displayRow[] = '&nbsp;';
                }
            } else {
                $displayRow[] = xarVarPrepHTMLDisplay($$sortCols[1]);
            }
        }

        /*
         * Step 4 - Check for any custom fields to display
         */
        foreach($custFields as $custField) {
            if ($custField['custDisplay']) {
                switch ($custField['custType']) {
                    case 'int(1) default NULL':
                        if ($$custField['colName']) {
                            $displayRow[] = '<acronym title="'.$custField['custLabel'].'">'.$custField['custShortLabel'].'</acronym>';
                        } else {
                            $displayRow[] = "&nbsp;";
                        }
                        break;
                    default:
                        $displayRow[] = $$custField['colName'];
                        break;
                }
            }
        }

        /*
         * Step 5 - Format Contact information
         */
        switch($c_main) {
            case 0:
                if(!xarModAPIFunc('addressbook','util','is_email',array('email'=>$contact_1))) {
                    if(!xarModAPIFunc('addressbook','util','is_url',array('url'=>$contact_1))) {
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
                if(!xarModAPIFunc('addressbook','util','is_email',array('email'=>$contact_2))) {
                    if(!xarModAPIFunc('addressbook','util','is_url',array('url'=>$contact_2))) {
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
                if(!xarModAPIFunc('addressbook','util','is_email',array('email'=>$contact_3))) {
                    if(!xarModAPIFunc('addressbook','util','is_url',array('url'=>$contact_3))) {
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
                if(!xarModAPIFunc('addressbook','util','is_email',array('email'=>$contact_4))) {
                    if(!xarModAPIFunc('addressbook','util','is_url',array('url'=>$contact_4))) {
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
                if(!xarModAPIFunc('addressbook','util','is_email',array('email'=>$contact_5))) {
                    if(!xarModAPIFunc('addressbook','util','is_url',array('url'=>$contact_5))) {
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
                if(!xarModAPIFunc('addressbook','util','is_email',array('email'=>$contact_1))) {
                    if(!xarModAPIFunc('addressbook','util','is_url',array('url'=>$contact_1))) {
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
                        'catview'=>$output['catview'],
                        'sortview'=>$output['sortview'],
                        'formSearch'=>urlencode($output['formSearch']),
                        'all'=>$output['all'],
                        'menuprivate'=>$output['menuprivate'],
                        'total'=>$output['total'],
                        'page'=>$output['page'],
                        'char'=>$selChar);

        //FIXME:<garrett> sloppy way of of setting up data. Redundant vars (accessLevel, *TEXT..)
        $output['displayRows'][] = array ('displayRow' => $displayRow
                                         ,'user'    => $user
                                       ,'detailURL' => xarModURL('addressbook','user','viewdetail',$detailargs)
                                       ,'detailTXT' => xarML('Details')
                                       ,'deleteURL' => xarModURL('addressbook','user','confirmdelete',$detailargs)
                                       ,'deleteTXT' => xarML('Delete')
                                       ,'editURL'   => xarModURL('addressbook','user','insertedit',$detailargs)
                                       ,'editTXT'   => xarML('Edit')
                                       ,'accessLevel'=> array('option'=>'edit')
                                        );
    } // END for $results

    $numPages = (($output['total']/$items)+1);
    for($i=1;$i<$numPages;$i++) {
        if ($output['all']==0) {
            $params = array('authid'=>xarSecGenAuthKey(),
                            'sortview'=>$output['sortview'],
                            'catview'=>$output['catview'],
                            'menuprivate'=>$output['menuprivate'],
                            'all'=>$output['all'],
                            'formSearch'=>$output['formSearch'],
                            'total'=>$output['total'],
                            'page'=>$i,
                            'char'=>$selChar);
        }
        else {
            $params = array('authid'=>xarSecGenAuthKey(),
                'sortview'=>$output['sortview'],
                'catview'=>$output['catview'],
                'menuprivate'=>$output['menuprivate'],
                'all'=>$output['all'],
                'formSearch'=>$output['formSearch'], //gehDEBUG - good place to test exception handling
                'total'=>$output['total'],
                'page'=>$i);
        }
        $output['pageNav'][]  = array ('pageURL' => xarModURL('addressbook','user','viewall',$params)
                                    ,'pageNum' => $i
                                    ,'absolutePage' => $result->AbsolutePage());
    } // END for

    return $output;

} // END getAddressList

?>
