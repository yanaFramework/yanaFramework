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
 * <<builder>> Plugin configuration builder
 *
 * This class produces a configuration from a class reflection.
 *
 * @name        PluginConfiguration
 * @package     yana
 * @subpackage  plugins
 *
 * @ignore
 */
class Builder extends \Yana\Plugins\Configs\AbstractBuilder
{

    /**
     * @var  \Yana\Plugins\Annotations\ReflectionClass
     */
    private $_class = null;

    /**
     * @var  \Yana\Plugins\Annotations\ReflectionMethod
     */
    private $_method = null;

    /**
     * @var  \Yana\Plugins\Annotations\IsParser
     */
    private $_parser = null;

    /**
     * Build class object.
     */
    protected function buildClass()
    {
        if ($this->_class) {
            $this->object->setNamespace($this->_class->getNamespaceName());
            $this->object->setClassName($this->_class->getClassName());
            $parser = $this->getAnnotationParser();
            $parser->setText($this->_class->getPageComment());

            $titles = array();
            $texts = array();
            $this->_getTranslation($this->_class, $titles, $texts);

            $this->object->setDefaultTitle($this->_class->getTitle());
            $this->object->setDefaultText($this->_class->getText());
            $this->object->setTitles($titles);
            $this->object->setTexts($texts);
            $this->object->setDirectory($this->_class->getDirectory());
            $type = $parser->getTag(\Yana\Plugins\Annotations\Enumeration::TYPE, \Yana\Plugins\TypeEnumeration::DEFAULT_SETTING);
            $this->object->setType($type);
            $this->object->setAuthors($parser->getTags(\Yana\Plugins\Annotations\Enumeration::AUTHOR));
            $priorityString = $parser->getTag(\Yana\Plugins\Annotations\Enumeration::PRIORITY);
            $this->object->setPriority(\Yana\Plugins\PriorityEnumeration::fromString($priorityString));
            $this->object->setGroup(mb_strtolower($parser->getTag(\Yana\Plugins\Annotations\Enumeration::GROUP)));
            $this->object->setParent($parser->getTag(\Yana\Plugins\Annotations\Enumeration::PARENT));
            $this->object->setDependencies($parser->getTags(\Yana\Plugins\Annotations\Enumeration::REQUIRES));
            $this->object->setLicense($parser->getTag(\Yana\Plugins\Annotations\Enumeration::LICENSE));
            $this->object->setUrl($parser->getTag(\Yana\Plugins\Annotations\Enumeration::URL));
            $this->object->setVersion($parser->getTag(\Yana\Plugins\Annotations\Enumeration::VERSION));
            $this->object->setLastModified($this->_class->getLastModified());
            $activityString = $parser->getTag(\Yana\Plugins\Annotations\Enumeration::ACTIVE, '0');
            $this->object->setActive(\Yana\Plugins\ActivityEnumeration::getActiveState($activityString));
            assert('!isset($tags); // Cannot redeclare var $tags');
            assert('!isset($tag); // Cannot redeclare var $tag');
            $tags = $parser->getTags(\Yana\Plugins\Annotations\Enumeration::MENU);
            foreach ($tags as $tag) {
                assert('!isset($menu); // Cannot redeclare var $menu');
                $menu = new \Yana\Plugins\Menus\Entry();
                if (isset($tag[\Yana\Plugins\Annotations\Enumeration::GROUP])) {
                    $menu->setGroup($tag[\Yana\Plugins\Annotations\Enumeration::GROUP]);
                }
                if (isset($tag[\Yana\Plugins\Annotations\Enumeration::TITLE])) {
                    $menu->setTitle($tag[\Yana\Plugins\Annotations\Enumeration::TITLE]);
                }
                $this->object->addMenu($menu);
                unset($menu);
            }
            unset($tags, $tag);

            foreach ($this->_class->getMethods(\ReflectionProperty::IS_PUBLIC) as $method)
            {
                $parser->setText($method->getDocComment());
                if (!$parser->getTag(\Yana\Plugins\Annotations\Enumeration::IGNORE)) {
                    $this->_method = $method;
                    $this->buildMethod();
                }
            }
        }
        return $this->object;
    }

    /**
     * Build method object.
     */
    protected function buildMethod()
    {
        $method = new \Yana\Plugins\Configs\MethodConfiguration();
        if ($this->_method) {
            $parser = $this->getAnnotationParser();

            $classPath = $this->_class->getDirectory();
            assert(is_dir($classPath));
            $method->setClassName($this->_method->getClassName());
            $method->setMethodName($this->_method->getName());

            $typeClassTag = $this->object->getType();
            $method->setType(mb_strtolower($parser->getTag(\Yana\Plugins\Annotations\Enumeration::TYPE, $typeClassTag)));
            $method->addPath($classPath);
            $method->setTitle($parser->getTag(\Yana\Plugins\Annotations\Enumeration::TITLE));
            $method->setReturn($parser->getTag(\Yana\Plugins\Annotations\Enumeration::RETURN_VALUE));
            $method->setTemplate(mb_strtolower($parser->getTag(\Yana\Plugins\Annotations\Enumeration::TEMPLATE, 'null')));
            assert('!isset($users); // Cannot redeclare var $users');
            $users = array();
            assert('!isset($item); // Cannot redeclare var $item');
            assert('!isset($tag); // Cannot redeclare var $tag');
            foreach ($parser->getTags(\Yana\Plugins\Annotations\Enumeration::USER, array()) as $tag)
            {
                $user = new \Yana\Plugins\Configs\UserPermissionRule();
                if (isset($tag[\Yana\Plugins\Annotations\Enumeration::GROUP])) {
                    $user->setGroup($tag[\Yana\Plugins\Annotations\Enumeration::GROUP]);
                }
                if (isset($tag[\Yana\Plugins\Annotations\Enumeration::ROLE])) {
                    $user->setRole($tag[\Yana\Plugins\Annotations\Enumeration::ROLE]);
                }
                if (isset($tag[\Yana\Plugins\Annotations\Enumeration::LEVEL])) {
                    $user->setLevel((int) $tag[\Yana\Plugins\Annotations\Enumeration::LEVEL]);
                }
                $users[] = $user;
            }
            unset($item, $tag);
            $method->setUserLevels($users);
            assert('!isset($tag); // Cannot redeclare var $tag');
            $tag = $parser->getTag(\Yana\Plugins\Annotations\Enumeration::MENU);
            if (!empty($tag)) {
                assert('!isset($menu); // Cannot redeclare var $menu');
                $menu = new \Yana\Plugins\Menus\Entry();
                if (isset($tag[\Yana\Plugins\Annotations\Enumeration::GROUP])) {
                    $menu->setGroup($tag[\Yana\Plugins\Annotations\Enumeration::GROUP]);
                }
                if (isset($tag[\Yana\Plugins\Annotations\Enumeration::TITLE])) {
                    $menu->setTitle($tag[\Yana\Plugins\Annotations\Enumeration::TITLE]);
                }
                $method->setMenu($menu);
                unset($menu);
            }
            $tag = $parser->getTag(\Yana\Plugins\Annotations\Enumeration::ONERROR);
            if (!empty($tag)) {
                assert('!isset($event); // Cannot redeclare var $event');
                $event = new \Yana\Plugins\Configs\EventRoute();
                if (isset($tag[\Yana\Plugins\Annotations\Enumeration::GO])) {
                    $event->setTarget($tag[\Yana\Plugins\Annotations\Enumeration::GO]);
                }
                if (isset($tag[\Yana\Plugins\Annotations\Enumeration::TEXT])) {
                    $event->setMessage($tag[\Yana\Plugins\Annotations\Enumeration::TEXT]);
                }
                $method->setOnError($event);
                unset($event);
            }
            $tag = $parser->getTag(\Yana\Plugins\Annotations\Enumeration::ONSUCCESS);
            if (!empty($tag)) {
                assert('!isset($event); // Cannot redeclare var $event');
                $event = new \Yana\Plugins\Configs\EventRoute();
                if (isset($tag[\Yana\Plugins\Annotations\Enumeration::GO])) {
                    $event->setTarget($tag[\Yana\Plugins\Annotations\Enumeration::GO]);
                }
                if (isset($tag[\Yana\Plugins\Annotations\Enumeration::TEXT])) {
                    $event->setMessage($tag[\Yana\Plugins\Annotations\Enumeration::TEXT]);
                }
                $method->setOnSuccess($event);
                unset($event);
            }
            unset($tag);
            $method->setSafeMode($parser->getTag(\Yana\Plugins\Annotations\Enumeration::SAFEMODE));
            if ($this->_class) {
                $method->setGroup($this->object->getGroup());
            }
            $method->setOverwrite((bool) $parser->getTag(\Yana\Plugins\Annotations\Enumeration::OVERWRITE, '0'));
            $method->setSubscribe((bool) $parser->getTag(\Yana\Plugins\Annotations\Enumeration::SUBSCRIBE, '0'));
            $method->setLanguages($parser->getTags(\Yana\Plugins\Annotations\Enumeration::LANGUAGE));
            // process and add scripts
            assert('!isset($scripts); // Cannot redeclare var $scripts');
            $scripts = array();
            assert('!isset($script); // Cannot redeclare var $script');
            foreach ($parser->getTags(\Yana\Plugins\Annotations\Enumeration::SCRIPT, array()) as $script)
            {
                if (!is_string($script)) {
                    $message = 'Syntax error in @script: ' . $this->className . '::' . $this->methodName . '()';
                    $level = \Yana\Log\TypeEnumeration::ERROR;
                    \Yana\Log\LogManager::getLogger()->addLog($message, $level, $param);
                    continue;
                }
                $scripts[] = $classPath . "/" . $script;
            }
            $method->setScripts($scripts);
            unset($scripts, $script);
            // process and add styles
            assert('!isset($styles); // Cannot redeclare var $scripts');
            $styles = array();
            assert('!isset($style); // Cannot redeclare var $style');
            foreach ($parser->getTags(\Yana\Plugins\Annotations\Enumeration::STYLE, array()) as $style)
            {
                if (!is_string($style)) {
                    $message = 'Syntax error in @style: ' .$this->className . '::' . $this->methodName . '()';
                    $level = \Yana\Log\TypeEnumeration::ERROR;
                    \Yana\Log\LogManager::getLogger()->addLog($message, $level, $param);
                    continue;
                }
                $styles[] = $classPath . "/" . $style;
            }
            $method->setStyles($styles);
            unset($styles, $style);
            // process template
            assert('!isset($template); // Cannot redeclare var $template');
            $template = $classPath . "/" . $parser->getTag(\Yana\Plugins\Annotations\Enumeration::TEMPLATE);
            if (is_file($template)) {
                $method->setTemplate($template);
            }
            unset($template);
            // process params
            assert('!isset($params); // Cannot redeclare var $params');
            $params = array();
            assert('!isset($param); // Cannot redeclare var $param');
            assert('!isset($match); // Cannot redeclare var $match');
            assert('!isset($name); // Cannot redeclare var $name');
            assert('!isset($type); // Cannot redeclare var $type');
            foreach ($parser->getTags(\Yana\Plugins\Annotations\Enumeration::PARAM, array()) as $param)
            {
                if (!is_string($param)) {
                    $message = 'Syntax error in @param: ' .$this->className . '::' . $this->methodName . '()';
                    $level = \Yana\Log\TypeEnumeration::ERROR;
                    \Yana\Log\LogManager::getLogger()->addLog($message, $level, $param);
                    continue;
                }
                if (preg_match('/^(\w+)\s+\$(\w+)/', $param, $match)) {
                    $name = $match[2];
                    $type = $match[1];
                    $params[$name] = $type;
                }
            }
            $method->setParams($params);
            $method->setHasGenericParams(isset($params['ARGS']));
            unset($params, $match, $name, $type, $param);
            /* @var $param \ReflectionParameter */
            assert('!isset($defaults); // Cannot redeclare var $defaults');
            $defaults = array();
            assert('!isset($param); // Cannot redeclare var $param');
            foreach($this->_method->getParameters() as $i => $param)
            {
                if ($param->isDefaultValueAvailable()) {
                    $defaults[$i] = $param->getDefaultValue();
                }
            }
            $method->setDefaults($defaults);
            unset($defaults, $param);
            $this->object->addMethod($method);
        }
        return $this->_method;
    }

    /**
     * Build from PHP reflection.
     *
     * @param   \Yana\Plugins\Annotations\ReflectionClass  $pluginClass  base class description
     * @return  \Yana\Plugins\Configs\Builder 
     */
    public function setReflection(\Yana\Plugins\Annotations\ReflectionClass $pluginClass)
    {
        $this->_class = $pluginClass;
        return $this;
    }

    /**
     * Select annotation parser to use.
     *
     * Defaults to {@see PluginAnnotationParser}.
     *
     * @param   \Yana\Plugins\Annotations\IsParser  $parser  used to parse the class for annotations.
     * @return  \Yana\Plugins\Configs\Builder 
     */
    public function setAnnotationParser(\Yana\Plugins\Annotations\IsParser $parser)
    {
        $this->_parser = $parser;
        return $this;
    }

    /**
     * Get annotation parser to use.
     *
     * Defaults to {@see \Yana\Plugins\Annotations\Parser}.
     *
     * @return  \Yana\Plugins\Annotations\IsParser 
     */
    protected function getAnnotationParser()
    {
        if (!isset($this->_parser)) {
            $this->_parser = new \Yana\Plugins\Annotations\Parser();
        }
        return $this->_parser;
    }

    /**
     * get title and text from translation tag
     *
     * @param  \Yana\Plugins\Annotations\ReflectionClass  $pluginClass  plugin configuration class
     * @param  string                                     &$title       output title var
     * @param  string                                     &$text        output text var
     */
    private function _getTranslation(\Yana\Plugins\Annotations\ReflectionClass $pluginClass, array &$title, array &$text)
    {
        $parser = $this->getAnnotationParser();
        $parser->setText($pluginClass->getPageComment());
        $translation = $parser->getTag(\Yana\Plugins\Annotations\Enumeration::TRANSLATION);
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

}

?>