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
 * database function implementation definition
 *
 * This wrapper class represents the structure of a database
 *
 * Note that the implementation is DBMS and language specific.
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 */
class DDLFunctionImplementation extends DDL
{
    /**#@+
     * @ignore
     * @access  protected
     */

    /**
     * tag name for persistance mapping: object <-> XDDL
     * @var  string
     */
    protected $xddlTag = "implementation";

    /**
     * attributes for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlAttributes = array(
        'dbms'     => array('dbms',     'string'),
        'language' => array('language', 'string')
    );

    /**
     * tags for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlTags = array(
        'param'  => array('parameters', 'array', 'DDLFunctionParameter'),
        'return' => array('return',     'string'),
        'code'   => array('code',       'string')
    );

    /** @var string                 */ protected $dbms = "generic";
    /** @var string                 */ protected $return = null;
    /** @var string                 */ protected $language = null;
    /** @var string                 */ protected $code = null;
    /** @var DDLFunctionParameter[] */ protected $parameters = array();

    /**#@-*/

    /**
     * <<magic>> Get function parameter, with the given name.
     *
     * Alias of {@see DDLFunctionImplementation::getParameter()}.
     *
     * @access  public
     * @param   string  $name  parameter name
     * @return  DDLFunctionParameter
     */
    public function __get($name)
    {
        return $this->getParameter($name);
    }

    /**
     * get DBMS
     *
     * Returns the name of the target DBMS for this definition as a lower-cased string.
     * The default is "generic".
     *
     * @access  public
     * @return  string
     */
    public function getDBMS()
    {
        return $this->dbms;
    }

    /**
     * create new instance
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
     * @access  public
     * @param   string  $dbms   target DBMS, defaults to "generic"
     */
    public function __construct($dbms = "generic")
    {
        assert('is_string($dbms); // Wrong type for argument 1. String expected');
        $dbms = strtolower($dbms);
        assert('in_array($dbms, DDLDatabase::getSupportedDBMS()); // Unsupported DBMS');
        $this->dbms = "$dbms";
    }

    /**
     * Get returned data-type.
     *
     * The data-type returned by the function.
     * Will return NULL if the returned type is void.
     *
     * @access  public
     * @return  string
     */
    public function getReturn()
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
     * @access  public
     * @param   string  $type  valid data-type in the selected programming-language
     * @return  DDLFunctionImplementation 
     */
    public function setReturn($type = "")
    {
        assert('is_string($type); // Wrong type for argument 1. String expected');
        if (empty($type)) {
            $this->return = null;
        } else {
            $this->return = "$type";
        }
        return $this;
    }

    /**
     * get parameter
     *
     * Returns the parameter definition with the name $name as an instance of
     * DDLFunctionParameter. If no parameter with the given name exists, the
     * function returns NULL instead.
     *
     * @access  public
     * @param   string  $name   parameter name
     * @return  DDLFunctionParameter
     */
    public function getParameter($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        $tableName = mb_strtolower($name);
        if (isset($this->parameters[$tableName])) {
            return $this->parameters[$tableName];
        } else {
            return null;
        }
    }

    /**
     * get parameter list
     *
     * Returns a list of all function parameters as instances of
     * DDLFunctionParameter.
     * If no parameters are defined, the list is empty.
     *
     * Important note! You can NOT add a new parameter by adding a new item to
     * the list. Use the function {see DDL::addParameter} instead.
     *
     * @access  public
     * @return  array
     */
    public function getParameters()
    {
        assert('is_array($this->parameters); // member "parameters" is expected to be an array');
        return $this->parameters;
    }

    /**
     * list all parameters by name
     *
     * Returns a numeric array with the names of all registered parameters.
     *
     * @access  public
     * @return  array
     */
    public function getParameterNames()
    {
        assert('is_array($this->parameters); // member "parameters" is expected to be an array');
        return array_keys($this->parameters);
    }

    /**
     * add parameter to function
     *
     * Adds a new parameter item and returns the definition as an instance of
     * DDLFunctionParameter.
     *
     * If another parameter with the same name already exists, it throws an
     * AlreadyExistsException.
     * The name must start with a letter and may only contain: a-z, 0-9, '-' and
     * '_'. Otherwise an InvalidArgumentException is thrown.
     *
     * @access  public
     * @param   string  $name   name of a new parameter
     * @return  DDLFunctionParameter
     * @throws  AlreadyExistsException  when a parameter with the same name already exists
     * @throws  InvalidArgumentException  on invalid name
     */
    public function addParameter($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        $name = mb_strtolower($name);
        if (isset($this->parameters[$name])) {
            throw new AlreadyExistsException("Another parameter with the name '$name' is already defined.");

        } else {
            $this->parameters[$name] = new DDLFunctionParameter($name);
            return $this->parameters[$name];
        }
    }

    /**
     * drop parameter definition
     *
     * Drops a parameter definition.
     *
     * @access  public
     * @param   string  $name   name for drop a parameter
     */
    public function dropParameter($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        if (isset($this->parameters[$name])) {
            unset($this->parameters[$name]);
        }
    }

    /**
     * get code
     *
     * Get the implementing source-code for this function.
     *
     * @access  public
     * @return  string
     */
    public function getCode()
    {
        assert('is_string($this->code); // Wrong type for argument 1. String expected');
        return $this->code;
    }

    /**
     * Set source code.
     *
     * Sets the implementing source-code for this function.
     * Note that it is not checked wether or not the given code is valid.
     *
     * @access  public
     * @param   string  $code  must be a valid implementation for the selected programming language (not checked here)
     * @return  DDLFunctionImplementation 
     */
    public function setCode($code)
    {
        assert('is_string($code); // Wrong type for argument 1. String expected');
        $this->code = "$code";
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
     * @access  public
     * @return  string
     */
    public function getLanguage()
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
     * @access  public
     * @param   string  $language   name of a programming-language
     * @return  DDLFunctionImplementation 
     */
    public function setLanguage($language)
    {
        assert('is_string($language); // Wrong type for argument 1. String expected');
        $this->language = "$language";
        return $this;
    }

    /**
     * Unserializes a XDDL-node to an instance of this class and returns it.
     *
     * @access  public
     * @static
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed              $parent  parent node (if any)
     * @return  DDLFunctionImplementation
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null)
    {
        $ddl = new self();
        $ddl->_unserializeFromXDDL($node);
        return $ddl;
    }
}

?>