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
class PluginConfigurationMethod extends Object implements IsSerializable
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
    /** @var array  */ private $args = array();
    /** @var array  */ private $defaults = array();
    /** @var bool   */ private $hasGenericParams = false;
    /** @var array  */ private $paths = array();

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

        $typeClassTag = $class->getTag(PluginAnnotation::TYPE, 'default');
        $this->paths[] = $class->getDirectory();
        $this->configuration = array
        (
            /* reserved for future use

                PluginAnnotation::TITLE => $method->getTitle(),
                PluginAnnotation::TEXT => $method->getText(),

            */

            PluginAnnotation::TITLE => $method->getTag(PluginAnnotation::TITLE),
            PluginAnnotation::TEXT => "",
            PluginAnnotation::PARAM => array(),
            PluginAnnotation::RETURN_VALUE => $method->getTags(PluginAnnotation::RETURN_VALUE),
            PluginAnnotation::TYPE => mb_strtolower($method->getTag(PluginAnnotation::TYPE, $typeClassTag)),
            PluginAnnotation::TEMPLATE => mb_strtolower($method->getTag(PluginAnnotation::TEMPLATE, 'null')),
            PluginAnnotation::USER => $method->getTags(PluginAnnotation::USER),
            PluginAnnotation::SAFEMODE => $method->getTag(PluginAnnotation::SAFEMODE),
            PluginAnnotation::MENU => $method->getTag(PluginAnnotation::MENU),
            PluginAnnotation::ONERROR => $method->getTag(PluginAnnotation::ONERROR),
            PluginAnnotation::ONSUCCESS => $method->getTag(PluginAnnotation::ONSUCCESS),
            PluginAnnotation::GROUP => mb_strtolower((!is_null($class)) ? $class->getTag(PluginAnnotation::GROUP) : ''),
            PluginAnnotation::OVERWRITE => $method->getTag(PluginAnnotation::OVERWRITE, '0'),
            PluginAnnotation::SUBSCRIBE => $method->getTag(PluginAnnotation::SUBSCRIBE, '0'),
            PluginAnnotation::LANGUAGE => $method->getTags(PluginAnnotation::LANGUAGE, array()),
            PluginAnnotation::SCRIPT => array(),
            PluginAnnotation::STYLE => array()
        );
        // process and add scripts
        assert('!isset($script); // Cannot redeclare var $script');
        foreach ($method->getTags(PluginAnnotation::SCRIPT, array()) as $script)
        {
            if (!is_string($script)) {
                $message = 'Syntax error in @script: ' .$this->className . '::' . $this->methodName . '()';
                Log::report($message, E_USER_ERROR, $param);
                continue;
            }
            $script = $this->paths[0] . "/$script";
            $this->configuration[PluginAnnotation::SCRIPT][] = $script;
        }
        unset($script);
        // process and add styles
        assert('!isset($style); // Cannot redeclare var $style');
        foreach ($method->getTags(PluginAnnotation::STYLE, array()) as $style)
        {
            if (!is_string($style)) {
                $message = 'Syntax error in @style: ' .$this->className . '::' . $this->methodName . '()';
                Log::report($message, E_USER_ERROR, $param);
                continue;
            }
            $style = $this->paths[0] . "/$style";
            $this->configuration[PluginAnnotation::STYLE][] = $style;
        }
        unset($style);
        // process template
        assert('!isset($template); // Cannot redeclare var $template');
        $template = $this->paths[0] . "/" . $this->configuration[PluginAnnotation::TEMPLATE];
        if (is_file($template)) {
            $this->configuration[PluginAnnotation::TEMPLATE] = $template;
        }
        unset($template);
        // process params
        assert('!isset($param); // Cannot redeclare var $param');
        assert('!isset($match); // Cannot redeclare var $match');
        assert('!isset($name); // Cannot redeclare var $name');
        assert('!isset($type); // Cannot redeclare var $type');
        foreach ($method->getTags(PluginAnnotation::PARAM, array()) as $param)
        {
            if (!is_string($param)) {
                $message = 'Syntax error in @param: ' .$this->className . '::' . $this->methodName . '()';
                Log::report($message, E_USER_ERROR, $param);
                continue;
            }
            if (preg_match('/^(\w+)\s+\$(\w+)/', $param, $match)) {
                $name = $match[2];
                $type = $match[1];
                $this->configuration[PluginAnnotation::PARAM][$name] = $type;
            }
        }
        unset($match, $name, $type, $param);
        $this->hasGenericParams = isset($this->configuration[PluginAnnotation::PARAM]['ARGS']);
        /* @var $param ReflectionParameter */
        assert('!isset($param); // Cannot redeclare var $param');
        foreach($method->getParameters() as $i => $param)
        {
            if ($param->isDefaultValueAvailable()) {
                $this->defaults[$i] = $param->getDefaultValue();
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
        return $this->configuration[PluginAnnotation::TYPE];
    }

    /**
     * get directory
     *
     * @access  public
     * @return  string
     */
    public function getPath()
    {
        return $this->paths[0];
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
        return $this->paths;
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
        $this->paths[] = $subscriberConfig->getPath();
        $scripts = $this->configuration[PluginAnnotation::SCRIPT];
        $this->configuration[PluginAnnotation::SCRIPT] = array_merge($scripts, $subscriberConfig->getScripts());
        $lang = $this->configuration[PluginAnnotation::LANGUAGE];
        $this->configuration[PluginAnnotation::LANGUAGE] = array_merge($lang, $subscriberConfig->getLanguages());
        $styles = $this->configuration[PluginAnnotation::STYLE];
        $this->configuration[PluginAnnotation::STYLE] = array_merge($styles, $subscriberConfig->getStyles());
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
        if (!isset($this->configuration[PluginAnnotation::SCRIPT])) {
            $this->configuration[PluginAnnotation::SCRIPT] = array();
        }
        return $this->configuration[PluginAnnotation::SCRIPT];
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
        if (!isset($this->configuration[PluginAnnotation::STYLE])) {
            $this->configuration[PluginAnnotation::STYLE] = array();
        }
        return $this->configuration[PluginAnnotation::STYLE];
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
        if (!isset($this->configuration[PluginAnnotation::LANGUAGE])) {
            $this->configuration[PluginAnnotation::LANGUAGE] = array();
        }
        return $this->configuration[PluginAnnotation::LANGUAGE];
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
        return $this->configuration[PluginAnnotation::PARAM];
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
        return $this->configuration[PluginAnnotation::RETURN_VALUE];
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
        return $this->configuration[PluginAnnotation::GROUP];
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
        return $this->configuration[PluginAnnotation::MENU];
    }

    /**
     * get settings on how to react on success
     *
     * @access  public
     * @return  array
     */
    public function getOnSuccess()
    {
        assert('empty($this->configuration[PluginAnnotation::ONSUCCESS]) || ' .
               'is_array($this->configuration[PluginAnnotation::ONSUCCESS]);');
        return $this->configuration[PluginAnnotation::ONSUCCESS];
    }

    /**
     * get settings on how to react on error
     *
     * @access  public
     * @return  array
     */
    public function getOnError()
    {
        assert('empty($this->configuration[PluginAnnotation::ONERROR]) || ' .
               'is_array($this->configuration[PluginAnnotation::ONERROR]);');
        return $this->configuration[PluginAnnotation::ONERROR];
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
        if (isset($this->configuration[PluginAnnotation::TITLE])) {
            return $this->configuration[PluginAnnotation::TITLE];
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
        if (isset($this->configuration[PluginAnnotation::TEXT])) {
            return $this->configuration[PluginAnnotation::TEXT];
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
        if (is_bool($this->configuration[PluginAnnotation::SAFEMODE])) {
            return $this->configuration[PluginAnnotation::SAFEMODE];

        } else {
            switch ($this->configuration[PluginAnnotation::SAFEMODE])
            {
                case 'false':
                case 'no':
                    return false;
                break;
                case 'true':
                case 'yes':
                    return true;
                break;
                default:
                    return null;
                break;
            }
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
        if (isset($this->configuration[PluginAnnotation::TEMPLATE])) {
            return $this->configuration[PluginAnnotation::TEMPLATE];
        } else {
            return null;
        }
    }

    /**
     * get user security levels
     *
     * Returns an associative array of items like:
     * <code>
     * array(
     *     0 => array(
     *         PluginAnnotation::ROLE => "string",
     *         PluginAnnotation::GROUP => "string",
     *         PluginAnnotation::LEVEL => int(0:100)
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
        if (isset($this->configuration[PluginAnnotation::USER])) {
            return $this->configuration[PluginAnnotation::USER];
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
        if (!empty($this->configuration[PluginAnnotation::OVERWRITE])) {
            return true;

        } else {
            return false;
        }
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
        if (!empty($this->configuration[PluginAnnotation::SUBSCRIBE])) {
            return true;

        } else {
            return false;
        }
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
        $this->args = array();

        if ($this->hasGenericParams) {
            $this->args = $args;
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
                            $this->args[$name] = (int) $value;
                        }
                    break;
                    case 'float':
                    case 'double':
                        if (!is_numeric($value)) {
                            throw new InvalidValueWarning($name);
                        } else {
                            $this->args[$name] = (float) $value;
                        }
                    break;
                    case 'bool':
                    case 'boolean':
                        $this->args[$name] = !empty($value);
                    break;
                    case 'array':
                        if (!is_array($value)) {
                            throw new InvalidValueWarning($name);
                        } else {
                            $this->args[$name] = $value;
                        }
                    break;
                    default:
                        if (!is_string($value)) {
                            throw new InvalidValueWarning($name);
                        } else {
                            $this->args[$name] = $value;
                        }
                    break;
                }
            } elseif (array_key_exists($i, $this->defaults)) {
                $this->args[$name] = $this->defaults[$i];
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
            if ($this->hasGenericParams) {
                return $instance->{$this->methodName}($this->args);
            } else {
                return call_user_func_array(array($instance, $this->methodName), $this->args);
            }
        } else {
            return $instance->_default($this->methodName, $this->args);
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
     * serialize this object to a string
     *
     * Returns the serialized object as a string.
     *
     * @access  public
     * @return  string
     */
    public function serialize()
    {
        $this->args = array();
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