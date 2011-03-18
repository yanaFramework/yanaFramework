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
 * <<builder>> Plugin configuration builder
 *
 * This class produces a configuration from a class reflection.
 *
 * @access      public
 * @name        PluginConfiguration
 * @package     yana
 * @subpackage  core
 *
 * @ignore
 */
class PluginConfigurationBuilder extends PluginConfigurationAbstractBuilder
{

    /**
     * @access  private
     * @var     PluginReflectionClass
     */
    private $_class = null;

    /**
     * @access  private
     * @var     PluginReflectionMethod
     */
    private $_method = null;

    /**
     * Build class object.
     *
     * @access protected
     */
    protected function buildClass()
    {
        if ($this->_class) {
            $this->object->setClassName($this->_class->getClassName());

            $titles = array();
            $texts = array();
            $this->_getTranslation($this->_class, $titles, $texts);

            $this->object->setDefaultTitle($this->_class->getTitle());
            $this->object->setDefaultText($this->_class->getText());
            $this->object->setTitles($titles);
            $this->object->setTexts($texts);
            $this->object->setDirectory($this->_class->getDirectory());
            $this->object->setType(mb_strtolower($this->_class->getTag(PluginAnnotationEnumeration::TYPE, 'default')));
            $this->object->setAuthors($this->_class->getTags(PluginAnnotationEnumeration::AUTHOR));
            $priorityString = $this->_class->getTag(PluginAnnotationEnumeration::PRIORITY);
            $this->object->setPriority(PluginPriorityEnumeration::fromString($priorityString));
            $this->object->setGroup(mb_strtolower($this->_class->getTag(PluginAnnotationEnumeration::GROUP)));
            $this->object->setParent($this->_class->getTag(PluginAnnotationEnumeration::PARENT));
            $this->object->setDependencies($this->_class->getTags(PluginAnnotationEnumeration::REQUIRES));
            $this->object->setLicense($this->_class->getTag(PluginAnnotationEnumeration::LICENSE));
            $this->object->setUrl($this->_class->getTag(PluginAnnotationEnumeration::URL));
            $this->object->setVersion($this->_class->getTag(PluginAnnotationEnumeration::VERSION));
            $this->object->setLastModified($this->_class->getLastModified());
            $activityString = $this->_class->getTag(PluginAnnotationEnumeration::ACTIVE, '0');
            $this->object->setActive(PluginActivityEnumeration::getActiveState($activityString));
            assert('!isset($tags); // Cannot redeclare var $tags');
            assert('!isset($tag); // Cannot redeclare var $tag');
            $tags = $this->_class->getTags(PluginAnnotationEnumeration::MENU);
            foreach ($tags as $tag) {
                assert('!isset($menu); // Cannot redeclare var $menu');
                $menu = new PluginMenuEntry();
                if (isset($tag[PluginAnnotationEnumeration::GROUP])) {
                    $menu->setGroup($tag[PluginAnnotationEnumeration::GROUP]);
                }
                if (isset($tag[PluginAnnotationEnumeration::TITLE])) {
                    $menu->setTitle($tag[PluginAnnotationEnumeration::TITLE]);
                }
                $this->object->addMenu($menu);
                unset($menu);
            }
            unset($tags, $tag);

            foreach ($this->_class->getMethods(ReflectionProperty::IS_PUBLIC) as $method)
            {
                if (!$method->getTag(PluginAnnotationEnumeration::IGNORE)) {
                    $this->_method = $method;
                    $this->buildMethod();
                }
            }
        }
        return $this->object;
    }

    /**
     * Build method object.
     *
     * @access protected
     */
    protected function buildMethod()
    {
        $method = new PluginConfigurationMethod();
        if ($this->_method) {
            $classPath = $this->_class->getDirectory();
            assert(is_dir($classPath));
            $method->setClassName($this->_method->getClassName());
            $method->setMethodName($this->_method->getName());

            $typeClassTag = $this->object->getType();
            $method->setType(mb_strtolower($this->_method->getTag(PluginAnnotationEnumeration::TYPE, $typeClassTag)));
            $method->addPath($classPath);
            $method->setTitle($this->_method->getTag(PluginAnnotationEnumeration::TITLE));
            $method->setReturn($this->_method->getTag(PluginAnnotationEnumeration::RETURN_VALUE));
            $method->setTemplate(mb_strtolower($this->_method->getTag(PluginAnnotationEnumeration::TEMPLATE, 'null')));
            assert('!isset($users); // Cannot redeclare var $users');
            $users = array();
            assert('!isset($item); // Cannot redeclare var $item');
            assert('!isset($tag); // Cannot redeclare var $tag');
            foreach ($this->_method->getTags(PluginAnnotationEnumeration::USER, array()) as $tag)
            {
                $user = new PluginUserLevel();
                if (isset($tag[PluginAnnotationEnumeration::GROUP])) {
                    $user->setGroup($tag[PluginAnnotationEnumeration::GROUP]);
                }
                if (isset($tag[PluginAnnotationEnumeration::ROLE])) {
                    $user->setRole($tag[PluginAnnotationEnumeration::ROLE]);
                }
                if (isset($tag[PluginAnnotationEnumeration::LEVEL])) {
                    $user->setLevel((int) $tag[PluginAnnotationEnumeration::LEVEL]);
                }
            }
            unset($item, $tag);
            $method->setUserLevels($users);
            assert('!isset($tag); // Cannot redeclare var $tag');
            $tag = $this->_method->getTag(PluginAnnotationEnumeration::MENU);
            if (!empty($tag)) {
                assert('!isset($menu); // Cannot redeclare var $menu');
                $menu = new PluginMenuEntry();
                if (isset($tag[PluginAnnotationEnumeration::GROUP])) {
                    $menu->setGroup($tag[PluginAnnotationEnumeration::GROUP]);
                }
                if (isset($tag[PluginAnnotationEnumeration::TITLE])) {
                    $menu->setTitle($tag[PluginAnnotationEnumeration::TITLE]);
                }
                $method->setMenu($menu);
                unset($menu);
            }
            $tag = $this->_method->getTag(PluginAnnotationEnumeration::ONERROR);
            if (!empty($tag)) {
                assert('!isset($event); // Cannot redeclare var $event');
                $event = new PluginEventRoute();
                if (isset($tag[PluginAnnotationEnumeration::GO])) {
                    $event->setTarget($tag[PluginAnnotationEnumeration::GO]);
                }
                if (isset($tag[PluginAnnotationEnumeration::TEXT])) {
                    $event->setMessage($tag[PluginAnnotationEnumeration::TEXT]);
                }
                $method->setOnError($event);
                unset($event);
            }
            $tag = $this->_method->getTag(PluginAnnotationEnumeration::ONSUCCESS);
            if (!empty($tag)) {
                assert('!isset($event); // Cannot redeclare var $event');
                $event = new PluginEventRoute();
                if (isset($tag[PluginAnnotationEnumeration::GO])) {
                    $event->setTarget($tag[PluginAnnotationEnumeration::GO]);
                }
                if (isset($tag[PluginAnnotationEnumeration::TEXT])) {
                    $event->setMessage($tag[PluginAnnotationEnumeration::TEXT]);
                }
                $method->setOnSuccess($event);
                unset($event);
            }
            unset($tag);
            $method->setSafeMode($this->_method->getTag(PluginAnnotationEnumeration::SAFEMODE));
            if ($this->_class) {
                $method->setGroup(mb_strtolower($this->_class->getTag(PluginAnnotationEnumeration::GROUP)));
            }
            $method->setOverwrite((bool) $this->_method->getTag(PluginAnnotationEnumeration::OVERWRITE, '0'));
            $method->setSubscribe((bool) $this->_method->getTag(PluginAnnotationEnumeration::SUBSCRIBE, '0'));
            $method->setLanguages($this->_method->getTags(PluginAnnotationEnumeration::LANGUAGE));
            // process and add scripts
            assert('!isset($scripts); // Cannot redeclare var $scripts');
            $scripts = array();
            assert('!isset($script); // Cannot redeclare var $script');
            foreach ($this->_method->getTags(PluginAnnotationEnumeration::SCRIPT, array()) as $script)
            {
                if (!is_string($script)) {
                    $message = 'Syntax error in @script: ' . $this->className . '::' . $this->methodName . '()';
                    Log::report($message, E_USER_ERROR, $param);
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
            foreach ($this->_method->getTags(PluginAnnotationEnumeration::STYLE, array()) as $style)
            {
                if (!is_string($style)) {
                    $message = 'Syntax error in @style: ' .$this->className . '::' . $this->methodName . '()';
                    Log::report($message, E_USER_ERROR, $param);
                    continue;
                }
                $styles[] = $classPath . "/" . $style;
            }
            $method->setStyles($styles);
            unset($styles, $style);
            // process template
            assert('!isset($template); // Cannot redeclare var $template');
            $template = $classPath . "/" . $this->_method->getTag(PluginAnnotationEnumeration::TEMPLATE);
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
            foreach ($this->_method->getTags(PluginAnnotationEnumeration::PARAM, array()) as $param)
            {
                if (!is_string($param)) {
                    $message = 'Syntax error in @param: ' .$this->className . '::' . $this->methodName . '()';
                    Log::report($message, E_USER_ERROR, $param);
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
            /* @var $param ReflectionParameter */
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
     * @access public
     * @param  PluginReflectionClass  $pluginClass  base class description
     */
    public function setReflection(PluginReflectionClass $pluginClass)
    {
        $this->_class = $pluginClass;
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
        $translation = $pluginClass->getTag(PluginAnnotationEnumeration::TRANSLATION);
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