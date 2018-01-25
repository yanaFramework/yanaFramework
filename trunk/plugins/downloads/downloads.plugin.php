<?php
/**
 * Downloads
 *
 * A library to handle downloads of files.
 *
 * {@translation
 *   de: Downloads
 *
 *       Eine Bibliothek zum Herunterladen von Dateien.
 * }
 *
 * @author     Thomas Meyer
 * @type       primary
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @active     always
 *
 * @package    yana
 * @subpackage plugins
 */

namespace Plugins\Downloads;

/**
 * Default library for common functions
 *
 * This plugin is important. It provides functionality
 * that might be usefull for other plugins.
 *
 * @package    yana
 * @subpackage plugins
 */
class DownloadsPlugin extends \Yana\Plugins\AbstractPlugin
{

    /**
     * Create an image or download a file from a source.
     *
     * The files are stored in the user's session.
     * This function can only download files, that have been marked for download.
     *
     * @type        primary
     * @template    null
     *
     * @access      public
     * @param       int   $target    image identifier
     * @param       bool  $fullsize  show fullsize image (or preview?)
     * @throws      \Yana\Core\Exceptions\Files\NotFoundException
     */
    public function download_file($target, $fullsize = false)
    {
        $filenameCache = new \Yana\Db\Binaries\FileNameCache();
        $source = $filenameCache->getFilename($target, $fullsize);
        unset($filenameCache);

        if (preg_match('/\.gz$/', $source)) {
            $this->_downloadDocument($source);
        } else {
            $this->_downloadImage($source);
        }
        exit(0);
    }

    /**
     * Transmit headers and pass file contents through to client.
     *
     * @param  string  $source  file path
     */
    private function _downloadDocument($source)
    {
        $i = 0;

        $gz = gzopen($source, 'r');
        while (!gzeof($gz))
        {
            $buffer = gzgets($gz, 4096);
            switch ($i)
            {
                case 0:
                    if (preg_match('/^[\w\.\d\-\_]+$/s', $buffer) && !headers_sent()) {
                        header("Content-Disposition: attachment; filename={$buffer}");
                    }
                break;

                case 1:
                    if (is_numeric($buffer) && !headers_sent()) {
                        header("Content-Length: {$buffer}");
                    }
                break;

                case 2:
                    if (preg_match('/^\w+\/[\w-]+$/s', $buffer) && !headers_sent()) {
                        header("Content-type: {$buffer}");
                    }
                break;

                default:
                    print $buffer;
                break;

            }
            $i++;
        }
        gzclose($gz);
    }

    /**
     * Transmit image headers and pass file contents through to client.
     *
     * @access  private
     * @param   string  $source  image path
     */
    private function _downloadImage($source)
    {
        header("Content-type: image/png");
        $handle = fopen($source, 'r');
        fpassthru($handle);
    }

}

?>