<?php

/**
 * main display function
 */
function polls_user_main()
{
    xarResponseRedirect(xarModURL('polls',
                                         'user',
                                         'list'));
}

?>