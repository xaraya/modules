<?php
/**
* Generate an issue
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Generate an issue
*
* We use BlockLayout to generate the HTML and/or TXT body of the issue.  But first,
* we need to create appropriate date strings and other values that should be
* available to the template
*
* @params...........
subject optional
htmltemplate optional
txttemplate optional
themename optional
issuedate
startdate
enddate
*/
function ebulletin_adminapi_generateissue($args)
{
    extract($args);

    if (!isset($subject)) $subject = '';
    if (!isset($publication)) $publication = array();

    // validate vars
    $invalid = array();
    if (empty($issuedate) || !preg_match("/\d\d\d\d-\d\d-\d\d/", $issuedate)) {
        $invalid[] = 'issue date';
    }
    if (!empty($htmltemplate) && !file_exists($htmltemplate)) {
        $invalid[] = 'HTML template';
    }
    if (!empty($txttemplate) && !file_exists($txttemplate)) {
        $invalid[] = 'TXT template';
    }
    if (empty($startdate) || !is_numeric($startdate)) {
        $invalid[] = 'start date';
    }
    if (empty($enddate) || !is_numeric($enddate)) {
        $invalid[] = 'end date';
    }
    if (empty($issueid) || !is_numeric($issueid)) {
        $invalid[] = 'issue ID';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'adminapi', 'generateissue', 'eBulletin');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // generate dates for template
    $date = array();
    $date['ymd'] = $issuedate;
    $date['unix'] = strtotime($issuedate);
    $date['short'] = xarLocaleGetFormattedDate('short', $date['unix']);
    $date['medium'] = xarLocaleGetFormattedDate('medium', $date['unix']);
    $date['long'] = xarLocaleGetFormattedDate('long', $date['unix']);
    $date['start_unix'] = $startdate;
    $date['start_ymd'] = date('Y-m-d', $date['start_unix']);
    $date['end_unix'] = $enddate;
    $date['end_ymd'] = date('Y-m-d', $date['end_unix']);

    // do regexps on subject if we're given one
    if (!empty($subject)) {
        foreach ($date as $datetype => $datevalue) {
            $subject = str_replace("%$datetype%", $datevalue, $subject);
        }
    }
    $subject = str_replace("%issueid%", $issueid, $subject);

    // initialize template vars
    $data = array();

    // set template vars and add additional vars
    $data['date'] = $date;
    $data['subject'] = $subject;
    $data['publication'] = $publication;
    $data['issueid'] = $issueid;
    $data['sitename'] = xarModGetVar('themes', 'SiteName');
    $data['siteslogan'] = xarModGetVar('themes', 'SiteSlogan');
    $data['footer'] = xarModGetVar('themes', 'SiteFooter');
    $data['copyright'] = xarModGetVar('themes', 'SiteCopyRight');
    $data['adminname'] = xarModGetVar('mail', 'adminname');
    $data['adminemail'] = xarModGetVar('mail', 'adminemail');

    // swap themes while we generate newsletter
    $current_theme = xarTplGetThemeName();
    xarTplSetThemeName($themename);

    // get contents of template files
    $htmlstring = file_get_contents($htmltemplate);
    $txtstring = file_get_contents($txttemplate);

    // generate newsletter, avoiding fatal errors
    $html = empty($htmltemplate) ? '' : xarTplString(xarTplCompileString($htmlstring), $data);
    $txt = empty($txttemplate) ? '' : xarTplString(xarTplCompileString($txtstring), $data);

    // put theme back
    xarTplSetThemeName($current_theme);

    return array($subject, $html, $txt);
}

?>
