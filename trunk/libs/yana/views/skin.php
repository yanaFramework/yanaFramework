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
        assert('is_string($skinName)', ' Wrong type for argument 1. String expected');

        $this->_name = "$skinName";
    }

    /**
     * Returns a helper class to load meta information for this package.
     *
     * @return  \Yana\Views\MetaData\IsDataProvider
     */
    protected function _getDataProvider()
    {
        if (!isset($this->_dataProvider)) {
            $this->_dataProvider = new \Yana\Views\MetaData\XmlDataProvider(self::$_baseDirectory);
        }
        return $this->_dataProvider;
    }

    /**
     * Choose a provider to load meta-data.
     *
     * @param   \Yana\Views\MetaData\IsDataProvider  $provider  designated meta-data provider
     * @return  \Yana\Views\Skin
     * @see     \Yana\Views\MetaData\XmlDataProvider
     */
    public function setMetaDataProvider(\Yana\Views\MetaData\IsDataProvider $provider)
    {
        $this->_dataProvider = $provider;
        return $this;
    }

    /**
     * Set base directory from where to read skin files.
     *
     * @param  string  $baseDirectory  base directory
     *
     * @ignore
     */
    public static function setBaseDirectory($baseDirectory)
    {
        assert('is_string($baseDirectory)', ' Wrong argument type argument 1. String expected');
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

            // Load Defaults first
            if ($skinName !== 'default') {
                $this->_configurations['default'] = $this->_loadConfiguration('default');
            }

            // Now load extensions where available
            $this->_configurations[$skinName] = $this->_loadConfiguration($skinName);
        }
        return $this->_configurations;
    }

    /**
     * Read the skin definition file and return it.
     *
     * @throws  \Yana\Core\Exceptions\NotFoundException  when the skin definition file is not found
     * @return  \Yana\Views\MetaData\SkinMetaData
     */
    private function _loadConfiguration($skinName)
    {
        $dataProvider = $this->_getDataProvider();
        return $dataProvider->loadOject($skinName);
    }

    /**
     * Returns the skin's meta information.
     *
     * Use this to get more info on the skin pack's author, title or description.
     *
     * @return  \Yana\Views\MetaData\SkinMetaData
     */
    public function getMetaData()
    {
        $configs = $this->_getConfigurations();
        $skinName = $this->getName();

        return $configs[$skinName];
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
        assert('is_string($templateId)', ' Invalid argument $templateId: string expected');

        $templateId = mb_strtoupper("$templateId");
        $templates = array();

        $configs = $this->_getConfigurations();
        foreach ($configs as $config)
        {
            $templates = $config->getTemplates();
            if (isset($templates[$templateId])) {
                $templateData = $templates[$templateId];
                assert($templateData instanceof \Yana\Views\MetaData\TemplateMetaData);
                $templates[] = $templateData;
            }
        }
        unset($config, $templateData);

        if (empty($templates)) {
            $message = "No template found with id '{$templateId}'.";
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Core\Exceptions\NotFoundException($message, $level);
        }

        // inherit settings from parent templates
        $templateData = \array_shift($templates);
        while (!empty($templates)) {
            $newData = new \Yana\Views\MetaData\TemplateMetaData();
            $moreData = \array_shift($templates);
            /* @var $moreData \Yana\Views\MetaData\TemplateMetaData */
            $newData->setId($templateId)
                ->setFile((!$moreData->getFile()) ? $templateData->getFile() : $moreData->getFile())
                ->setLanguages(\array_merge($templateData->getLanguages(), $moreData->getLanguages()))
                ->setScripts(\array_merge($templateData->getScripts(), $moreData->getScripts()))
                ->setStyles(\array_merge($templateData->getStyles(), $moreData->getStyles()));
            $templateData = $newData;
        }
        return $templateData;
    }

    /**
     * Returns a list of all skins.
     *
     * Returns an associative array with a list of ids and names for all installed skins.
     *
     * @return  array
     * @since   3.1.0
     */
    public function getSkins()
    {
        $skins = array();
        foreach (glob(self::$_baseDirectory . "*" . self::$_fileExtension) as $file)
        {
            $id = basename($file, self::$_fileExtension);
            $configuration = $this->_loadConfiguration($id);
            $title = $id;
            if ($configuration->getTitle() > "") {
                $title = $configuration->getTitle();
            }
            $skins[$id] = $title;
        }
        assert('is_array($skins);');
        return $skins;
    }

    /**
     * Returns the name of the skin.
     *
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
        assert('is_string($skinName)', ' Wrong type for argument 1. String expected');
        return self::$_baseDirectory . "$skinName/";
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

        assert('!isset($template)', ' Cannot redeclare var $template');
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
            $language = \Yana\Translations\Facade::getInstance(); // get instance of language manager
            assert('!isset($value)', 'cannot redeclare variable $value');
            foreach ($template->getLanguages() as $value)
            {
                if (empty($value)) {
                    continue;
                }
                try {

                    $language->readFile($value); // may throw exception

                } catch (\Yana\Core\Exceptions\Translations\TranslationException $e) {
                    $subReport->addWarning("A required language file '{$value}' is not available. " .
                        "Please check if the chosen language file is correct and update your " .
                        "language pack if needed. " . $e->getMessage());
                    unset($e);
                }
            }
            unset($value);

            /*
             * check stylesheet references
             */
            assert('!isset($value)', 'cannot redeclare variable $value');
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
            assert('!isset($value)', 'cannot redeclare variable $value');
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