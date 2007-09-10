<?php
/**
 * Site Tools Backup functions
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitetools Module
 * @link http://xaraya.com/index.php/release/887.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * @Backup tables in your database
 * @Parameters
 * TODO: Add in multidatabase once multidatabase functionality and location decided
 * TODO: add in more customization of configurations
 */
function sitetools_admin_backup($args)
{
   if (!xarVarFetch('confirm',        'str:1:', $confirm,       '', XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('startbackup',    'str:2:', $startbackup,   '', XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('usegz',          'int:1',  $usegz,         0, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('screen',         'int:1',  $screen,        0, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('dbname',         'str:1',  $dbname,        '' , XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('SelectedTables', 'array:', $SelectedTables, '', XARVAR_NOT_REQUIRED)) return;
   /* Security check */
    if (!xarSecurityCheck('AdminSiteTools')) return;

    $data=array();
    /*setup variables */
    $data['usegz']=$usegz;
    $data['screen']=$screen;
      $data['startbackup']=$startbackup;

    $data['number_of_cols'] = xarModGetVar('sitetools','colnumber');
    $number_of_cols=$data['number_of_cols'];
    $backupabsolutepath= xarModGetVar('sitetools','backuppath').'/';
    $data['warning']=0;
    $data['warningmessage']='<span class="xar-accent">'
                            .xarML('WARNING: directory does not exist or is not writeable: ').$backupabsolutepath.'</span><br /><br />'
                            .xarML(' Please ensure the backup directory exisits and is writeable');

    if ((!is_dir($backupabsolutepath)) || (!is_writeable($backupabsolutepath))) {
       $data['warning']=1;
       return $data;
    }
    $data['authid']     = xarSecGenAuthKey();
    /* Setup the current database for backup - until there is option to choose it TODO */
    if (($dbname='') || (empty($dbname))){
        $dbconn =& xarDBGetConn();
            $dbname= xarDBGetName();
            $dbtype= xarDBGetType();
    }

    $data['confirm']=$confirm;
    $data['dbname']=$dbname;
    $data['dbtype']=$dbtype;


    if (empty($startbackup)) {
       /* No confirmation yet - display a suitable form to obtain confirmation
        * of this action from the user
        * setup option links
        */
        $data['backupops']=array();
        $data['backupops']['complete'] = xarML('Full backup - complete inserts');
        $data['backupops']['standard'] = xarML('Full backup - standard inserts');
        $data['backupops']['partial'] =  xarML('Partial - select tables, complete inserts');
        $data['backupops']['structure'] = xarML('Full backup - Structure only');

        $confirm='';

    /* Start actual backup for all types here */
    } elseif ($startbackup) {

        $confirm='';
        if ($startbackup =='partial'){
           $tabledata=array();
           $tabledata=xarModAPIFunc('sitetools','admin','gettabledata');
           if ($tabledata == false) {
                /* Throw back any system exceptions (e.g. database failure) */
                if (xarCurrentErrorType() == XAR_SYSTEM_EXCEPTION) {
                    return; /* throw back */
                }
                /* Handle the user exceptions yourself */
                $status = xarML('Unable to access database table information');
                $reason = xarCurrentError();
                if (!empty($reason)) {
                    $status .= '<br /><br />'. xarML('Reason') .' : '. $reason->toString();
                }
                /* Free the exception to tell Xaraya that you handled it */
                xarErrorFree();
                return $status;
            }
            /* set javascript header */
            xarModAPIfunc('base', 'javascript', 'modulefile', array('filename'=>'sitetools_admin_backup.js'));

            $data['dbtables']    = $tabledata['dbtables'];
            $tabletotal          = $tabledata['tabletotal'];
            $data['dbname']      = $tabledata['dbname'];
            $dbname              = $tabledata['dbname'];
            $data['checkboxname']= 'SelectedTables['.htmlentities($dbname, ENT_QUOTES).'][]';

            return $data;
        }

        if (!xarSecConfirmAuthKey()) {return;}
        @set_time_limit(600);

        $bkupdata=array();
        $bkupdata= xarModAPIFunc('sitetools','admin','backupdb',
                               array ('usegz'          => $data['usegz'],
                                      'startbackup'    => $data['startbackup'],
                                      'screen'         => $data['screen'],
                                      'SelectedTables' => $SelectedTables,
                                      'dbname'         => $dbname,
                                      'dbtype'         => $dbtype));


        if ($bkupdata == false) {
            /* Throw back any system exceptions (e.g. database failure) */
            if (xarCurrentErrorType() == XAR_SYSTEM_EXCEPTION) {
                return; // throw back
            }
            /* Handle the user exceptions yourself */
            $status = xarML('Unable to backup database');
            $reason = xarCurrentError();
            if (!empty($reason)) {
                $status .= '<br /><br />'. xarML('Reason') .' : '. $reason->toString();
            }
            /* Free the exception to tell Xaraya that you handled it */
            xarErrorFree();
            return $status;
        }


        $data['deleteurl'] =$bkupdata['deleteurl'];
        $data['warning'] =$bkupdata['warning'];
        if ($screen==0) {
           $data['runningstatus'] ='';
        } else {
            $data['runningstatus'] =$bkupdata['runningstatus'];
        }


        $data['bkfiletype'] =$bkupdata['bkfiletype'];
        $data['bkfilename'] =$bkupdata['bkfilename'];
        $data['bkname'] =$bkupdata['bkname'];
        $data['bkfilesize'] =$bkupdata['bkfilesize'];
        $data['completetime'] =$bkupdata['completetime'];
        $data['backuptype'] =$bkupdata['backuptype'];
        $data['btype'] =$bkupdata['btype'];
        /*  $downloadfile=$bkupdata['bkname']; */

       /*Generate download, view and delete URLS */

        $data['downloadurl']= xarModURL('sitetools','admin','downloadbkup',
                                     array('savefile' => $data['bkname']));
        $data['deleteurl']= xarModURL('sitetools','admin','downloaddel',
                                     array('savefile' => $data['bkname']));
    }

  return $data;

}

?>