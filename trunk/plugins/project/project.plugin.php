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

/**
 * <<plugin>> class "plugin_project"
 *
 * @access      public
 * @package     plugins
 * @subpackage  project
 */
class plugin_project extends StdClass implements IsPlugin
{
    /**
     * Connection to data source (API)
     *
     * @access  private
     * @static
     * @var     DbStream
     */
    private static $database = null;

    /**
     * Form definition for projects
     *
     * @access  private
     * @static
     * @var     DDLDefaultForm
     */
    private static $projectForm = null;

    /**
     * Form definition for efforts
     *
     * @access  private
     * @static
     * @var     DDLDefaultForm
     */
    private static $effortForm = null;

    /**
     * get database connection
     *
     * @access  protected
     * @static
     * @return  DbStream
     */
    protected static function getDatabase()
    {
        if (!isset(self::$database)) {
            self::$database = Yana::connect("project");
        }
        return self::$database;
    }

    /**
     * get form definition
     *
     * @access  protected
     * @static
     * @return  DDLDefaultForm
     */
    protected static function getProjectForm()
    {
        if (!isset(self::$projectForm)) {
            $db = self::getDatabase();
            self::$projectForm = $db->schema->getForm("project");
        }
        return self::$projectForm;
    }

    /**
     * get form definition
     *
     * @access  protected
     * @static
     * @return  DDLDefaultForm
     */
    protected static function getEffortForm()
    {
        if (!isset(self::$effortForm)) {
            $form = self::getProjectForm();
            self::$effortForm = $form->getForm("effort");
        }
        return self::$effortForm;
    }

    /**
     * Default event handler
     *
     * returns bool(true) on success and bool(false) on error
     *
     * @access  public
     * @return  bool
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of arguments passed to the function
     * @ignore
     */
    public function _default($event, array $ARGS)
    {
        return true;
    }

    /**
     * project_list
     *
     * returns bool(true) on success and bool(false) on error
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
     * @return      bool
     * @name        plugin_project::project_list()
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
        $rows = self::getDatabase()->select($key, $where, $orderBy, $offset, $limit, $desc);
        Yana::getInstance()->setVar('PROJECT', $rows);
        return true;
    }

    /**
     * calculate sum of hours spent on a project
     *
     * returns bool(true) on success and bool(false) on error
     *
     * @type        read
     * @user        group: project
     * @user        group: admin
     * @template    NULL
     * @language    project
     *
     * @access      public
     * @param       int  $target  project id
     * @return      bool
     * @name        plugin_project::project_list()
     */
    public function project_sum($target)
    {
        $language = Language::getInstance();

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
            $description = SmartUtility::replaceToken($description, $values);

            exit($description);
        }
        $sum = array_sum($rows);
        if (!is_numeric($sum)) {
            exit('Error: invalid data.');
        }
        $sum = (float) $sum;
        $loan = $database->select("project.${projectId}.project_loan");
        if (is_numeric($loan)) {
            $loan = (float) $loan;

            $description = $language->getVar('PRJ.SUM.HOURS') . ",\n" .
            $language->getVar('PRJ.SUM.LOAN') . ",\n" .
            $language->getVar('PRJ.SUM.TOTAL');
            $values = array('SUM' => $sum,
                            'COUNT' => count($rows),
                            'LOAN' => $loan,
                            'TOTAL' => ($sum * $loan));
            $description = SmartUtility::replaceToken($description, $values);

            exit($description);
        } else {

            $description = $language->getVar('PRJ.SUM.HOURS');
            $values = array('SUM' => $sum, 'COUNT' => count($rows));
            $description = SmartUtility::replaceToken($description, $values);

            exit($description);
        }
    }

    /**
     * project_write_edit_project
     *
     * returns bool(true) on success and bool(false) on error
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
     * @name        plugin_project::project_write_edit_project()
     */
    public function project_edit_project ()
    {
        $database = self::getDatabase();
        $updatedEntries = self::getProjectForm()->getUpdateValues();

        /* no data has been provided */
        if (empty($updatedEntries)) {
            throw new InvalidInputWarning();
        }

        foreach ($updatedEntries as $id => $entry)
        {
            $id = mb_strtolower($id);

            /* before doing anything, check if entry exists */
            if (!$database->exists("project.${id}")) {

                /* error - no such entry */
                throw new InvalidInputWarning();

            /* update the row */
            } else if (!$database->update("project.${id}", $entry)) {
                /* error - unable to perform update - possibly readonly */
                return false;
            }
        } /* end for */
        /* commit changes */
        return $database->write();
    }


    /**
     * project_write_delete_project
     *
     * returns bool(true) on success and bool(false) on error
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
     * @name        plugin_project::project_write_delete_project()
     */
    public function project_delete_project (array $selected_entries)
    {
        $database = self::getDatabase();
        /* remove entry from database */
        foreach ($selected_entries as $id)
        {
            if (!$database->remove("project.${id}")) {
                /* entry does not exist */
                throw new InvalidInputWarning();
            }
        } /* end for */
        /* commit changes */
        return $database->commit();
    }

    /**
     * project_write_new_project
     *
     * returns bool(true) on success and bool(false) on error
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
     * @name        plugin_project::project_write_new_project()
     */
    public function project_new_project ()
    {
        $newEntry = self::getProjectForm()->getInsertValues();

        /* no data has been provided */
        if (empty($newEntry)) {
            throw new InvalidInputWarning();
        }

        $database = self::getDatabase();
        /* insert new entry into table */
        if (!$database->insert("project.*", $newEntry)) {
            throw new InvalidInputWarning();
        } else {
            return $database->write();
        }
    }

    /**
     * project_write_edit_effort
     *
     * returns bool(true) on success and bool(false) on error
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
     * @name        plugin_project::project_write_edit_effort()
     */
    public function project_edit_effort ()
    {
        $database = self::getDatabase();
        $updatedEntries = self::getEffortForm()->getUpdateValues();

        /* no data has been provided */
        if (empty($updatedEntries)) {
            throw new InvalidInputWarning();
        }

        foreach ($updatedEntries as $id => $entry)
        {
            $id = mb_strtolower($id);

            /* before doing anything, check if entry exists */
            if (!$database->exists("effort.${id}")) {

                /* error - no such entry */
                throw new InvalidInputWarning();

            /* update the row */
            } elseif (!$database->update("effort.${id}", $entry)) {
                /* error - unable to perform update - possibly readonly */
                return false;
            }
        } /* end for */
        /* commit changes */
        return $database->write();
    }


    /**
     * project_write_delete_effort
     *
     * returns bool(true) on success and bool(false) on error
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
     * @name        plugin_project::project_write_delete_effort()
     */
    public function project_delete_effort (array $selected_entries)
    {
        $database = self::getDatabase();
        /* remove entry from database */
        foreach ($selected_entries as $id)
        {
            if (!$database->remove("effort.${id}")) {
                /* entry does not exist */
                throw new InvalidInputWarning();
            }
        } /* end for */
        /* commit changes */
        return $database->write();
    }

    /**
     * project_write_new_effort
     *
     * returns bool(true) on success and bool(false) on error
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
     * @name        plugin_project::project_write_new_effort()
     */
    public function project_new_effort ()
    {
        $newEntry = self::getEffortForm()->getInsertValues();

        /* no data has been provided */
        if (empty($newEntry)) {
            throw new InvalidInputWarning();
        }

        $database = self::getDatabase();
        /* insert new entry into table */
        if (!$database->insert("effort.*", $newEntry)) {
            throw new InvalidInputWarning();
        } else {
            return $database->write();
        }
    }

    /**
     * event handler
     *
     * returns bool(true) on success and bool(false) on error
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
     * @return      bool
     */
    public function project_search_project()
    {
        if (!$this->project()) {
            return false;
        }
        $form = $this->getProjectForm();
        $where = $form->getSearchValuesAsWhereClause();
        if (!is_null($where)) {
            $form->getQuery()->setHaving($where);
        }
        return true;
    }

    /**
     * event handler
     *
     * returns bool(true) on success and bool(false) on error
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
     * @return      bool
     */
    public function project_search_effort()
    {
        if (!$this->project()) {
            return false;
        }
        $form = $this->getEffortForm();
        $where = $form->getSearchValuesAsWhereClause();
        if (!is_null($where)) {
            $form->getQuery()->setHaving($where);
        }
        return true;
    }

}

?>