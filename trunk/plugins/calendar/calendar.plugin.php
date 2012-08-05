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

/**
 * @ignore
 */
require_once 'calendar.php';

/**
 * @ignore
 */
require_once 'indexnotfoundexception.php';

/**
 * Calendar plugin
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_calendar extends StdClass implements IsPlugin
{
    /**
     * @access  private
     * @static
     * @var     \Yana\Db\IsConnection
     */
    private static $database = null;

    /**
     * @access  private
     * @static
     * @var     array
     */
    private static $calendars = array();

    /**
     * Constructor
     *
     * @access  public
     * @ignore
     */
    public function __construct()
    {
        $categories = self::getCategories();
        Calendar::setCategories($categories);
    }

    /**
     * returns database connection
     *
     * @access  private
     * @static
     * @return  \Yana\Db\IsConnection
     */
    private static function _getDatabase()
    {
        if (!isset(self::$database)) {
            self::$database = Yana::connect('calendar');
        }
        return self::$database;
    }

    /**
     * returns database connection
     *
     * @access  private
     * @static
     * @param   int  $id  calendar id
     * @return  Calendar
     */
    private static function _getCalendar($id = null)
    {
        // if id is not provided, get last selected calendar ...
        if (!isset($id)) {
            // if no calendar has been selected yet, auto-select the user's default calendar
            if (!isset($_SESSION[__CLASS__]['calendar_id'])) {
                $where = array(
                    array('calendar_default', '=', true),
                    'AND',
                    array('user_created', '=', YanaUser::getUserName())
                );
                $_SESSION[__CLASS__]['calendar_id'] = self::_getDatabase()->select("calendar.?.calendar_id", $where);
                unset($where);
            }
            // read the selected calendar id from session cache
            $id = $_SESSION[__CLASS__]['calendar_id'];
        }
        assert('is_int($id); // Wrong argument type argument 1. Integer expected');
        if (!isset(self::$calendars[$id])) {

            // get calendar settings from database
            $dataset = self::_getDatabase()->select("calendar.$id");

            if (empty($dataset) || !is_array($dataset)) {
                return null; // error - no such calendar
            }

            $_SESSION[__CLASS__]['calendar_filename'] = self::fileIdtoPath($dataset['CALENDAR_FILENAME']);
            $path = $_SESSION[__CLASS__]['calendar_filename'];
            $calendar = new Calendar($path, $id);
            $owner = $dataset['USER_CREATED'];
            $calendar->setOwner($owner);
            $name = $dataset['CALENDAR_NAME'];
            $calendar->setName($name);
            self::$calendars[$id] = $calendar;
        }
        return self::$calendars[$id];
    }

    /**
     * getCategories
     *
     * @access  protected
     * @static
     * @return  array
     */
    protected static function getCategories()
    {
        $yana = Yana::getInstance();
        $categories = $yana->getPlugins()->calendar->getVar('categories');
        $result = array();
        if (!empty($categories['category'])) {
            $category = $categories['category'];
            foreach ($category as $cat)
            {
                $item['name'] = $cat['name'];
                $item['color'] = $cat['color'];
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * Default event handler
     *
     * @access  public
     * @return  bool
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of arguments passed to the function
     *
     * @ignore
     */
    public function catchAll($event, array $ARGS)
    {
        return true;
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
     *
     * @access      public
     */
    public function get_calendar_input()
    {
        $yana = Yana::getInstance();
        // set default frequency
        $frequency = $yana->getPlugins()->calendar->getVar('frequency');
        if (!empty($frequency)) {
            $frequency = $frequency['freq'];
        } else {
            $frequency = array();
        }
        $yana->setVar('frequencyOptions', $frequency);

        // set default days
        $days = $yana->getPlugins()->calendar->getVar('days');
        if (!empty($days)) {
            $days = $days['day'];
        } else {
            $days = array();
        }
        $yana->setVar('dayOptions', $days);

        // set default months [diference betwen the other default : array start with 0]
        $month = $yana->getPlugins()->calendar->getVar('months');
        if (!empty($month)) {
            $month = $month['month'];
        } else {
            $month = array();
        }
        $yana->setVar('monthOptions', $month);

        // set default categories
        $categories = $yana->getPlugins()->calendar->getVar('categories');
        if (!empty($categories)) {
            $categories = $categories['category'];
        } else {
            $categories = array();
        }
        $yana->setVar('categories', $categories);

        // set default month repeat options

        $monthRepeatOpt = $yana->getPlugins()->calendar->getVar('repeat_month_options');
        $yana->setVar('monthRepeatOpt', $monthRepeatOpt['option']);

        $numbers = array();
        for ($i = 1; $i <= 31; $i++)
        {
            $numbers[$i] = $i;
        }

        $yana->setVar('monthNumbers', $numbers);

        // get calendar list
        $userCalendarList = self::getCalendarList();

        if (empty($userCalendarList)) {
            $createCalendar = self::createCalendar('default');
            if ($createCalendar) {
                $userCalendarList = self::getCalendarList();
            }
            $yana->setVar('calendarList', $userCalendarList);
        } else {
            $yana->setVar('calendarList', $userCalendarList);
        }

        $defaultCalendar = self::_getCalendar();
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
            $calendar = self::_getCalendar();
            $data = $calendar->getMergedEvents();
        } else {
            $data = $this->set_double_view($calendar_id, $current_calendar_id);
        }
        foreach ($data as &$event)
        {
            if (!empty($event['start'])) {
                $event['start'] = date('Y-m-d H:i:s', $event['start']);
            }
            if (!empty($event['end'])) {
                $event['end'] = date('Y-m-d H:i:s', $event['end']);
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
                $secondCalendar = self::_getCalendar($id);
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
        return self::createCalendar($new_calendar_name);
    }

    /**
     * Creates a new calendar file for the current user.
     *
     * @param   string   $name  calendar name
     * @static
     * @access  protected
     * @return  bool
     */
    protected static function createCalendar($name)
    {
        assert('is_string($name); // Wrong argument type argument 1. String expected');

        if (empty($name)) {
            return false;
        }
        global $YANA;
        /* @var $dir \Yana\Files\Dir */
        $dir = $YANA->getPlugins()->{'calendar:/xcal'};

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
        $fileResult = $file->write();

        // insert a new database entry with the currentUser informations about the calendar file
        if ($fileResult) {
            $result = self::insertCalendar($name, $fileName);
        } else {
            $result = false;
        }
        if ($result) {
            $db = self::_getDatabase();
            $db->commit();
        }

        return $result;
    }

    /**
     * get current calendar path
     *
     * This function set a path too the calendar which is expected
     *
     * @access  protected
     * @static
     * @param   string  $id  file identifier
     * @return  string
     * @ignore
     */
    protected static function fileIdtoPath($id)
    {
        if (empty($id)) {
            return null;
        }

        $dir = $GLOBALS['YANA']->getPlugins()->{'calendar:/xcal'};
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
        global $YANA;
        $event = $ARGS['event'];
        $eventData = self::prepareEventData($event);
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
        $eventData['created_by'] = YanaUser::getUserName();

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
            $calendar = self::_getCalendar();
            $calendar->insertOrUpdateEvent($eventData);
            return $calendar->getMergedEvents();
        }

        $result = false;
        if (!empty($eventID) && $insertForDefaultUser == 'true' && !empty($eventData)) {
            $calendar = self::_getCalendar();
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

                $calendar = self::_getCalendar($id);
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
        $defaultCalendar = self::_getCalendar();
        return $defaultCalendar->getMergedEvents();
    }

    /**
     * prepare_event_data
     *
     * Prepare the serialized array dataset
     *
     * @access      protected
     * @param       array   $event  event arguments
     * @return      array
     * @ignore
     */
    protected static function prepareEventData(array $event)
    {
        $data = array();
        foreach($event as $items)
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
        $calendar = self::_getCalendar();
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
        $calendar = self::_getCalendar();
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
        $calendar = self::_getCalendar();
        $xmlContent = $calendar->send($ARGS);
        $result = self::setICal(null, $xmlContent);
        if (empty($result)) {
            return false;
        } else {
            return self::downloadFile($result);
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
        $calendar = self::_getCalendar();
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
        $eventData = self::prepareEventData($event);
        if (empty($eventData)) {
            return '';
        }
        $eventID = $eventData['eventid'];
        $date = $eventData['start'];
        $calendar = self::_getCalendar();
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
        $db = self::_getDatabase();
        $row = $db->select("calendar.$current_calendar", array('user_created', '=', YanaUser::getUserName()));
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
     * @access      protected
     * @static
     * @return      array  current user calendar list
     */
    protected static function getCalendarList()
    {
        $where = array('user_created', '=', YanaUser::getUserName());
        $db = self::_getDatabase();
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
     * @access  public
     * @static
     * @param   string  $name       name of the current calendar
     * @param   string  $filename   filename of the current calendar
     * @param   string  $url        url of the calendar file (when calendar is subscribe)
     * @param   bool    $subscribe  true when a calendar is subscribe otherweise false
     *
     * @return  bool
     */
    protected static function insertCalendar($name, $filename, $url = "", $subscribe = false)
    {
        $user = YanaUser::getUserName();
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
        $db = self::_getDatabase();

        if (isset($calendarData)) {
            if (!$db->insert("calendar", $calendarData)) {
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
        global $YANA;
        $db = self::_getDatabase();
        $data = $db->select("calendar.$key", array('user_created', '=', YanaUser::getUserName()));
        if (empty($data) || !isset($data['CALENDAR_URL']) || !isset($data['CALENDAR_FILENAME'])) {
            return false; // has no URL - nothing to refresh
        }
        $xml = self::iCalToXCal($data['CALENDAR_URL']); // convert ical into xcal
        if (!$xml) {
            return false;
        }
        $path = self::fileIdtoPath($data['CALENDAR_FILENAME']);
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
        global $YANA;
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
        $xml = self::iCalToXCal($path);
        if ($xml == false) {
            return false;
        }
        $content = $xml;
        $writeXML = self::writeXml($content, $filename);
        if ($writeXML) {
            $result = self::insertCalendar($name, $filename, $url, true);
            $db = self::_getDatabase();
            try {
                $db->commit();
            } catch (\Exception $e) {
                $result = false;
            }
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * Export changes to XML file.
     *
     * @access  protected
     * @static
     * @param   string  $content   file contents
     * @param   string  $fileName  file name
     * @return  bool
     */
    protected static function writeXml($content, $fileName)
    {
        assert('is_string($content); // Wrong argument type argument 1. String expected');
        assert('is_string($filename); // Wrong argument type argument 2. String expected');

        /* @var $YANA Yana */
        global $YANA;
        /* @var $dir Dir */
        $dir = $YANA->getPlugins()->{'calendar:/xcal'};
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
        self::removeCalendarFile($calendarID);
        $where = array('user_created', '=', YanaUser::getUserName());
        $db = self::_getDatabase();
        /* remove the row */
        if (!$db->remove("calendar.${calendarID}", $where)) {
            /* error - unable to perform update - possibly readonly */
            return false;
        }

        /* commit changes */
        $db->commit(); // may throw exception
        return true;
    }

    /**
     * Remove calendar file.
     *
     * @type        write
     * @template    MESSAGE
     * @user        group: admin, level: 100
     * @user        group: calendar
     * @onsuccess   goto: GET_CALENDAR_INPUT
     * @onerror     goto: GET_CALENDAR_INPUT
     *
     * @access      protected
     * @static
     * @param       integer  $datasetID  id of the current calendar too remove
     * @return      bool
     */
    protected static function removeCalendarFile($datasetID)
    {
        $isSuccess = false;
        $db = self::_getDatabase();
        $calendar = $db->select("calendar.{$datasetID}");

        if (empty($calendar)) {

            $fileName = $calendar['CALENDAR_FILENAME'];
            if (!empty($fileName)) {
                $isSuccess = (bool) self::removeXCalFile($fileName);
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
     * @access  protected
     * @param   string  $path  path to ICal file
     * @return  xml|bool    if succesfull than return an xml object otherweise false
     * @ignore
     */
    protected static function iCalToXCal($path)
    {
        assert('is_string($path); // Wrong argument type argument 1. String expected');

        $icalendarData = @file_get_contents($path);
        if ($icalendarData == false) {
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

        $dtd = Calendar::getDtd();
        if (empty($dtd)) {
            $dtdString = '<!DOCTYPE iCalendar SYSTEM "../../../config/dtd/xcal.dtd">' . "\n";
        } else {
            $dtdString = $dtd;
        }

        $xml = '<?xml version="1.0"?>' . "\n";
        $xml .= $dtdString;
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
            if (!preg_match("/^[^X^x]+[-\w]+[-\w]+/", $line)) {
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
        assert('is_string($calendar_name); // Wrong argument type argument 1. String expected');

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
        $xml = self::iCalToXCal($filePath);
        if ($xml == false) {
            return false;
        }
        // set calendar name and the file name of the calendar
        $name = $calendarName;
        $fileName = md5($name.$fileName);

        // create the xml calendar file
        $xmlWrite = self::writeXml($xml, $fileName);

        // insert a new database entry when xml file is created
        if (!$xmlWrite || empty($name) || empty($fileName)) {
            return false;
        }
        // inserts entries into database, but does not commit them
        if (!self::insertCalendar($name, $fileName)) {
            return false;
        }
        // commit the new entry into database
        $db = self::_getDatabase();
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
     * @access  protected
     * @static
     * @param   integer  $datasetID    id of the current dataset
     * @param   string   $xmlContent   xml contetn of an event
     * @return  bool
     */
    protected static function setICal($datasetID = null, $xmlContent = '')
    {
        /* @var $YANA Yana */
        global $YANA;
        $db = self::_getDatabase();
        if ($datasetID != null && is_int($datasetID)) {
            $where = array('user_created', '=', YanaUser::getUserName());
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
                $dir = $YANA->getPlugins()->{'calendar:/xcal'};
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
                                $string .= strtoupper($name).':';
                                $categoriesBegin = 1;
                            } else {
                                $string .= 'BEGIN:'.strtoupper($name)."\n";
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
                                    $categories .=$categoriesSeperator.$childValue;
                                    $categoriesBegin = 0;
                                } else {
                                    $string .= strtoupper($childName).':'.$childValue."\n";
                                }

                            }
                            if (isset($categories)) {
                                $string .= $categories."\n";
                            }
                            if (strtoupper($name) != 'CATEGORIES') {
                                $string .= 'END:'.strtoupper($name)."\n";
                            }
                        }
                    }
                    if ($value->attributes()) {
                        foreach ($value->attributes() as $key => $item)
                        {
                            if ($name == 'attendee') {
                                if ($key == 'cn') {
                                    $string .= strtoupper($name).';'.$key.'='.$item.';';
                                }
                                if ($key == 'rsvp') {
                                    $string .= $key.'='.$item.':';
                                }
                            }
                        }
                        if ($name == 'attendee') {
                            $string .= $value."\n";
                        } else {
                            if ($value->attributes()) {
                                $attr = $value->attributes();
                                foreach ($attr as $attrKey => $attrV)
                                {
                                    $additional = explode(':', $value);
                                    if (!isset($additional[1])) {
                                        $string .= strtoupper($name).';'.$attrKey.'='.$attrV.':'.$additional[0]."\n";
                                    } else {
                                        $string .= strtoupper($name).';'.$attrKey.'='.$attrV.':'.$additional[0].':'.
                                            $additional[1]."\n";
                                    }
                                }
                            }
                        }
                    }
                    if (!$value->attributes() && !$value->children()) {
                        $string .= strtoupper($name).':'.$value."\n";
                    }
                }
                    $string .= 'END:'.strtoupper($rootName)."\n";
            }
            $string .= 'END:'.strtoupper($root)."\n";
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
        $data = self::setICal($calendarID);

        if (empty($data)) {
            return false;
        }
        return self::downloadFile($data);
    }

    /**
     * Download ICAL file.
     *
     * @access      protected
     * @static
     * @param       array   $data   file information
     * @return      bool    return bool false if the expected param is Empty
     * @ignore
     */
    protected static function downloadFile($data)
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
     * @access      protected
     * @static
     * @param       string  $fileName  name of the removed file
     * @return      bool
     */
    protected static function removeXCalFile($fileName)
    {
        /* @var $YANA Yana */
        global $YANA;
        /* @var $dir Dir */
        $dir = $YANA->getPlugins()->{'calendar:/xcal'};

        $path = $dir->getPath() . $fileName.'.xml';
        $deleteUserFile = new \Yana\Files\Text($path);

        if ($deleteUserFile->exists()) {
            $deleteUserFile->delete();
        }

        return true;
    }

}

?>
