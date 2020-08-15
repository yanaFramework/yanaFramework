<?php
$COMPONENTS += array
(
'yana' => array(
    LABEL => array(
        'de' => 'Yana Framework',
        'en' => 'Yana Framework'),
    IS => MANDATORY,
    FILES => array(
        'cache/',
        'common_files/',
        'config/db',
        'config/dtd',
        'config/profiles/default.config',
        'config/profiles/delete_me.txt',
        'config/.',
        'config/dbconfig',
        'config/reserved',
        'config/system',
        'libs/',
        'plugins/config',
        'plugins/aboutconfig',
        'plugins/dbadmin',
        'plugins/defaultlibrary',
        'plugins/downloads',
        'plugins/sitemap',
        'plugins/user/',
        'skins/default/',
        'skins/.config/',
        'skins/default.skin.xml',
        'plugins/delete_me.txt',
        '.',
        'index.php',
        'library.php',
        ),
    ),
'diagnostics' => array(
    LABEL => array(
        'de' => 'Selbstdiagnose',
        'en' => 'Diagnostics'),
    FILES => array(
        'plugins/diagnostics/',),
    IS => MANDATORY
    ),
'cli' => array(
    LABEL => array(
        'de' => 'Tool für den Zugriff über die Kommandozeile (zur Verwendung von Cron-Jobs)',
        'en' => 'command line interface (to use with cron jobs)'),
    FILES => array(
        'cli.php'),
    IS => DISABLED
    )
);
?>
