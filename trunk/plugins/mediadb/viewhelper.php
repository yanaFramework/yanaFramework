<?php
/**
 * Media Database
 *
 * Allows a user to upload, media files to the database and group them in public or private folders or galleries.
 *
 * @author     Thomas Meyer
 * @url        http://www.yanaframework.net
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @package    yana
 * @subpackage plugins
 */

namespace Plugins\MediaDb;

/**
 * <<helper>> View helper producing a form.
 *
 * @package     yana
 * @subpackage  plugins
 */
class ViewHelper extends \Yana\Core\Object implements \Yana\Views\Helpers\IsFunction
{

    /**
     * @var \Yana\Forms\IsBuilder
     */
    private $_formBuilder = null;

    /**
     * @var \Yana\Forms\Facade
     */
    private $_form = null;

    /**
     * <<construct>> Initialize dependencies.
     *
     * @param  \Yana\Forms\IsBuilder  $formBuilder  loads form object
     */
    public function __construct(\Yana\Forms\IsBuilder $formBuilder)
    {
        $this->_formBuilder = $formBuilder;
    }

    /**
     * get form definition
     *
     * @return  \Yana\Forms\Facade
     */
    protected function _getMediafolderForm()
    {
        if (!isset($this->_form)) {
            $builder = $this->_formBuilder;
            $this->_form = $builder();
        }
        return $this->_form;
    }

    /**
     * <<smarty function>> Create a folder list from a data table.
     *
     * @param   array                      $params  ignored
     * @param   \Smarty_Internal_Template  $smarty  ignored
     * @return  string
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        return (string) $this->_getMediafolderForm();
    }

}

?>