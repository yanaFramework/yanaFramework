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
     * @access  private
     * @var     IsAnnotationParser
     */
    private $_parser = null;

    /**
     * Build class object.
     *
     * @access protected
     */
    protected function buildClass()
    {
        if ($this->_class) {
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
            $type = $parser->getTag(PluginAnnotationEnumeration::TYPE, PluginTypeEnumeration::DEFAULT_SETTING);
            $this->object->setType($type);
            $this->object->setAuthors($parser->getTags(PluginAnnotationEnumeration::AUTHOR));
            $priorityString = $parser->getTag(PluginAnnotationEnumeration::PRIORITY);
            $this->object->setPriority(PluginPriorityEnumeration::fromString($priorityString));
            $this->object->setGroup(mb_strtolower($parser->getTag(PluginAnnotationEnumeration::GROUP)));
            $this->object->setParent($parser->getTag(PluginAnnotationEnumeration::PARENT));
            $this->object->setDependencies($parser->getTags(PluginAnnotationEnumeration::REQUIRES));
            $this->object->setLicense($parser->getTag(PluginAnnotationEnumeration::LICENSE));
            $this->object->setUrl($parser->getTag(PluginAnnotationEnumeration::URL));
            $this->object->setVersion($parser->getTag(PluginAnnotationEnumeration::VERSION));
            $this->object->setLastModified($this->_class->getLastModified());
            $activityString = $parser->getTag(PluginAnnotationEnumeration::ACTIVE, '0');
            $this->object->setActive(PluginActivityEnumeration::getActiveState($activityString));
            assert('!isset($tags); // Cannot redeclare var $tags');
            assert('!isset($tag); // Cannot redeclare var $tag');
            $tags = $parser->getTags(PluginAnnotationEnumeration::MENU);
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
                $parser->setText($method->getDocComment());
                if (!$parser->getTag(PluginAnnotationEnumeration::IGNORE)) {
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
            $parser = $this->getAnnotationParser();

            $classPath = $this->_class->getDirectory();
            assert(is_dir($classPath));
            $method->setClassName($this->_method->getClassName());
            $method->setMethodName($this->_method->getName());

            $typeClassTag = $this->object->getType();
            $method->setType(mb_strtolower($parser->getTag(PluginAnnotationEnumeration::TYPE, $typeClassTag)));
            $method->addPath($classPath);
            $method->setTitle($parser->getTag(PluginAnnotationEnumeration::TITLE));
            $method->setReturn($parser->getTag(PluginAnnotationEnumeration::RETURN_VALUE));
            $method->setTemplate(mb_strtolower($parser->getTag(PluginAnnotationEnumeration::TEMPLATE, 'null')));
            assert('!isset($users); // Cannot redeclare var $users');
            $users = array();
            assert('!isset($item); // Cannot redeclare var $item');
            assert('!isset($tag); // Cannot redeclare var $tag');
            foreach ($parser->getTags(PluginAnnotationEnumeration::USER, array()) as $tag)
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
                $users[] = $user;
            }
            unset($item, $tag);
            $method->setUserLevels($users);
            assert('!isset($tag); // Cannot redeclare var $tag');
            $tag = $parser->getTag(PluginAnnotationEnumeration::MENU);
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
            $tag = $parser->getTag(PluginAnnotationEnumeration::ONERROR);
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
            $tag = $parser->getTag(PluginAnnotationEnumeration::ONSUCCESS);
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
            $method->setSafeMode($parser->getTag(PluginAnnotationEnumeration::SAFEMODE));
            if ($this->_class) {
                $method->setGroup($this->object->getGroup());
            }
            $method->setOverwrite((bool) $parser->getTag(PluginAnnotationEnumeration::OVERWRITE, '0'));
            $method->setSubscribe((bool) $parser->getTag(PluginAnnotationEnumeration::SUBSCRIBE, '0'));
            $method->setLanguages($parser->getTags(PluginAnnotationEnumeration::LANGUAGE));
            // process and add scripts
            assert('!isset($scripts); // Cannot redeclare var $scripts');
            $scripts = array();
            assert('!isset($script); // Cannot redeclare var $script');
            foreach ($parser->getTags(PluginAnnotationEnumeration::SCRIPT, array()) as $script)
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
            foreach ($parser->getTags(PluginAnnotationEnumeration::STYLE, array()) as $style)
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
            $template = $classPath . "/" . $parser->getTag(PluginAnnotationEnumeration::TEMPLATE);
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
            foreach ($parser->getTags(PluginAnnotationEnumeration::PARAM, array()) as $param)
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
     * @access  public
     * @param   PluginReflectionClass  $pluginClass  base class description
     * @return  PluginConfigurationBuilder 
     */
    public function setReflection(PluginReflectionClass $pluginClass)
    {
        $this->_class = $pluginClass;
        return $this;
    }

    /**
     * Select annotation parser to use.
     *
     * Defaults to {@see PluginAnnotationParser}.
     *
     * @access  public
     * @param   IsAnnotationParser  $parser  used to parse the class for annotations.
     * @return  PluginConfigurationBuilder 
     */
    public function setAnnotationParser(IsAnnotationParser $parser)
    {
        $this->_parser = $parser;
        return $this;
    }

    /**
     * Get annotation parser to use.
     *
     * Defaults to {@see PluginAnnotationParser}.
     *
     * @access  protected
     * @return  IsAnnotationParser 
     */
    protected function getAnnotationParser()
    {
        if (!isset($this->_parser)) {
            $this->_parser = new PluginAnnotationParser();
        }
        return $this->_parser;
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
        $parser = $this->getAnnotationParser();
        $parser->setText($pluginClass->getPageComment());
        $translation = $parser->getTag(PluginAnnotationEnumeration::TRANSLATION);
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