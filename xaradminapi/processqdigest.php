<?php
/**
 * Pubsub module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Pubsub Module
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 */
/**
 * Process the queue and run all pending jobs (executed by the scheduler module)
 * @return mixed number of jobs run on success, false if not
 * @throws DATABASE_ERROR
 */
function pubsub_adminapi_processqdigest($args)
{
    // Get arguments from argument array
    extract($args);

    // Database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Get the wrapper template
    $pubsubtemplatestable = $xartable['pubsub_templates'];
    $query = "SELECT xar_compiled
              FROM $pubsubtemplatestable
              WHERE xar_name= 'wrapper'";
    $result   = $dbconn->Execute($query);
    if (!$result) $compiled ='<?php echo $contents; ?>';
    $compiled = $result->fields[0];

    // Get all jobs in pending state
    $pubsubprocesstable = $xartable['pubsub_process'];
    $query = "SELECT xar_handlingid,
                     xar_pubsubid,
                     xar_objectid,
                     xar_templateid
              FROM $pubsubprocesstable
              WHERE xar_status = 'pending'";
    $result = $dbconn->Execute($query);
    if (!$result) return;

    // set count to 1 so that the scheduler knows we're doing OK :)
    $count = 1;
    $digest = array();
    $name = array();
    $handle = array();
    $handlecount = array();
    $handleverify = array();

    // now start building the digest
    while (!$result->EOF) {
        list($handlingid,$pubsubid,$objectid,$templateid) = $result->fields;
        // run the job passing it the handling, pubsub and object ids.
        $message= xarModAPIFunc('pubsub','admin','runjobdigest',
                      array('handlingid' => $handlingid,
                            'pubsubid' => $pubsubid,
                            'objectid' => $objectid,
                            'templateid' => $templateid));
        if (!isset($digest[$message['email']])) {
            $digest[$message['email']] = $message['content'] ;
        } else {
            $digest[$message['email']] .= "\n\n".$message['content'];
        }
        if (!isset($name[$message['email']])) {
            $name[$message['email']] = $message['name'];
        }
//      $handle[$message['email']][] = $handlingid;
        if (!isset($handlecount[$handlingid])) {
            $handlecount[$handlingid] = 1;
        } else {
            $handlecount[$handlingid]++ ;
        }
        $count++;
        $result->MoveNext();
    }

    $fmail = xarConfigGetVar('adminmail');
    $fname = xarConfigGetVar('adminmail');
    $sitename = xarModGetVar('themes','SiteName');
    $subject = xarML('New articles from').' '.$sitename;

    foreach ($digest as $email => $content) {

    $tplData = array();
    $tplData['contents'] = $content;

    $html = xarTplString($compiled, $tplData);
    $plaintext = strip_tags($html);
        if (!xarModAPIFunc('mail',
                           'admin',
                           'sendmail',
                           array('info'     => $email,
                                 'name'     => $name[$email],
                                 'subject'  => $subject,
                                 'message'  => $plaintext,
                                 'from'     => $fmail,
                                 'fromname' => $fname))) return;
        /*
        foreach($handle[$email] as $key=>$value) {
            if (!isset($handleverify[$handlingid])) {
                $handleverify[$value] = 1;
            } else {
                $handleverify[$value]++;
            }
        }
        */
    }
    foreach ($handlecount as $handlingid=> $value) {
//      if ($value = $handleverify[$handlingid]) {
           xarModAPIFunc('pubsub','admin','deljob',
                         array('handlingid' => $handlingid));
//        }
    }
    return $count;

} // END processq

?>
