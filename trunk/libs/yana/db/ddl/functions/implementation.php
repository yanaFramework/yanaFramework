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

namespace Yana\Db\Ddl\Functions;

/**
 * database function implementation definition
 *
 * This wrapper class represents the structure of a database
 *
 * Note that the implementation is DBMS and language specific.
 *
 * @package     yana
 * @subpackage  db
 */
class Implementation extends \Yana\Db\Ddl\DDL
{

    /**
     * tag name for persistance mapping: object <-> XDDL
     *
     * @var  string
     * @ignore
     */
    protected $xddlTag = "implementation";

    /**
     * attributes for persistance mapping: object <-> XDDL
     *
     * @var  array
     * @ignore
     */
    protected $xddlAttributes = array(
        'dbms'     => array('dbms',     'string'),
        'language' => array('language', 'string')
    );

    /**
     * tags for persistance mapping: object <-> XDDL
     *
     * @var  array
     * @ignore
     */
    protected $xddlTags = array(
        'param'  => array('parameters', 'array', 'Yana\Db\Ddl\Functions\Parameter'),
        'return' => array('return',     'string'),
        'code'   => array('code',       'string')
    );

    /**
     * @var string
     * @ignore
     */
    protected $dbms = \Yana\Db\DriverEnumeration::GENERIC;

    /**
     * @var string
     * @ignore
     */
    protected $return = null;

    /**
     * @var string
     * @ignore
     */
     protected $language = null;

    /**
     * @var string
     * @ignore
     */
     protected $code = null;

    /**
     * @var \Yana\Db\Ddl\Functions\Parameter[]
     * @ignore
     */
    protected $parameters = array();

    /**
     * <<magic>> Get function parameter, with the given name.
     *
     * Alias of {@see \Yana\Db\Ddl\Functions\Implementation::getParameter()}.
     *
     * @param   string  $name  parameter name
     * @return  \Yana\Db\Ddl\Functions\Parameter
     */
    public function __get($name)
    {
        assert(is_string($name), 'Invalid argument $name: string expected');
        return $this->getParameter($name);
    }

    /**
     * Get DBMS.
     *
     * Returns the name of the target DBMS for this definition as a lower-cased string.
     * The default is "generic".
     *
     * @return  string
     */
    public function getDBMS(): string
    {
        return $this->dbms;
    }

    /**
     * Create new instance.
     *
     * While you may settle for any target DBMS you want and provide it in any kind of writing you
     * choose, you should remind, that not every DBMS is supported by the database API provided
     * here.
     *
     * The special "generic" DBMS-value means that the constraint is suitable for any DBMS.
     * Usually this is used as a fall-back option for DBMS you haven't thought of when creating the
     * database structure or for those that simply doesn't have the feature in question.
     *
     * Generic values are usually simulated using PHP-code.
     *
     * @param   string  $dbms   target DBMS, defaults to "generic"
     */
    public function __construct(string $dbms =\Yana\Db\DriverEnumeration::GENERIC)
    {
        $this->dbms = strtolower($dbms);
    }

    /**
     * Get returned data-type.
     *
     * The data-type returned by the function.
     * Will return NULL if the returned type is void.
     *
     * @return  string|NULL
     */
    public function getReturn(): ?string
    {
        return $this->return;
    }

    /**
     * Set returned data-type.
     *
     * The data-type the function should return.
     *
     * To reset this option, call the function with an empty parameter
     * If you provide no or an empty type, the function return type will be set
     * to 'void'.
     *
     * @param   string  $type  valid data-type in the selected programming-language
     * @return  $this
     */
    public function setReturn(string $type = "")
    {
        if (empty($type)) {
            $this->return = null;
        } else {
            $this->return = $type;
        }
        return $this;
    }

    /**
     * Get parameter.
     *
     * Returns the parameter definition with the name $name as an instance of
     * \Yana\Db\Ddl\Functions\Parameter. If no parameter with the given name exists, the
     * function returns NULL instead.
     *
     * @param   string  $name   parameter name
     * @return  \Yana\Db\Ddl\Functions\Parameter|NULL
     */
    public function getParameter(string $name): ?\Yana\Db\Ddl\Functions\Parameter
    {
        $tableName = mb_strtolower($name);
        if (isset($this->parameters[$tableName])) {
            return $this->parameters[$tableName];
        } else {
            return null;
        }
    }

    /**
     * Get parameter list.
     *
     * Returns a list of all function parameters as instances of
     * \Yana\Db\Ddl\Functions\Parameter.
     * If no parameters are defined, the list is empty.
     *
     * Important note! You can NOT add a new parameter by adding a new item to
     * the list. Use the function {see \Yana\Db\Ddl\Ddl::addParameter} instead.
     *
     * @return  array
     */
    public function getParameters(): array
    {
        assert(is_array($this->parameters), 'member "parameters" is expected to be an array');
        return $this->parameters;
    }

    /**
     * List all parameters by name.
     *
     * Returns a numeric array with the names of all registered parameters.
     *
     * @return  array
     */
    public function getParameterNames(): array
    {
        assert(is_array($this->parameters), 'member "parameters" is expected to be an array');
        return array_keys($this->parameters);
    }

    /**
     * Add parameter to function.
     *
     * Adds a new parameter item and returns the definition as an instance of
     * \Yana\Db\Ddl\Functions\Parameter.
     *
     * If another parameter with the same name already exists, it throws an
     * AlreadyExistsException.
     * The name must start with a letter and may only contain: a-z, 0-9, '-' and '_'.
     *
     * @param   string  $name   name of a new parameter
     * @return  \Yana\Db\Ddl\Functions\Parameter
     * @throws  \Yana\Core\Exceptions\AlreadyExistsException    when a parameter with the same name already exists
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  on invalid name
     */
    public function addParameter(string $name): \Yana\Db\Ddl\Functions\Parameter
    {
        $name = mb_strtolower($name);
        if (isset($this->parameters[$name])) {
            $message = "Another parameter with the name '$name' is already defined.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            $exception = new \Yana\Core\Exceptions\AlreadyExistsException($message, $level);
            $exception->setId($name);
            throw $exception;

        } else {
            $this->parameters[$name] = new \Yana\Db\Ddl\Functions\Parameter($name);
            return $this->parameters[$name];
        }
    }

    /**
     * Drops a parameter definition.
     *
     * @param   string  $name   name for drop a parameter
     */
    public function dropParameter(string $name)
    {
        $name = mb_strtolower($name);
        if (isset($this->parameters[$name])) {
            unset($this->parameters[$name]);
        }
    }

    /**
     * Get the implementing source-code for this function.
     *
     * @return  string|NULL
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Set source code.
     *
     * Sets the implementing source-code for this function.
     * Note that it is not checked wether or not the given code is valid.
     *
     * @param   string  $code  must be a valid implementation for the selected programming language (not checked here)
     * @return  $this 
     */
    public function setCode(string $code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get programming-language.
     *
     * Get the programming-language the implementing code is written for.
     * Most DBMS should at least support the value "SQL" as a language, while
     * others may also include "Java", "C++" or more.
     *
     * If no language is given, the default language is used. This is OK as
     * several vendors support just one language anyway.
     * DBMS that support multiple languages may demand that you specify the
     * language you wish to use.
     *
     * @return  string|NULL
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * Set programming-language.
     *
     * Sets the programming-language the implementing code is written for.
     * Most DBMS should at least support the value "SQL" as a language, while
     * others may also include "Java", "C++" or more.
     *
     * If no language is given, the default language is used. This is OK as
     * several vendors support just one language anyway.
     * DBMS that support multiple languages may demand that you specify the
     * language you wish to use.
     *
     * Note that this function does NOT check if the given language is really
     * implemented for the given DBMS. So use this option with care!
     *
     * @param   string  $language   name of a programming-language
     * @return  $this 
     */
    public function setLanguage(string $language)
    {
        if ($language === "") {
            $this->language = null;
        } else {
            $this->language = $language;
        }
        return $this;
    }

    /**
     * Unserializes a XDDL-node to an instance of this class and returns it.
     *
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed              $parent  parent node (if any)
     * @return  $this
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null)
    {
        $ddl = new self();
        $ddl->_unserializeFromXDDL($node);
        return $ddl;
    }

}

?>