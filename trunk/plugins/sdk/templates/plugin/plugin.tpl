{$plugin}
declare(strict_types=1);

namespace {$plugin->getNamespace()};

/**
 * <<plugin>> Class "{$plugin->getClassName()}"
 *
 * @package     yana
 * @subpackage  plugins
 */
class {$plugin->getClassName()} extends \Yana\Plugins\AbstractPlugin
{

{if $schema}
    /**
     * Connection to data source (API)
     *
     * @var  \Yana\Db\IsConnection  Database-API with Query-Builder (also works with text-files)
     */
    private $_database = null;

    /**
     * Return database connection.
     *
     * @return  \Yana\Db\IsConnection
     */
    protected function _getDatabase(): \Yana\Db\IsConnection
    {
        if (!isset($this->_database)) {
            $this->_database = $this->_connectToDatabase('{$schema->getName()}');
        }
        return $this->_database;
    }

{foreach item="form" from=$schema->getForms()}
{if $form->getSchemaName() == $schema->getName()}
    /**
     * Get form definition.
     *
     * @return  \Yana\Forms\Facade
     */
    protected function _get{$form->getName()|capitalize}Form(): \Yana\Forms\Facade
    {
        $builder = $this->_getApplication()->buildForm('{$schema->getName()}', '{$form->getName()}');
        return $builder->__invoke();
    }
{/if}

{/foreach}
{/if}
    /**
     * Default event handler.
     *
     * The default event handler catches all events, whatever they might be.
     * If you don't need it, you may deactive it by adding an @ignore to the annotations below.
     *
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of arguments passed to the function
     * @return  bool
     * @ignore
     */
    public function catchAll($event, array $ARGS)
    {
        // @todo add your code here
        return true;
    }

    /* NOTE:
     * All member-functions stated here act as action handlers (event handlers) and may be called
     * directly in a browser by typing: index.php?action=function_name
     *
     * You may exclude a single function from this behaviour by either making it non-public, or by
     * adding @ignore to the function description.
     */

{foreach item="method" from=$plugin->getMethods()}{if !$method->isAutoGenerated()}

{$method}
    {
        // @todo add your code here
        return true;
    }

{/if}{/foreach}

{if $schema}
{foreach item="form" from=$schema->getForms()}
{if $form->getSchemaName() == $schema->getName()}
    /**
     * Provide edit-form.
     *
     * @type      read
     * @user      group: {$plugin->getId()}
     * @user      group: admin, level: 1
     * @menu      group: {if $plugin->getType() === 'config'}setup{else}start{/if}

     * @title     {$form->getName()}
     * @template  templates/{$form->getName()}.html.tpl
     * @language  {$plugin->getId()}
     */
    public function {$plugin->getId()}{ucfirst($form->getName())}()
    {
        // @todo add your code here
    }

{if $form->getEvent('search')}
    /**
     * Process search query.
     *
     * @type      read
     * @user      group: {$plugin->getId()}
     * @user      group: admin, level: 1
     * @template  templates/{$form->getName()}.html.tpl
     * @language  {$plugin->getId()}
     */
    public function {$form->getEvent('search')->getAction()}()
    {
        // @todo add your code here
    }

{/if}
{if $form->getEvent('update')}
    /**
     * Save changes made in edit-form.
     *
     * @type       write
     * @user       group: {$plugin->getId()}, role: moderator
     * @user       group: admin, level: 75
     * @template   MESSAGE
     * @language   {$plugin->getId()}
     * @onsuccess  goto: {$plugin->getId()}{$form->getName()}
     * @onerror    goto: {$plugin->getId()}{$form->getName()}
     * @return     bool
     */
    public function {$form->getEvent('update')->getAction()}()
    {
        $form = $this->_get{$form->getName()|capitalize}Form();
        $worker = new \Yana\Forms\Worker($this->_getDatabase(), $form);
        return $worker->update();
    }

{/if}
{if $form->getEvent('delete')}
    /**
     * Delete an entry.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @type       write
     * @user       group: {$plugin->getId()}, role: moderator
     * @user       group: admin, level: 75
     * @template   MESSAGE
     * @language   {$plugin->getId()}
     * @onsuccess  goto: {$plugin->getId()}{$form->getName()}
     * @onerror    goto: {$plugin->getId()}{$form->getName()}
     * @param      array  $selected_entries  array of entries to delete
     * @return     bool
     */
    public function {$form->getEvent('delete')->getAction()}(array $selected_entries)
    {
        $form = $this->_get{$form->getName()|capitalize}Form();
        $worker = new \Yana\Forms\Worker($this->_getDatabase(), $form);
        return $worker->delete($selected_entries);
    }

{/if}
{if $form->getEvent('insert')}
    /**
     * Write new entry to database.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @type       write
     * @user       group: {$plugin->getId()}, role: moderator
     * @user       group: admin, level: 30
     * @template   MESSAGE
     * @language   {$plugin->getId()}
     * @onsuccess  goto: {$plugin->getId()}{$form->getName()}
     * @onerror    goto: {$plugin->getId()}{$form->getName()}
     * @return     bool
     */
    public function {$form->getEvent('insert')->getAction()}()
    {
        $form = $this->_get{$form->getName()|capitalize}Form();
        $worker = new \Yana\Forms\Worker($this->_getDatabase(), $form);
        return $worker->create();
    }

{/if}
{if $form->getEvent('export')}
    /**
     * Export entry from database.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @type       read
     * @user       group: {$plugin->getId()}, role: moderator
     * @user       group: admin, level: 75
     * @template   NULL
     * @return     string
     */
    public function {$form->getEvent('export')->getAction()}()
    {
        $form = $this->_get{$form->getName()|capitalize}Form();
        $worker = new \Yana\Forms\Worker($this->_getDatabase(), $form);
        return $worker->export();
    }

{/if}
{if $form->getEvent('download') && $form->getEvent('download')->getAction() != 'download_file'}
    /**
     * Download action.
     *
     * {literal}{@internal
     * If you need to restrict access to images or files in the database,
     * please add appropriate security tests to this function.
     * }}{/literal}
     *
     * @type    read
     * @user    group: {$plugin->getId()}
     * @user    group: admin, level: 100
     */
    public function {$form->getEvent('download')->getAction()}()
    {
        $this->_downloadFile();
    }
{/if}
{/if}
{/foreach}
{/if}

}
