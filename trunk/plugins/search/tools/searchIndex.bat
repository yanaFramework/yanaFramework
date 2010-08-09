ECHO OFF

ECHO --------------------------------------------------------------------------
ECHO  This program creates an index for a search engine!
ECHO -------------------------------------------------------------------------
ECHO    This is how the call to this file should look like:
ECHO    java -classpath index.jar suchindexErstellen folder follow meta
ECHO ---------------------------------------------
ECHO    - this tool should be run in the same folder on your local hard disk
ECHO      where the HTML files are stored, that you want to be searched.
ECHO ---------------------------------------------
ECHO    "folder" should be the root folder of your web site stored on your
ECHO             hard disk. If this is also the current directory,
ECHO             just enter ".\" 
ECHO    "follow" should be "true", if included sub-directories of your web site
ECHO             are to be searched for keywords too, or "false" otherwise ...
ECHO    "meta"   should be "true", if the keywords contained in meta-tags of a 
ECHO             document are to be included, or "false" otherwise ...

ECHO ON
java -classpath index.jar index.suchindexErstellen .\ true true
