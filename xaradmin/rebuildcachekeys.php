<?php
/**
 * Xaraya Google Search
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Google Search Module
 * @link http://xaraya.com/index.php/release/809.html
 * @author John Cox
 */
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