<?php
/**
 * File: $Id$
 *
 * XarLDAP Administration
 * 
 * @package authentication
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage xarldap
 * @author Richard Cave <rcave@xaraya.com>
*/

/**
 * xarldap_admin_search: 
 *
 * Search an LDAP server
 *
 * @author  Richard Cave <rcave@xaraya.com>
 * @access  public
 * @param   none 
 * @return  returns true on success or false on failure
 * @throws  none
 * @todo    display results better
*/
function xarldap_admin_search()
{
    // Security check
    if(!xarSecurityCheck('AdminXarLDAP')) return;

    // Get input parameters
    if (!xarVarFetch('search', 'str:1:', $search, 'user')) return;
    if (!xarVarFetch('value', 'str:1:', $value, '')) return;

    $result = xarModAPIFunc('xarldap',
                            'user',
                            'search',
                            array('search' => $search,
                                  'value' => $value));
    
    // Initialise data array
    $data = array();
    $data['search'] = $search;
    $data['value'] = $value;

    // Create template title
    switch ($search) {
        case 'user':
            $data['title'] = xarML('User Search');
            if ($result) {
                // Get the resulting user info array and parse into xml
                $xml = xarldap_array_to_xml($result);
                $data['result'] = $xml;
            } else {
error_log("HERE");
                $data['result'] = false;
            }
            break;

        default:
            $data['title'] = xarML('Unknown Search');
            $data['result'] = false;
            break;
    }

    // Return the template variables defined in this function
    return $data;
}


/**
 * xarldap_array_to_xml
 *
 * Convert multi-dimenionsal array into an XML tree 
 *
 * @author  Gijs van Tulder
 * @access  private
 * @param   none 
 * @return  returns xml tree 
 * @throws  none
 * @todo    none
*/
function xarldap_array_to_xml($array, $level=1) 
{
    $xml = '';
    if ($level==1) {
        $xml .= '<?xml version="1.0" encoding="ISO-8859-1"?>'.
                "<br /><array><br />";
    }
    foreach ($array as $key=>$value) {
        $key = strtolower($key);
        if (is_array($value)) {
            $multi_tags = false;
            foreach($value as $key2=>$value2) {
                if (is_array($value2)) {
                    $xml .= str_repeat("&nbsp;",$level)."<$key><br />";
                    $xml .= xarldap_array_to_xml($value2, $level+1);
                    $xml .= str_repeat("&nbsp;",$level)."</$key><br />";
                    $multi_tags = true;
                } else {
                    if (trim($value2)!='') {
                        if (htmlspecialchars($value2)!=$value2) {
                            $xml .= str_repeat("&nbsp;",$level).
                                    "<$key><![CDATA[$value2]]>".
                                    "</$key><br />";
                        } else {
                            $xml .= str_repeat("&nbsp;",$level).
                                    "<$key>$value2</$key><br />";
                        }
                    }
                    $multi_tags = true;
                }
            }
            if (!$multi_tags and count($value)>0) {
                $xml .= str_repeat("&nbsp;",$level)."<$key><br />";
                $xml .= xarldap_array_to_xml($value, $level+1);
                $xml .= str_repeat("&nbsp;",$level)."</$key><br />";
            }
        } else {
            if (trim($value)!='') {
                if (htmlspecialchars($value)!=$value) {
                    $xml .= str_repeat("&nbsp;",$level)."<$key>".
                            "<![CDATA[$value]]></$key><br />";
                } else {
                    $xml .= str_repeat("&nbsp;",$level).
                            "<$key>$value</$key><br />";
                }
            }
        }
    }
    if ($level==1) {
        $xml .= "</array><br />";
    }
    return $xml;
}

?>
