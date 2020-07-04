<?php
// A list of files for the file select dialog
$COMPONENTS = array
(
    'package name' => array(
        LABEL => array(
            'en' => 'English label', // mandatory
            'fr' => 'French label',  // you may add more languages like this
        ),
        IS => FOLDER, // FOLDER = This is a header used for all the following entries, you can have multiple such FOLDER entries to indicate multiple packages
    ),
    'feature name' => array(
        LABEL => array(
            'en' => 'English label', // mandatory
            'fr' => 'French label',  // you may add more languages like this
        ),
        IS => DISABLED, // MANDATORY = always selected, ENABLED = selected by default, DISABLED = unselected by default
        FILES => array(
            'files that match this package must begin with this text', // Indicates which files and directories belong to this feature
            'folder/',                                                 // This will unpack all files or folder that start with the text given
            'file',                                                    // For example, "file" unpacks or files or folders whose path starts with "file", like "file.zip" aso.
        ),
    ),
);
