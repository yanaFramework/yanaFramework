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

namespace Yana\Views\Managers;

/**
 * Manager class to automate searching and loading of templates, belonging to view layer.
 *
 * @package     yana
 * @subpackage  views
 */
abstract class AbstractManager extends \Yana\Core\StdObject
{

    /**
     * List of stylesheets.
     *
     * @var  array
     */
    private $_styles = array();

    /**
     * List of script files.
     *
     * @var  array
     */
    private $_scripts = array();

    /**
     * Add path to CSS stylesheet file.
     *
     * @param   string  $file  path and file name
     * @return  $this
     */
    public function addStyle($file)
    {
        assert(is_string($file), 'Wrong argument type argument 1. String expected');
        if (!\in_array($file, $this->_styles)) {
            $this->_styles[] = "$file";
        }
        return $this;
    }

    /**
     * Add path to javascript file.
     *
     * @param   string  $file  path and file name
     * @return  $this
     */
    public function addScript($file)
    {
        assert(is_string($file), 'Wrong argument type argument 1. String expected');
        if (!\in_array($file, $this->_scripts)) {
            $this->_scripts[] = "$file";
        }
        return $this;
    }

    /**
     * Add multiple CSS files.
     *
     * @param   array  $files  path and file names
     * @return  $this
     */
    public function addStyles(array $files)
    {
        foreach ($files as $file)
        {
            $this->addStyle($file);
        }
        return $this;
    }

    /**
     * Add multiple JavaScript files.
     *
     * @param  array  $files  path and file names
     * @return \Yana\Views\Managers\IsManager
     */
    public function addScripts(array $files)
    {
        foreach ($files as $file)
        {
            $this->addScript($file);
        }
        return $this;
    }

    /**
     * Returns list of paths to CSS stylesheets.
     *
     * @return  array
     */
    public function getStyles()
    {
        return $this->_styles;
    }

    /**
     * Returns list of paths to javascript files.
     *
     * @return  array
     */
    public function getScripts()
    {
        return $this->_scripts;
    }

}

?>