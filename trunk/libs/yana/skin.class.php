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
 *
 * @ignore
 */

/**
 * <<Singleton>> Skin
 *
 * @access      public
 * @package     yana
 * @subpackage  core
 *
 * @ignore
 */
class Skin implements IsReportable, IsSerializable
{
    /**
     * Name of currently selected main skin
     *
     * @access  private
     * @static
     * @var     string
     */
    private static $selectedSkin = "default";

    /**
     * List of existing instances
     *
     * @access  private
     * @static
     * @var     array
     */
    private static $instances = array();

    /**
     * a list of all skins installed
     *
     * @access  private
     * @static
     * @var     array
     */
    private static $skins;

    /**
     * file extension for language definition files
     *
     * @access  private
     * @static
     * @var     string
     */
    private static $fileExtension = ".skin.xml";

    /**#@+
     * @ignore
     * @access  private
     */

    /** @var string */ private $name = "default";
    /** @var array  */ private $value = array();
    /** @var string */ private $title = "";
    /** @var string */ private $author = "";
    /** @var string */ private $url = "";
    /** @var array  */ private $descriptions = array();

    /**#@-*/

    /**
     * file path cache
     *
     * @access  private
     * @static
     * @var array
     * @ignore
     */
    private static $filePaths = array();

    /**
     * base directory
     *
     * @access  private
     * @static
     * @var     string
     */
    private static $baseDirectory = "";

    /**
     * get skin
     *
     * Looks up an returns the instance by the given name.
     * If there is none, it creates a new one.
     *
     * If $skinName is NULL the function will return the currently
     * selected main skin instead.
     *
     * @access  public
     * @static
     * @param   string  $skinName  name of instance to get
     * @return  Skin
     */
    public static function &getInstance($skinName = null)
    {
        if (empty($skinName)) {
            $skinName = self::$selectedSkin;
        }
        if (!isset(self::$instances[$skinName])) {
            self::$instances[$skinName] = new Skin($skinName);
        }
        return self::$instances[$skinName];
    }

    /**
     * Constructor
     *
     * Creates a skin by name.
     * Sets the directory from where to read skin files.
     *
     * @access  private
     * @param   string  $skinName  current skin directory
     */
    private function __construct($skinName)
    {
        assert('is_string($skinName); // Wrong type for argument 1. String expected');

        $this->name = "$skinName";
        $this->value = array();

        /* Load Defaults first */
        if ($this->name !== 'default') {
            $this->_loadTemplates('default');
        }

        /* Overwrite Defaults where needed */
        $this->_loadTemplates($this->name);

        /* select as main skin, if there is no other */
        if (!isset(self::$selectedSkin)) {
            $this->selectMainSkin();
        }
    }

    /**
     * re-initialize main instance
     *
     * @access  public
     * @ignore
     */
    public function __wakeup()
    {
        self::$instances[$this->getName()] = $this;
        if (!isset(self::$selectedSkin)) {
            self::selectSkin($this);
        }
    }

    /**
     * select as main skin
     *
     * Selects the current instance as the main skin for the application.
     *
     * @access  public
     */
    public function selectMainSkin()
    {
        self::$selectedSkin = $this->getName();
    }

    /**
     * is selected main skin
     *
     * Returns bool(true) if the skin is the currently selected main skin
     * and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isSelected()
    {
        return self::$selectedSkin === $this->getName();
    }

    /**
     * set base directory
     *
     * Set the base directory from where to read skin files.
     *
     * @access  public
     * @static
     * @param   Dir     $baseDirectory     base directory
     *
     * @ignore
     */
    public static function setBaseDirectory(Dir $baseDirectory)
    {
        self::$baseDirectory = $baseDirectory->getPath();
    }

    /**
     * load template definitions
     *
     * Read the skin definition file, get all defined templates and store definitions.
     *
     * NOTE: Collisions are treated as follows.
     * Numeric indices will always be appended, no matter what, while textual indices will get
     * replaced.
     *
     * This allows the user to decide wether to create an 'anonymous' association (numbered index)
     * or a named association (textual index) with a stylesheet.
     *
     * So in a derived template the user may decide for himself what element to take and what to
     * drop.
     *
     * @access  private
     * @param   string  $skinName  name of skin definition that should be loaded
     *
     * @ignore
     */
    private function _loadTemplates($skinName)
    {
        $file = self::getSkinPath($skinName);
        if (!is_file($file)) {
            throw new NotFoundException("Skin definition not found: '$skinName'.");
        }
        // load definition
        $xml = simplexml_load_file($file, null, LIBXML_NOWARNING | LIBXML_NOERROR);
        unset($file);
        // get information
        if (!empty($xml)) {
            $dir = self::$baseDirectory;
            // head
            if ($xml->head) {
                $this->title = (string) $xml->head->title;
                $this->author = (string) implode(', ', (array) $xml->head->author);
                $this->url = (string) $xml->head->url;
                assert('!isset($description); // Cannot redeclare var $description');
                foreach ($xml->head->description as $description)
                {
                    $this->descriptions[(string) $description->attributes()->lang] = (string) $description;
                }
                unset($description);
            }
            // body
            foreach ($xml->body->template as $element)
            {
                $attributes = $element->attributes();
                if (empty($attributes['id'])) {
                    continue;
                }
                $id = (string) $attributes['id'];
                if (!empty($attributes['file'])) {
                    assert('!isset($file); // Cannot redeclare $file');
                    $file = $dir . $attributes['file'];
                    if (!is_file("$file")) {
                        $message = "The value '$file' is not a valid file resource.";
                        trigger_error($message, E_USER_WARNING);
                        unset($file);
                        continue;
                    }
                    $this->value[$id]['FILE'] = $file;
                    unset($file);
                }
                unset($attributes);
                foreach ($element->children() as $item)
                {
                    $attributes = $item->attributes();
                    $name = strtoupper($item->getName());
                    switch ($name)
                    {
                        case 'SCRIPT':
                        case 'SKIN':
                        case 'STYLE':
                            if (!empty($item)) {
                                if (!is_file("$dir$item")) {
                                    $message = "The value '{$item}' is not a valid file resource.";
                                    trigger_error($message, E_USER_WARNING);
                                    continue;
                                }
                                $item = "$dir$item";
                            }
                        // fall through
                        case 'LANGUAGE':
                            if (!isset($attributes['id'])) {
                                $this->value[$id][$name][] = "$item";
                            } else {
                                $this->value[$id][$name][(string) $attributes['id']] = "$item";
                            }
                        break;
                    }
                }
            }
        }
    }

    /**
     * get filename
     *
     * This returns the path and name of the template file associated with $key (the template id).
     * An exception is thrown, if it does not exist.
     *
     * Note: This function does not check if the defined file actually does exist.
     *
     * @access  public
     * @param   string  $key  template id
     * @return  string
     * @throws  NotFoundException  when the given template id does not exist
     */
    public function getFile($key)
    {
        assert('is_string($key); // Wrong type for argument 1. String expected');
        $key = mb_strtoupper("$key");

        // load file
        if (!isset(self::$filePaths[$key])) {

            // load language files, stylesheets and scripts
            self::loadDependencies($key); // throws NotFoundException

            // cache the name of the file
            if (!isset($this->value[$key]['FILE'])) {
                throw new NotFoundException("Missing file attribute for template with id '$key'.");
            }
            self::$filePaths[$key] = $this->value[$key]['FILE'];

        }

        return self::$filePaths[$key];
    }

    /**
     * load dependencies for template
     *
     * This function takes the name of a template, looks up any language files,
     * scripts and stylesheets that the template depends on and loads them.
     *
     * @access  public
     * @param   string  $key  template id
     * @throws  NotFoundException when the given template id does not exist
     */
    public function loadDependencies($key)
    {
        assert('is_string($key); // Wrong type for argument 1. String expected');
        $key = mb_strtoupper("$key");

        if (!isset($this->value[$key])) {
            throw new NotFoundException("Template '$key' not found.");
        }

        // load language files associated with the template
        $languageList = $this->getLanguage($key);
        $language = Language::getInstance();
        assert('!isset($languageFile); // Cannot redclare $languageFile');
        foreach ($languageList as $languageFile)
        {
            $language->readFile($languageFile);
        }
        unset($languageFile);

        // prepare a list of css styles associated with the template
        SmartView::addStyles($this->getStyle($key));

        // prepare a list of javascript files associated with the template
        SmartView::addScripts($this->getScript($key));
    }

    /**
     * set template file
     *
     * This function set the path to a template file for the given id.
     *
     * @access  public
     * @param   string  $key       id of template file
     * @param   string  $filename  path and name of template file
     */
    public function setFile($key, $filename)
    {
        assert('is_string($key); // Wrong argument type argument 1. String expected');
        assert('is_string($filename); // Wrong argument type argument 2. String expected');
        assert('is_file($filename); // Invalid argument 2. File not found');
        $key = mb_strtoupper("$key");
        if (empty($this->value[$key])) {
            $this->value[$key] = array();
        }
        $this->value[$key]['FILE'] = $filename;
    }

    /**
     * check if input is a valid template id
     *
     * Returns bool(true) if a template named $key is currently registered
     * and bool(false) otherwise.
     *
     * @access  public
     * @param   string  $key    key
     * @return  bool
     */
    public function isId($key)
    {
        assert('is_string($key); // Wrong type for argument 1. String expected');
        if (empty($key)) {
            /* nothing to do */
            return false;
        } else {
            $key = mb_strtoupper("$key");
            return isset($this->value[$key]['FILE']);
        }
    }

    /**
     * get language
     *
     * @access  public
     * @param   string  $key    key
     * @return  array
     */
    public function getLanguage($key)
    {
        assert('is_string($key); // Wrong type for argument 1. String expected');
        return $this->_getProperty($key, 'LANGUAGE');
    }

    /**
     * set stylesheet
     *
     * This sets a CSS-stylesheet for template $key.
     *
     * Examples:
     * <code>
     * // add stylesheet
     * $skin->setStyle('foo', 'foo.css');
     * // replace stylesheet 'bar'
     * $skin->setStyle('foo', 'bar.css', 'bar');
     * // remove stylesheet 'bar'
     * $skin->setStyle('foo', null, 'bar');
     * // remove all stylesheets
     * $skin->setStyle('foo');
     * </code>
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access  public
     * @param   string  $key    key
     * @param   string  $file   file
     * @param   string  $name   name
     * @return  bool
     * @since   2.9.8
     */
    public function setStyle($key, $file = '', $name = '')
    {
        assert('is_string($key); // Wrong type for argument 1. String expected');
        assert('is_string($file); // Wrong type for argument 2. String expected');
        assert('is_string($name); // Wrong type for argument 3. String expected');
        return $this->_setProperty($key, 'STYLE', "$file", "$name");
    }

    /**
     * set javascript
     *
     * This sets a javascript-file for template $key.
     *
     * Examples:
     * <code>
     * // add script
     * $skin->setScript('foo', 'foo.js');
     * // replace script 'bar'
     * $skin->setScript('foo', 'bar.js', 'bar');
     * // remove script 'bar'
     * $skin->setScript('foo', null, 'bar');
     * // remove all scripts
     * $skin->setScript('foo');
     * </code>
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access  public
     * @param   string  $key    key
     * @param   string  $file   file
     * @param   string  $name   name
     * @return  bool
     * @since   2.9.8
     */
    public function setScript($key, $file = '', $name = '')
    {
        assert('is_string($key); // Wrong type for argument 1. String expected');
        assert('is_string($file); // Wrong type for argument 2. String expected');
        assert('is_string($name); // Wrong type for argument 3. String expected');
        return $this->_setProperty($key, 'SCRIPT', "$file", "$name");
    }

    /**
     * set language
     *
     * This sets a language-file for template $key.
     *
     * Examples:
     * <code>
     * // add language
     * $skin->setLanguage('foo', 'foo.language.xml');
     * // replace language 'bar'
     * $skin->setLanguage('foo', 'bar.language.xml', 'bar');
     * // remove language 'bar'
     * $skin->setLanguage('foo', null, 'bar');
     * // remove all language-files
     * $skin->setLanguage('foo');
     * </code>
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access  public
     * @param   string  $key    key
     * @param   string  $file   file
     * @param   string  $name   name
     * @return  bool
     * @since   2.9.8
     */
    public function setLanguage($key, $file = '', $name = '')
    {
        assert('is_string($key); // Wrong type for argument 1. String expected');
        assert('is_string($file); // Wrong type for argument 2. String expected');
        assert('is_string($name); // Wrong type for argument 3. String expected');
        return $this->_setProperty("$key", 'LANGUAGE', "$file", "$name");
    }

    /**
     * set property
     *
     * This sets a property for template $key.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access  private
     * @param   string  $key            key
     * @param   string  $propertyName   property name
     * @param   string  $file           file
     * @param   string  $name           name
     * @return  bool
     * @since   2.9.8
     */
    private function _setProperty($key, $propertyName, $file = '', $name = '')
    {
        assert('is_string($key); // Wrong type for argument 1. String expected');
        assert('is_string($propertyName); // Wrong type for argument 2. String expected');
        assert('$propertyName === strtoupper($propertyName); // Argument 2 must be upper-cased');
        assert('is_string($file); // Wrong type for argument 3. String expected');
        assert('is_string($name); // Wrong type for argument 4. String expected');

        if (empty($key)) {
            throw new InvalidArgumentException("Template name may not be empty.");
        } else {
            $key = mb_strtoupper("$key");
        }
        if (!isset($this->value[$key])) {
            return false;
        }

        /*
         * 1) modify whole list
         */
        if ($name === "") {

            /*
             * 1.1) unset list
             */
            if ($file === "") {
                if (isset($this->value[$key][$propertyName])) {
                    unset($this->value[$key][$propertyName]);
                }
                return true;

            /*
             * 1.2) add file
             */
            } else {
                if (!isset($this->value[$key][$propertyName])) {
                    $this->value[$key][$propertyName] = array();
                }
                $this->value[$key][$propertyName][] = $file;
                return true;

            }

        /*
         * 2) modify particular entry
         */
        } elseif (!isset($this->value[$key][$propertyName]) || !is_array($this->value[$key][$propertyName])) {
            return false;

        } else {
            /*
             * 2.1) unset file
             */
            if ($file === "") {
                if (isset($this->value[$key][$propertyName][$name])) {
                    unset($this->value[$key][$propertyName][$name]);
                    return true;

                } else {
                    return false;
                }

            /*
             * 2.2) replace file
             */
            } else {
                if (!isset($this->value[$key][$propertyName])) {
                    $this->value[$key][$propertyName] = array();
                }
                $this->value[$key][$propertyName][$name] = $file;
                return true;

            }
        }
    }


    /**
     * get property
     *
     * This gets a property for a template $key.
     *
     * @access  private
     * @param   string  $key            key
     * @param   string  $propertyName   property name
     * @return  array
     */
    private function _getProperty($key, $propertyName)
    {
        assert('is_string($key); // Wrong type for argument 1. String expected');
        assert('is_string($propertyName); // Wrong type for argument 2. String expected');
        assert('$propertyName === strtoupper($propertyName); // Argument 2 must be upper-cased');
        if (empty($key)) {
            throw new InvalidArgumentException("Template name may not be empty.");
        } else {
            $key = mb_strtoupper("$key");
        }

        if (!isset($this->value[$key])) {
            return array();
        }

        if (!isset($this->value[$key][$propertyName]) || !is_array($this->value[$key][$propertyName])) {
            return array();
        } else {
            return $this->value[$key][$propertyName];
        }
    }

    /**
     * get style
     *
     * Returns the definition of the identified stylesheet.
     *
     * @access  public
     * @param   string  $key    key
     * @return  array
     */
    public function getStyle($key)
    {
        assert('is_string($key); // Wrong type for argument 1. String expected');
        return $this->_getProperty($key, 'STYLE');
    }

    /**
     * get script
     *
     * @access  public
     * @param   string  $key    key
     * @return  array
     */
    public function getScript($key)
    {
        assert('is_string($key); // Wrong type for argument 1. String expected');
        return $this->_getProperty($key, 'SCRIPT');
    }

    /**
     * returns a list of all skins
     *
     * Returns an associative array with a list of ids and names for all installed skins.
     *
     * @access  public
     * @static
     * @return  array
     * @since   3.1.0
     */
    public static function getSkins()
    {
        if (!isset(self::$skins)) {
            self::$skins = array();
            $path = self::$baseDirectory;
            foreach (glob($path . "*" . self::$fileExtension) as $file)
            {
                $id = basename($file, self::$fileExtension);
                $xml = simplexml_load_file($file, null, LIBXML_NOWARNING | LIBXML_NOERROR);
                if (!empty($xml)) {
                    $title = (string) $xml->head->title;
                } else {
                    $title = $id;
                }
                self::$skins[$id] = $title;
            }
        }
        assert('is_array(self::$skins);');
        if (is_array(self::$skins)) {
            return self::$skins;
        } else {
            return array();
        }
    }

    /**
     * get name of skin
     *
     * Returns the name of the skin as a string.
     * The default is 'default'.
     *
     * @access  public
     * @return  string
     */
    public function getName()
    {
        assert('is_string($this->name);');
        return $this->name;
    }

    /**
     * get path of skin's configuration file
     *
     * @access  public
     * @return  string
     */
    public function getPath()
    {
        return self::getSkinPath($this->name);
    }

    /**
     * get this skin's directory path
     *
     * @access  public
     * @return  string
     */
    public function getDirectory()
    {
        return self::getSkinDirectory($this->name);
    }

    /**
     * get a skin's directory path
     *
     * @access  public
     * @static
     * @param   string  $skinName  identifier for the skin
     * @return  string
     *
     * @ignore
     */
    public static function getSkinDirectory($skinName)
    {
        assert('is_string($skinName); // Wrong type for argument 1. String expected');
        return self::$baseDirectory . "$skinName/";
    }

    /**
     * get name and path of skin's configuration file
     *
     * @access  public
     * @static
     * @param   string  $skinName  identifier for the skin
     * @return  string
     *
     * @ignore
     */
    public static function getSkinPath($skinName)
    {
        assert('is_string($skinName); // Wrong type for argument 1. String expected');
        return self::$baseDirectory . "$skinName" . self::$fileExtension;
    }

    /**
     * get description
     *
     * This returns the skin description as a translated string.
     * If no description is given, it returns an empty string.
     *
     * @access  public
     * @return  string
     */
    public function getText()
    {
        // get translated description
        $lang = Language::getInstance();
        switch (true)
        {
            case isset($this->descriptions[$lang->getLocale()]):
                return $this->descriptions[$lang->getLocale()];
            break;
            case isset($this->descriptions[$lang->getLanguage()]):
                return $this->descriptions[$lang->getLanguage()];
            break;
            case isset($this->descriptions['']):
                return $this->descriptions[''];
            break;
            default:
                return "";
            break;
        }
    }

    /**
     * get time when file was last modified
     *
     * @access  public
     * @return  int
     */
    public function getLastModified()
    {
        return filemtime($this->getPath());
    }

    /**
     * get title
     *
     * This returns the skin title.
     *
     * @access  public
     * @return  string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * get URL
     *
     * This returns the skin URL.
     * The URL is meant to point to a website where the user may find additional
     * information about the auhtor or the skin itself.
     *
     * @access  public
     * @return  string
     */
    public function getUrl()
    {
        return $this->title;
    }

    /**
     * get author
     *
     * This returns the name of the author(s) as a string.
     * If none are given, it returns an empty string.
     *
     * @access  public
     * @return  string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * get path to preview image
     *
     * This returns the path to a preview image.
     * Note that this function does not check, whether the image does exist.
     *
     * @access  public
     * @return  string
     */
    public function getPreviewImage()
    {
        return self::$baseDirectory . $this->name . "/icon.png";
    }

    /**
     * get a report
     *
     * Returns a ReportXML object, which you may print, transform or output to a file.
     * It informs you about configuration issues or errors.
     *
     * Example:
     * <code>
     * <?xml version="1.0"?>
     * <report>
     *   <text>Skin directory: skins/foo/</text>
     *   <report>
     *     <title>index</title>
     *     <error>File 'index.html' does not exist.</error>
     *   </report>
     *   <report>
     *     <title>foo</title>
     *     <text>Path: foo.html</text>
     *     <text>language: bar</text>
     *   </report>
     * </report>
     * </code>
     *
     * @access  public
     * @param   ReportXML  $report  base report
     * @return  ReportXML
     * @name    Skin::getReport()
     * @ignore
     */
    public function getReport(ReportXML $report = null)
    {
        if (is_null($report)) {
            $report = ReportXML::createReport(__CLASS__);
        }
        $report->addText("Skin directory: {$this->name}");

        if (empty($this->value)) {
            $report->addWarning("Cannot perform check! No template definitions found.");

        } else {
            /* get instance of language manager */
            $language = Language::getInstance();
            /*
             * loop through template definition and create a report for each
             */
            foreach ($this->value as $key => $element)
            {
                $subReport = $report->addReport("$key");
                $hasError = false;
                
                /*
                 * check if template file exists
                 */
                try {
                    $filename = $this->getFile($key);
                    if (!file_exists($filename)) {
                        $subReport->addError("File '$filename' does not exist. " .
                            "Please make sure this path and filename is correct " .
                            "and you have all files installed. Reinstall if necessary.");
                        $hasError = true;
                    } else {
                        $subReport->addText("File: $filename");
                    }
                } catch (NotFoundException $e) {
                    $subReport->addWarning($e->getMessage());
                }
                
                /*
                 * check language references
                 */
                if (!empty($element['LANGUAGE']) && is_array($element['LANGUAGE']) && count($element['LANGUAGE']) > 0) {
                    assert('!isset($value); /* cannot redeclare variable $value */');
                    foreach ($element['LANGUAGE'] as $value)
                    {
                        if (!empty($value)) {
                            if (!$language->readFile($value)) {
                                $subReport->addWarning("A required language file '{$value}' is not available. " .
                                    "Please check if the chosen language file is correct and update your ".
                                    "language pack if needed.");
                            }
                        }
                    }
                    unset($value);
                }
                
                /*
                 * check stylesheet references
                 */
                if (!empty($element['STYLE']) && is_array($element['STYLE']) && count($element['STYLE']) > 0) {
                    assert('!isset($value); /* cannot redeclare variable $value */');
                    foreach ($element['STYLE'] as $value)
                    {
                        if (!file_exists($value)) {
                            $subReport->addError("A required stylesheet is not available." .
                            "This template may not be displayed correctly.");
                            $hasError = true;
                        }
                    }
                    unset($value);
                }
                
                /*
                 * check script references
                 */
                if (!empty($element['SCRIPT']) && is_array($element['SCRIPT']) && count($element['SCRIPT']) > 0) {
                    assert('!isset($value); /* cannot redeclare variable $value */');
                    foreach ($element['SCRIPT'] as $value)
                    {
                        if (!file_exists($value)) {
                            $subReport->addError("A required javascript file is not available." .
                            "This template may not be displayed correctly.");
                            $hasError = true;
                        }
                    }
                    unset($value);
                }
                
                if ($hasError !== true) {
                    $subReport->addText("No problems found.");
                }
            } // end foreach
        } // end if

        return $report;
    }

    /**
     * serialize this object to a string
     *
     * Returns the serialized object as a string.
     *
     * @access  public
     * @return  string
     */
    public function serialize()
    {
        return serialize($this);
    }

    /**
     * unserialize a string to a serializable object
     *
     * Returns the unserialized object.
     *
     * @access  public
     * @static
     * @param   string  $string  string to unserialize
     * @return  IsSerializable
     */
    public static function unserialize($string)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        if (!isset(self::$selectedSkin)) {
            self::$selectedSkin = unserialize($string);
            return self::$selectedSkin;
        } else {
            return unserialize($string);
        }
    }
}

?>