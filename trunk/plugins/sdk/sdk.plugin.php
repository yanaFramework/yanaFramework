<?php
/**
 * Software Development Kit
 *
 * The SDK is an assistant to help you create new plugins.<br />
 * <br />
 * <b>Attention!</b> The plugins is intended to be used by developers and
 * should not be activated on a public web server.
 *
 * {@translation
 *
 *    de:  Software Development Kit
 *
 *         Das SDK ist ein Assistent zum Generieren neuer Plugins.<br />
 *         <br />
 *         <b>Achtung!</b> Dieses Programm ist für den Einsatz durch Entwickler zum Erstellen neuer Plugins
 *         gedacht und sollte deshalb auf einem öffentlich zugänglichen Webserver nicht aktiviert werden.
 * }
 *
 * @type       primary
 * @group      sdk
 * @author     Thomas Meyer
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

/**
 * @ignore
 */
require_once 'pluginworker.class.php';

/**
 * Software Developement Kit
 *
 * This implements a code generator utility
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_sdk extends StdClass implements IsPlugin
{

    /**
     * List of DBMS ids and names
     *
     * @access  private
     * @static
     * @var     array
     * @ignore
     */
    private static $_listOfDBMS = array(
        'mysql'  => 'MySQL',
        'db2'    => 'DB2',
        'mssql'  => 'MS-SQL',
        'oci8'   => 'Oracle',
        'pgsql'  => 'PostgreSQL'
    );

    /**
     * Default event handler
     *
     * returns bool(true) on success and bool(false) on error
     *
     * @access  public
     * @return  bool
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of arguments passed to the function
     * @ignore
     */
    public function catchAll($event, array $ARGS)
    {
        return true;
    }

    /**
     * Software Development Kit
     *
     * Enter information used to create a new plugin.
     *
     * @type        read
     * @user        group: admin, level: 100
     * @template    templates/sdk.html.tpl
     * @style       templates/sdk.css
     * @script      templates/sdk.js
     * @language    sdk
     * @menu        group: start
     * @safemode    true
     *
     * @access      public
     * @return      bool
     */
    public function sdk()
    {
        $yana = Yana::getInstance();
        $dir = $yana->plugins->{'sdk:/images/logos'};
        $dir->read();
        $yana->setVar('FILES', $dir->getContent());
        $yana->setVar('IMG_SRC', $dir->getPath());
        if (isset($_SESSION[__CLASS__])) {
            $yana->setVar('PLUGIN', unserialize($_SESSION[__CLASS__]));
        }
        $yana->setVar('GROUPS', SessionManager::getGroups());
        $yana->setVar('ROLES', SessionManager::getRoles());

        $yana->setVar('LIST_OF_DBMS', self::$_listOfDBMS);
        return true;
    }

    /**
     * Create plugin
     *
     * Handle plugin settings.
     *
     * @type        write
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: sdk
     * @onerror     goto: sdk
     * @safemode    true
     *
     * @access      public
     * @param       array $ARGS array of params passed to the function
     * @return      bool
     */
    public function sdk_write_plugin(array $ARGS)
    {
        /* load files */
        $pluginWorker = new PluginWorker();

        $plugin = $pluginWorker->getPlugin();
        $plugin->setTitle($ARGS['name']);
        if (!empty($ARGS['parent'])) {
            $plugin->setParent($ARGS['parent']);
        }
        if (!empty($ARGS['package'])) {
            $plugin->setGroup($ARGS['package']);
        }
        if (!empty($ARGS['type'])) {
            $plugin->setType($ARGS['type']);
        }
        if (!empty($ARGS['priority'])) {
            $plugin->setPriority($ARGS['priority']);
        }
        if (!empty($ARGS['author'])) {
            $plugin->setAuthor($ARGS['author']);
        }
        if (!empty($ARGS['description'])) {
            $plugin->setText($ARGS['description']);
        }
        if (!empty($ARGS['url'])) {
            $plugin->setUrl($ARGS['url']);
        }

        if (!empty($ARGS['image'])) {
            $pluginWorker->setImage($ARGS['image']);
        }

        if (!empty($ARGS['overwrite'])) {
            $pluginWorker->setOverwrite(true);
        }

        // SQL files
        assert('!isset($dbms); // Cannot redeclare $dbms');
        foreach (array_keys(self::$_listOfDBMS) as $dbms)
        {
            if (!empty($_FILES[$dbms]['tmp_name'])) {
                if (!preg_match('/^\S+\.sql$/s', $_FILES[$dbms]['name'])) {
                    throw new InvalidInputWarning();
                }
                $pluginWorker->addSqlFile($dbms, $_FILES[$dbms]['tmp_name']);
            }
        }
        unset($dbms);

        // Schema file
        if (!empty($_FILES['sourcefile']['tmp_name'])) {
            assert('!isset($node); // Cannot redeclare var $node');
            $node = simplexml_load_file($_FILES['sourcefile']['tmp_name']);
            if (!isset($node['name'])) {
                $node->addAttribute('name', $plugin->getId());
            }
            $pluginWorker->setSchemaXml($node);
            unset($node);
        }

        // Custom interface
        if ($ARGS['interface']) {
            foreach (explode("\n", $ARGS['interface']) as $action)
            {
                $action = explode(",", $action);
                $methodName = array_shift($action);
                if(empty($methodName)) {
                    continue;
                }
                $method = $plugin->addMethod($methodName);
                $method->setType(array_shift($action));
                $method->setTemplate(array_shift($action));
                $method->setUserLevels(array_shift($action), array_shift($action), array_shift($action));
                $method->setMenu(array_shift($action));
            }
            unset($action, $methodName, $method);
        }

        $pluginWorker->createPlugin();

        /* load new plugin */
        $yana = Yana::getInstance();
        $yana->callAction('refresh_pluginlist');
        self::_createBackup($ARGS);

        return true;

    }

    /**
     * Backup current settings
     *
     * @access  private
     * @static
     * @param   array  $arguments  form arguments
     * @ignore
     */
    private static function _createBackup(array $arguments)
    {
        $_SESSION[__CLASS__] = serialize($arguments);
    }

}

?>