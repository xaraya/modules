<?php
/**
 * Newsletter
 *
 * @package modules
 * @copyright (C) 2004 by the Xaraya Development Team
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
 */
/**
 * Import alternative subscriptions into Newsletter
 *
 * @public
 * @author Richard Cave
 * @param string 'import' the CSV separate import
 * @param string 'delim' the delimitor (assuming ',')
 * @param array  'pids' the publication ids
 * @param int    'htmlmail' send mail html or text (0 = text, 1 = html)
 * @return array $data
 */
function newsletter_admin_importaltsubscription()
{
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for creating new #(1) item', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // Get parameters from the input
    if (!xarVarFetch('import', 'str:1:', $import, '')) return;
    if (!xarVarFetch('delim', 'str:1:2', $delim, ',')) return;

    if (!xarVarFetch('pids', 'array:1:', $pids)) {
        xarErrorFree();
        $msg = xarML('You must select at least one publication.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    if (!xarVarFetch('htmlmail', 'int:0:1:', $htmlmail, 0)) return;

    // Windows browsers insert '\r\n' but other platforms have their
    // own line endings.  Most use '\n' - so replace '\r'.
    $import = str_replace("\r", null, $import);

    // Explode text area into separate strings
    $importstrings = explode("\n", trim($import));

    // Parse import into altsubscriptions
    foreach ($importstrings as $importstring)
    {
        // Parse each line from the import text area
        $subscriptions[] = newsletter_admin__explodeimport($importstring, $delim);
    }

    // Initialize values
    $imports_valid = array();
    $imports_invalid = array();
    $valid_count = 0;
    $invalid_count = 0;

    // Create the syntactical validation regular expression for email validation
    $regexp = "^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,6})$";

    // MS Outlook export quotes sections that have commas as part
    // of the string.  This may occur with the names and the
    // $subscription array will only contain 2 fields
    $idx = 0;
    foreach ($subscriptions as $subscription) {
        // Initialize name and email
        $name = '';
        $email = '';

        // Check if email set
        if (isset($subscription[2])) {
            // Set name
            $name = $subscription[0] . ' ' . $subscription[1];
            // Set email
            $email = $subscription[2];
        } else {
            // Parse first field into name
            $tempname = preg_split("/[,]+/", $subscription[0]);
            if (!empty($tempname[1])) {
                $name = $tempname[0] . ' ' . $tempname[1];
            } else {
                $name = $tempname[0];
            }
            // Set email
            if (isset($subscription[1])) {
                $email = $subscription[1];
            }
        }

        // Check email for extra fields (e.g. 'email SMTP email')
        $tempemail = preg_split("/[\s,]+/", $email);

        // Validate the email syntax
        $valid = 0;
        if (eregi($regexp, $tempemail[0]))
        {
            $email = $tempemail[0];
            $valid = 1;
        }

        // Make sure there is an email
        if ($valid) {
            // Loop through each publication and add subscriber
            foreach ($pids as $pid) {
                // Retrieve publication information
                $publication = xarModAPIFunc('newsletter',
                                             'user',
                                             'getpublication',
                                             array('id' => $pid));

                // Check for exceptions
                if (!isset($publication) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
                    return; // throw back

                // See if there is a role associated with the email address
                $role = xarModApiFunc('roles','user','get',array('email'=>$email));
                if(empty($role['uid'])) {

                    // Call create subscription function API
                    $subscriptionId = xarModAPIFunc('newsletter',
                                         'admin',
                                         'createaltsubscription',
                                          array('name' => $name,
                                                'email' => $email,
                                                'pid' => $pid,
                                                'htmlmail' => $htmlmail));
                } else {
                    // Call create subscription function API
                    // MichelV: this generated a true: bool and interfers with below
                    $subscriptionId = xarModAPIFunc('newsletter',
                                         'admin',
                                         'createsubscription',
                                          array('uid' => $role['uid'],
                                                'pid' => $pid,
                                                'htmlmail' => $htmlmail));
                }
                // Check if valid subscription
                if ($subscriptionId) {
                    $imports_valid[$idx]['name'] = trim($name);
                    $imports_valid[$idx]['email'] = trim($email);

                    $imports_valid[$idx]['publication'] = $publication['title'];

                    // Create url titles
                    $imports_valid[$idx]['edittitle'] = xarML('Edit');
                    $imports_valid[$idx]['deletetitle'] = xarML('Delete');
                    // If we have an integer, then the subscription in an alt subscription.
                    if (is_int($subscriptionId)) {
                        $imports_valid[$idx]['id'] = $subscriptionId;
                        $is_alt = true;
                    } else {
                        $imports_valid[$idx]['id'] = 0;
                        $is_alt = false;
                    }
                    // Create edit url
                    if((xarSecurityCheck('EditNewsletter', 0)) && $is_alt) {
                        $imports_valid[$idx]['editurl'] = xarModURL('newsletter',
                                                                    'admin',
                                                                    'modifyaltsubscription',
                                                                    array('id' => $imports_valid[$idx]['id']));
                    } else {
                        $imports_valid[$idx]['editurl'] = '';
                    }

                    // Create delete url
                    if((xarSecurityCheck('DeleteNewsletter', 0)) && $is_alt) {
                        $imports_valid[$idx]['deleteurl'] = xarModURL('newsletter',
                                                                      'admin',
                                                                      'deletealtsubscription',
                                                                      array('id' => $imports_valid[$idx]['id']));
                    } else {
                        $imports_valid[$idx]['deleteurl'] = '';
                    }

                    $idx++;
                    $valid_count++;
                } else {
                    // Invalid import
                    $imports_invalid[] = array('name' => $name,
                                               'email' => $email,
                                               'error' => -1);
                    $invalid_count++;
                }
            }
        } else {
            // Invalid import
            $imports_invalid[] = array('name' => $name,
                                       'email' => $email,
                                       'error' => 0);
            $invalid_count++;
        }
    }

    // Get the admin subscription menu
    $data = array();
    $data['menu'] = xarModFunc('newsletter', 'admin', 'subscriptionmenu');

    // Set parameters for template
    $data['pids'] = $pids;
    $data['imports_valid'] = $imports_valid;
    $data['imports_invalid'] = $imports_invalid;
    $data['valid_count'] = $valid_count;
    $data['invalid_count'] = $invalid_count;

    // Redirect to create a story for the issue
    return $data;
}

function newsletter_admin__explodeimport($str, $delim = ',', $qual = "\"")
{
    // Check if last character of line is the delim and remove
    $str = rtrim($str, $delim);

    $len = strlen($str);
    $inside = false;
    $word = '';

    // Explode import
    for ($i = 0; $i < $len; ++$i) {
        if ($str[$i]==$delim && !$inside) {
           $out[] = $word;
           $word = '';
        } else if ($inside && $str[$i]==$qual && ($i<$len && $str[$i+1]==$qual)) {
           $word .= $qual;
           ++$i;
        } else if ($str[$i] == $qual) {
           $inside = !$inside;
        } else {
           $word .= $str[$i];
        }
    }
    $out[] = $word;
    return $out;
}

?>
