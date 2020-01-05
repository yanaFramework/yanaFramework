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

namespace Plugins\SDK;

/**
 * Software Developement Kit
 *
 * This implements a code generator utility
 *
 * @package    yana
 * @subpackage plugins
 */
class SdkPlugin extends \Yana\Plugins\AbstractPlugin
{

    /**
     * List of DBMS ids and names
     *
     * @var  array
     */
    private static $_listOfDBMS = array(
        'mysql'  => 'MySQL',
        'db2'    => 'DB2',
        'mssql'  => 'MS-SQL',
        'oci8'   => 'Oracle',
        'pgsql'  => 'PostgreSQL'
    );

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
     */
    public function sdk()
    {
        $yana = $this->_getApplication();
        $dir = $yana->getPlugins()->{'sdk:/images/logos'};
        $dir->read();
        $yana->setVar('FILES', $dir->getContent());
        $yana->setVar('IMG_SRC', $dir->getPath());
        if (isset($_SESSION[__CLASS__])) {
            $yana->setVar('PLUGIN', unserialize($_SESSION[__CLASS__]));
        }
        $yana->setVar('GROUPS', $this->_getSecurityFacade()->loadListOfGroups());
        $yana->setVar('ROLES', $this->_getSecurityFacade()->loadListOfRoles());

        $yana->setVar('LIST_OF_DBMS', self::$_listOfDBMS);
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
     * @param       array $ARGS array of params passed to the function
     */
    public function sdk_write_plugin(array $ARGS)
    {
        $pluginBuilder = new \Plugins\SDK\ConfigurationBuilder();
        $pluginBuilder->setApplication($this->_getApplication());
        $pluginBuilder->createNewConfiguration();
        $pluginBuilder->setSdkConfiguration($ARGS);
        /* @var $plugin \Yana\Plugins\Configs\IsClassConfiguration */
        $plugin = $pluginBuilder->getPluginConfigurationClass();

        if (!empty($ARGS['image'])) {
            $pluginBuilder->setImage($ARGS['image']);
        }

        // SQL files
        assert(!isset($dbms), 'Cannot redeclare $dbms');
        foreach (array_keys(self::$_listOfDBMS) as $dbms)
        {
            if (!empty($_FILES[$dbms]['tmp_name'])) {
                if (!preg_match('/^\S+\.sql$/s', $_FILES[$dbms]['name'])) {
                    $message = "SQL file expected";
                    $level = \Yana\Log\TypeEnumeration::WARNING;
                    $warning = new \Yana\Core\Exceptions\Files\InvalidTypeException($message, $level);
                    $warning->setFile();
                    throw $warning;
                }
                $pluginBuilder->addSqlFile($dbms, $_FILES[$dbms]['tmp_name']);
            }
        }
        unset($dbms);

        // Schema file
        if (!empty($_FILES['sourcefile']['tmp_name'])) {
            try {
                assert(!isset($node), 'Cannot redeclare var $node');
                $node = simplexml_load_file($_FILES['sourcefile']['tmp_name']);
                if (!isset($node['name'])) {
                    $node->addAttribute('name', $plugin->getId());
                }

                $databaseDirectory = $this->_getApplication()->getVar('DBDIR');
                $pluginBuilder->setSchemaXml($node, $databaseDirectory);

            } catch (\Yana\Db\Ddl\XddlException $e) {
                $message = "Syntax error in XML database definition language file.";
                $level = \Yana\Log\TypeEnumeration::WARNING;
                $warning = new \Yana\Core\Exceptions\Forms\InvalidValueException($message, $level, $e);
                $fieldName = 'SDK.SOURCE';
                $fieldName .= " = " . $e->getMessage();
                $warning->setField($fieldName);
                throw $warning;

            } catch (\Exception $e) {
                $message = "Syntax error in XML database definition language file.";
                $level = \Yana\Log\TypeEnumeration::WARNING;
                $warning = new \Yana\Core\Exceptions\Forms\InvalidValueException($message, $level, $e);
                $fieldName = 'SDK.SOURCE';
                $warning->setField($fieldName);
                throw $warning;
            }
            unset($node);
        }

        $overwriteFiles = !empty($ARGS['overwrite']);
        $pluginBuilder->buildPlugin($overwriteFiles);

        /* load new plugin */
        $this->_getApplication()->execute('refresh_pluginlist');
        self::_createBackup($ARGS);
    }

    /**
     * Backup current settings
     *
     * @param  array  $arguments  form arguments
     */
    private static function _createBackup(array $arguments)
    {
        $_SESSION[__CLASS__] = serialize($arguments);
    }

}

?>