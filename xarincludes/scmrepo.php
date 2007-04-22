<?php
 // TODO: guess ;-)
define('BK_SEARCH_REPO',   8);
define('BK_SEARCH_FILE',   4);
define('BK_SEARCH_CSET',   1);
define('BK_SEARCH_DELTAS', 2);
define('BK_FIELD_MARKER','|');
define('BK_NEWLINE_MARKER','<nl/>');

define('BK_FLAG_FORWARD'   ,  1);
define('BK_FLAG_SHOWMERGE' , 2);
define('BK_FLAG_TAGGEDONLY', 4);
define('BK_FLAG_NORANGEREVS', 8);

class scmRepo
{
    var $_root;     // what is the root location of the repository
    var $_basecmd;  // base command to prefix the running of commands
    
    /**
     * Construct a repository object 
     *
     */
    public static function construct($brand ='mtn', $args)
    {
        include_once "modules/bkview/xarincludes/$brand/$brand.php";
        $className =  "$brand"."Repo";
        switch($brand) {
            case 'bk':
                return new $className($args['repopath']);
            case 'mtn':
                return new $className($args['repopath'],$args['repobranch']);
        }
    }
    
    // FIXME: protect this somehow, so no arbitrary commands can be run.
    public function &_run($cmd='echo "No command given.."', $asis = false) 
    {
        if(function_exists('xarLogMessage')) {
            xarLogMessage("MT: ".$this->_basecmd. $cmd, XARLOG_LEVEL_DEBUG);
        }

        // Save the current directory
        $savedir = getcwd();
        chdir(dirname($this->_root));
        //die($this->_basecmd . $cmd);
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

    public function getBranches()
    {
        return array();
    }

    public static function map($id)
    {
        if(!isset($id)) return;
        switch($id) {
            case 1: return 'bk';
            case 2: return 'mtn';
            default: return;
        }
    }
    
    public static function RangeToText($range='') 
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
     
    /**
     * Conver a range specification to utc point(s) in time
     *
     * @param string $range Range specifier as in bk terms
     *
     */
    public static function RangeToUtcPoints($range = '')
    {
        $utcpoints = array(
                    'start' => '19700000000000',
                    'end'   => date('YmdHis'));
        if($range == '') return $utcpoints;
        
        // Get the number specifier
        // FIXME: this assumes a negative number
        $number = (-(int) $range);
               
        // Converts range specification to text to display
        $period = $number;
        switch (strtolower($range[strlen($range)-1])) {
            case 'y':
                $period *= 12;         //-> to months;
            case 'm':
                // FIXME: make this correct
                $period *= (13.0/3.0); //-> to weeks
            case 'w':
                $period *= 7;          //-> to days
            case 'd':
                $period *= 24;         //-> to hours
            case 'h':
                $period *= 3600;       //-> to seconds
                break;
            default:
                $period = 0;
        }
        // Period now contains the number of seconds specifed in the range specifier
        $now = date('U');
        // FIXME: This assumes range is always negative
        $past = $now - $period; 
        $utcpoints['start'] = date('YmdHis', $past);
        return $utcpoints;
    }
    
    
    function CountChangeSets($range='', $merge=false,$user='')
    {
        $out = $this->GetChangeSets($range,$merge,$user);
        return count($out);
    }
}

/**
* callback function for the array_filter
 *
 */
function notempty($item) 
{
    return (strlen($item)!=0);
}

function colour_from_string($str)
{
    // PHP4 doesnt have raw output, let's roll our own
    $hash = sha1($str);
    // We need the binary equivalent of this
    $hashval = pack("H" . strlen($hash), $hash);
 
    $hue = (ord($hashval[5]) / 256.0) * 1.00 + 0.00; 
    $li  = (ord($hashval[1]) / 256.0) * 0.15 + 0.75; 
    $sat = (ord($hashval[2]) / 256.0) * 0.50 + 0.60;
    return "$hue $li $sat";
}

?>
