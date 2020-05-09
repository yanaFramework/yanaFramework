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
declare(strict_types=1);

namespace Yana\Views\Skins;

/**
 * Skin Manager class.
 *
 * @package     yana
 * @subpackage  views
 */
class Skin extends \Yana\Core\StdObject implements \Yana\Views\Skins\IsSkin
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
     * @var  \Yana\Core\MetaData\IsDataProvider
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
    public function __construct(string $skinName)
    {
        $this->_name = $skinName;
    }

    /**
     * Returns a helper class to load meta information for this package.
     *
     * @return  \Yana\Core\MetaData\IsDataProvider
     */
    protected function _getMetaDataProvider(): \Yana\Core\MetaData\IsDataProvider
    {
        if (!isset($this->_dataProvider)) {
            $this->_dataProvider = new \Yana\Views\MetaData\XmlDataProvider(new \Yana\Files\Dir(self::$_baseDirectory));
        }
        return $this->_dataProvider;
    }

    /**
     * Choose a provider to load meta-data.
     *
     * @param   \Yana\Views\MetaData\IsDataProvider  $provider  designated meta-data provider
     * @return  $this
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
    public static function setBaseDirectory(string $baseDirectory)
    {
        assert(is_dir($baseDirectory), 'Not a directory: $baseDirectory');
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
    protected function _getConfigurations(): array
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
     * @param   string  $skinName  
     * @return  \Yana\Views\MetaData\IsSkinMetaData
     */
    private function _loadConfiguration(string $skinName): \Yana\Views\MetaData\IsSkinMetaData
    {
        $dataProvider = $this->_getMetaDataProvider();
        return $dataProvider->loadOject($skinName);
    }

    /**
     * Returns the skin's meta information.
     *
     * Use this to get more info on the skin pack's author, title or description.
     *
     * @return  \Yana\Views\MetaData\IsSkinMetaData
     */
    public function getMetaData(): \Yana\Views\MetaData\IsSkinMetaData
    {
        $configs = $this->_getConfigurations();
        $skinName = $this->getName();

        return $configs[$skinName];
    }

    /**
     * Returns a template definition.
     *
     * @param   string  $templateId  any valid identifier
     * @return  \Yana\Views\MetaData\IsTemplateMetaData
     * @throws  \Yana\Core\Exceptions\NotFoundException  when no matching template was found
     */
    public function getTemplateData(string $templateId): \Yana\Views\MetaData\IsTemplateMetaData
    {
        $templateId = mb_strtoupper($templateId);
        $templatesFound = array();
        /* @var $templatesFound \Yana\Views\MetaData\TemplateMetaData[] */

        assert(!isset($templates), 'Cannot redeclare var $templates');
        foreach ($this->_getConfigurations() as $config)
        {
            $templates = $config->getTemplates();
            /* @var $templates \Yana\Views\MetaData\TemplateMetaData[] */
            if (isset($templates[$templateId])) {
                assert($templates[$templateId] instanceof \Yana\Views\MetaData\TemplateMetaData);
                $templatesFound[] = $templates[$templateId];
            }
        }
        unset($config);

        if (empty($templatesFound)) {
            $message = "No template found with id '{$templateId}'.";
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Core\Exceptions\NotFoundException($message, $level);
        }

        // inherit settings from parent templates
        $templateData = \array_shift($templatesFound);
        /* @var $templateData \Yana\Views\MetaData\TemplateMetaData */
        while (!empty($templatesFound))
        {
            $newData = new \Yana\Views\MetaData\TemplateMetaData();
            $moreData = \array_shift($templatesFound);
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
    public function getSkins(): array
    {
        assert(!isset($skins), 'Cannot redeclare var $skins');
        $skins = array();
        assert(!isset($file), 'Cannot redeclare var $file');
        foreach (glob(self::$_baseDirectory . "/*" . self::$_fileExtension) as $file)
        {
            $id = basename($file, self::$_fileExtension);
            $configuration = $this->_loadConfiguration($id);
            $title = $id;
            if ($configuration->getTitle() > "") {
                $title = $configuration->getTitle();
            }
            $skins[$id] = $title;
        }
        assert(is_array($skins), 'is_array($skins)');
        return $skins;
    }

    /**
     * Returns the name of the skin.
     *
     * The default is 'default'.
     *
     * @return  string
     */
    public function getName(): string
    {
        return $this->_name;
    }

    /**
     * get this skin's directory path
     *
     * @return  string
     */
    public function getDirectory(): string
    {
        return self::getSkinDirectory($this->getName());
    }

    /**
     * get a skin's directory path
     *
     * @param   string  $skinName  identifier for the skin
     * @return  string
     *
     * @ignore
     */
    public static function getSkinDirectory(string $skinName): string
    {
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

        $configurations = $this->_getConfigurations();
        assert(isset($configurations[$skinName]), 'isset($configurations[$skinName])');
        $configuration = $configurations[$this->getName()];
        /* @var $configuration \Yana\Views\MetaData\SkinMetaData */
        assert($configuration instanceof \Yana\Views\MetaData\SkinMetaData);

        return $configuration->getReport($report);
    }

}

?>