<?php
/**
 * Visitor-Counter.
 *
 * This template-plugin displays the number of unique visitors/users on your application page.
 *
 * @author     Thomas Meyer
 * @url        http://www.yanaframework.net
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @package    yana
 * @subpackage plugins
 */

namespace Plugins\Counter;

/**
 * <<helper>> View helper adding a counter text.
 *
 * @package     yana
 * @subpackage  plugins
 */
class ViewHelper extends \Yana\Core\StdObject implements \Yana\Views\Helpers\IsFunction
{

    /**
     * @var  string
     */
    private $_count = "";

    /**
     * @var  string
     */
    private $_label = "";

    /**
     * <<constructor>> Initialize count and label.
     *
     * @param  int     $count  number of visitors
     * @param  string  $label  shown before the number of visitors
     */
    public function __construct($count, $label)
    {
        $this->_count = (string) $count;
        $this->_label = $label;
    }

    /**
     * <<smarty function>> Function.
     *
     * @param   array                      $params  ignored
     * @param   \Smarty_Internal_Template  $smarty  ignored
     * @return  string
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        return $this->_label . ' <span style="font-weight: bold;">' . $this->_count . '</span>';
    }

}

?>