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
 *
 * @ignore
 */
declare(strict_types=1);

namespace Yana\Security\Passwords\Providers;

/**
 * Authentication provider setup data-adapter.
 *
 * This persistent class provides access to setup data to configure classes that handle password checks and password changes.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class Adapter extends \Yana\Security\Passwords\Providers\AbstractAdapter
{

    /**
     * <<construct>> Creates a new authentication provider manager.
     *
     * If no mapper is given, this function creates and uses an instance of \Yana\Security\Passwords\Providers\Mapper.
     *
     * @param  \Yana\Db\IsConnection               $connection  database connection to schema user
     * @param  \Yana\Data\Adapters\IsEntityMapper  $mapper      simple OR-mapper to convert database entries to objects
     */
    public function __construct(\Yana\Db\IsConnection $connection, \Yana\Data\Adapters\IsEntityMapper $mapper = null)
    {
        if (\is_null($mapper)) {
            $mapper = new \Yana\Security\Passwords\Providers\Mapper();
        }
        parent::__construct($connection, $mapper);
    }

    /**
     * Returns the name of the target table.
     *
     * @return  string
     */
    protected function _getTableName()
    {
        return \Yana\Security\Data\Tables\AuthenticationProviderEnumeration::TABLE;
    }

    /**
     * Serializes the entity object to a table-row.
     *
     * @param   \Yana\Data\Adapters\IsEntity  $entity  object to convert
     * @return  array
     */
    protected function _serializeEntity(\Yana\Data\Adapters\IsEntity $entity)
    {
        return $this->_getEntityMapper()->toDatabaseRow($entity);
    }

    /**
     * Unserializes the table-row to an entity object.
     *
     * @param   array  $dataSet  table row to convert
     * @return  \Yana\Data\Adapters\IsEntity
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the given data is invalid
     */
    protected function _unserializeEntity(array $dataSet)
    {
        $entity = $this->_getEntityMapper()->toEntity($dataSet);
        $entity->setDataAdapter($this);
        return $entity;
    }

    /**
     * Loads and returns an authentication provider setup from the database.
     *
     * @param   scalar  $providerId  database ID
     * @return  \Yana\Security\Passwords\Providers\IsEntity
     * @throws  \Yana\Core\Exceptions\NotFoundException  when no such database entry exists
     */
    public function offsetGet($providerId)
    {
        assert(is_scalar($providerId), 'Wrong type argument $providerId. Integer expected.');

        try {
            return parent::offsetGet((int) $providerId);

        } catch (\Yana\Core\Exceptions\InvalidArgumentException $e) {

            $message = "No authentication provider setup found with id: " . (int) $providerId;
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Core\Exceptions\NotFoundException($message, $level, $e);
        }
    }

    /**
     * Write an authentication provider setup to the database.
     *
     * @param  scalar                                       $providerId  database ID
     * @param  \Yana\Security\Passwords\Providers\IsEntity  $entity      the setup data
     * @return \Yana\Security\Passwords\Providers\IsEntity
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the value is not a valid entity
     * @throws  \Yana\Db\DatabaseException                      if the commit statement failed
     */
    public function offsetSet($providerId, $entity)
    {
        assert(is_scalar($providerId), 'Wrong type argument $userId. Integer expected.');

        if (!($entity instanceof \Yana\Security\Passwords\Providers\IsEntity)) {
            assert(!isset($className), 'Cannot redeclare var $className');
            $className = \is_object($entity) ? \get_class($entity) : \gettype($entity);
            assert(!isset($message), 'Cannot redeclare var $message');
            $message = "Instance of \Yana\Security\Passwords\Providers\IsEntity expected. Found " . $className . " instead.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }

        return parent::offsetSet((int) $providerId, $entity);
    }

    /**
     * Tries to delete the authentication provider setup from the database.
     *
     * @param  scalar  $providerId  database ID
     * @throws  \Yana\Db\Queries\Exceptions\NotDeletedException  when there was a problem with the database
     */
    public function offsetUnset($providerId)
    {
        assert(is_scalar($providerId), 'Wrong type argument $userId. Integer expected.');

        assert(!isset($db), 'Cannot redeclare var $db');
        $db = $this->_getDatabaseConnection();
        try {
            $db->remove(\Yana\Security\Data\Tables\ProfileEnumeration::TABLE . "." . (int) $providerId)
                ->commit(); // may throw exception

        } catch (\Yana\Core\Exceptions\NotFoundException $e) {

            $db->rollback(); // The entry we are trying to delete doesn't exist. (Mission accomplished?)

        } catch (\Exception $e) {

            $message = "Unable to commit changes to the database server while trying to remove authentication provider setup: " . (int) $providerId;
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Db\Queries\Exceptions\NotDeletedException($message, $level, $e);
        }
    }

    /**
     * Return the number of authentication provider setups in the database.
     *
     * @return  int
     */
    public function count()
    {
        return $this->_getDatabaseConnection()->length($this->_getTableName());
    }

    /**
     * Return an array of all valid identifiers.
     *
     * @return  array
     */
    public function getIds()
    {
        assert(!isset($key), 'Cannot redeclare var $key');
        $key = $this->_getTableName() . '.*.' . \Yana\Security\Data\Tables\AuthenticationProviderEnumeration::ID;
        return $this->_getDatabaseConnection()->select($key);
    }

    /**
     * Saves the authentication provider setup to the database.
     *
     * @param  \Yana\Data\Adapters\IsEntity  $entity  object to persist
     * @throws \Yana\Core\Exceptions\InvalidArgumentException  when the entity is invalid
     */
    public function saveEntity(\Yana\Data\Adapters\IsEntity $entity)
    {
        $this->offsetSet(null, $entity);
    }

    /**
     * Build a provider entity based on a given user name.
     * 
     * @param   string $userId  the name/id of the user
     * @return \Yana\Security\Passwords\Providers\IsEntity
     */
    public function getFromUserName(string $userId): \Yana\Security\Passwords\Providers\IsEntity
    {
        $db = $this->_getDatabaseConnection();
        $select = new \Yana\Db\Queries\Select($db);
        $select
                ->setTable(\Yana\Security\Data\Tables\UserEnumeration::TABLE)
                ->setRow($userId)
                ->setColumn(\Yana\Security\Data\Tables\UserEnumeration::AUTHENTICATION_ID);
        $providerId = (int) $select->getResults();
        if ($providerId > 0) {
            $entity = $this->offsetGet($providerId);
        } else {
            $entity = new \Yana\Security\Passwords\Providers\Entity();
        }
        return $entity;
    }

}

?>