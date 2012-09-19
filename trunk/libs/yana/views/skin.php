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
 * Skin Manager class.
 *
 * @package     yana
 * @subpackage  views
 */
class Skin extends \Yana\Core\Object implements \Yana\Report\IsReportable
{

    /**
     * a list of all skins installed
     *
     * @var  \Yana\Views\MetaData\SkinMetaData[]
     */
    private $_configurations = array();

    /**
     * some data provider
     *
     * @var  \Yana\Views\MetaData\IsDataProvider
     */
    private $_dataProvider = null;

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
        $this->_dataProvider = new \Yana\Views\MetaData\XmlDataProvider(self::$_baseDirectory);
    }

    /**
     * @return  \Yana\Views\MetaData\IsDataProvider
     */
    protected function _getDataProvider()
    {
        return $this->_dataProvider;
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
     * @throws  \Yana\Core\Exceptions\NotFoundException  when the skin definition file is not found
     * @return  \Yana\Views\MetaData\SkinMetaData[]
     */
    protected function _getConfigurations()
    {
        if (empty($this->_configurations)) {

            $skinName = $this->getName();
            $dataProvider = $this->_getDataProvider();

            // Load Defaults first
            if ($skinName !== 'default') {
                $this->_configurations['default'] = $dataProvider->loadOject('default');
            }

            // Now load extensions where available
            $this->_configurations[$skinName] = $dataProvider->loadOject($skinName);
        }
        return $this->_configurations;
    }

    /**
     * Returns a template definition.
     *
     * @param   string  $templateId  any valid identifier
     * @return  \Yana\Views\MetaData\TemplateMetaData
     * @throws  \Yana\Core\Exceptions\NotFoundException  when no matching template was found
     */
    public function getTemplateData($templateId)
    {
        assert('is_string($templateId); // Invalid argument $templateId: string expected');

        $templateId = mb_strtoupper("$templateId");

        $configs = $this->_getConfigurations();
        foreach ($configs as $config)
        {
            $templates = $config->getTemplates();
            if (isset($templates[$templateId])) {
                $templateData = $templates[$templateId];
                assert($templateData instanceof \Yana\Views\MetaData\TemplateMetaData);
                return $templateData;
            }
        }
        $message = "No template found with id '{$templateId}'.";
        $level = \Yana\Log\TypeEnumeration::ERROR;
        throw new \Yana\Core\Exceptions\NotFoundException($message, $level);
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
        return self::_getSkinPath($this->_name);
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
    private static function _getSkinPath($skinName)
    {
        assert('is_string($skinName); // Wrong type for argument 1. String expected');
        return self::$_baseDirectory . "$skinName" . self::$_fileExtension;
    }

    /**
     * Get a report.
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
        $skinName = $this->getName();
        $report->addText("Skin directory: {$skinName}");

        /*
         * loop through template definition and create a report for each
         */
        $configurations = $this->_getConfigurations();
        assert('isset($configurations[$skinName]);');
        $configuration = $configurations[$skinName];
        /* @var $configuration \Yana\Views\MetaData\SkinMetaData */
        assert($configuration instanceof \Yana\Views\MetaData\SkinMetaData);
        unset($configurations);

        assert('!isset($template); // Cannot redeclare var $template');
        foreach ($configuration->getTemplates() as $key => $template)
        {
            /* @var $template \Yana\Views\MetaData\TemplateMetaData */
            $subReport = $report->addReport("$key");
            $hasError = false;

            /*
             * check if template file exists
             */
            $filename = $template->getFile();
            if (!file_exists($filename)) {
                $subReport->addError("File '{$filename}' does not exist. " .
                    "Please make sure this path and filename is correct " .
                    "and you have all files installed. Reinstall if necessary.");
                $hasError = true;
            } else {
                $subReport->addText("File: {$filename}");
            }
            unset($filename);

            /*
             * check language references
             */
            $language = \Yana\Translations\Language::getInstance(); // get instance of language manager
            assert('!isset($value); /* cannot redeclare variable $value */');
            foreach ($template->getLanguages() as $value)
            {
                if (!empty($value)) {
                    try {

                        $language->readFile($value); // may throw exception

                    } catch (\Yana\Core\Exceptions\Translations\TranslationException $e) {
                        $subReport->addWarning("A required language file '{$value}' is not available. " .
                            "Please check if the chosen language file is correct and update your " .
                            "language pack if needed. " . $e->getMessage());
                        unset($e);
                    }
                }
            }
            unset($value);

            /*
             * check stylesheet references
             */
            assert('!isset($value); /* cannot redeclare variable $value */');
            foreach ($template->getStyles() as $value)
            {
                if (!file_exists($value)) {
                    $subReport->addError("A required stylesheet is not available." .
                        "This template may not be displayed correctly.");
                    $hasError = true;
                }
            }
            unset($value);

            /*
             * check script references
             */
            assert('!isset($value); /* cannot redeclare variable $value */');
            foreach ($template->getScripts() as $value)
            {
                if (!file_exists($value)) {
                    $subReport->addError("A required javascript file is not available." .
                        "This template may not be displayed correctly.");
                    $hasError = true;
                }
            }
            unset($value);

            if ($hasError !== true) {
                $subReport->addText("No problems found.");
            }
        } // end foreach
        unset($template);

        return $report;
    }

}

?>