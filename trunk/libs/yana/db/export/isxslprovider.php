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

namespace Yana\Db\Export;

/**
 * <<interface>> A XSL-DOMDocument provider.
 *
 * It creates and returns an instance of DOMDocument,
 * containing the XSL-Template for the requested DBMS.
 *
 * <code>
 * $domDocument = $isXslProvider->mysql;
 * // or
 * $domDocument = $isXslProvider->{IsXslProvider::MYSQL};
 * // or
 * $domDocument = $isXslProvider->__get(IsXslProvider::MYSQL);
 * </code>
 *
 * @package     yana
 * @subpackage  core
 */
interface IsXslProvider
{
    /**
     * MySQL
     */
    const MYSQL = 'mysql';

    /**
     * PostGreSQL
     */
    const POSTGRESQL = 'postgresql';

    /**
     * Microsoft SQL-Server
     */
    const MSSQL = 'mssql';

    /**
     * Oracle Database
     */
    const ORACLEDB = 'oracle';

    /**
     * IBM DB2
     */
    const DB2 = 'db2';

    /**
     * Creates a new \DOMDocument of an XSL-template.
     *
     * @param string $name
     * @return \DOMDocument
     * @throws \Yana\Db\Export\Xsl\InvalidNameException  if no document with the given name is found
     */
    public function __get($name);

}

?>