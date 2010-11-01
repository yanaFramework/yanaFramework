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
class PluginConfiguration extends Object implements IsSerializable
{
    /**#@+
     * class constant
     *
     * @ignore
     */

    const DIR = 'dir';
    const DEFAULT_TITLE = 'defaultTitle';
    const DEFAULT_TEXT = 'defaultText';
    const MODIFIED = 'modified';

    /**#@-*/
    /**#@+
     * class var
     *
     * @ignore
     * @access  protected
     * @ignore
     */

    /** @var string */ protected $className = "";
    /** @var array  */ protected $configuration = array();
    /** @var array  */ protected $methods = array();

    /**#@-*/

    /**
     * Constructor
     *
     * @access  public
     * @param   PluginReflectionClass  $pluginClass plugin configuration class
     */
    public function __construct(PluginReflectionClass $pluginClass)
    {
        $this->className = $pluginClass->getClassName();

        $title = array();
        $text = array();
        $this->_getTranslation($pluginClass, $title, $text);

        $this->configuration = array
        (
            self::DEFAULT_TITLE => $pluginClass->getTitle(),
            self::DEFAULT_TEXT => $pluginClass->getText(),
            PluginAnnotation::TITLE => $title,
            PluginAnnotation::TEXT => $text,
            self::DIR => $pluginClass->getDirectory(),
            PluginAnnotation::TYPE => mb_strtolower($pluginClass->getTag(PluginAnnotation::TYPE, 'default')),
            PluginAnnotation::AUTHOR => $pluginClass->getTags(PluginAnnotation::AUTHOR),
            PluginAnnotation::PRIORITY => PluginPriority::getPriority($pluginClass->getTag(PluginAnnotation::PRIORITY)),
            PluginAnnotation::GROUP => mb_strtolower($pluginClass->getTag(PluginAnnotation::GROUP)),
            PluginAnnotation::PARENT => $pluginClass->getTag(PluginAnnotation::PARENT),
            PluginAnnotation::REQUIRES => $pluginClass->getTags(PluginAnnotation::REQUIRES),
            PluginAnnotation::LICENSE => $pluginClass->getTag(PluginAnnotation::LICENSE),
            PluginAnnotation::URL => $pluginClass->getTag(PluginAnnotation::URL),
            PluginAnnotation::VERSION => $pluginClass->getTag(PluginAnnotation::VERSION),
            PluginAnnotation::CATEGORY => $pluginClass->getTag(PluginAnnotation::CATEGORY),
            PluginAnnotation::PACKAGE => $pluginClass->getTag(PluginAnnotation::PACKAGE, 'yana'),
            PluginAnnotation::SUBPACKAGE => $pluginClass->getTag(PluginAnnotation::SUBPACKAGE, 'plugins'),
            self::MODIFIED => $pluginClass->getLastModified(),
            PluginAnnotation::MENU => $pluginClass->getTags(PluginAnnotation::MENU),
            PluginAnnotation::ACTIVE => $pluginClass->getTag(PluginAnnotation::ACTIVE, '0')
        );

        // check priority settings
        $this->_checkPriority();

        // store methods
        foreach ($pluginClass->getMethods(ReflectionProperty::IS_PUBLIC) as $method)
        {
            $name = $method->getName();
            if (!$method->getTag(PluginAnnotation::IGNORE)) {
                $this->methods[$name] = new PluginConfigurationMethod($method, $pluginClass);
            }
        } /* end foreach */
    }

    /**
     * check priority settings
     *
     * Raise priority for library and security plugins.
     * Ensure that plugins of type security always have a higher priority than
     * others. Libraries are loaded AFTER security plugins, but BEFORE any other.
     *
     * @access  private
     */
    private function _checkPriority()
    {
        $MIN_PRIORITY_LIBRARY = PluginPriority::HIGHEST + 1;
        $MAX_PRIORITY_LIBRARY = PluginPriority::HIGHEST * 2;
        $MIN_PRIORITY_SECURITY = $MAX_PRIORITY_LIBRARY + 1;
        $MAX_PRIORITY_SECURITY = PluginPriority::HIGHEST * 3;
        $MIN_PRIORITY = PluginPriority::LOWEST;
        $MAX_PRIORITY = PluginPriority::HIGHEST;

        // check priority settings
        switch ($this->configuration[PluginAnnotation::TYPE])
        {
            case 'security':
                if ($this->configuration[PluginAnnotation::PRIORITY] < $MIN_PRIORITY_SECURITY) {
                    $this->configuration[PluginAnnotation::PRIORITY] += $MIN_PRIORITY_SECURITY;
                }
                if ($this->configuration[PluginAnnotation::PRIORITY] > $MAX_PRIORITY_SECURITY) {
                    $this->configuration[PluginAnnotation::PRIORITY] = $MAX_PRIORITY_SECURITY;
                }
            break;
            case 'library':
                if ($this->configuration[PluginAnnotation::PRIORITY] < $MIN_PRIORITY_LIBRARY) {
                    $this->configuration[PluginAnnotation::PRIORITY] += $MIN_PRIORITY_LIBRARY;
                }
                if ($this->configuration[PluginAnnotation::PRIORITY] > $MAX_PRIORITY_LIBRARY) {
                    $this->configuration[PluginAnnotation::PRIORITY] = $MAX_PRIORITY_LIBRARY;
                }
            break;
            default:
                if ($this->configuration[PluginAnnotation::PRIORITY] < $MIN_PRIORITY) {
                    $this->configuration[PluginAnnotation::PRIORITY] = $MIN_PRIORITY;
                }
                if ($this->configuration[PluginAnnotation::PRIORITY] > $MAX_PRIORITY) {
                    $this->configuration[PluginAnnotation::PRIORITY] = $MAX_PRIORITY;
                }
            break;
        }
    }

    /**
     * get title and text from translation tag
     *
     * @access  private
     * @param   PluginReflectionClass  $pluginClass      plugin configuration class
     * @param   string                 &$title           output title var
     * @param   string                 &$text            output text var
     */
    private function _getTranslation(PluginReflectionClass $pluginClass, array &$title, array &$text)
    {
        $translation = $pluginClass->getTag(PluginAnnotation::TRANSLATION);
        $title = array();
        $text = array();

        if (!empty($translation)) {
            foreach($translation as $locale => $docBlock)
            {
                if (preg_match('/^\s*?([^\r\n\f]+)\s*(\S.*?)?\s*$/s', $docBlock, $array)) {
                    $title[$locale] = $array[1];
                    if (!empty($array[2])) {
                        $text[$locale] = $array[2];
                        $text[$locale] = preg_replace('/^\s+/m', '', $text[$locale]);
                    }
                }
            }
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
        return $this->configuration[self::MODIFIED];
    }

    /**
     * get title
     *
     * @access  public
     * @param   string  $language   language
     * @param   string  $country    country
     * @return  string
     */
    public function getTitle($language = null, $country = null)
    {
        assert('is_null($language) || is_string($language); // Wrong type for argument 1. String expected');
        assert('is_null($country) || is_string($country); // Wrong type for argument 2. String expected');
        $node = $this->configuration[PluginAnnotation::TITLE];

        // get defaults
        if (is_null($language)) {
            $languageManager = Language::getInstance();
            $language = $languageManager->getLanguage();
            $country = $languageManager->getCountry();
        }

        if (isset($node["$language-$country"])) {
            return $node["$language-$country"];
        } elseif (isset($node[$language])) {
            return $node[$language];
        } else {
            return $this->configuration[self::DEFAULT_TITLE];
        }
    }

    /**
     * get text
     *
     * @access  public
     * @param   string  $language   language
     * @param   string  $country    country
     * @return  string
     */
    public function getText($language = null, $country = null)
    {
        assert('is_null($language) || is_string($language); // Wrong type for argument 1. String expected');
        assert('is_null($country) || is_string($country); // Wrong type for argument 2. String expected');
        $node = $this->configuration[PluginAnnotation::TEXT];

        // get defaults
        if (is_null($language) && class_exists('Language')) {
            $languageManager = Language::getInstance();
            $language = $languageManager->getLanguage();
            $country = $languageManager->getCountry();
        }

        if (isset($node["$language-$country"])) {
            return $node["$language-$country"];
        } elseif (isset($node[$language])) {
            return $node[$language];
        } else {
            return $this->configuration[self::DEFAULT_TEXT];
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
        return $this->configuration[PluginAnnotation::TYPE];
    }

    /**
     * get author
     *
     * Returns a comma-seperated list of all authors.
     * If there is only one author, this name is returned.
     *
     * @access  public
     * @return  string
     */
    public function getAuthor()
    {
        return implode(', ', $this->configuration[PluginAnnotation::AUTHOR]);
    }

    /**
     * get authors
     *
     * Returns a list of all authors.
     *
     * @access  public
     * @return  array
     */
    public function getAuthors()
    {
        return $this->configuration[PluginAnnotation::AUTHOR];
    }

    /**
     * get priority
     *
     * Returns the plugin's priority level.
     * This is an integer within a range of -1 through 3.
     *
     * -1 = lowest, 0 = low, 1 = normal, 2 = high, 3 = highest
     *
     * The default setting is 0 (low priority).
     *
     * @access  public
     * @return  string
     */
    public function getPriority()
    {
        assert('is_numeric($this->configuration[PluginAnnotation::PRIORITY]); // '.
            'Priority is expected to be a numeric value');
        return (int) $this->configuration[PluginAnnotation::PRIORITY];
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
        return $this->configuration[PluginAnnotation::GROUP];
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
        return $this->configuration[PluginAnnotation::PARENT];
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
        return $this->configuration[PluginAnnotation::REQUIRES];
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
        return $this->configuration[PluginAnnotation::VERSION];
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
        return $this->configuration[PluginAnnotation::URL];
    }

    /**
     * get category
     *
     * Returns the documentation category .
     *
     * This tag is optional.
     *
     * @access  public
     * @return  string
     */
    public function getCategory()
    {
        return $this->configuration[PluginAnnotation::CATEGORY];
    }

    /**
     * get package name
     *
     * Returns the plugin package name.
     * This is also the intended root namespace.
     *
     * @access  public
     * @return  string
     */
    public function getPackage()
    {
        return $this->configuration[PluginAnnotation::PACKAGE];
    }

    /**
     * get sub-package name
     *
     * Returns the plugin sub-package name.
     * This is the intended namespace WITHOUT the root.
     *
     * Example: namespace A\B\C is: package A, sub-packge B\C
     *
     * @access  public
     * @return  string
     */
    public function getSubPackage()
    {
        return $this->configuration[PluginAnnotation::SUBPACKAGE];
    }

    /**
     * get license
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
        return $this->configuration[PluginAnnotation::LICENSE];
    }

    /**
     * get menu names
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
        return $this->configuration[PluginAnnotation::MENU];
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
        foreach ($this->methods as $name => $configuration)
        {
            $menu = $configuration->getMenu();
            if (!empty($menu)) {
                if (is_null($group) || $group == @$menu[PluginAnnotation::GROUP]) {
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
        return $this->configuration[self::DIR];
    }

    /**
     * get plugin's default active state
     *
     * A plugin may define it's own prefered initial
     * active state. Default is 'inactive'.
     *
     * @access  public
     * @return  string
     */
    public function getActive()
    {
        return $this->configuration[PluginAnnotation::ACTIVE];
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
        return $this->className;
    }

    /**
     * get plugin configuration
     *
     * @access  public
     * @return  array
     */
    public function getConfiguration()
    {
        return $this->configuration;
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
        return unserialize($string);
    }
}
?>