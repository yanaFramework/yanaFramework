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
 * @name        PluginConfigurationMethod
 * @package     yana
 * @subpackage  plugins
 *
 * @ignore
 */
class MethodConfiguration extends \Yana\Core\Object implements \Yana\Plugins\Configs\IsMethodConfiguration, \Yana\Report\IsReportable
{

    /**
     * @var  string
     */
    private $_className = "";

    /**
     * @var  string
     */
    private $_methodName = "";

    /**
     * @var  array
     */
    private $_args = array();

    /**
     * @var  array
     */
    private $_params = array();

    /**
     * @var  string
     */
    private $_return = "";

    /**
     * @var  array
     */
    private $_defaults = array();

    /**
     * @var  bool
     */
    private $_hasGenericParams = false;

    /**
     * @var  array
     */
    private $_paths = array();

    /**
     * Method title.
     *
     * @var  string
     */
    private $_title = "";

    /**
     * Plugin method type.
     *
     * @var  string
     */
    private $_type = \Yana\Plugins\TypeEnumeration::DEFAULT_SETTING;

    /**
     * Template identifier.
     *
     * @var  string
     */
    private $_template = "";

    /**
     * User settings.
     *
     * @var  string
     */
    private $_users = array();

    /**
     * @var  bool
     */
    private $_safeMode = null;

    /**
     * @var  \Yana\Plugins\Menus\IsEntry
     */
    private $_menu = null;

    /**
     * @var  \Yana\Plugins\Configs\EventRoute
     */
    private $_onError = null;

    /**
     * @var  \Yana\Plugins\Configs\EventRoute
     */
    private $_onSuccess = null;

    /**
     * @var  string
     */
    private $_group = "";

    /**
     * @var  bool
     */
    private $_overwrite = false;

    /**
     * @var  bool
     */
    private $_subscribe = false;

    /**
     * @var  array
     */
    private $_languages = array();

    /**
     * @var  array
     */
    private $_scripts = array();

    /**
     * @var  array
     */
    private $_styles = array();

    /**
     * Get method type.
     *
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
     * @param   string  $type  valid method type
     * @return  self
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
     * @param   string  $path  absolute path to plugin class file
     * @return  self
     */
    public function addPath($path)
    {
        assert('is_string($path); // Invalid argument $path: string expected');
        $this->_paths[] = $path;
        return $this;
    }

    /**
     * Get directory.
     *
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
     * @return  array
     */
    public function getPaths()
    {
        return $this->_paths;
    }

    /**
     * Add Configuration.
     *
     * @param   \Yana\Plugins\Configs\IsMethodConfiguration $subscriberConfig  configuration of subscribing method
     * @return  self
     * @ignore
     */
    public function addSubscription(\Yana\Plugins\Configs\IsMethodConfiguration $subscriberConfig)
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
     * @return  array
     */
    public function getScripts()
    {
        return $this->_scripts;
    }

    /**
     * Set Javascript files.
     *
     * @param   array  $scripts  list of paths to javascript files.
     * @return  self
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
     * @return  array
     */
    public function getStyles()
    {
        return $this->_styles;
    }

    /**
     * Set CSS styles.
     *
     * @param   array  $styles  list of paths to CSS files.
     * @return  self
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
     * @return  array
     */
    public function getLanguages()
    {
        return $this->_languages;
    }

    /**
     * Set language files.
     *
     * @param   array  $languages  list of names of XLIFF files.
     * @return  self
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
     * @param   array  $params  keys are the param-names and the values are the param-types
     * @return  self
     */
    public function setParams(array $params)
    {
        $this->_params = $params;
        return $this;
    }

    /**
     * Add method parameter.
     * 
     * @param   string  $name     identifier
     * @param   string  $type     data type (string, int, bool, array, float)
     * @param   mixed   $default  value
     * @return  self
     */
    public function addParam($name, $type, $default = null)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        assert('is_string($type); // Invalid argument $type: string expected');
        $this->_params[(string) $name] = (string) $type;
        $this->_defaults[] = $default;
        return $this;
    }

    /**
     * Get return value.
     *
     * Returns the methods return value.
     *
     * @return  string
     */
    public function getReturn()
    {
        return $this->_return;
    }

    /**
     * Set return value.
     *
     * @param   string  $return  valid PHP type - or empty string, if the function doesn't return a value
     * @return  self
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
     * @param   string  $group  unique name
     * @return  self
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
     * @return  \Yana\Plugins\Menus\IsEntry
     */
    public function getMenu()
    {
        return $this->_menu;
    }

    /**
     * Set menu entry.
     *
     * @param   \Yana\Plugins\Menus\IsEntry  $menu  menu configuration
     * @return  self
     */
    public function setMenu(\Yana\Plugins\Menus\IsEntry $menu)
    {
        $this->_menu = $menu;
        return $this;
    }

    /**
     * Get settings on how to react on success.
     *
     * @return  \Yana\Plugins\Configs\EventRoute
     */
    public function getOnSuccess()
    {
        return $this->_onSuccess;
    }

    /**
     * Set settings on how to react on success.
     *
     * @param   \Yana\Plugins\Configs\EventRoute  $onSuccess  event configuration
     * @return  self
     */
    public function setOnSuccess(\Yana\Plugins\Configs\EventRoute $onSuccess)
    {
        $onSuccess->setCode(\Yana\Plugins\Configs\ReturnCodeEnumeration::SUCCESS);
        $this->_onSuccess = $onSuccess;
        return $this;
    }

    /**
     * Get settings on how to react on error.
     *
     * @return  \Yana\Plugins\Configs\EventRoute
     */
    public function getOnError()
    {
        return $this->_onError;
    }

    /**
     * Set settings on how to react on error.
     *
     * @param   \Yana\Plugins\Configs\EventRoute  $onError  event configuration
     * @return  self
     */
    public function setOnError(\Yana\Plugins\Configs\EventRoute $onError)
    {
        $onError->setCode(\Yana\Plugins\Configs\ReturnCodeEnumeration::ERROR);
        $this->_onError = $onError;
        return $this;
    }

    /**
     * Get human readable name.
     *
     * Returns the name (title) as defined in the method's doc block.
     *
     * @return  string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Set title.
     *
     * @param   string  $title  human readable name
     * @return  self
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
     * @param   bool  $safeMode  true = requires safe-mode, false = disallows safe-mode, null = don't care
     * @return  self
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
     * @return  string
     */
    public function getTemplate()
    {
        return $this->_template;
    }

    /**
     * Set template path.
     *
     * @param   string  $template  relative path to template file
     * @return  self
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
     * @return  \Yana\Plugins\Configs\IsUserPermissionRule[]
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
     * @param   \Yana\Plugins\Configs\IsUserPermissionRule[]  $users  list of user level definitions
     * @return  self
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
     * @param   \Yana\Plugins\Configs\IsUserPermissionRule  $user  user level definition
     * @return  self
     */
    public function addUserLevel(\Yana\Plugins\Configs\IsUserPermissionRule $user)
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
     * @return  bool
     */
    public function getOverwrite()
    {
        return $this->_overwrite;
    }

    /**
     * Set overwrite setting of method.
     *
     * @param   bool  $overwrite  true = overwrite parent declaration, false = default
     * @return  self
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
     * @return  bool
     */
    public function getSubscribe()
    {
        return $this->_subscribe;
    }

    /**
     * Set subscribe setting of method.
     *
     * @param   bool  $subscribe  true = extend parent, false = implement yourself
     * @return  self
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
     * @return  string
     */
    public function getClassName()
    {
        return $this->_className;
    }

    /**
     * Set class name
     *
     * @param   string  $className  case-sensitive identifier
     * @return  self
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
     * @return  string
     */
    public function getMethodName()
    {
        return $this->_methodName;
    }

    /**
     * Set method name.
     *
     * @param   string  $methodName  case-sensitive text
     * @return  self
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
     * Returns arguments with added default values.
     *
     * @param   array  $args  list of arguments
     * @return  array
     * @throws  \Yana\Core\Exceptions\Forms\MissingFieldException  when a provided argument is missing or not valid
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when a provided argument is missing or not valid
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
        return $this->_args;
    }

    /**
     * Set default values for method params.
     *
     * @param   array  $defaults  list of default arguments
     * @return  self
     */
    public function setDefaults(array $defaults)
    {
        $this->_defaults = $defaults;
        return $this;
    }

    /**
     * Get default values for method params.
     *
     * @return  array
     */
    public function getDefaults()
    {
        return $this->_defaults;
    }

    /**
     * Set if the function uses a generic, unchecked parameter list.
     *
     * @param   bool  $hasGenericParams  true = parameter list is generic, false = parameter list explicitely given
     * @return  self
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
     * @return  bool
     */
    public function hasGenericParams()
    {
        return $this->_hasGenericParams;
    }

    /**
     * Executes the event on the provided instance and returns the result.
     *
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
     * Plug-in has method?
     *
     * Returns bool(true) if the given plug-in implements this method and bool(false) otherwise.
     *
     * @param   \Yana\IsPlugin  $instance  object to send event to
     * @return  bool
     */
    public function hasMethod(\Yana\IsPlugin $instance)
    {
        return method_exists($instance, $this->_methodName);
    }

    /**
     * Reinitialize instance.
     */
    public function __wakeup()
    {
        $this->_args = array();
    }

    /**
     * Returns a xml-report object, which you may print, transform or output to a file.
     *
     * @param   \Yana\Report\IsReport  $report  base report
     * @return  \Yana\Report\IsReport
     */
    public function getReport(\Yana\Report\IsReport $report = null)
    {
        if (is_null($report)) {
            $report = \Yana\Report\Xml::createReport(__CLASS__);
        }

        /**
         * check for type attribute
         */
        assert('!isset($type); // Cannot redeclare var $type');
        $type = $this->getType();
        if (empty($type)) {
            $report->addWarning("The mandatory attribute 'type' is missing.");
        } else {
            $report->addText('Type: ' . $type);
        }
        unset($type);

        /**
         * check if template file exists
         */
        assert('!isset($template); // Cannot redeclare var $template');
        $template = $this->getTemplate();
        assert('is_string($template); // Unexpected value: $template. String expected');
        $tplMessage = strcasecmp($template, "message");
        if (!empty($template) && strcasecmp($template, "null") !== 0 && $tplMessage !== 0) {

            $filename = $template;
            assert('!isset($filename); // Cannot redeclare var $filename');
            try {

                assert('!isset($builder); // Cannot redeclare var $builder');
                assert('!isset($skin); // Cannot redeclare var $skin');
                $builder = new \Yana\ApplicationBuilder();
                $skin = $builder->buildApplication()->getSkin();
                $filename = file_exists($filename) ? $filename : $skin->getTemplateData($template)->getFile();
            } catch (\Yana\Core\Exceptions\NotFoundException $e) {
                $report->addError("The definition of template '" . $template . "' contains errors: " .
                    $e->getMessage());
            }

            if (!file_exists($filename)) {
                $report->addError("The chosen template '" . $template . "' is not available. " .
                    "Please check if reference and filename for this template are correct and " .
                    "all files have been installed correctly.");
            } else {
                $report->addText("Template: {$filename}");
            }
            unset($filename);
        }
        unset($template);

        return $report;
    }

}

?>