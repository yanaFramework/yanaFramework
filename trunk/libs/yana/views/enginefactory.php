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
declare(strict_types=1);

namespace Yana\Views;

/**
 * <<factory>> Smarty template engine factory.
 *
 * This implements a configuration loader and factory class for the smarty template engine.
 * Note that this class does not extend or change the engine itself.
 *
 * @package     yana
 * @subpackage  views
 */
class EngineFactory extends \Yana\Core\StdObject
{

    /**
     * global Smarty instance
     *
     * @var  \Yana\Views\Managers\IsManager
     */
    private static $_instance = null;

    /**
     * @var  \Yana\Core\Dependencies\IsViewContainer
     */
    private $_dependencyContainer = null;

    /**
     * <<construct>> Initialize dependencies.
     *
     * @param  \Yana\Core\Dependencies\IsViewContainer  $container  dependency container
     */
    public function __construct(\Yana\Core\Dependencies\IsViewContainer $container)
    {
        $this->_dependencyContainer = $container;
    }

    /**
     * Returns the container.
     *
     * The dependency container contains code to initialize and return dependent objects.
     *
     * @return  \Yana\Core\Dependencies\IsViewContainer
     */
    protected function _getDependencyContainer()
    {
        return $this->_dependencyContainer;
    }

    /**
     * Registers a plugin.
     *
     * @param  \Smarty                  $smarty  instance that should be modified
     * @param  int                      $type    either a modifier, function, block
     * @param  \Yana\Util\Xml\IsObject  $plugin  configuration element
     */
    private function _registerPlugin(\Smarty $smarty, $type, \Yana\Util\Xml\IsObject $plugin)
    {
        $className = $plugin->getPcData();
        $instance = new $className($this->_getDependencyContainer());
        $smarty->registerPlugin(
            $type,
            $plugin->getAttribute("name"),
            array($instance, '__invoke'),
            strtolower($plugin->getAttribute("cacheable")) !== 'false'
        );
    }

    /**
     * Registers a filter.
     *
     * @param  \Smarty                  $smarty  instance that should be modified
     * @param  int                      $type    either pre, post, output or var
     * @param  \Yana\Util\Xml\IsObject  $filter  configuration element
     */
    private function _registerFilter(\Smarty $smarty, $type, \Yana\Util\Xml\IsObject $filter)
    {
        $className = $filter->getPcData();
        if ($className) {
            $instance = new $className($this->_getDependencyContainer());
            $smarty->registerFilter(
                $type,
                array($instance, '__invoke')
            );
        }
    }

    /**
     * Registers a resource.
     *
     * @param  \Smarty                  $smarty  instance that should be modified
     * @param  \Yana\Util\Xml\IsObject  $resource  configuration element
     */
    private function _registerResource(\Smarty $smarty, \Yana\Util\Xml\IsObject $resource)
    {
        $className = $resource->getPcData();
        $instance = new $className($this->_getDependencyContainer());
        $smarty->registerResource((string) $resource->getAttribute("name"), $instance);
    }

    /**
     * Set up directories, debugging and caching.
     *
     * @param  \Smarty                  $smarty  instance that will be configured
     * @param  \Yana\Util\Xml\IsObject  $config  configuration settings
     * @return  EngineFactory
     */
    protected function _configureGeneralSettings(\Smarty $smarty, \Yana\Util\Xml\IsObject $config)
    {
        if (!empty($config->leftdelimiter)) {
            $smarty->left_delimiter = (string) $config->leftdelimiter;
        }
        if (!empty($config->rightdelimiter)) {
            $smarty->right_delimiter = (string) $config->rightdelimiter;
        }

        /**
         * Set debugging
         */
        $smarty->debugging = strtolower((string) $config->debugging) === 'true';

        /**
         * Directory setup
         */
        foreach ($config->getAll("templatedir") as $dir)
        {
            $smarty->addTemplateDir((string) $dir);
        }
        unset($dir);

        foreach ($config->getAll("configdir") as $dir)
        {
            $smarty->addConfigDir((string) $dir);
        }
        unset($dir);

        $smarty->setCompileDir((string) $config->compiledir);
        $smarty->setCacheDir((string) $config->cachedir);

        /**
         * Caching behavior
         */
        $smarty->caching = strtolower((string) $config->caching) === 'true';
        if (isset($config->cachelifetime)) {
            $smarty->cache_lifetime = (int) (string) $config->cachelifetime;
        }
        if (!empty($config->cachingtype)) {
            $smarty->caching_type = (string) $config->cachingtype;
        }
        $smarty->use_sub_dirs = strtolower((string) $config->usesubdirs) !== 'false';
        $smarty->compile_check = strtolower((string) $config->compilecheck) !== 'false';

        $smarty->error_reporting = E_ALL ^ (E_NOTICE | E_DEPRECATED);

        return $this;
    }

    /**
     * Set up filters, modifiers and functions.
     *
     * @param  \Smarty                  $smarty  instance that will be configured
     * @param  \Yana\Util\Xml\IsObject  $config  configuration settings
     * @return  EngineFactory
     */
    protected function _configurePlugins(\Smarty $smarty, \Yana\Util\Xml\IsObject $config)
    {
        /**
         * Register plugins
         */
        foreach ($config->getAll("modifier") as $plugin)
        {
            $this->_registerPlugin($smarty, \Smarty::PLUGIN_MODIFIER, $plugin);
        }
        unset($plugin);

        foreach ($config->getAll("defaultmodifier") as $plugin)
        {
            $smarty->addDefaultModifiers((string) $plugin);
        }
        unset($plugin);

        foreach ($config->getAll("function") as $plugin)
        {
            $this->_registerPlugin($smarty, \Smarty::PLUGIN_FUNCTION, $plugin);
        }
        unset($plugin);

        foreach ($config->getAll("blockfunction") as $plugin)
        {
            $this->_registerPlugin($smarty, \Smarty::PLUGIN_BLOCK, $plugin);
        }
        unset($plugin);

        foreach ($config->getAll("prefilter") as $filter)
        {
            $this->_registerFilter($smarty, \Smarty::FILTER_PRE, $filter);
        }
        unset($filter);

        foreach ($config->getAll("postfilter") as $filter)
        {
            $this->_registerFilter($smarty, \Smarty::FILTER_POST, $filter);
        }
        unset($filter);

        foreach ($config->getAll("outputfilter") as $filter)
        {
            $this->_registerFilter($smarty, \Smarty::FILTER_OUTPUT, $filter);
        }
        unset($filter);

        foreach ($config->getAll("varfilter") as $filter)
        {
            $this->_registerFilter($smarty, \Smarty::FILTER_VARIABLE, $filter);
        }
        unset($filter);

        foreach ($config->getAll("resourcetype") as $resource)
        {
            $this->_registerResource($smarty, $resource);
        }
        unset($resource);

        $smarty->default_resource_type = (string) $config->defaultresourcetype;
        return $this;
    }

    /**
     * Set up filters, modifiers and functions.
     *
     * @param  \Smarty_Security         $security  instance that will be configured
     * @param  \Yana\Util\Xml\IsObject  $config    configuration settings
     * @return  EngineFactory
     */
    protected function _configureSecuritySettings(\Smarty_Security $security, \Yana\Util\Xml\IsObject $config)
    {
        /**
         * Security settings
         */
        switch (strtolower((string) $config->phphandling))
        {
            case 'passthru':
                $phpHandling = \Smarty::PHP_PASSTHRU;
                break;
            case 'quote':
                $phpHandling = \Smarty::PHP_QUOTE;
                break;
            case 'allow':
                $phpHandling = \Smarty::PHP_ALLOW;
                break;
            case 'remove':
            default:
                $phpHandling = \Smarty::PHP_REMOVE;
                break;
        };
        $security->php_handling = $phpHandling;

        /**
         * PHP-constants
         *
         * If set to TRUE, constants via {$smarty.const.FOO} are allowed in
         * the templates.
         */
        $security->allow_constants = strtolower((string) $config->allowconstants) === 'true';
        /**
         * PHP-super globals
         *
         * If set to TRUE, super-globals like $GLOBAL or $_COOKIE are allowed in the templates.
         */
        $security->allow_super_globals = strtolower((string) $config->allowsuperglobals) === 'true';

        /**
         * Template directories that are considered secure.
         */
        $security->secure_dir = array();
        foreach ($config->getAll("securedir") as $item)
        {
            $security->secure_dir[] = (string) $item;
        }
        unset($item);

        /**
         * Trusted directories are where you keep php scripts that are executed directly from
         * the templates with {includephp}.
         */
        $security->trusted_dir = array();
        foreach ($config->getAll("trusteddir") as $item)
        {
            $security->trusted_dir[] = (string) $item;
        }
        unset($item);

        /**
         * Blacklist elements.
         */
        $security->disabled_modifiers = array();
        foreach ($config->getAll("disabledmodifier") as $item)
        {
            $security->disabled_modifiers[] = (string) $item;
        }
        unset($item);

        $security->disabled_tags = array();
        foreach ($config->getAll("disabledtag") as $item)
        {
            $security->disabled_tags[] = (string) $item;
        }
        unset($item);

        /**
         * Whitelist elements.
         */
        foreach ($config->getAll("allowedtag") as $item)
        {
            $security->allowed_tags[] = (string) $item;
        }
        unset($item);

        foreach ($config->getAll("allowedmodifier") as $item)
        {
            $security->allowed_modifiers[] = (string) $item;
        }
        unset($item);
        if (!empty($config->phpfunction)) {
            $security->php_functions = array();
            foreach ($config->getAll("phpfunction") as $item)
            {
                $security->php_functions[] = (string) $item;
            }
            unset($item);
        }

        if (!empty($config->phpmodifier)) {
            $security->php_modifiers = array();
            foreach ($config->getAll("phpmodifier") as $item)
            {
                $security->php_modifiers[] = (string) $item;
            }
            unset($item);
        }

        $security->static_classes = 'none';
        if (!empty($config->staticclass)) {
            $security->static_classes = array();
            foreach ($config->getAll("staticclass") as $item)
            {
                $security->static_classes[] = (string) $item;
            }
            unset($item);
        }

        if (!empty($config->stream)) {
            $security->streams = array();
            foreach ($config->getAll("stream") as $item)
            {
                $security->streams[] = (string) $item;
            }
            unset($item);
        }

        return $this;
    }

    /**
     * Builds a new Smarty instance based on the given configuration.
     *
     * @return  \Yana\Views\Managers\IsManager
     */
    public function createInstance()
    {
        if (!self::$_instance instanceof \Yana\Views\Managers\IsManager) {
            $smarty = new \Smarty();
            self::$_instance = new \Yana\Views\Managers\Manager($smarty, $this->_getDependencyContainer());
            $config = $this->_getDependencyContainer()->getTemplateConfiguration();
            $this->_configureGeneralSettings($smarty, $config);
            $this->_configurePlugins($smarty, $config);
            if ($config->security) {
                $smarty->enableSecurity();
                $this->_configureSecuritySettings($smarty->security_policy, $config->security);
            }
        }

        return self::$_instance;
    }

}

?>