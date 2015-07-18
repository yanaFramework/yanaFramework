<?php
/**
 * Project Management
 *
 * This is a sample application to demonstrate management and cost survey of projects.
 *
 * {@translation
 *
 *    de:  Projektverwaltung
 *
 *         Dies ist eine Beispielanwendung zur Demonstration einer Verwaltung und Abrechnung von Projekten.
 *
 * , fr:   Gestion de Projets
 *
 * }
 *
 * @author     Thomas Meyer
 * @type       primary
 * @group      project
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

namespace Plugins\Project;

/**
 * <<plugin>> demo plugin for a (very) simple project management software
 *
 * @package     plugins
 * @subpackage  project
 */
class Plugin extends \Yana\Plugins\AbstractPlugin
{

    /**
     * get form definition
     *
     * @access  protected
     * @static
     * @return  FormFacade
     */
    protected static function getProjectForm()
    {
        $builder = new \Yana\Forms\Builder('project');
        return $builder->setId('project')->__invoke();
    }

    /**
     * get form definition
     *
     * @access  protected
     * @static
     * @return  FormFacade
     */
    protected static function getEffortForm()
    {
        $form = self::getProjectForm();
        return $form->getForm("effort");
    }

    /**
     * Default event handler.
     *
     * @access  public
     * @return  bool
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of arguments passed to the function
     * @ignore
     */
    public function catchAll($event, array $ARGS)
    {
        return true;
    }

    /**
     * Show project list
     *
     * @type        read
     * @user        group: project
     * @user        group: admin
     * @template    templates/project.html.tpl
     * @language    project
     * @style       templates/default.css
     * @menu        group: start
     *
     * @access      public
     */
    public function project()
    {
        /* get entries from database */
        $key = 'project';
        $where = array();
        $orderBy = 'project_created';
        $offset = 0;
        $limit = 50;
        $desc = true;
        $rows = $this->_connectToDatabase('project')->select($key, $where, $orderBy, $offset, $limit, $desc);
        $this->_getApplication()->setVar('PROJECT', $rows);
    }

    /**
     * calculate sum of hours spent on a project
     *
     * @type        read
     * @user        group: project
     * @user        group: admin
     * @template    NULL
     * @language    project
     *
     * @access      public
     * @param       int  $target  project id
     */
    public function project_sum($target)
    {
        $language = $this->_getApplication()->getLanguage();
        $database = $this->_connectToDatabase('project');

        /* get entries from database */
        $key = 'effort.*.effort_duration';
        $where = array(
            array('project_id', '=', $target),
            'and',
            array('effort_state', '!=', 1)
        );
        $rows = $database->select($key, $where);
        if (empty($rows)) {

            $description = $language->getVar('PRJ.SUM.HOURS');
            $values = array('SUM' => 0, 'COUNT' => 0);
            $description = \Yana\Util\String::replaceToken($description, $values);

            exit($description);
        }
        $sum = array_sum($rows);
        if (!is_numeric($sum)) {
            exit('Error: invalid data.');
        }
        $sum = (float) $sum;
        $loan = $database->select("project.{$projectId}.project_loan");
        if (is_numeric($loan)) {
            $loan = (float) $loan;

            $description = $language->getVar('PRJ.SUM.HOURS') . ",\n" .
            $language->getVar('PRJ.SUM.LOAN') . ",\n" .
            $language->getVar('PRJ.SUM.TOTAL');
            $values = array('SUM' => $sum,
                            'COUNT' => count($rows),
                            'LOAN' => $loan,
                            'TOTAL' => ($sum * $loan));
            $description = \Yana\Util\String::replaceToken($description, $values);

            exit($description);
        } else {

            $description = $language->getVar('PRJ.SUM.HOURS');
            $values = array('SUM' => $sum, 'COUNT' => count($rows));
            $description = \Yana\Util\String::replaceToken($description, $values);

            exit($description);
        }
    }

    /**
     * Edit projects
     *
     * @type        write
     * @user        group: project, role: moderator
     * @user        group: admin, level: 75
     * @template    MESSAGE
     * @onsuccess   goto: project_read_edit_project
     * @onerror     goto: project_read_edit_project
     *
     * @access      public
     * @return      bool
     */
    public function project_edit_project()
    {
        $form = self::getProjectForm();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('project'), $form);
        return $worker->update();
    }

    /**
     * Delete projects
     *
     * @type        write
     * @user        group: project, role: moderator
     * @user        group: admin, level: 75
     * @template    MESSAGE
     * @onsuccess   goto: project_read_edit_project
     * @onerror     goto: project_read_edit_project
     *
     * @access      public
     * @param       array  $selected_entries  list of projects to delete
     * @return      bool
     */
    public function project_delete_project(array $selected_entries)
    {
        $form = self::getProjectForm();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('project'), $form);
        return $worker->delete($selected_entries);
    }

    /**
     * Add new project
     *
     * @type        write
     * @user        group: project, role: moderator
     * @user        group: admin, level: 75
     * @template    MESSAGE
     * @onsuccess   goto: project_read_read_project
     * @onerror     goto: project_default_new_project
     *
     * @access      public
     * @return      bool
     */
    public function project_new_project()
    {
        $form = self::getProjectForm();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('project'), $form);
        return $worker->create();
    }

    /**
     * Edit efforts
     *
     * @type        write
     * @user        group: project, role: moderator
     * @user        group: admin, level: 50
     * @template    MESSAGE
     * @onsuccess   goto: project_read_edit_effort
     * @onerror     goto: project_read_edit_effort
     *
     * @access      public
     * @return      bool
     */
    public function project_edit_effort()
    {
        $form = self::getEffortForm();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('project'), $form);
        return $worker->update();
    }

    /**
     * Delete efforts
     *
     * @type        write
     * @user        group: project, role: moderator
     * @user        group: admin, level: 50
     * @template    MESSAGE
     * @onsuccess   goto: project_read_edit_effort
     * @onerror     goto: project_read_edit_effort
     *
     * @access      public
     * @param       array  $selected_entries  list of efforts to delete
     * @return      bool
     */
    public function project_delete_effort(array $selected_entries)
    {
        $form = self::getEffortForm();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('project'), $form);
        return $worker->delete($selected_entries);
    }

    /**
     * Add new effort
     *
     * @type        write
     * @user        group: project
     * @user        group: admin, level: 30
     * @template    MESSAGE
     * @onsuccess   goto: project_read_read_effort
     * @onerror     goto: project_default_new_effort
     *
     * @access      public
     * @return      bool
     */
    public function project_new_effort()
    {
        $form = self::getEffortForm();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('project'), $form);
        return $worker->create();
    }

    /**
     * Search projects list
     *
     * @type        read
     * @user        group: project
     * @user        group: admin
     * @template    templates/project.html.tpl
     * @language    project
     * @onsuccess   goto: project_read_search_project
     * @onerror     goto: project_read_search_project
     *
     * @access      public
     */
    public function project_search_project()
    {
        $this->project();
    }

    /**
     * Search efforts list
     *
     * @type        read
     * @user        group: project
     * @user        group: admin, level 30
     * @template    templates/project.html.tpl
     * @language    project
     * @onsuccess   goto: project_read_search_effort
     * @onerror     goto: project_read_search_effort
     *
     * @access      public
     */
    public function project_search_effort()
    {
        $this->project();
    }

}

?>