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

namespace Yana\Core\Autoloaders;

/**
 * <<abstract>> Base class for mapping class names to file paths.
 *
 * @package     yana
 * @subpackage  core
 */
abstract class AbstractMapper extends \Yana\Core\Object
    implements \Yana\Core\Autoloaders\IsMapper
{

    /**
     * The namespace this mapper reacts to.
     *
     * @var  string
     */
    private $_nameSpace = "";

    /**
     * As absolute path.
     *
     * @var  string
     */
    private $_baseDirectory = "";

    /**
     * PHP-extension including the dot.
     *
     * @var  string
     */
    private $_fileExtension = ".php";

    /**
     * A prefix to be put in front of the filename.
     *
     * @var  string
     */
    private $_filePrefix = "";

    /**
     * Get absolute path of base-directory including final seperator.
     *
     * @return  string
     */
    public function getBaseDirectory()
    {
        return $this->_baseDirectory;
    }

    /**
     * Get file extension including the dot.
     *
     * @return  string
     */
    public function getFileExtension()
    {
        return $this->_fileExtension;
    }

    /**
     * Get a prefix to put in front of the file-name.
     *
     * @return  string
     */
    public function getFilePrefix()
    {
        return $this->_filePrefix;
    }

    /**
     * The namespace this mapper reacts to.
     *
     * @return  string
     */
    public function getNameSpace()
    {
        return $this->_nameSpace;
    }

    /**
     * Set the path to the directory where the files are to be found.
     *
     * Important note! You HAVE to add the final directory seperator at this point.
     * 
     * @param  string  $baseDirectory  as absolute path
     * @return \Yana\Core\Autoloaders\AbstractMapper
     */
    public function setBaseDirectory($baseDirectory)
    {
        assert('is_string($baseDirectory)', ' Invalid input $baseDirectory. String expected');
        $this->_baseDirectory = (string) $baseDirectory;
        return $this;
    }

    /**
     * Set the extension for your PHP files use including the dot.
     *
     * Usually this is ".php", which is also the default.
     *
     * @param   string  $fileExtension  should start with a dot
     * @return  \Yana\Core\Autoloaders\IsMapper
     */
    public function setFileExtension($fileExtension)
    {
        assert('is_string($fileExtension)', ' Invalid input $fileExtension. String expected');
        $this->_fileExtension = (string) $fileExtension;
        return $this;
    }

    /**
     * Set a prefix to put in front of the filename.
     *
     * For example if your class-files are named "class.foobar.php" you set this
     * to "class.".
     *
     * @param   string  $filePrefix  the new prefix
     * @return  \Yana\Core\Autoloaders\IsMapper
     */
    public function setFilePrefix($filePrefix)
    {
        assert('is_string($filePrefix)', ' Invalid input $filePrefix. String expected');
        $this->_filePrefix = (string) $filePrefix;
        return $this;
    }

    /**
     * Set namespace that mapper will be limited to.
     * 
     * @param   string  $nameSpace  including final namespace separator
     * @return  \Yana\Core\Autoloaders\IsMapper
     */
    public function setNameSpace($nameSpace)
    {
        assert('is_string($nameSpace)', ' Invalid input $nameSpace. String expected');
        $this->_nameSpace = (string) $nameSpace;
        return $this;
    }

}
