<?php

function xarbb_user_view()
{
    // Security Check
    if(!xarSecurityCheck('ViewxarBB',1,'Forum')) return;
    
       xarResponseRedirect(xarModURL('xarbb', 'user', 'main'));

 return true;
}

?>
