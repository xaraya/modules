<?php
/**
 * Class to model a bitkeeper repository 
 *
 * @package modules
 * @copyright (C) 2004 The Digital Development Foundation, Inc.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
 */


/**
 *  Class to model a bitkeeper repository
 */
include_once "modules/bkview/xarincludes/scmrepo.php";
class bkRepo extends scmRepo
{
    var $_desc;     // what is the description of the repository
    var $_config;   // array with the configuration parameters
    var $_tagcsets; // array with the csets which have tags
    
    // Constructor
    function bkRepo($root='') 
    {
        $this->_basecmd = 'bk ';

        if ($root!='' && file_exists($root)) {
            $this->_root=$root.'/ChangeSet';
            $this->_getconfig();
            $cmd="changes -t -d':REV:\n'";
            $this->_tagcsets = $this->_run($cmd);
            return $this;
        } else {
            return false;
        }
    }
    
    // Private method
    function _getconfig() 
    {
        // Read configuration of this repository and store in properties
        $config=dirname($this->_root)."/BitKeeper/etc/config";
        $cmd = "get -qS $config";
        $this->_run($cmd);
        if (!file_exists($config)) {
            return false;
        } else {
            // format is key : value pairs
            $key='';$value='';
            $format ="%[^:]:%[a-zA-Z@./: ]\n";
            $fp = fopen ($config,"r");
            while (fscanf ($fp, $format,$key,$value)) {
                $key=trim($key);$value=trim($value);
                if (strlen($key)!=0) {
                    if ($key[0]!='#') {
                        $this->_config[$key]=$value;
                        $key='';$value='';
                    }
                }
            }
            fclose($fp);
        }
    }
    
    
    // Operations on a repository
    function GetConfigVar($var='name') 
    {
        return $this->_config[$var];
    }
    
    // FIXME: this should be a method of a delta
    function ChangeSet($file,$rev) 
    {
        if($file == 'ChangeSet') return $rev;
        $file = __fileproper($file);
        $cmd="r2c -r".$rev." ".$file;
        $cset = $this->_run($cmd);
        return $cset[0];
    } 
    
    function getDelta($file,$rev)
    {
        return new bkDelta($this, $file, $rev);
    }
    
    function getFile($file)
    {
        return new bkFile($this,$file);
    }
    
    function GetChangeSets($range='',$merge=false,$user='') 
    {
        $params='';
        if ($user!='') {
            $params .= " -u$user ";
        }
        if ($range) {
            $params .= " -c".$range;
        }
        if ($merge) {
            $params .= " -d':REV:\\n'";
        } else {
            $params .= " -d'\$unless(:MERGE:){:REV:}\\n'";
        }
        $cmd = "changes $params";
        return $this->_run($cmd);

    }

    // Count the number of changed lines in a list of changesets
    function CountChangedLines($csets = Array()) 
    {
        $lines=0;
        foreach ($csets as $cset) {
            $cmd="prs -r$cset -d\":LI:\"";
            $out = $this->_run($cmd);
            unset($out[0]);
            foreach ($out as $output_line) {
                $lines+=$output_line;
            }
        }
    }

    // Instantiate a changeset
    function getChangeSet($rev)
    {
        return new bkChangeSet($this, $rev);
    }
    
    // Get changesets
    function &ChangeSets($user, $range,$flags = 0)
    {
        $params='-n '; $dspec = "'";
        
        // Do we want tagged only csets?
        if($flags & SCM_FLAG_TAGGEDONLY) $dspec .= "\$if(:TAG:){";
        $dspec .= ":TAGS:|:AGE:|:P:|:REV:|:UTC:|\$each(:C:){(:C:)".BK_NEWLINE_MARKER."}";
        if($flags & SCM_FLAG_TAGGEDONLY) $dspec .= "}";
        $dspec .= "'";
        
        // Do we want forward sorting?
        if ($flags & SCM_FLAG_FORWARD) $params.='-f ';
        
        // Do we want to show merge csets?
        if (!($flags & SCM_FLAG_SHOWMERGE)) {
            $dspec="'\$unless(:MERGE:){".substr($dspec,1,strlen($dspec)-2)."}'";
        } else {
            $params.='-e ';
        }
        
        if ($range!='') {
            // If the revs are not a range (not continuous or no range format) we use -r, otherwise -c
            if($flags & BK_FLAG_NORANGEREVS) {
                $params .= '-r'.$range.' ';
            } else {
                $params .= '-c'.$range.' ';
            }
        }
        if ($user!='') $params.='-u'.$user.' ';

        $params.="-d$dspec";
        $cmd="changes $params";
        //echo "<pre>$cmd"."</pre><br/>";
        $csetlist = $this->_run($cmd);

        $csets=array(); $tags = array();
        while (list($key,$val) = each($csetlist)) {
            if(substr($val,0,1) != '|') {
                // We have a tagline
                $tags[] = str_replace('S ','',$val);
                continue;
            }
            list(,$age, $author, $rev, $utc, $comments) = explode('|',$val);
              
            $changeset = (object) null;
            $changeset->file = 'ChangeSet';
            $changeset->tag = implode(',',$tags);
            $tags = array(); // reset
            $changeset->age = $age;
            $changeset->author = $author;
            $changeset->rev = $rev;
            $changeset->checkedout = file_exists($this->_root . '/' . $changeset->file);
            $changeset->comments = $comments;
            $csets[$rev] = $changeset;
        }
        return $csets;
    }
    
    function GetUsers() 
    {
        $cmd="users";

        //Although bk treats users only by the username, which is present
        //before the '@', it prints all know e-mails as different users
        //which leads to a bunch of repeated information

        $user_mails = $this->_run($cmd);
        
        $users = Array();
        foreach ($user_mails as $mail) {
            $pos = strpos($mail, "@");
            if ($pos === false) {
                $user_string = $mail;
            } else {
                $user_string = substr($mail, 0, $pos);
            }
            
            if (!array_search($user_string, $users)) {
                $users[] = $user_string;
            }
        }
 
        $users = array_unique($users);
        asort($users);
        return $users;
    }
    
    function dirList($dir='/') 
    {
        // First construct the array elements for directories
        $ret=array();
        $savedir = getcwd();
        chdir($this->_root."/".$dir);
        // If we're not in the root of repository first element is parent dir
        if ($reploc = opendir($this->_root."/".$dir)) {
            while (($file = readdir($reploc)) !== false) {
                if ($file!="." && $file!="SCCS" && is_dir($file)){
                    $ret[]="$file";
                }  
            }
            closedir($reploc);
        }
        chdir($savedir);
        return $ret;
    }
    
    function fileList($dir='/') 
    {
        $cmd="prs -hn -r+ -d':TAG:|:GFILE:|:REV:|:AGE:|:P:|\$each(:C:){(:C:)".BK_NEWLINE_MARKER."}' ".$this->_root."/".$dir;
        $filelist = $this->_run($cmd);
        asort($filelist);
        return $filelist;
    }

    function GetId($type='package') 
    {
        if($type =='package') {
            $cmd="id";
        } else {
            $cmd="id -r";
        }
        $package_id = $this->_run($cmd);
        return $package_id;
    }

    function Search($term,$what_to_search = BK_SEARCH_CSET) 
    {
        $result = array();
        switch($what_to_search) {
        case BK_SEARCH_CSET:
            $cmd = "changes -nm -d'\$each(:C:){:I:|(:C:)}' -/".$term."/i";
            break;
        case BK_SEARCH_DELTAS:
            // FIXME: the grep should only be on the comments of the delta, not on the rest
            $cmd = "sfind -U | prs -h -d'\$each(:C:){:GFILE:|:I:|(:C:)}\n' - | grep '$term'";
            break;
        case BK_SEARCH_FILE:
            $cmd = "sfind -U | bk grep -a -r+ -fm '$term' -";
            break;
        default: 
            return array();
        }
        $result = $this->_run($cmd);
        return $result;
    }
    
    function GetStats($user='',$branch='') 
    {
        $params = '';
        if($user!='') {
            $params.='-u'.$user.' ';
        }

        // Get all stats info at once, we can sort out later
        $cmd = "changes $params -d'\$if(:Li: -gt 0){:USER:|:UTC:}\n'";
        $rawresults = $this->_run($cmd);
        // Construct a slightly more friendly array to return
        foreach($rawresults as $rawresult) {
            list($user,$timestamp) = explode("|",$rawresult);
            // FIXME: How do we treat double timestamps?
            $results[$timestamp] = $user;
        }
        // We sort the stats such that the newest comes first.
        krsort($results);
    
        return $results;
    }
    
    function &GetGraphData($start = '-3d', $end = '+', $file ='ChangeSet')
    {
        if(!trim($end)) $end="+";
        // First, translate the ranges to revisions
        $cmd = "prs -fhr$start -nd':REV:|:DS:' $file";
        $revs = $this->_run($cmd);
        if(empty($revs)) 
        {
            // Nothing in the range, take the last rev, which will be earlier
            // than the range specified
            $cmd = "prs -fhr+ -nd':REV:|:DS:' $file";
            $revs = $this->_run($cmd);
        }
        list($startRev,$startSerial) = explode('|',$revs[0]);
        
        // Do the same for the endmarker
        $cmd = "prs -fhr$end -nd':REV:|:DS:' $file";
        $revs = $this->_run($cmd);
        if(empty($revs)) {
            $endRev = $startRev;
            $endSerial = $startSerial;
        } else {
            list($endRev,$endSerial) = explode('|',$revs[0]);
        }
        $reverse = false;
        if($startSerial > $endSerial) {
            // Make bk happy
            $tmp = $startRev;
            $startRev = $endRev;
            $endRev = $tmp;
        }
        
        $edges = array(); $nodes = array(); $nodeIndex = array();
        $inEdges = array(); $lateMergeNodes = array();
        $graph = array('nodes' => $nodes, 'edges' => $edges, 'pastconnectors' => $lateMergeNodes, 'startRev' => $startRev, 'endRev' => $endRev);
        
        $nrOfChanges = abs($endSerial - $startSerial);
        xarLogMessage("BK: trying to graph $nrOfChanges changes");
        if($nrOfChanges > 500 | $nrOfChanges == 0) {
            $graph['nodes'][] = array('rev' => xarML('Too many/few\nchanges (#(1))\nin range',$nrOfChanges), 'author' => 'Graph Error', 'tags' => '','date'=>'TBD');
            return $graph;
        }
        $cmd = "prs -hr$startRev..$endRev -nd':TAGS:|:REV:|:KIDS:|:DS:|:P:' $file";
        $rawdata = $this->_run($cmd); $tags = array();
        foreach($rawdata as $primeLine) {
            if(substr($primeLine,0,1) != '|') {
                // We have a tagline
                $tags[] = str_replace('S ','',$primeLine);
                continue;
            }
            list(, $rev, $kids,$serial, $author) = explode('|',$primeLine);
            
            if(!empty($kids)) $kids = explode(' ',$kids); else $kids = array();
            $nodeIndex[$serial] = $rev;
            $nodes[$serial] = array('rev' => $rev,'author' => $author, 'tags' => implode(',',$tags),'date'=>'TBD');
            $tags = array(); // reset
            foreach($kids as $next) {
                if($rev != $next && $next != $startRev && $rev != $endRev) 
                {
                    $edges[] = array($rev => $next);
                    $inEdges[$next][] = $rev;
                } 
            }
        }
        // Compare the values of the total nodes and the ones which have arrows coming into them
        $lateMergeNodes = array_diff($nodeIndex, array_keys($inEdges));
        if($Key = array_search($startRev, $lateMergeNodes)) unset($lateMergeNodes[$Key]);
        if($Key = array_search($endRev, $lateMergeNodes)) unset($lateMergeNodes[$Key]);
        ksort($nodes);
        
        $graph = array('nodes' => $nodes, 'edges' => $edges,'pastconnectors' => $lateMergeNodes, 'startRev' => $startRev, 'endRev' => $endRev);
        return $graph;
    
    }
    
    /**
     * Translate a range to a text string
     *
     * Currently maintained on ad-hoc basis
     * THIS IS A CLASS METHOD
     */

    
    /**
     * Convert an age specifier to a rangecode
     *
     * THIS IS A CLASS METHOD
     */
    function AgeToRangeCode($age) 
    {
        // Converts an age as output by :AGE: dspec to range code 
        // useable by bk prs (bit lame that prs doesn't do that itself)
        // First part: multiplier
        // Second part: unit:
        //    Y/y - years
        //    M   - months
        //    W/w - weeks
        //    D/d - days
        //    h   - hours
        //    m   - minutues
        //    s   - seconds
        
        $parts = explode(' ',$age);
        switch (strtolower($parts[1][0])) {
            case 'y':
            case 'w':
            case 'd':
            case 'h':
            case 's':
                $ageCode = "-". $parts[0] . $parts[1][0];
                break;
            case 'm':
                if(strtolower($parts[1][1]) =='o') {
                    $ageCode = "-". $parts[0] . 'M';
                } else {
                    $ageCode = "-". $parts[0] . 'm';
                }
                break;
            default:
                $ageCode = '-1h';
        }
        return $ageCode;
    }
}

// TODO: move this
function __fileproper($file) 
{
    if(substr($file,0,1) == "/") {
        $file=substr($file,1,strlen($file)-1);
    }
    return $file;
}


?>
