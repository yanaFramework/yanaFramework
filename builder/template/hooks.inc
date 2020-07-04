<?php
// A list of hook function to be called during specific parts of the installation process
$HOOKS = array(
   HOOK_TEST => function($showDetails) { return true; },       // called after unpacking files, to perform checks on the installation folder, find and display potential post-installation problems
   HOOK_ADMIN => function($newPassword) { return true; },      // called after unacking files, when the user is prompted to enter the initial password for the administrator account
   HOOK_INSTALLATION_COMPLETE => function() { return true; },  // called at the end of the installation process, after everything is done
   HOOK_TERMINATE_PROGRAM => function() { return true; }       // called when the user terminates the installation process before it is finished
);
