<?php

/**
 * the main user function
 * 
 * @author mikespub
 * @access public 
 * @param no $ parameters
 * @return true on success or void on falure
 * @throws XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION'
 */
function workflow_user_main()
{ 
    // Security Check
    if (!xarSecurityCheck('ReadWorkflow')) return;

    // Return the output
    return array();
} 

?>
