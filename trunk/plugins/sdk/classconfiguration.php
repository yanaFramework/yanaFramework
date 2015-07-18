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

namespace Plugins\SDK;

/**
 * Plugin configurator for creating manual configurations
 *
 * @package     yana
 * @subpackage  plugins
 */
class ClassConfiguration extends \Yana\Plugins\Configs\ClassConfiguration
{

    /**
     * Outputs the method doc-header as a PHP-Doc comment with annotations.
     *
     * @return  string
     */
    public function __toString()
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
            $string .= $tab . "@" . \Yana\Plugins\Annotations\Enumeration::TYPE . "       " . $this->getType();
        }
        if ($this->getGroup()) {
            $string .= $tab . "@" . \Yana\Plugins\Annotations\Enumeration::GROUP . "      " . $this->getGroup();
        }
        if ($this->getParent()) {
            $string .= $tab . "@" . \Yana\Plugins\Annotations\Enumeration::PARENT . "    " . $this->getParent();
        }
        foreach ($this->getDependencies() as $dependency)
        {
            $string .= $tab . "@" . \Yana\Plugins\Annotations\Enumeration::REQUIRES . "   " . $dependency;
        }
        if ($this->getPriority()) {
            $string .= $tab . "@" . \Yana\Plugins\Annotations\Enumeration::PRIORITY . "   " . $this->getPriority();
        }
        foreach ($this->getMenuNames() as $menu)
        {
            $string .= $tab . "@" . \Yana\Plugins\Annotations\Enumeration::MENU . "       " .
                \Yana\Plugins\Annotations\Enumeration::GROUP . ": " . $menu->getGroup();
            if ($menu->getTitle()) {
                $string .= ', ' . \Yana\Plugins\Annotations\Enumeration::TITLE . ': ' . $menu->getTitle();
            }
        }
        if ($this->getActive() === \Yana\Plugins\ActivityEnumeration::DEFAULT_ACTIVE) {
            $string .= $tab . "@" . \Yana\Plugins\Annotations\Enumeration::ACTIVE . "     always";
        }
        foreach ($this->getAuthors() as $author)
        {
            $string .= $tab . "@" . \Yana\Plugins\Annotations\Enumeration::AUTHOR . "     " . $author;
        }
        if ($this->getLicense()) {
            $string .= $tab . "@" . \Yana\Plugins\Annotations\Enumeration::LICENSE . "    " . $this->getLicense();
        }
        if ($this->getVersion()) {
            $string .= $tab . "@" . \Yana\Plugins\Annotations\Enumeration::VERSION . "    " . $this->getVersion();
        }
        if ($this->getUrl()) {
            $string .= $tab . "@" . \Yana\Plugins\Annotations\Enumeration::URL . "        " . $this->getUrl();
        }
        $string .= $tab . "@" . \Yana\Plugins\Annotations\Enumeration::PACKAGE . "    yana";
        $string .= $tab . "@" . \Yana\Plugins\Annotations\Enumeration::SUBPACKAGE . " plugins";

        $string .= "\n */";
        return $string;
    }

}

?>