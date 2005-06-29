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
}

?>