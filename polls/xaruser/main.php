<?php

/**
 * main display function
 */
function polls_user_main($args)
{
    return xarModFunc('polls', 'user', 'list', $args);
}

?>
