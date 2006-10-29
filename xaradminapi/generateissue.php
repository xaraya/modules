<?php
/**
* Generate the body of an issue
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
* Generate the body of an issue
*
* @param date    $args['today'] (Optional, format=YYYY-MM-DD, default=today) Date of issue
* @param integer $args['startday'] (Optional,default=0) Offset from $args['today'] to start of
*                                  date range.  Negative numbers indicate days in the past.
* @param integer $args['endday'] (Optional,default=0) Offset from $args['today'] to end of date
*                                range.  Negative numbers indicate days in the past.
* @param string  $args['defaulttheme'] (Optional,default=system theme) Theme to use while
*                                     generating message body
* @param string  $args['subject'] (Optional,default=blank) Message subject
* @param string  $args['template_html'] (Optional,default=theme or module default template for
*                                      ebulletin_admin_publication()) Template to use for
*                                      generating HTML message body
* @param string  $args['template_txt'] (Optional,default=theme or module default template for
*                                     ebulletin_admin_publication()) Template to use for
*                                     generating text message body
* @param mixed   $args['...'] (Optional) All args will be passed straight to template
* @param return array
*/
function ebulletin_adminapi_generateissue($args)
{
    extract($args);

    // set defaults
    if (!isset($today))         $today    = date('Y-m-d');
    if (!isset($startday))      $startday = 0;
    if (!isset($endday))        $endday   = 7;
    if (!isset($subject))       $subject   = '';
    if (!isset($defaulttheme))  $defaulttheme  = '';
    if (!isset($template_html)) $template_html = '';
    if (!isset($template_txt))  $template_txt  = '';

    // validate vars
    $invalid = array();
    if (!empty($id) && !is_numeric($id)) {
        $invalid[] = 'issue ID';
    }
    if (empty($today) ||
        !preg_match("/^\d\d\d\d-\d\d-\d\d\$/", $today) ||
        strtotime($today) <= 0) {
        $invalid[] = 'issue date';
    }
    if (!is_numeric($startday)) {
        $invalid[] = 'start day';
    }
    if (!is_numeric($endday)) {
        $invalid[] = 'end day';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'adminapi', 'getissuebody', 'eBulletin');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return false;
    }

    // work out date ranges
    $startsign = (substr($startday, 0, 1) == '') ? '+' : '';
    $endsign   = (substr($endday,   0, 1) == '') ? '+' : '';
    $startdate = date("Y-m-d", strtotime("$today $startsign$startday days"));
    $enddate   = date("Y-m-d", strtotime("$today $endsign$endday days"));

    // do replacements on subject
    // TODO: evaluate what replacements we want (add issue number)
    $today_unix = strtotime($today);
    $trans = array(
        '%shortdate%'  => xarLocaleGetFormattedDate('short', $today_unix),
        '%mediumdate%' => xarLocaleGetFormattedDate('medium', $today_unix),
        '%longdate%'   => xarLocaleGetFormattedDate('long', $today_unix),
    );
    $subject = strtr($subject, $trans);

    // assemble template data
    $data = $args;
    $data['startdate'] = $startdate;
    $data['enddate']   = $enddate;
    $data['subject']   = $subject;

    // set the preferred theme
    if (!empty($defaulttheme)) {
        $origtheme = xarTplGetThemeName();
        xarTplSetThemeName($defaulttheme);
    }

    // get the page output
    $body_html = xarTplModule('ebulletin', 'admin', 'publication', $data, $template_html);
    $body_txt  = xarTplModule('ebulletin', 'admin', 'publication', $data, $template_txt);

    // restore the theme
    if (!empty($defaulttheme)) xarTplSetThemeName($origtheme);

    return array($subject, $body_html, $body_txt);
}

?>
