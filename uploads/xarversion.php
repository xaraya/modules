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



$modversion['name'] = 'uploads';
$modversion['id'] = '666';
$modversion['version'] = '0.75';
$modversion['description'] = 'Upload/Download File Handler';
$modversion['credits'] = 'docs/credits.txt';
$modversion['help'] = 'docs/help.txt';
$modversion['changelog'] = 'docs/changelog.txt';
$modversion['license'] = 'docs/license.txt';
$modversion['official'] = 1;
$modversion['author'] = 'Marie Altobelli (Ladyofdragons); Michael Cortez (mcortez); Carl P. Corliss (rabbitt)';
$modversion['contact'] = 'ladyofdragons@xaraya.com; mcortez@xaraya.com; rabbitt@xaraya.com';
$modversion['admin'] = 1;
$modversion['user']  = 0;
$modversion['securityschema'] = array('uploads::File' => 'Content Type:Content Subtype:File ID');
$modversion['class'] = 'Utility';
$modversion['category'] = 'Global';
$modversion['dependency'] = array(999);
?>
