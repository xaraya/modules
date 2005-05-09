<?php

/**
 * Implementation of the metaWeblog.newMediaObject method
 *
 *
 * This method is probably the most difficult, because it requires some
 * careful mapping on the xaraya side.
 * What the method does:
 * It brings in a base64 encoded object of a certain mimetype and a certain
 * name. Name includes (in my clients) a partial path specifier
 *
 * Example request msg:
 *
 *    <?xml version="1.0" encoding="UTF-8"?>
 *    <methodCall>
 *      <methodName>metaWeblog.newMediaObject</methodName>
 *      <params>
 *        <param>
 *          <value><string>2</string></value>
 *        </param>
 *        <param>
 *          <value><string>marcel</string></value>
 *        </param>
 *        <param>
 *          <value><string>******</string></value>
 *        </param>
 *        <param>
 *          <value><struct>
 *            <member>
 *              <name>bits</name>
 *              <value>REMOVED</value> <-- this contains the base64 encoded stuff
 *            </member>
 *            <member>
 *              <name>name</name>
 *              <value><string>images/4358fzjc.pdf</string></value>
 *            </member>
 *            <member>
 *              <name>type</name>
 *              <value><string>application/pdf</string></value>
 *            </member>
 *          </struct></value>
 *        </param>
 *      </params>
 *    </methodCall>
 *
 * The method should return an url to the posted object, which means that subsequent newPost or editPost 
 * methods can refer to the object. The parameters should at least include 'name', 'type' and 'bits'.
 *
 * Idea for Xaraya is the following:
 *
 * 1. Parse the mimetype with the mime module
 * 2. Parse the filename out of the name member
 * 3. Making sure we have only a filename, not the partial path information
 * 4. Use the uploads module to upload the file into xaraya, using the settings
 *    from the uploads module or providing these settings as configvars somewhere
 *    (rationale: server manager decides where stuff goes, and how)
 * 5. Determine the url, optionally based on mimetype (image-> preview, other->icon)
 * 6. Return that url
 */
function metaweblogapi_userapi_newmediaobject($args)
{
    xarLogMessage("Metaweblogapi: newmediaobject");
    extract($args);    
    // Extract the parameters from the xmlrpc msg
    $sn0=$msg->getParam(0);  $blogid   = $sn0->scalarval();
    $sn1=$msg->getParam(1);  $username = $sn1->scalarval();
    $sn2=$msg->getParam(2);  $password = $sn2->scalarval();
    
    // Get the members from the struct which represents the mediaobject
    $sn3=$msg->getParam(3); $struct = $sn3->getval();
    
    // Check requirements
    if (empty($password) || !xarUserLogin($username,$password))
        $err = xarML("Invalid user (#(1)) or wrong password while uploading media object",$username);
    if(!isset($struct['type']) || !isset($struct['name']) || !isset($struct['bits'])) 
        $err = xarML('Your blog client did not supply the required attributes (name, type and bits) for the media object');
    if(!xarModIsAvailable('uploads'))
        $err = xarML('Uploading files is not supported on this server. (the uploads module is required)');
    if(!empty($err)) {
        $output = xarModAPIFunc('xmlrpcserver','user','faultresponse',array('errorstring' => $err));
        return $output;        
    }

    // The basics seem to be ok, start processing the data
    $fileName      = basename($struct['name']);
    $mimeType      = $struct['type'];
    $content       = base64_decode ($struct['bits']);
    $contentLength = strlen($content);
    $userId        = $uid = xarUserGetVar('uid');
    
    // Get some info on where the file would end up.
    $maxLength = xarModGetVar('uploads','file.maxsize');
    $uploadLocation = xarModGetVar('uploads','path.uploads-directory');
    // and check
    if($contentLength > $maxLength) 
        $err = xarML('The file you are trying to upload is too large (#(1) > #(2))',$contentLength,$maxLength);
    if(!file_exists($uploadLocation) || !is_writable($uploadLocation) ) 
        $err = xarML('The configured upload destination does not exist on the server, or is not writable. This is a configuration error on the server');
    if(!empty($err)) {
        $output = xarModAPIFunc('xmlrpcserver','user','faultresponse',array('errorstring' => $err));
        return $output;        
    }

    // We should be good to go.
    // First create the file physically, uploads doesnt have this function yet.
    $fullPath = $uploadLocation . '/' . $fileName;
    if($fileHandle = fopen($fullPath,'wb')) {
        // We created the empty file OK, now write the content to it.
        xarLogMessage("Mediaobject empty file OK");
        $written = fwrite($fileHandle, $content, $contentLength);
        if(!$written || $written != $contentLength) {
            $err = xarML('Writing of the file contents to the file failed, i dunno what happened');
        } else {
            // Write went, add the meta info to the database through uploads module
            xarModAPILoad('uploads','user');
            $fileId = xarModApiFunc('uploads','user','db_add_file', array('userId'       => $userId,
                                                                          'fileName'     => $fileName,
                                                                          'fileLocation' => $fullPath,
                                                                          'fileType'     => $mimeType,
                                                                          'fileStatus'   => _UPLOADS_STATUS_APPROVED,
                                                                          'store_type'   => _UPLOADS_STORE_FILESYSTEM));
            if($fileId == false) {
                $err = xarML('Failed to write the metadata for the uploaded media to the database.');
                $unlink = unlink($fullPath);
                if($unlink == false) {
                    $err = xarML('Failed to undo the write of the file contents, please contact the administrator.');
                }
            }
        }
        fclose($fileHandle);
    }
    if(!empty($err)) {
        $output = xarModAPIFunc('xmlrpcserver','user','faultresponse',array('errorstring' => $err));
        return $output;        
    }
    
    // The file should be there now, and the metadata registered into the database
    // return an url where this file can be reached.
    //$err = 'Debugging created this phony error';
    if(!empty($err)) {
        $output = xarModAPIFunc('xmlrpcserver','user','faultresponse',array('errorstring' => $err));
    } else {
        // Create the proper response, i.e. the url where this media object can be reached.
        $data['url'] = xarModURL('uploads', 'user', 'download', array('fileId' => $fileId));
        $output = xarModAPIFunc('xmlrpcserver','user','createresponse',
                      array('module'  => 'metaweblogapi',
                            'command' => 'newmediaobject',
                            'params'  => $data));
    }
    return $output;
}

?>