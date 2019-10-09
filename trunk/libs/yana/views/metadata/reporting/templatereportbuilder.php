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

namespace Yana\Views\MetaData\Reporting;

/**
 * Helper class that creates reporting objects for managers.
 *
 * @package     yana
 * @subpackage  views
 * @ignore
 */
class TemplateReportBuilder extends \Yana\Views\MetaData\Reporting\AbstractTemplateReportBuilder
{

    /**
     * Check if template file exists.
     *
     * Returns bool(true) if it does and bool(false) otherwise.
     * On FALSE it also creates adds an error description to the report.
     *
     * @return  bool
     */
    protected function _checkTemplateFileExists()
    {
        $template = $this->getTemplateConfiguration();
        if (!$template) {
            return true;
        }

        assert(!isset($filename), 'Cannot redeclare var $filename');
        $filename = $template->getFile();
        assert(!isset($fileExists), 'Cannot redeclare var $fileExists');
        $fileExists = file_exists($filename);

        if (!$fileExists) {
            $this->_getReport()->addError("File '{$filename}' does not exist. " .
                "Please make sure this path and filename is correct " .
                "and you have all files installed. Reinstall if necessary.");
        } else {
            $this->_getReport()->addText("File: {$filename}");
        }
        unset($filename);

        assert(is_bool($fileExists));
        return $fileExists;
    }

    /**
     * Check language file references.
     *
     * Returns bool(true) if all required language files exist and bool(false) otherwise.
     * On FALSE it also creates adds an error description to the report.
     *
     * @return  bool
     */
    protected function _checkLanguageFilesExist()
    {
        $template = $this->getTemplateConfiguration();
        if (!$template) {
            return true;
        }

        assert(!isset($filesExist), 'Cannot redeclare var $filesExist');
        $filesExist = true;

        assert(!isset($value), 'Cannot redeclare var $value');
        foreach ($template->getLanguages() as $value)
        {
            if (!empty($value)) {
                continue;
            }
            try {

                \Yana\Translations\Facade::getInstance()->readFile($value); // may throw exception

            } catch (\Yana\Core\Exceptions\Translations\TranslationException $e) {
                $this->_getReport()->addWarning("A required language file '{$value}' is not available. " .
                    "Please check if the chosen language file is correct and update your " .
                    "language pack if needed. " . $e->getMessage());
                $filesExist = false;
            }
        }
        unset($value);

        assert(is_bool($filesExist));
        return $filesExist;
    }

    /**
     * Check stylesheet references.
     *
     * Returns bool(true) if all required stylesheet files exist and bool(false) otherwise.
     * On FALSE it also creates adds an error description to the report.
     *
     * @return  bool
     */
    protected function _checkStylesheetFilesExist()
    {
        $template = $this->getTemplateConfiguration();
        if (!$template) {
            return true;
        }

        assert(!isset($filesExist), 'Cannot redeclare var $filesExist');
        $filesExist = true;

        /*
         * check stylesheet references
         */
        assert(!isset($value), 'cannot redeclare variable $value');
        foreach ($template->getStyles() as $value)
        {
            if (!file_exists($value)) {
                $this->_getReport()->addError("A required stylesheet is not available." .
                    "This template may not be displayed correctly.");
                $filesExist = false;
            }
        }
        unset($value);

        assert(is_bool($filesExist));
        return $filesExist;
    }

    /**
     * Check script file references.
     *
     * Returns bool(true) if all required script files exist and bool(false) otherwise.
     * On FALSE it also creates adds an error description to the report.
     *
     * @return  bool
     */
    protected function _checkScriptFilesExist()
    {
        $template = $this->getTemplateConfiguration();
        if (!$template) {
            return true;
        }

        assert(!isset($filesExist), 'Cannot redeclare var $filesExist');
        $filesExist = true;

        assert(!isset($value), 'cannot redeclare variable $value');
        foreach ($template->getScripts() as $value)
        {
            if (!file_exists($value)) {
                $this->_getReport()->addError("A required javascript file is not available." .
                    "This template may not be displayed correctly.");
                $filesExist = false;
            }
        }
        unset($value);

        assert(is_bool($filesExist));
        return $filesExist;
    }

    /**
     * Hook function on success.
     */
    protected function _onSuccess()
    {
        $this->_getReport()->addText('No problems found.');
    }

    /**
     * Hook function on failure.
     */
    protected function _onError()
    {
        // Don't output additional text!
        // The error messages already generated will be explanation enough.
    }

}

?>