<?php
/**
 * View responses
 *
 * @package Xaraya
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com
 *
 * @subpackage SiteContact Module
 * @copyright (C) 2004-2008 2skies.com
 * @link http://xarigami.com/project/sitecontact
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */
/**
 * view responses
 */
function sitecontact_admin_view($args)
{
    extract($args);
  
    $defaultform =  (int)xarModGetVar('sitecontact','defaultform');
    // Get parameters
    if(!xarVarFetch('startnum', 'isset', $startnum, 1,    XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('scid',     'int:0:', $scid,     $defaultform, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('itemtype', 'int:0:', $itemtype, NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('responsetime',  'int:0:', $responsetime,  NULL, XARVAR_NOT_REQUIRED)) {return;}
  
    // Default parameters
    if (!isset($scid)) {
        if (!empty($itemtype) && is_numeric($itemtype)) {
            // when we use some categories filter
            $scid = $itemtype;
        } 
    }

    if (!isset($scid) || empty($scid)) {
          $msg = xarML('You need a scid form type and none was provided');
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_DATA',
                            new SystemException($msg));
          return;
    }

    $thisformarray = xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid' => $scid));
    $thisform=$thisformarray[0];

    $scformtypes = xarModAPIFunc('sitecontact','user','getcontacttypes');

    $data = array();
    $data['scid'] = $scid;
    $data['responsetime'] = $responsetime;

    if (empty($scid)) {
        if (!xarSecurityCheck('EditSiteContact',0,'ContactForm',"All:All:All")) {
            $msg = xarML('You have no permission to edit #(1)',
                         'Sitecontact Reponses');
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                            new SystemException($msg));
            return;
        }
    } elseif (!is_numeric($scid) || !isset($scid)) {
        $msg = xarML('Invalid sitecontact form type');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
        return;
    } elseif (!xarSecurityCheck('EditSitecontact',0,'ContactForm',"$scid:All:All")) {
        $msg = xarML('You have no permission to edit #(1)',
                     $thisform['sctypename']);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                        new SystemException($msg));
        return;
    }


    if (xarModGetVar('sitecontact','itemsperpage')) {
        $numitems = xarModGetVar('sitecontact','itemsperpage');
    } else {
        $numitems = 30;
    }
    // Get item information
    $responses = xarModAPIFunc('sitecontact', 'user', 'getall',
                             array('startnum' => $startnum,
                                   'numitems' => $numitems,
                                   'scid'     => (int)$scid));

    // Save the current admin view, so that we can return to it after update
    $lastview = array('scid' => $scid,
                      'responsetime' => $responsetime,
                      'startnum' => $startnum > 1 ? $startnum : null);
    xarSessionSetVar('Sitecontact.LastView',serialize($lastview));

    // the form
    $data['formname']= $thisform['sctypename'];
    $data['scid']    =  $thisform['scid'];
 
    $totalresponses = count($responses);
   
    if ($responses != false) {

        for ($i = 0; $i < $totalresponses; $i++) {
            $response = array();
            $response = $responses[$i];

            if (xarSecurityCheck('EditSiteContact', 0, 'ContactForm', "$response[scid]:All:All")) {
                $responses[$i]['viewurl'] = xarModURL('sitecontact','admin','display', array('scrid' => $response['scrid']));
            } else {
            $responses[$i]['viewurl'] = '';
            }
/* We don't really want to allow editing the user response forms ...
            if (xarSecurityCheck('EditSiteContact', 0, 'ContactForm', "$response[scid]:All:All")) {
                $responses[$i]['editurl'] = xarModURL('sitecontact','admin','modify', array('scrid' => $response['scrid']));
            } else {
            $responses[$i]['editurl'] = '';
          }
*/
            if (xarSecurityCheck('DeleteSiteContact', 0, 'ContactForm', "$response[scid]:All:All")) {
                $responses[$i]['deleteurl'] = xarModURL('sitecontact','admin','delete',array('scrid' => $response['scrid']));
            } else {
                $responses[$i]['deleteurl'] = '';
            }
        }
     /* Add the array of items to the template variables */
    $data['deletetitle'] = xarML('Delete');
    $data['edittitle'] = xarML('Edit');
    $data['viewtitle'] = xarML('View');
  
    $data['responses'] = $responses;
    $data['totalresponses'] = $totalresponses;
    } else {
        $data['responses']='';
    }


    // Add pager
    $data['pager'] = xarTplGetPager($startnum,
                            xarModAPIFunc('sitecontact', 'user', 'countresponses',
                                          array('scid' => $scid,
                                                'responsetime' => $responsetime,
                                                )),
                            xarModURL('sitecontact', 'admin', 'view',
                                      array('startnum' => '%%',
                                            'scid' => $scid,
                                            'responsetime' => $responsetime)),
                                            $numitems);

    // Create filters based on publication type
    $formfilters = array();
    foreach ($scformtypes as $id => $formtype) {
        if (!xarSecurityCheck('EditSiteContact',0,'ContactForm',"$formtype[scid]:All:All")) {
            continue;
        }
        $responseitem = array();
        if ($formtype['scid'] == $scid) {
            $responseitem['flink'] = '';
            $responseitem['current']=false;
        } else {
            $responseitem['flink'] = xarModURL('sitecontact','admin','view',
                                         array('scid' => $formtype['scid']));
            $responseitem['current']=false;
        }
        $responseitem['ftitle'] = $formtype['sctypename'];
        $formfilters[] = $responseitem;
    }
    $data['formfilters'] = $formfilters;

    xarVarSetCached('Blocks.sitecontact','itemtype',$scid);
    if (!empty($scid) && !empty($formtypes[$scid]['sctypename'])) {
        xarVarSetCached('Blocks.sitecontact','formname',$formtypes[$scid]['sctypename']);
    }

    return $data;
}
?>