<?php
/**
 * Class to model a file in a bitkeeper repository 
 *
 * @package modules
 * @copyright (C) 2004 The Digital Development Foundation, Inc.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
 */

/**
 * Class to model a file in the repository 
 */
include_once "modules/bkview/xarincludes/scmfile.php";
class bkFile extends scmFile
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
        $this->_csets=$this->ChangeSets($this->_file);
        //$tagcsets = array_intersect($this->_repo->_tagcsets,$this->_csets);
        $this->_tagrevs=array();
        
        //     foreach($tagcsets as $index => $cset) {
        //            $cmd = "bk c2r -r$cset $this->_file";
        //            $rev = $this->_repo->_run($cmd);
        //            if(count($rev)) {
        //                $this->_tagrevs[$rev[0]] = $cset;
        //            }
        //        }
        return $this;
   }
    
    function History($user) 
   {
        $formatstring="'";
        if($user != '') $formatstring .="\$if(:P:=$user){";
        $formatstring .= ":AGE:|:P:|:REV:|\$each(:C:){(:C:)}";
        if($user != '') $formatstring .= "}";
        $formatstring .= "'";
        
        $cmd="prs -nh -d$formatstring ".$this->_file;
        $history = $this->_repo->_run($cmd);
            
        while (list(,$row) = each($history)) {
            list($age, $author, $filerev, $comments) = explode('|',$row);
            $delta = (object) null;
            $delta->rev = $filerev;
            $delta->age = $age;
            $delta->author = $author;
            $delta->file = $this->_file;
            $delta->checkedout = file_exists($this->_repo->_root . '/' . $this->_file);
            $delta->comments = $comments;
            $deltas[$filerev] = $delta;
        }
        return $deltas;
   }
    
    function &ChangeSets($user='') 
   {
        if(!empty($this->_csets)) return $this->_csets;
        
        // First get the cset revs for this file
        $cmd="f2csets $this->_file";
        $csetrevs = $this->_repo->_run($cmd);

        // Make it suitable for bk cmd
        $revs='';
        while (list(,$cset) = each($csetrevs)) $revs.="$cset,";
        $revs=substr($revs,0,strlen($revs)-1);
        
        // Get these revisions from the repo, we are sure it wont be a range, so tell bk that.
        $csets =& $this->_repo->ChangeSets('',$revs, BK_FLAG_NORANGEREVS);
        return $csets;
   }
    
    function ChangeSet($rev) 
   {
        // Return the corresponding cset for the specified delta
        if($this->_file == 'ChangeSet') return $rev;
        $cmd = "r2c -r$rev " . $this->_file;
        $cset = $this->_repo->_run($cmd);
        return $cset[0];
   }
    function Tag($rev) 
   {
        // Because the ChangeSet file has all tags, it's kinda silly to include it
        // besides, nobody is interested in the ChangeSet file but me
        if ($this->_file != "/ChangeSet") {
            // FIXME: use the tagset array for this.
            //if(array_key_exists($rev,$this->_tagrevs)) {
            // This revision is mentioned in the tagrev array
            // get the tagname
            $cmd = "changes -r`bk r2c -r$rev ". $this->_file ."`  -d':TAG:\n'";
            $tag = $this->_repo->_run($cmd);
            if (count($tag)) {
                // FIXME: remove the br, it assumes browser output
                return implode($tag,"<br/>");
            }
            //}
        }
        return '';
   }

    function AbsoluteName()
    {
        return $this->_repo->_root . '/' . $this->_file;
    }
}
?>
