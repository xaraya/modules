<?php

function simplepie_user_main()
{
   $url = 'http://www.iema.net/jobs/view?theme=rss';
   $url = 'http://www.newsfeedmaker.com/feed_rss.php?query=climate+change';
   $url = 'http://www.iema.net/news/iemanews?cat=311&theme=rss';
   $url = 'http://www.iema.net/envjobs/view?showform=simple&qtext=%27&catid=_297&theme=rss';
   $url = 'http://www.iema.net/index.php/articles/weblinks?theme=rss';

   $feed = xarModAPIfunc('simplepie', 'user', 'process', array('url' => $url));

   echo "<pre>";
   var_dump($feed);
   echo "</pre>";
/*
   echo "<hr />";

   $pie = xarModAPIfunc('simplepie', 'user', 'newfeed');

   $pie->feed_url($url);

   $pie->init();

   echo "<pre>";
   var_dump($pie);
   echo "</pre>";
*/
   echo "done";
}

?>
