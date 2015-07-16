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

namespace Yana\Plugins\Configs;

/**
 * Plugin information
 *
 * This class represents a plugin's meta information.
 * This is it's interface, name and description plus and more.
 *
 * @name        ClassConfiguration
 * @package     yana
 * @subpackage  plugins
 *
 * @ignore
 */
class ClassConfiguration extends \Yana\Core\Object implements \Yana\Core\MetaData\IsPackageMetaData
{

    /**
     * class name
     *
     * @var  string
     */
    private $_className = "";

    /**
     * Path to plugin file.
     *
     * @var  string
     */
    private $_directory = "";

    /**
     * Translated plugin titles.
     *
     * Keys are locales, values are texts.
     *
     * @var  array
     */
    private $_titles = array();

    /**
     * Plugin title in default language.
     *
     * @var  string
     */
    private $_defaultTitle = "";

    /**
     * Translated plugin descriptions.
     *
     * Keys are locales, values are texts.
     *
     * @var  string
     */
    private $_texts = array();

    /**
     * Plugin description in default language.
     *
     * @var  string
     */
    private $_defaultText = "";

    /**
     * Plugin type.
     *
     * @var  string
     */
    private $_type = \Yana\Plugins\TypeEnumeration::DEFAULT_SETTING;

    /**
     * Authors.
     *
     * @var  array
     */
    private $_authors = array();

    /**
     * Priority setting.
     *
     * @var  int
     */
    private $_priority = \Yana\Plugins\PriorityEnumeration::NORMAL;

    /**
     * Plugin application group.
     *
     * @var  string
     */
    private $_group = "";

    /**
     * Name of parent plugin, if inherited.
     *
     * @var  string
     */
    private $_parent = "";

    /**
     * List of plugin names, that this plugin depends on.
     *
     * @var  array
     */
    private $_dependencies = array();

    /**
     * License string.
     *
     * @var  string
     */
    private $_license = "";

    /**
     * URL
     *
     * @var  string
     */
    private $_url = "";

    /**
     * Version information - e.g. a date string.
     *
     * @var  string
     */
    private $_version = "";

    /**
     * Timestamp of when the source file was last modified.
     *
     * @var  int
     */
    private $_lastModified = null;

    /**
     * List of menu definitions.
     *
     * Keys are menu ids and values are descriptions or language tokens.
     *
     * @var  \Yana\Plugins\Menus\IsEntry[]
     */
    private $_menus = array();

    /**
     * Activity setting.
     *
     * @var  int
     */
    private $_active = \Yana\Plugins\ActivityEnumeration::INACTIVE;

    /**
     * the plugin's identifier
     *
     * @var  string
     */
    private $_id = "";

    /**
     * Public methods that this plugin offers.
     *
     * @var  array
     * @ignore
     */
    protected $methods = array();

    /**
     * Set plug-in's id.
     *
     * @param   string  $id  plugin unique identifier
     * @return  \Yana\Plugins\Configs\ClassConfiguration
     */
    public function setId($id)
    {
        assert('is_string($id); // Invalid argument $id: String expected');
        $this->_id = (string) $id;
        return $this;
    }

    /**
     * Get plug-in's id.
     *
     * @return  string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set class name.
     *
     * @param   string  $className  plugin's class name
     * @return  \Yana\Plugins\Configs\ClassConfiguration
     */
    public function setClassName($className)
    {
        assert('is_string($className); // Invalid argument $className: string expected');
        $this->_className = $className;
        return $this;
    }

    /**
     * Set path to source file.
     *
     * @param   string  $directory  absolute path
     * @return  \Yana\Plugins\Configs\ClassConfiguration
     */
    public function setDirectory($directory)
    {
        assert('is_string($directory); // Invalid argument $directory: string expected');
        $this->_directory = $directory;
        return $this;
    }

    /**
     * Set titles.
     *
     * Keys are locales, values are texts.
     *
     * @param   array  $titles  list of titles
     * @return  \Yana\Plugins\Configs\ClassConfiguration
     */
    public function setTitles(array $titles)
    {
        $this->_titles = $titles;
        return $this;
    }

    /**
     * Set default title.
     *
     * @param   string  $defaultTitle  title using default locale.
     * @return  \Yana\Plugins\Configs\ClassConfiguration
     */
    public function setDefaultTitle($defaultTitle)
    {
        assert('is_string($defaultTitle); // Invalid argument $defaultTitle: string expected');
        $this->_defaultTitle = $defaultTitle;
        return $this;
    }

    /**
     * Set translated plugin descriptions.
     *
     * @param   array  $texts  Keys are locales, values are texts.
     * @return  \Yana\Plugins\Configs\ClassConfiguration
     */
    public function setTexts(array $texts)
    {
        $this->_texts = $texts;
        return $this;
    }

    /**
     * Set plugin description in default language.
     *
     * @param   string  $defaultText  some user-defined text
     * @return  \Yana\Plugins\Configs\ClassConfiguration
     */
    public function setDefaultText($defaultText)
    {
        assert('is_string($defaultText); // Invalid argument $defaultText: string expected');
        $this->_defaultText = $defaultText;
        return $this;
    }

    /**
     * Set plugin type.
     *
     * Valid types are: primary, default, config, read, write, security, library.
     *
     * @param   string  $type  valid type identifier
     * @return  \Yana\Plugins\Configs\ClassConfiguration
     */
    public function setType($type)
    {
        assert('is_string($type); // Invalid argument $type: string expected');
        $this->_type = \Yana\Plugins\TypeEnumeration::fromString($type);
        return $this;
    }

    /**
     * Set authors.
     *
     * @param   array  $authors  list of author names and/or e-mails.
     * @return  \Yana\Plugins\Configs\ClassConfiguration
     */
    public function setAuthors(array $authors)
    {
        $this->_authors = $authors;
        return $this;
    }

    /**
     * Set priority level.
     *
     * @param   int  $priority  element of PluginPriorityEnumeration
     * @return  \Yana\Plugins\Configs\ClassConfiguration
     */
    public function setPriority($priority)
    {
        if (is_string($priority)) {
            $priority = \Yana\Plugins\PriorityEnumeration::fromString($priority);
        }
        assert('is_int($priority); // Invalid argument $priority: Integer expected');
        if ($priority < \Yana\Plugins\PriorityEnumeration::LOWEST) {
            $priority = \Yana\Plugins\PriorityEnumeration::LOWEST;
        }
        if ($priority > \Yana\Plugins\PriorityEnumeration::HIGHEST) {
            $priority = \Yana\Plugins\PriorityEnumeration::HIGHEST;
        }
        $this->_priority = $priority;
        return $this;
    }

    /**
     * Set plugin application group.
     *
     * @param   string  $group  unique identifier
     * @return  \Yana\Plugins\Configs\ClassConfiguration
     */
    public function setGroup($group)
    {
        assert('is_string($group); // Invalid argument $group: string expected');
        $this->_group = $group;
        return $this;
    }

    /**
     * Set name of parent plugin, if inherited.
     *
     * @param   string  $parent  class name
     * @return  \Yana\Plugins\Configs\ClassConfiguration
     */
    public function setParent($parent)
    {
        assert('is_string($parent); // Invalid argument $parent: string expected');
        $this->_parent = $parent;
        return $this;
    }

    /**
     * List of plugin names, that this plugin depends on.
     *
     * @param   array  $dependencies  class names
     * @return  \Yana\Plugins\Configs\ClassConfiguration
     */
    public function setDependencies(array $dependencies)
    {
        $this->_dependencies = $dependencies;
        return $this;
    }

    /**
     * Set license string.
     *
     * @param   string  $license  some text
     * @return  \Yana\Plugins\Configs\ClassConfiguration
     */
    public function setLicense($license)
    {
        assert('is_string($license); // Invalid argument $license: string expected');
        $this->_license = $license;
        return $this;
    }

    /**
     * Set URL.
     *
     * @param   string  $url  URL of plugin maker's website
     * @return  \Yana\Plugins\Configs\ClassConfiguration
     */
    public function setUrl($url)
    {
        assert('is_string($url); // Invalid argument $url: string expected');
        $this->_url = $url;
        return $this;
    }

    /**
     * Set version.
     *
     * @param   string  $version  some information - e.g. a date string.
     * @return  \Yana\Plugins\Configs\ClassConfiguration
     */
    public function setVersion($version)
    {
        assert('is_string($version); // Invalid argument $version: string expected');
        $this->_version = $version;
        return $this;
    }

    /**
     * Set timestamp of when the source file was last modified.
     *
     * @param   int  $lastModified
     * @return  \Yana\Plugins\Configs\ClassConfiguration
     */
    public function setLastModified($lastModified)
    {
        assert('is_int($lastModified); // Invalid argument $lastModified: int expected');
        $this->_lastModified = $lastModified;
        return $this;
    }

    /**
     * Add menu definition.
     *
     * @param  \Yana\Plugins\Menus\IsEntry  $menu  Keys are menu ids and values are descriptions or language tokens.
     */
    public function addMenu(\Yana\Plugins\Menus\IsEntry $menu)
    {
        $this->_menus[] = $menu;
        return $this;
    }

    /**
     * List of menu definitions.
     *
     * @param   array  $menus  Keys are menu ids and values are descriptions or language tokens.
     * @return  \Yana\Plugins\Configs\ClassConfiguration
     */
    public function setMenus(array $menus)
    {
        $this->_menus = array();
        foreach ($menus as $menu) {
            $this->addMenu($menu);
        }
        return $this;
    }

    /**
     * Set activity state.
     *
     * @param   int  $active  element of PluginActivityEnumeration
     * @return  \Yana\Plugins\Configs\ClassConfiguration
     */
    public function setActive($active)
    {
        assert('is_int($active); // Invalid argument $active: int expected');
        $this->_active = (int) $active;
        return $this;
    }

    /**
     * Get time when file was last modified.
     *
     * @return  int
     */
    public function getLastModified()
    {
        return $this->_lastModified;
    }

    /**
     * Get title.
     *
     * @param   string  $language  target language (default: auto-detect)
     * @param   string  $country   target country  (default: auto-detect)
     * @return  string
     */
    public function getTitle($language = null, $country = null)
    {
        assert('is_null($language) || is_string($language); // Wrong type for argument 1. String expected');
        assert('is_null($country) || is_string($country); // Wrong type for argument 2. String expected');

        // get defaults
        if (is_null($language)) {
            $languageManager = \Yana\Translations\Facade::getInstance();
            $language = $languageManager->getLanguage();
            $country = $languageManager->getCountry();
        }

        if (isset($this->_titles["$language-$country"])) {
            return $this->_titles["$language-$country"];
        } elseif (isset($this->_titles[$language])) {
            return $this->_titles[$language];
        } else {
            return $this->_defaultTitle;
        }
    }

    /**
     * Get plugin description.
     *
     * @param   string  $language  target language
     * @param   string  $country   target country
     * @return  string
     */
    public function getText($language = "", $country = "")
    {
        assert('is_string($language); // Invalid argument $language: string expected');
        assert('is_string($country); // Invalid argument $country: string expected');

        if (!empty($country) && isset($this->_texts["{$language}-{$country}"])) {
            return $this->_texts["{$language}-{$country}"];
        } elseif (!empty($language) && isset($this->_texts[$language])) {
            return $this->_texts[$language];
        } else {
            return $this->_defaultText;
        }
    }

    /**
     * Get plugin type.
     *
     * @return  string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Get author.
     *
     * Returns a comma-seperated list of all authors.
     * If there is only one author, this name is returned.
     *
     * @return  string
     */
    public function getAuthor()
    {
        return implode(', ', $this->_authors);
    }

    /**
     * Get authors.
     *
     * Returns a list of all authors.
     *
     * @return  array
     */
    public function getAuthors()
    {
        return $this->_authors;
    }

    /**
     * Get priority.
     *
     * Returns the plugin's priority level as an integer.
     * The loweset priority is 0. The higher the value, the higher the priority.
     *
     * @return  string
     */
    public function getPriority()
    {
        /**
         * Check priority settings.
         *
         * Raise priority for library and security plugins.
         * Ensure that plugins of type security always have a higher priority than
         * others. Libraries are loaded AFTER security plugins, but BEFORE any other.
         */
        switch ($this->getType())
        {
            case 'library':
                return $this->_priority + \Yana\Plugins\PriorityEnumeration::HIGHEST;
            case 'security':
                return $this->_priority + (\Yana\Plugins\PriorityEnumeration::HIGHEST * 2);
            default:
                return $this->_priority;
        }
    }

    /**
     * Get group.
     *
     * Returns the plugin's group (if any).
     * This is similar to a "package" in OO-style programming languages.
     *
     * A group may have multiple plugins, but a plugin may only be a member of one group.
     *
     * @return  string
     */
    public function getGroup()
    {
        return $this->_group;
    }

    /**
     * Returns the plugin's parent plugin.
     *
     * This is when a plugin extends another by adding,
     * extending or overwriting methods.
     *
     * This is similar to a "parent class" in most OO-style programming languages.
     * A parent may have multiple child plugins, but a plugin may only have one parent.
     *
     * @return  string
     */
    public function getParent()
    {
        return $this->_parent;
    }

    /**
     * Returns the list of plugins who depend on this.
     *
     * @return  array
     */
    public function getDependencies()
    {
        return $this->_dependencies;
    }

    /**
     * Returns the plugin's version string.
     *
     * This tag is optional.
     * The version may also be derived from
     * the plugin file's modification time.
     *
     * @return  string
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * Returns the plugin's URL (if any).
     *
     * This tag is optional.
     *
     * @return  string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * Returns the plugin's license string.
     *
     * This tag is optional.
     *
     * @return  string
     */
    public function getLicense()
    {
        return $this->_license;
    }

    /**
     * Get menu names.
     *
     * Each plugin may define it's own menues
     * and add entries to them. The names
     * are defined in the file's doc-block,
     * while the menu entries are defined
     * at the methods that are to be added to
     * the menu.
     *
     * Use this function to get all menu titles
     * defined by the plugin.
     *
     * @return  array
     */
    public function getMenuNames()
    {
        return $this->_menus;
    }

    /**
     * Get menu entries.
     *
     * Each plugin may define it's own menues
     * and add entries to them. The names
     * are defined in the file's doc-block,
     * while the menu entries are defined
     * at the methods that are to be added to
     * the menu.
     *
     * Use this function to get all menu entries
     * defined by methods.
     *
     * @param   string  $group  optionally limit entries to a certain group
     * @return  array
     */
    public function getMenuEntries($group = null)
    {
        assert('is_null($group) || is_string($group); // Wrong type for argument 1. String expected');
        $menuEntries = array();
        /* @var $method \Yana\Plugins\Configs\MethodConfiguration */
        foreach ($this->methods as $name => $method)
        {
            $menu = $method->getMenu();
            if (!empty($menu)) {
                if (is_null($group) || $group == $menu->getGroup()) {
                    $menuEntries[$name] = $menu;
                }
            }
        }
        return $menuEntries;
    }

    /**
     * Get directory where file is stored.
     *
     * Returns bool(false) on error.
     *
     * @return  string
     */
    public function getDirectory()
    {
        return $this->_directory;
    }

    /**
     * Get plugin's default active state.
     *
     * A plugin may define it's own prefered initial
     * active state. Default is 'inactive'.
     *
     * @return  int
     */
    public function getActive()
    {
        return $this->_active;
    }

    /**
     * Get URI to a preview image.
     *
     * @return  array
     */
    public function getPreviewImage()
    {
        return $this->getDirectory() . '/preview.png';
    }

    /**
     * Get URI to an icon image.
     *
     * @return  array
     */
    public function getIcon()
    {
        return $this->getDirectory() . '/icon.png';
    }

    /**
     * Get class name.
     *
     * @return  string
     */
    public function getClassName()
    {
        return $this->_className;
    }

    /**
     * Get method configuration.
     *
     * Returns the method configuration if it exists,
     * or NULL if there is none.
     *
     * @param   string  $methodName  name of method
     * @return  \Yana\Plugins\Configs\MethodConfiguration
     */
    public function getMethod($methodName)
    {
        assert('is_string($methodName); // Wrong argument type for argument 1. String expected.');
        if (isset($this->methods[$methodName])) {
            return $this->methods[$methodName];
        } else {
            return null;
        }
    }

    /**
     * Get method configurations.
     *
     * @return  array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Add a method configuration.
     *
     * @param   \Yana\Plugins\Configs\MethodConfiguration  $method  configuration data
     */
    public function addMethod(\Yana\Plugins\Configs\MethodConfiguration $method)
    {
        $this->methods[$method->getMethodName()] = $method;
    }

    /**
     * Unset method configuration.
     *
     * @param   string  $methodName  name of method
     * @ignore
     */
    protected function unsetMethod($methodName)
    {
        if (isset($this->methods[$methodName])) {
            unset($this->methods[$methodName]);
        }
    }

}

?>