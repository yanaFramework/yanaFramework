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

namespace Yana\Templates\Helpers\Functions;

/**
 * Smarty-compatible function.
 *
 * This class is registered when instantiating the Smarty Engine.
 *
 * @package     yana
 * @subpackage  templates
 */
class Form extends \Yana\Core\Object implements \Yana\Templates\Helpers\IsFunction
{

    /**
     * <<smarty function>> Create.
     *
     * This is a generator function to create dynamic HTML forms from database
     * schema files and database values.
     * Forms with several layouts can be generated to view, edit, insert, delete
     * or search database values.
     *
     * If necessary it communicates with the database to retrieve values.
     * This communication is limited to Select-Statements. No data is changed.
     *
     * For security reasons this function does NOT provide functionality to
     * write changes made in the forms to the database on it's own.
     * This might have introduced the possibility to compromise the security settings
     * of third-party plugIns and thus has never been implemented and never will be.
     *
     * Instead you need to provide the name of a function in your plugin,
     * that will handle the requests. This gives you the chance to do additional
     * security checks and filter the provided data as you see fit.
     *
     * <pre>
     * This function takes the following arguments:
     *
     * string  $file        (mandatory) path and name of structure file
     * string  $table       (optional)  table to choose from structure file
     * string  $id          (optional)  name of form to use (either $id or $table must be present!)
     * string  $show        (optional)  comma seperated list of columns,
     *                                  that should be shown in the form
     * string  $hide        (optional)  comma seperated list of columns,
     *                                  that should NOT be shown in the form
     * string  $where       (optional)  sequence for SQL-where clause
     *                                  <FIELDNAME1>=<VALUE1>[,<FIELDNAME2>=<VALUE2>[,...]]
     * string  $sort        (optional)  name of column to sort entries by
     * boolean $desc        (optional)  sort entries in descending (true) or ascending (false) order
     * integer $page        (optional)  number of 1st entry to show
     * integer $entries     (optional)  number of entries to show on each page
     * string  $oninsert    (optional)  name of action (plugin-function) to execute on the event
     * string  $onupdate    (optional)  name of action (plugin-function) to execute on the event
     * string  $ondelete    (optional)  name of action (plugin-function) to execute on the event
     * string  $onsearch    (optional)  name of action (plugin-function) to execute on the event
     * string  $ondownload  (optional)  name of action (plugin-function) to execute on the event
     * string  $onexport    (optional)  name of action (plugin-function) to execute on the event
     * int     $layout      (optional)  where multiple layouts are available to present the result,
     *                                  this allows to choose the prefered one
     * </pre>
     *
     * Example of usage:
     * <code>
     * {create file="guestbook" table="guestbook" sort="guestbook_date" desc="true"}
     * </code>
     *
     * @param   array                      $params  any list of arguments
     * @param   \Smarty_Internal_Template  $smarty  reference to currently rendered template
     * @return  scalar
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        // parameter 'file' is mandatory.
        if (!isset($params['file']) || !is_string($params['file'])) {
            return "Error: Missing parameter 'file'.";
        }

        // create database query
        $smartForm = new \Yana\Forms\Builder($params['file']);

        if (isset($params['id'])) {
            $smartForm->setId($params['id']);
        }
        if (isset($params['table'])) {
            $smartForm->setTable($params['table']);
        }
        if (isset($params['show'])) {
            if (!is_array($params['show'])) {
                $params['show'] = explode(',', $params['show']);
            }
            $smartForm->setShow($params['show']);
        }
        if (isset($params['hide'])) {
            if (!is_array($params['hide'])) {
                $params['hide'] = explode(',', $params['hide']);
            }
            $smartForm->setHide($params['hide']);
        }
        if (isset($params['where'])) {
            $smartForm->setWhere($params['where']);
        }
        if (isset($params['on_insert'])) {
            $smartForm->setOninsert($params['on_insert']);
        }
        if (isset($params['on_update'])) {
            $smartForm->setOnupdate($params['on_update']);
        }
        if (isset($params['on_delete'])) {
            $smartForm->setOndelete($params['on_delete']);
        }
        if (isset($params['on_search'])) {
            $smartForm->setOnsearch($params['on_search']);
        }
        if (isset($params['on_export'])) {
            $smartForm->setOnexport($params['on_export']);
        }
        if (isset($params['on_download'])) {
            $smartForm->setOndownload($params['on_download']);
        }
        if (isset($params['sort'])) {
            $smartForm->setSort($params['sort']);
        }
        if (isset($params['desc'])) {
            $smartForm->setDescending($params['desc']);
        }
        if (isset($params['page'])) {
            $smartForm->setPage($params['page']);
        }
        if (isset($params['entries'])) {
            $smartForm->setEntries($params['entries']);
        }
        if (isset($params['layout'])) {
            $smartForm->setLayout($params['layout']);
        }

        $facade = $smartForm->__invoke();
        return $facade->__toString();
    }

}

?>