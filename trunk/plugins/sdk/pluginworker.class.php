<?php
/**
 * YANA library
 *
 * Software:  Yana PHP-Framework
 * Version:   {VERSION} - {DATE}
 * License:   GNU GPL  http://www.gnu.org/licenses/
 *
 * This program: can be redistributed and/or modified under the
 * terms of the GNU General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://www.gnu.org/licenses/.
 *
 * This notice MAY NOT be removed.
 *
 * @package  yana
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

/**
 * @ignore
 */
require_once 'pluginconfigurator.class.php';

/**
 * <<worker>> Plugin generator
 *
 * This is a "worker" class, which means that is primary function is to serve
 * as a controller, thus implementing business logic, having no - or at least
 * not too much - data itself.
 * Instead it "works" on a given object and provides functions to manipulate it.
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class PluginWorker extends Object
{
    /**
     * plugin configuration
     *
     * @access  private
     * @var     PluginConfigurator
     * @ignore
     */
    private $plugin = null;

    /**
     * directory
     *
     * @access  private
     * @var     Dir
     * @ignore
     */
    private $pluginDir = null;

    /**
     * directory
     *
     * @access  private
     * @var     Dir
     * @ignore
     */
    private $skinDir = null;

    /**
     * list of files to copy
     *
     * @access  private
     * @var     array
     * @ignore
     */
    private $filesToCopy = array();

    /**
     * overwrite existing files
     *
     * @access  private
     * @var     bool
     * @ignore
     */
    private $isOverwrite = false;

    /**
     * list of templates to create
     *
     * @access  private
     * @var     array
     * @ignore
     */
    private $templates = array();

    /**
     * database schema
     *
     * @access  private
     * @var     DDLDatabase
     * @ignore
     */
    private $schema = null;

    /**
     * translation units
     *
     * Syntax: array('id' => array('source' => string, 'target' => string))
     *
     * @access  private
     * @var     array
     * @ignore
     */
    private $translations = array();

    /**
     * constructor
     *
     * @access  public
     * @param   PluginConfigurator  $plugin  new plugin configuration
     */
    public function __construct(PluginConfigurator $plugin = null)
    {
        if (!is_null($plugin)) {
            $this->plugin = $plugin;
        }
    }

    /**
     * get plugin directory
     *
     * @access  protected
     * @return  Dir
     * @ignore
     */
    protected function getPluginDir()
    {
        if (!isset($this->pluginDir)) {
            $yana = Yana::getInstance();
            $this->pluginDir  = new Dir($yana->getVar('PLUGINDIR') . $this->getPlugin()->getId() . '/');
        }
        return $this->pluginDir;
    }

    /**
     * set image
     *
     * @access  public
     * @param   string  $image  image path
     */
    public function setImage($image)
    {
        switch (true)
        {
            case !is_string($image):
            case !preg_match('/^[\d\w-_\.]+\.(gif|jpg|png)$/si', $image):
                $options = array(
                    'FIELD' => 'IMAGE',
                    'VALUE' => print_r($image, true),
                    'VALID' => 'file.gif, file.jpg, file.png'
                );
                $error = new InvalidSyntaxWarning();
                $error->setData($options);
                throw $error;
            break;
            default:

                $yana = Yana::getInstance();
                $logoDir = $yana->plugins->{'sdk:/images/logos'};
                $iconDir = $yana->plugins->{'sdk:/images/icons'};

                // copy preview image
                $this->filesToCopy[] = array(
                    'src' => $logoDir->getPath() . $image,
                    'dest' => $this->getPluginDir()->getPath() . 'preview.png'
                );
                // copy icon
                $this->filesToCopy[] = array(
                    'src' => $iconDir->getPath() . $image,
                    'dest' => $this->getPluginDir()->getPath() . 'icon.png'
                );

            break;
        }
    }

    /**
     * get plugin-configurator
     *
     * @access  public
     * @return  PluginConfigurator
     */
    public function getPlugin()
    {
        if (!isset($this->plugin)) {
            $this->plugin = new PluginConfigurator();
        }
        return $this->plugin;
    }

    /**
     * overwrite existing files
     *
     * @access  public
     * @param   bool  $isOverwrite  overwrite existing files (true = yes, false = no)
     */
    public function setOverwrite($isOverwrite)
    {
        $this->isOverwrite = !empty($isOverwrite);
    }

    /**
     * add SQL file
     *
     * @access  public
     * @param   string  $dbms  name of dbms
     * @param   string  $file  sql file
     */
    public function addSqlFile($dbms, $file)
    {
        $yana = Yana::getInstance();
        $installDirectory = $yana->getResource('system:/dbinstall/' . mb_strtolower($dbms));
        if (!is_object($installDirectory)) {
            throw new Log("Registry error: there is no registered install directory ".
                "named '{$dbms}'. The uploaded SQL-file could not be saved.");
        }
        if (!$installDirectory->exists()) {
            $installDirectory->create(0777);
        }
        if (!is_dir($installDirectory->getPath())) {
            throw new Log("Unable to create the directory '{$dbms}'. " .
                "The uploaded SQL-file could not be saved.");
        }
        $this->filesToCopy[] = array(
            'src' => $file,
            'dest' => $installDirectory->getPath().$plugin->getId().'.sql'
        );
    }

    /**
     * add an HTML template file
     *
     * @access  protected
     * @param   DDLForm $form  form object the template is based on
     */
    protected function addTemplate(DDLForm $form)
    {
        $yana = Yana::getInstance();
        $plugin = $this->getPlugin();
        $name = $form->getTable();

        // create HTML page
        /* @var $html SmartTemplate */;
        $html = $yana->plugins->{'sdk:/templates/html.smarttemplate'};
        $html->setVar('form', $form);
        $html->setVar('database', $form->getDatabase());
        $this->templates["$name.html.tpl"] = $html->toString();
    }

    /**
     * set schema file
     *
     * @access  public
     * @param   string  $file  sql file
     */
    public function setSchemaFile($file)
    {
        $xddlFile = new XDDL($file);
        $this->schema = $xddlFile->toDatabase();
        $this->findTranslations($this->schema);
        $this->createForms($this->schema);

        $plugin = $this->getPlugin();
        $directory = DDL::getDirectory() . '/';
        $this->filesToCopy[] = array(
            'content' => $this->schema->toString(),
            'dest' => $directory . $plugin->getId() . '.db.xml'
        );
    }

    /**
     * set schema from XML
     *
     * @access  public
     * @param   SimpleXMLElement  $node  database root node
     */
    public function setSchemaXml(SimpleXMLElement $node)
    {
        $this->schema = DDLDatabase::unserializeFromXDDL($node);
        $this->findTranslations($this->schema);
        $this->createForms($this->schema);

        $plugin = $this->getPlugin();
        $directory = DDL::getDirectory() . '/';
        $this->filesToCopy[] = array(
            'content' => $this->schema->toString(),
            'dest' => $directory . $plugin->getId() . '.db.xml'
        );
    }

    /**
     * auto-create forms
     *
     * @access  protected
     * @param   DDLDatabase  $schema  schema definition
     * @ignore
     */
    protected function createForms(DDLDatabase $schema)
    {
        $plugin = $this->getPlugin();
        /* @var $table DDLTable */
        assert('!isset($table); // Cannot redeclare var $table');
        foreach ($schema->getTables() as $table)
        {
            // tables imported from another file using the "include" tag have a different parent schema
            if ($table->getSchemaName() != $schema->getName()) {
                continue; // ignore imported tables
            }
            $tableName = $table->getName();
            if ($schema->isForm($tableName)) {
                continue; // form does already exist
            }

            $form = $schema->addForm($tableName, 'DDLDefaultForm');
            /* @var $form DDLDefaultForm */
            $form->setTable($tableName);

            $action = self::_createActionName($plugin->getId(), 'search', $tableName);
            $form->setSearchAction($action);
            $plugin->addMethod($action)->setAutoGenerated();

            $action = self::_createActionName($plugin->getId(), 'insert', $tableName);
            $form->setInsertAction($action);
            $plugin->addMethod($action)->setAutoGenerated();

            $action = self::_createActionName($plugin->getId(), 'update', $tableName);
            $form->setUpdateAction($action);
            $plugin->addMethod($action)->setAutoGenerated();

            $action = self::_createActionName($plugin->getId(), 'delete', $tableName);
            $form->setDeleteAction($action);
            $plugin->addMethod($action)->setAutoGenerated();

            $action = self::_createActionName($plugin->getId(), 'export', $tableName);
            $form->setExportAction($action);
            $plugin->addMethod($action)->setAutoGenerated();

            // only add download action if there is something to download
            if (count($table->getFileColumns()) > 0) {
                $action = self::_createActionName($plugin->getId(), 'download', $tableName);
                $form->setDownloadAction($action);
            }

            // auto-generate links between tables along existing foreign keys
            /* @var $foreign DDLForeignKey */
            assert('!isset($foreign); // Cannot redeclare var $foreign');
            foreach ($table->getForeignKeys() as $foreign)
            {
                $targetTable = $foreign->getTargetTable();
                $keys = $foreign->getColumns();
                $fieldName = current($keys);
                if (empty($fieldName)) {
                    $fieldName = $schema->getTable($targetTable)->getPrimaryKey();
                }
                /* @var $field DDLDefaultField */
                if (!$form->isField($fieldName)) {
                    $field = $form->addField($fieldName, 'DDLDefaultField');
                } else {
                    $field = $form->getField($fieldName);
                }
                $targetAction = self::_createActionName($plugin->getId(), 'form', $targetTable);
                $action = $field->addEvent($targetAction);
            }
            unset($foreign);

            // let's auto-hide the primary-key
            $key = $table->getPrimaryKey();
            if (!$form->isField($key)) {
                $field = $form->addField($key, 'DDLDefaultField');
                $field->setVisible(false);
            }

            $form->setAllInput(true);

            $this->addTemplate($form);

        } // end foreach
    }

    /**
     * get XLIFF file
     *
     * Creates the content of a XLIFF file and returns it as a string.
     *
     * @access  protected
     * @param   string  $source  source language
     * @param   string  $target  target language
     * @return  string
     * @ignore
     */
    protected function getXliff($source = "en", $target = "en")
    {
        assert('is_string($source); // Wrong argument type argument 1. String expected');
        assert('is_string($target); // Wrong argument type argument 2. String expected');

        $yana = Yana::getInstance();
        /* @var $xliffTemplate SmartTemplate */
        $xliffTemplate = $yana->plugins->{'sdk:/templates/language.smarttemplate'};
        $xliffTemplate->setVar('source', $source);
        $xliffTemplate->setVar('target', $target);
        $xliffTemplate->setVar('translations', $this->translations);
        return $xliffTemplate->toString();
    }

    /**
     * create plugin
     *
     * @access  public
     */
    public function createPlugin()
    {
        $plugin = $this->getPlugin();
        $pluginDir = $this->getPluginDir();
        $skinDir = new Dir($pluginDir->getPath() . '/templates/');
        $langDir = new Dir($pluginDir->getPath() . '/languages/');
        $enDir = new Dir($langDir->getPath() . '/en/');
        $deDir = new Dir($langDir->getPath() . '/de/');
        if ($this->isOverwrite) {
            if ($pluginDir->exists()) {
                $pluginDir->delete(true);
            }
        } elseif ($pluginDir->exists()) {
            $error = new AlreadyExistsWarning();
            $error->setData(array('ID' => $plugin->getId()));
            throw new $error;
        }
        // create directories
        $pluginDir->create(0777);
        $skinDir->create(0777);
        $langDir->create(0777);
        $enDir->create(0777);
        $deDir->create(0777);

        // add program title
        $this->addTranslation('program_title', $plugin->getTitle());

        /* If you wish to add a default menu for the plug-in, you may do it with this code:
         * $plugin->addMenu($plugin->getId(), $plugin->getTitle());
         */

        // copy files
        assert('!isset($file); // Cannot redeclare var $file');
        foreach ($this->filesToCopy as $file)
        {
            if (isset($file['src'])) {
                if (!copy($file['src'], $file['dest'])) {
                    $error = new NotWriteableError();
                    $error->setData(array('FILE' => $file['dest']));
                    throw new $error;
                }
            } elseif (isset($file['content'])) {
                if (file_put_contents($file['dest'], $file['content']) === false) {
                    $error = new NotWriteableError();
                    $error->setData(array('FILE' => $file['dest']));
                    throw new $error;
                }
            }
            chmod($file['dest'], 0777);
        }
        unset($file);

        // copy templates
        assert('!isset($fileName); // Cannot redeclare var $fileName');
        assert('!isset($content); // Cannot redeclare var $content');
        foreach ($this->templates as $fileName => $content)
        {
            file_put_contents($skinDir->getPath() . '/' . $fileName, $content);
        }
        unset($fileName, $content);

        // create class skeleton
        $phpFile = new TextFile($pluginDir->getPath() . $plugin->getId() . '.plugin.php');
        $phpFile->create();
        $phpFile->setContent($this->getClassSkeleton());
        if (!$phpFile->write()) {
            $error = new NotWriteableError();
            $error->setData(array('FILE' => $phpFile->getPath()));
            throw new $error;
        }

        // create AJAX-Yana bridge
        $apiFile = new TextFile($skinDir->getPath() . '/api.js');
        $apiFile->create();
        $apiFile->setContent($this->getJsApi());
        if (!$apiFile->write()) {
            $error = new NotWriteableError();
            $error->setData(array('FILE' => $apiFile->getPath()));
            throw new $error;
        }

        // create XLIFF translation file
        $xliffFile = new TextFile($enDir->getPath() . '/' . $plugin->getId() . '.xlf');
        $xliffFile->create();
        $xliffFile->setContent("<?xml version=\"1.0\"?>\n" . $this->getXliff());
        if (!$xliffFile->write()) {
            $error = new NotWriteableError();
            $error->setData(array('FILE' => $xliffFile->getPath()));
            throw new $error;
        }
        $xliffFile = new TextFile($deDir->getPath() . '/' . $plugin->getId() . '.xlf');
        $xliffFile->create();
        $xliffFile->setContent("<?xml version=\"1.0\"?>\n" . $this->getXliff("en", "de"));
        if (!$xliffFile->write()) {
            $error = new NotWriteableError();
            $error->setData(array('FILE' => $xliffFile->getPath()));
            throw new $error;
        }
    }

    /**
     * add translation
     *
     * Returns the appropriate language token or an empty string if the source was empty.
     *
     * @access  protected
     * @param   string  $id      translation unit identifier
     * @param   string  $source  source text
     * @param   string  $target  target translation
     * @return  string
     * @assert  ("", "") == ""
     * @assert  ("1", "foo") == '{lang id="1"}'
     * @assert  ("1", '{lang id="1"}') == '{lang id="1"}'
     * @ignore
     */
    protected function addTranslation($id, $source, $target = "")
    {
        assert('is_string($id); // Wrong argument type argument 1. String expected');
        assert('is_string($source) || is_null($source); // Wrong argument type argument 2. String expected');
        assert('is_string($target); // Wrong argument type argument 3. String expected');

        if (!empty($source) && strpos($source, YANA_LEFT_DELIMITER) === false) {
            $i = "";
            // seek next free id (add auto-increment number, where necessary)
            while (isset($this->translations["$id$i"]))
            {
                $i++; // auto-convert to integer
            }
            $this->translations["$id$i"] = array(
                'source' => htmlspecialchars("$source"),
                'target' => htmlspecialchars("$target")
            );
            return YANA_LEFT_DELIMITER . "lang id=\"$id$i\"" . YANA_RIGHT_DELIMITER;
        } else {
            return "$source";
        }
    }

    /**
     * find translation strings in database schema
     *
     * @access  protected
     * @param   DDLDatabase  $schema  database schema
     * @ignore
     */
    protected function findTranslations(DDLDatabase $schema)
    {
        $text = $this->addTranslation($schema->getName(), $schema->getTitle());
        $schema->setTitle($text);
        assert('!isset($table); // Cannot redeclare var $table');
        foreach ($schema->getTables() as $table)
        {
            $text = $this->addTranslation($table->getName(), $table->getTitle());
            $table->setTitle($text);
            unset($text);
            assert('!isset($column); // Cannot redeclare var $column');
            foreach ($table->getColumns() as $column)
            {
                /* @var $column DDLColumn */
                $text = $this->addTranslation($column->getName(), $column->getTitle());
                $column->setTitle($text);
                unset($text);
                $options = $column->getEnumerationItems();
                if (!empty($options)) {
                    $column->setEnumerationItems($this->_findTranslationsInEnumeration($column->getName(), $options));
                }
                unset($options);
                if ($column->getDescription()) {
                    $text = $this->addTranslation($column->getName() . '-description', $column->getDescription());
                    $column->setDescription($text);
                }
                unset($text);
            }
            unset($column);
        }
        unset($table);
        assert('!isset($form); // Cannot redeclare var $form');
        foreach ($schema->getForms() as $form)
        {
            $text = $this->addTranslation($form->getName(), $form->getTitle());
            $form->setTitle($text);
            assert('!isset($field); // Cannot redeclare var $field');
            foreach ($form->getFields() as $field)
            {
                $text = $this->addTranslation($field->getName(), $field->getTitle());
                $field->setTitle($text);
            }
            unset($field);
        }
        unset($form);
    }

    /**
     * recursively find translation candidates in list of enumeration options
     *
     * Returns the scanned/modified list.
     *
     * @access  private
     * @param   string  $id       translation identifier
     * @param   array   $options  list of enumeration options
     * @return  array
     */
    private function _findTranslationsInEnumeration($id, array $options)
    {
        foreach ($options as $name => $text)
        {
            if (is_array($text)) {
                unset($options[$name]);
                $name = $this->addTranslation($id . '.optgroup', $name);
                $options[$name] = $this->_findTranslationsInEnumeration($id, $text);
            } elseif (!is_null($name) && !is_numeric($text)) {
                $options[$name] = $this->addTranslation($id . '.' . $name, $text);
            }
        }
        return $options;
    }

    /**
     * create javascript Ajax Api
     *
     * @access  protected
     * @access  string
     * @ignore
     */
    protected function getJsApi()
    {
        $yana = Yana::getInstance();
        $plugin = $this->getPlugin();
        /* @var $apiTemplate SmartTemplate */
        $apiTemplate = $yana->plugins->{'sdk:/templates/jsapi.smarttemplate'};
        $apiTemplate->setVar('plugin', $plugin);
        $apiTemplate->setVar('class', 'Api' .
                str_replace(' ', '', ucwords(preg_replace('/_/', ' ', $plugin->getId()))));
        return $apiTemplate->toString();
    }

    /**
     * create PHP class skeleton
     *
     * @access  protected
     * @access  string
     * @ignore
     */
    protected function getClassSkeleton()
    {
        $yana = Yana::getInstance();
        /* @var $phpTemplate SmartTemplate */
        $phpTemplate = $yana->plugins->{'sdk:/templates/class.smarttemplate'};
        $phpTemplate->setVar('plugin', $this->getPlugin());
        if (isset($this->schema)) {
            $phpTemplate->setVar('schema', $this->schema);
        } else {
            $phpTemplate->setVar('schema', false);
        }
        return "<?php\n$phpTemplate\n?>";
    }

    /**
     * create action name
     *
     * @access  private
     * @static
     * @param   scalar  $id     plugin id
     * @param   scalar  $name   action name
     * @param   scalar  $table  target table
     * @return  string
     * @ignore
     */
    private static function _createActionName($id, $name, $table)
    {
        return "{$id}_{$name}_{$table}";
    }
}

?>