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

namespace Yana\Views;

/**
 * <<Singleton>> Skin
 *
 * @package     yana
 * @subpackage  views
 */
class Skin extends \Yana\Core\Object implements \Yana\Report\IsReportable, \Yana\Core\IsPackageMetaData
{

    /**
     * Name of currently selected main skin
     *
     * @var  string
     */
    private static $_selectedSkin = "default";

    /**
     * a list of all skins installed
     *
     * @var  array
     */
    private static $_skins;

    /**
     * file extension for language definition files
     *
     * @var  string
     */
    private static $_fileExtension = ".skin.xml";

    /**
     * @var string
     */
    private $_name = "default";

    /**
     * @var array
     */
    private $_value = array();

    /**
     * @var string
     */
    private $_title = "";

    /**
     * @var string
     */
    private $_author = "";

    /**
     * @var string
     */
    private $_url = "";

    /**
     * @var array
     */
    private $_descriptions = array();

    /**
     * file path cache
     *
     * @var  array
     */
    private static $_filePaths = array();

    /**
     * base directory
     *
     * @var  string
     */
    private static $_baseDirectory = "";

    /**
     * Creates a skin by name.
     *
     * Sets the directory from where to read skin files.
     *
     * @param  string  $skinName  current skin directory
     */
    public function __construct($skinName)
    {
        assert('is_string($skinName); // Wrong type for argument 1. String expected');

        $this->_name = "$skinName";
        $this->_value = array();

        /* Load Defaults first */
        if ($this->_name !== 'default') {
            $this->_loadTemplates('default');
        }

        /* Overwrite Defaults where needed */
        $this->_loadTemplates($this->_name);

        /* select as main skin, if there is no other */
        if (!isset(self::$_selectedSkin)) {
            $this->selectMainSkin();
        }
    }

    /**
     * Returns an instance of the translations container.
     *
     * @return \Yana\Translations\Language
     */
    protected function _getLanguageInstance()
    {
        return \Yana\Translations\Language::getInstance();
    }

    /**
     * Selects the current instance as the main skin for the application.
     */
    public function selectMainSkin()
    {
        self::$_selectedSkin = $this->getName();
    }

    /**
     * is selected main skin
     *
     * Returns bool(true) if the skin is the currently selected main skin and bool(false) otherwise.
     *
     * @return  bool
     */
    public function isSelected()
    {
        return self::$_selectedSkin === $this->getName();
    }

    /**
     * set base directory
     *
     * Set the base directory from where to read skin files.
     *
     * @param  string $baseDirectory  base directory
     *
     * @ignore
     */
    public static function setBaseDirectory($baseDirectory)
    {
        assert('is_string($baseDirectory); // Wrong argument type argument 1. String expected');
        assert('is_dir($baseDirectory);');
        self::$_baseDirectory = $baseDirectory;
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
     * @param   string  $skinName  name of skin definition that should be loaded
     * @throws  \Yana\Core\Exceptions\NotFoundException  when the skin definition file is not found
     * @ignore
     */
    private function _loadTemplates($skinName)
    {
        $file = self::getSkinPath($skinName);
        if (!is_file($file)) {
            throw new \Yana\Core\Exceptions\NotFoundException("Skin definition not found: '$skinName'.");
        }
        // load definition
        $xml = simplexml_load_file($file, null, LIBXML_NOWARNING | LIBXML_NOERROR);
        unset($file);
        // get information
        if (!empty($xml)) {
            $dir = self::$_baseDirectory;
            // head
            if ($xml->head) {
                $this->_title = (string) $xml->head->_title;
                $this->_author = (string) implode(', ', (array) $xml->head->_author);
                $this->_url = (string) $xml->head->_url;
                assert('!isset($description); // Cannot redeclare var $description');
                foreach ($xml->head->description as $description)
                {
                    $this->_descriptions[(string) $description->attributes()->lang] = (string) $description;
                }
                unset($description);
            } // end if
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
                        $message = "The value '{$file}' is not a valid file resource.";
                        trigger_error($message, E_USER_WARNING);
                        unset($file);
                        continue;
                    }
                    $this->_value[$id]['FILE'] = $file;
                    unset($file);
                } // end if
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
                                if (!is_file("{$dir}{$item}")) {
                                    $message = "The value '{$item}' is not a valid file resource.";
                                    trigger_error($message, E_USER_WARNING);
                                    continue;
                                }
                                $item = "$dir$item";
                            }
                        // fall through
                        case 'LANGUAGE':
                            if (!isset($attributes['id'])) {
                                $this->_value[$id][$name][] = (string) $item;
                            } else {
                                $this->_value[$id][$name][(string) $attributes['id']] = (string) $item;
                            }
                        break;
                    } // end switch
                } // end foreach
            } // end foreach
        } // end if
    }

    /**
     * Get filename.
     *
     * This returns the path and name of the template file associated with $key (the template id).
     * An exception is thrown, if it does not exist.
     *
     * Note: This function does not check if the defined file actually does exist.
     *
     * @param   string  $key  template id
     * @return  string
     * @throws  \Yana\Core\Exceptions\NotFoundException  when the given template id does not exist
     */
    public function getFile($key)
    {
        assert('is_string($key); // Wrong type for argument 1. String expected');
        $key = mb_strtoupper("$key");

        // load file
        if (!isset(self::$_filePaths[$key])) {

            // cache the name of the file
            if (!isset($this->_value[$key]['FILE'])) {
                $message = "Missing file attribute for template with id '{$key}'.";
                throw new \Yana\Core\Exceptions\NotFoundException($message);
            }
            self::$_filePaths[$key] = $this->_value[$key]['FILE'];

        }

        return self::$_filePaths[$key];
    }

    /**
     * Set template file.
     *
     * This function set the path to a template file for the given id.
     *
     * @param   string  $key       id of template file
     * @param   string  $filename  path and name of template file
     */
    public function setFile($key, $filename)
    {
        assert('is_string($key); // Wrong argument type argument 1. String expected');
        assert('is_string($filename); // Wrong argument type argument 2. String expected');
        assert('is_file($filename); // Invalid argument 2. File not found');
        $key = mb_strtoupper("$key");
        if (empty($this->_value[$key])) {
            $this->_value[$key] = array();
        }
        $this->_value[$key]['FILE'] = $filename;
    }

    /**
     * Check if input is a valid template id.
     *
     * Returns bool(true) if a template named $key is currently registered
     * and bool(false) otherwise.
     *
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
            return isset($this->_value[$key]['FILE']);
        }
    }

    /**
     * Get language.
     *
     * @param   string  $key    key
     * @return  array
     */
    public function getLanguage($key)
    {
        assert('is_string($key); // Wrong type for argument 1. String expected');
        return $this->_getProperty($key, 'LANGUAGE');
    }

    /**
     * Set a CSS-stylesheet for template $key.
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
     * Set a javascript-file for template $key.
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
     * Set a language-file for template $key.
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
     * Set a property for template $key.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   string  $key            key
     * @param   string  $propertyName   property name
     * @param   string  $file           file
     * @param   string  $name           name
     * @return  bool
     * @since   2.9.8
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the key is empty
     */
    private function _setProperty($key, $propertyName, $file = '', $name = '')
    {
        assert('is_string($key); // Wrong type for argument 1. String expected');
        assert('is_string($propertyName); // Wrong type for argument 2. String expected');
        assert('$propertyName === strtoupper($propertyName); // Argument 2 must be upper-cased');
        assert('is_string($file); // Wrong type for argument 3. String expected');
        assert('is_string($name); // Wrong type for argument 4. String expected');

        if (empty($key)) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Template name may not be empty.");
        } else {
            $key = mb_strtoupper("$key");
        }
        if (!isset($this->_value[$key])) {
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
                if (isset($this->_value[$key][$propertyName])) {
                    unset($this->_value[$key][$propertyName]);
                }
                return true;

            /*
             * 1.2) add file
             */
            } else {
                if (!isset($this->_value[$key][$propertyName])) {
                    $this->_value[$key][$propertyName] = array();
                }
                $this->_value[$key][$propertyName][] = $file;
                return true;

            }

        /*
         * 2) modify particular entry
         */
        } elseif (!isset($this->_value[$key][$propertyName]) || !is_array($this->_value[$key][$propertyName])) {
            return false;

        } else {
            /*
             * 2.1) unset file
             */
            if ($file === "") {
                if (isset($this->_value[$key][$propertyName][$name])) {
                    unset($this->_value[$key][$propertyName][$name]);
                    return true;

                } else {
                    return false;
                }

            /*
             * 2.2) replace file
             */
            } else {
                if (!isset($this->_value[$key][$propertyName])) {
                    $this->_value[$key][$propertyName] = array();
                }
                $this->_value[$key][$propertyName][$name] = $file;
                return true;

            }
        }
    }


    /**
     * Gets a property for a template $key.
     *
     * @param   string  $key            key
     * @param   string  $propertyName   property name
     * @return  array
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the given key is empty
     */
    private function _getProperty($key, $propertyName)
    {
        assert('is_string($key); // Wrong type for argument 1. String expected');
        assert('is_string($propertyName); // Wrong type for argument 2. String expected');
        assert('$propertyName === strtoupper($propertyName); // Argument 2 must be upper-cased');
        if (empty($key)) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Template name may not be empty.");
        } else {
            $key = mb_strtoupper("$key");
        }

        if (!isset($this->_value[$key])) {
            return array();
        }

        if (!isset($this->_value[$key][$propertyName]) || !is_array($this->_value[$key][$propertyName])) {
            return array();
        } else {
            return $this->_value[$key][$propertyName];
        }
    }

    /**
     * Returns the definition of the identified stylesheet.
     *
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
     * @return  array
     * @since   3.1.0
     */
    public static function getSkins()
    {
        if (!isset(self::$_skins)) {
            self::$_skins = array();
            $path = self::$_baseDirectory;
            foreach (glob($path . "*" . self::$_fileExtension) as $file)
            {
                $id = basename($file, self::$_fileExtension);
                $xml = simplexml_load_file($file, null, LIBXML_NOWARNING | LIBXML_NOERROR);
                if (!empty($xml)) {
                    $title = (string) $xml->head->title;
                } else {
                    $title = $id;
                }
                self::$_skins[$id] = $title;
            }
        }
        assert('is_array(self::$_skins);');
        if (is_array(self::$_skins)) {
            return self::$_skins;
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
     * @return  string
     */
    public function getName()
    {
        assert('is_string($this->_name);');
        return $this->_name;
    }

    /**
     * get path of skin's configuration file
     *
     * @return  string
     */
    public function getPath()
    {
        return self::getSkinPath($this->_name);
    }

    /**
     * get this skin's directory path
     *
     * @return  string
     */
    public function getDirectory()
    {
        return self::getSkinDirectory($this->_name);
    }

    /**
     * get a skin's directory path
     *
     * @param   string  $skinName  identifier for the skin
     * @return  string
     *
     * @ignore
     */
    public static function getSkinDirectory($skinName)
    {
        assert('is_string($skinName); // Wrong type for argument 1. String expected');
        return self::$_baseDirectory . "$skinName/";
    }

    /**
     * get name and path of skin's configuration file
     *
     * @param   string  $skinName  identifier for the skin
     * @return  string
     *
     * @ignore
     */
    public static function getSkinPath($skinName)
    {
        assert('is_string($skinName); // Wrong type for argument 1. String expected');
        return self::$_baseDirectory . "$skinName" . self::$_fileExtension;
    }

    /**
     * get description
     *
     * This returns the skin description as a translated string.
     * If no description is given, it returns an empty string.
     *
     * @return  string
     */
    public function getText()
    {
        // get translated description
        $lang = $this->_getLanguageInstance();
        switch (true)
        {
            case isset($this->_descriptions[$lang->getLocale()]):
                return $this->_descriptions[$lang->getLocale()];
            case isset($this->_descriptions[$lang->getLanguage()]):
                return $this->_descriptions[$lang->getLanguage()];
            case isset($this->_descriptions['']):
                return $this->_descriptions[''];
            default:
                return "";
        }
    }

    /**
     * get time when file was last modified
     *
     * @return  int
     */
    public function getLastModified()
    {
        return filemtime($this->getPath());
    }

    /**
     * Returns the skin title.
     *
     * @return  string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Returns the skin URL.
     *
     * The URL is meant to point to a website where the user may find additional
     * information about the auhtor or the skin itself.
     *
     * @return  string
     */
    public function getUrl()
    {
        return $this->_title;
    }

    /**
     * Returns the name of the author(s) as a string.
     *
     * If none are given, it returns an empty string.
     *
     * @return  string
     */
    public function getAuthor()
    {
        return $this->_author;
    }

    /**
     * Returns the path to a preview image.
     *
     * Note that this function does not check, whether the image does exist.
     *
     * @return  string
     */
    public function getPreviewImage()
    {
        return self::$_baseDirectory . $this->_name . "/icon.png";
    }

    /**
     * get a report
     *
     * Returns a \Yana\Report\Xml object, which you may print, transform or output to a file.
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
     * @param   \Yana\Report\IsReport  $report  base report
     * @return  \Yana\Report\IsReport
     * @name    Skin::getReport()
     * @ignore
     */
    public function getReport(\Yana\Report\IsReport $report = null)
    {
        if (is_null($report)) {
            $report = \Yana\Report\Xml::createReport(__CLASS__);
        }
        $report->addText("Skin directory: {$this->_name}");

        if (empty($this->_value)) {
            $report->addWarning("Cannot perform check! No template definitions found.");

        } else {
            /* get instance of language manager */
            $language = $this->_getLanguageInstance();
            /*
             * loop through template definition and create a report for each
             */
            foreach ($this->_value as $key => $element)
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
                } catch (\Yana\Core\Exceptions\NotFoundException $e) {
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
                            try {

                                $language->readFile($value); // may throw exception

                            } catch (\Yana\Core\Exceptions\Translations\TranslationException $e) {
                                $subReport->addWarning("A required language file '{$value}' is not available. " .
                                    "Please check if the chosen language file is correct and update your ".
                                    "language pack if needed. " . $e->getMessage());
                                unset($e);
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
     * Reinitialize instance.
     */
    public function __wakeup()
    {
        if (!isset(self::$_selectedSkin)) {
            self::selectSkin($this);
        }
    }

}

?>