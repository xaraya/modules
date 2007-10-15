<?php

function mag_user_imgtest($args)
{
    extract($args);

    // Get module parameters
    extract(xarModAPIfunc('mag', 'user', 'params',
        array(
            'knames' => 'module,itemtype_articles'
        )
    ));

    // Search criteria.
    $params = array (
        'module' => 'Articles',
        'itemtype' => 1,
    );

    $articles = xarModAPIfunc('articles', 'user', 'getall', $params);

    foreach($articles as $key=>$article) {
        $body = $article['body'];
        $output = xarModAPIFunc('mag', 'user', 'imgfilter', array('text'=>$body));
        echo "$output<br/><br/>";
        //print_r($articles);
        //$articles[$key]['body'] = $output;
    }
    return true;
}