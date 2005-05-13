<?php

/**
 * Browse for files.
 * I'm keeping this generic, so it can be reused.
 *
 * Identifying the base directory:
 * @param basedir string the absolute or relative base directory
 * @param module string the name of the module to look in (treated as optional root)
 *
 * Matching files and directories (filtering rules):
 * @param match_glob string file glob expression
 * @param match_re string regular expression
 * @param match_exact string expression
 *
 * Transform functions (modifying the filename to be returned):
 * @param strip_re string regular expression matching details to strip out of the filename
 *
 * Flags:
 * @param levels integer number of levels to recurse (default=max_levels)
 * @param retpath string 'abs' will return the absolute OS path, 'rel' the relative path to the basedir, 'file' just the filename
 * @param retdirs boolean flag that indicates all directories should be returned (default false)
 * @param retfiles boolean flag that indicates all files should be returned (default true)
 *
 * @todo move this to some central location; file browsing should be a kernel/core function
 * @todo support sorting of the files (by name, by date, asc/desc, etc)
 * @todo support timestamp matching
 * @todo support other areas than the module 'home', e.g. module theme area
 * @todo support retpath value 'rel2' for path relative to the site entry point
 * @todo allow the returning of more detailed file information than just names - full inode info
 * @todo support file type selection (e.g. just files, just directories)
 * @todo provide a simple transform function for the filename, probably a callback function
 * @todo allow wildcards for modules and even for basedir, so the function will scan multiple modules or trees
 */

function xarpages_userapi_browse_files($args)
{
    extract($args);

    // Maximum possible directory levels the function will follow
    $max_levels = 255;

    // Levels lies between 1 and max_levels.
    // Set levels=1 to stay in a single diectory.
    if (!xarVarValidate('int:1:'.$max_levels, $levels, true)) {$levels = $max_levels;}

    // The path return format is an unumerated type.
    if (!xarVarValidate('enum:abs:rel:file', $retpath, true)) {$retpath = 'file';}

    // Other flags.
    if (!isset($retdirs)) {$retdirs = false;}
    if (!isset($retfiles)) {$retfiles = true;}

    // Get the root directory.
    $rootdir = '.';

    // If the module is set, then find its home.
    if (!empty($module)) {
        // Assume for now that we are looking only in the module home directory.
        $modinfo = xarModGetInfo(xarModGetIDFromName($module));
        if (!empty($modinfo)) {
            $rootdir = './modules/' . $modinfo['directory'];
        }
    }
    
    // Get the base directory.
    // A relative base directory will be added to the [non-empty] root directory.
    // An absolute base directory will override the root directory.
    if (!empty($basedir)) {
        $basedir = trim($basedir);
        // TODO: is this the only check we need to do?
        if (substr($basedir, 0, 1) != '/' && !empty($rootdir)) {
            // The basedir is a relative path.
            $basedir = $rootdir . '/' . $basedir;
        }
    } else {
        $basedir = $rootdir;
    }

    // Get the absolute basedir path.
    $basedir = realpath($basedir);
    if (empty($basedir)) {
        // The base directory does not exist.
        return;
    }

    // Now we have the absolute base pathname. Start the search.
    $filelist = array();
    $scandir = array();

    // Start the file scan on the base directory.
    array_push($scandir, array(1, ''));

    while (!empty($scandir)) {
        list($thislevel, $thisdir) = array_shift($scandir);
        if ($dh = @opendir($basedir . $thisdir)) {
            while(($filename = @readdir($dh)) !== false) {
                // Got a file or directory.

                if (is_file($basedir . $thisdir . '/' . $filename)) {
                    // Go to the next file if we don't want to return files.
                    if (!$retfiles) {continue;}

                    // Check the filtering rules.
                    if (!empty($match_glob) && @fnmatch($match_glob, $filename) !== true) {continue;}
                    if (!empty($match_re) && @preg_match($match_preg, $filename) !== true) {continue;}
                    if (!empty($match_exact) && $match_exact !== $filename) {continue;}
                }

                if (is_dir($basedir . $thisdir . '/' . $filename)) {
                    // Skip the current and parent directories.
                    if ($filename == '.' || $filename == '..') {continue;}

                    if ($thislevel < $levels) {
                        // We have not maxed out on the levels yet, so go deeper.
                        array_push($scandir, array($thislevel + 1, $thisdir . '/' . $filename));
                    }

                    // Go to the next file if we don't want to log the directory in the result set.
                    if (!$retdirs) {continue;}

                    // Suffix to indicate this is a directory.
                    $filename .= '/';
                }

                // Strip out parts of the filename if necessary
                if (!empty($strip_re)) {
                    $filename = @preg_replace($strip_re, '', $filename);
                }
                
                // If we have got this far, then we have a file or directory to return.
                switch (strtolower($retpath)) {
                    case 'abs':
                        $filelist[] = $basedir . $thisdir . '/' . $filename;
                        break;
                    case 'rel':
                        $filelist[] = ltrim($thisdir . '/' . $filename, '/');
                        break;
                    case 'file':
                        $filelist[] = $filename;
                        break;
                }
            }
            closedir($dh);
        }
    }

    return $filelist;
}

?>