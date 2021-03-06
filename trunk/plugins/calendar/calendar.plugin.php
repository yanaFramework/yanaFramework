<?php
/**
 * Calendar
 *
 * Provides basic functionality for import, export, browsing and editing of calendars in ICal-format
 * (RFC 5545 Standard).
 *
 * {@translation
 *
 *   de:   Kalender
 *
 *         Bietet grundlegende Funktionalität zum Importieren, Exportieren, Anzeigen und bearbeiten
 *         von Kalendern im ICal-Format (RFC 5545 Standard).
 *
 * }
 *
 * @author     Dariusz Josko
 * @author     Thomas Meyer
 * @type       primary
 * @group      calendar
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

namespace Plugins\Calendar;

/**
 * Calendar plugin
 *
 * @package    yana
 * @subpackage plugins
 */
class CalendarPlugin extends \Yana\Plugins\AbstractPlugin
{
    /**
     * @var  \Yana\Db\IsConnection
     */
    private $_database = null;

    /**
     * @var  array
     */
    private $_calendars = array();

    /**
     * Constructor
     *
     * @ignore
     */
    public function __construct()
    {
        $categories = $this->_getPluginsFacade()->getPluginRegistry('calendar')->getVar('categories');
        $result = array();
        if (!empty($categories['category'])) {
            $category = $categories['category'];
            foreach ($category as $cat)
            {
                $itemId = $cat['@id'];
                $item['name'] = $cat['name'];
                $item['color'] = $cat['color'];
                $result[$itemId] = $item;
            }
        }
        \Plugins\Calendar\Calendar::setCategories($result);
        \Plugins\Calendar\Calendar::setAdditionalEventKeys(array('extends' => array('event' => array('created_by'))));
    }

    /**
     * returns database connection
     *
     * @return  \Yana\Db\IsConnection
     */
    protected function _getDatabase()
    {
        if (!isset($this->_database)) {
            $this->_database = $this->_connectToDatabase('calendar');
        }
        return $this->_database;
    }

    /**
     * returns database connection
     *
     * @param   int  $id  calendar id
     * @return  \Plugins\Calendar\Calendar
     */
    private function _getCalendar($id = null)
    {
        // if id is not provided, get last selected calendar ...
        if (!isset($id)) {
            // if no calendar has been selected yet, auto-select the user's default calendar
            if (!isset($_SESSION[__CLASS__]['calendar_id'])) {
                $where = array(
                    array('calendar_default', '=', true),
                    'AND',
                    array('user_created', '=', $this->_getSession()->getCurrentUserName())
                );
                $_SESSION[__CLASS__]['calendar_id'] = $this->_getDatabase()->select("calendar.?.calendar_id", $where);
                unset($where);
            }
            // read the selected calendar id from session cache
            $id = $_SESSION[__CLASS__]['calendar_id'];
        }
        assert(is_int($id), 'Wrong argument type argument 1. Integer expected');
        if (!isset($this->_calendars[$id])) {

            // get calendar settings from database
            $dataset = $this->_getDatabase()->select("calendar.$id");

            if (empty($dataset) || !is_array($dataset)) {
                return null; // error - no such calendar
            }

            $_SESSION[__CLASS__]['calendar_filename'] = $this->_fileIdtoPath($dataset['CALENDAR_FILENAME']);
            $path = $_SESSION[__CLASS__]['calendar_filename'];
            $calendar = new \Plugins\Calendar\Calendar($path, $this->_getPluginsFacade()->getPluginRegistry('calendar'), $id);
            $owner = $dataset['USER_CREATED'];
            $calendar->setOwner($owner);
            $name = $dataset['CALENDAR_NAME'];
            $calendar->setName($name);
            $this->_calendars[$id] = $calendar;
        }
        return $this->_calendars[$id];
    }

    /**
     * set xml
     *
     * set the calendar overview page
     *
     * @type        read
     * @user        group: admin, level: 100
     * @menu        group: start
     * @template    templates/user_calendar.html.tpl
     * @language    calendar
     * @language    admin
     * @language    user
     * @style       templates/calendar.css
     * @style       templates/fullcalendar.css
     * @script      templates/jquery-ui-1.8.11.custom.min.js
     * @script      templates/fullcalendar.min.js
     * @script      ../../skins/default/scripts/gui_generator.js
     * @script      ../../skins/default/scripts/calendar/calendar.js
     * @script      ../../skins/default/scripts/calendar/calendar-setup.js
     * @style       ../../skins/default/scripts/calendar/calendar.css
     */
    public function get_calendar_input()
    {
        $yana = $this->_getApplication();
        $pluginRegistry = $this->_getPluginsFacade()->getPluginRegistry('calendar');
        // set default frequency
        $frequency = $pluginRegistry->getVar('frequency');
        if (!empty($frequency)) {
            $frequency = $frequency['freq'];
        } else {
            $frequency = array();
        }
        $yana->setVar('frequencyOptions', $frequency);

        // set default days
        $days = $pluginRegistry->getVar('days');
        if (!empty($days)) {
            $days = $days['day'];
        } else {
            $days = array();
        }
        $yana->setVar('dayOptions', $days);

        // set default months [diference betwen the other default : array start with 0]
        $month = $pluginRegistry->getVar('months');
        if (!empty($month)) {
            $month = $month['month'];
        } else {
            $month = array();
        }
        $yana->setVar('monthOptions', $month);

        // set default categories
        $yana->setVar('categories', \Plugins\Calendar\Calendar::getCategories());

        // set default month repeat options

        $monthRepeatOpt = $pluginRegistry->getVar('repeat_month_options');
        $yana->setVar('monthRepeatOpt', $monthRepeatOpt['option']);

        $numbers = array();
        for ($i = 1; $i <= 31; $i++)
        {
            $numbers[$i] = $i;
        }

        $yana->setVar('monthNumbers', $numbers);

        // get calendar list
        $userCalendarList = $this->_getCalendarList();

        if (empty($userCalendarList)) {
            $createCalendar = $this->_createCalendar('default');
            if ($createCalendar) {
                $userCalendarList = $this->_getCalendarList();
            }
            $yana->setVar('calendarList', $userCalendarList);
        } else {
            $yana->setVar('calendarList', $userCalendarList);
        }

        $defaultCalendar = $this->_getCalendar();
        if (!empty($defaultCalendar)) {
            $yana->setVar('defaultCalendarID', $defaultCalendar->getId());
            $calendarName = basename($defaultCalendar->getPath(), '.xml');
            $yana->setVar('calendarName', $calendarName);
        }
    }

    /**
     * Returns list of calendar events.
     *
     * @access  public
     * @param   int     $current_calendar_id  current calendar ID
     * @param   string  $calendar_id          calendar ID
     * @return  array
     */
    public function display_calendar($current_calendar_id, $calendar_id = null)
    {
        $data = array();
        if (empty($calendar_id)) {
            $calendar = $this->_getCalendar();
            $data = $calendar->getMergedEvents();
        } else {
            $data = $this->set_double_view($calendar_id, $current_calendar_id);
        }
        foreach ($data as &$event)
        {
            if (!empty($event['start'])) {
                $event['start'] = date('Y-m-d H:i:s', (int) (is_float($event['start']) ? $event['start'] / 1000 : $event['start']));
            }
            if (!empty($event['end'])) {
                $event['end'] = date('Y-m-d H:i:s', (int) (is_float($event['end']) ? $event['end'] / 1000 : $event['end']));
            }
        }
        return $data;
    }

    /**
     * Returns content of two calendars.
     *
     * @access  public
     * @param   string  $calendarIDs  calendarID
     * @param   int     $defaultID    current calendar ID
     * @return  array
     */
    public function set_double_view($calendarIDs, $defaultID)
    {
        $className = array('event_red', 'event_dark_blue', 'event_green', 'event_light_blue', 'event_light_orange');
        $events = array();
        $calendarIDs = explode(',', $calendarIDs);
        $calendarIDs[] = $defaultID;
        if (is_array($calendarIDs)) {
            foreach ($calendarIDs as $id)
            {
                if (empty($id)) {
                    continue;
                } else {
                    $id = (int) $id;
                }
                $secondCalendar = $this->_getCalendar($id);
                $secondCalendar->setColor(array_pop($className));
                if ($id != $defaultID) {
                    $secondCalendar->setDisableEvents(true);
                }
                $events[] = $secondCalendar->getMergedEvents();
                unset($secondCalendar);
            }
        }

        $eventSet = array();
        foreach($events as $dataset)
        {
            foreach($dataset as $key => $item)
            {
                $eventSet[] = $item;
            }
        }
        return $eventSet;
    }

    /**
     * Creates a new calendar file for the current user.
     *
     * @template   MESSAGE
     * @type       write
     * @user       group: admin, level: 100
     * @onsuccess  goto: GET_CALENDAR_INPUT
     * @onerror    goto: GET_CALENDAR_INPUT
     * @access     public
     * @param      string  $new_calendar_name  name of newly created calendar
     * @return     bool
     */
    public function new_calendar($new_calendar_name)
    {
        return $this->_createCalendar($new_calendar_name);
    }

    /**
     * Creates a new calendar file for the current user.
     *
     * @param   string  $name  calendar name
     * @return  bool
     */
    protected function _createCalendar($name)
    {
        assert(is_string($name), 'Wrong argument type argument 1. String expected');

        if (empty($name)) {
            return false;
        }

        /* @var $dir \Yana\Files\Dir */
        $dir = $this->_getPluginsFacade()->getFileObjectFromVirtualDrive('calendar:/xcal');

        // this is the model path of the calendar which contains the body of the calendar
        $path = $dir->getPath() . 'model.xml';

        // load the calendar model content
        $content = file_get_contents($path);
        if (!empty($content)) {
            $newCalendarXML = new \SimpleXMLElement($content);
            $xmlCalendarModel = $newCalendarXML->asXML();
        } else {
            return false;
        }

        // create a new calendar file for the current user
        $fileName = $name.time().'calendar';
        $savePath = $dir->getPath() . $fileName.'.xml';
        $file = new \Yana\Files\Text($savePath);
        if (!$file->exists()) {
            $file->create();
        }
        $file->setContent($xmlCalendarModel);
        try {
            $file->write();
            // insert a new database entry with the currentUser informations about the calendar file
            $result = $this->_insertCalendar($name, $fileName);

        } catch(\Exception $e) {
            $result = false;
        }

        if ($result) {
            $db = $this->_getDatabase();
            $db->commit();
        }

        return $result;
    }

    /**
     * get current calendar path
     *
     * This function set a path too the calendar which is expected
     *
     * @param   string  $id  file identifier
     * @return  string
     * @ignore
     */
    protected function _fileIdtoPath($id)
    {
        if (empty($id)) {
            return null;
        }

        $dir = $this->_getPluginsFacade()->getFileObjectFromVirtualDrive('calendar:/xcal');
        return $dir->getPath() . $id . '.xml';
    }

    /**
     * set_calendar_event_save
     *
     * Get the event data form for update or insert.
     * Additionally for this steep is a check if an event is for an other user.
     *
     * @type        write
     * @user        group: admin, level: 100
     *
     * @access      public
     * @param       array   $ARGS  event arguments
     * @return      array
     */
    public function set_calendar_event_save(array $ARGS)
    {
        $YANA = $this->_getApplication();
        $event = $ARGS['event'];
        $eventData = $this->_prepareEventData($event);
        if ($eventData['freq'] == 'NONE') {
            $fields = array('freq','alldayinterval', 'monthrepeatinterval', 'monthdayinterval', 'numbers', 'month',
                'year_weekinterval', 'year_day', 'year_month', 'until_date', 'count_nr');
            foreach ($fields as $field)
            {
                if(isset($eventData[$field])) {
                    unset($eventData[$field]);
                }
            }
        }
        //add user created
        $eventData['created_by'] = $this->_getSession()->getCurrentUserName();

        // this contain the calendar ids which are updated too
        $calendarIDs = array();
        if (!empty($ARGS['user_id'])) {
            $otherUserCalendarIDs = $ARGS['user_id'];
            $calendarIDs = explode(',', $otherUserCalendarIDs);
        }

        // current user informations - if the event is for himself
        $currentUserID = $ARGS['current_user_calendar_id'];
        $insertForDefaultUser = false;
        if (!empty($ARGS['insert_for_default'])) {
            $insertForDefaultUser = $ARGS['insert_for_default'];
        }

        $eventID = '';
        if (isset($eventData['eventid'])) {
            $eventID = $eventData['eventid'];
        }

        if (empty($eventID) && empty($calendarIDs) && $insertForDefaultUser == 'true') {
            $result = true;
            // make an entry for yourself
            $calendar = $this->_getCalendar();
            $calendar->insertOrUpdateEvent($eventData);
            return $calendar->getMergedEvents();
        }

        $result = false;
        if (!empty($eventID) && $insertForDefaultUser == 'true' && !empty($eventData)) {
            $calendar = $this->_getCalendar();
            $result = $calendar->insertOrUpdateEvent($eventData);
            if (!empty($calendarIDs)) {
                $eventUpdated = $calendar->getEventById(); //result is the updated event
            }
        }
        $isUpdated = !empty($eventUpdated);
        if ($isUpdated) {
            // add the current user calendar id into the array
            array_push($calendarIDs, $currentUserID);
        }

        if (isset($calendarIDs) && !empty($calendarIDs)) {
            foreach ($calendarIDs as $id)
            {
                if (empty($id) || empty($eventData)) {
                    continue;
                }

                $calendar = $this->_getCalendar($id);
                if ($isUpdated) {
                    $calendar->removeEventById($eventID);
                    $result = $calendar->insertEvent($eventUpdated);
                } else {
                    $result = $calendar->insertOrUpdateEvent($eventData);
                }
            }
        }

        if (!$result) {
            return array();
        }
        $defaultCalendar = $this->_getCalendar();
        return $defaultCalendar->getMergedEvents();
    }

    /**
     * prepare_event_data
     *
     * Prepare the serialized array dataset
     *
     * @param   array  $events  event arguments
     * @return  array
     * @ignore
     */
    protected function _prepareEventData(array $events)
    {
        $data = array();
        foreach($events as $items)
        {
            $check = preg_split("/\[([^\]]*)\]/", $items['name']);
            if (is_array($check) && isset($check[1])) {
                $name = $check[0];
                $subName = preg_match_all("/\[([^\]]*)\]/", $items['name'], $sub);
                if (isset($sub[1])) {
                    $data[strtolower($name)][strtolower($sub[1][0])] = $items['value'];
                }
            } else {
                $data[strtolower($items['name'])] = $items['value'];
            }
        }

        return $data;
    }

    /**
     * Prepare the event by resize.
     *
     * @type    write
     * @user    group: admin, level: 100
     *
     * @access  public
     * @param   string  $eventid  event identifier
     * @param   int     $resize   ammount of days to resize
     * @param   int     $min      ammount of minutes to resize
     * @return  array
     */
    public function update_event_by_resize($eventid, $resize = 0, $min = 0)
    {
        $calendar = $this->_getCalendar();
        if ($calendar->updateEventByResize($eventid, $resize, $min)) {
            return $calendar->getMergedEvents();
        } else {
            return array();
        }
    }

    /**
     * Updates the event after drag'n'drop.
     *
     * @type        write
     * @user        group: admin, level: 100
     *
     * @access      public
     * @param   string  $eventid  event identifier
     * @param   int     $resize   ammount of days to resize
     * @param   int     $min      ammount of minutes to resize
     * @return  array
     */
    public function update_event_by_drop($eventid, $resize, $min = 0)
    {
        $calendar = $this->_getCalendar();
        if ($calendar->updateEventByDrop($eventid, $resize, $min)) {
            return $calendar->getMergedEvents();
        } else {
            return array();
        }
    }

    /**
     * This function sends the ical file (file to download) .
     *
     * @type        write
     * @user        group: admin, level: 100
     * @onsuccess   goto: GET_CALENDAR_INPUT
     * @onerror     goto: GET_CALENDAR_INPUT
     *
     * @access      public
     * @param       array   $ARGS  event arguments
     * @return      bool
     */
    public function calendar_send_event(array $ARGS)
    {
        $calendar = $this->_getCalendar();
        $xmlContent = $calendar->send($ARGS);
        $result = $this->_setICal(null, $xmlContent);
        if (empty($result)) {
            return false;
        } else {
            return $this->_downloadCalendar($result);
        }
    }

    /**
     * Remove the current event by ID.
     *
     * @type    write
     * @user    group: admin, level: 100
     *
     * @access  public
     * @param   array   $ARGS  event arguments
     * @return  array
     */
    public function remove_calendar_event(array $ARGS)
    {
        $removeID = $ARGS['eventid'];
        if (empty($removeID)) {
            return '';
        }
        $calendar = $this->_getCalendar();
        $calendar->removeEventById($removeID);
        return $calendar->getMergedEvents();
    }

    /**
     * calendar_delete_serial_entry
     *
     * This Function set a new exdate for this event
     *
     * @type        write
     * @user        group: admin, level: 100
     * @user        group: calendar
     *
     * @access      public
     * @param       array   $ARGS  event arguments
     * @return      array
     */
    public function calendar_delete_serial_entry(array $ARGS)
    {
        $event = $ARGS['event'];
        $eventData = $this->_prepareEventData($event);
        if (empty($eventData)) {
            return '';
        }
        $eventID = $eventData['eventid'];
        $date = $eventData['start'];
        $calendar = $this->_getCalendar();
        $calendar->setExdate($eventID, $date);
        return $calendar->getMergedEvents();
    }

    /**
     * set calendar view
     *
     * This function set the new default user calendar and load them for overview
     *
     * @type        write
     * @user        group: admin, level: 100
     * @user        group: calendar
     * @onsuccess   goto: GET_CALENDAR_INPUT
     * @onerror     goto: GET_CALENDAR_INPUT
     * @template    MESSAGE
     *
     * @access      public
     * @param       int  $current_calendar  current calendar id
     * @return      bool
     */
    public function set_calendar_view($current_calendar)
    {
        // check if selected calendar exists and the crrent user is it's owner
        $db = $this->_getDatabase();
        $row = $db->select("calendar." . (int) $current_calendar, array('user_created', '=', $this->_getSession()->getCurrentUserName()));
        if (empty($row)) {
            return false; // error - the calendar does not exist, or the user has no permission to view it
        } else {
            $_SESSION[__CLASS__]['calendar_id'] = $current_calendar; // store id for later use
            return true;
        }
    }


    /**
     * get calendar list
     *
     * This function get all calendar for the current user
     *
     * @return  array  current user calendar list
     */
    protected function _getCalendarList()
    {
        $where = array('user_created', '=', $this->_getSession()->getCurrentUserName());
        $db = $this->_getDatabase();
        $calendarList = $db->select("calendar", $where);

        if (empty($calendarList)) {
            return null;
        }
        $list = array();
        foreach ($calendarList as $item)
        {
            $list[$item['CALENDAR_ID']]['NAME'] = $item['CALENDAR_NAME'];
            if (isset($item['CALENDAR_SUBSCRIBE'])) {
                $list[$item['CALENDAR_ID']]['SUBSCRIBE'] = $item['CALENDAR_SUBSCRIBE'];
            } else {
                $list[$item['CALENDAR_ID']]['SUBSCRIBE'] = false;
            }

        }

        if (empty($list)) {
            return array();
        }

        return $list;
    }

    /**
     * Insert new calendar with the current user data.
     *
     * @param   string  $name       name of the current calendar
     * @param   string  $filename   filename of the current calendar
     * @param   string  $url        url of the calendar file (when calendar is subscribe)
     * @param   bool    $subscribe  true when a calendar is subscribe otherweise false
     *
     * @return  bool
     */
    protected function _insertCalendar($name, $filename, $url = "", $subscribe = false)
    {
        $user = $this->_getSession()->getCurrentUserName();
        if (empty($user)) {
            return false;
        }
        if (empty($filename) || empty($name)) {
            return false;
        }
        if ($subscribe) {
            $subscribe = $subscribe;
            $url = $url;
        } else {
            $subscribe = false;
            $url = '';
        }

        $calendarData =  array();
        $calendarData['USER_CREATED'] = $user;
        $calendarData['CALENDAR_NAME'] = $name;
        $calendarData['CALENDAR_FILENAME'] = $filename;
        $calendarData['CALENDAR_DEFAULT'] = true;
        $calendarData['CALENDAR_SUBSCRIBE'] = $subscribe;
        $calendarData['CALENDAR_URL'] = $url;
        $db = $this->_getDatabase();

        if (isset($calendarData)) {
            try {
                $db->insert("calendar", $calendarData);
            } catch (\Exception $e) {
                return false;
            }
        }
        return true;
    }

    /**
     * Import calendar events with holidays.
     *
     * @type        write
     * @user        group: admin, level: 100
     * @user        group: calendar
     * @template    MESSAGE
     * @onsuccess   goto: GET_CALENDAR_INPUT
     * @onerror     goto: GET_CALENDAR_INPUT
     *
     * @access      public
     * @param       string  $key  calendar id
     * @return      bool
     */
    public function refresh_calendar_subscribe($key)
    {
        $db = $this->_getDatabase();
        $data = $db->select("calendar.$key", array('user_created', '=', $this->_getSession()->getCurrentUserName()));
        if (empty($data) || !isset($data['CALENDAR_URL']) || !isset($data['CALENDAR_FILENAME'])) {
            return false; // has no URL - nothing to refresh
        }
        $xml = $this->_iCalToXCal($data['CALENDAR_URL']); // convert ical into xcal
        if (!$xml) {
            return false;
        }
        $path = $this->_fileIdtoPath($data['CALENDAR_FILENAME']);
        if (file_put_contents($path, $xml)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Subscribe to an internet-calendar.
     *
     * @type        write
     * @user        group: admin, level: 100
     * @user        group: calendar
     * @template    MESSAGE
     * @onsuccess   goto: GET_CALENDAR_INPUT
     * @onerror     goto: GET_CALENDAR_INPUT
     *
     * @access      public
     * @param       array   $ARGS  event arguments
     * @return      bool
     */
    public function subscribe_calendar(array $ARGS)
    {
        if (isset($ARGS['new_calendar_abo'])) {
            $path = $ARGS['new_calendar_abo'];
            $path = str_replace('\\', '/', $path);
        }
        $url = $path;
        $explode = explode('/', $path);

        if (is_array($explode)) {
            $lastEntry = array_pop($explode);
            $calendarName = explode('.', $lastEntry);
            $name = $calendarName[0];
        }
        if (!isset($name)) {
            $name = 'default';
        }
        $filename = $name.time();

        // convert ical into xcal
        $xml = $this->_iCalToXCal($path);
        if ($xml == false) {
            return false;
        }
        $content = $xml;
        $this->_writeXml($content, $filename);
        $result = $this->_insertCalendar($name, $filename, $url, true);
        $db = $this->_getDatabase();
        try {
            $db->commit();
        } catch (\Exception $e) {
            $result = false;
        }
        return $result;
    }

    /**
     * Export changes to XML file.
     *
     * @param   string  $content   file contents
     * @param   string  $fileName  file name
     */
    protected function _writeXml(string $content, string $fileName)
    {
        /* @var $dir Dir */
        $dir = $this->_getPluginsFacade()->getFileObjectFromVirtualDrive('calendar:/xcal');
        $path = $dir->getPath() . $fileName.'.xml';
        $file = new \Yana\Files\Text($path);
        if (!$file->exists()) {
            $file->create();
        }
        $file->setContent($content);
        $file->write();
    }

    /**
     * Remove a calendar.
     *
     * @type        write
     * @template    MESSAGE
     * @user        group: admin, level: 100
     * @user        group: calendar
     * @onsuccess   goto: GET_CALENDAR_INPUT
     * @onerror     goto: GET_CALENDAR_INPUT
     *
     * @access      public
     * @param       int  $key  calendar id
     * @return      bool
     */
    public function remove_user_calendar($key)
    {
        $calendarID = $key;
        $this->_removeCalendarFile($calendarID);
        $where = array('user_created', '=', $this->_getSession()->getCurrentUserName());
        $db = $this->_getDatabase();
        /* remove the row */
        try {
            $db->remove("calendar.{$calendarID}", $where)
                ->commit(); // may throw exception
            return true;
        } catch (\Exception $e) {
            /* error - unable to perform update - possibly readonly */
            return false;
        }
    }

    /**
     * Remove calendar file.
     *
     * @param   integer  $datasetID  id of the current calendar too remove
     * @return  bool
     */
    protected function _removeCalendarFile($datasetID)
    {
        $isSuccess = false;
        $db = $this->_getDatabase();
        $calendar = $db->select("calendar.{$datasetID}");

        if (empty($calendar)) {

            $fileName = $calendar['CALENDAR_FILENAME'];
            if (!empty($fileName)) {
                $isSuccess = (bool) $this->_removeXCalFile($fileName);
            }
        }

        if (!$isSuccess) {
            $message = "Trying to delete a calendar that does not exist.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            \Yana\Log\LogManager::getLogger()->addLog($message, $level, $datasetID);
        }

        return $isSuccess;
    }

    /**
     * Convert ical calendar file to xcal.
     *
     * @param   string  $path  path to ICal file
     * @return  xml|bool    if succesfull than return an xml object otherweise false
     * @ignore
     */
    protected function _iCalToXCal($path)
    {
        assert(is_string($path), 'Wrong argument type argument 1. String expected');

        $icalendarData = file_get_contents($path);
        if (!$icalendarData) {
            return false;
        }
        // Detecting line endings
        if (strpos($icalendarData, "\r\n")) {
            $lb = "\r\n";
        } elseif (strpos($icalendarData, "\n")) {
            $lb = "\n";
        } else {
            $lb = "\r\n";
        }

        // Splitting up items per line
        $lines = explode($lb, $icalendarData);

        // Properties can be folded over 2 lines. In this case the second
        // line will be preceeded by a space or tab.
        $lines2 = array();
        foreach($lines as $line)
        {
            if (isset($line[0]) && ($line[0] == " " || $line[0] == "\t")) {
                $lines2[count($lines2)-1] .= substr($line, 1);
                continue;
            }
            $lines2[]=$line;
        }

        $xml = '<?xml version="1.0"?>' . "\n";
        $xml .= '<!DOCTYPE iCalendar SYSTEM "../../../config/dtd/xcal.dtd">' . "\n";
        $xml .= '<iCalendar>' . "\n";
        $spaces = 0;

        // array with properties for select as last (needed for match the dtd) // remove 'EXDATE' => null
        $lastPropertiesOptions = array (
            'ATTACH' => null,
            'ATTENDEE' => null,
            'CATEGORIES' => null,
            'COMMENT' => null,
            'CONTACT' => null,
            'EXRULE' => null,
            'RDATE' => null,
            'RELATED-TO' => null,
            'RESOURCES' => null,
            'REQUEST-STATUS' => null,
            'RRULE' => null,
            'FREEBUSY' => null,
            'TZNAME' => null
        );
        $vcalendar = array();
        $beginTag = false;
        $xmlcountarrayProperties = 0;

        foreach ($lines2 as $line)
        {
            $matches = array();
            // This matches all rules not starting with an X character
            if (!preg_match("/^[^X^x]+[\-\w]+[\-\w]+/", $line)) {
                continue;
            }

            // This matches PROPERTYNAME;ATTRIBUTES:VALUE
            if (!preg_match('/^([^:^;]*)(?:;([^:]*))?:(.*)$/', $line, $matches)) {
                continue;
            }

            $propertyName = strtoupper($matches[1]);
            $attributes = $matches[2];
            $value = $matches[3];
            // If the line was in the format BEGIN:COMPONENT or END:COMPONENT, we need to special case it.

            if ($propertyName == 'PRODID') {
                $vcalendar['PRODID']= $value;
            } elseif ($propertyName == 'VERSION') {
                $vcalendar['VERSION']= $value;
            } elseif ($propertyName == 'METHOD') {
                $vcalendar['METHOD']= $value;
            }
            if (!empty($vcalendar['PRODID']) && !empty($vcalendar['VERSION']) && $beginTag == false) {

                $xml.=str_repeat(" ", $spaces);
                $xml.='<vcalendar prodid="' . $vcalendar['PRODID'].'" version="'.$vcalendar['VERSION'].'"';
                if (!empty($vcalendar['METHOD'])) {
                     $xml.= ' method="'.$vcalendar['METHOD'].'"';
                }
                $xml.=">\n";
                $spaces+=2;
                $beginTag = true;
                continue;
            }

            if ($propertyName == 'BEGIN') {
                if ($value == 'VCALENDAR') {
                    $spaces += 2;
                    continue;
                }
                if ($xmlcountarrayProperties != 0) {
                    foreach ($lastPropertiesOptions as $keyName => $keyValue)
                    {
                        $xml .= $keyValue;
                        $lastPropertiesOptions[$keyName] = null;
                    }
                    $xmlcountarrayProperties = 0;
                }
                $xml.= str_repeat(" ", $spaces);
                $xml.='<' . strtolower($value) . ">\n";
                $spaces += 2;
                continue;
            } elseif ($propertyName == 'END') {
                if ($xmlcountarrayProperties != 0) {
                    foreach ($lastPropertiesOptions as $keyName => $keyValue)
                    {
                        $xml .= $keyValue;
                        $lastPropertiesOptions[$keyName] = null;
                    }
                    $xmlcountarrayProperties = 0;
                }
                $spaces -= 2;
                $xml .= str_repeat(" ", $spaces);
                $xml .= '</' . strtolower($value) . ">\n";
                continue;
            }

            if ($propertyName == 'PRODID') {
                continue;
            }
            if ($propertyName == 'VERSION') {
                continue;
            }
            if ($propertyName == 'METHOD') {
                continue;
            }

            $xmlProperty = null;
            $xmlProperty .= str_repeat(" ", $spaces);
            $xmlProperty .= '<' . strtolower($propertyName);
            if ($attributes) {
                // There can be multiple attributes
                $attributes = explode(';', $attributes);
                foreach($attributes as $att)
                {
                    list($attName, $attValue) = explode('=', $att, 2);
                    $xmlProperty.=' ' . strtolower($attName) . '="' . htmlspecialchars($attValue) . '"';
                }
            }
            if ($propertyName == 'CATEGORIES') {
                $itemSpace = "        ";
                $tagSpace = "      ";
                $xmlProperty.='>'."\n";
                $items = explode(',', $value);
                if (!empty($items)) {
                    foreach ($items as $item)
                    {
                        $xmlProperty.= $itemSpace.'<item>'. htmlspecialchars($item) . '</item>'."\n";
                    }
                }
                $xmlProperty.= $tagSpace.'</' . strtolower($propertyName) . ">\n";
            } else {
                $xmlProperty.='>'. htmlspecialchars($value) . '</' . strtolower($propertyName) . ">\n";
            }
            if (array_key_exists($propertyName, $lastPropertiesOptions)) {
                $xmlcountarrayProperties ++;
                $lastPropertiesOptions[$propertyName] = $xmlProperty;
            } else {
                $xml .= $xmlProperty;
            }
        }
        $xml .= '</iCalendar>';

        return $xml;
    }


    /**
     * Convert ical format to xcal format.
     *
     * @type        write
     * @template    MESSAGE
     * @user        group: admin, level: 100
     * @user        group: calendar
     * @onsuccess   goto: GET_CALENDAR_INPUT
     * @onerror     goto: GET_CALENDAR_INPUT
     *
     * @access      public
     * @return      bool
     * @param       string  $calendar_name  calender name
     * @throws      \Yana\Core\Exceptions\NotFoundException
     * @throws      \Yana\Core\Exceptions\NotReadableException
     */
    public function set_xcal($calendar_name)
    {
        assert(is_string($calendar_name), 'Wrong argument type argument 1. String expected');

        // check if name is set
        if (empty($calendar_name)) {
            $calendarName = 'default';
        } else {
            $calendarName = $calendar_name;
        }

        $file = $_FILES['file'];
        if (empty($file)) {
            throw new \Yana\Core\Exceptions\NotFoundException('File not found', \Yana\Log\TypeEnumeration::INFO);
        }
        $fileName = explode('.', $file['name']);
        $type = $fileName[1];
        $fileName = $fileName[0];
        $filePath = $file['tmp_name'];
        if (!file_exists($filePath)) {
            $message = 'The expected File: '.$fileName.' does not exist';
            $level = \Yana\Log\TypeEnumeration::INFO;
            throw new \Yana\Core\Exceptions\NotFoundException($message, $level);
            return false;
        }
        if (!isset($type) || $type != 'ics') {
            $message = 'The expected File: '.$file['name'].' does not exist';
            $level = \Yana\Log\TypeEnumeration::INFO;
            throw new \Yana\Core\Exceptions\NotReadableException($message, $level);
            return false;
        }

        // convert ical into xcal
        $xml = $this->_iCalToXCal($filePath);
        if ($xml == false) {
            return false;
        }
        // set calendar name and the file name of the calendar
        $name = $calendarName;
        $fileName = md5($name . $fileName);

        // create the xml calendar file
        $this->_writeXml($xml, $fileName);

        // insert a new database entry when xml file is created
        if (empty($name) || empty($fileName)) {
            return false;
        }
        // inserts entries into database, but does not commit them
        if (!$this->_insertCalendar($name, $fileName)) {
            return false;
        }
        // commit the new entry into database
        $db = $this->_getDatabase();
        $db->commit(); // may throw exception
        return true;
    }

    /**
     * This function convert xcal to ical.
     *
     * The default use of this option is set only the datasetID for convert a xcal file into ical.
     * The second option is to set the datasetID of null and the second paramenter needs a xml content of an event
     * which will be convertet into the ical standard. Important is if both are set than the convert will be executed
     * by the datasetID.
     *
     * @param   integer  $datasetID    id of the current dataset
     * @param   string   $xmlContent   xml contetn of an event
     * @return  bool
     */
    protected function _setICal($datasetID = null, $xmlContent = '')
    {
        $db = $this->_getDatabase();
        if ($datasetID != null && is_int($datasetID)) {
            $where = array('user_created', '=', $this->_getSession()->getCurrentUserName());
            $row = $db->select("calendar.{$datasetID}", $where);

            if (empty($row)) {
                // the expected file does not exist or the current user have no premissions to use this function
                return false;
            }
            // set the path of the xcal file
            if (isset($row['CALENDAR_FILENAME'])) {
                $fileName = $row['CALENDAR_FILENAME'];
                $calendarID = $row['CALENDAR_ID'];
                $calendarName = $row['CALENDAR_NAME'];
                /* @var $dir Dir */
                $dir = $this->_getPluginsFacade()->getFileObjectFromVirtualDrive('calendar:/xcal');
                $path = $dir->getPath() . $fileName.'.xml';
            } else {
                return false;
            }
            $xmlContent = file_get_contents($path);
        } elseif (!empty ($xmlContent)) {
            $xmlContent = $xmlContent;
            $fileName = 'event-'.time();
            $calendarID = time();
            $calendarName = $fileName;
        } else {
            return false;
        }

        $xmlRoot = new \SimpleXMLElement($xmlContent);
        $count = 0;
        $string = null;
        $array= array();
        foreach ($xmlRoot as $root => $children)
        {
            // Begin Element
            $string .= 'BEGIN:'.strtoupper($root)."\n";
            // attributes for the current root element
            foreach ($children->attributes() as $attrName => $attrValue)
            {
                $string .= strtoupper($attrName).':'.$attrValue."\n";
            }
            //children element for the current root element
            foreach ($children as $rootName => $rootValue)
            {
                //add this check if attribute property is set
                $checkAttr = (string)$rootValue->attributes();

                if (empty($checkAttr)) {
                    $string .= 'BEGIN:'.strtoupper($rootName)."\n";
                } else {
                    $additionalValue = (string)$rootValue;
                    $attr = (string)$rootValue->attributes();
                    $additionalKey = strtoupper($rootName).";name=".$attr.":".$additionalValue;
                    $string .= $additionalKey ."\n";
                    continue;
                }
                foreach ($rootValue as $name => $value)
                {
                    if ($value->children()) {
                        if (!$value->attributes()) {
                            // only for categories
                            $categoriesBegin = 0;
                            if (strtoupper($name) == 'CATEGORIES') {
                                $string .= strtoupper($name) . ':';
                                $categoriesBegin = 1;
                            } else {
                                $string .= 'BEGIN:' . strtoupper($name) . "\n";
                            }
                            $categories = null;
                            foreach ($value->children() as $childName => $childValue)
                            {
                                if (strtoupper($name) == 'CATEGORIES') {
                                    if ($categoriesBegin == 0) {
                                        $categoriesSeperator = ',';
                                    } else {
                                        $categoriesSeperator = '';
                                    }
                                    $categories .= $categoriesSeperator . $childValue;
                                    $categoriesBegin = 0;
                                } else {
                                    $string .= strtoupper($childName) . ':' . $childValue . "\n";
                                }
                            }
                            if (isset($categories)) {
                                $string .= $categories . "\n";
                            }
                            if (strtoupper($name) != 'CATEGORIES') {
                                $string .= 'END:' . strtoupper($name) . "\n";
                            }
                        }
                    }
                    if ($value->attributes()) {
                        foreach ($value->attributes() as $key => $item)
                        {
                            if ($name == 'attendee') {
                                if ($key == 'cn') {
                                    $string .= strtoupper($name) . ';' . $key . '=' . $item . ';';
                                }
                                if ($key == 'rsvp') {
                                    $string .= $key . '=' . $item . ':';
                                }
                            }
                        }
                        if ($name == 'attendee') {
                            $string .= $value . "\n";
                        } else {
                            if ($value->attributes()) {
                                $attr = $value->attributes();
                                foreach ($attr as $attrKey => $attrV)
                                {
                                    $additional = explode(':', $value);
                                    if (!isset($additional[1])) {
                                        $string .= strtoupper($name) . ';' . $attrKey . '=' . $attrV . ':' . $additional[0] . "\n";
                                    } else {
                                        $string .= strtoupper($name) . ';' . $attrKey . '=' . $attrV . ':' . $additional[0] . ':' .
                                            $additional[1] . "\n";
                                    }
                                }
                            }
                        }
                    }
                    if (!$value->attributes() && !$value->children()) {
                        $string .= strtoupper($name) . ':' . $value . "\n";
                    }
                }
                $string .= 'END:' . strtoupper($rootName) . "\n";
            }
            $string .= 'END:' . strtoupper($root) . "\n";
        }
        $dataSet = array();
        if (!empty($calendarID)) {
            $dataSet['id'] = $calendarID;
        }
        $dataSet['name'] = $fileName;
        if (isset($path)) {
            $dataSet['path'] = $path;
        } else {
            $dataSet['path'] = '';
        }
        $dataSet['content'] = $string;
        $dataSet['calendarname'] = $calendarName;
        return $dataSet;
    }

    /**
     * Download calendar file.
     *
     * @type        read
     * @user        group: admin, level: 100
     * @user        group: calendar
     * @onerror     goto: GET_CALENDAR_INPUT
     *
     * @access      public
     * @param       int     $key  calendar id
     * @return      bool    return bool false if set_ical get an empty value
     */
    public function calendar_download_file($key)
    {
        $calendarID = $key;
        // convert the xcal dataset into the ical format
        $data = array();
        $data = $this->_setICal($calendarID);

        if (empty($data)) {
            return false;
        }
        return $this->_downloadCalendar($data);
    }

    /**
     * Download ICAL file.
     *
     * @param   array|bool   $data   file information
     * @return  bool         return bool false if the expected param is Empty
     * @ignore
     */
    protected function _downloadCalendar($data)
    {
        if (empty($data)) {
            return false;
        }
        if (isset($data['calendarname'])) {
            $filename = $data['calendarname'].'.ics';
        } else {
            $filename = 'termin.ics';
        }

        if (empty($data['content'])) {
            return false;
        }
        header("Content-type: text/html; charset=utf-8");
        header("Content-type: application/ics");
        header("Cache-Control: maxage=1"); // Bug in IE8 with HTTPS-downloads
        header("Pragma: public");
        header("Content-Disposition: attachment; filename=\"".$filename."\"");
        $content = $data['content'];
        die(utf8_decode($content));
    }

    /**
     * remove user calendar file
     *
     * This function remove the calendar file
     *
     * @param   string  $fileName  name of the removed file
     * @return  bool
     */
    protected function _removeXCalFile($fileName)
    {
        /* @var $dir Dir */
        $dir = $this->_getPluginsFacade()->getFileObjectFromVirtualDrive('calendar:/xcal');

        $path = $dir->getPath() . $fileName.'.xml';
        $deleteUserFile = new \Yana\Files\Text($path);

        if ($deleteUserFile->exists()) {
            $deleteUserFile->delete();
        }

        return true;
    }

}

?>
