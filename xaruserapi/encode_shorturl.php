<?php
//$Id: encode_shorturl.php,v 1.2 2003/06/20 17:31:56 roger Exp $

function calendar_userapi_encode_shorturl(&$params)
{
    // Get arguments from argument array
    //extract($args); unset($args);
    // check if we have something to work with
    if (!isset($params['func'])) { return; }
    $day = $month = $year = null;
    // default path is empty -> no short URL
    $path = '';
    $extra = '';
    // we can't rely on xarModGetName() here (yet) !
    $module = 'calendar';
    if(isset($params['cal_date']) && !empty($params['cal_date'])) {
        $year = substr($params['cal_date'],0,4);
        $month = substr($params['cal_date'],4,2);
        $day = substr($params['cal_date'],6,2);
    }
    if(empty($year))  $year  = xarLocaleFormatDate('%Y');
    if(empty($month)) $month = xarLocaleFormatDate('%m');
    if(empty($day))   $day   = xarLocaleFormatDate('%d');


    // specify some short URLs relevant to your module
    switch($params['func']) {
        case 'main':
            $path = "/$module/";
            break;

        case 'day':
            $path = "/$module/$year$month$day/";
            break;

        case 'week': // it would be nice if this could be shorter, but probably not
            $path = "/$module/week/$year$month$day/";
            break;

        case 'month':
            $path = "/$module/$year$month/";
            break;

        case 'year':
            $path = "/$module/$year/";
            break;

        case 'modifyconfig':
            $path = "/$module/modifyconfig/";
            break;

        /*
        case 'submit':
            $path = "/$module/submit/$year$month$day/";
            break;

        case 'edit':
            $path = "/$module/edit/";
            if(isset($params['cal_eid']) && !empty($params['cal_eid'])) {
                $path .= xarVarPrepForDisplay($params['cal_eid']).'.html/';
            }
            break;

        case 'publish':
            $path = "/$module/publish/";
            if(isset($params['calname']) && !empty($params['calname'])) {
                $path .= xarVarPrepForDisplay($params['calname']).'.ics';
            }
            break;
        */
    }

    /*
    if(!empty($path) && isset($params['cal_sdow'])) {
        $join = empty($extra) ? '?' : '&amp;';
        $extra .= $join . 'cal_sdow=' . $params['cal_sdow'];
    }
    */

    if(!empty($path) && isset($params['cal_category'])) {
        $join = empty($extra) ? '?' : '&amp;';
        $extra .= $join . 'cal_category=' . $params['cal_category'];
    }

    if(!empty($path) && isset($params['cal_topic'])) {
        $join = empty($extra) ? '?' : '&amp;';
        $extra .= $join . 'cal_topic=' . $params['cal_topic'];
    }


    return $path.$extra;

}

?>
