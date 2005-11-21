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
    
    function GetStats($user='',$branch='') 
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
        // a -> authors
        // d -> dates
        // b -> branches
        $sql = "SELECT a.id, unbase64(a.value), unbase64(d.value), unbase64(b.value) as bvalue ";
        $sql.= "FROM revision_certs AS a, revision_certs AS d, revision_certs AS b ";
        $sql.= "WHERE a.id=d.id AND d.id=b.id AND a.name='author' AND d.name='date' AND b.name='branch' ";
        if($user != '') {
            $sql.= "AND unbase64(a.value) like '$user'";
        }
        if($branch != '') {
            $sql.= "AND unbase64(b.value) like '$branch'";
        }
        $result = $this->_run("db execute \"$sql\"");
        array_shift($result); // Pop off the first result and reindex 
        foreach($result as $index => $line)
        {
            list($revid, $author, $timestamp,$lod) = explode('|',$line);
            // Make a timestamp out of the iso format
            $timestamp = $this->iso8601_To_Utc($timestamp);
            $stats[trim($timestamp)] = trim($author);
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
                $certs[] = $tmpcert; $tmpcert = array();
                break;
            default:
                //xarLogMessage("MT: found unknown certname '$name'". var_export($line,true)."");
                // Assuming continuation of value
                $tmpcert['value'] .= "<br/>" .trim($line);
            }
        }
        return $certs;
    }

    function &ChangeSets($user, $range='',$flags = 0,$branch='')
    {
        xarLogMessage("MT: repo:ChangeSets($user,$range,$flags,$branch)");
        // Need to get:
        // tag, age, author, rev id, utc timestamp, comments

        $selector=array();
        // Range selector
        $utcpoints = scmRepo::RangeToUtcPoints($range);
        $begin = $this->utc_to_iso8601($utcpoints['start']);
        $end = $this->utc_to_iso8601($utcpoints['end']);
        $selector[] = 'l:'.$begin.'/e:'.$end;
        
        // Author selector
        if($user != '') $selector[] = 'a:'.$user;
      
        // Tag selector
        if($flags & SCM_FLAG_TAGGEDONLY) $selector[] = "c:tag";
        
        // Branch selector
        if($branch != '') $selector[] = 'b:'.$branch;
        // Consolidate selector conditions
        $selector = join('/',$selector);
        xarLogMessage("MT: total selector in repo->ChangeSets ($branch) $selector");

        // Get the selected revisions
        $cmd = "automate select $selector";
        $revs = $this->_run($cmd);
        
        $csets = array(); $tags = array();
        foreach($revs as $index => $revid) {
            // Retrieve the certificates
            $certs = $this->certs($revid);
            
            $cset = (object) null;
            $cset->file ='ChangeSet';
            $cset->rev = $revid;
            $cset->tag = '';
            $cset->age = 'TBD';
            $cset->branch = $branch;
            $cset->checkedout = false;

            foreach($certs as $index => $cert) {
                switch($cert['name']) {
                case 'tag':
                    $cset->tag = $cert['value'];
                    break;
                case 'author':
                    $cset->author = $cert['value'];
                    break;
                case 'date':
                    $cset->date = $cert['value'];
                    break;
                case 'changelog':
                    $cset->comments = $cert['value'];
                    break;
                }
            }
                // Add it to the collection
            $csets[$revid] = $cset;
        }
        return $csets;
    }

    function &manifest($manifest_id)
    {
        static $manifests = array();
        
        if(!isset($manifests[$manifest_id])) {
            $cmd = "automate get_manifest $manifest_id";
            $result = $this->_run($cmd);
            $manifests[$manifest_id] = $result;
        }
        return $manifests[$manifest_id];
    }

    function DirList($dir="/",$rev='',$branch='')
    {
        // Determine the revision
        if($rev=='') {
            $cmd = "automate heads $branch";
            $result = $this->_run($cmd);
            $rev = $result[0];
        }
        
        // Determine the manifest
        $cmd = "automate get_revision $rev";
        $result = $this->_run($cmd);
        // CHECKME: seems ok, check stdio for relevance of spacing
        $manifest_id = substr($result[0],14,40);
        
        // Retrieve the manifest
        $result =& $this->manifest($manifest_id);

        $ret = array();
        foreach($result as $index => $line) {
            $ident = explode('  ',$line);
            $file = '/'.trim($ident[1]);
            $part = strstr($file, $dir);
            if($part) {
                // $part contains everything after $dir including $dir itself
                $part = substr($part, strlen($dir));
                if($firstslash = strpos($part,'/')) {
                    // Get the part until the slash
                    $part = substr($part,0,$firstslash);
                } 
                $ret[$part] = $part;
            }
        }
        return $ret;
    }
    
    function FileList($dir='/',$rev='',$branch)
    {
        // Determine the revision
        if($rev=='') {
            $cmd = "automate heads $branch";
            $result = $this->_run($cmd);
            $rev = $result[0];
        }
        
        // Determine the manifest
        $cmd = "automate get_revision $rev";
        $result = $this->_run($cmd);
        // CHECKME: seems ok, check stdio for relevance of spacing
        $manifest_id = substr($result[0],14,40);
        
        // Retrieve the manifest
        $result =& $this->manifest($manifest_id);
        $ret = array();
        foreach($result as $index => $line) {
            $ident = explode('  ',$line);
            $file = '/'.trim($ident[1]);
            $part = strstr($file, $dir);
            if($part) {
                $part = substr($part, strlen($dir)+1);
                $firstslash = strpos($part, '/');
                if(!$firstslash) {
                    $ret[] = "tag|$part|".$ident[0]."|age|author|comments\n";
                }
            }
        }
        return $ret;
    }

    function &GetGraphData($start='-3d', $end, $file, $branch='')
    {
        xarLogMessage("MT: repo:GetGraphData($start,$end,$branch)");
        // First get the revisions in the range
        $revs = $this->ChangeSets('',$start,0,$branch);
        $nodes = array(); $edges = array(); $inEdges = array();
        $lateMergeNodes = array(); $startRev=0; $endRev=0;$nrOfChanges=0;
        $graph = array('nodes' => $nodes, 'edges' => $edges,'pastconnectors' => $lateMergeNodes, 'startRev' => $startRev, 'endRev' => $endRev);
        
        foreach($revs as $revid => $revdetail) {
            $cmd = "db execute \"SELECT child, parent FROM revision_ancestry WHERE parent ='$revid' or child ='$revid';\"";
            $result =& $this->_run($cmd);
            array_shift($result);
            foreach($result as $trans) {
                $node = explode('|', $trans);
                $parent = trim($node[0]); $child = trim($node[1]);
                if($parent == $revid) {
                    $edges[] = array($parent => $child);
                    $nodes[] = array('rev' => $parent, 'author' => $revdetail->author, 'tags' => $revdetail->tag, 'date' => $revdetail->date);
                } elseif ($child == $revid) {
                    $inEdges[$child][] = $parent;
                    $childtag = $revdetail->tag;
                    $childauthor= $revdetail->author;
                    $nodes[] = array('rev' => $child, 'author' => $revdetail->author, 'tags' => $revdetail->tag, 'date' => $revdetail->date);

                } else {
                    $nodes[] = array('rev' => $parent, 'author' => 'TBD', 'tags' => $parenttag);
                    $nodes[] = array('rev' => $child, 'author' => 'TBD', 'tags' => $childtag);
                }
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

    function getBranches()
    {
        $cmd = 'ls branches';
        $result = $this->_run($cmd);
        return $result;
    }
}

?>