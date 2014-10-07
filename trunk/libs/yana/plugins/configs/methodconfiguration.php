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
 * Plugin Method information
 *
 * This class represents a plugin method's meta information.
 * This is it's interface, name and description and more.
 *
 * @access      public
 * @name        PluginConfigurationMethod
 * @package     yana
 * @subpackage  plugins
 *
 * @ignore
 */
class MethodConfiguration extends \Yana\Core\Object
{
    /**
     * @access  private
     * @var     string
     */
    private $_className = "";

    /**
     * @access  private
     * @var     string
     */
    private $_methodName = "";

    /**
     * @access  private
     * @var     array
     */
    private $_args = array();

    /**
     * @access  private
     * @var     array
     */
    private $_params = array();

    /**
     * @access  private
     * @var     string
     */
    private $_return = "";

    /**
     * @access  private
     * @var     array
     */
    private $_defaults = array();

    /**
     * @access  private
     * @var     bool
     */
    private $_hasGenericParams = false;

    /**
     * @access  private
     * @var     array
     */
    private $_paths = array();

    /**
     * Method title.
     *
     * @access  private
     * @var     string
     */
    private $_title = "";

    /**
     * Plugin method type.
     *
     * @access  private
     * @var     string
     */
    private $_type = \Yana\Plugins\TypeEnumeration::DEFAULT_SETTING;

    /**
     * Template identifier.
     *
     * @access  private
     * @var     string
     */
    private $_template = "";

    /**
     * User settings.
     *
     * @access  private
     * @var     string
     */
    private $_users = array();

    /**
     * @access  private
     * @var     bool
     */
    private $_safeMode = null;

    /**
     * @access  private
     * @var     \Yana\Plugins\MenuEntry
     */
    private $_menu = null;

    /**
     * @access  private
     * @var     \Yana\Plugins\Configs\EventRoute
     */
    private $_onError = null;

    /**
     * @access  private
     * @var     \Yana\Plugins\Configs\EventRoute
     */
    private $_onSuccess = null;

    /**
     * @access  private
     * @var     string
     */
    private $_group = "";

    /**
     * @access  private
     * @var     bool
     */
    private $_overwrite = false;

    /**
     * @access  private
     * @var     bool
     */
    private $_subscribe = false;

    /**
     * @access  private
     * @var     array
     */
    private $_languages = array();

    /**
     * @access  private
     * @var     array
     */
    private $_scripts = array();

    /**
     * @access  private
     * @var     array
     */
    private $_styles = array();

    /**
     * Get method type.
     *
     * @access  public
     * @return  string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Set method type.
     *
     * Valid types are: default, config, read, write, security, library.
     *
     * @access  public
     * @param   string  $type  valid method type
     * @return  \Yana\Plugins\Configs\MethodConfiguration
     */
    public function setType($type)
    {
        assert('is_string($type); // Invalid argument $type: string expected');
        $this->_type = \Yana\Plugins\TypeEnumeration::fromString($type);
        return $this;
    }

    /**
     * Add directory.
     *
     * @access  public
     * @param   string  $path  absolute path to plugin class file
     */
    public function addPath($path)
    {
        assert('is_string($path); // Invalid argument $path: string expected');
        $this->_paths[] = $path;
    }

    /**
     * Get directory.
     *
     * @access  public
     * @return  string
     */
    public function getPath()
    {
        if (!empty($this->_paths)) {
            return $this->_paths[0];
        } else {
            return '';
        }
    }

    /**
     * Fet directory names of subscribing plugins.
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
     * @param   \Yana\Plugins\Configs\MethodConfiguration $subscriberConfig  configuration of subscribing method
     * @return  \Yana\Plugins\Configs\MethodConfiguration
     * @ignore
     */
    public function addSubscription(\Yana\Plugins\Configs\MethodConfiguration $subscriberConfig)
    {
        $this->addPath($subscriberConfig->getPath());
        $this->setScripts(array_merge($this->getScripts(), $subscriberConfig->getScripts()));
        $this->setLanguages(array_merge($this->getLanguages(), $subscriberConfig->getLanguages()));
        $this->setStyles(array_merge($this->getStyles(), $subscriberConfig->getStyles()));
        return $this;
    }

    /**
     * Get Javascript files.
     *
     * Returns a list of all associated javascript files.
     * These are loaded together with the template.
     *
     * @access  public
     * @return  array
     */
    public function getScripts()
    {
        return $this->_scripts;
    }

    /**
     * Set Javascript files.
     *
     * @access  public
     * @param   array  $scripts  list of paths to javascript files.
     * @return  \Yana\Plugins\Configs\MethodConfiguration
     */
    public function setScripts(array $scripts)
    {
        $this->_scripts = $scripts;
        return $this;
    }

    /**
     * Get CSS-styles.
     *
     * Returns a list of all associated CSS files.
     * These are loaded together with the template.
     *
     * @access  public
     * @return  array
     */
    public function getStyles()
    {
        return $this->_styles;
    }

    /**
     * Set CSS styles.
     *
     * @access  public
     * @param   array  $styles  list of paths to CSS files.
     * @return  \Yana\Plugins\Configs\MethodConfiguration
     */
    public function setStyles(array $styles)
    {
        $this->_styles = $styles;
        return $this;
    }

    /**
     * Get language files.
     *
     * Returns a list of all associated XLIFF files.
     * These are loaded together with the template.
     *
     * @access  public
     * @return  array
     */
    public function getLanguages()
    {
        return $this->_languages;
    }

    /**
     * Set language files.
     *
     * @access  public
     * @param   array  $languages  list of names of XLIFF files.
     * @return  \Yana\Plugins\Configs\MethodConfiguration
     */
    public function setLanguages(array $languages)
    {
        $this->_languages = $languages;
        return $this;
    }

    /**
     * Get parameters.
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
        return $this->_params;
    }

    /**
     * Set parameters.
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
     * @param   array  $params  keys are the param-names and the values are the param-types
     * @return  \Yana\Plugins\Configs\MethodConfiguration
     */
    public function setParams(array $params)
    {
        $this->_params = $params;
        return $this;
    }

    /**
     * Get return value.
     *
     * Returns the methods return value.
     *
     * @access  public
     * @return  string
     */
    public function getReturn()
    {
        return $this->_return;
    }

    /**
     * Set return value.
     *
     * @access  public
     * @param   string  $return  valid PHP type - or empty string, if the function doesn't return a value
     * @return  \Yana\Plugins\Configs\MethodConfiguration
     */
    public function setReturn($return)
    {
        assert('is_string($return); // Invalid argument $return: string expected');
        $this->_return = (string) $return;
        return $this;
    }

    /**
     * Get group.
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
        return $this->_group;
    }

    /**
     * Set group.
     *
     * A group may have multiple plugins, but a plugin may only be a member of one group.
     *
     * @access  public
     * @param   string  $group  unique name
     * @return  \Yana\Plugins\Configs\MethodConfiguration
     */
    public function setGroup($group)
    {
        assert('is_string($group); // Invalid argument $group: string expected');
        $this->_group = (string) $group;
        return $this;
    }

    /**
     * Get menu entry.
     *
     * Each plugin may define it's own menues and add entries to them. The names
     * are defined in the file's doc-block, while the menu entries are defined
     * at the methods that are to be added to the menu.
     *
     * Use this function to get the menu entry defined by the method (if any).
     *
     * @access  public
     * @return  \Yana\Plugins\MenuEntry
     */
    public function getMenu()
    {
        return $this->_menu;
    }

    /**
     * Set menu entry.
     *
     * @access  public
     * @param   \Yana\Plugins\MenuEntry  $menu  menu configuration
     * @return  \Yana\Plugins\Configs\MethodConfiguration
     */
    public function setMenu(\Yana\Plugins\MenuEntry $menu)
    {
        $this->_menu = $menu;
        return $this;
    }

    /**
     * Get settings on how to react on success.
     *
     * @access  public
     * @return  \Yana\Plugins\Configs\EventRoute
     */
    public function getOnSuccess()
    {
        return $this->_onSuccess;
    }

    /**
     * Set settings on how to react on success.
     *
     * @access  public
     * @param   \Yana\Plugins\Configs\EventRoute  $onSuccess  event configuration
     * @return  \Yana\Plugins\Configs\MethodConfiguration
     */
    public function setOnSuccess(\Yana\Plugins\Configs\EventRoute $onSuccess)
    {
        $onSuccess->setCode(\Yana\Plugins\Configs\EventRoute::CODE_SUCCESS);
        $this->_onSuccess = $onSuccess;
        return $this;
    }

    /**
     * Get settings on how to react on error.
     *
     * @access  public
     * @return  \Yana\Plugins\Configs\EventRoute
     */
    public function getOnError()
    {
        return $this->_onError;
    }

    /**
     * Set settings on how to react on error.
     *
     * @access  public
     * @param   \Yana\Plugins\Configs\EventRoute  $onError  event configuration
     * @return  \Yana\Plugins\Configs\MethodConfiguration
     */
    public function setOnError(\Yana\Plugins\Configs\EventRoute $onError)
    {
        $onError->setCode(\Yana\Plugins\Configs\EventRoute::CODE_ERROR);
        $this->_onError = $onError;
        return $this;
    }

    /**
     * Get human readable name.
     *
     * Returns the name (title) as defined in the method's doc block.
     *
     * @access  public
     * @return  string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Set title.
     *
     * @access  public
     * @param   string  $title  human readable name
     * @return  \Yana\Plugins\Configs\MethodConfiguration
     */
    public function setTitle($title)
    {
        assert('is_string($title); // Invalid argument $title: string expected');
        $this->_title = $title;
        return $this;
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
     * @access  public
     * @return  bool
     */
    public function getSafeMode()
    {
        return $this->_safeMode;
    }

    /**
     * Set safemode setting of method.
     *
     * Allowed values are:
     *  Boolean true or false,
     *  Strings "true", "false", "yes", "no"
     *
     * Any other value will reset the setting to NULL.
     *
     * @access  public
     * @param   bool  $safeMode  true = requires safe-mode, false = disallows safe-mode, null = don't care
     * @return  \Yana\Plugins\Configs\MethodConfiguration
     */
    public function setSafeMode($safeMode = null)
    {
        if (is_string($safeMode)) {
            switch ($safeMode)
            {
                case 'false':
                case 'no':
                    $safeMode = false;
                    break;
                case 'true':
                case 'yes':
                    $safeMode = true;
                    break;
                default:
                    $safeMode = null;
                    break;
            }
        }
        assert('is_null($safeMode) || is_bool($safeMode); // Invalid argument $safeMode: bool expected');
        $this->_safeMode = $safeMode;
        return $this;
    }

    /**
     * Get template path.
     *
     * @access  public
     * @return  string
     */
    public function getTemplate()
    {
        return $this->_template;
    }

    /**
     * Set template path.
     *
     * @access  public
     * @param   string  $template  relative path to template file
     * @return  \Yana\Plugins\Configs\MethodConfiguration
     */
    public function setTemplate($template)
    {
        assert('is_string($template); // Invalid argument $template: string expected');
        $this->_template = strip_tags($template);
        return $this;
    }

    /**
     * Get user security levels.
     *
     * Returns a list of instances of PluginUserLevel.
     *
     * @access  public
     * @return  \Yana\Plugins\UserLevel[]
     */
    public function getUserLevels()
    {
        return $this->_users;
    }

    /**
     * Set user security levels.
     *
     * All elements must be instances of PluginUserLevel.
     *
     * @access  public
     * @param   \Yana\Plugins\UserLevel[]  $users  list of user level definitions
     * @return  \Yana\Plugins\Configs\MethodConfiguration
     */
    public function setUserLevels(array $users)
    {
        $this->_users = array();
        foreach ($users as $user)
        {
            $this->addUserLevel($user);
        }
        return $this;
    }

    /**
     * Add user user level rule.
     *
     * @access  public
     * @param   \Yana\Plugins\UserLevel  $user  user level definition
     * @return  \Yana\Plugins\Configs\MethodConfiguration
     */
    public function addUserLevel(\Yana\Plugins\UserLevel $user)
    {
        $this->_users[] = $user;
        return $this;
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
        return $this->_overwrite;
    }

    /**
     * Set overwrite setting of method.
     *
     * @access  public
     * @param   bool  $overwrite  true = overwrite parent declaration, false = default
     * @return  \Yana\Plugins\Configs\MethodConfiguration
     */
    public function setOverwrite($overwrite)
    {
        assert('is_bool($overwrite); // Invalid argument $overwrite: bool expected');
        $this->_overwrite = (bool) $overwrite;
        return $this;
    }

    /**
     * Get subscribe setting of method.
     *
     * Returns value of subscribe setting.
     *
     * A method may subscribe to an event that it doesn't define itself.
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
        return $this->_subscribe;
    }

    /**
     * Set subscribe setting of method.
     *
     * @access  public
     * @param   bool  $subscribe  true = extend parent, false = implement yourself
     * @return  \Yana\Plugins\Configs\MethodConfiguration
     */
    public function setSubscribe($subscribe)
    {
        assert('is_bool($subscribe); // Invalid argument $subscribe: bool expected');
        $this->_subscribe = (bool) $subscribe;
        return $this;
    }

    /**
     * Get class name.
     *
     * @access  public
     * @return  string
     */
    public function getClassName()
    {
        return $this->_className;
    }

    /**
     * Set class name
     *
     * @access  public
     * @param   string  $className  case-sensitive identifier
     * @return  \Yana\Plugins\Configs\MethodConfiguration
     */
    public function setClassName($className)
    {
        assert('is_string($className); // Invalid argument $className: string expected');
        $this->_className = $className;
        return $this;
    }

    /**
     * Get method name.
     *
     * @access  public
     * @return  string
     */
    public function getMethodName()
    {
        return $this->_methodName;
    }

    /**
     * Set method name.
     *
     * @access  public
     * @param   string  $methodName  case-sensitive text
     * @return  \Yana\Plugins\Configs\MethodConfiguration
     */
    public function setMethodName($methodName)
    {
        assert('is_string($methodName); // Invalid argument $methodName: string expected');
        $this->_methodName = $methodName;
        return $this;
    }

    /**
     * Set event arguments.
     *
     * @access  public
     * @param   array  $args  list of arguments
     * @return  \Yana\Plugins\Configs\MethodConfiguration
     * @throws  Warning       when a provided argument is missing or not valid
     */
    public function setEventArguments(array $args)
    {
        $this->_args = array();

        if ($this->hasGenericParams()) {
            $this->_args = $args;
            return $this;
        }

        $message = "A provided paramter is invalid.";
        $errorLevel = \Yana\Log\TypeEnumeration::ERROR;
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
                            $error = new \Yana\Core\Exceptions\Forms\InvalidValueException($message, $errorLevel);
                            throw $error->setField($name);
                        } else {
                            $this->_args[$name] = (int) $value;
                        }
                    break;
                    case 'float':
                    case 'double':
                        if (!is_numeric($value)) {
                            $error = new \Yana\Core\Exceptions\Forms\InvalidValueException($message, $errorLevel);
                            throw $error->setField($name);
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
                            $error = new \Yana\Core\Exceptions\Forms\InvalidValueException($message, $errorLevel);
                            throw $error->setField($name);
                        } else {
                            $this->_args[$name] = $value;
                        }
                    break;
                    default:
                        if (!is_string($value)) {
                            $error = new \Yana\Core\Exceptions\Forms\InvalidValueException($message, $errorLevel);
                            throw $error->setField($name);
                        } else {
                            $this->_args[$name] = $value;
                        }
                    break;
                }
            } elseif (array_key_exists($i, $this->_defaults)) {
                $this->_args[$name] = $this->_defaults[$i];
            } else {
                // missing parameter
                $message = "A mandatory parameter is missing.";
                $error = new \Yana\Core\Exceptions\Forms\MissingFieldException($message, $errorLevel);
                throw $error->setField($name);
            }
            $i++;
        } // end foreach
        return $this;
    }

    /**
     * Set default values for method params.
     *
     * @access  public
     * @param   array  $defaults  list of default arguments
     * @return  \Yana\Plugins\Configs\MethodConfiguration
     */
    public function setDefaults(array $defaults)
    {
        $this->_defaults = $defaults;
        return $this;
    }

    /**
     * Get default values for method params.
     *
     * @access  public
     * @return  array
     */
    public function getDefaults()
    {
        return $this->_defaults;
    }

    /**
     * Set if the function uses a generic, unchecked parameter list.
     *
     * @access  public
     * @param   bool  $hasGenericParams  true = parameter list is generic, false = parameter list explicitely given
     * @return  \Yana\Plugins\Configs\MethodConfiguration
     */
    public function setHasGenericParams($hasGenericParams)
    {
        assert('is_bool($hasGenericParams); // Invalid argument $hasGenericParams: bool expected');
        $this->_hasGenericParams = (bool) $hasGenericParams;
        return $this;
    }

    /**
     * Check if the function uses a generic, unchecked parameter list.
     *
     * @access  public
     * @return  bool
     */
    public function hasGenericParams()
    {
        return $this->_hasGenericParams;
    }

    /**
     * send event
     *
     * Executes the event on the provided instance and returns the result.
     *
     * @access  public
     * @param   \Yana\IsPlugin  $instance  object to send event to
     * @return  mixed
     */
    public function sendEvent(\Yana\IsPlugin $instance)
    {
        if ($this->hasMethod($instance)) {
            $methodName = $this->_methodName;
            if ($this->hasGenericParams()) {
                return $instance->{$methodName}($this->_args);
            } else {
                return call_user_func_array(array($instance, $methodName), $this->_args);
            }
        } else {
            return $instance->catchAll($this->_methodName, $this->_args);
        }
    }

    /**
     * plug-in has method
     *
     * Returns bool(true) if the given plug-in implements this method and bool(false) otherwise.
     *
     * @access  public
     * @param   \Yana\IsPlugin  $instance  object to send event to
     * @return  bool
     */
    public function hasMethod(\Yana\IsPlugin $instance)
    {
        return method_exists($instance, $this->_methodName);
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