<?php
/**
 * Standard view function
 *
 */
function reports_admin_view() 
{
    xarResponseRedirect(xarModUrl('report','admin','view_reports'));
    return true;
}

?>