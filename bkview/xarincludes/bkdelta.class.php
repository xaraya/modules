<?php

/**
 * Class to model a bitkeeper delta 
 *
 * @package modules
 * @copyright (C) 2004 The Digital Development Foundation, Inc.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
 */

/**
 * Class to model a bitkeeper delta
 */
class bkDelta 
{
    var $cset;       // in which cset is this delta
    var $file;       // which file
    var $rev;        // what revision?
    var $author;     // who authored it?
    var $age;        // how long ago?
    var $domain;     // from where?
    var $comments;   // what were the comments?
    var $date;       // exact date of the delta
    var $checkedout; // is the file containing this delta check out?
    
    function bkDelta(&$cset,$file,$rev) 
   {
        $file = __fileproper($file);
        $this->file=$file;
        $abspath =  $cset->_repo->_root . '/' . $this->file;
        $this->checkedout = file_exists($abspath);
        $this->rev=$rev;
        $this->cset=$cset;
        $cmd ="bk prs -hvn -r$rev -d':D:|:T:|:AGE:|:P:|:DOMAIN:|\$each(:C:){(:C:)".BK_NEWLINE_MARKER."}' $file";
        
        $info = $this->cset->_repo->_run($cmd);
        list($date,$time,$age, $author,$domain, $comments) = explode('|',$info[0]);
        $this->date = $date;
        $this->time = $time;
        $this->age=$age;
        $this->author=$author;
        $this->domain=$domain;
        $this->comments=str_replace(BK_NEWLINE_MARKER,"\n",$comments);
   }
    
    function bkDiffs() 
   {
        $cmd="bk diffs -hu -R".$this->rev." ".$this->file;
        return $this->cset->_repo->_run($cmd);
   }
    
    function bkAnnotate() 
   {
        $cmd="bk annotate -aum -r".$this->rev." ".$this->file;
        return $this->cset->_repo->_run($cmd);
   }
    
    function bkFile() 
   {
        return $this->file;
   }
    
    function bkRev() 
   {
        return $this->rev;
   }
}

?>
