<?php

/**
 *
 * The "media-types" directory contains a subdirectory for each content
 * type and each of those directories contains a file for each content
 * subtype.
 *
 *                                |-application-
 *                                |-audio-------
 *                                |-image-------
 *                  |-media-types-|-message-----
 *                                |-model-------
 *                                |-multipart---
 *                                |-text--------
 *                                |-video-------
 *
 *    URL = ftp://ftp.isi.edu/in-notes/iana/assignments/media-types
 */



$modversion['name']         = 'filemanager';
$modversion['id']           = '666';
$modversion['version']      = '2.0.0'; // long jump from current filemanager version
$modversion['displayname']  = xarML('FileManager');
$modversion['description']  = 'Virtual/Real Directory and File Management';
$modversion['credits']      = 'docs/credits.txt';
$modversion['help']         = 'docs/help.txt';
$modversion['changelog']    = 'docs/changelog.txt';
$modversion['license']      = 'docs/license.txt';
$modversion['official']     = 1;
$modversion['author']       = 'Marie Altobelli (Ladyofdragons); Michael Cortez (mcortez); Carl P. Corliss (rabbitt)';
$modversion['contact']      = 'ladyofdragons@xaraya.com; mcortez@xaraya.com; rabbitt@xaraya.com';
$modversion['admin']        = 1;
$modversion['user']         = 0;
$modversion['class']        = 'Utility';
$modversion['category']     = 'Global';
$modversion['dependency']   = array(999);
?>
