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

namespace Yana\Db\Ddl;

/**
 * database column structure
 *
 * This is a generic implementation for columns of various types.
 * This is due to the fact that the list of supported types may expand over time.
 *
 * Note though, that not all properties are supported for all types and some settings may thus
 * be ignored.
 *
 * @package     yana
 * @subpackage  db
 */
class Column extends \Yana\Db\Ddl\AbstractNamedObject
{
    /**#@+
     * @ignore
     */

    /**
     * tag name for persistance mapping: object <-> XDDL
     * @var  string
     */
    protected $xddlTag = "column";

    /**
     * attributes for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlAttributes = array(
        'name'          => array('name',            'nmtoken'),
        'title'         => array('title',           'string'),
        'readonly'      => array('readonly',        'bool'),
        'notnull'       => array('notNull',         'bool'),
        'autoincrement' => array('autoincrement',   'bool'),
        'unsigned'      => array('unsigned',        'bool'),
        'fixed'         => array('fixed',           'bool'),
        'length'        => array('_length',         'int'),
        'maxsize'       => array('_maxsize',        'int'),
        'pattern'       => array('pattern',         'string'),
        'precision'     => array('precision',       'int'),
        'unique'        => array('unique',          'bool'),
        'width'         => array('imageWidth',      'int'),
        'height'        => array('imageHeight',     'int'),
        'ratio'         => array('imageRatio',      'bool'),
        'background'    => array('imageBackground', 'string'),
        'table'         => array('referenceTable',  'nmtoken'),
        'column'        => array('referenceColumn', 'nmtoken'),
        'label'         => array('referenceLabel',  'string'),
        'min'           => array('min',             'float'),
        'max'           => array('max',             'float'),
        'step'          => array('step',            'float')
    );

    /**
     * tags for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlTags = array(
        'description' => array('description', 'string'),
        'grant'       => array('grants',      'array', 'Yana\Db\Ddl\Grant'),
        'constraint'  => array('constraints', 'array', 'Yana\Db\Ddl\Constraint'),
        'default'     => array('default',     'array', null, 'dbms')
        // option and optgroup elements are serialized and unserialized elsewhere
    );

    /** @var bool                 */ protected $autoincrement = null;
    /** @var DLLConstraint[]      */ protected $constraints = array();
    /** @var array                */ protected $default = array();
    /** @var string               */ protected $description = null;
    /** @var array                */ protected $enumerationItems = array();
    /** @var bool                 */ protected $fixed = null;
    /** @var \Yana\Db\Ddl\Grant[] */ protected $grants = array();
    /** @var string               */ protected $imageBackground = null;
    /** @var int                  */ protected $imageHeight = null;
    /** @var int                  */ protected $imageWidth = null;
    /** @var bool                 */ protected $imageRatio = null;
    /** @var float                */ protected $max = null;
    /** @var float                */ protected $min = null;
    /** @var bool                 */ protected $notNull = null;
    /** @var \Yana\Db\Ddl\Table   */ protected $parent = null;
    /** @var string               */ protected $pattern = null;
    /** @var int                  */ protected $precision = null;
    /** @var bool                 */ protected $readonly = null;
    /** @var string               */ protected $referenceColumn = null;
    /** @var string               */ protected $referenceLabel = null;
    /** @var string               */ protected $referenceTable = null;
    /** @var int                  */ protected $size = null;
    /** @var float                */ protected $step = null;
    /** @var string               */ protected $title = null;
    /** @var string               */ protected $type = null;
    /** @var bool                 */ protected $unique = null;
    /** @var bool                 */ protected $unsigned = null;
    /** @var int                  */ protected $_maxsize = null;
    /** @var int                  */ protected $_length = null;

    /**#@-*/

    /**
     * @access  \Yana\Db\Ddl\Column    * @var     bool
     * @ignore
     */
    private $hasIndex = null;

    /**
     * @var  bool
     */
    private $isForeignKey = null;

    /**
     * cached grant-value
     *
     * @var  bool
     */
    private $isUpdatable = null;

    /**
     * cached enum list
     *
     * @var  bool
     */
    private $_enumValues = null;

    /**
     * Get list of column types.
     *
     * Returns a list with all supported column types as a numeric array.
     *
     * @return  array
     * @ignore
     */
    public static function getSupportedTypes()
    {
        return array('array','bool','color','date','enum','file','float','html','image','list',
            'inet','integer','list','mail','password','range','reference','set','string','tel',
            'text','time','timestamp','url');
    }

    /**
     * Initialize instance.
     *
     * @param  string  $name  foreign key name
     * @param  \Yana\Db\Ddl\Table  $parent  parent table
     */
    public function __construct($name = "", \Yana\Db\Ddl\Table $parent = null)
    {
        parent::__construct($name);
        $this->parent = $parent;
        $this->xddlTag =& $this->type;
    }

    /**
     * Get parent database.
     *
     * @return  \Yana\Db\Ddl\Database
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get data type of column.
     *
     * The 'type' is a lower-cased string, that represents the semantic data type of the column.
     * It is the same as the column's tag-name in XDDL files.
     *
     * The type is DBMS independent. Note that the physical in the database may be different.
     *
     * They type is mandatory. However: for new columns, where the type is not yet set, the function
     * may return NULL.
     *
     * @return  string
     * @name    \Yana\Db\Ddl\Column::getType()
     * @see     \Yana\Db\Ddl\Column::setType()
     */
    public function getType()
    {
        if (is_string($this->type)) {
            return $this->type;
        } else {
            return null;
        }
    }

    /**
     * Check if column is a type containing a file.
     *
     * This function returns bool(true) if the column is of type 'file' or 'image'
     * and bool(false) otherwise.
     *
     * @return  bool
     * @see     \Yana\Db\Ddl\Column::getType()
     */
    public function isFile()
    {
        return ($this->type == 'file' || $this->type == 'image');
    }

    /**
     * Set the type of a field as specified in the structure.
     *
     * The display attribute is an information that 1st informs about the type of a columns and 2nd
     * aims at UI-generators to tell them, how to interpret and display a certain field.
     *
     * While a column named "pass" may be of type varchar, you may want to display it using a
     * password input-element, while a column "title" might use the same physical type, but should
     * be displayed as a single-line string.
     * Use this whenever the generator may not guess the right presentation by using the type of the
     * column.
     *
     * The current implementation includes:
     * <ul>
     *   <li> array - any data, stored as serialized string
     *        (use this to store a bag of configuration settings) </li>
     *   <li> bool - a boolean value, the physical type depends on the DBMS (bool or int(1)) </li>
     *   <li> color - a hexadecimal color-value </li>
     *   <li> date - date element consisting of day, month and year
     *        (physical type depends on DBMS) </li>
     *   <li> enum - a enumeration element (usually stored as int, numeric or varchar) </li>
     *   <li> file - binary large object, stored outside database for better performance
     *        (physical type varchar, containing ressource identifier) </li>
     *   <li> float - floating point value
     *        (physical type depends on DBMS, length and precission attributes) </li>
     *   <li> html - varchar containing HTML </li>
     *   <li> inet - an IP address (may contain IPv4 or IPv6), physical type depends on DBMS
     *        (PostgreSQL has native support) </li>
     *   <li> integer - integer value, where physical type depends on DBMS and length-attribute
     *        (tinyint, smallint, bigint et cetera) </li>
     *   <li> list - numeric array of items (editable enumeration) </li>
     *   <li> mail - e. mail address </li>
     *   <li> password - protected input-element </li>
     *   <li> range - floating point value </li>
     *   <li> reference - used for foreign keys </li>
     *   <li> string - single-line text (physical type: varchar) </li>
     *   <li> tel - single-line text containing a telephone number (physical type: varchar) </li>
     *   <li> text - multi-line text (physical type: varchar) </li>
     *   <li> time - full date containing day, month, year, hours, minutes, seconds and time zone
     *        (physical type depends on DBMS) </li>
     *   <li> timestamp - full date stored as a portable unified time code
     *        (physical type integer) </li>
     *   <li> url - a clickable link </li>
     * </ul>
     *
     * The default is "auto" (as in the CSS element display: auto).
     *
     * @param   string  $value  new value of this property
     * @return  \Yana\Db\Ddl\Column 
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the parameter is empty
     */
    public function setType($value)
    {
        assert('is_string($value)', ' Invalid argument $value: string expected');
        $value = strtolower($value);
        assert('in_array($value, self::getSupportedTypes())', ' Undefined column type "' . $value . '". ');
        if (empty($value)) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Type cannot be empty.");
        }

        $this->type = "$value";
        return $this;
    }

    /**
     * Get title.
     *
     * The title is a label text that should be displayed in the UI when viewing this object.
     *
     * It is optional. If it is not set, the function returns NULL instead.
     *
     * @return  string
     */
    public function getTitle()
    {
        if (is_string($this->title)) {
            return $this->title;
        } else {
            return null;
        }
    }

    /**
     * Set title.
     *
     * Sets the title used to display the object in the UI.
     * To reset the property, leave the parameter empty.
     *
     * @param   string  $title  set title for display in User Interface
     * @return  \Yana\Db\Ddl\Column
     */
    public function setTitle($title = "")
    {
        assert('is_string($title)', ' Wrong type for argument 1. String expected');
        if (empty($title)) {
            $this->title = null;
        } else {
            $this->title = "$title";
        }
        return $this;
    }

    /**
     * Get pattern.
     *
     * A pattern is a textual regular expression pattern using the syntax described in
     * the ECMA262 standard, without delimiters or modifiers.
     *
     * The check a pattern use: /^pattern$/
     *
     * This attribute applies to columns of types: string, url, mail and tel.
     *
     * Returns the pattern as a string or NULL, if it is undefined. 
     *
     * @return  string
     */
    public function getPattern()
    {
        if (is_string($this->pattern)) {
            return $this->pattern;
        } else {
            return null;
        }
    }

    /**
     * Set pattern.
     *
     * A pattern is a textual regular expression pattern using the syntax described in
     * the ECMA262 standard, without delimiters or modifiers.
     *
     * Note: The pattern must match the whole value, not just a subset, as in: '/^pattern$/'.
     * In addition be sure to escape the delimiter '/'.
     *
     * This attribute applies to columns of types: string, url, mail and tel.
     *
     * To reset this property, leave the parameter empty.
     *
     * @param   string  $pattern  regular expression pattern
     * @return  \Yana\Db\Ddl\Column
     */
    public function setPattern($pattern = "")
    {
        assert('is_string($pattern)', ' Wrong type for argument 1. String expected');
        if (empty($pattern)) {
            $this->pattern = null;
        } else {
            $this->pattern = "$pattern";
        }
        return $this;
    }

    /**
     * Get the user description.
     *
     * The description serves two purposes:
     * 1st as offline-documentation 2nd as online-documentation.
     *
     * The form-generator may use the description to provide context-sensitive
     * help or additional information (depending on it's implementation) on a
     * auto-generated database application.
     *
     * The description is optional. If there is none, the function will return
     * NULL instead. Note that the description may also contain an identifier
     * for automatic translation.
     *
     * @return  string
     */
    public function getDescription()
    {
        if (is_string($this->description)) {
            return $this->description;
        } else {
            return null;
        }
    }

    /**
     * Set the description property.
     *
     * The description serves two purposes:
     * 1st as offline-documentation 2nd as online-documentation.
     *
     * Note that the description may also contain an identifier for automatic
     * translation.
     *
     * To reset the property, leave the parameter $description empty.
     *
     * @param   string  $description  new value of this property
     * @return  \Yana\Db\Ddl\Column
     */
    public function setDescription($description = "")
    {
        assert('is_string($description)', ' Wrong type for argument 1. String expected');
        if (empty($description)) {
            $this->description = null;
        } else {
            $this->description = "$description";
        }
        return $this;
    }

    /**
     * Get rights management settings.
     *
     * Returns an array of \Yana\Db\Ddl\Grant objects.
     *
     * Note! If no grant is defined, the form is considered to be public and the
     * resulting array will be empty.
     *
     * If at least one grant is set, any user that does not match the given
     * restrictions is not permitted to access the form.
     *
     * @return  array
     */
    public function getGrants()
    {
        assert('is_array($this->grants)', ' Member "grants" is expected to be an array.');
        return $this->grants;
    }

    /**
     * Drop rights management settings.
     *
     * {@link \Yana\Db\Ddl\Grant}s control the access permissions granted to the user.
     *
     * Note! If no grant is defined, the form is considered to be public.
     *
     * If at least one grant is set, any user that does not match the given
     * restrictions is not permitted to access the form.
     *
     * @return  \Yana\Db\Ddl\Column
     */
    public function dropGrants()
    {
        $this->grants = array();
        return $this;
    }

    /**
     * Set rights management setting.
     *
     * {@link \Yana\Db\Ddl\Grant}s control the access permissions granted to the user.
     *
     * This function adds a new grant to the configuration.
     *
     * @param   \Yana\Db\Ddl\Grant  $grant set a new grant object
     * @return  \Yana\Db\Ddl\Column 
     */
    public function setGrant(\Yana\Db\Ddl\Grant $grant)
    {
        $this->grants[] = $grant;
        return $this;
    }

    /**
     * Add rights management setting.
     *
     * {@link \Yana\Db\Ddl\Grant}s control the access permissions granted to the user.
     *
     * This function adds a new grant to the configuration by using the given
     * options and returns it as an \Yana\Db\Ddl\Grant object.
     *
     * @param   string  $user   user group
     * @param   string  $role   user role
     * @param   int     $level  security level
     * @return  \Yana\Db\Ddl\Grant
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when $level is out of range [0,100]
     */
    public function addGrant($user = null, $role = null, $level = null)
    {
        assert('is_null($user) || is_string($user)', ' Wrong type for argument 1. String expected');
        assert('is_null($role) || is_string($role)', ' Wrong type for argument 2. String expected');
        assert('is_null($level) || is_int($level)', ' Wrong type for argument 3. Integer expected');
        $grant = new \Yana\Db\Ddl\Grant();
        if (!empty($user)) {
            $grant->setUser($user);
        }
        if (!empty($role)) {
            $grant->setRole($role);
        }
        // may throw an \Yana\Core\Exceptions\InvalidArgumentException
        if (!is_null($level)) {
            $grant->setLevel($level);
        }
        $this->grants[] = $grant;
        return $grant;
    }

    /**
     * check if form is updatable
     *
     * Returns bool(true) if form is updatable to the current user and bool(false) otherwise.
     *
     * @return  bool
     */
    public function isUpdatable()
    {
        if (!isset($this->isUpdatable)) {
            $this->isUpdatable = (bool) \Yana\Db\Ddl\Grant::checkPermissions($this->getGrants(), false, false, true);
        }
        return $this->isUpdatable;
    }

    /**
     * check whether the dbo has read-only access
     *
     * Returns bool(true) if the column is read-only and bool(false)
     * otherwise.
     *
     * The default is bool(false).
     *
     * @return  bool
     */
    public function isReadonly()
    {
        return !empty($this->readonly);
    }

    /**
     * set read-only access
     *
     * You may set the column to be read-only to prevent any changes to it by setting this to
     * bool(true).
     *
     * @param   bool  $isReadonly   set read-only access
     * @return  \Yana\Db\Ddl\Column
     */
    public function setReadonly($isReadonly = false)
    {
        assert('is_bool($isReadonly)', ' Wrong type for argument 1. Boolean expected');
        $this->readonly = (bool) $isReadonly;
        return $this;
    }

    /**
     * check whether column allows NULL values
     *
     * Returns bool(true) if the column allows undefined values (NULL).
     * Returns bool(false) otherwise.
     *
     * The default is bool(true).
     *
     * @return  bool
     */
    public function isNullable()
    {
        return empty($this->notNull);
    }

    /**
     * Choose wether column should be nullable.
     *
     * If the argument $isNullable is bool(false), then the DDL for this column would contain the
     * "not null" keyword. This means a column may not contain undefined values, if the property is
     * set to bool(false).
     *
     * The default is bool(true).
     *
     * @param  bool  $isNullable  new value of this property
     */
    public function setNullable($isNullable = true)
    {
        assert('is_bool($isNullable)', ' Wrong type for argument 1. Boolean expected');
        $this->notNull = ! $isNullable;
        return $this;
    }

    /**
     * Check whether a column has a unique constraint.
     *
     * Returns bool(true) if this column has a unique constraint.
     * Returns bool(false) otherwise.
     *
     * A unique constraint means, that the column may not contain doubled values.
     * Note that a unique constraint technicaly implies an unique index on this column and vice
     * versa. (It implies, but not demands it!)
     *
     * Important! Even if an unique index exists, the column is not reported to have an unique
     * constraint.
     * You are thus strongly encouraged NOT to use unique indexes whenever you can express the same
     * thing using a constraint.
     *
     * Note that you can't define an unique constraint using multiple columns. In that case you
     * should use an unique index.
     *
     * @return  bool
     */
    public function isUnique()
    {
        return !empty($this->unique);
    }

    /**
     * Add/remove a unique constraint on a column.
     *
     * Note: you don't need to set a "unique" constraint on a primary key. Primary keys implicitely
     * have a unique constraint.
     *
     * A unique constraint means, that the column may not contain doubled values.
     * Note that a unique constraint technicaly implies an unique index on this column and vice
     * versa. (It implies, but not demands it!)
     *
     * Important! Even if an unique index exists, the column is not reported to have an unique
     * constraint.
     * You are thus strongly encouraged NOT to use unique indexes whenever you can express the same
     * thing using a constraint.
     *
     * Note that you can't define an unique constraint using multiple columns. In that case you
     * should use an unique index.
     *
     * @param   bool  $isUnique  new value
     * @return  \Yana\Db\Ddl\Column
     */
    public function setUnique($isUnique = true)
    {
        assert('is_bool($isUnique)', ' Wrong type for argument 1. Boolean expected');
        $this->unique = (bool) $isUnique;
        return $this;
    }

    /**
     * Check whether column is unsigned number.
     *
     * Returns bool(true) if this column has the flag unsigned set to bool(true).
     * Returns bool(false) otherwise.
     *
     * This function will also return bool(true), if the property "fixed" is set to true, as "fixed"
     * requires the "unsigned" flag to be set.
     *
     * Important! if unsigned is not supported by your DBMS, it is emulated by the framework's
     * database API.
     *
     * @return  bool
     */
    public function isUnsigned()
    {
        return !empty($this->unsigned) || $this->isFixed();
    }

    /**
     * Set column to unsigned number.
     *
     * An "unsigned" number is supposed to be interpreted as a positive value.
     * This means, with "unsigned" = true, any value lesser than 0 is invalid.
     *
     * If the framework's API encounters an invalid number, it returns false and issues an error.
     * Note that this is unlike MySQL, which automatically and silently replaces an invalid value
     * by 0 - which MIGHT lead to an error or unexpected behavior of an application working on the
     * database.
     *
     * If the type of this column is not numeric, the function throws a NotImplementedException.
     *
     * @param   bool    $isUnsigned      true: unsigned number, false: signed number
     * @return  \Yana\Db\Ddl\Column
     * @throws  \Yana\Core\Exceptions\NotImplementedException  if column is not a number
     */
    public function setUnsigned($isUnsigned)
    {
        assert('is_bool($isUnsigned)', ' Wrong type for argument 1. Boolean expected');
        if (!$this->isNumber()) {
            $message = "Property 'unsigned' not implemented for type '{$this->type}'.";
            throw new \Yana\Core\Exceptions\NotImplementedException($message, E_USER_WARNING);
        }

        $this->unsigned = (bool) $isUnsigned;
        return $this;
    }

    /**
     * Check whether a column is a fixed-length number.
     *
     * Returns bool(true) if this column has the flag fixed set to bool(true).
     * Returns bool(false) otherwise.
     *
     * Note: For columns of type integer, this sets the zerofill-flag for MySQL.
     *
     * It is meant to be interpreted as follows:
     * For zerofill, the number is always expanded to the maximum number of digits, defined by the
     * maximum length of the number. If length is not set, it is to be ignored.
     *
     * Important note: if zerofill is not supported by your DBMS, it is emulated by the framework's
     * database API.
     *
     * @return  bool
     */
    public function isFixed()
    {
        return !empty($this->fixed);
    }

    /**
     * Set a numeric column to fixed length.
     *
     * Note: For columns of type integer, this sets the zerofill-flag for MySQL.
     *
     * Be aware that setting "fixed" to bool(true) will also set the property "unsigned", as "fixed"
     * depends on this.
     *
     * Important note: if zerofill is not supported by your DBMS, it is emulated by the framework's
     * database API.
     *
     * @param   bool  $isFixed  new value
     * @return  \Yana\Db\Ddl\Column
     */
    public function setFixed($isFixed)
    {
        assert('is_bool($isFixed)', ' Wrong type for argument 1. Boolean expected');
        if ($isFixed) {
            $this->fixed = true;
            $this->unsigned = true;
        } else {
            $this->fixed = false;
        }
        return $this;
    }

    /**
     * Check whether column uses auto-increment.
     *
     * Returns bool(true) if this is an autonumbered colummn. Returns bool(false) otherwise.
     *
     * Auto-increment is a MySQL-feature, that may be emulated on other DBMS.
     * It can however only be used on columns of type integer.
     *
     * @return  bool
     */
    public function isAutoIncrement()
    {
        return !empty($this->autoincrement);
    }

    /**
     * Make a column use auto-increment.
     *
     * Auto-increment is a MySQL-feature, that may be emulated on other DBMS.
     * It can however only be used on columns of type integer.
     *
     * You should note, that the user input takes precedence over the AutoIncrement feature, which
     * defines a default value.
     *
     * Note: this function does not clear the "default" property of the column, if any.
     *
     * If the type of this column is not numeric, the function throws a NotImplementedException.
     *
     * @param   bool   $isAutoIncrement  new value of this property
     * @return  \Yana\Db\Ddl\Column
     * @throws  \Yana\Core\Exceptions\NotImplementedException  if column is not a number
     */
    public function setAutoIncrement($isAutoIncrement)
    {
        assert('is_bool($isAutoIncrement)', ' Wrong type for argument 1. Boolean expected');
        if (!$this->isNumber()) {
            $message = "Property 'autoincrement' not implemented for type '{$this->type}'.";
            throw new \Yana\Core\Exceptions\NotImplementedException($message, E_USER_WARNING);
        }

        $this->autoincrement = (bool) $isAutoIncrement;
        return $this;
    }

    /**
     * Check whether a column uses the "auto-fill" feature.
     *
     * Returns bool(true) the column uses an auto-fill feature. Returns bool(false) otherwise.
     *
     * The meaning of this feature depends on the data type.
     *
     * @return  bool
     */
    public function isAutoFill()
    {
        switch ($this->type)
        {
            case 'integer':
                return $this->isAutoIncrement();

            case 'inet':
                return ($this->getDefault() === 'REMOTE_ADDR');

            case 'time':
            case 'date':
            case 'timestamp':
                return ($this->getDefault() === 'CURRENT_TIMESTAMP');

            default:
                return false;
        } // end switch
    }

    /**
     * Make a column use auto-filled values.
     *
     * This enables the "auto-fill" feature, which is available for columns of several types. On
     * columns of type "integer" it mimics MySQL's "auto increment" feature.
     * On columns of type "ip" it enters the visitor's remote address (IP) automatically.
     * For types "date", "time" and "timestamp" it enters the current server time.
     *
     * However: you should note, that the user input takes precedence over the auto-fill feature,
     * which defines a default value.
     *
     * Also note that this property is "virtual". This means, there is not really a property "auto"
     * in structure files. Instead this will set other properties (depending on the chosen type) to
     * such a value, that the column will be filled automatically with non-static values, whereever
     * this makes sense.
     *
     * If the argument $isAutoFill is set to false, these changes will be reversed and the changed
     * properties will be reset to default values.
     *
     * This function will throw a NotImplementedException if the feature is not available for the
     * chosen type.
     *
     * @param   bool  $isAutoFill        new value of this property
     * @return  \Yana\Db\Ddl\Column
     * @throws  \Yana\Core\Exceptions\NotImplementedException  when auto-fill is not available for this column type
     */
    public function setAutoFill($isAutoFill)
    {
        assert('is_bool($isAutoFill)', ' Wrong type for argument 1. Boolean expected');
        switch ($this->type)
        {
            case 'integer':
                return $this->setAutoIncrement($isAutoFill);

            case 'inet':
                if ($isAutoFill) {
                    $this->setDefault('REMOTE_ADDR');
                } else {
                    if ($this->getDefault() === 'REMOTE_ADDR') {
                        $this->setDefault();
                    }
                }
            break;

            case 'time':
            case 'date':
            case 'timestamp':
                if ($isAutoFill) {
                    $this->setDefault('CURRENT_TIMESTAMP');
                } else {
                    if ($this->getDefault() === 'CURRENT_TIMESTAMP') {
                        $this->setDefault();
                    }
                }
            break;

            default:
                $message = "Auto-fill is not implemented for columns of type '{$this->type}'.";
                throw new \Yana\Core\Exceptions\NotImplementedException($message, E_USER_NOTICE);
        } // end switch
        return $this;
    }

    /**
     * Check whether a foreign key exists in the current structure.
     *
     * Returns bool(true) if the parent table is known and the current column is in its list of
     * foreign keys. Returns bool(false) otherwise.
     *
     * Note that the function only returns true, if the constraint is not a compound-key.
     *
     * Note that this operation is not case sensitive.
     *
     * FOR INTERNAL USE BY {@see \Yana\Db\FileDb\Driver} ONLY.
     *
     * @return  bool
     * @ignore
     */
    public function isForeignKey()
    {
        if (!isset($this->isForeignKey)) {
            $this->isForeignKey = false;
            if ($this->type === 'reference') {
                $this->isForeignKey = true;
            } elseif (isset($this->parent)) {
                // get list of foreign key constraints
                $foreignKeys = $this->parent->getForeignKeys();
                foreach ($foreignKeys as $key)
                {
                    // for each constraint check if column is in list
                    $list = $key->getColumns();
                    if (count($list) === 1 && isset($list[$this->name])) {
                        $this->isForeignKey = true;
                        break;
                    }
                }
            }
        }
        return (bool) $this->isForeignKey;
    }

    /**
     * Check whether a primary key exists in the current structure.
     *
     * Returns bool(true) if the parent table is known and the current column is it's primary key.
     * Returns bool(false) otherwise.
     *
     * Note that this operation is not case sensitive.
     *
     * @return  bool
     */
    public function isPrimaryKey()
    {
        return (isset($this->parent)) ? (bool) ($this->parent->getPrimaryKey() === $this->name) : false;
    }

    /**
     * Check if column has a numeric data type.
     *
     * Returns bool(true) if and only if the column has a numeric data type.
     * Returns bool(false) otherwise.
     *
     * @return  bool
     */
    public function isNumber()
    {
        switch ($this->type)
        {
            case 'integer':
            case 'float':
                return true;
            default:
                return false;
        }
    }

    /**
     * Get the maximum length of a column.
     *
     * Alias of getSize().
     *
     * @return  int
     * @see     \Yana\Db\Ddl\Column::getSize()
     */
    public function getLength()
    {
        return $this->getSize();
    }

    /**
     * Set the maximum length.
     *
     * The argument $length must be a positive integer.
     *
     * The argument $precision applies to floating point values only and defines the length of the
     * decimal fraction of the input number.
     *
     * The maximum number of full digits is: length - precision. So be aware, the precision may
     * not be larger than length. Otherwise the function will throw an InvalidArgumentExpection.
     * If you wish to set a precision, you need to set a length as well.
     *
     * @param   int  $length     a positive integer
     * @param   int  $precision  applies to type float only
     * @return  \Yana\Db\Ddl\Column
     * @see     \Yana\Db\Ddl\Column::setSize()
     * @throws  InvalidArgumentExpection  if precission is greater than length
     */
    public function setLength($length = -1, $precision = -1)
    {
        assert('is_int($length)', ' Wrong type for argument 1. Integer expected');
        assert('is_int($precision)', ' Wrong type for argument 2. Integer expected');
        if ($precision > $length) {
            $message = "The precission '$precision' may not exceed the maximum length of '$length'.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
        }

        $this->setSize($length);

        $this->precision = null;
        if ($precision > 0) {
            $this->precision = (int) $precision;
        }
        return $this;
    }

    /**
     * Get the maximum size of a column.
     *
     * Returns the maximum size of the colum or NULL if the value is not set.
     *
     * @return  int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Get the maximum length of the decimal fraction of a float.
     *
     * Returns the maximum size of the colum or NULL if the value is not set.
     * Note that this value applies to columns of type float only.
     *
     * @return  int
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * Set the maximum size.
     *
     * The argument $size must be a positive integer. To reset the property, leave the argument off.
     *
     * Not that for files there is another boundary in file-size, which is the maximum file upload
     * size defined in php.ini. It may be set to a value lower than that. The ini-setting takes
     * precedence other anything you specify here.
     *
     * @param   int  $size  maximum size in byte
     * @return  \Yana\Db\Ddl\Column
     */
    public function setSize($size = -1)
    {
        assert('is_int($size)', ' Wrong type for argument 1. Integer expected');
        $this->size = null;

        if ($size > 0) {
            $this->size = (int) $size;
        }
        return $this;
    }

    /**
     * Get the properties of a field of type 'image'.
     *
     * Returns an array of the following values:
     * <code>
     * array (
     *    'width'      : int,   // horizontal dimension in px
     *    'height'     : int,   // vertical dimension in px
     *    'ratio'      : bool,  // keep aspect-ratio (true=yes, false=no)
     *    'background' : string // hex-color of canvas (#RRGGBB)
     * )
     * </code>
     * If one of the values above does not exist, the field is set to 'null'.
     *
     * @return  array
     */
    public function getImageSettings()
    {
        $imageSettings = array(
            'width' => $this->imageWidth,
            'height' => $this->imageHeight,
            'ratio' => $this->imageRatio,
            'background' => $this->imageBackground
        );
        return $imageSettings;
    }

    /**
     * Set the properties of a field of type 'image'.
     *
     * To reset one of the values, set it to null.
     *
     * @param   int     $width       horizontal dimension in px
     * @param   int     $height      vertical dimension in px
     * @param   bool    $ratio       keep aspect-ratio (true=yes, false=no)
     * @param   string  $background  hex-color of canvas (#RRGGBB)
     * @return  \Yana\Db\Ddl\Column 
     */
    public function setImageSettings($width = null, $height = null, $ratio = null, $background = null)
    {
        assert('is_null($width) || is_int($width)', ' Wrong type for argument 1. Integer expected');
        assert('is_null($height) || is_int($height)', ' Wrong type for argument 2. Integer expected');
        assert('is_null($ratio) || is_bool($ratio)', ' Wrong type for argument 3. Boolean expected');
        assert('is_null($background) || is_string($background)', ' Wrong type for argument 4. String expected');
        $this->imageWidth = $width;
        $this->imageHeight = $height;
        $this->imageRatio = $ratio;
        $this->imageBackground = $background;
        return $this;
    }

    /**
     * Get the properties of a field of type 'reference'.
     * 
     * @return  \Yana\Db\Ddl\Reference
     */
    public function getReferenceSettings()
    {
        return new \Yana\Db\Ddl\Reference((string) $this->referenceTable, (string) $this->referenceColumn, (string) $this->referenceLabel);
    }

    /**
     * Set the properties of a field of type 'reference'.
     *
     * To reset one of the values, set it to null.
     * This applies to columns of type reference only.
     *
     * Reference columns are used to represent foreign-keys. The physical type of this column
     * depends on the type of the target column.
     * A reference column implies a foreign key.
     *
     * Since the target column might be an integer (or something similar) you may optionally define
     * a label column. If so, the label is displayed in GUI instead of the numeric value.
     * The target column MUST be unique, the label column SHOULD be unique.
     *
     * @param   string   $table   table name
     * @param   string   $column  column name
     * @param   string   $label   label
     * @return  \Yana\Db\Ddl\Column
     */
    public function setReferenceSettings($table = null, $column = null, $label = null)
    {
        assert('is_null($table) || is_string($table)', ' Wrong type for argument 1. String expected');
        assert('is_null($column) || is_string($column)', ' Wrong type for argument 2. String expected');
        assert('is_null($label) || is_string($label)', ' Wrong type for argument 3. String expected');
        $this->referenceTable = $table;
        $this->referenceColumn = $column;
        $this->referenceLabel = $label;
        return $this;
    }

    /**
     * Get the default values.
     *
     * Returns an associative array of all default values of the column, where
     * the target DBMS are the keys and the defaults are the values of the array.
     *
     * @return  array
     */
    public function getDefaults()
    {
        assert('is_array($this->default);');

        return (array) $this->default;
    }

    /**
     * Get the default value.
     *
     * Returns the default value of the column (where available) or NULL, if there is none.
     * The type of the default value returned depends on the type of the column.
     *
     * @param   string  $dbms  target DBMS, defaults to "generic"
     * @return  mixed
     */
    public function getDefault($dbms = 'generic')
    {
        assert('is_string($dbms)', ' Wrong type for argument 1. String expected');
        $dbms = strtolower($dbms);
        assert('in_array($dbms, \Yana\Db\Ddl\Database::getSupportedDBMS())', ' Unsupported DBMS');

        $default = null;
        if (isset($this->default[$dbms])) {
            $default = $this->default[$dbms];
        } elseif (isset($this->default['generic'])) {
            $default = $this->default['generic'];
        }

        return $default;
    }

    /**
     * Set the default value.
     *
     * Set the default value for the specified DBMS. Note that the type of the value depends on the
     * type of column. The physical default value may depend on the DBMS and physical data type.
     *
     * A good example is data type Boolean, which is natively supported by PostGreSQL and thus the
     * physical default value would be true or false. For MySQL it is stored as TinyInt with the
     * default values 1 or 0.
     * However the DBMS-independent (generic) default values would be true or false. The database
     * API will convert them automatically.
     *
     * You may use the DBMS setting to overwrite this conversion.
     *
     * @param   mixed   $value  new value of this property
     * @param   string  $dbms   target DBMS, defaults to "generic"
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when parameter is empty
     * @return  \Yana\Db\Ddl\Column
     */
    public function setDefault($value = null, $dbms = "generic")
    {
        $dbms = strtolower($dbms);
        assert('is_string($dbms)', ' Wrong type for argument 1. String expected');
        assert('in_array($dbms, \Yana\Db\Ddl\Database::getSupportedDBMS())', ' Unsupported DBMS');
        if (is_null($value)) {
            unset($this->default[$dbms]);
        } elseif (!empty($dbms)) {
            $this->default[$dbms] = $value;
        } else {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Parameter with the name '\$dbms' can not be empty.");
        }
        return $this;
    }

    /**
     * List all constraints.
     *
     * Retrieves all "constraint" entries that apply to the given DBMS and returns the results as a numeric array.
     *
     * If no constraints have been defined the returned array will be empty.
     *
     * @param   string  $dbms  target DBMS, defaults to "generic"
     * @return  array
     */
    public function getConstraints($dbms = "generic")
    {
        assert('is_string($dbms)', ' Wrong type for argument 1. String expected');
        $dbms = strtolower($dbms);
        $constraints = array();

        foreach ((array) $this->constraints as $constraint)
        {
            /* @var $constraint \Yana\Db\Ddl\Constraint */
            assert($constraint instanceof \Yana\Db\Ddl\Constraint);

            if ($constraint->getDBMS() === $dbms) {
                $constraints[] = $constraint;
            }
        }

        return $constraints;
    }

    /**
     * Get constraint.
     *
     * Returns the an instance of \Yana\Db\Ddl\Constraint, that matches the given name and target DBMS.
     * If no such instance is found the function returns NULL instead.
     *
     * @param   string  $name  constraint-name
     * @param   string  $dbms  target DBMS, defaults to "generic"
     * @return  \Yana\Db\Ddl\Constraint
     */
    public function getConstraint($name, $dbms = "generic")
    {
        assert('is_string($name)', ' Invalid argument $name: string expected');
        assert('is_string($dbms)', ' Invalid argument $dbms: string expected');
        $dbms = strtolower($dbms);

        foreach ((array) $this->constraints as $constraint)
        {
            /* @var $constraint \Yana\Db\Ddl\Constraint */
            assert($constraint instanceof \Yana\Db\Ddl\Constraint);

            if ($constraint->getDBMS() === $dbms && $constraint->getName() === $name) {
                return $constraint;
            }
        }

        return null;
    }

    /**
     * Add constraint.
     *
     * Note: This function can't ensure that your codes makes sense.
     * So keep in mind that it is your job in the first place to ensure the constraint is valid!
     * The syntax depends on the target DBMS. For type "generic" the feature is emulated using PHP
     * code.
     *
     * BE WARNED: As always - do NOT use this function with any unchecked user input.
     *
     * Note that the name should be unique for each DBMS.
     * You may however have several constraints with the same name for different DBMS.
     * The function will not check this!
     *
     * A constraint is a boolean expression that must evaluate to true at all times for the row to
     * be valid. The database should ensure that. For databases that don't have that feature, you
     * may use the vendor-independent type "generic" to simluate it.
     *
     * @param   string  $constraint  Code
     * @param   string  $name        optional constraint-name
     * @param   string  $dbms        target DBMS, defaults to "generic"
     */
    public function addConstraint($constraint, $name = "", $dbms = "generic")
    {
        assert('is_string($constraint)', ' Wrong type for argument 1. String expected');
        assert('is_string($name)', ' Wrong type for argument 2. String expected');
        assert('is_string($dbms)', ' Wrong type for argument 3. String expected');
        $dbms = strtolower($dbms);
        $object = new \Yana\Db\Ddl\Constraint($name);
        $object->setDBMS($dbms);
        $object->setConstraint($constraint);
        $this->constraints[] = $object;
    }

    /**
     * Drops the list of all defined constraints.
     *
     * @return  \Yana\Db\Ddl\Column
     */
    public function dropConstraints()
    {
        $this->constraints = array();
        return $this;
    }

    /**
     * Set enumeration option.
     *
     * Applies to columns of type Enumeration only.
     *
     * Set the option with the given $name to the given value.
     *
     * For an Enumeration, the value serves as a label, while the name is stored in the database.
     * Note that the value may also be a language reference for I18N.
     *
     * It might help to think of it as a HTML select-element:
     * <code>
     * <select>
     *    <option name="$name">$value</option>
     *    ...
     * </select>
     * </code>
     *
     * @param   scalar  $name   name of the enum
     * @param   string  $value  value
     * @return  \Yana\Db\Ddl\Column
     */
    public function setEnumerationItem($name, $value = null)
    {
        assert('is_scalar($name)', ' Wrong type for argument 1. Scalar value expected');
        assert('is_null($value) || is_string($value)', ' Wrong type for argument 2. String value expected');
        $this->enumerationItems[$name] = $value;
        $this->_enumValues = null; // reset cache
        return $this;
    }

    /**
     * Set enumeration options.
     *
     * Applies to columns of type Enumeration only.
     *
     * Set the valid options for the enumeration column.
     *
     * The input serves as an array where the value are labels, while the name is stored in the
     * database. Note that the value may also be a language reference for I18N.
     *
     * It might help to think of it as a HTML select-element:
     * <code>
     * <select>
     *    <option name="$name">$value</option>
     *    ...
     * </select>
     * </code>
     *
     * @param   array  $options expected an array with options for the enumeration column.
     * @return  \Yana\Db\Ddl\Column
     */
    public function setEnumerationItems(array $options)
    {
        $this->enumerationItems = $options;
        $this->_enumValues = null; // reset cache
        return $this;
    }

    /**
     * Drop enumeration options.
     *
     * Applies to columns of type Enumeration only.
     *
     * Removes all previously set options and resets the property.
     *
     * @return  \Yana\Db\Ddl\Column
     */
    public function dropEnumerationItems()
    {
        $this->enumerationItems = array();
        $this->_enumValues = array(); // reset cache
        return $this;
    }

    /**
     * Get all enumeration options.
     *
     * Applies to columns of type Enumeration only.
     *
     * Returns a list of all options, where the keys are the option names (which are stored in the
     * database and serve as identifiers) and the values are label which explain what the option is.
     *
     * Keys may be of any scalar type, while values are expected to be strings.
     * Note that a value may contain a language reference as well.
     *
     * @return  array
     */
    public function getEnumerationItems()
    {
        return $this->enumerationItems;
    }

    /**
     * Get all enumeration options.
     *
     * Applies to columns of type Enumeration only.
     *
     * Returns a list of all options, where the keys are the option names (which are stored in the
     * database and serve as identifiers) and the values are label which explain what the option is.
     *
     * Keys may be of any scalar type, while values are expected to be strings.
     * Note that a value may contain a language reference as well.
     *
     * @param   scalar  $id  id of a enumeration item which would be droped
     * @throws  \Yana\Core\Exceptions\NotFoundException  when no option with the given name exists
     */
    public function dropEnumerationItem($id)
    {
        assert('is_scalar($id)', ' Wrong type for argument 1. Scalar value expected');
        if (!isset($this->enumerationItems[$id])) {
            $message = "No such option '$id' in Enumeration '{$this->getName()}'.";
            throw new \Yana\Core\Exceptions\NotFoundException($message, E_USER_WARNING);
        }

        unset($this->enumerationItems[$id]);
        $this->_enumValues = null; // reset cache
    }

    /**
     * Get enumeration option.
     *
     * Applies to columns of type Enumeration only.
     *
     * Returns the value with the name $name. If no option with the given name exists, the function
     * returns NULL instead.
     *
     * @param   scalar  $id  id of a enumeration item
     * @return  scalar
     */
    public function getEnumerationItem($id)
    {
        assert('is_scalar($id)', ' Wrong type for argument 1. String expected');
        if (isset($this->enumerationItems[$id])) {
            return $this->enumerationItems[$id];
        } else {
            return null;
        }
    }

    /**
     * Get names of enumeration options.
     *
     * Applies to columns of type Enumeration only.
     *
     * Returns a list for all valid Enumeration values. Any value not stored in this list is
     * invalid (unless it's NULL and the column is NULLABLE).
     *
     * @return  array
     */
    public function getEnumerationItemNames()
    {
        if (!isset($this->_enumValues)) {
            $this->_enumValues = array();
            foreach ($this->enumerationItems as $key => $item)
            {
                if (is_array($item)) {
                    $this->_enumValues = array_merge($this->_enumValues, array_keys($item));
                } else {
                    $this->_enumValues[] = $key;
                }
            }
        }
        return $this->_enumValues;
    }

    /**
     * Get range's upper boundary.
     *
     * Applies to columns of type Range only.
     *
     * Returns a maximum valid number (upper boundary) for the given range as a value of type float.
     * Returns null if the attribute is undefined.
     *
     * @return  float
     */
    public function getRangeMax()
    {
        if (is_float($this->max)) {
            return $this->max;
        } else {
            return null;
        }
    }

    /**
     * Get range's lower boundary.
     *
     * Applies to columns of type Range only.
     *
     * Returns a minimum valid number (lower boundary) for the given range as a value of type float.
     * Returns null if the attribute is undefined.
     *
     * @return  float
     */
    public function getRangeMin()
    {
        if (is_float($this->min)) {
            return $this->min;
        } else {
            return null;
        }
    }

    /**
     * Get range's step value.
     *
     * Applies to columns of type Range only.
     *
     * Returns step value for the given range as a value of type float.
     * Returns null if the attribute is undefined.
     *
     * @return  float
     */
    public function getRangeStep()
    {
        if (is_float($this->step)) {
            return $this->step;
        } else {
            return null;
        }
    }

    /**
     * Set range values.
     *
     * Applies to columns of type Range only.
     *
     * Sets the minimum, maximum and step values.
     * The minium value must be smaller than the maximum value and vice versa.
     * There must be at least 2 valid values.
     * Step must be greater 0.
     *
     * @param   float  $min   lower boundary
     * @param   float  $max   upper boundary
     * @param   float  $step  step value (defaults to 1.0)
     * @return  \Yana\Db\Ddl\Column
     */
    public function setRange($min, $max, $step = 1.0)
    {
        assert('is_numeric($min)', ' Wrong type for argument 1. Float expected');
        assert('is_numeric($max)', ' Wrong type for argument 2. Float expected');
        assert('is_numeric($step)', ' Wrong type for argument 3. Float expected');
        assert('$step > 0', ' Step must be greater than 0.');
        assert('$min + $step <= $max', ' $min + $step value may not be greater ' .
            'than the $max value, so you have at least 2 valid values.');
        $this->min = (float) $min;
        $this->max = (float) $max;
        $this->step = (float) $step;
        return $this;
    }

    /**
     * check if column has an index
     *
     * Returns bool(true) if there is an index in the parent table, that has an index on this
     * column.
     *
     * FOR INTERNAL USE BY {@see \Yana\Db\FileDb\Driver} ONLY.
     *
     * @return  float
     * @ignore
     */
    public function hasIndex()
    {
        if (!isset($this->hasIndex)) {
            $this->hasIndex = false;
            if (isset($this->parent)) {
                foreach ($this->parent->getIndexes() as $index)
                {
                    $columns = $index->getColumns();
                    if (count($columns) === 1) {
                        $column = array_pop($columns);
                        if ($column->getName() === $this->name) {
                            $this->hasIndex = true;
                            break;
                        }
                    }
                }
            }
        }
        return (bool) $this->hasIndex;
    }

    /**
     * Get auto-filled value.
     *
     * Returns the default value for the column (if there is any).
     * If there is none, NULL is returned.
     *
     * Note: this doesn't automatically get the next sequence-value for columns using
     * auto-increment. Instead the function returns NULL and leaves the validation to the
     * database.
     *
     * @param   string  $dbms   target DBMS, defaults to "generic"
     * @return  mixed
     */
    public function getAutoValue($dbms = "generic")
    {
        $default = $this->getDefault($dbms);
        switch ($this->type)
        {
            // IPs
            case 'inet':
                if ($default === 'REMOTE_ADDR') {
                    // The fall-back is here to avoid errors when the framework is used in command-line mode
                    return (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
                }
                return $default;

            // dates and times
            case 'time':
                if ($default === 'CURRENT_TIMESTAMP') {
                    return date('c');
                }
                return $default;

            case 'date':
                if ($default === 'CURRENT_TIMESTAMP') {
                    return date('Y-m-d');
                }
                return $default;

            case 'timestamp':
                if ($default === 'CURRENT_TIMESTAMP') {
                    return time();
                }
                return $default;

            case 'string':
                switch ($this->name)
                {
                    case 'profile_id':
                        return \Yana\Application::getId();
                    case 'user_created':
                    case 'user_modified':
                        return \Yana\User::getUserName();
                }
                return $default;
            // any other
            default:
                return $default;
        }
    }

    /**
     * Get referenced target column for columns of type "reference".
     *
     * @return  \Yana\Db\Ddl\Column
     * @throws  \Yana\Core\Exceptions\NotFoundException  when the database definition is not found
     */
    public function getReferenceColumn()
    {
        /*
         * if (is foreign-key) then { get target column }
         */
        $refrenceColumn = $this;
        if ($this->getType() === 'reference' && isset($this->parent)) {
            $referenceSettings = $this->getReferenceSettings();
            $tableName = $referenceSettings->getTable();
            if (empty($tableName)) {
                $tableName = $this->parent->getTableByForeignKey($this->name);
                $this->referenceTable = $tableName;
            }
            $columnName = $referenceSettings->getColumn();
            if (empty($columnName)) {
                $columnName = $this->parent->getColumnByForeignKey($this->name);
                $this->referenceColumn = $columnName;
            }
            try {
                /* @var $column \Yana\Db\Ddl\Column */
                $refrenceColumn = $this->getParent()->getParent()->{$tableName}->{$columnName};
                if ($refrenceColumn->getType() === 'reference') {
                    $refrenceColumn = $refrenceColumn->getReferenceColumn();
                }
            } catch (\Exception $e) {
                throw new \Yana\Core\Exceptions\NotFoundException("Database definition not found: " . $e->getMessage());
            }
            unset($tableName, $columnName);
        }
        return $refrenceColumn;
    }

    /**
     * Prepare a database entry for output.
     *
     * @param   mixed   $value  value of the row
     * @param   string  $key    array address (applies to columns of type array only)
     * @param   string  $dbms   target DBMS (e.g. mysql, mssql, ..., generic)
     * @return  bool
     * @ignore
     */
    public function interpretValue($value, $key = "", $dbms = "generic")
    {
        $title = $this->getTitle();
        if (empty($title)) {
            $title = $this->getName();
        }
        $column = $this->getReferenceColumn();
        $type = $column->getType();
        $length = (int) $column->getLength();

        switch ($type)
        {
            case 'array':
            case 'list':
                if (!is_array($value)) {
                    assert('is_string($value);');
                    $value = json_decode($value, true);
                }
                assert('is_array($value)', ' Unexpected result: $value should be an array.');
                if ($key !== "") {
                    $value = \Yana\Util\Hashtable::get($value, mb_strtolower($key));
                    if (is_null($value)) {
                        $value = null;
                    }
                }
                return $value;
            break;
            case 'bool':
                return (!empty($value));
            break;
            case 'date':
                if (!is_string($value)) {
                    return null;
                }
                return strtotime($value);
            break;
            case 'html':
                if (!is_scalar($value)) {
                    return "";
                }
                return htmlspecialchars_decode($value);
            break;
            case 'color':
            case 'enum':
            case 'inet':
            case 'mail':
            case 'password':
            case 'string':
            case 'tel':
            case 'text':
            case 'url':
                if (!is_scalar($value)) {
                    return "";
                }
                return "$value";
            break;
            case 'file':
            case 'image':
                if (empty($value)) {
                    return null;
                }
                return \Yana\Db\Blob::getFilenameFromFileId($value, $type);
            break;
            case 'range':
            case 'float':
                if (!is_numeric($value)) {
                    return null;
                }
                $value = (float) $value;
                assert('!isset($precision)', ' Cannot redeclare var $precision');
                $precision = $column->getPrecision();
                /* apply precision */
                if ($precision > 0) {
                    $value = round($value, $precision);
                }
                unset($precision);
                /* apply unsigned */
                if ($column->isUnsigned()) {
                    $value = abs($value);
                }
                /* apply zerofill (MySQL-compatible)
                 *
                 * Example: FLOAT(6,2) ZEROFILL
                 * -12.1 => 0012.10
                 */
                if ($column->isFixed()) {
                    $length = $column->getLength();
                    // fixed length columns are always unsigned
                    $value = (string) abs($value);
                    $digits = preg_replace('/(\d+)\.(\d+)/', '{$1}{$2}', $value, $number);
                    if ($length > 0 && strlen($digits) < $length) {
                        $value = str_pad($number[1], $length - $precision, '0', STR_PAD_LEFT);
                        $value .= '.';
                        $value = str_pad($number[2], $precision, '0', STR_PAD_RIGHT);
                    }
                }
                return $value;
            break;
            case 'integer':
                if (!is_numeric($value)) {
                    return null;
                }
                $value = (int) $value;
                /* apply unsigned */
                if ($column->isUnsigned()) {
                    $value = abs($value);
                }
                /* apply zerofill (MySQL-compatible)
                 *
                 * Example: INT(4) ZEROFILL
                 * -12 => 0012
                 */
                if ($column->isFixed()) {
                    $length = $column->getLength();
                    // fixed length columns are always unsigned
                    $value = (string) abs($value);
                    if ($length > 0 && mb_strlen($value) < $length) {
                        $value = str_pad($value, $length, '0', STR_PAD_LEFT);
                    }
                }
                return $value;
            break;
            case 'time':
                if (!is_string($value)) {
                    return null;
                }
                return strtotime($value);
            break;
            case 'timestamp':
                if (!is_numeric($value)) {
                    return null;
                }
                return (int) $value;
            break;
            default:
                \Yana\Log\LogManager::getLogger()->addLog("Unknown column type '{$column->getType()}'.");
                return null;
            break;
        }
        throw null;
    }

    /**
     * Serialize this object to XDDL.
     *
     * Returns the serialized object as a string in XML-DDL format.
     *
     * Note: parent of each column is expected to be a declaration-tag. If the parent is another tag
     * this function will create an empty declaration tag in the parent and add the column there.
     * If another declaration tag exists, it will add the column to the existing tag.
     *
     * If no parent tag is given, it will just return the column.
     *
     * @param   \SimpleXMLElement $parentNode  parent node
     * @return  \SimpleXMLElement
     */
    public function serializeToXDDL(\SimpleXMLElement $parentNode = null)
    {
        if ($this->xddlTag === 'file' || $this->xddlTag === 'image') {
            if (isset($this->size)) {
                $this->_maxsize = $this->size;
            } else {
                $this->_maxsize = null;
            }
        } else {
            if (isset($this->size)) {
                $this->_length = $this->size;
            } else {
                $this->_length = null;
            }
        }
        // parent is given, but is not declaration tag
        if (!is_null($parentNode)) {
            assert('!isset($name)', ' Cannot redeclare var $name');
            $name = $parentNode->getName();
            if ($name !== 'declaration' && $name !== 'input') {
                // parent has a declaration tag
                if ($parentNode->declaration) {
                    $parentNode = $parentNode->declaration;

                // create missing declaration tag
                } else {
                    $parentNode = $parentNode->addChild('declaration');
                }
            }
            unset($name);
        }
        $node = parent::serializeToXDDL($parentNode);
        // add enumeration items if there are any
        if (!empty($this->enumerationItems)) {
            self::_serializeOptions($node, $this->enumerationItems);
        }
        return $node;
    }

    /**
     * Serialize options and optgroups to \SimpleXMLElement.
     *
     * @param   \SimpleXMLElement  $node   node to serialize items to
     * @param   array              $items  list of option and optgroup items
     */
    private static function _serializeOptions(\SimpleXMLElement $node, array $items)
    {
        foreach ($items as $key => $item)
        {
            if (is_array($item)) {
                $optgroup = $node->addChild('optgroup');
                $optgroup->addAttribute('label', $key);
                self::_serializeOptions($optgroup, $item);
                unset($optgroup);
            } else {
                $option = $node->addChild('option', \Yana\Util\String::htmlEntities($item));
                if ($key !== $item) {
                    $option->addAttribute('value', $key);
                }
                unset($option);
            }
        }
    }

    /**
     * unserialize a XDDL-node to an object
     *
     * Returns the unserialized object.
     *
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed              $parent  parent node (if any)
     * @return  \Yana\Db\Ddl\Table
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null)
    {
        // unserialize single column
        if ($node->getName() !== 'declaration') {
            $attributes = $node->attributes();
            if (!isset($attributes['name'])) {
                throw new \Yana\Core\Exceptions\InvalidArgumentException("Missing name attribute.", E_USER_WARNING);
            }
            $ddl = new self((string) $attributes['name'], $parent);
            $ddl->_unserializeFromXDDL($node);
            $ddl->type = $node->getName();
            // default settings
            foreach (array_keys($ddl->default) as $i)
            {
                if (is_int($i)) {
                    $ddl->default['generic'] = $ddl->default[$i];
                    unset($ddl->default[$i]);
                }
            }
            if (isset($ddl->_maxsize)) {
                $ddl->size = $ddl->_maxsize;
            } elseif (isset($ddl->_length)) {
                $ddl->size = $ddl->_length;
            }
            return $ddl;

        // unserialize list of columns
        } else {
            $columns = array();
            foreach ($node->children() as $child)
            {
                $column = self::unserializeFromXDDL($child, $parent);
                $column->enumerationItems = self::_unserializeOptions($child);
                $columns[] = $column;
            }
            unset($child);
            return $columns;
        }
    }

    /**
     * unserialize column's option nodes
     *
     * @param   \SimpleXMLElement  $node   column node
     * @return  array
     */
    private static function _unserializeOptions(\SimpleXMLElement $node)
    {
        $items = array();
        foreach ($node->children() as $child)
        {
            /* @var $child \SimpleXMLElement */
            switch ($child->getName())
            {
                case 'option':
                    if (isset($child['value'])) {
                        $key = (string) $child['value'];
                        $value = (string) $child;
                    } else {
                        $key = $value = (string) $child;
                    }
                    $items[$key] = $value;
                    unset($key, $value);
                break;
                case 'optgroup':
                    $key = (string) $child['label'];
                    $items[$key] =  self::_unserializeOptions($child);
                break;
            }
        } // end foreach
        return $items;
    }

}

?>