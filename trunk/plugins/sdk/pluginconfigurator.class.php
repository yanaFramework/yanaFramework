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
                PluginAnnotation::TITLE => '',
                PluginAnnotation::TEXT => '',
                self::DIR => '',
                PluginAnnotation::TYPE => 'default',
                PluginAnnotation::AUTHOR => array(),
                PluginAnnotation::PRIORITY => '',
                PluginAnnotation::GROUP => '',
                PluginAnnotation::PARENT => '',
                PluginAnnotation::REQUIRES => array(),
                PluginAnnotation::LICENSE => '',
                PluginAnnotation::URL => '',
                PluginAnnotation::VERSION => '',
                PluginAnnotation::CATEGORY => '',
                PluginAnnotation::PACKAGE => 'yana',
                PluginAnnotation::SUBPACKAGE => 'plugins',
                self::MODIFIED => time(),
                PluginAnnotation::MENU => array(),
                PluginAnnotation::ACTIVE => '0'
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
                $this->configuration[PluginAnnotation::PARENT] = $parent;
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
                $this->configuration[PluginAnnotation::GROUP] = $group;
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
                $this->configuration[PluginAnnotation::TYPE] = $type;
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
        $this->configuration[PluginAnnotation::PRIORITY] = PluginPriority::getPriority($priority);
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
        $this->configuration[PluginAnnotation::AUTHOR][] = strip_tags(nl2br($author));
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

        $this->configuration[parent::DEFAULT_TEXT] = str_replace("\n", '<br/>', strip_tags($description));
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
        $this->configuration[PluginAnnotation::URL] = str_replace("\n", '<br/>', strip_tags($url));
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
            throw new AlreadyExistsException("Another method by the name '$methodName' already exists.");
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
        $this->configuration[PluginAnnotation::MENU][] = array(
            PluginAnnotation::GROUP => "$group",
            PluginAnnotation::TITLE => "$title"
        );
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
            $string .= $tab . "@menu       group: " . $menu[PluginAnnotation::GROUP];
            if (isset($menu[PluginAnnotation::TITLE])) {
                $string .= ', title: ' . $menu[PluginAnnotation::TITLE];
            }
        }
        if ($this->getActive() === PluginActivity::DEFAULT_ACTIVE) {
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