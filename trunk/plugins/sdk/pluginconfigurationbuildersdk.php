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
require_once 'pluginconfigurationclasssdk.php';

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
class PluginConfigurationBuilderSdk extends PluginConfigurationAbstractBuilder
{

    /**
     * plugin configuration
     *
     * @access  protected
     * @var     PluginConfigurationClassSdk
     * @ignore
     */
    protected $object = null;

    /**
     * directory
     *
     * @access  private
     * @var     Dir
     */
    private $_pluginDir = null;

    /**
     * list of files to copy
     *
     * @access  private
     * @var     array
     */
    private $_filesToCopy = array();

    /**
     * list of templates to create
     *
     * @access  private
     * @var     array
     */
    private $_templates = array();

    /**
     * database schema
     *
     * @access  private
     * @var     DDLDatabase
     */
    private $_schema = null;

    /**
     * translation units
     *
     * Syntax: array('id' => array('source' => string, 'target' => string))
     *
     * @access  private
     * @var     array
     */
    private $_translations = array();

    /**
     * SDK configuration
     *
     * @access  private
     * @var     array
     */
    private $_sdkConfiguration = array();

    /**
     * Method configuration.
     *
     * Numeric array containing method head.
     * Used by buildMethod().
     *
     * @access  private
     * @var     array
     * @see     PluginConfigurationBuilderSdk::buildMethod()
     */
    private $_methodConfiguration = array();

    /**
     * get plugin directory
     *
     * @access  protected
     * @return  Dir
     * @ignore
     */
    protected function getPluginDir()
    {
        if (!isset($this->_pluginDir)) {
            $dir = PluginManager::getPluginDirectoryPath();
            $this->_pluginDir  = new Dir($dir . '/' . strtolower($this->object->getId()) . '/');
        }
        return $this->_pluginDir;
    }

    /**
     * set image
     *
     * @access  public
     * @param   string  $image  image path
     * @return  PluginConfigurationBuilderSdk
     * @throws  InvalidSyntaxWarning  when the image has no valid type
     */
    public function setImage($image)
    {
        switch (true)
        {
            case !is_string($image):
            case !preg_match('/^[\d\w-_\.]+\.(gif|jpg|png)$/si', $image):
                $error = new InvalidSyntaxWarning();
                $error->setField('image')->setValue($image)->setValid('file.gif, file.jpg, file.png');
                throw $error;
            break;
            default:

                $yana = Yana::getInstance();
                $pluginManager = $yana->getPlugins();
                $logoDir = $pluginManager->{'sdk:/images/logos'};
                $iconDir = $pluginManager->{'sdk:/images/icons'};

                // copy preview image
                $this->_filesToCopy[] = array(
                    'src' => $logoDir->getPath() . $image,
                    'dest' => $this->getPluginDir()->getPath() . 'preview.png'
                );
                // copy icon
                $this->_filesToCopy[] = array(
                    'src' => $iconDir->getPath() . $image,
                    'dest' => $this->getPluginDir()->getPath() . 'icon.png'
                );

            break;
        }
        return $this;
    }

    /**
     * add SQL file
     *
     * @access  public
     * @param   string  $dbms  name of dbms
     * @param   string  $file  sql file
     * @return  PluginConfigurationBuilderSdk
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
        $this->_filesToCopy[] = array(
            'src' => $file,
            'dest' => $installDirectory->getPath().$plugin->getId().'.sql'
        );
        return $this;
    }

    /**
     * add an HTML template file
     *
     * @access  protected
     * @param   DDLForm $form  form object the template is based on
     * @return  PluginConfigurationBuilderSdk
     */
    protected function addTemplate(DDLForm $form)
    {
        $yana = Yana::getInstance();
        $name = $form->getTable();

        // create HTML page
        /* @var $html SmartTemplate */;
        $html = $yana->getPlugins()->{'sdk:/templates/html.smarttemplate'};
        $html->setVar('form', $form);
        $html->setVar('database', $form->getDatabase());
        $this->_templates["$name.html.tpl"] = (string) $html;
        return $this;
    }

    /**
     * set schema from XML
     *
     * @access  public
     * @param   \SimpleXMLElement  $node  database root node
     * @return  PluginConfigurationBuilderSdk
     */
    public function setSchemaXml(\SimpleXMLElement $node)
    {
        $this->_schema = DDLDatabase::unserializeFromXDDL($node);
        $this->findTranslations($this->_schema);
        $this->buildForms($this->_schema);

        $directory = DDL::getDirectory() . '/';
        $dom = \dom_import_simplexml($this->_schema->serializeToXDDL())->ownerDocument;
        $dom->formatOutput = true;
        $this->_filesToCopy[] = array(
            'content' => $dom->saveXML(),
            'dest' => $directory . strtolower($this->object->getId()) . '.db.xml'
        );
        return $this;
    }

    /**
     * auto-create forms
     *
     * @access  protected
     * @param   DDLDatabase  $schema  schema definition
     */
    protected function buildForms(DDLDatabase $schema)
    {
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

            $form = $schema->addForm($tableName);
            /* @var $form DDLDefaultForm */
            $form->setTable($tableName);

            foreach (array('search', 'insert', 'update', 'delete', 'export') as $actionId)
            {
                $action = self::_buildActionName($this->object->getId(), $actionId, $tableName);
                $form->addEvent($actionId)->setAction($action);
                $method = new PluginConfigurationMethodSdk();
                $method->setAutoGenerated()->setMethodName($action)->setTitle($action);
                $this->object->addMethod($method);
            }

            // only add download action if there is something to download
            if (count($table->getFileColumns()) > 0) {
                $action = self::_buildActionName($this->object->getId(), 'Download', $tableName);
                $form->addEvent('download')->setAction($action);
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
                /* @var $field DDLField */
                $field = null;
                if (!$form->isField($fieldName)) {
                    $field = $form->addField($fieldName);
                } else {
                    $field = $form->getField($fieldName);
                }
                $targetAction = self::_buildActionName($this->object->getId(), 'Form', $targetTable);
                $action = $field->addEvent($targetAction);
            }
            unset($foreign);

            // let's auto-hide the primary-key
            $key = $table->getPrimaryKey();
            if (!$form->isField($key)) {
                $field = $form->addField($key);
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
        $xliffTemplate = $yana->getPlugins()->{'sdk:/templates/language.smarttemplate'};
        $xliffTemplate->setVar('source', $source);
        $xliffTemplate->setVar('target', $target);
        $xliffTemplate->setVar('translations', $this->_translations);
        return (string) $xliffTemplate;
    }

    /**
     * create plugin
     *
     * @access  public
     * @param   bool  $overwrite  Replace existing files? True = yes, false = no.
     */
    public function buildPlugin($overwrite = false)
    {
        assert('is_bool($overwrite); // Invalid argument $overwrite: bool expected');

        $pluginId = strtolower($this->object->getId());

        $pluginDir = $this->getPluginDir();
        $skinDir = new Dir($pluginDir->getPath() . '/templates/');
        $langDir = new Dir($pluginDir->getPath() . '/languages/');
        $enDir = new Dir($langDir->getPath() . '/en/');
        $deDir = new Dir($langDir->getPath() . '/de/');
        if ($overwrite) {
            if ($pluginDir->exists()) {
                $pluginDir->delete(true);
            }
        } elseif ($pluginDir->exists()) {
            $error = new AlreadyExistsWarning();
            $error->setId($pluginId);
            throw $error;
        }
        // create directories
        $pluginDir->create(0777);
        $skinDir->create(0777);
        $langDir->create(0777);
        $enDir->create(0777);
        $deDir->create(0777);

        // add program title
        $this->addTranslation('program_title', $this->object->getTitle());

        /* If you wish to add a default menu for the plug-in, you may do it with this code:
         * $plugin->addMenu($plugin->getId(), $plugin->getTitle());
         */

        // copy files
        assert('!isset($file); // Cannot redeclare var $file');
        foreach ($this->_filesToCopy as $file)
        {
            if (isset($file['src'])) {
                if (!copy($file['src'], $file['dest'])) {
                    $error = new NotWriteableError();
                    $error->setFilename($file['dest']);
                    throw $error;
                }
            } elseif (isset($file['content'])) {
                if (file_put_contents($file['dest'], $file['content']) === false) {
                    $error = new NotWriteableError();
                    $error->setFilename($file['dest']);
                    throw $error;
                }
            }
            chmod($file['dest'], 0777);
        }
        unset($file);

        // copy templates
        assert('!isset($fileName); // Cannot redeclare var $fileName');
        assert('!isset($content); // Cannot redeclare var $content');
        foreach ($this->_templates as $fileName => $content)
        {
            file_put_contents($skinDir->getPath() . '/' . $fileName, $content);
        }
        unset($fileName, $content);

        // create class skeleton
        $phpFile = new TextFile($pluginDir->getPath() . $pluginId . '.plugin.php');
        $phpFile->create();
        $phpFile->setContent($this->getClassSkeleton());
        if (!$phpFile->write()) {
            $error = new NotWriteableError();
            $error->setFilename($phpFile->getPath());
            throw $error;
        }

        // create AJAX-Yana bridge
        $apiFile = new TextFile($skinDir->getPath() . '/api.js');
        $apiFile->create();
        $apiFile->setContent($this->getJsApi());
        if (!$apiFile->write()) {
            $error = new NotWriteableError();
            $error->setFilename($apiFile->getPath());
            throw $error;
        }

        // create XLIFF translation file
        $xliffFile = new TextFile($enDir->getPath() . '/' . $pluginId . '.xlf');
        $xliffFile->create();
        $xliffFile->setContent("<?xml version=\"1.0\"?>\n" . $this->getXliff());
        if (!$xliffFile->write()) {
            $error = new NotWriteableError();
            $error->setFilename($xliffFile->getPath());
            throw $error;
        }
        $xliffFile = new TextFile($deDir->getPath() . '/' . $pluginId . '.xlf');
        $xliffFile->create();
        $xliffFile->setContent("<?xml version=\"1.0\"?>\n" . $this->getXliff("en", "de"));
        if (!$xliffFile->write()) {
            $error = new NotWriteableError();
            $error->setFilename($xliffFile->getPath());
            throw $error;
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
        assert('is_string($id); // Invalid argument $id: String expected');
        assert('is_string($source) || is_null($source); // Invalid argument $source: String expected');
        assert('is_string($target); // Invalid argument $target: string expected');

        if (!empty($source) && strpos($source, YANA_LEFT_DELIMITER) === false) {
            $i = "";
            // seek next free id (add auto-increment number, where necessary)
            while (isset($this->_translations["$id$i"]))
            {
                $i++; // auto-convert to integer
            }
            $this->_translations["$id$i"] = array(
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
        /* @var $apiTemplate SmartTemplate */
        $apiTemplate = $yana->getPlugins()->{'sdk:/templates/jsapi.smarttemplate'};
        $apiTemplate->setVar('plugin', $this->object);
        $apiTemplate->setVar('class', 'Api' .
                str_replace(' ', '', ucwords(preg_replace('/_/', ' ', $this->object->getId()))));
        return (string) $apiTemplate;
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
        $phpTemplate = $yana->getPlugins()->{'sdk:/templates/class.smarttemplate'};
        $phpTemplate->setVar('plugin', $this->object);
        if (isset($this->_schema)) {
            $phpTemplate->setVar('schema', $this->_schema);
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
    private static function _buildActionName($id, $name, $table)
    {
        return lcfirst($id) . ucfirst($name) . ucfirst($table);
    }

    /**
     * Build class object.
     *
     * @access protected
     */
    protected function buildClass()
    {
        foreach ($this->_sdkConfiguration as $key => $value)
        {
            switch ($key)
            {
                case 'name':
                    if (mb_strlen($value) > 15 || !preg_match('/^[\d\w-_ äüöß\(\)]+$/si', $value)) {
                        $error = new InvalidCharacterWarning();
                        $error->setField($key)->setValue($name)->setValid('a-z, 0-9, -, _, ß, ä, ö, ü, " "');
                        throw $error;
                    }
                    $this->object->setDefaultTitle($value);
                    $id = ucwords($value);
                    $id = preg_replace('/[^\d\w_]/', '', $id);
                    $this->object->setId($id);
                    $this->object->setClassName('Plugin_' . $id);
                    break;
                case 'parent':
                    if (!preg_match('/^[\d\w-_]*$/si', $value)) {
                        $error = new InvalidCharacterWarning();
                        $error->setField($key)->setValue($value)->setValid('a-z, 0-9, -, _');
                        throw $error;
                    }
                    $this->object->setParent($value);
                    break;
                case 'package':
                    if (!preg_match('/^[\d\w-_]*$/si', $value)) {
                        $error = new InvalidCharacterWarning();
                        $error->setField($key)->setValue($value)->setValid('a-z, 0-9, -, _');
                        throw $error;
                    }
                    $this->object->setGroup($value);
                    break;
                case 'type':
                    $value = strip_tags(nl2br($value));
                    $this->object->setType($value);
                    break;
                case 'priority':
                    $value = strip_tags(nl2br($value));
                    $this->object->setPriority($value);
                    break;
                case 'author':
                    $value = strip_tags(nl2br($value));
                    $this->object->setAuthors(array($value));
                    break;
                case 'description':
                    $value = str_replace("\n", '<br/>', strip_tags($value));
                    $this->object->setDefaultText($value);
                    break;
                case 'url':
                    $value = strip_tags(nl2br($value));
                    $this->object->setUrl($value);
                    break;
            }
        }
        if (!empty($this->_sdkConfiguration['interface'])) {
            $value = strip_tags($this->_sdkConfiguration['interface']);
            foreach (explode("\n", $value) as $action)
            {
                $this->_methodConfiguration = explode(",", $action);
                $this->buildMethod();
            }
        }
    }

    /**
     * Build method object.
     *
     * @access protected
     */
    protected function buildMethod()
    {
        $action = $this->_methodConfiguration;
        $methodName = array_shift($action);
        if(empty($methodName)) {
            return;
        }
        $method = new PluginConfigurationMethodSdk();
        $method->setMethodName($methodName)
            ->setTitle($methodName)
            ->setType(array_shift($action))
            ->setTemplate(array_shift($action));

        $user = new PluginUserLevel();
        try {
            $user->setGroup(array_shift($action));
        } catch (InvalidArgumentException $e) {
            $error = new InvalidCharacterWarning();
            $error->setField('GROUP')->setValid('a-z, 0-9, -, _')->setValue($group);
            throw $error;
        }
        try {
            $user->setRole(array_shift($action));
        } catch (InvalidArgumentException $e) {
            $error = new InvalidCharacterWarning();
            $error->setField('ROLE')->setValid('a-z, 0-9, -, _')->setValue($role);
            throw $error;
        }
        try {
            $user->setLevel((int) array_shift($action));
        } catch (InvalidArgumentException $e) {
            $error = new InvalidCharacterWarning();
            $error->setField('LEVEL')->setValid('0-100')->setValue($level);
            throw $error;
        }
        $method->addUserLevel($user);

        $group = array_shift($action);
        if (!empty($group)) {
            $menu = new PluginMenuEntry();
            $menu->setGroup($group);
            $method->setMenu($menu);
        }

        $this->object->addMethod($method);
    }

    /**
     * Resets the instance that is currently build.
     *
     * @access  public
     */
    public function createNewConfiguration()
    {
        $this->object = new PluginConfigurationClassSdk();
        $this->_filesToCopy = array();
        $this->_templates = array();
        $this->_translations = array();
    }

    /**
     * Set SDK configuration form values.
     *
     * @access  public
     * @param   array  $sdkConfiguration  user input taken from HTML form
     * @return  PluginConfigurationBuilderSdk
     */
    public function setSdkConfiguration(array $sdkConfiguration)
    {
        $this->_sdkConfiguration = $sdkConfiguration;
        return $this;
    }

}

?>