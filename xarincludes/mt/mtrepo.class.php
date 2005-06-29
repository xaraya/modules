<?php

include_once "modules/bkview/xarincludes/scmrepo.class.php";

class mtRepo extends scmRepo
{
    var $_branch;   // Which branch from this repository are we interested in.
    var $_dbconn;   // Connection to the sqlite database for this repository.
    
    // NOTES:
    // getting a list of branches:
    // --> select distict base64_decode(value) from revision_certs where name='branch'
    
    function mtRepo($root='',$branch='')
    {
        if($root!='' && file_exists($root) && $branch!='') {
            $this->_root   = $root;
            $this->_branch = $branch;
            $args = array(
                'databaseType' => 'sqlite',
                'databaseHost' => dirname($this->_root),
                'databaseName' => basename($this->_root),
                'userName'     => 'dummy',
                'password'     => 'dummy');
            $this->_dbconn =& xarDBNewConn($args);
            return $this;           
        } else {
            $this = false;
            return false;
        }
    }
    
    function GetStats($user='') 
    {
        // Need to get:
        // - author identification
        // - utc timestamp
        // select id, value from revision_certs where name='author' 
        // that gets the revid + author (base64) for the revisions.
        // select id, value from revision_certs where name='date'
        // that gets the revid + its date (base64) for the revisions.
        $stats = array();
        $sql = "SELECT authors.id, authors.value, dates.value
                FROM revision_certs AS authors, revision_certs AS dates
                WHERE authors.id = dates.id AND authors.name=? AND dates.name=?";
        $result =& $this->_dbconn->execute($sql, array('author','date'));
        while(!$result->EOF)
        {
            list($revid, $author, $timestamp) = $result->fields;
            // Value fields are base64 encoded
            $author = base64_decode($author); 
            $timestamp = base64_decode($timestamp);
            $timestamp = $this->iso8601_To_Utc($timestamp);
            // Make a timestamp out of the iso format
            if($user != '') {
                // Only add if matched
                if($user == $author) $stats[$timestamp] = $author;
            } else {
                // No user specified, add always
                $stats[$timestamp] = $author;
            }
            $result->MoveNext();
        }
        krsort($stats);
        return $stats;
    }
    
    // This is generic enough to be moved somewhere else
    function iso8601_to_utc($isodate)
    {
        // 2005-06-29T13:00:21 -> 20050629130021
        return str_replace(array('-','T',':'),'', $isodate);
    }

    function GetChangeSets($range='', $merge=false, $user='')
    {
        // Only getting revision id's as output
        $sql = "SELECT id FROM revision_certs ";
        $result =& $this->_dbconn->execute($sql);
        while(!$result->EOF) {
            list($revid) = $result->fields;
            $revs[] = $revid;
            $result->MoveNext();
        }
        return $revs;
    }
    
    function ChangeSets($user, $range,$flags = 0)
    {
        // Need to get:
        // tag, age, author, rev id, utc timestamp, comments
         
        $sql = "SELECT authors.id, dates.value, comments.value
                FROM revision_certs as authors, 
                     revision_certs as dates,
                     revision_certs as comments
                WHERE authors.id = dates.id AND
                      dates.id = comments.id AND
                      authors.name = ? AND
                      dates.name = ? AND
                      comments.name = ?";
        $bindvars = array('author','date','changelog');
        $result =& $this->_dbconn->execute($sql, $bindvars);
        $csets = array(); $tags = array();
        // return array of cset objects indexed by revid
    }
    
    

}

?>