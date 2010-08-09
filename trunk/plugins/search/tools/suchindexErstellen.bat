ECHO OFF

ECHO --------------------------------------------------------------------------
ECHO  Dieses Programm erstellt den Suchindex fuer eine Suchmaschine!
ECHO --------------------------------------------------------------------------
ECHO    Der Aufruf muss so aussehen:
ECHO    java -classpath index.jar suchindexErstellen verzeichnis verfolgen meta
ECHO ---------------------------------------------
ECHO    - das Tool muss in dem Verzeichnis gestartet werden, in dem auf ihrer
ECHO      Festplatte die HTML-Dateien fuer die Suche liegen.
ECHO ---------------------------------------------
ECHO    "verzeichnis" sollte das Wurzelverzeichnis ihrer Homepage auf ihrer
ECHO                  Fesplatte sein. Wenn dies das aktuelle Verzeihnis ist,
ECHO                  geben sie einfach ".\" ein
ECHO    "verfolgen"   sollte "true" sein, wenn die untergeordneten
ECHO                  Verzeichnisse ihrer Homepage ebenfalls nach Stichworten
ECHO                  durchsucht werden sollen, bzw. "false" wenn nicht ...
ECHO    "meta"        sollte "true" sein, wenn in den Meta-Tags enthaltene
ECHO                  Keywords ebenfalls durchsucht werden sollen und "false"
ECHO                  falls nicht ...

ECHO ON
java -classpath index.jar index.suchindexErstellen .\ true true
