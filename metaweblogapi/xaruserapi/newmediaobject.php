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
 *              <value>REMOVED</value>
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
 * methods can refer to the object.
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
 

?>