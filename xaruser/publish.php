<?php

/**
 * Publish a calendar
 */
function calendar_user_publish($args)
{
    extract($args);
    xarVarFetch('calid','id',$calid,0,XARVAR_NOT_REQUIRED);
    xarVarFetch('calname','str:1:',$calname,'',XARVAR_NOT_REQUIRED);

// test
    xarModSetVar('calendar', 'SupportShortURLs', 1);

// TODO: security et al.

    if (!empty($calid) || !empty($calname)) {

/* TEST: protect remote calendar access with basic authentication
    // cfr. notes at http://www.php.net/features.http-auth for IIS or CGI support
        if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW']) ||
            // is this a valid user/password ?
            !xarUserLogIn($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) ||
            // does this user have access to this calendar ?
            !xarSecurityCheck('ViewCalendar',0,'All',$calname)) {

            $realm = xarModVars::get('themes','SiteName');
            header('WWW-Authenticate: Basic realm="'.$realm.'"');
            //header('HTTP/1.0 401 Unauthorized');
            header("Status: 401 Access Denied");
            echo xarML('You must enter a valid username and password to access this calendar');
            exit;
         }
*/
         $calendars = xarModAPIFunc('calendar','user','get',
                                    array('calid' => $calid,
                                          'calname' => $calname));
         if (!isset($calendars)) return;

         // we found a calendar
         if (count($calendars) == 1) {
             if (empty($calendars[0]['cpath'])) {
                 // TODO: retrieve entries from database and create ics file

             } else {
                 $curdir = sys::varpath() . '/calendar';
                 $curfile = $curdir . '/' . $calendars[0]['cpath'];
                 if (file_exists($curfile) && filesize($curfile) > 0) {

                     if($_SERVER['REQUEST_METHOD'] != 'PUT')
                     {
                         // return the .ics file
                         header('Content-Type: text/calendar');
                         @readfile($curfile);

             // TODO: use webdavserver instead ?
                 // Cfr. phpicalendar/calendars/publish.php (doesn't seem to work for PHP < 4.3)
                     // publishing
                     } else {
                         // get calendar data
                         $data = '';
                         if($fp = fopen('php://input','r'))
                         {
                             while($chunk = fgets($fp,4096))
                             {
                                 $data .= $chunk;
                             }
                             /*
                             while(!@feof($fp))
                             {
                                 $data .= fgets($fp,4096);
                             }
                             */
                             @fclose($fp);
                         } else {
                             xarLogMessage('failed opening standard input');
                         }

                         if(!empty($data))
                         {
                             //xarLogMessage($data);
                             // write to file
                             if($fp = fopen($curfile,'w+'))
                             {
                                 fputs($fp, $data, strlen($data) );
                                 @fclose($fp);
                             }
                             else
                             {
                                 xarLogMessage( 'couldnt open file '.$curfile );
                             }
                         } else {
                             xarLogMessage('failed getting any data');
                         }
                     }
                     // we're done here
                     exit;
                 }
             }
         }
    }
    $data = array();
    $data['calendars'] = xarModAPIFunc('calendar','user','getall');

    return $data;
}

?>
