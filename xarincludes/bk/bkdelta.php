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
include_once "modules/bkview/xarincludes/scmdelta.php";
class bkDelta extends scmDelta
{
    var $repo;       // which repo are we talking about?
    var $file;       // which file
    var $rev;        // what revision?
    var $author;     // who authored it?
    var $age;        // how long ago?
    var $domain;     // from where?
    var $comments;   // what were the comments?
    var $date;       // exact date of the delta
    var $time;       // exact time of the delta
    var $checkedout; // is the file containing this delta check out?
    
    function bkDelta($repo, $file, $rev) 
   {
        $this->repo = $repo;
        $file = __fileproper($file);
        $this->file=$file;
        $this->rev=$rev;
        $abspath =  $repo->_root . '/' . $this->file;
        $this->checkedout = file_exists($abspath);
        
        $cmd ="prs -hvn -r$rev -d':D:|:T:|:AGE:|:P:|:DOMAIN:|\$each(:C:){(:C:)".BK_NEWLINE_MARKER."}' $file";
        
        $info = $this->repo->_run($cmd);
        list($date,$time,$age, $author,$domain, $comments) = explode('|',$info[0]);
        $this->date = $date;
        $this->time = $time;
        $this->age=$age;
        $this->author=$author;
        $this->domain=$domain;
        $this->comments=str_replace(BK_NEWLINE_MARKER,"\n",$comments);
   }
    
    function Diffs() 
   {
        $cmd="diffs -hu -R".$this->rev." ".$this->file;
        return $this->repo->_run($cmd);
   }
    
    function Annotate() 
   {
        $cmd="annotate -aum -r".$this->rev." ".$this->file;
        return $this->repo->_run($cmd);
   }
}

?>
