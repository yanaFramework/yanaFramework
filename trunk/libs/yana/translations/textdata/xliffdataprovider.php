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

namespace Yana\Translations\TextData;

/**
 * XLIFF data provider to load XLIFF-files.
 *
 * @package     yana
 * @subpackage  translations
 * @ignore
 */
class XliffDataProvider extends \Yana\Core\Object implements \Yana\Translations\TextData\IsDataProvider
{

    /**
     * Where the XLIFF-files should be.
     *
     * @var  \Yana\Files\IsDir
     */
    private $_directory = null;

    /**
     * Setup target directory and container.
     *
     * @param  \Yana\Files\Dir  $directory  where the files will be found
     */
    public function __construct(\Yana\Files\IsDir $directory)
    {
        $this->_directory = $directory;
    }

    /**
     * Get target directory.
     *
     * @return  \Yana\Files\IsDir
     */
    protected function _getDirectory()
    {
        return $this->_directory;
    }

    /**
     * Get path to XLIFF file.
     *
     * {@internal Overwrite or extend this function where needed. }}
     *
     * @param   string  $id  identifier for the file to be loaded
     * @return  string
     * @throws  \Yana\Core\Exceptions\Translations\InvalidFileNameException  when the given filename is invalid
     */
    protected function _convertIdToFilePath($id)
    {
        assert('is_string($id); // Invalid argument $id: string expected');

        // check syntax of filename
        if (!preg_match("/^[\w_\-\d]+$/i", $id)) {
            $message = "The provided language file id contains illegal characters.".
                " Be aware that only alphanumeric (a-z,0-9,-,_) characters are allowed.";
            $level = \Yana\Log\TypeEnumeration::INFO;
            $exception = new \Yana\Core\Exceptions\Translations\InvalidFileNameException($message, $level);
            throw $exception->setFilename($id);
        }

        $directory = $this->_getDirectory()->getPath() . '/';
        return "{$directory}{$id}.xlf";
    }

    /**
     * Create and load XML object.
     *
     * {@internal Overwrite this function where needed. }}
     *
     * @param   string  $file  file path
     * @return  \Yana\Translations\TextData\IsLanguageInterchangeFile
     * @throws  \Exception  when the XML data could not be parsed
     */
    protected function _loadXmlByFileName($file)
    {
        assert('is_string($file); // Invalid argument $file: string expected');
        return new \Yana\Translations\TextData\LanguageInterchangeFile($file, LIBXML_NOENT, true);
    }

    /**
     * Read XLIFF-file.
     *
     * This tries to read a given language file.
     *
     * {@internal Overwrite or extend this function where needed. }}
     *
     * @param   \Yana\Translations\TextData\IsLanguageInterchangeFile  $xml        provided XML meta data
     * @param   \Yana\Translations\TextData\IsTextContainer            $container  container to fill
     * @param   string                                                 $id         name of XLIFF file without file-ending and path
     * @return  \Yana\Translations\TextData\IsTextContainer
     */
    protected function _fillContainer(\Yana\Translations\TextData\IsLanguageInterchangeFile $xml,
        \Yana\Translations\TextData\IsTextContainer $container, $id)
    {
        if (!empty($xml)) {
            $strings = \array_change_key_case($xml->toArray());
            $groups = \array_change_key_case($xml->getGroups($container->getGroups()));
            $container->addVars($strings)
                ->addGroups($groups)
                ->setLoaded($id); // Success: mark id as loaded
        }

        return $container;
    }

    /**
     * Loads the contents of a XLIFF file.
     *
     * The strings are added to a container and the container is returned.
     * Already existing strings will be replaced.
     *
     * You may find valid filenames in the following directory 'languages/<locale>/*.xlf'.
     * Provide the file without path and file extension.
     *
     * @param   string  $id  base-name of XLIFF file without file-ending and path
     * @param   \Yana\Translations\TextData\IsTextContainer  $container  container to fill
     * @return  \Yana\Translations\TextData\IsTextContainer
     * @throws  \Yana\Core\Exceptions\Translations\InvalidFileNameException  when the given filename is invalid
     * @throws  \Yana\Core\Exceptions\Translations\LanguageFileNotFoundException  when the XLIFF file is not found
     * @throws  \Yana\Core\Exceptions\Translations\InvalidSyntaxException  when there is a problem with the file
     */
    public function loadOject($id, \Yana\Translations\TextData\IsTextContainer $container = null)
    {
        if (is_null($container)) {
            $container = new \Yana\Translations\TextData\TextContainer();
        }
        assert($container instanceof \Yana\Translations\TextData\IsTextContainer);
        /**
         * If file is not yet loaded, read it now.
         * Value isLoaded() should be set to true on success and remain false on error.
         */
        if (!$container->isLoaded($id)) {

            // build target path
            $selectedFile = $this->_convertIdToFilePath($id); // may throw exception

            // check path
            if (!\file_exists($selectedFile)) {
                assert('!isset($message); // Cannot redeclare var $message');
                $message = "No language-file found for id '{$id}'.";
                assert('!isset($level); // Cannot redeclare variable $level');
                $level = \Yana\Log\TypeEnumeration::INFO;
                assert('!isset($exception); // Cannot redeclare variable $exception');
                $exception = new \Yana\Core\Exceptions\Translations\LanguageFileNotFoundException($message, $level);
                throw $exception->setFilename($id);
            }

            /**
             * Read XLIFF-file.
             *
             * This tries to read a given language file.
             * If the file is not valied, it writes a warning to the logs.
             */
            try {

                assert('!isset($xml); /* cannot redeclare variable $xml */');
                $xml = $this->_loadXmlByFileName($selectedFile); // May throw exception if XML is invalid
                $this->_fillContainer($xml, $id);
                unset($xml);

            } catch (\Exception $e) {
                assert('!isset($message); // Cannot redeclare var $message');
                $message = "Error in language file: '$id'.";
                assert('!isset($level); // Cannot redeclare variable $level');
                $level = \Yana\Log\TypeEnumeration::WARNING;
                $exception = new \Yana\Core\Exceptions\Translations\InvalidSyntaxException($message, $level, $e);
                $exception->setFilename($selectedFile);
                throw $exception; // Re-throw exception
            }
            unset($selectedFile);
        }
        return $container;
    }

}

?>