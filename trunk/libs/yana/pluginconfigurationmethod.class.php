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
 * Plugin Method information
 *
 * This class represents a plugin method's meta information.
 * This is it's interface, name and description and more.
 *
 * @access      public
 * @name        PluginConfigurationMethod
 * @package     yana
 * @subpackage  core
 *
 * @ignore
 */
class PluginConfigurationMethod extends Object
{
    /**#@+
     * @ignore
     * @access  protected
     */

    /** @var string */ protected $className = "";
    /** @var string */ protected $methodName = "";
    /** @var array  */ protected $configuration = array();

    /**#@-*/

    /**#@+
     * @ignore
     * @access  private
     */
    /** @var array  */ private $_args = array();
    /** @var array  */ private $_defaults = array();
    /** @var bool   */ private $_hasGenericParams = false;
    /** @var array  */ private $_paths = array();

    /**#@-*/

    /**
     * Constructor
     *
     * @access  public
     * @param   PluginReflectionMethod  $method  method reflection
     * @param   PluginReflectionClass   $class   class reflection
     */
    public function __construct(PluginReflectionMethod $method, PluginReflectionClass $class)
    {
        $this->className = $method->getClassName();
        $this->methodName = $method->getName();

        $typeClassTag = $class->getTag(PluginAnnotationEnumeration::TYPE, 'default');
        $this->_paths[] = $class->getDirectory();
        $this->configuration = array
        (
            PluginAnnotationEnumeration::TITLE => $method->getTag(PluginAnnotationEnumeration::TITLE),
            PluginAnnotationEnumeration::TEXT => "",
            PluginAnnotationEnumeration::PARAM => array(),
            PluginAnnotationEnumeration::RETURN_VALUE => $method->getTags(PluginAnnotationEnumeration::RETURN_VALUE),
            PluginAnnotationEnumeration::TYPE =>
                mb_strtolower($method->getTag(PluginAnnotationEnumeration::TYPE, $typeClassTag)),
            PluginAnnotationEnumeration::TEMPLATE =>
                mb_strtolower($method->getTag(PluginAnnotationEnumeration::TEMPLATE, 'null')),
            PluginAnnotationEnumeration::USER => $method->getTags(PluginAnnotationEnumeration::USER),
            PluginAnnotationEnumeration::SAFEMODE => $method->getTag(PluginAnnotationEnumeration::SAFEMODE),
            PluginAnnotationEnumeration::MENU => $method->getTag(PluginAnnotationEnumeration::MENU),
            PluginAnnotationEnumeration::ONERROR => $method->getTag(PluginAnnotationEnumeration::ONERROR),
            PluginAnnotationEnumeration::ONSUCCESS => $method->getTag(PluginAnnotationEnumeration::ONSUCCESS),
            PluginAnnotationEnumeration::GROUP =>
                mb_strtolower((!is_null($class)) ? $class->getTag(PluginAnnotationEnumeration::GROUP) : ''),
            PluginAnnotationEnumeration::OVERWRITE => $method->getTag(PluginAnnotationEnumeration::OVERWRITE, '0'),
            PluginAnnotationEnumeration::SUBSCRIBE => $method->getTag(PluginAnnotationEnumeration::SUBSCRIBE, '0'),
            PluginAnnotationEnumeration::LANGUAGE => $method->getTags(PluginAnnotationEnumeration::LANGUAGE),
            PluginAnnotationEnumeration::SCRIPT => array(),
            PluginAnnotationEnumeration::STYLE => array()
        );
        // process and add scripts
        assert('!isset($script); // Cannot redeclare var $script');
        foreach ($method->getTags(PluginAnnotationEnumeration::SCRIPT, array()) as $script)
        {
            if (!is_string($script)) {
                $message = 'Syntax error in @script: ' .$this->className . '::' . $this->methodName . '()';
                Log::report($message, E_USER_ERROR, $param);
                continue;
            }
            $script = $this->_paths[0] . "/$script";
            $this->configuration[PluginAnnotationEnumeration::SCRIPT][] = $script;
        }
        unset($script);
        // process and add styles
        assert('!isset($style); // Cannot redeclare var $style');
        foreach ($method->getTags(PluginAnnotationEnumeration::STYLE, array()) as $style)
        {
            if (!is_string($style)) {
                $message = 'Syntax error in @style: ' .$this->className . '::' . $this->methodName . '()';
                Log::report($message, E_USER_ERROR, $param);
                continue;
            }
            $style = $this->_paths[0] . "/$style";
            $this->configuration[PluginAnnotationEnumeration::STYLE][] = $style;
        }
        unset($style);
        // process template
        assert('!isset($template); // Cannot redeclare var $template');
        $template = $this->_paths[0] . "/" . $this->configuration[PluginAnnotationEnumeration::TEMPLATE];
        if (is_file($template)) {
            $this->configuration[PluginAnnotationEnumeration::TEMPLATE] = $template;
        }
        unset($template);
        // process params
        assert('!isset($param); // Cannot redeclare var $param');
        assert('!isset($match); // Cannot redeclare var $match');
        assert('!isset($name); // Cannot redeclare var $name');
        assert('!isset($type); // Cannot redeclare var $type');
        foreach ($method->getTags(PluginAnnotationEnumeration::PARAM, array()) as $param)
        {
            if (!is_string($param)) {
                $message = 'Syntax error in @param: ' .$this->className . '::' . $this->methodName . '()';
                Log::report($message, E_USER_ERROR, $param);
                continue;
            }
            if (preg_match('/^(\w+)\s+\$(\w+)/', $param, $match)) {
                $name = $match[2];
                $type = $match[1];
                $this->configuration[PluginAnnotationEnumeration::PARAM][$name] = $type;
            }
        }
        unset($match, $name, $type, $param);
        $this->_hasGenericParams = isset($this->configuration[PluginAnnotationEnumeration::PARAM]['ARGS']);
        /* @var $param ReflectionParameter */
        assert('!isset($param); // Cannot redeclare var $param');
        foreach($method->getParameters() as $i => $param)
        {
            if ($param->isDefaultValueAvailable()) {
                $this->_defaults[$i] = $param->getDefaultValue();
            }
        }
        unset($param);
    }

    /**
     * get type
     *
     * @access  public
     * @return  string
     */
    public function getType()
    {
        return $this->configuration[PluginAnnotationEnumeration::TYPE];
    }

    /**
     * get directory
     *
     * @access  public
     * @return  string
     */
    public function getPath()
    {
        return $this->_paths[0];
    }

    /**
     * get directory names of subscribing plugins
     *
     * This includes the path of the implementing method, as it always subscribes to itself.
     *
     * @access  public
     * @return  array
     */
    public function getPaths()
    {
        return $this->_paths;
    }

    /**
     * get directory
     *
     * @access  public
     * @param   PluginConfigurationMethod $subscriberConfig  configuration of subscribing method
     * @ignore
     */
    public function addSubscription(PluginConfigurationMethod $subscriberConfig)
    {
        $this->_paths[] = $subscriberConfig->getPath();
        $scripts = $this->configuration[PluginAnnotationEnumeration::SCRIPT];
        $this->configuration[PluginAnnotationEnumeration::SCRIPT] = array_merge($scripts, $subscriberConfig->getScripts());
        $lang = $this->configuration[PluginAnnotationEnumeration::LANGUAGE];
        $this->configuration[PluginAnnotationEnumeration::LANGUAGE] = array_merge($lang, $subscriberConfig->getLanguages());
        $styles = $this->configuration[PluginAnnotationEnumeration::STYLE];
        $this->configuration[PluginAnnotationEnumeration::STYLE] = array_merge($styles, $subscriberConfig->getStyles());
    }

    /**
     * get javascripts
     *
     * Returns a list of all associated javascript files.
     * These are loaded together with the template.
     *
     * @access  public
     * @return  array
     */
    public function getScripts()
    {
        if (!isset($this->configuration[PluginAnnotationEnumeration::SCRIPT])) {
            $this->configuration[PluginAnnotationEnumeration::SCRIPT] = array();
        }
        assert('is_array($this->configuration[PluginAnnotationEnumeration::SCRIPT]);');
        return $this->configuration[PluginAnnotationEnumeration::SCRIPT];
    }

    /**
     * get CSS-styles
     *
     * Returns a list of all associated CSS files.
     * These are loaded together with the template.
     *
     * @access  public
     * @return  array
     */
    public function getStyles()
    {
        if (!isset($this->configuration[PluginAnnotationEnumeration::STYLE])) {
            $this->configuration[PluginAnnotationEnumeration::STYLE] = array();
        }
        assert('is_array($this->configuration[PluginAnnotationEnumeration::STYLE]);');
        return $this->configuration[PluginAnnotationEnumeration::STYLE];
    }

    /**
     * get language files
     *
     * Returns a list of all associated XLIFF files.
     * These are loaded together with the template.
     *
     * @access  public
     * @return  array
     */
    public function getLanguages()
    {
        if (!isset($this->configuration[PluginAnnotationEnumeration::LANGUAGE])) {
            $this->configuration[PluginAnnotationEnumeration::LANGUAGE] = array();
        }
        assert('is_array($this->configuration[PluginAnnotationEnumeration::LANGUAGE]);');
        return $this->configuration[PluginAnnotationEnumeration::LANGUAGE];
    }

    /**
     * get parameters
     *
     * Returns a list of all parameters as an array,
     * where the keys are the param-names and the values are the param-types.
     *
     * Example:
     * <code>
     * array(
     *     'id' => 'int',
     *     'title' => 'string'
     * );
     * </code>
     *
     * @access  public
     * @return  array
     */
    public function getParams()
    {
        assert('is_array($this->configuration[PluginAnnotationEnumeration::PARAM]);');
        return $this->configuration[PluginAnnotationEnumeration::PARAM];
    }

    /**
     * get return value
     *
     * Returns the methods return value.
     *
     * @access  public
     * @return  string
     */
    public function getReturn()
    {
        return $this->configuration[PluginAnnotationEnumeration::RETURN_VALUE];
    }

    /**
     * get group
     *
     * Returns the method's group (if any).
     * This is similar to a "package" in OO-style programming languages.
     *
     * A group may have multiple plugins, but a plugin may only be a member of one group.
     *
     * @access  public
     * @return  string
     */
    public function getGroup()
    {
        return $this->configuration[PluginAnnotationEnumeration::GROUP];
    }

    /**
     * get menu entry
     *
     * Each plugin may define it's own menues and add entries to them. The names
     * are defined in the file's doc-block, while the menu entries are defined
     * at the methods that are to be added to the menu.
     *
     * Use this function to get the menu entry defined by the method (if any).
     *
     * @access  public
     * @return  array
     */
    public function getMenu()
    {
        if (empty($this->configuration[PluginAnnotationEnumeration::MENU])) {
            $this->configuration[PluginAnnotationEnumeration::MENU] = array();
        }
        assert('is_array($this->configuration[PluginAnnotationEnumeration::MENU]);');
        return $this->configuration[PluginAnnotationEnumeration::MENU];
    }

    /**
     * get settings on how to react on success
     *
     * @access  public
     * @return  array
     */
    public function getOnSuccess()
    {
        if (empty($this->configuration[PluginAnnotationEnumeration::ONSUCCESS])) {
            $this->configuration[PluginAnnotationEnumeration::ONSUCCESS] = array();
        }
        assert('is_array($this->configuration[PluginAnnotationEnumeration::ONSUCCESS]);');
        return $this->configuration[PluginAnnotationEnumeration::ONSUCCESS];
    }

    /**
     * get settings on how to react on error
     *
     * @access  public
     * @return  array
     */
    public function getOnError()
    {
        if (empty($this->configuration[PluginAnnotationEnumeration::ONERROR])) {
            $this->configuration[PluginAnnotationEnumeration::ONERROR] = array();
        }
        assert('is_array($this->configuration[PluginAnnotationEnumeration::ONERROR]);');
        return $this->configuration[PluginAnnotationEnumeration::ONERROR];
    }

    /**
     * get human readable name
     *
     * Returns the name (title) as defined in the method's doc block.
     *
     * @access  public
     * @return  string
     */
    public function getName()
    {
        if (isset($this->configuration[PluginAnnotationEnumeration::TITLE])) {
            assert('is_string($this->configuration[PluginAnnotationEnumeration::TITLE]);');
            return $this->configuration[PluginAnnotationEnumeration::TITLE];
        } else {
            return null;
        }
    }

    /**
     * get text of method
     *
     * @access  public
     * @return  string
     */
    public function getText()
    {
        if (isset($this->configuration[PluginAnnotationEnumeration::TEXT])) {
            assert('is_string($this->configuration[PluginAnnotationEnumeration::TEXT]);');
            return $this->configuration[PluginAnnotationEnumeration::TEXT];
        } else {
            return null;
        }
    }

    /**
     * get safemode setting of method
     *
     * Returns value of safemode setting.
     *
     * It is:
     *  bool(true) for "safemode must be active",
     *  bool(false) for "safemode must NOT be active",
     *  or NULL for "don't care".
     *
     * Allowed values are:
     *  Boolean true or false,
     *  Strings "true", "false", "yes", "no"
     *
     * Any other value will return NULL.
     *
     * @access  public
     * @return  bool
     */
    public function getSafeMode()
    {
        if (!isset($this->configuration[PluginAnnotationEnumeration::SAFEMODE])) {
            return null;
        }
        switch ($this->configuration[PluginAnnotationEnumeration::SAFEMODE])
        {
            case false:
            case 'false':
            case 'no':
                return false;
            case true:
            case 'true':
            case 'yes':
                return true;
            default:
                return null;
        }
    }

    /**
     * get template
     *
     * @access  public
     * @return  string
     */
    public function getTemplate()
    {
        return $this->configuration[PluginAnnotationEnumeration::TEMPLATE];
    }

    /**
     * get user security levels
     *
     * Returns an associative array of items like:
     * <code>
     * array(
     *     0 => array(
     *         PluginAnnotationEnumeration::ROLE => "string",
     *         PluginAnnotationEnumeration::GROUP => "string",
     *         PluginAnnotationEnumeration::LEVEL => int(0:100)
     *     ),
     *     1 => ...
     * );
     * </code>
     *
     * @access  public
     * @return  array
     */
    public function getUserLevels()
    {
        if (isset($this->configuration[PluginAnnotationEnumeration::USER])) {
            return $this->configuration[PluginAnnotationEnumeration::USER];
        } else {
            return null;
        }
    }

    /**
     * get overwrite setting of method
     *
     * Returns value of overwrite setting.
     *
     * A method my overwrite the method of it's parent plugin.
     * To do so, it defines the annotation "overwrite".
     * The annotation is a flag, that has no special value.
     *
     * This has no effect if the plugin does not define a parent.
     *
     * @access  public
     * @return  bool
     */
    public function getOverwrite()
    {
        return !empty($this->configuration[PluginAnnotationEnumeration::OVERWRITE]);
    }

    /**
     * get subscribe setting of method
     *
     * Returns value of subscribe setting.
     *
     * A method my subscribe to an event that it doesn't define itself.
     * If so, it uses the annotation "subscribe" and must NOT use other
     * annotations to change the type of event et cetera.
     *
     * Note: you may NOT use the annotations "overwrite" and "subscribe" at
     * the same time.
     *
     * @access  public
     * @return  bool
     */
    public function getSubscribe()
    {
        return !empty($this->configuration[PluginAnnotationEnumeration::SUBSCRIBE]);
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
     * get method name
     *
     * @access  public
     * @return  string
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * set event arguments
     *
     * @access  public
     * @param   array  $args  list of arguments
     * @throws  Warning       when a provided argument is missing or not valid
     */
    public function setEventArguments(array $args)
    {
        $this->_args = array();

        if ($this->_hasGenericParams) {
            $this->_args = $args;
            return;
        }

        $i = 0;
        foreach ($this->getParams() as $name => $type)
        {
            $name = strtolower($name);
            if (isset($args[$name]) && ($args[$name] !== '' || ($type === 'bool' || $type === 'boolean'))) {
                $value = $args[$name];
                switch ($type)
                {
                    case 'integer':
                    case 'int':
                        if (!is_numeric($value)) {
                            throw new InvalidValueWarning($name);
                        } else {
                            $this->_args[$name] = (int) $value;
                        }
                    break;
                    case 'float':
                    case 'double':
                        if (!is_numeric($value)) {
                            throw new InvalidValueWarning($name);
                        } else {
                            $this->_args[$name] = (float) $value;
                        }
                    break;
                    case 'bool':
                    case 'boolean':
                        $this->_args[$name] = !empty($value);
                    break;
                    case 'array':
                        if (!is_array($value)) {
                            throw new InvalidValueWarning($name);
                        } else {
                            $this->_args[$name] = $value;
                        }
                    break;
                    default:
                        if (!is_string($value)) {
                            throw new InvalidValueWarning($name);
                        } else {
                            $this->_args[$name] = $value;
                        }
                    break;
                }
            } elseif (array_key_exists($i, $this->_defaults)) {
                $this->_args[$name] = $this->_defaults[$i];
            } else {
                // missing parameter
                throw new MissingFieldWarning($name);
            }
            $i++;
        } // end foreach
    }

    /**
     * send event
     *
     * Executes the event on the provided instance and returns the result.
     *
     * @access  public
     * @param   IsPlugin  $instance  object to send event to
     * @return  mixed
     */
    public function sendEvent(IsPlugin $instance)
    {
        if ($this->hasMethod($instance)) {
            if ($this->_hasGenericParams) {
                return $instance->{$this->methodName}($this->_args);
            } else {
                return call_user_func_array(array($instance, $this->methodName), $this->_args);
            }
        } else {
            return $instance->catchAll($this->methodName, $this->_args);
        }
    }

    /**
     * plug-in has method
     *
     * Returns bool(true) if the given plug-in implements this method and bool(false) otherwise.
     *
     * @access  public
     * @param   IsPlugin  $instance  object to send event to
     * @return  bool
     */
    public function hasMethod(IsPlugin $instance)
    {
        return method_exists($instance, $this->methodName);
    }

    /**
     * Reinitialize instance.
     *
     * @access  public
     */
    public function __wakeup()
    {
        $this->_args = array();
    }

}

?>