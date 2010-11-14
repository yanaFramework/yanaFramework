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
require_once 'pluginmethodconfigurator.class.php';

/**
 * Plugin configurator for creating manual configurations
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class PluginConfigurator extends PluginConfiguration
{
    /**
     * the plugin's identifier
     *
     * @access  private
     * @var     string
     */
    private $_id = null;

    /**
     * Constructor
     *
     * @access  public
     * @param   PluginReflectionClass  $pluginClass plugin configuration class
     */
    public function __construct(PluginReflectionClass $pluginClass = NULL)
    {
        if (!is_null($pluginClass)) {
            parent::__construct($pluginClass);
        } else {
            $this->configuration = array
            (
                self::DEFAULT_TITLE => '',
                self::DEFAULT_TEXT => '',
                PluginAnnotationEnumeration::TITLE => '',
                PluginAnnotationEnumeration::TEXT => '',
                self::DIR => '',
                PluginAnnotationEnumeration::TYPE => 'default',
                PluginAnnotationEnumeration::AUTHOR => array(),
                PluginAnnotationEnumeration::PRIORITY => '',
                PluginAnnotationEnumeration::GROUP => '',
                PluginAnnotationEnumeration::PARENT => '',
                PluginAnnotationEnumeration::REQUIRES => array(),
                PluginAnnotationEnumeration::LICENSE => '',
                PluginAnnotationEnumeration::URL => '',
                PluginAnnotationEnumeration::VERSION => '',
                PluginAnnotationEnumeration::CATEGORY => '',
                PluginAnnotationEnumeration::PACKAGE => 'yana',
                PluginAnnotationEnumeration::SUBPACKAGE => 'plugins',
                self::MODIFIED => time(),
                PluginAnnotationEnumeration::MENU => array(),
                PluginAnnotationEnumeration::ACTIVE => '0'
            );
        }
    }

    /**
     * set name
     *
     * @access  public
     * @param   string  $name  plugin name
     */
    public function setTitle($name)
    {
        switch (true)
        {
            case !is_string($name):
            case mb_strlen($name) > 15:
            case !preg_match('/^[\d\w-_ äüöß\(\)]+$/si', $name):
                $data = array(
                    'FIELD' => 'NAME',
                    'VALUE' => print_r($name, true),
                    'VALID' => 'a-z, 0-9, -, _, ß, ä, ö, ü, " "'
                );
                $error = new InvalidCharacterWarning();
                $error->setData($data);
                throw new $error;
            break;
            default:
                $this->configuration[parent::DEFAULT_TITLE] = $name;
                $this->_id = preg_replace('/[^\d\w_]/', '_', mb_strtolower($name));
                $this->className = 'plugin_' . $this->_id;
            break;
        }
    }

    /**
     * get plug-in's id
     *
     * @access  public
     * @return  string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * set parent
     *
     * @access  public
     * @param   string  $parent  plugin name
     */
    public function setParent($parent)
    {
        switch (true)
        {
            case !is_string($parent):
            case !preg_match('/^[\d\w-_]*$/si', $parent):
                $data = array(
                    'FIELD' => 'PARENT',
                    'VALUE' => print_r($parent, true),
                    'VALID' => 'a-z, 0-9, -, _'
                );
                $error = new InvalidCharacterWarning();
                $error->setData($data);
                throw new $error;
            break;
            default:
                $parent = strip_tags(nl2br($parent));
                if (!empty($parent)) {
                    $this->configuration[PluginAnnotationEnumeration::PARENT] = $parent;
                }
            break;
        }
    }

    /**
     * set group name
     *
     * @access  public
     * @param   string  $group  group name
     */
    public function setGroup($group)
    {
        switch (true)
        {
            case !is_string($group):
            case !preg_match('/^[\d\w-_]*$/si', $group):
                $data = array(
                    'FIELD' => 'PACKAGE',
                    'VALUE' => print_r($group, true),
                    'VALID' => 'a-z, 0-9, -, _'
                );
                $error = new InvalidCharacterWarning();
                $error->setData($data);
                throw new $error;
            break;
            default:
                $group = strip_tags(nl2br($group));
                if (!empty($group)) {
                    $this->configuration[PluginAnnotationEnumeration::GROUP] = $group;
                }
            break;
        }
    }

    /**
     * set type
     *
     * Valid types are: primary, default, config, read, write, security, library
     *
     * @access  public
     * @param   string  $type  plugin type
     */
    public function setType($type)
    {
        $type = strtolower($type);
        switch ($type)
        {
            case 'primary':
            case 'default':
            case 'config':
            case 'read':
            case 'write':
            case 'security':
            case 'library':
                $this->configuration[PluginAnnotationEnumeration::TYPE] = $type;
            break;
            default:
                $data = array(
                    'FIELD' => 'TYPE',
                    'VALUE' => print_r($type, true),
                    'VALID' => 'primary, default, config, read, write, security, library'
                );
                $error = new InvalidCharacterWarning();
                $error->setData($data);
                throw new $error;
            break;
        }
    }

    /**
     * set plug-in's priority
     *
     * Valid values are: lowest, low, normal, high, highest
     *
     * @access  public
     * @param   string  $priority  priority identifier
     */
    public function setPriority($priority)
    {
        $this->configuration[PluginAnnotationEnumeration::PRIORITY] = PluginPriorityEnumeration::getPriority($priority);
    }

    /**
     * set author
     *
     * @access  public
     * @param   string  $author  author name
     */
    public function setAuthor($author)
    {
        if (!is_string($author)) {
            throw new InvalidInputWarning();
        }
        $author = strip_tags(nl2br($author));
        if (!empty($author)) {
            $this->configuration[PluginAnnotationEnumeration::AUTHOR][] = $author;
        }
    }

    /**
     * set description
     *
     * @access  public
     * @param   string  $description  text describing what the plug-in does
     */
    public function setText($description)
    {
        if (!is_string($description)) {
            throw new InvalidInputWarning();
        }
        $description = str_replace("\n", '<br/>', strip_tags($description));

        if (!empty($description)) {
            $this->configuration[parent::DEFAULT_TEXT] = $description;
        }
    }

    /**
     * set URL
     *
     * An URL pointing to a website, where the user may get updates or more
     * information.
     *
     * @access  public
     * @param   string  $url  text describing what the plug-in does
     */
    public function setUrl($url)
    {
        if (!is_string($url)) {
            throw new InvalidInputWarning();
        }
        $url = str_replace("\n", '<br/>', strip_tags($url));
        if (!empty($url)) {
            $this->configuration[PluginAnnotationEnumeration::URL] = $url;
        }
    }

    /**
     * add a new method configurator
     *
     * Adds a method for configuration and returns it.
     *
     * @access  public
     * @param   string  $name  method name
     * @return  PluginMethodConfigurator
     */
    public function addMethod($name)
    {
        $method = new PluginMethodConfigurator();
        $method->setTitle($name);
        $methodName = $method->getMethodName();
        if (isset($this->methods[$methodName])) {
            $message = "There are two methods by the name '$methodName'. Chek your interace settings";
            throw new AlreadyExistsWarning($message);
        }
        $this->methods[$methodName] = $method;
        return $method;
    }

    /**
     * add a menu
     *
     * @access  public
     * @param   string  $group  menu id
     * @param   string  $title  menu title
     */
    public function addMenu($group, $title)
    {
        assert('is_string($group); // Wrong argument type argument 1. String expected');
        assert('is_string($title); // Wrong argument type argument 2. String expected');
        if (!empty($group)) {
            $this->configuration[PluginAnnotationEnumeration::MENU][] = array(
                PluginAnnotationEnumeration::GROUP => "$group",
                PluginAnnotationEnumeration::TITLE => "$title"
            );
        }
    }

    /**
     * convert to string
     *
     * Outputs the results as a PHP-Doc comment
     *
     * @access  public
     * @return  string
     */
    public function toString()
    {
        $tab = "\n * ";
        $string = "/**" . $tab;
        // head line
        if ($this->getTitle()) {
            $string .= $this->getTitle();
        } else {
            $string .= $this->getClassName();
        }
        $string .= $tab; // empty line after title
        // description
        if ($this->getText()) {
            $text = $this->getText();
            preg_replace('/^/m', $tab, $text);
            $string .= $text . $tab;
        }
        // annotations
        if ($this->getType()) {
            $string .= $tab . "@type       " . $this->getType();
        }
        if ($this->getGroup()) {
            $string .= $tab . "@group      " . $this->getGroup();
        }
        if ($this->getParent()) {
            $string .= $tab . "@extends    " . $this->getParent();
        }
        foreach ($this->getDependencies() as $dependency)
        {
            $string .= $tab . "@requires   " . $dependency;
        }
        if ($this->getPriority()) {
            $string .= $tab . "@priority   " . $this->getPriority();
        }
        foreach ($this->getMenuNames() as $menu)
        {
            $string .= $tab . "@menu       group: " . $menu[PluginAnnotationEnumeration::GROUP];
            if (isset($menu[PluginAnnotationEnumeration::TITLE])) {
                $string .= ', title: ' . $menu[PluginAnnotationEnumeration::TITLE];
            }
        }
        if ($this->getActive() === PluginActivityEnumeration::DEFAULT_ACTIVE) {
            $string .= $tab . "@active     always";
        }
        foreach ($this->getAuthors() as $author)
        {
            $string .= $tab . "@author     " . $author;
        }
        if ($this->getLicense()) {
            $string .= $tab . "@licence    " . $this->getLicense();
        }
        if ($this->getVersion()) {
            $string .= $tab . "@version    " . $this->getVersion();
        }
        if ($this->getUrl()) {
            $string .= $tab . "@url        " . $this->getUrl();
        }
        if ($this->getCategory()) {
            $string .= $tab . "@category   " . $this->getCategory();
        }
        if ($this->getPackage()) {
            $string .= $tab . "@package    " . $this->getPackage();
        }
        if ($this->getSubPackage()) {
            $string .= $tab . "@subpackage " . $this->getSubPackage();
        }
        $string .= "\n */";
        return $string;
    }
}

?>