<?php

function googlesearch_admin_rebuildcachekeys()
{
  $newfile = '';
  $path = 'var/cache/google';
  $dir = opendir($path);
  $index = fopen($path.'/CACHEKEYS', 'w');

  while (($item = readdir($dir)) !== false) {
    if ($item == '.' || $item == '..' || $item == 'CACHEKEYS') continue;
    $fp = fopen($path.'/'.$item, 'r');
    $data = fread($fp, 4096);

    if (preg_match('/<base href="([^"]+)">/i', $data, $m)) {
      $url = $m[1];
      $hash = str_replace('.html', '', $item);
      $newfile .= "$hash = $url\n";
    }// if
    fclose($fp);

  }// while

  fputs($index, $newfile);
  fflush($index);
  fclose($index);

  xarResponseRedirect(xarModURL('googlesearch', 'admin', 'managecached'));
}

?>