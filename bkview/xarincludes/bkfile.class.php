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
    
    function bkHistory($formatstring) 
   {
        $cmd="bk prs -nh -d$formatstring ".$this->_file;
        return $this->_repo->_run($cmd);
   }
    
    function bkChangeSets() 
   {
        if(!empty($this->_csets)) return $this->_csets;
        
        $cmd="bk f2csets $this->_file";
        $csetrevs = $this->_repo->_run($cmd);
        // Make the list of changesets into a range
        $revs='';
        while (list(,$cset) = each($csetrevs)) $revs.="$cset,";
        $revs=substr($revs,0,strlen($revs)-1);
        $formatstring = "':AGE:|:P:|:REV:|\$each(:C:){(:C:)<br />}'";
        $list = $this->_repo->bkChangeSets($revs,'',$formatstring,false);
        
        $csets=array();
        $counter=1;
        while (list($key,$val) = each($list)) {
            list($age, $author, $rev, $comments) = explode('|',$val);
            $changeset = (object) null;
            $changeset->file = $this->_file;
            $changeset->rev = $rev;
            $changeset->age = $age;
            $changeset->author = $author;
            $changeset->comments = $comments;
            $csets[$rev] = $changeset;
        }
        return $csets;
   }
    
    function bkChangeSet($rev) 
   {
        // Return the corresponding cset for the specified delta
        if($this->_file == 'ChangeSet') return $rev;
        $cmd = "bk r2c -r$rev " . $this->_file;
        $cset = $this->_repo->_run($cmd);
        return $cset[0];
   }
    function bkTag($rev) 
   {
        // Because the ChangeSet file has all tags, it's kinda silly to include it
        // besides, nobody is interested in the ChangeSet file but me
        if ($this->_file != "/ChangeSet") {
            // FIXME: use the tagset array for this.
            //if(array_key_exists($rev,$this->_tagrevs)) {
            // This revision is mentioned in the tagrev array
            // get the tagname
            $cmd = "bk changes -r`bk r2c -r$rev ". $this->_file ."`  -d':TAG:\n'";
            $tag = $this->_repo->_run($cmd);
            if (count($tag)) {
                // FIXME: remove the br, it assumes browser output
                return implode($tag,"<br/>");
            }
            //}
        }
        return '';
   }

    function bkAbsoluteName()
    {
        return $this->_repo->_root . '/' . $this->_file;
    }
}
?>
