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

namespace Yana\Db;

/**
 * Transparent provider, that delivers pre-initialized \DomDocument instances.
 *
 * All templates provided through this class are meant to transform XDDL to SQL.
 *
 * @package     yana
 * @subpackage  db
 * @property-read \DOMDocument $mysql      MySQL XSL-Template
 * @property-read \DOMDocument $postgresql PostGreSQL XSL-Template
 */
class DomDocumentProvider extends \Object
{

    /**
     * @var \DOMDocument[] 
     */
    private static $_instances = array();

    /**
     * MySQL
     */
    const MYSQL = 'mysql';

    /**
     * PostGreSQL
     */
    const POSTGRESQL = 'postgresql';

    /**
     * Creates a new \DOMDocument of an XSL-template.
     *
     * @param string $name
     * @return \DOMDocument
     * @throws \Yana\Core\UndefinedPropertyException
     */
    public function __get($name)
    {
        $name = strtolower($name);
        if (!isset(self::$_instances[$name])) {

            $xslFilename = __DIR__ . '/';
            switch ($name)
            {
                case 'mysql':
                case 'postgresql':
                    $xslFilename .= $name . ".xsl";
                    break;
                default:
                    throw new \Yana\Db\DomProvider\InvalidNameException($name);
            }
            // Stylesheet
            $xsl = new \DOMDocument();
            $xsl->load($xslFilename);
            self::$_instances[$name] = $xsl;
        }
        return self::$_instances[$name];
    }

}

?>