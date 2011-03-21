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

/**
 * @ignore
 */
require_once 'pluginconfigurationmethodsdk.class.php';

/**
 * Plugin configurator for creating manual configurations
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class PluginConfigurationClassSdk extends PluginConfigurationClass
{

    /**
     * the plugin's identifier
     *
     * @access  private
     * @var     string
     */
    private $_id = null;

    /**
     * Set plug-in's id.
     *
     * @access  public
     * @param   string  $id  plugin unique identifier
     * @return  PluginConfigurationClassSdk
     */
    public function setId($id)
    {
        assert('is_string($id); // Invalid argument $id: String expected');
        $this->_id = (string) $id;
        return $this;
    }

    /**
     * Get plug-in's id.
     *
     * @access  public
     * @return  string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * convert to string
     *
     * Outputs the results as a PHP-Doc comment
     *
     * @access  public
     * @return  string
     */
    public function toString()
    {
        $tab = "\n * ";
        $string = "/**" . $tab;
        // head line
        if ($this->getTitle()) {
            $string .= $this->getTitle();
        } else {
            $string .= $this->getClassName();
        }
        $string .= $tab; // empty line after title
        // description
        if ($this->getText()) {
            $text = $this->getText();
            preg_replace('/^/m', $tab, $text);
            $string .= $text . $tab;
        }
        // annotations
        if ($this->getType()) {
            $string .= $tab . "@" . PluginAnnotationEnumeration::TYPE . "       " . $this->getType();
        }
        if ($this->getGroup()) {
            $string .= $tab . "@" . PluginAnnotationEnumeration::GROUP . "      " . $this->getGroup();
        }
        if ($this->getParent()) {
            $string .= $tab . "@" . PluginAnnotationEnumeration::PARENT . "    " . $this->getParent();
        }
        foreach ($this->getDependencies() as $dependency)
        {
            $string .= $tab . "@" . PluginAnnotationEnumeration::REQUIRES . "   " . $dependency;
        }
        if ($this->getPriority()) {
            $string .= $tab . "@" . PluginAnnotationEnumeration::PRIORITY . "   " . $this->getPriority();
        }
        foreach ($this->getMenuNames() as $menu)
        {
            $string .= $tab . "@" . PluginAnnotationEnumeration::MENU . "       " .
                PluginAnnotationEnumeration::GROUP . ": " . $menu->getGroup();
            if ($menu->getTitle()) {
                $string .= ', ' . PluginAnnotationEnumeration::TITLE . ': ' . $menu->getTitle();
            }
        }
        if ($this->getActive() === PluginActivityEnumeration::DEFAULT_ACTIVE) {
            $string .= $tab . "@" . PluginAnnotationEnumeration::ACTIVE . "     always";
        }
        foreach ($this->getAuthors() as $author)
        {
            $string .= $tab . "@" . PluginAnnotationEnumeration::AUTHOR . "     " . $author;
        }
        if ($this->getLicense()) {
            $string .= $tab . "@" . PluginAnnotationEnumeration::LICENSE . "    " . $this->getLicense();
        }
        if ($this->getVersion()) {
            $string .= $tab . "@" . PluginAnnotationEnumeration::VERSION . "    " . $this->getVersion();
        }
        if ($this->getUrl()) {
            $string .= $tab . "@" . PluginAnnotationEnumeration::URL . "        " . $this->getUrl();
        }
        $string .= $tab . "@" . PluginAnnotationEnumeration::PACKAGE . "    yana";
        $string .= $tab . "@" . PluginAnnotationEnumeration::SUBPACKAGE . " plugins";

        $string .= "\n */";
        return $string;
    }

}

?>