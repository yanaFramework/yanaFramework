<?php
$COMPONENTS += array
(
'skin' => array(
    LABEL => array(
        'de' => 'Skins',
        'en' => 'Skins'),
    IS => FOLDER
    ),
'blue' => array(
    LABEL => array(
        'de' => 'der blaue Skin',
        'en' => 'the BLUE skin'),
    FILES => array(
        'skins/blue',
        'config/profiles/blue'),
    IS => DISABLED
    ),
'light' => array(
    LABEL => array(
        'de' => 'Leichtigkeit',
        'en' => 'Light'),
    FILES => array(
        'skins/light',
        'config/profiles/light'),
    IS => DISABLED
    ),
'minimal' => array(
    LABEL => array(
        'de' => 'Minimalistisch',
        'en' => 'Minimal'),
    FILES => array(
        'skins/minimal',
        'config/profiles/minimal'),
    IS => DISABLED
    ),
'natural' => array(
    LABEL => array(
        'de' => 'Natur',
        'en' => 'Natural'),
    FILES => array(
        'skins/natural',
        'config/profiles/natural'),
    IS => DISABLED
    ),
'simple' => array(
    LABEL => array(
        'de' => 'Simpel',
        'en' => 'Simple'),
    FILES => array(
        'skins/simple',
        'config/profiles/simple'),
    IS => DISABLED
    ),
'summer' => array(
    LABEL => array(
        'de' => 'Sommer',
        'en' => 'Summer'),
    FILES => array(
        'skins/summer',
        'config/profiles/summer'),
    IS => DISABLED
    )
);
?>
