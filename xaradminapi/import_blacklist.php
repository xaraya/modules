<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Comments Module
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 *  Imports Current Blacklist
 *  left/right values
 *
 *  @author John Cox
 *  @access public
 *  @returns true on success
 */
function comments_adminapi_import_blacklist( $args )
{
    extract($args);
    xarDBLoadTableMaintenanceAPI();
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $btable = $xartable['blacklist'];
    $bbtable = &$xartable['blacklist_column'];
    $feedfile = 'http://www.jayallen.org/comment_spam/blacklist.txt';

    // Get the feed file (from cache or from the remote site)
    $filegrab = xarModAPIFunc('base', 'user', 'getfile',
                           array('url' => $feedfile,
                                 'cached' => true,
                                 'cachedir' => 'cache/rss',
                                 'refresh' => 604800,
                                 'extension' => '.txt'));
    if (!$filegrab) {
        $msg = xarML('Could not get new blacklist file.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Kinda hackish here.  No empty table command that I can find.

    $query = xarDBDropTable($xartable['blacklist']);
    $result =& $dbconn->Execute($query);

    if(!$result)
        return;


    // Create blacklist tables
    $fields = array(
        'xar_id'       => array('type'=>'integer',  'null'=>FALSE,  'increment'=> TRUE, 'primary_key'=>TRUE),
        'xar_domain'   => array('type'=>'varchar',  'null'=>FALSE,  'size'=>255)
    );

    $query = xarDBCreateTable($xartable['blacklist'], $fields);
    $file = file('var/cache/rss/'.md5($feedfile).'.txt');
    $result =& $dbconn->Execute($query);
    if (!$result)
        return;
    for ($i=0; $i<count($file); $i++) {
        $data = $file[$i];
        $domain = "";
        for ($j=0; $j<strlen($data); $j++)  {
            if ($data[$j]==" " || $data[$j] == "#"){
                break;
            } else {
                $domain .= $data[$j];
                continue;
            }
        }
        if (strpos($domain, '[\w\-_.]')) {
            $domaim = str_replace('[\w\-_.]','[-\w\_.]', $domain);
        }
        $ps = strpos($domain, '/');
        while ($ps !== false) {
            if ($ps == 0) {
                $domain = '\\' + $domain;
            } else if (substr($domain, $ps-1, 1) != '\\') {
                $domain = substr_replace($domain, '\/', $ps, 1);
            }
            $ps = strpos($domain, '/', $ps+2);
        }
        $domain = trim($domain);
        if ($domain != ""){
            $nextId = $dbconn->GenId($btable);
            $query = "INSERT INTO $btable(xar_id,
                                          xar_domain)
                      VALUES (?,?)";
            $bindvars = array($nextId, $domain);
            $result =& $dbconn->Execute($query,$bindvars);
            if (!$result) return;
        }
    }
    return true;
}
?>