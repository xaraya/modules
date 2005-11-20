<?php

include_once "modules/bkview/xarincludes/scmrepo.class.php";

class mtRepo extends scmRepo
{
    var $_branch;   // Which branch from this repository are we interested in.
    var $_dbconn;   // Connection to the sqlite database for this repository.
    
    function mtRepo($root='',$branch='')
    {
        if($root!='' && file_exists($root)) {
            $this->_root   = $root;
            $this->_branch = $branch;
            $this->_basecmd= "monotone --root=/ -d $root ";
        } 
    }

    // FIXME: protect this somehow, so no arbitrary commands can be run.
    function &_run($cmd='echo "No command given.."', $asis = false) 
    {
        if(function_exists('xarLogMessage')) {
            xarLogMessage("MT: $cmd", XARLOG_LEVEL_DEBUG);
        }
        // Save the current directory
        $savedir = getcwd();
        chdir(dirname($this->_root));
        
        $out=array();$retval='';
        $out = shell_exec($this->_basecmd . $cmd);

        if(!$asis) {
            $out = str_replace("\r\n","\n",$out);
            $out = explode("\n", $out);
            $out = array_filter($out,'notempty');
        }
        chdir($savedir);
        // We need to do this here, because this class changes the cwd secretly and we dont 
        // know what kind of effect this has on the environment
        return $out;
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
        // This needs to be on one line
        $sql = "SELECT a.id, unbase64(a.value), unbase64(d.value) FROM revision_certs AS a, revision_certs AS d  WHERE a.id = d.id AND a.name='author' AND d.name='date'";
        $result = $this->_run("db execute \"$sql\"");
        array_shift($result); // Pop off the first result and reindex 
        foreach($result as $index => $line)
        {
            list($revid, $author, $timestamp) = explode('|',$line);
            // Make a timestamp out of the iso format
            $timestamp = $this->iso8601_To_Utc($timestamp);
            if($user != '') {
                // Only add if matched
                if($user == trim($author)) $stats[trim($timestamp)] = trim($author);
            } else {
                // No user specified, add always
                $stats[trim($timestamp)] = trim($author);
            }
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
    function utc_to_iso8601($utcdate)
    {
        // 20050629130021 -> 2005-06-29T13:00:21
        return substr($utcdate,0,4).'-'.substr($utcdate,4,2).'-'.substr($utcdate,6,2).'T'.substr($utcdate,8,2).':'.substr($utcdate,10,2).':'.substr($utcdate,12,2);

    }

    function &GetChangeSets($range='', $merge=false, $user='')
    {
        // Only getting revision id's as output
        // TODO: take $user into account
        // TODO: take $merge into account
        // TODO: take $range into account
        $utcpoints = scmRepo::RangeToUtcPoints($range);
        $begin = $this->utc_to_iso8601($utcpoints['start']);
        $end = $this->utc_to_iso8601($utcpoints['end']);
        $selector = "l:".$begin."/e:".$end;

        $cmd = "automate select $selector";;
        $result =& $this->_run($cmd);
        array_shift($result);
        return $result;
    }
    
    function getChangeSet($rev)
    {
        return new mtChangeSet($this, $rev);
    }
    
    function getDelta($file, $rev)
    {
        return new mtDelta($this, $file, $rev);
    }
    
    function getFile($file)
    {
        return new mtFile($this, $file);
    }
    
    // FIXME: This should be a method of a delta
    // anyway, it tries to get the revision in which a certain delta appeared
    function ChangeSet($file, $rev)
    {
        // This kinda sucks in monotone. How do you get a revision from a delta?
        // the db model would force more or less to scan all revisions and look for the
        // delta id in the data, which is obviously undoable 
        
    }
    
    function certs($revid)
    {
        $certlines = $this->_run("automate certs $revid");
        $certs=array();
        foreach($certlines as $line) {
            $name = trim(substr($line,0,9));
            switch($name) {
            case 'key':
            case 'signature':
            case 'name':
            case 'value':
                $tmpcert[$name] = substr(trim($line),strlen($name)+2,-1);
                continue;
            case 'trust':
                // end of current cert
                $tmpcert[$name]= substr(trim($line),strlen($name)+2,-1);
                $certs[] = $tmpcert;
                break;
            default:
                // Assuming continuation of value
                $tmpcert['value'] .= ' ' .trim($line);
            }
        }
        return $certs;
    }

    function &ChangeSets($user, $range='',$flags = 0)
    {
        // Need to get:
        // tag, age, author, rev id, utc timestamp, comments
        
        // Get the boundaries of what to get
        $utcpoints = scmRepo::RangeToUtcPoints($range);
        $begin = $this->utc_to_iso8601($utcpoints['start']);
        $end = $this->utc_to_iso8601($utcpoints['end']);
        
        $cmd = "automate select l:".$begin."/e:".$end;
        $revs = $this->_run($cmd);
        
        $csets = array(); $tags = array();
        foreach($revs as $index => $revid) {
            $certs = $this->certs($revid);
            
            $add = false;
            // No user specified, add it
            if($user == '') {
                $add=true;
            } else {
                // User is specified, need to check the certs
                $add=true;
            }
            if($add) {
                $cset = (object) null;
                $cset->file ='ChangeSet';
                $cset->rev = $revid;
                $cset->tag = '';
                $cset->age = 'TBD';
                $cset->checkedout = false;

                foreach($certs as $index => $cert) {
                    switch($cert['name']) {
                    case 'tag':
                        $cset->tag = $cert['value'];
                        break;
                    case 'author':
                        $cset->author = $cert['value'];
                        break;
                    case 'changelog':
                        $cset->comments = $cert['value'];
                        break;
                    }
                }
                // Add it to the collection
                $csets[$revid] = $cset;
            }
        }
        
        return $csets;
    }
    
    function GetGraphData($start='-3d', $end, $file)
    {
        // First get the revisions in the rang
        $revs = $this->ChangeSets('',$start);
        $nodes = array(); $edges = array(); $inEdges = array();
        $lateMergeNodes = array(); $startRev=0; $endRev=0;$nrOfChanges=0;
        $graph = array('nodes' => $nodes, 'edges' => $edges,'pastconnectors' => $lateMergeNodes, 'startRev' => $startRev, 'endRev' => $endRev);
        
        foreach($revs as $revid => $revdetail) {
            $sql = "SELECT parent, child FROM revision_ancestry WHERE parent = ? or child = ?";
            $result =& $this->_dbconn->Execute($sql,array($revid,$revid));
            if(!$result) return;
            while(!$result->EOF) {
                list($parent, $child) = $result->fields;
                if($parent == $revid) {
                    $edges[] = array($parent => $child);
                    $nodes[] = array('rev' => $parent, 'author' => $revdetail->author, 'tags' => $revdetail->tag);
                } elseif ($child == $revid) {
                    $inEdges[$child][] = $parent;
                    $childtag = $revdetail->tag;
                    $childauthor= $revdetail->author;
                    $nodes[] = array('rev' => $child, 'author' => $revdetail->author, 'tags' => $revdetail->tag);

                } else {
                    $nodes[] = array('rev' => $parent, 'author' => 'TBD', 'tags' => $parenttag);
                    $nodes[] = array('rev' => $child, 'author' => 'TBD', 'tags' => $childtag);
                }
                $result->MoveNext();
            }
        }
        // Limit the thing a bit
        $nrOfChanges = count($nodes);
        xarLogMessage("BK: trying to graph $nrOfChanges changes");
        // FIXME: make the 500 configurable
        if($nrOfChanges > 500 | $nrOfChanges == 0) {
            $graph['nodes'][] = array('rev' => xarML('Too many/few\nchanges (#(1))\nin range',$nrOfChanges), 'author' => 'Graph Error', 'tags' => '');
            return $graph;
        }
       
        $graph = array('nodes' => $nodes, 'edges' => $edges,'pastconnectors' => $lateMergeNodes, 'startRev' => $startRev, 'endRev' => $endRev);
        return $graph;
    }

}
/**
* callback function for the array_filter on line 39
 *
 */
function notempty($item) 
{
    return (strlen($item)!=0);
}

?>