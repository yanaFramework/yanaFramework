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
class XmlMetaData extends \Yana\Core\MetaData\XmlMetaData
{

    /**
     * Returns list of template meta data elements.
     *
     * @param   string  $directory  file path
     * @return  \Yana\Views\MetaData\IsTemplateMetaData[]
     */
    public function getTemplates($directory = "")
    {
        $templates = array();
        if (!empty($this->body->template)) {
            foreach ($this->body->template as $element)
            {
                $template = new \Yana\Views\MetaData\TemplateMetaData();
                $attributes = $element->attributes();
                if (empty($attributes['id'])) {
                    continue;
                }
                $id = (string) $attributes['id'];
                $template->setId($id);

                if (!empty($attributes['file'])) {
                    assert('!isset($file); // Cannot redeclare $file');
                    $file = $directory . $attributes['file'];
                    $template->setFile($file);
                    unset($file);
                } // end if
                unset($attributes);

                assert('!isset($values); // cannot redeclare variable $values');
                $values = array(
                    'SCRIPT' => array(),
                    'STYLE' => array(),
                    'LANGUAGE' => array(),
                );
                foreach ($element->children() as $item)
                {
                    $attributes = $item->attributes();
                    $name = strtoupper($item->getName());
                    switch ($name)
                    {
                        case 'SCRIPT':
                        case 'STYLE':
                            if ((string) $item !== '') {
                                if (!is_file("{$directory}{$item}")) {
                                    $message = "The value '{$item}' is not a valid file resource.";
                                    \Yana\Log\LogManager::getLogger()
                                        ->addLog($message, \Yana\Log\TypeEnumeration::WARNING);
                                    continue;
                                }
                                $item = "{$directory}{$item}";
                            }
                        // fall through
                        case 'LANGUAGE':
                            if (!isset($attributes['id'])) {
                                $values[$name][] = (string) $item;
                            } else {
                                $values[$name][(string) $attributes['id']] = (string) $item;
                            }
                            break;
                    } // end switch
                } // end foreach
                $template->setScripts($values['SCRIPT'])
                    ->setStyles($values['STYLE'])
                    ->setLanguages($values['LANGUAGE']);
                unset($values);
                $templates[$id] = $template;
            } // end foreach
        }
        return $templates;
    }

}

?>