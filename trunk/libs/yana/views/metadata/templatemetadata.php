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

namespace Yana\Views\MetaData;

/**
 * XML skin description file.
 *
 * @package     yana
 * @subpackage  views
 */
class TemplateMetaData extends \Yana\Core\Object
{

    /**
     * @var  string
     */
    private $_id = "";

    /**
     * @var  string
     */
    private $_file = "";

    /**
     * @var  array
     */
    private $_languages = array();

    /**
     * @var  array
     */
    private $_scripts = array();

    /**
     * @var  array
     */
    private $_styles = array();

    /**
     * @param   string  $id
     * @return  \Yana\Views\MetaData\TemplateMetaData
     */
    public function setId($id)
    {
        assert('is_string($id); // Invalid argument $id. String expected');
        $this->_id = $id;
        return $this;
    }

    /**
     * @return  string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set path to template file.
     *
     * @param   string  $file  valid file path
     * @return  \Yana\Views\MetaData\TemplateMetaData
     */
    public function setFile($file)
    {
        assert('is_string($file); // Invalid argument $file. String expected');
        $this->_file = $file;
        return $this;
    }

    /**
     * Return path to template file.
     *
     * This returns the path and name of the template file associated with the template as it was defined.
     * Note: This function does not check if the defined file actually does exist.
     *
     * @return  string
     */
    public function getFile()
    {
        return $this->_file;
    }

    /**
     * @param   array  $languages
     * @return  \Yana\Views\MetaData\TemplateMetaData
     */
    public function setLanguages(array $languages)
    {
        $this->_languages = $languages;
        return $this;
    }

    /**
     * @return  array
     */
    public function getLanguages()
    {
        return $this->_languages;
    }

    /**
     * @param   array  $scripts
     * @return  \Yana\Views\MetaData\TemplateMetaData
     */
    public function setScripts(array $scripts)
    {
        $this->_scripts = $scripts;
        return $this;
    }

    /**
     * @return  array
     */
    public function getScripts()
    {
        return $this->_scripts;
    }

    /**
     * @param   array  $styles
     * @return  \Yana\Views\MetaData\TemplateMetaData
     */
    public function setStyles(array $styles)
    {
        $this->_styles = $styles;
        return $this;
    }

    /**
     * @return  array
     */
    public function getStyles()
    {
        return $this->_styles;
    }

}

?>