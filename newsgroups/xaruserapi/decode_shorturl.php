<?php

/**
 * extract function and arguments from short URLs for this module, and pass
 * them back to xarGetRequestInfo()
 * @param $params array containing the elements of PATH_INFO
 * @returns array
 * @return array containing func the function to be called and args the query
 *         string arguments, or empty if it failed
 */
function newsgroups_userapi_decode_shorturl($params)
{
    $args = array();

    $module = 'newsgroups';

    if (empty($params[1])) {
        return array('main', $args);

    } elseif (preg_match('/^index/i',$params[1])) {
        return array('main', $args);

    } else {
        $args['group'] = $params[1];
        if (empty($params[2])) {
            return array('group', $args);
        } elseif (preg_match('/^index/i',$params[2])) {
            return array('group', $args);
        } elseif ($params[2] == 'post') {
            $args['phase'] = 'new';
            return array('post', $args);
        } elseif (preg_match('/^(\d+)/i',$params[2],$matches)) {
            $args['article'] = $matches[1];
            if (empty($params[3]) || $params[3] != 'reply') {
                return array('article', $args);
            } else {
                $args['phase'] = 'reply';
                return array('post', $args);
            }
        } else {
            $args['messageid'] = $params[2];
            return array('article', $args);
        }
    }

    // default : return nothing -> no short URL
    // (e.g. for multiple category selections)

}

?>
