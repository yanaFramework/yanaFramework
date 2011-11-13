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

namespace Yana\Templates;

/**
 * <<decorator>> SmartTemplate
 *
 * This implements a decorator class for the smarty
 * template engine. The use of the "decorator" pattern
 * actually means it "implements an API" on top
 * of the original.
 *
 * Note that this does not touch or even change the
 * engine itself.
 *
 * This class provides two things: most obviously it
 * provides some features, that smarty does not have and
 * in addition it does some more type checking and
 * automates the initialization process.
 *
 * @access      public
 * @package     yana
 * @subpackage  core
 */
class EngineFactory extends \Yana\Core\Object
{

    /**
     * global Smarty instance
     *
     * @var  Smarty
     */
    private static $_instance = null;

    /**
     * @var \SimpleXMLElement
     */
    private $_config = null;

    /**
     *
     * @param \SimpleXMLElement $configuration
     */
    public function __construct(\SimpleXMLElement $configuration)
    {
        $this->_config = $configuration;
    }

    /**
     * @return \SimpleXMLElement
     */
    protected function _getConfiguration()
    {
        return $this->_config;
    }

    /**
     * Registers a plugin.
     *
     * @param  \Smarty            $smarty  instance that should be modified
     * @param  int                $type    either a modifier, function, block
     * @param  \SimpleXMLElement  $plugin  configuration element
     */
    private function _registerPlugin(\Smarty $smarty, $type, \SimpleXMLElement $plugin)
    {
        $className = (string) $plugin;
        $instance = new $className();
        $attributes = $plugin->attributes();
        $smarty->registerPlugin(
            $type,
            (string) $attributes->name,
            array($instance, '__invoke'),
            strtolower((string) $attributes->cacheable) !== 'false'
        );
    }

    /**
     * Registers a filter.
     *
     * @param  \Smarty            $smarty  instance that should be modified
     * @param  int                $type    either pre, post, output or var
     * @param  \SimpleXMLElement  $filter  configuration element
     */
    private function _registerFilter(\Smarty $smarty, $type, \SimpleXMLElement $filter)
    {
        $className = (string) $filter;
        if ($className) {
            $instance = new $className();
            $smarty->registerFilter(
                $type,
                array($instance, '__invoke')
            );
        }
    }

    /**
     * Registers a resource.
     *
     * @param  \Smarty            $smarty  instance that should be modified
     * @param  \SimpleXMLElement  $resource  configuration element
     */
    private function _registerResource(\Smarty $smarty, \SimpleXMLElement $resource)
    {
        $className = (string) $resource;
        $instance = new $className();
        $attributes = $resource->attributes();
        $smarty->registerResource((string) $attributes->name, $instance);
    }

    /**
     * Set up directories, debugging and caching.
     *
     * @param  \Smarty            $smarty  instance that will be configured
     * @param  \SimpleXMLElement  $config  configuration settings
     * @return  EngineFactory
     */
    protected function _configureGeneralSettings(\Smarty $smarty, \SimpleXMLElement $config)
    {
        if ($config->leftdelimiter) {
            $smarty->left_delimiter = (string) $config->leftdelimiter;
        }
        if ($config->rightdelimiter) {
            $smarty->right_delimiter = (string) $config->rightdelimiter;
        }

        /**
         * Set debugging
         */
        $smarty->debugging = strtolower((string) $config->debugging) === 'true';

        /**
         * Directory setup
         */
        foreach ((array) $config->templatedir as $dir)
        {
            $smarty->addTemplateDir($dir);
        }
        unset($dir);

        foreach ((array) $config->configdir as $dir)
        {
            $smarty->addConfigDir($dir);
        }
        unset($dir);

        $smarty->setCompileDir((string) $config->compiledir);
        $smarty->setCacheDir((string) $config->cachedir);

        /**
         * Caching behavior
         */
        $smarty->caching = strtolower((string) $config->caching) === 'true';
        if (isset($config->cachelifetime)) {
            $smarty->cache_lifetime = (int) $config->cachelifetime;
        }
        if ($config->cachingtype) {
            $smarty->caching_type = (string) $config->cachingtype;
        }
        $smarty->use_sub_dirs = strtolower((string) $config->usesubdirs) !== 'false';
        $smarty->compile_check = strtolower((string) $config->compilecheck) !== 'false';

        $smarty->error_reporting = E_ALL & ~E_NOTICE;
        return $this;
    }

    /**
     * Set up filters, modifiers and functions.
     *
     * @param  \Smarty            $smarty  instance that will be configured
     * @param  \SimpleXMLElement  $config  configuration settings
     * @return  EngineFactory
     */
    protected function _configurePlugins(\Smarty $smarty, \SimpleXMLElement $config)
    {
        /**
         * Register plugins
         */
        foreach ($config->modifier as $plugin)
        {
            $this->_registerPlugin($smarty, \Smarty::PLUGIN_MODIFIER, $plugin);
        }
        unset($plugin);

        foreach ((array) $config->defaultmodifier as $plugin)
        {
            $smarty->addDefaultModifiers((string) $plugin);
        }
        unset($plugin);

        foreach ($config->function as $plugin)
        {
            $this->_registerPlugin($smarty, \Smarty::PLUGIN_FUNCTION, $plugin);
        }
        unset($plugin);

        foreach ($config->blockfunction as $plugin)
        {
            $this->_registerPlugin($smarty, \Smarty::PLUGIN_BLOCK, $plugin);
        }
        unset($plugin);

        foreach ($config->prefilter as $filter)
        {
            $this->_registerFilter($smarty, \Smarty::FILTER_PRE, $filter);
        }
        unset($filter);

        foreach ($config->postfilter as $filter)
        {
            $this->_registerFilter($smarty, \Smarty::FILTER_POST, $filter);
        }
        unset($filter);

        foreach ($config->outputfilter as $filter)
        {
            $this->_registerFilter($smarty, \Smarty::FILTER_OUTPUT, $filter);
        }
        unset($filter);

        foreach ($config->varfilter as $filter)
        {
            $this->_registerFilter($smarty, \Smarty::FILTER_VARIABLE, $filter);
        }
        unset($filter);

        foreach ($config->resourcetype as $resource)
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
     * @param  \Smarty_Security   $security  instance that will be configured
     * @param  \SimpleXMLElement  $config    configuration settings
     * @return  EngineFactory
     */
    protected function _configureSecuritySettings(\Smarty_Security $security, \SimpleXMLElement $config)
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
        foreach ($config->securedir as $item)
        {
            $security->secure_dir[] = (string) $item;
        }
        unset($item);

        /**
         * Trusted directories are where you keep php scripts that are executed directly from
         * the templates with {includephp}.
         */
        $security->trusted_dir = array();
        foreach ($config->trusteddir as $item)
        {
            $security->trusted_dir[] = (string) $item;
        }
        unset($item);

        /**
         * Blacklist elements.
         */
        $security->disabled_modifiers = array();
        foreach ($config->disabledmodifier as $item)
        {
            $security->disabled_modifiers[] = (string) $item;
        }
        unset($item);

        $security->disabled_tags = array();
        foreach ($config->disabledtag as $item)
        {
            $security->disabled_tags[] = (string) $item;
        }
        unset($item);

        /**
         * Whitelist elements.
         */
        foreach ($config->allowedtag as $item)
        {
            $security->allowed_tags[] = (string) $item;
        }
        unset($item);

        foreach ($config->allowedmodifier as $item)
        {
            $security->allowed_modifiers[] = (string) $item;
        }
        unset($item);
        if (!empty($config->phpfunction)) {
            $security->php_functions = array();
            foreach ($config->phpfunction as $item)
            {
                $security->php_functions[] = (string) $item;
            }
            unset($item);
        }

        if (!empty($config->phpmodifier)) {
            $security->php_modifiers = array();
            foreach ($config->phpmodifier as $item)
            {
                $security->php_modifiers[] = (string) $item;
            }
            unset($item);
        }

        $security->static_classes = 'none';
        if (!empty($config->staticclass)) {
            $security->static_classes = array();
            foreach ($config->staticclass as $item)
            {
                $security->static_classes[] = (string) $item;
            }
            unset($item);
        }

        if (!empty($config->stream)) {
            $security->streams = array();
            foreach ($config->stream as $item)
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
     * @return  \Smarty
     */
    public function createInstance()
    {
        if (!self::$_instance instanceof \Smarty) {
            $smarty = new \Smarty();
            $config = $this->_getConfiguration();
            $this->_configureGeneralSettings($smarty, $config)
                    ->_configurePlugins($smarty, $config);
            if ($config->security) {
                $smarty->enableSecurity();
                $this->_configureSecuritySettings($smarty->security_policy, $config->security);
            }
            self::$_instance = $smarty;
        }

        return self::$_instance;
    }

}

?>