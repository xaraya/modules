<?php

class scmRepo
{
    var $_root;     // what is the root location of the repository
    
    /**
     * Construct a repository object 
     *
     */
    function construct($brand ='bk', $args)
    {
        include_once "modules/bkview/xarincludes/$brand/$brand.class.php";
        $className =  "$brand"."Repo";
        switch($brand) {
            case 'bk':
                return new $className($args['repopath']);
            case 'mt':
                return new $className($args['repopath'],$args['repobranch']);
        }
    }
    
    function map($id)
    {
        if(!isset($id)) return;
        switch($id) {
            case 1: return 'bk';
            case 2: return 'mt';
            default: return;
        }
    }
    
    function RangeToText($range='') 
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
    function RangeToUtcPoints($range = '')
    {
        $utcpoints = array(
                    'start' => '00000000000000',
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

?>