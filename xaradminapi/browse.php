<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author mikespub
 */
function publications_adminapi_browse($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check - make sure that all required arguments are present
    // and in the right format, if not then set an appropriate error
    // message and return
    if (empty($basedir) || empty($filetype)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'base directory', 'admin', 'browse',
                    'Publications');
        throw new BadParameterException(null,$msg);
    }

    $filelist = array();

    // Security Check
    if (!xarSecurity::check('SubmitPublications',0)) return $filelist;

    // not supported under safe_mode
    @set_time_limit(120);

    $todo = array();
    $basedir = realpath($basedir);
    array_push($todo, $basedir);
    while (count($todo) > 0) {
        $curdir = array_shift($todo);
        if ($dir = @opendir($curdir)) {
            while(($file = @readdir($dir)) !== false) {
                $curfile = $curdir . '/' . $file;
                if (preg_match("/\.$filetype$/",$file) && is_file($curfile) && filesize($curfile) > 0) {
                    $curfile = preg_replace('#'.preg_quote($basedir,'#').'/#','',$curfile);
                    $filelist[] = $curfile;
                } elseif ($file != '.' && $file != '..' && is_dir($curfile)) {
                    array_push($todo, $curfile);
                }
            }
            closedir($dir);
        }
    }
    natsort($filelist);
    return $filelist;
}

?>