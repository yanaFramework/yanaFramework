<?php
$COMPONENTS += array
(
'plugin' => array(
    LABEL => array(
        'de' => 'Plugins',
        'en' => 'Plugins'),
    IS => FOLDER
    ),
'calendar' => array(
    LABEL => array(
        'de' => 'Kalender',
        'en' => 'calendar'),
    FILES => array(
        'plugins/calendar',
        ),
    IS => DISABLED
    ),
'guestbook' => array(
    LABEL => array(
        'de' => 'Gästebuch',
        'en' => 'Guestbook'),
    FILES => array(
        'plugins/guestbook',
        'plugins/guestbookadmin',
        ),
    IS => DISABLED
    ),
'blog' => array(
    LABEL => array(
        'de' => 'Blog',
        'en' => 'Blog'),
    FILES => array(
        'plugins/blog',
        ),
    IS => DISABLED
    ),
'counter' => array(
    LABEL => array(
        'de' => 'Besucherzähler',
        'en' => 'Visitor counter'),
    FILES => array(
        'plugins/counter'),
    IS => DISABLED
    ),
'rss' => array(
    LABEL => array(
        'de' => 'RSS-Anzeige',
        'en' => 'RSS-to-HTML factory'),
    FILES => array(
        'plugins/rss',
        'plugins/rssadmin'),
    IS => DISABLED
    ),
'search' => array(
    LABEL => array(
        'de' => 'Suchmaschine',
        'en' => 'Search Engine'),
    FILES => array(
        'plugins/search',
        'plugins/searchadmin'),
    IS => DISABLED
    ),
'project' => array(
    LABEL => array(
        'de' => 'Projektverwaltung (Demo)',
        'en' => 'project management (demo)'),
    FILES => array(
        'plugins/project',
        ),
    IS => DISABLED
    ),
'mediadb' => array(
    LABEL => array(
        'de' => 'Mediendatenbank (Demo)',
        'en' => 'Media Database (demo)'),
    FILES => array(
        'plugins/mediadb'),
    IS => DISABLED
    ),
'mediagallery' => array(
    LABEL => array(
        'de' => 'Bildergalerie (Demo)',
        'en' => 'Media Gallery (demo)'),
    FILES => array(
        'plugins/mediadb',
        'plugins/mediagallery'),
    IS => DISABLED
    ),
'sdk' => array(
    LABEL => array(
        'de' => 'Software Developement Kit',
        'en' => 'Software Developement Kit'),
    FILES => array(
        'plugins/sdk'),
    IS => DISABLED
    ),
'dbtools' => array(
    LABEL => array(
        'de' => 'SDK Datenbankwerkzeuge',
        'en' => 'SDK database tools'),
    FILES => array(
        'plugins/dbtools',
        ),
    IS => DISABLED
    ),
'helloworld' => array(
    LABEL => array(
        'de' => 'SDK "Hallo Welt"-Beispiel',
        'en' => 'SDK Hello World example'),
    FILES => array(
        'plugins/helloworld',
        ),
    IS => DISABLED
    ),
'datasources' => array(
    LABEL => array(
        'de' => 'Verbindungen zu mehr als einer Datenbank verwenden',
        'en' => 'Load data from more than one database source'),
    FILES => array(
        'plugins/datasources',
        ),
    IS => DISABLED
    ),
'ipblock' => array(
    LABEL => array(
        'de' => 'IP-Filter',
        'en' => 'IP filter'),
    FILES => array(
        'plugins/ipblock',
        'plugins/ipblockadmin',
        ),
    IS => DISABLED
    ),
'antispam' => array(
    LABEL => array(
        'de' => 'Spamfilter',
        'en' => 'Spam filter'),
    FILES => array(
        'plugins/antispam',
        'plugins/antispamadmin',
        ),
    IS => ENABLED
    ),
'db' => array(
    LABEL => array(
        'de' => 'Datenbankunterstützung',
        'en' => 'database support'),
    FILES => array(
        'plugins/dbadmin',
        ),
    IS => DISABLED
    ),
'user' => array(
    LABEL => array(
        'de' => 'Nutzerverwaltung',
        'en' => 'user administration'),
    FILES => array(
        'plugins/useradmin',
        ),
    IS => DISABLED
    ),
'userauthadmin' => array(
    LABEL => array(
        'de' => 'Authentifizierungsoptionen: für zusätzliche Authentifizierungsmethoden wie LDAP.',
        'en' => 'User Authentication Management: to configure authentication methods like LDAP.'),
    FILES => array(
        'plugins/userauthadmin',
        ),
    IS => DISABLED
    ),
'usergroup' => array(
    LABEL => array(
        'de' => 'Nutzergruppen und Rollen verwenden',
        'en' => 'Activate user groups and roles'),
    FILES => array(
        'plugins/usergroup',
        'plugins/usergroupadmin',
        ),
    IS => ENABLED
    ),
'userprofile' => array(
    LABEL => array(
        'de' => 'Persönliche Profilseiten für Nutzer',
        'en' => 'Personal profile page for all users'),
    FILES => array(
        'plugins/userprofile',
        ),
    IS => DISABLED
    ),
'userproxy' => array(
    LABEL => array(
        'de' => 'Nutzerrechte übertragen (z.Bsp. als Urlaubsvertretung)',
        'en' => 'Transfer your permissions to another user (e.g. during your vacation)'),
    FILES => array(
        'plugins/userproxy',
        ),
    IS => DISABLED
    ),
'userpwdadmin' => array(
    LABEL => array(
        'de' => 'Passwortqualitätsrichtlinie festlegen',
        'en' => 'Password quality setup'),
    FILES => array(
        'plugins/userpwdadmin',
        ),
    IS => DISABLED
    ),
'userregistration' => array(
    LABEL => array(
        'de' => 'Nutzerregistrierung: Besucher können sich als Nutzer registrieren',
        'en' => 'User registration: Allow visitors to sign up for user accounts'),
    FILES => array(
        'plugins/userregistration',
        ),
    IS => DISABLED
    )
);
?>
