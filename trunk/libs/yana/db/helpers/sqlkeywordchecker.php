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

namespace Yana\Db\Helpers;

/**
 * <<algorithm>> Checks whether the given string is a known SQL keyword.
 *
 * Note that the function has O(log(n)) running time.
 *
 * @package     yana
 * @subpackage  db
 */
class SqlKeywordChecker extends \Yana\Core\StdObject implements \Yana\Db\Helpers\IsSqlKeywordChecker
{

    /**
     * @var  array
     */
    private $_listOfKeywords = array();

    /**
     * <<constructor>> Initialize list of keywords.
     *
     * @param  array  $listOfKeywords  numeric array of UPPER CASE reserved SQL keywords
     */
    public function __construct(array $listOfKeywords)
    {
        $this->_listOfKeywords = $listOfKeywords;
    }

    /**
     * Get list of known reserved SQL keywords.
     *
     * @return  array
     */
    protected function _getListOfKeywords(): array
    {
        return $this->_listOfKeywords;
    }

    /**
     * Return bool(true) if the string is a reserved keyword.
     *
     * @param   string  $id  to test
     * @return  bool
     */
    public function isSqlKeyword(string $id): bool
    {
        $listOfKeywords = $this->_getListOfKeywords();
        if (empty($listOfKeywords)) {
            return false;
        }

        $idUpperCase = mb_strtoupper($id);
        return (bool) (\Yana\Util\Hashtable::quickSearch($listOfKeywords, $idUpperCase) !== false);
    }

    /**
     * Create instance from file.
     *
     * @param   string  $filename  path to text file where each line is a SQL keyword
     * @return  \Yana\Db\Helpers\IsSqlKeywordChecker
     * @codeCoverageIgnore
     */
    public static function createFromApplicationDefault(): \Yana\Db\Helpers\IsSqlKeywordChecker
    {
        $builder = new \Yana\ApplicationBuilder();
        $application = $builder->buildApplication();
        /* Load list of reserved SQL keywords (required for smart id quoting) */
        if (isset($application)) {
            $filename = (string) $application->getResource('system:/config/reserved_sql_keywords.file')->getPath();
        }

        if (is_string($filename) && is_file($filename)) {
            $reservedSqlKeywords = file($filename);
            if (!is_array($reservedSqlKeywords)) {
                $reservedSqlKeywords = array();
            }
        }

        return self::createFromFile($filename);
    }

    /**
     * Create instance from file.
     *
     * The input must be alphabetically sorted in ascending order.
     *
     * @param   string  $filename  path to text file where each line is a SQL keyword
     * @return  \Yana\Db\Helpers\IsSqlKeywordChecker
     * @codeCoverageIgnore
     */
    public static function createFromFile(string $filename): \Yana\Db\Helpers\IsSqlKeywordChecker
    {
        assert('!isset($reservedSqlKeywords); // Cannot redeclare $reservedSqlKeywords');
        $reservedSqlKeywords = array();

        if (is_string($filename) && is_file($filename) && is_readable($filename)) {
            $reservedSqlKeywords = file($filename);
            if (!is_array($reservedSqlKeywords)) {
                $reservedSqlKeywords = array();
            }
        }
        assert('!isset($instance); // Cannot redeclare $instance');
        $instance = new self($reservedSqlKeywords);
        return $instance;
    }

}

?>