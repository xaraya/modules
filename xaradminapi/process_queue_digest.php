<?php
/**
 * Pubsub Module
 *
 * @package modules
 * @subpackage pubsub module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Process the queue and run all pending jobs (executed by the scheduler module)
 * @return mixed number of jobs run on success, false if not
 * @throws DATABASE_ERROR
 */
function pubsub_adminapi_process_queue_digest($args)
{
    // Get arguments from argument array
    extract($args);

    // Database information
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();

    // Get the wrapper template
    $pubsubtemplatestable = $xartable['pubsub_templates'];
    $query = "SELECT compiled
              FROM $pubsubtemplatestable
              WHERE name= 'wrapper'";
    $result   = $dbconn->Execute($query);
    if (!$result) $compiled ='<?php echo $contents; ?>';
    $compiled = $result->fields[0];

    // Get all jobs in pending state
    $pubsubprocesstable = $xartable['pubsub_process'];
    $query = "SELECT id,
                     pubsub_id,
                     object_id,
                     template_id
              FROM $pubsubprocesstable
              WHERE status = 'pending'";
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
        list($id,$pubsub_id,$object_id,$template_id) = $result->fields;
        // run the job passing it the handling, pubsub and object ids.
        $message= xarMod::apiFunc('pubsub','admin','runjobdigest',
                      array('id' => $id,
                            'pubsub_id' => $pubsub_id,
                            'object_id' => $object_id,
                            'template_id' => $template_id));
        if (!isset($digest[$message['email']])) {
            $digest[$message['email']] = $message['content'] ;
        } else {
            $digest[$message['email']] .= "\n\n".$message['content'];
        }
        if (!isset($name[$message['email']])) {
            $name[$message['email']] = $message['name'];
        }
//      $handle[$message['email']][] = $id;
        if (!isset($handlecount[$id])) {
            $handlecount[$id] = 1;
        } else {
            $handlecount[$id]++ ;
        }
        $count++;
        $result->MoveNext();
    }

    $fmail = xarModVars::get('role', 'adminmail');
    $fname = xarModVars::get('role', 'adminmail');
    $sitename = xarModVars::get('themes','SiteName');
    $subject = xarML('New articles from').' '.$sitename;

    foreach ($digest as $email => $content) {

    $tplData = array();
    $tplData['contents'] = $content;

    $html = xarTpl::string($compiled, $tplData);
    $plaintext = strip_tags($html);
        if (!xarMod::apiFunc('mail',
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
            if (!isset($handleverify[$id])) {
                $handleverify[$value] = 1;
            } else {
                $handleverify[$value]++;
            }
        }
        */
    }
    foreach ($handlecount as $id=> $value) {
//      if ($value = $handleverify[$id]) {
           xarMod::apiFunc('pubsub','admin','deljob',
                         array('id' => $id));
//        }
    }
    return $count;

} // END processq

?>