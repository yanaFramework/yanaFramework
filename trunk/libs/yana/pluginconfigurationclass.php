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
 * Plugin information
 *
 * This class represents a plugin's meta information.
 * This is it's interface, name and description plus and more.
 *
 * @access      public
 * @name        PluginConfiguration
 * @package     yana
 * @subpackage  core
 *
 * @ignore
 */
class PluginConfigurationClass extends \Yana\Core\Object
{

    /**
     * class name
     *
     * @access  private
     * @var     string
     */
    private $_className = "";

    /**
     * Path to plugin file.
     *
     * @access  private
     * @var     string
     */
    private $_directory = "";

    /**
     * Translated plugin titles.
     *
     * Keys are locales, values are texts.
     *
     * @access  private
     * @var     array
     */
    private $_titles = array();

    /**
     * Plugin title in default language.
     *
     * @access  private
     * @var     string
     */
    private $_defaultTitle = "";

    /**
     * Translated plugin descriptions.
     *
     * Keys are locales, values are texts.
     *
     * @access  private
     * @var     string
     */
    private $_texts = array();

    /**
     * Plugin description in default language.
     *
     * @access  private
     * @var     string
     */
    private $_defaultText = "";

    /**
     * Plugin type.
     *
     * @access  private
     * @var     string
     */
    private $_type = PluginTypeEnumeration::DEFAULT_SETTING;

    /**
     * Authors.
     *
     * @access  private
     * @var     array
     */
    private $_authors = array();

    /**
     * Priority setting.
     *
     * @access  private
     * @var     int
     */
    private $_priority = PluginPriorityEnumeration::NORMAL;

    /**
     * Plugin application group.
     *
     * @access  private
     * @var     string
     */
    private $_group = "";

    /**
     * Name of parent plugin, if inherited.
     *
     * @access  private
     * @var     string
     */
    private $_parent = "";

    /**
     * List of plugin names, that this plugin depends on.
     *
     * @access  private
     * @var     array
     */
    private $_dependencies = array();

    /**
     * License string.
     *
     * @access  private
     * @var     string
     */
    private $_license = "";

    /**
     * URL of plugin maker's website.
     *
     * @access  private
     * @var     string
     */
    private $_url = "";

    /**
     * Version information - e.g. a date string.
     *
     * @access  private
     * @var     string
     */
    private $_version = "";

    /**
     * Timestamp of when the source file was last modified.
     *
     * @access  private
     * @var     int
     */
    private $_lastModified = null;

    /**
     * List of menu definitions.
     *
     * Keys are menu ids and values are descriptions or language tokens.
     *
     * @access  private
     * @var     array
     */
    private $_menus = array();

    /**
     * Activity setting.
     *
     * @access  private
     * @var     int
     */
    private $_active = PluginActivityEnumeration::INACTIVE;

    /**
     * the plugin's identifier
     *
     * @access  private
     * @var     string
     */
    private $_id = "";

    /**
     * Public methods that this plugin offers.
     *
     * @access  protected
     * @var     array
     * @ignore
     */
    protected $methods = array();

    /**
     * Set plug-in's id.
     *
     * @access  public
     * @param   string  $id  plugin unique identifier
     * @return  PluginConfigurationClassSdk
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
     * @access  public
     * @return  string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set class name.
     *
     * @access  public
     * @param   string  $className  plugin's class name
     * @return  PluginConfigurationClass
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
     * @access  public
     * @param   string  $directory  absolute path
     * @return  PluginConfigurationClass
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
     * @access  public
     * @param   array  $titles  list of titles
     * @return  PluginConfigurationClass
     */
    public function setTitles(array $titles)
    {
        $this->_titles = $titles;
        return $this;
    }

    /**
     * Set default title.
     *
     * @access  public
     * @param   string  $defaultTitle  title using default locale.
     * @return  PluginConfigurationClass
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
     * @access  public
     * @param   array  $texts  Keys are locales, values are texts.
     * @return  PluginConfigurationClass
     */
    public function setTexts(array $texts)
    {
        $this->_texts = $texts;
        return $this;
    }

    /**
     * Set plugin description in default language.
     *
     * @access  public
     * @param   string  $defaultText  some user-defined text
     * @return  PluginConfigurationClass
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
     * @access  public
     * @param   string  $type  valid type identifier
     * @return  PluginConfigurationClass
     */
    public function setType($type)
    {
        assert('is_string($type); // Invalid argument $type: string expected');
        $this->_type = PluginTypeEnumeration::fromString($type);
        return $this;
    }

    /**
     * Set authors.
     *
     * @access  public
     * @param   array  $authors  list of author names and/or e-mails.
     * @return  PluginConfigurationClass
     */
    public function setAuthors(array $authors)
    {
        $this->_authors = $authors;
        return $this;
    }

    /**
     * Set priority level.
     *
     * @access  public
     * @param   int  $priority  element of PluginPriorityEnumeration
     * @return  PluginConfigurationClass
     */
    public function setPriority($priority)
    {
        if (is_string($priority)) {
            $priority = PluginPriorityEnumeration::fromString($priority);
        }
        assert('is_int($priority); // Invalid argument $priority: Integer expected');
        if ($priority < PluginPriorityEnumeration::LOWEST) {
            $priority = PluginPriorityEnumeration::LOWEST;
        }
        if ($priority > PluginPriorityEnumeration::HIGHEST) {
            $priority = PluginPriorityEnumeration::HIGHEST;
        }
        $this->_priority = $priority;
        return $this;
    }

    /**
     * Set plugin application group.
     *
     * @access  public
     * @param   string  $group  unique identifier
     * @return  PluginConfigurationClass
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
     * @access  public
     * @param   string  $parent  class name
     * @return  PluginConfigurationClass
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
     * @access  public
     * @param   array  $dependencies  class names
     * @return  PluginConfigurationClass
     */
    public function setDependencies(array $dependencies)
    {
        $this->_dependencies = $dependencies;
        return $this;
    }

    /**
     * Set license string.
     *
     * @access  public
     * @param   string  $license  some text
     * @return  PluginConfigurationClass
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
     * @access  public
     * @param   string  $url  URL of plugin maker's website
     * @return  PluginConfigurationClass
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
     * @access  public
     * @param   string  $version  some information - e.g. a date string.
     * @return  PluginConfigurationClass
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
     * @access  public
     * @param   int  $lastModified
     * @return  PluginConfigurationClass
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
     * @access public
     * @param  PluginMenuEntry  $menu  Keys are menu ids and values are descriptions or language tokens.
     */
    public function addMenu(PluginMenuEntry $menu)
    {
        $this->_menus[] = $menu;
        return $this;
    }

    /**
     * List of menu definitions.
     *
     * @access  public
     * @param   array  $menus  Keys are menu ids and values are descriptions or language tokens.
     * @return  PluginConfigurationClass
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
     * @access  public
     * @param   int  $active  element of PluginActivityEnumeration
     * @return  PluginConfigurationClass
     */
    public function setActive($active)
    {
        assert('is_int($active); // Invalid argument $active: int expected');
        $this->_active = (int) $active;
        return $this;
    }

    /**
     * get time when file was last modified
     *
     * @access  public
     * @return  int
     */
    public function getLastModified()
    {
        return $this->_lastModified;
    }

    /**
     * get title
     *
     * @access  public
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
            $languageManager = Language::getInstance();
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
     * Get plugin description
     *
     * @access  public
     * @param   string  $language  target language (default: auto-detect)
     * @param   string  $country   target country  (default: auto-detect)
     * @return  string
     */
    public function getText($language = null, $country = null)
    {
        assert('is_null($language) || is_string($language); // Wrong type for argument 1. String expected');
        assert('is_null($country) || is_string($country); // Wrong type for argument 2. String expected');

        // get defaults
        if (is_null($language) && class_exists('Language')) {
            $languageManager = Language::getInstance();
            $language = $languageManager->getLanguage();
            $country = $languageManager->getCountry();
        }

        if (isset($this->_texts["$language-$country"])) {
            return $this->_texts["$language-$country"];
        } elseif (isset($this->_texts[$language])) {
            return $this->_texts[$language];
        } else {
            return $this->_defaultText;
        }
    }

    /**
     * get type
     *
     * @access  public
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
     * @access  public
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
     * @access  public
     * @return  array
     */
    public function getAuthors()
    {
        return $this->_authors;
    }

    /**
     * get priority
     *
     * Returns the plugin's priority level as an integer.
     * The loweset priority is 0. The higher the value, the higher the priority.
     *
     * @access  public
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
                return $this->_priority + PluginPriorityEnumeration::HIGHEST;
            case 'security':
                return $this->_priority + (PluginPriorityEnumeration::HIGHEST * 2);
            default:
                return $this->_priority;
        }
    }

    /**
     * get group
     *
     * Returns the plugin's group (if any).
     * This is similar to a "package" in OO-style programming languages.
     *
     * A group may have multiple plugins, but a plugin may only be a member of one group.
     *
     * @access  public
     * @return  string
     */
    public function getGroup()
    {
        return $this->_group;
    }

    /**
     * get parent
     *
     * Returns the plugin's parent plugin.
     * This is when a plugin extends another by adding,
     * extending or overwriting methods.
     *
     * This is similar to a "parent class" in most OO-style programming languages.
     * A parent may have multiple child plugins, but a plugin may only have one parent.
     *
     * @access  public
     * @return  string
     */
    public function getParent()
    {
        return $this->_parent;
    }

    /**
     * get dependencies
     *
     * Returns the list of plugins who depend on this.
     *
     * @access  public
     * @return  array
     */
    public function getDependencies()
    {
        return $this->_dependencies;
    }

    /**
     * get version
     *
     * Returns the plugin's version string.
     *
     * This tag is optional.
     * The version may also be derived from
     * the plugin file's modification time.
     *
     * @access  public
     * @return  string
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * get URL
     *
     * Returns the plugin's URL (if any).
     *
     * This tag is optional.
     *
     * @access  public
     * @return  string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * Get license.
     *
     * Returns the plugin's license string.
     *
     * This tag is optional.
     *
     * @access  public
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
     * @access  public
     * @return  array
     */
    public function getMenuNames()
    {
        return $this->_menus;
    }

    /**
     * get menu entries
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
     * @access  public
     * @param   string  $group  optionally limit entries to a certain group
     * @return  array
     */
    public function getMenuEntries($group = null)
    {
        assert('is_null($group) || is_string($group); // Wrong type for argument 1. String expected');
        $menuEntries = array();
        /* @var $method PluginConfigurationMethod */
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
     * get directory where file is stored
     *
     * Returns bool(false) on error.
     *
     * @access  public
     * @return  string
     */
    public function getDirectory()
    {
        return $this->_directory;
    }

    /**
     * get plugin's default active state
     *
     * A plugin may define it's own prefered initial
     * active state. Default is 'inactive'.
     *
     * @access  public
     * @return  int
     */
    public function getActive()
    {
        return $this->_active;
    }

    /**
     * get URI to a preview image
     *
     * @access  public
     * @return  array
     */
    public function getPreviewImage()
    {
        return $this->getDirectory() . '/preview.png';
    }

    /**
     * get URI to an icon image
     *
     * @access  public
     * @return  array
     */
    public function getIcon()
    {
        return $this->getDirectory() . '/icon.png';
    }

    /**
     * get class name
     *
     * @access  public
     * @return  string
     */
    public function getClassName()
    {
        return $this->_className;
    }

    /**
     * get method configuration
     *
     * Returns the method configuration if it exists,
     * or NULL if there is none.
     *
     * @access  public
     * @param   string  $methodName  name of method
     * @return  PluginConfigurationMethod
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
     * get method configurations
     *
     * @access  public
     * @return  array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Add a method configuration.
     *
     * @access  public
     * @param   PluginConfigurationMethod  $method  configuration data
     */
    public function addMethod(PluginConfigurationMethod $method)
    {
        $this->methods[$method->getMethodName()] = $method;
    }

    /**
     * unset method
     *
     * @access  protected
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