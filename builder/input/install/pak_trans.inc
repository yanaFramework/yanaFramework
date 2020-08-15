<?php
$COMPONENTS += array
(
'translation' => array(
    LABEL => array(
        'de' => 'Übersetzungen',
        'en' => 'Translations'),
    IS => FOLDER
    ),
'de' => array(
    LABEL => array(
        'de' => 'Deutsch',
        'en' => 'German'),
    FILES => array(
        'languages/de/',
        'languages/de.language.xml'),
    IS => ENABLED
    ),
'en' => array(
    LABEL => array(
        'de' => 'Englisch',
        'en' => 'English'),
    FILES => array(
        'languages/en/',
        'languages/en.language.xml'),
    IS => MANDATORY
    )
);
?>
