<?php{$plugin}

/**
 * <<plugin>> class "{$plugin->getClassName()}"
 *
 * @package     yana
 * @subpackage  plugins
 */
class {$plugin->getClassName()} extends StdClass implements IsPlugin
{ldelim}

{if $schema}
    /**
     * Connection to data source (API)
     *
     * @access  private
     * @static
     * @var     DBStream  Database-API with Query-Builder (also works with text-files)
     */
    private static $database = null;

{foreach item="form" from=$schema->getForms()}
{if $form->getSchemaName() == $schema->getName()}
    /**
     * Form definition {$form->getName()}
     *
     * @access  private
     * @static
     * @var     DDLDefaultForm
     */
    private static ${$form->getName()}Form = null;

{/if}
{/foreach}
    /**
     * return database connection
     *
     * @access  protected
     * @static
     * @return  DBStream
     * @ignore
     */
    protected static function getDatabase()
    {ldelim}
        if (!isset(self::$database)) {ldelim}
            self::$database = Yana::connect("{$schema->getName()}");
        {rdelim}
        return self::$database;
    {rdelim}
    
    /**
     * auto-update form values
     *
     * This function determines if the given form has any updated values and if
     * so it will try to update the underlying table.
     *
     * The update may fail either because there is nothing to update, or the
     * database operation causes an error.
     *
     * The function will return bool(true) on success and bool(false) on error.
     *
     * @access  protected
     * @static
     * @param   DDLForm  $form  form that should be updated
     * @return  bool
     * @throws  Warning                if some of the input is missing
     * @throws  InvalidValueException  if the input contains invalid values
     * @ignore
     */
    protected static function updateContent(DDLForm $form)
    {ldelim}
        $updatedEntries = $form->getUpdateValues();
        $table = $form->getTable();

        if (empty($updatedEntries)) {ldelim}
            throw new InvalidInputWarning(); // no data has been provided
        {rdelim}

        $database = self::getDatabase();
        foreach ($updatedEntries as $id => $entry)
        {ldelim}
            $id = mb_strtolower($id);

            // before doing anything, check if entry exists
            if (!$database->exists("$table.$id")) {ldelim}
                throw new InvalidInputWarning(); // error - no such entry
            {rdelim}

            // update the row (may throw InvalidValueException)
            if (!$database->update("$table.$id", $entry)) {ldelim}
                // error - unable to perform update (may happen when permission to change the dataset is denied)
                return false;
            {rdelim}
        {rdelim}
        return $database->commit(); // returns true on success and false on error
    {rdelim}

    /**
     * delete rows from a form
     *
     * This function tries to delete entries from the given form and will return
     * bool(true) on success and bool(false) on error.
     *
     * An exception is thrown if the provided input contains invalid data.
     *
     * @access  protected
     * @static
     * @param   DDLForm  $form             form that contains the entries
     * @param   array    $selectedEntries  list of entries the should be deleted
     * @return  bool
     * @throws  Warning  if no entries were selected or a selected entry does not exist
     * @ignore
     */
    protected static function deleteContent(DDLForm $form, array $selectedEntries)
    {ldelim}
        $table = $form->getTable();
        if (empty($selectedEntries)) {ldelim}
            throw new InvalidInputWarning();  // no data has been provided
        {rdelim}
        $database = self::getDatabase();
        // remove entry from database
        foreach ($selectedEntries as $id)
        {ldelim}
            if (!$database->remove("$table.$id")) {ldelim}
                throw new InvalidInputWarning(); // entry does not exist
            {rdelim}
        {rdelim}
        return $database->commit();
    {rdelim}

    /**
     * add content to the form
     *
     * This function determines if the given form has a new row and if so it
     * will try to insert it into the underlying table.
     *
     * This may fail either because there is no new row, or the database
     * operation causes an error.
     *
     * The function will return bool(true) on success and bool(false) on error.
     *
     * @access  protected
     * @static
     * @param   DDLForm  $form  form that should be updated
     * @return  bool
     * @throws  Warning                if some of the input is missing
     * @throws  InvalidValueException  if the input contains invalid values
     * @ignore
     */
    protected static function insertContent(DDLForm $form)
    {ldelim}
        $newEntry = $form->getInsertValues();
        $table = $form->getTable();

        if (empty($newEntry)) {ldelim}
            throw new InvalidInputWarning(); // no data has been provided
        {rdelim}

        $database = self::getDatabase();
        // insert new entry into table (may throw InvalidValueException)
        if (!$database->insert($table, $newEntry)) {ldelim}
            return false;
        {rdelim}
        return $database->commit();
    {rdelim}

    /**
     * download a file
     *
     * This function will automatically determine the requested resource. It will
     * check whether it is of type "image" or "file" and handle the request
     * accordingly. This means it will be sending appropriate headers,
     * retrieving and outputting the contents of the resource and terminating
     * the program.
     *
     * @access  protected
     * @static
     */
    protected static function downloadFile()
    {ldelim}
        $source = DbBlob::getFileId();
        if ($source === false) {ldelim}
            exit("Error: invalid resource.");
        {rdelim}
        $dir = preg_quote(DbBlob::getDbBlobDir(), '/');
        // downloading a file
        if (preg_match('/^' . $dir . 'file\.\w+\.gz$/', $source)) {ldelim}

            $dbBlob = new DbBlob($source);
            $dbBlob->read();
            header('Content-Disposition: attachment; filename=' . $dbBlob->getPath());
            header('Content-Length: ' . $dbBlob->getFilesize());
            header('Content-type: ' . $dbBlob->getMimeType());
            print $dbBlob->getContent();

        // downloading an image
        {rdelim} elseif (preg_match('/^' . $dir . '(image|thumb)\.\w+\.png$/', $source)) {ldelim}
            $image = new Image($source);
            $image->outputToScreen();
        {rdelim} else {ldelim}
            print "Error: invalid resource.";
        {rdelim}
        exit;
    {rdelim}

{foreach item="form" from=$schema->getForms()}
{if $form->getSchemaName() == $schema->getName()}
    /**
     * get form definition
     *
     * @access  protected
     * @static
     * @return  DDLDefaultForm
     */
    protected static function get{$form->getName()|capitalize}Form()
    {ldelim}
        if (!isset(self::${$form->getName()}Form)) {ldelim}
            $database = self::getDatabase();
            self::${$form->getName()}Form = $database->schema->getForm("{$form->getName()}");
        {rdelim}
        return self::${$form->getName()}Form;
    {rdelim}
{/if}

{/foreach}
{/if}
    /**
     * Default event handler
     *
     * The default event handler catches all events, whatever they might be.
     * If you don't need it, you may deactive it by adding an @ignore to the annotations below.
     *
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of arguments passed to the function
     * @return  bool
     * @ignore
     */
    function _default($event, array $ARGS)
    {ldelim}
        $event = strtolower("$event");

        /* global variables */
        global $YANA;

        /* do something */

        /* NOTE: event handlers should always return a boolean value.
         * Return "true" on success or "false" on error.
         */
        return true;
    {rdelim}

    /* NOTE:
     * All member-functions stated here act as action handlers (event handlers) and may be called
     * directly in a browser by typing: index.php?action=function_name
     *
     * You may exclude a single function from this behaviour by either making it non-public, or by
     * adding @ignore to the function description.
     */

{foreach item="method" from=$plugin->getMethods()}{if !$method->isAutoGenerated()}

{$method}
    {ldelim}
        /* @todo implement this function */
        return true;
    {rdelim}

{/if}{/foreach}

{if $schema}
{foreach item="form" from=$schema->getForms()}
{if $form->getSchemaName() == $schema->getName()}
    /**
     * provide edit-form
     *
     * @type      read
     * @user      group: {$plugin->getId()}
     * @user      group: admin, level: 1
     * @menu      group: start
     * @title     {$form->getName()}
     * @template  templates/{$form->getName()}.html.tpl
     * @language  {$plugin->getId()}
     * @access    public
     * @return    bool
     */
    public function {$plugin->getId()}_{$form->getName()}()
    {ldelim}
        return true;
    {rdelim}

{if $form->getSearchAction()}
    /**
     * process search query
     *
     * @type      read
     * @user      group: {$plugin->getId()}
     * @user      group: admin, level: 1
     * @template  templates/{$form->getName()}.html.tpl
     * @language  {$plugin->getId()}
     * @access    public
     * @return    bool
     */
    public function {$form->getSearchAction()}()
    {ldelim}
        $form = self::get{$form->getName()|capitalize}Form();
        $having = $form->getSearchValuesAsWhereClause();
        if (!is_null($having)) {ldelim}
            $form->getQuery()->setHaving($having);
        {rdelim}
        return true;
    {rdelim}

{/if}
{if $form->getUpdateAction()}
    /**
     * save changes made in edit-form
     *
     * @type       write
     * @user       group: {$plugin->getId()}, role: moderator
     * @user       group: admin, level: 75
     * @template   MESSAGE
     * @language   {$plugin->getId()}
     * @onsuccess  goto: {$plugin->getId()}_{$form->getName()}
     * @onerror    goto: {$plugin->getId()}_{$form->getName()}
     * @access     public
     * @return     bool
     */
    public function {$form->getUpdateAction()}()
    {ldelim}
        $form = self::get{$form->getName()|capitalize}Form();
        return self::updateContent($form);
    {rdelim}

{/if}
{if $form->getDeleteAction()}
    /**
     * delete an entry
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @type       write
     * @user       group: {$plugin->getId()}, role: moderator
     * @user       group: admin, level: 75
     * @template   MESSAGE
     * @language   {$plugin->getId()}
     * @onsuccess  goto: {$plugin->getId()}_{$form->getName()}
     * @onerror    goto: {$plugin->getId()}_{$form->getName()}
     * @access     public
     * @param      array  $selected_entries  array of entries to delete
     * @return     bool
     */
    public function {$form->getDeleteAction()}(array $selected_entries)
    {ldelim}
        $form = self::get{$form->getName()|capitalize}Form();
        return self::deleteContent($form, $selected_entries);
    {rdelim}

{/if}
{if $form->getInsertAction()}
    /**
     * write new entry to database
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @type       write
     * @user       group: {$plugin->getId()}, role: moderator
     * @user       group: admin, level: 30
     * @template   MESSAGE
     * @language   {$plugin->getId()}
     * @onsuccess  goto: {$plugin->getId()}_{$form->getName()}
     * @onerror    goto: {$plugin->getId()}_{$form->getName()}
     * @access     public
     * @return     bool
     */
    public function {$form->getInsertAction()}()
    {ldelim}
        $form = self::get{$form->getName()|capitalize}Form();
        return self::insertContent($form);
    {rdelim}

{/if}
{if $form->getExportAction()}
    /**
     * write new entry to database
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @type       read
     * @user       group: {$plugin->getId()}, role: moderator
     * @user       group: admin, level: 75
     * @template   NULL
     * @access     public
     * @return     string
     */
    public function {$form->getExportAction()}()
    {ldelim}
        $query = self::get{$form->getName()|capitalize}Form()->getQuery();
        return $query->toCSV();
    {rdelim}

{/if}
{if $form->getDownloadAction() != 'download_file'}
    /**
     * download action
     *
     * {ldelim}@internal
     * If you need to restrict access to images or files in the database,
     * please add appropriate security tests to this function.
     * {rdelim}{rdelim}
     *
     * @type    read
     * @user    group: {$plugin->getId()}
     * @user    group: admin, level: 100
     * @access  public
     */
    public function {$form->getDownloadAction()}()
    {ldelim}
        self::downloadFile();
    {rdelim}
{/if}
{/if}
{/foreach}
{/if}

{rdelim}
?>