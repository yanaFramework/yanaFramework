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

namespace Yana\Security\Passwords\Builders;

/**
 * <<builder>> Helps creating password-algorithm instances.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class Builder extends \Yana\Core\Object implements \Yana\Security\Passwords\Builders\IsBuilder
{

    /**
     * @var  \Yana\Security\Passwords\IsAlgorithm
     */
    protected $_algorithm = null;

    /**
     * Make this algorithm the new main and any previous the fallback.
     *
     * @param   string  $algorithmName  of hash-algorithm
     * @return  \Yana\Security\Passwords\Builders\IsBuilder
     * @throws  \Yana\Core\Exceptions\NotImplementedException  when the desired algorithm isn't found
     */
    public function add($algorithmName)
    {
        switch ($algorithmName)
        {
            case \Yana\Security\Passwords\Builders\Enumeration::BASIC:
                $this->_algorithm = new \Yana\Security\Passwords\BasicAlgorithm($this->_algorithm);
            break;

            case \Yana\Security\Passwords\Builders\Enumeration::BCRYPT:
            case \Yana\Security\Passwords\Builders\Enumeration::BLOWFISH:
                $this->_algorithm = new \Yana\Security\Passwords\BcryptAlgorithm($this->_algorithm);
            break;

            case \Yana\Security\Passwords\Builders\Enumeration::SHA256:
                $this->_algorithm = new \Yana\Security\Passwords\Sha256Algorithm($this->_algorithm);
            break;

            case \Yana\Security\Passwords\Builders\Enumeration::SHA512:
                $this->_algorithm = new \Yana\Security\Passwords\Sha512Algorithm($this->_algorithm);
            break;

            default:
                throw new \Yana\Core\Exceptions\NotImplementedException("No suitable hash algorithm found");
        }
        return $this;
    }

    /**
     * Return algorithm.
     *
     * @return  \Yana\Security\Passwords\IsAlgorithm
     * @throws  \Yana\Core\Exceptions\NotImplementedException  when the desired algorithm isn't found
     */
    public function __invoke()
    {
        if (!isset($this->_algorithm)) {
            throw new \Yana\Core\Exceptions\NotImplementedException("Need to add hash algorithm first");
        }
        return $this->_algorithm;
    }

}

?>