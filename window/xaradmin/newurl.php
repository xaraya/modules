<?php
function window_admin_newurl($args)
{
  // UGLY, needs validations here.
  list($id,
         $authid,
         $window_status,
         $urlid,
         $url_address,
         $host,
         $alias,
         $reg_user_only,
         $open_direct,
         $use_fixed_title,
         $auto_resize,
         $vsize,
         $hsize) = xarVarCleanFromInput('id',
                                        'authid',
                                        'window_status',
                                        'urlid',
                                        'url_address',
                                        'host',
                                        'alias',
                                        'reg_user_only',
                                        'open_direct',
                                        'use_fixed_title',
                                        'auto_resize',
                                        'vsize',
                                        'hsize');
 $newurl = xarModAPIFunc('window',
                        'admin',
                        'addurl',
                        array('id' => $id,
                               'authid' => $authid,
                               'window_status' => $window_status));

            // The return value of the function is checked here, and if the function
            // suceeded then an appropriate message is posted.  Note that if the
            // function did not succeed then the API function should have already
            // posted a failure message so no action is required
            if (!isset($newurl) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back


}
?>