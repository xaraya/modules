<?php

/**
 * File: $Id$
 *
 * Classes to model bitkeeper repository objects
 *
 * @package modules
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

define('BK_SEARCH_REPO',   8);
define('BK_SEARCH_FILE',   4);
define('BK_SEARCH_CSET',   1);
define('BK_SEARCH_DELTAS', 2);

define('BK_FIELD_MARKER','|');
define('BK_NEWLINE_MARKER','<nl/>');


/**
 *  Class to model a repository
 *
 */
class bkRepo 
{
    var $_root;     // where is the root of the repository
    var $_desc;     // what is the description of the repository
    var $_config;   // array with the configuration parameters
    var $_tagcsets; // array with the csets which have tags
    
    // Constructor
    function bkRepo($root='') 
    {
        if ($root!='') {
            $this->_root=$root;
            $this->_getconfig();
            $cmd="bk changes -t -d':REV:\n'";
            $this->_tagcsets = $this->_run($cmd);
            return $this;
        } else {
            return false;
        }
    }
    
    // FIXME: protect this somehow, so no arbitrary commands can be run.
    function _run($cmd='echo "No command given.."') 
    {
        // Save the current directory
        $savedir = getcwd();
        chdir($this->_root);
        
        $out=array();$retval='';
        $out = shell_exec($cmd);
        if(function_exists('xarLogMessage')) {
            xarLogMessage("BK: $cmd");
        }
        $out = str_replace("\r\n","\n",$out);
        $out = explode("\n", $out);
        $out = array_filter($out,'notempty');

        chdir($savedir);
        return $out;
    }
    
    // Private method
    function _getconfig() 
    {
        // Read configuration of this repository and store in properties
        $config=$this->_root."/BitKeeper/etc/config";
        $cmd = "bk get -qS $config";
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
    function bkGetConfigVar($var='name') 
    {
        return $this->_config[$var];
    }
    
    function bkChangeSet($file,$rev) 
    {
        $file = __fileproper($file);
        $cmd="bk r2c -r".$rev." ".$file;
        $cset = $this->_run($cmd);
        return $cset;
    } 
    
    function bkGetChangeSets($range='',$merge=false,$user='') 
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
        $cmd = "bk changes $params";
        return $this->_run($cmd);

    }
    // Changeset counts
    function bkCountChangeSets($range='',$merge=false,$user='') 
    {
        $out = $this->bkGetChangeSets($range,$merge,$user);
        
        return count($out);
    }

    // Count the number of changed lines in a list of changesets
    function bkCountChangedLines($csets = Array()) 
    {
        $lines=0;
        foreach ($csets as $cset) {
            $cmd="bk prs -r$cset -d\":LI:\"";
            $out = $this->_run($cmd);
            unset($out[0]);
            foreach ($out as $output_line) {
                $lines+=$output_line;
            }
        }
    }

    // Get changesets
    function bkChangeSets($revs, $range,$dspec='\":REV:\",\":C:\"',$showmerge='',$sort=0,$user='') 
    {
        // FIXME: apparently -e and -r don't work together
        // FIXME: This sets too many non logical restrictions on how dspec should look
        $params='-n ';
        if ($sort==1) {
            $params.='-f ';
        }
        if ($showmerge==0) {
            $dspec="'\$unless(:MERGE:){".substr($dspec,1,strlen($dspec)-2)."}'";
        } else {
            $params.='-e ';
        }
        if ($range!='') $params.='-c'.$range.' ';
        if ($revs!='') $params.='-r'.$revs.' ';
        if ($user!='') $params.='-u'.$user.' ';

        $params.="-d$dspec";
        $cmd="bk changes $params";
        //echo $cmd."<br/>";
        return $this->_run($cmd);
    }
    
    function bkGetUsers() 
    {
        $cmd="bk users";

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
    
    function bkDirList($dir='/') 
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
    
    function bkFileList($dir='/') 
    {
        $cmd="bk prs -hn -r+ -d':TAG:|:GFILE:|:REV:|:AGE:|:P:|\$each(:C:){(:C:)".BK_NEWLINE_MARKER."}' ".$this->_root."/".$dir;
        $filelist = $this->_run($cmd);
        asort($filelist);
        return $filelist;
    }

    function bkGetId($type='package') 
    {
        if($type =='package') {
            $cmd="bk id";
        } else {
            $cmd="bk id -r";
        }
        $package_id = $this->_run($cmd);
        return $package_id;
    }

    function bkSearch($term,$what_to_search = BK_SEARCH_CSET) 
    {
        $result = array();
        switch($what_to_search) {
        case BK_SEARCH_CSET:
            $cmd = "bk changes -nm -d'\$each(:C:){:I:|(:C:)}' -/".$term."/i";
            break;
        case BK_SEARCH_DELTAS:
            // FIXME: the grep should only be on the comments of the delta, not on the rest
            $cmd = "bk sfind -U | prs -h -d'\$each(:C:){:GFILE:|:I:|(:C:)}\n' - | grep '$term'";
            break;
        case BK_SEARCH_FILE:
            $cmd = "bk sfind -U | bk grep -a -r+ -fm '$term' -";
            break;
        default: 
            return array();
        }
        $result = $this->_run($cmd);
        return $result;
    }
    
    function bkGetStats($user='') 
    {
        $params = '';
        if($user!='') {
            $params.='-u'.$user.' ';
        }

        // Get all stats info at once, we can sort out later
        $cmd = "bk changes $params -d'\$if(:Li: -gt 0){:USER:|:UTC:}\n'";
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
}

// Class to model a changeset
class bkChangeSet  
{
    var $_repo;   // in which repository is this changeset?
    var $_rev;    // which changeset to instantiate?
    var $_deltas; // array of file/rev combos which hold the deltas in this cset
    
    function bkChangeset($repo,$rev='+') 
    {
        $this->_repo=$repo;
        $this->_rev=$rev;   // changeset revision number
        // Fill delta array with identification of deltas
        $this->_deltas=NULL;
        $this->_deltas();
    }
    
    // Private function to initialize delta array
    function _deltas() 
    {
        $cmd="bk changes -vn -r".$this->_rev." -d':GFILE:|:REV:'";
        $tmp = $this->_repo->_run($cmd);
        while (list(,$did) = each($tmp)) {
            list($file,$rev) = explode('|',$did);
            if (strtolower($file)!="changeset") {
                $this->_deltas[$did]=new bkDelta($this,$file,$rev);
            }
        }
    }
    
    function bkDeltaList() 
    {
        return $this->_deltas;
    }
    
   
    function bkDeltas($formatstring="':GFILE:|:REV:'") 
    {
        $cmd="bk changes -vn -r".$this->_rev." -d$formatstring";
        return $this->_repo->_run($cmd);
    }
    
    function bkRev() 
    {
        return $this->_rev;
    }
}

// Class to model a delta
class bkDelta 
{
    var $_cset;     // in which cset is this delta
    var $_file;     // which file
    var $_rev;      // what revision?
    var $_author;   // who authored it?
    var $_age;      // how long ago?
    var $_domain;   // from where?
    var $_comments; // what were the comments?
    
    function bkDelta($cset='',$file,$rev) 
    {
        $file = __fileproper($file);
        $this->_file=$file;
        $this->_rev=$rev;
        $this->_cset=$cset;
        $cmd ="bk prs -hvn -r$rev -d':AGE:|:P:|:DOMAIN:|\$each(:C:){(:C:)".BK_NEWLINE_MARKER."}' $file";
        
        $info = $this->_cset->_repo->_run($cmd);
        list($age, $author,$domain, $comments) = explode('|',$info[0]);
        $this->_age=$age;
        $this->_author=$author;
        $this->_domain=$domain;
        $this->_comments=str_replace(BK_NEWLINE_MARKER,"\n",$comments);
    }
    
    function bkDiffs() 
    {
        $cmd="bk diffs -hu -R".$this->_rev." ".$this->_file;
        return $this->_cset->_repo->_run($cmd);
    }
    
    function bkAnnotate() 
    {
        $cmd="bk annotate -aum -r".$this->_rev." ".$this->_file;
        return $this->_cset->_repo->_run($cmd);
    }
    
    function bkFile() 
    {
        return $this->_file;
    }
    
    function bkRev() 
    {
        return $this->_rev;
    }
}

/* Class to model a file in the repository */
class bkFile 
{
    var $_repo;               // in which repository is this file?
    var $_file;               // filename
    var $_csets = array();   // array of csets in which deltas of this file were
    var $_tagrevs;            // array of revision numbers->csets which have tags and this file was in it
    
    function bkFile($repo,$file='ChangeSet') 
    {
        $this->_repo=$repo;
        // if / is first char, strip it
        $this->_file = (substr($file,0,1) =='/')?substr($file,1):$file;
        // Store the csets this file is in
        $this->_csets=$this->bkChangeSets($this->_file);
        $tagcsets = array_intersect($this->_repo->_tagcsets,$this->_csets);
        $this->_tagrevs=array();
        
        foreach($tagcsets as $index => $cset) {
            $cmd = "bk c2r -r$cset $this->_file";
            $rev = $this->_repo->_run($cmd);
            if(count($rev)) {
                $this->_tagrevs[$rev[0]] = $cset;
            }
        }
        return $this;
    }
    
    function bkHistory($formatstring) 
    {
        $cmd="bk prs -nh -d$formatstring ".$this->_file;
        return $this->_repo->_run($cmd);
    }
    
    function bkChangeSets() 
    {
        if(!empty($this->_csets)) return $this->_csets;
        
        $cmd="bk f2csets $this->_file";
        return $this->_repo->_run($cmd);
    }
    
    function bkTag($rev) 
    {
        // Because the ChangeSet file has all tags, it's kinda silly to include it
        // besides, nobody is interested in the ChangeSet file but me
        if ($this->_file != "/ChangeSet") {
            if(array_key_exists($rev,$this->_tagrevs)) {
                // This revision is mentioned in the tagrev array
                // get the tagname
                $cmd = "bk changes -r".$this->_tagrevs[$rev]. " -d':TAG:\n'";
                $tag = $this->_repo->_run($cmd);
                if (count($tag)) {
                    // FIXME: remove the br, it assumes browser output
                    return implode($tag,"<br/>");
                }
            }
        }
        return '';
    }
}

/**
 * UTILITY FUNCTIONS WHICH NEED A PLACE
 *
 */

/**
 * callback function for the array_filter on line 46
 *
 */
function notempty($item) 
{
    return (strlen($item)!=0);
}


function __fileproper($file) 
{
    if(substr($file,0,1) == "/") {
        $file=substr($file,1,strlen($file)-1);
    }
    return $file;
}

/**
 * Translate a range to a text string
 *
 * Currently maintained on ad-hoc basis
 */
function bkRangeToText($range='') 
{
  // FIXME: this is FAR FROM COMPLETE
  $text='';
  if ($range=='') return '';

  // Check before/after range
  if (substr($range,0,2)=='..') {
    return 'before '.substr($range,2,strlen($range)-2);
  }
  if (substr($range,-2,2)=='..') {
    return 'after '.substr($range,2,strlen($range)-2);
  }

  $number = (-(int) $range);

  // past?
  if (((int) $range) < 0) {
    $text .='in the last ';
  }
  // Converts range specification to text to display
  switch (strtolower($range[strlen($range)-1])) {
  case 'h':
    $text .=((-(int) $range)==1)?"hour":"$number hours";
    break;
  case 'd':
    $text .=((-(int) $range)==1)?'day':"$number days";
    break;
  case 'w':
    $text .=((-(int) $range)==1)?'week':"$number weeks";
    break;
  case 'm':
    $text .=((-(int) $range)==1)?'month':"$number months";
    break;
  case 'y':
    $text .=((-(int) $range)==1)?'year':"$number years";
    break;
  default:
    $text .= "unknown range $range";
  }
  return $text;
}

function bkAgeToRangeCode($age) 
{
    // Converts an age as output by :AGE: dspec to range code 
    // useable by bk prs (bit lame that prs doesn't do that itself)
    // First part: multiplier
    // Second part: unit:
    //    h - hours
    //    w - weeks
    //    d - days
    //    m - months
    //    y - years

    $ageCode='-1h';
    $parts = explode(' ',$age);
    $ageCode = "-". $parts[0] .$parts[1][0];
    return $ageCode;
}

?>
