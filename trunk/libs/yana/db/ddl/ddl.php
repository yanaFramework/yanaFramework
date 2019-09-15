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

namespace Yana\Db\Ddl;

/**
 * This wrapper class represents the structure of a database.
 *
 * @package     yana
 * @subpackage  db
 */
abstract class DDL extends \Yana\Core\Object
{
    /**
     * File extensions
     *
     * @var  string
     * @ignore
     */
    protected static $extension = ".db.xml";

    /**
     * tag name
     *
     * This setting is used for persistance mapping: object <-> XDDL
     *
     * @var  string
     */
    protected $xddlTag = null;

    /**
     * class name must match exactly
     *
     * @var  bool
     */
    protected $exactMatch = false;

    /**
     * list of XML-attributes
     *
     * There settings are used for persistance mapping: object <-> XDDL
     *
     * 2-dimensional, associative array, where each row presents a XML attribute.
     * They key to each row is the attribute name.
     *
     * Each row may have 2 values:
     * <ul>
     *   <li> Object property name </li>
     *   <li> Property type (string, bool, int, float, nmtoken, array) </li>
     * </ul>
     *
     * The type "nmtoken" refers to an identifier (like "name"), while "array" is a comma-seperated
     * list of identifiers.
     *
     * @var     array
     * @see     \Yana\Db\Ddl\Ddl::_unserializeFromXDDL()
     * @ignore
     */
    protected $xddlAttributes = array();

    /**
     * list of XML-tags
     *
     * There settings are used for persistance mapping: object <-> XDDL
     *
     * 2-dimensional, associative array, where each row presents a XML tag.
     * They key to each row is the tag name.
     *
     * Each row may have 6 values:
     * <ul>
     *   <li> Object property name </li>
     *   <li> Property type (string, bool, int, float, array, class name) </li>
     *   <li> (optional) class name (for each item of an array only) </li>
     *   <li> (optional) name of key column for array items </li>
     *   <li> (optional) name of value column for array items (only if array has scalar values) </li>
     *   <li> (optional) default key value </li>
     * </ul>
     *
     * The classes must all be sub-classes of DDL. These MUST implement \Yana\Db\Ddl\Ddl::unserializeFromXDDL()
     * and MAY overwrite \Yana\Db\Ddl\Ddl::serializeToXDDL().
     *
     * @var     array
     * @see     \Yana\Db\Ddl\Ddl::_unserializeFromXDDL()
     * @ignore
     */
    protected $xddlTags = array();

    /**
     * database base directory
     *
     * @var     string
     * @ignore
     */
    protected static $databaseDirectory = null;

    /**
     * Get a string representation of this object.
     *
     * @return  string
     * @codeCoverageIgnore
     */
    public function __toString()
    {
        try {
            $xml = $this->serializeToXDDL();
            return $xml->asXML();
        } catch (\Exception $e) {
            return $e->getMessage(); // must not throw an exception
        }
    }

    /**
     * Serializes this object to a string in XML-DDL format.
     *
     * @param   \SimpleXMLElement $parentNode  parent node
     * @return  \SimpleXMLElement
     * @throws  \Yana\Db\Ddl\NoTagNameException  when no tag name was given for this node
     */
    public function serializeToXDDL(\SimpleXMLElement $parentNode = null)
    {
        if (empty($this->xddlTag)) {
            $message = "This node cannot be serialized to XDDL format because no tag name was given.";
            throw new \Yana\Db\Ddl\NoTagNameException($message, \Yana\Log\TypeEnumeration::WARNING);
        }

        $xddl = null;
        // is root node
        if (is_null($parentNode)) {
            $xddl = new \SimpleXMLElement('<' . $this->xddlTag . '/>', LIBXML_NOXMLDECL | LIBXML_NOENT);

        // is inner node with pcdata-section
        } elseif (isset($this->xddlAttributes['#pcdata'])) {
            $property = $this->xddlAttributes['#pcdata'][0];
            $value = (string) $this->$property;
            $xddl = $parentNode->addChild($this->xddlTag, $value);
            unset($property, $value);

        // is inner node with children
        } else {
            $xddl = $parentNode->addChild($this->xddlTag);
        }

        // set attributes
        if (!empty($this->xddlAttributes)) {
            $this->_serializeAttributes($xddl);
        }

        // set children
        if (!empty($this->xddlTags)) {
            $this->_serializeChildren($xddl);
        }
        assert($xddl instanceof \SimpleXMLElement);
        return $xddl;
    }

    /**
     * serialize attributes
     *
     * @final
     * @param   \SimpleXMLElement  $node  XML node
     * @ignore
     */
    final protected function _serializeAttributes(\SimpleXMLElement $node)
    {
        foreach ($this->xddlAttributes as $name => $attribute)
        {
            if ($name !== '#pcdata') {
                $property = $attribute[0];
                $type = $attribute[1];
                if (isset($this->$property)) {
                    $value = null;
                    switch ($type)
                    {
                        case 'array':
                            if (!empty($this->$property)) {
                                $value = (string) implode(',', $this->$property);
                            }
                        break;
                        case 'bool':
                            if ($this->$property) {
                                $value = 'yes';
                            } else {
                                $value = 'no';
                            }
                        break;
                        default:
                            if (!is_null($this->$property) && $this->$property !== "") {
                                $value = (string) $this->$property;
                            }
                        break;
                    }
                    if (is_string($value)) {
                        $node->addAttribute($name, (string) $value);
                    }
                    unset($value);
                }
                unset($name, $attribute);
            }
        }
    }

    /**
     * serialize child nodes
     *
     * @final
     * @param   \SimpleXMLElement  $node  XML node
     * @ignore
     */
    final protected function _serializeChildren(\SimpleXMLElement $node)
    {
        foreach ($this->xddlTags as $name => $tag)
        {
            $property = $tag[0];
            $type = $tag[1];
            // skip undefined tags
            if (is_null($this->$property)) {
                continue;
            }
            // switch by data type
            switch ($type)
            {
                case 'string':
                case 'nmtoken':
                case 'int':
                case 'float':
                    $node->addChild($name, (string) $this->$property);
                break;
                case 'bool':
                    if ($this->$property) {
                        $node->addChild($name, 'yes');
                    } else {
                        $node->addChild($name, 'no');
                    }
                break;
                case 'array':
                    // is list of complex child-tags
                    if (isset($tag[2])) {
                        assert('!isset($className); // Cannot redeclare var $className');
                        $className = $tag[2];
                        foreach ($this->$property as $object)
                        {
                            if (! $object instanceof $className) {
                                continue;
                            }
                            if ($object->exactMatch && strcasecmp(get_class($object), $className) !== 0) {
                                continue;
                            }
                            if ($object instanceof \Yana\Db\Ddl\IsIncludableDDL && $this !== $object->getParent()) {
                                continue;
                            }
                            $object->serializeToXDDL($node);
                        }
                        unset($className);

                    // is list of string tags
                    } else {
                        // attribute name for keys
                        $keyAttr = null;
                        if (isset($tag[3])) {
                            $keyAttr = $tag[3];
                        }
                        $valAttr = null;
                        // attribute name for values
                        if (isset($tag[4])) {
                            $valAttr = $tag[4];
                        }
                        // iterate through
                        assert('!isset($key);');
                        assert('!isset($value);');
                        assert('!isset($childNode);');
                        $childNode = null;
                        foreach ($this->$property as $key => $value)
                        {
                            if (is_null($valAttr)) {
                                $childNode = $node->addChild($name, (string) $value);
                            } else {
                                $childNode = $node->addChild($name);
                                $childNode->addAttribute($valAttr, (string) $value);
                            }
                            if (!is_null($keyAttr)) {
                                if (!is_string($key) && $keyAttr === 'name') {
                                    continue;
                                }
                                if ($key !== '' && (is_string($key) || $key != $value) && $keyAttr != $valAttr) {
                                    $childNode->addAttribute($keyAttr, (string) $key);
                                }
                            }
                        }
                        unset($key, $value, $childNode);
                    }
                break;
                default:
                    if (is_array($this->$property)) {
                        foreach ($this->$property as $object)
                        {
                            $object->serializeToXDDL($node);
                        }
                    } elseif (!is_null($this->$property)) {
                        $object = $this->$property;
                        $object->serializeToXDDL($node);
                    }
                break;
            }

        }
        unset($name, $tag);
    }
    /**
     * Unserialize a XDDL-node to an object and returns it.
     *
     * @param   \SimpleXMLElement  $node  node create via \Yana\Util\XMLArray::toArray()
     * @return  \Yana\Db\Ddl\DDL
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node)
    {
        // Note: as of PHP 5.2 static functions may not be declared abstract
    }

    /**
     * Unserialize a XDDL-node to an object and returns it.
     *
     * @final
     * @param   \SimpleXMLElement  $node  XML node
     * @ignore
     */
    final protected function _unserializeFromXDDL(\SimpleXMLElement $node)
    {
        $this->_unserializeAttributes($node);
        foreach ($node->children() as $currentNode)
        {
            $this->_unserializeChild($currentNode);
        }
    }

    /**
     * unserialize child node
     *
     * @final
     * @param   \SimpleXMLElement  $node  XML node
     * @ignore
     */
    final protected function _unserializeChild(\SimpleXMLElement $node)
    {
        $xml = $node->getName();
        $attributes = $node->attributes();
        if (isset($this->xddlTags[$xml])) {
            $tag = $this->xddlTags[$xml];
            $property = $tag[0];
            $type = $tag[1];
            switch ($type)
            {
                case 'nmtoken':
                    $this->$property = mb_strtolower("$node");
                break;
                case 'string':
                    $this->$property = trim("$node");
                break;
                case 'array':
                    $array =& $this->$property;
                    $index = 'name';
                    if (isset($tag[3])) {
                        $index = $tag[3];
                    }
                    $id = null;
                    if (isset($attributes->$index)) {
                        $id = (string) $attributes->$index;
                    }
                    unset($index);
                    $value = null;
                    if (isset($tag[2])) {
                        if (!is_null($id)) {
                            $id = mb_strtolower($id);
                        }
                        $class = $tag[2];
                        $value = call_user_func(array($class, 'unserializeFromXDDL'), $node, $this);
                    } elseif (isset($tag[4])) {
                        $index = $tag[4];
                        if (isset($attributes->$index)) {
                            $value = (string) $attributes->$index;
                        } else {
                            $value = "";
                        }
                        unset($index);
                    } else {
                        $value = trim("$node");
                    }
                    if (!is_null($id)) {
                        $array["$id"] = $value;
                    } elseif (isset($tag[5])) {
                        $array[$tag[5]] = $value;
                    } else {
                        $array[] = $value;
                    }
                break;
                case 'bool':
                    $this->$property = ("$node" === 'yes');
                break;
                case 'int':
                    $this->$property = (int) "$node";
                break;
                case 'float':
                    $this->$property = (float) "$node";
                break;
                default:
                    $object = call_user_func(array($type, 'unserializeFromXDDL'), $node, $this);
                    $this->$property = $object;
                break;
            }
        }
    }

    /**
     * unserialize attributes
     *
     * @final
     * @param   \SimpleXMLElement  $node  XML node
     * @ignore
     */
    final protected function _unserializeAttributes(\SimpleXMLElement $node)
    {
        $attributes = $node->attributes();
        // set attributes
        foreach ($this->xddlAttributes as $xml => $attribute)
        {
            $value = null;
            $property = $attribute[0];
            $type = $attribute[1];
            if ($xml === '#pcdata') {
                $value = trim("$node");
            } elseif (isset($attributes->$xml)) {
                $value = (string) $attributes->$xml;
            }
            if (isset($value)) {
                switch ($type)
                {
                    case 'nmtoken':
                        $this->$property = mb_strtolower($value);
                    break;
                    case 'array':
                        $value = preg_split('/, ?/', $value);
                        $this->$property = $value;
                    break;
                    case 'bool':
                        $this->$property = ($value === 'yes');
                    break;
                    case 'int':
                        $this->$property = (int) $value;
                    break;
                    case 'float':
                        $this->$property = (float) $value;
                    break;
                    default:
                        $this->$property = (string) $value;
                    break;
                }
            } // end if
        } // end foreach
    }

    /**
     * get database directory
     *
     * Returns the path to the directory where XDDL files are to be stored.
     * The path is configured using the setting "DBDIR" in the system configuration.
     * You may overwrite this setting by calling \Yana\Db\Ddl\Ddl::setDirectory().
     *
     * @return  string
     */
    public static function getDirectory()
    {
        if (!isset(self::$databaseDirectory)) {
            $builder = new \Yana\ApplicationBuilder();
            $application = $builder->buildApplication();
            self::$databaseDirectory = getcwd() . '/' . $application->getVar('DBDIR');
        }
        return self::$databaseDirectory;
    }

    /**
     * set database directory
     *
     * Set the path to the directory where XDDL files are to be stored.
     *
     * @param   string  $directory  path to XDDL base directory
     */
    public static function setDirectory($directory)
    {
        assert('is_string($directory); // Wrong argument type argument 1. String expected');
        assert('is_dir($directory); // Wrong argument type argument 1. Directory expected');

        self::$databaseDirectory = "$directory";
    }

    /**
     * get file path from database name
     *
     * Looks up the source path for the wanted database in the file system and returns the path.
     *
     * @param   string  $databaseName   database name
     * @return  string
     */
    public static function getPath($databaseName)
    {
        assert('is_string($databaseName); // Wrong type for argument 1. String expected');
        if (!preg_match('/^([\w\d_]+)$/', $databaseName)) {
            return "$databaseName";
        }
        $file = self::getDirectory() . "$databaseName" . \Yana\Db\Ddl\DDL::$extension;
        assert('is_file($file); // File not found: ' . $file);
        return $file;
    }

    /**
     * get database name from file path
     *
     * Returns a unique identifier for the given file path as a string.
     * Actually this is the filename for the XDDL file.
     *
     * @param   string  $path   database path
     * @return  string
     */
    public static function getNameFromPath($path)
    {
        assert('is_string($path); // Wrong type for argument 1. String expected');
        return basename("$path", \Yana\Db\Ddl\DDL::$extension);
    }

    /**
     * return list of known DDL files
     *
     * This function returns a numeric list of filenames of known DDL files.
     * Each item is a valid argument for creating a new database connection.
     *
     * If the argument $useFullFilename is set to bool(true) the items are complete filenames,
     * including the path, relative to the framework's root directory.
     * Otherwise only the file names are returned, which are easier to read for humans.
     *
     * In case of an unexpected error, this function returns an empty array.
     *
     * @param   bool  $useFullFilename  return items as full filenames (true = yes, false = no)
     * @return  array
     */
    public static function getListOfFiles($useFullFilename = false)
    {
        assert('is_bool($useFullFilename); // Wrong type for argument 1. Boolean expected');
        $dbDir = self::getDirectory();
        $list = array();
        $dirList = glob($dbDir . "/*" . \Yana\Db\Ddl\DDL::$extension);
        if (is_array($dirList)) {
            foreach ($dirList as $filename)
            {
                /* prepend path */
                if ($useFullFilename) {
                    $list[] = $filename;

                /* remove suffix: '.db.xml' */
                } else {
                    $list[] = basename($filename, \Yana\Db\Ddl\DDL::$extension);
                }
            }
        }
        return $list;
    }
}

?>