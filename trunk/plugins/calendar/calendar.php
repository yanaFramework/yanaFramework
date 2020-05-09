<?php
/**
 * Calendar
 *
 * This class create an calendar object of an calendar xml file (xcal).
 * It is used for send the calendar informations with the json function too the calendar for display.
 *
 * You can use the calendar like in this example:
 * <code>
 *      get the path of the calendar xcal file.
 *      $path = $this->getPath();
 *
 *      // get calendar instance, and execute the needed function to display events
 *      $calendar = new Calendar($path);
 *      $calendar->mergeEvents();
 *      $json = $calendar->asJson();
 *      print $json;
 * </code>
 *
 * Send the json string to the calendar function which is load the events.
 *
 * @author     Dariusz Josko
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

namespace Plugins\Calendar;

/**
 * Calendar handling class
 *
 * @package     yana
 * @subpackage  plugins
 */
class Calendar extends \Yana\Files\AbstractResource
{

    /**
     * @var string
     */
    private $name = "";

    /**
     * @var string
     */
    private $id = "";

    /**
     * @var \SimpleXMLElement
     */
    private $content = null;

    /**
     * @var array
     */
    private $vevents = null;

    /**
     * @var array
     */
    private $eventList = null;

    /**
     * @var string
     */
    private $owner = "";

    /**
     * @var bool
     */
    private $readonly = false;

    /**
     * @var string
     */
    private $className = '';

    /**
     * @var \SimpleXMLElement
     */
    protected $eventByID = null;

    /**
     * @var \Yana\Core\IsVarContainer
     */
    private $varContainer = null;

    /**
     * @var array
     */
    private static $categories = array();

    /**
     * @var array
     */
    private static $eventKeys = array(
        'uid' => 'id',
        'summary' => 'title',
        'location' => 'location',
        'created' => 'created',
        'dtstart' => 'start',
        'dtend' => 'end',
        'last-modified' => 'last-modified',
        'dtstamp' => 'dtstmap',
        'exdate' => 'exdate',
        'categories' => 'categories',
        'description' => 'description',
        'rrule' => 'rrule'
    );

    /**
     * @var array
     */
    protected static $additionalEventKeys = array();

    /**
     * Create a new instance of this class.
     *
     * @param  string                     $filename   to calendar ical file
     * @param  \Yana\Core\IsVarContainer  $container  holds calendar settings
     * @param  int                        $id         unique identifier
     */
    public function __construct($filename, \Yana\Core\IsVarContainer $container, $id = 0)
    {
        assert(is_int($id), 'Wrong argument type argument 2. Integer expected');

        parent::__construct($filename);
        $this->varContainer = $container;
        $this->id = (int) $id;
    }

    /**
     * Get container with calendar settings.
     *
     * @return  \Yana\Core\IsVarContainer
     */
    protected function _getVarContainer()
    {
        return $this->varContainer;
    }

    /**
     * get unique identifier
     *
     * Returns an unique identifier for this calendar, that can be used in a database.
     * Returns an empty string if none has been defined.
     *
     * @return  string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * set color
     *
     * This function set the calendar css class.
     * If none is set, the default category colors will be used.
     *
     * @param   string  $className  event calendar css class
     */
    public function setColor($className)
    {
        assert(is_string($className), 'Wrong argument type argument 1. String expected');
        $this->className = $className;
    }

    /**
     * get color
     *
     * This function returns the calendar's css class.
     *
     * @return  string
     */
    public function getColor()
    {
        return $this->className;
    }

    /**
     * setDisableEvents
     *
     * This function set the events as disabled.
     * Use this function only if u want the events as readonly
     *
     * @param   bool   $readonly  set to true if the events should be only for read
     * @return  bool
     */
    public function setDisableEvents($readonly = false)
    {
        assert(is_bool($readonly), 'Wrong argument type argument 1. Boolean expected');
        $this->readonly = (bool) $readonly;
    }

    /**
     * setDisableEvents
     *
     * This function sets the events as disabled.
     * Use this function only if u want the events as readonly
     *
     * @return  bool
     */
    public function getDisableEvents()
    {
        return $this->readonly;
    }

    /**
     * set additional event keys
     *
     * This function merges the list of scanned elements in the ICal-file with
     * additional elements provided in the list $additionalEventKeys.
     *
     * @param   array  $additionalEventKeys  list of additional ICal elements
     */
    public static function setAdditionalEventKeys(array $additionalEventKeys)
    {
        self::$eventKeys = array_merge(self::$eventKeys, $additionalEventKeys);
        self::$additionalEventKeys = $additionalEventKeys['extends']['event'];
    }

    /**
     * setCategories
     *
     * This function set the categories list, this is needed for calculate the events color
     *
     * @param   array  $categories  categories list
     */
    public static function setCategories(array $categories)
    {
        self::$categories = array();
        if (!empty($categories)) {
            foreach ($categories as $categoryId => $category)
            {
                self::$categories[(string) $categoryId] = array(
                    'name' => (string) $category['name'],
                    'color' => (string) $category['color']
                );
            };
        }
    }

    /**
     * getCategories
     *
     * This function get the categories list
     *
     * @return  array
     */
    public static function getCategories()
    {
        return self::$categories;
    }

    /**
     * set owner
     *
     * This function sets another calendar user.
     * Use this function if you want a default calendar from another person.
     * You only need to know is the other User current Name.
     * Execute this function after you get the calendar instance.
     *
     * @param   string  $user  user name
     */
    public function setOwner($user)
    {
        assert(is_string($user), 'Wrong argument type argument 1. String expected');
        $this->owner = $user;
    }

    /**
     * Get owner.
     *
     * This function get the name of the calendar user, if there is any.
     *
     * @return  string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * setName
     *
     * This function sets the name of the current calendar
     *
     * @param   string  $name  calendar name
     * @return  string
     */
    public function setName($name)
    {
        assert(is_string($name), 'Wrong argument type argument 1. String expected');
        $this->name = "$name";
    }

    /**
     * getName
     *
     * This function get the name of the current calendar
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * setEvent
     *
     * This function set all events of the current calendar.
     * The expected param is an array with the events informations.
     * This steep is setting the recurencerule object and calculate the additional keys when they set.
     *
     * @param   \SimpleXMLElement  $xml  event dataset
     */
    private function _addEventFromXML(\SimpleXMLElement $xml)
    {
        $event = array();
        $exdate = array();
        foreach (self::$eventKeys as $key => $replaceKey)
        {
            if (isset($xml->exdate)) {
                foreach ($xml->exdate as $exitsExdate)
                {
                    if (!array_key_exists((string) $xml->uid, $exdate)) {
                        $exdate = array();
                    }
                    $exdate[(string) $xml->uid] [(string)$exitsExdate] = 'exdate';
                }
            }
            if ($key == 'rrule' && !empty($xml->{$key})) {
                $event['rrule'] = new \Plugins\Calendar\Recurrance(
                    $xml->uid,
                    $xml->dtstart,
                    $xml->dtend,
                    $xml->rrule,
                    $exdate
                );
            } elseif ($key == 'extends') {
                $check = $xml->x;
                $additionalKeys = array_flip($replaceKey['event']);
                if (!empty($check)) {
                    $name = "";
                    foreach ($xml->x as $exKey => $attr)
                    {
                        $name = (string) $attr->attributes()->name;
                        $value = (string) $attr;
                        if (array_key_exists($name, $additionalKeys)) {
                            if (!empty($value)) {
                                $event[$name] = $value;
                                unset($additionalKeys[$name]);
                            }
                        }
                    }
                    if (!empty($additionalKeys)) {
                        foreach ($additionalKeys as $nodeName => $id)
                        {
                            $event[$nodeName] = "";
                            unset($additionalKeys[$name]);
                        }
                    }
                } else {
                    foreach ($additionalKeys as $nodeName => $id)
                    {
                        $event[$nodeName] = "";
                        unset($additionalKeys[$nodeName]);
                    }
                }
            } else {
                if ($key == 'categories') {
                    if ($xml->{$key}->item) {
                        // when the node exist take the last entry for display
                        $count = count($xml->{$key}->item) - 1;
                        $value = (string) $xml->{$key}->item[$count];
                    } else {
                        $value = (string) $xml->{$key};
                    }
                } else {
                    $value = (string) $xml->{$key};
                }
                $event[$replaceKey] = $value;
            }
            $exdate = array();
        } // end foreach
        if ($xml->uid && !isset($this->vevents[(string) $xml->uid])) {
            $this->vevents[(string) $xml->uid] = $event;
        } else {
            $this->vevents[] = $event;
        }
    }

    /**
     * getEvents
     *
     * This function get all Events
     *
     * @return  array
     */
    public function getEvents()
    {
        if (!isset($this->vevents)) {
            $this->vevents = array();
            $xml = $this->getContent();
            if ($xml instanceof \SimpleXMLElement) {
                foreach ($xml->xpath('//vevent') as $vEvent)
                {
                    $this->_addEventFromXML($vEvent);
                }
            }
        }
        return $this->vevents;
    }

    /**
     * getMergedEvents
     *
     * This function get all merged Events
     *
     * @return  array
     */
    public function getMergedEvents()
    {
        if (!isset($this->eventList)) {
            $this->eventList = array();
            foreach ($this->getEvents() as $event)
            {
                $this->_addToEventList($event);
            }
        }
        return $this->eventList;
    }

    /**
     * getCalendarContent
     *
     * This function get the content of the current calendar.
     *
     * @return  \SimpleXmlElement
     */
    protected function getContent()
    {
        if (!isset($this->content)) {
            $path = $this->getPath();
            $this->content = simplexml_load_file($path);
        }
        return $this->content;
    }

    /**
     * get vendor settings
     *
     * This function retrieves and returns all vendor specific, non-standard calendar extension settings.
     * (In ICal these are marked by a "X" character, followed by a vendor specific name.)
     * If there are none, the returned array will be empty. Otherwise the list will contain \SimpleXMLElements,
     * each with an attribute "name" and a value.
     *
     * @return  array
     */
    public function getVendorSettings()
    {
        return $this->getContent()->vcalendar->x;
    }

    /**
     * set vendor settings
     *
     * This function adds vendor specific settings to the calendar.
     * (In ICal these are marked by a "X" character, followed by a vendor specific name.)
     * You may provide a scalar or an array of scalars as value.
     *
     * @param   string  $name    name of element
     * @param   array   $values  element values
     */
    public function addVendorSettings($name, $values)
    {
        $values = (array) $values;
        $calendar = $this->getContent()->vcalendar;

        foreach ($values as $value)
        {
            $node = $calendar->addChild('x', $value);
            $node->addAttribute('name', 'agenda');
        }
    }

    /**
     * drop vendor setting
     *
     * This function looks up and deletes any settings with the given name (and value, if provided).
     *
     * @param   string  $name   name of element
     * @param   string  $value  element value
     */
    public function dropVendorSetting($name, $value = null)
    {
        $calendar = $this->getContent();
        $xpath = "//x[@name = '$name' and (. = '$value' or '' = '$value')]";
        foreach ($calendar->xpath($xpath) as $node)
        {
            $node = null;
        }
    }

    /**
     * merge event
     *
     * This function parses and adds an event to the eventList.
     *
     * @access  private
     * @param   array  $event  event declaration
     */
    private function _addToEventList(array $event)
    {
        $result = array();
        $e['id'] = $event['id'];
        $e['readonly'] = $this->getDisableEvents();
        $e['title'] = $event['title'];
        $e['location'] = $event['location'];
        $eventCategory = $event['categories'];
        $e['allDay'] = false;
        // BEGIN : set class name for event by selected category
        if (!empty($eventCategory)) {
            $cat = self::getCategories();
            if (is_array($cat) && !empty($cat)) {
                foreach ($cat as $categoryId => $category)
                {
                    if ($categoryId == $eventCategory) {
                        $className = $category['color'];
                    }
                }
            }
        }
        // @todo make this in a seperated part
        $calendarClassName = $this->getColor();
        if (empty($calendarClassName)) {
            if (isset($className)) {
                $e['className'] = $className;
            } else {
                $e['className'] = 'event_default_color';
            }
        } else {
            $e['className'] = $calendarClassName;
        }

        $e['categories'] = $eventCategory;
        unset ($eventCategory, $className, $calendarClassName);

        // END : set class name for event by selected category
        $e['description'] = $event['description'];
        /* -------------------------------------- */
        // additional event options
        $additionalEventKeys = self::$additionalEventKeys;
        if (!empty($additionalEventKeys)) {
            foreach ($additionalEventKeys as $value)
            {
                if (isset($event[$value])) {
                    if (!empty($event[$value])) {
                        $e[$value] = $event[$value];
                    } else {
                        $e[$value] = '';
                    }
                }
            }
        }
        /* -------------------------------------- */

        if (is_object($event['rrule'])) {
            $e['frequency'] = $event['rrule']->getFreq();
            $e['interval'] = $event['rrule']->getInterval();
            $endlesserial = false;
            if (!$event['rrule']->isUntil()) {
                $endlesserial = true;
            }
            if (!$event['rrule']->isCount()) {
                $endlesserial = true;
            }
            $e['endlessSerial'] = $endlesserial;
            unset($endlesserial);
            $e['count'] = $event['rrule']->getCount();
            //$e['until'] = $event['rrule']->getUntil();
            $until = $event['rrule']->getUntilAsArray();
            if (!empty($until)) {
                $e['until'] = $until;
            } else {
                $e['until'] = null;
            }
            $e['workDays'] = $event['rrule']->isDay();
            $e['days'] = $event['rrule']->getDay();
            if (!empty($e['days']) && isset($e['days'][0])) {
                $weekNr = preg_split("/[a-zA-Z]+/", $e['days'][0]);
                if (!empty($weekNr[0])) {
                    $nr = $weekNr[0];
                }
                if (isset($nr)) {
                    $repeatMonthOptions = $this->_getVarContainer()->getVar('repeat_month_options');
                    foreach ($repeatMonthOptions['option'] as $key => $data)
                    {
                        if ($data['default'] == $nr) {
                            $repeatPosition = $key;
                        }
                    }
                    unset ($key, $data);
                }
            }
            if (isset($repeatPosition)) {
                $e['repeat_position'] = $repeatPosition;
                unset ($repeatPosition);
            }

            $e['month'] = $event['rrule']->getMonth();
            // for monthly days options
            $numbers = array();
            for ($i = 1; $i <= 31; $i++)
            {
                $numbers[$i] = false;
            }
            $monthDays = $event['rrule']->getMonthDay();
            if (!empty($monthDays)) {
                foreach ($monthDays as $day)
                {
                    $day = (int) $day;
                    if (array_key_exists($day, $numbers)) {
                        $numbers[$day] = true;
                    }
                }
            }
            $e['monthdays'] = $numbers;
            // end
            $expectedDay =  $event['rrule']->getDay();
            $freq = $event['rrule']->getFreq();
            if (!empty($expectedDay) && ($freq == 'MONTHLY' || $freq == 'YEARLY')) {
                $value = preg_split("/[\d{1,2}]+/", $expectedDay[0]);
                if (isset($value[0]) && isset($value[1])) {
                    $weekDays = $event['rrule']->getWeekDays();
                    $weekDays = array_flip($weekDays);
                    $e['monthEachWeekDay'] = $weekDays[$value[1]];
                }
            }

            $dayList = $event['rrule']->getWeekDays();
            foreach($dayList as $key => $item)
            {
                if (in_array($item, $event['rrule']->getDay())) {
                    $e[$item] = $key;
                } else {
                    $e[$item] = null;
                }
            }
        } else {
            $e['frequency'] = '';
            $e['interval'] = '';
            $e['count'] = '';
            $e['workDays'] = '';
            $e['days'] = '';
            $e['month'] = '';
            $e['monthdays'] = array();
            $e['monthEachDay'] = '';
            $e['monthEachWeekDay'] = '';
            $e['SU'] = null;
            $e['MO'] = null;
            $e['TU'] = null;
            $e['WE'] = null;
            $e['TH'] = null;
            $e['FR'] = null;
            $e['SA'] = null;
        }

        /* -------------------------------------- */
        if (!empty($event['rrule'])) {
            $rrule = $event['rrule'];
            $ruleResult = $rrule->getResultList($event['id']);
            if (isset($ruleResult[$event['id']])) {
                $count = count($ruleResult[$event['id']]);
                if($rrule->isAllDay()) {
                    $allDay = true;
                    $subTime = 86400;
                } else {
                    $allDay = false;
                    $subTime = 0;
                }
                foreach ($ruleResult[$event['id']] as $data)
                {
                    $e['start'] = $data['start'];
                    $e['end'] = $data['end'] - $subTime;
                    $e['allDay'] = $allDay;
                    $this->eventList[] = $e;
                }
            }
        } else {
            if (preg_match('/(\d{4})(\d{2})(\d{2})T(\d{2})(\d{2})(\d{2})(\w*)/', $event['start'], $sTime)) {
                $date = mktime($sTime[4], $sTime[5], $sTime[6], $sTime[2], $sTime[3], $sTime[1]);
                $e['start'] = $date;
            } else {
                $start = '';
                if (preg_match('/(\d{4})(\d{2})(\d{2})/', $event['start'], $sTime)) {
                    // for date only
                    $date = mktime(0, 0, 0, $sTime[2], $sTime[3], $sTime[1]);
                    $e['start'] = $date;
                    $start = $sTime;
                }
            }
            if (preg_match('/(\d{4})(\d{2})(\d{2})T(\d{2})(\d{2})(\d{2})(\w*)/', $event['end'], $eTime)) {
                $date = mktime($eTime[4], $eTime[5], $eTime[6], $eTime[2], $eTime[3], $eTime[1]);
                $e['end'] = $date;
            } else {
                if (preg_match('/(\d{4})(\d{2})(\d{2})/', $event['end'], $sTime)) {
                    // for date only
                    $day = $sTime[3];
                    $month = $sTime[2];
                    // Note: end date is non-inclusive as of RFC 5545: thus $day - 1
                    $date = mktime(23, 59, 0, $month, $day - 1, $sTime[1]);
                    $e['end'] = $date ;
                    //if no time is givin set all day true
                    $e['allDay'] = true;
                }
            }
            $this->eventList[] = $e;
        }
    }

    /**
     * removeEventById
     *
     * This function is used for remove a event by Event ID.
     * The expected param is a string with the number or some digits which identify the event.
     * If more than one event has the same id than all be removed.
     *
     * @param   string  $eventID  event id
     * @return  bool    (true = event removed, false otherweise)
     */
    public function removeEventById($eventID)
    {
        assert(is_string($eventID), 'Wrong argument type argument 1. String expected');
        assert(!empty($eventID), 'Wrong argument type argument 1. can not be empty');

        $xml = $this->getContent();
        foreach ($xml->xpath('//vevent') as $key => $id)
        {
            $removeID = (string) $id->uid;
            if ($removeID === $eventID) {
                $dom = \dom_import_simplexml($id);
                $dom->parentNode->removeChild($dom);
            }
        }

        return $this->writeXml($xml->asXML());
    }

    /**
     * setEventById
     *
     * This function is used to remove an event by Event ID.
     * If more than one event has the same id than all will be removed.
     *
     * @param   string  $eventID  number that identifies the event
     */
    public function setEventById($eventID)
    {
        assert(is_string($eventID), 'Wrong argument type argument 1. String expected');
        assert(!empty($eventID), 'Wrong argument type argument 1. can not be empty');
        // this is needed for get the new content
        $this->content = null;

        $xml = $this->getContent();
        $event = '';
        foreach ($xml->xpath('//vevent') as $key => $id)
        {
            $currentID = (string) $id->uid;
            if ($currentID === $eventID) {
                $event = $id;
            }
        }
        if (empty($event)) {
            $content = '';
        } else {
            $content = $event;
        }
        $this->eventByID = $content;
    }

    /**
     * getEventById
     *
     * This function is used for remove a event by Event ID.
     * The expected param is a string with the number or some digits which identify the event.
     * If more than one event has the same id than all be removed.
     *
     * @return  string
     */
    public function getEventById()
    {
        return $this->eventByID;
    }

    /**
     * insertEvent
     *
     * This function insert a event into calendar.
     * This function is called when the current user update an event for more users.
     *
     * @param   \SimpleXMLElement  $content  xml content for insert
     * @return  bool
     */
    public function insertEvent(\SimpleXMLElement $content)
    {

        $calendar = $this->getContent();
        $currentNode = $calendar->vcalendar;
        $oneNode = $currentNode->addChild('vevent');
        foreach ($content as $data => $item)
        {
            if ($data == 'categories') {
                $categories = $oneNode->addChild($data);
                $categories->addChild('item', (string)$item->item);
            } else {
                $root = $oneNode->addChild((string)$data, (string)$item);
            }
            if ($item->attributes()) {
                foreach($item->attributes() as $name => $attr)
                {
                    $root->addAttribute((string)$name, (string)$attr);
                }
            }
        }
        return $this->writeXml($calendar->asXML());
    }


    /**
     * setExdate
     *
     * This funcion is an special way too set an event date as exdate.
     * That means that the event with this date will be igmored by fill the calendar container.
     * This step expected the event ID and the start date of this event.
     * The changes will be written into the current calendar file.
     *
     * @param       string   $eventID     event id
     * @param       string   $date        date
     * @return      bool     (true = exdate is set, false otherweise)
     */
    public function setExdate($eventID, $date)
    {
        assert(is_string($eventID), 'Wrong argument type argument 1. String expected');
        assert(is_array($date), 'Wrong argument type argument 1. Array expected');

        $xml = $this->getContent();
        $setDate = $date;
        if (strlen($setDate['month']) == 1) {
            $setDate['month'] = '0' . $setDate['month'];
        }
        if (strlen($setDate['day']) == 1) {
            $setDate['day'] = '0' . $setDate['day'];
        }
        if (isset($setDate['hour']) && strlen($setDate['hour']) == 1) {
            $setDate['hour'] = '0' . $setDate['hour'];
        }
        if (!isset($setDate['hour'])) {
            $setDate['hour'] = '00';
        }
        if (isset($setDate['minute']) && strlen($setDate['minute']) == 1) {
            $setDate['minute'] = '0' . $setDate['minute'];
        }
        if (!isset($setDate['minute'])) {
            $setDate['minute'] = '00';
        }

        $date = $setDate['year'] . $setDate['month'] . $setDate['day'] . 'T' .
            $setDate['hour'] . $setDate['minute'] . '00Z';

        foreach ($xml->xpath('//vevent') as $dataset)
        {
            $currentID = (string) $dataset->uid;
            if ($currentID === $eventID) {
                $dataset->addChild('exdate', $date);
            }
        }
        return $this->writeXml($xml->asXML());
    }

    /**
     * calculateDataEntry
     *
     * This function expected an array with events which are send from the js calendar.
     * It prepair the dataset and return an array with the expected current event informations.
     *
     * @param   array  $event  expected an array with event options
     * @return  array  get modefied event dataset too save
     */
    protected function calculateDataEntry($event)
    {
        // prapair the event for save or update
        $dataset = array();
        foreach ($event as $key => $value)
        {
            switch ($key)
            {
                case 'start':
                case 'end':
                case 'until_date':
                    if ($key == 'start') {
                        $dataset['dtstart'] = $value;
                    } elseif ($key == 'end') {
                        $dataset['dtend'] = $value;
                    }
                break;
                case 'location':
                    $dataset['location'] = $value;
                break;
                case 'description':
                    $dataset['description'] = $value;
                break;
                case 'title':
                    $dataset['summary'] = $value;
                break;
                case 'category':
                    $dataset['categories'] = $value;
                break;
                case 'freq':

                    if (isset($event['repeatopt']) && $event['repeatopt'] == 'counter') {
                        $repeat = 'COUNT='.$event['count_nr'].';';
                    } elseif (isset($event['repeatopt']) && $event['repeatopt'] == 'until') {
                        $until = $event['until_date'];
                        if (is_array($until)) {
                            $until = self::_arrayToTime($until);
                        }
                        if (is_int($until)) {
                            $until = date('Ymd\THis\Z', (int) $until);
                        }
                        $repeat = 'UNTIL='.$until.';';
                    }
                    $days = $this->_getVarContainer()->getVar('days');
                    $days = $days['day'];
                    $repeatMonthOptions = $this->_getVarContainer()->getVar('repeat_month_options');
                    $repeatMonthOptions = $repeatMonthOptions['option'];

                    if ($value == 'DAILY') {
                        $rrule = 'FREQ='.$value.';';
                        if (isset($repeat)) {
                            $rrule .= $repeat;
                        }

                        if (isset($event['dayoption']) && $event['dayoption'] == 'byDay') {
                            $dayOptions = 'INTERVAL='.$event['alldayinterval'];
                        } elseif (isset($event['dayoption']) && $event['dayoption'] == 'byWeekDays') {
                            $dayOptions = 'BYDAY=MO,TU,WE,TH,FR';
                        }
                        $rrule .= $dayOptions;

                    } elseif ($value == 'MONTHLY') {
                        $rrule = 'FREQ='.$value.';';
                        if (isset($repeat)) {
                            $rrule .= $repeat;
                        }

                        $monthOption = '';
                        if (isset($event['monthly_options']) && $event['monthly_options'] == 'bymonthday') {
                            $monthOption .= 'BYMONTHDAY=';
                            foreach ($event['day'] as $key => $monthDay)
                            {
                                $monthOption .= $monthDay.',';
                            }
                        } elseif (isset($event['monthly_options']) && $event['monthly_options'] == 'monthByDay') {
                            $monthOption .= 'BYDAY=';
                            $dayName = (int) $event['monthdayinterval'];
                            $monthRepeatInterval = (int) $event['monthrepeatinterval'];

                            $dayRepeatInterval = $repeatMonthOptions[$monthRepeatInterval]['default'];
                            $monthOption .= $dayRepeatInterval;

                            $byDay = $days[$dayName]['default'];
                            $monthOption .= $byDay;
                        }

                        $rrule .= $monthOption;

                    } elseif ($value == 'WEEKLY') {
                        $rrule = 'FREQ='.$value.';';
                        if (isset($repeat)) {
                            $rrule .= $repeat;
                        }
                        $byDay = 'BYDAY=';
                        if (isset($event['week_days'])) {
                            foreach ($event['week_days'] as $key => $day)
                            {
                                $byDay .= $days[(int)$day]['default'].',';
                            }
                        }
                        $rrule .= $byDay;
                    } elseif ($value == 'YEARLY') {
                        $rrule = 'FREQ='.$value.';';
                        if (isset($repeat)) {
                            $rrule .= $repeat;
                        }
                        $year = '';
                        if (isset($event['y_opt']) && $event['y_opt'] == 'yearMonthDay') {

                            $monthday = $event['numbers'];
                            $year .= 'BYMONTHDAY=' . $monthday . ';';

                            $month = $event['month'];
                            $year .= 'BYMONTH=' . $month;

                        } elseif (isset($event['y_opt']) && $event['y_opt'] == 'yearMonthDayInterval') {
                            // which week
                            $weekRepeat = $event['year_weekinterval'];
                            $repeatNumber = $repeatMonthOptions[$weekRepeat]['default'];
                            $year .= 'BYDAY=' . $repeatNumber;

                            // day
                            $yearDay = $event['year_day'];
                            $byDay = $days[$yearDay]['default'];
                            $year .= $byDay . ';';
                            // month
                            $yearMonth = $event['year_month'];
                            $year .= 'BYMONTH=' . $yearMonth;
                        }
                        $rrule .= $year;
                    } else {
                        // left blank
                    }
                    if (isset($rrule)) {
                        $dataset['rrule'] = $rrule;
                    } else {
                        $dataset['rrule'] = '';
                    }
                break;
                default:

                // this step is for additional event informations if this are defined
                    $additionalEventKeys = self::$additionalEventKeys;
                    if (!empty($additionalEventKeys)) {
                        if (in_array($key, $additionalEventKeys)) {
                            $dataset['extends'][$key] = $value;
                        }
                    }
                break;
            } // end switch
        } // end foreach

        // check if rrule exists
        if (!isset($rrule)) {
            $dataset['rrule'] = '';
        }

        return $dataset;
    }

    /**
     * send
     *
     * This function expected the current event informations.
     * This steep prepair the calendar entries and get only the structure with the current event informations and
     * return this as an xml string for download.
     *
     * @param   array  $eventData  expected an array with event options
     * @return  string
     */
    public function send(array $eventData)
    {
        // load the current calendar file
        $xml = $this->getContent();

        $eventID = '';
        if (!empty($eventData['eventid'])) {
            $eventID = (string) $eventData['eventid'];
        }

        // select the current event and unset the others
        foreach ($xml->xpath("//vevent[uid != '$eventID']") as $vEvent)
        {
            $vEvent = null;
        }
        unset($eventID, $vEvent);

        if (!empty($eventID)) {
            $eventData = $this->calculateDataEntry($eventData);
            self::_addEventToXML($xml, $eventData);
        }

        return $xml->asXML();
    }

    /**
     * insertOrUpdateEvent
     *
     * This functions handle the update and insert actions.
     * If an event is given which contains an event ID than the update action will be call otherwise insert will be
     * executed. The changes will be written into the current calendar file.
     *
     * @param   array  $event  list of events
     * @return  bool
     */
    public function insertOrUpdateEvent(array $event)
    {
        if (isset($event['eventid'])) {
            $id = $event['eventid'];
        } else {
            $id = '';
        }
        // prepare dataset
        $resultArray = $this->calculateDataEntry($event);
        // update or insert event
        if (empty($id)) {
            $result = $this->insertNewEvent($resultArray);
        } else {
            $result = $this->updateEvent($resultArray, $id);
            $this->setEventById($id);
        }
        return $this->writeXml($result);

    }

    /**
     * insertNewEvent
     *
     * This function get the current calendar and add the new event into.
     * After the modification an xml string will be returned.
     *
     * @param   array  $eventData  event information
     * @return  string
     */
    protected function insertNewEvent(array $eventData)
    {
        $xml = $this->getContent();
        self::_addEventToXML($xml, $eventData);
        return $xml->asXML();
    }

    /**
     * updateEvent
     *
     * This function gets the current calendar and updates the event with the expected changes.
     * After the modification an xml string will be returned.
     *
     * @param   array   $eventData  expected an array with event options
     * @param   string  $uid        unique id
     * @return  string
     */
    protected function updateEvent($eventData, $uid)
    {
        assert(is_array($eventData), 'Wrong argument type argument 1. Array expected');
        assert(!empty($uid), 'Wrong argument type argument 2. can not be empty');
        $xml = $this->getContent();
        $updated = array();
        $set = '';
        foreach ($xml->xpath("//vevent[uid = '$uid']") as $event)
        {
            foreach ($eventData as $node => $value)
            {
                if (in_array($node, array('dtstart', 'dtend'))) {
                    self::_addDateNode($event, $node, $value);

                } else {
                    if (isset($event->$node)) {
                        if (!is_array($value)) {
                            if ($node == 'categories') {
                                $cat = count($event->$node->item);
                                // array begin with 0
                                $nr = $cat - 1;
                                $event->$node->item[$nr] = $value;
                            } else {
                                $event->$node = $value;
                            }
                        }
                    } elseif ($node == 'categories') {
                        $categories = $event->addChild($node);
                        $categories->addChild('item', $value);
                    } else {
                        if (!is_array($value) && !empty($value)) {
                            $event->addChild($node, $value);
                        } elseif (is_array($value) && !empty($value)) {
                            // additional options
                            $additionalKeys = self::$additionalEventKeys;
                            if (!empty($additionalKeys)) {
                                $additionalEventKeys = array_flip($additionalKeys);
                                $existNode = array();
                                foreach($value as $key => $item)
                                {
                                    $exist = array_key_exists($key, $additionalEventKeys);
                                    if ($event->x) {
                                        foreach($event->x as $xEvent)
                                        {
                                            $existNode = (string)$xEvent->attributes()->name;
                                            if ($existNode == $key) {
                                                $dom = \dom_import_simplexml($xEvent);
                                                $dom->parentNode->removeChild($dom);
                                                $ext = $event->addChild('x', $item);
                                                $ext->addAttribute('name', $key);
                                                $updated[$uid][$key] = true;
                                            } else {
                                                if ($exist && !isset($updated[$uid][$key])) {
                                                    $ext = $event->addChild('x', $item);
                                                    $ext->addAttribute('name', $key);
                                                    $updated[$uid][$key] = true;
                                                }
                                            }
                                        }
                                    } elseif ($exist && !isset($updated[$uid][$key])) {
                                        $ext = $event->addChild('x', $item);
                                        $ext->addAttribute('name', $key);
                                        $updated[$uid][$key] = true;
                                    }
                                }
                            } // end if
                        } // end if
                    } // end if
                } // end if
            } // end foreach
        } // end foreach
        $result = $xml->asXML();
        return $result;
    }

    /**
     * updateEventByDrop
     *
     * This function is called when an event was dragged.
     * The dataset will be prepaired and an event update will be execute.
     * The changes will be written into the current calendar file.
     *
     * @param   string  $id      event id
     * @param   int     $resize  ammount of days to resize
     * @param   int     $min     ammount of minutes to resize
     * @return  bool
     */
    public function updateEventByDrop($id, $resize, $min = 0)
    {
        $e = array();
        $result = $this->getTimeByEventID($id);

        $e['dtstart'] = self::_stringToTime($result['start'], $resize, $min);
        $e['dtend'] = self::_stringToTime($result['end'], $resize, $min);

        $result = $this->updateEvent($e, $id);
        return $this->writeXml($result);
    }


    /**
     * update event
     *
     * This function is called when the an event was resize.
     * The dataset will be prepaired and an event update will be execute.
     * The changes will be written into the current calendar file.
     *
     * @param   string  $id      event id
     * @param   int     $resize  ammount of days to resize
     * @param   int     $min     ammount of minutes to resize
     * @return  bool
     */
    public function updateEventByResize($id, $resize = 0, $min = 0)
    {
        $e = array();

        $result = $this->getTimeByEventID($id);
        $date = array();
        if (!empty($result)) {
            $e['dtend'] = self::_stringToTime($result['end'], $resize, $min);
        }

        $result = $this->updateEvent($e, $id);
        return $this->writeXml($result);
    }

    /**
     * string to time
     *
     * This function takes a time string an converts it to a timestamp.
     *
     * @param   string  $string        string to parse
     * @param   int     $dayOffset     number of days to move date
     * @param   int     $minuteOffset  number of minutes to move date
     * @return  int
     */
    private static function _stringToTime($string, $dayOffset = 0, $minuteOffset = 0)
    {
        // format: yyyymmddThhmmssZ
        if (!preg_match('/^(\d{4})(\d{2})(\d{2})(?:T(\d{2})(\d{2})\d{2}\w*)?/', $string, $date)) {
            return array();
        }
        $year = (int) $date[1];
        $month = (int) $date[2];
        $day = (int) $date[3] + $dayOffset;
        if (!empty($date[4])) {
            $hour = (int) $date[4];
        } else {
            $hour = 0;
        }
        if (!empty($date[5])) {
            $minute = (int) $date[5] + $minuteOffset;
        } else {
            $minute = 0;
        }
        return mktime($hour, $minute, 0, $month, $day, $year);
    }

    /**
     * array to time
     *
     * This function takes an array with time settings an converts it to a timestamp.
     *
     * @param   array  $array         string to parse
     * @param   int    $dayOffset     number of days to move date
     * @param   int    $minuteOffset  number of minutes to move date
     * @return  int
     */
    private static function _arrayToTime($array, $dayOffset = 0, $minuteOffset = 0)
    {
        $year = (int) $array['year'];
        $month = (int) $array['month'];
        $day = (int) $array['day'] + $dayOffset;
        $hour = 0;
            $minute = 0;
        if (!empty($array['hour'])) {
            $hour = (int) $array['hour'];
        }
        if (!empty($array['minute'])) {
            $minute = (int) $array['minute'] + $minuteOffset;
        }
        return mktime($hour, $minute, 0, $month, $day, $year);
    }

    /**
     * add date node to event element
     *
     * @param   \SimpleXMLElement  $event  event node
     * @param   string             $name   name of date node
     * @param   mixed              $value  date value to parse
     */
    private static function _addDateNode(\SimpleXMLElement $event, $name, $value)
    {
        if (is_array($value)) {
            $value = self::_arrayToTime($value);
        }
        if (is_string($value)) {
            $value = self::_stringToTime($value);
        }

        /* check if event takes all day (true = yes, false = no)
         *
         * If "yes", we expect a value of type DATE (not TIMESTAMP).
         * This is: it has no hours, minutes, seconds and no timezone.
         */
        $date = getdate($value);
        assert(!isset($isAllDay), 'Cannot redeclare var $isAllDay');
        $isAllDay = empty($date['hours']) && empty($date['minutes']);

        if (is_int($value)) {
            if ($isAllDay) {
                // dtend is non-inclusive as of RFC 5545 standard, thus add 1 day
                if ($name == 'dtend') {
                    $value += 86400; // 86400 seconds = 1 day
                }
                $value = date('Ymd', $value);
            } else {
                $value = date('Ymd\THis\Z', $value);
            }
        }
        $dateNode = null;
        if (!isset($event->$name)) {
            $dateNode = $event->addChild($name, $value);
        } else {
            $event->$name = $value;
            $dateNode = $event->$name;
        }
        $dateNode['tzid'] = 'Europe/Berlin';

        // set type to DATE where necessary (TIMESTAMP is the default)
        if ($isAllDay) {
            $dateNode['value'] = 'DATE';
        } else {
            unset($dateNode['value']);
        }
    }

    /**
     * add event node to XML
     *
     * @param  \SimpleXmlElement  $xml   calendar node
     * @param   array             $data  event information
     */
    private static function _addEventToXML(\SimpleXmlElement $xml, array $data)
    {
        $eventNode = $xml->vcalendar->addChild('vevent');

        // add default nodes
        $date = date('Ymd\THis\Z');
        $eventNode->addChild('created', $date);
        $eventNode->addChild('last-modified', $date);
        $eventNode->addChild('dtstamp', $date);
        $eventNode->addChild('uid', time());

        foreach ($data as $name => $value)
        {
            if (in_array($name, array('dtstart', 'dtend'))) {
                self::_addDateNode($eventNode, $name, $value);
            } else {
                if (!is_array($value)) {
                    $eventNode->addChild($name, $value);
                } else {
                    // @todo this step must be testet / set the child name with a var
                    foreach($value as $key => $item)
                    {
                        $ext = $eventNode->addChild('x', $item);
                        $ext->addAttribute('name', $key);
                    }
                }
            }
        } // end foreach
    }

    /**
     * getTimeByEventID
     *
     * This function get the current start and end date of the event
     *
     * @param   string  $uid  event ID
     * @return  array
     */
    public function getTimeByEventID($uid)
    {
        // load the current calendar file
        $path = $this->getPath();
        $xmlFile = new \Yana\Files\Text($path);
        $xmlFile->read();
        $content = $xmlFile->getContent();
        $xml = \simplexml_load_string($content);
        $result = array();
        foreach ($xml->xpath('//vevent') as $event)
        {
            $currentID = (string) $event->uid;
            if ($currentID === $uid) {
                $result['start'] = (string) $event->dtstart;
                $result['end'] = (string) $event->dtend;
                if (!preg_match('/(\d{4})(\d{2})(\d{2})T(\d{2})(\d{2})(\d{2})(\w*)/', $result['start'])) {
                    $result['start'] .= 'T000000';
                }
                if (!preg_match('/(\d{4})(\d{2})(\d{2})T(\d{2})(\d{2})(\d{2})(\w*)/', $result['end'])) {
                    $result['end'] .= 'T000000';
                }
            }
        }
        return $result;
    }

    /**
     * getNewTimeForResize
     *
     * This function calculate the new date and time for an event that would be dragging or resize.
     * The result is an array with the current date informations (start, end)
     *
     * @param       string|int   $hour           hour
     * @param       string|int   $minutes        minutes
     * @param       string|int   $minToResize    minutes to resize
     * @return      array
     */
    protected function getNewTimeForResize($hour, $minutes, $minToResize)
    {
        $result = array();
        if ($minToResize > 0) {
            $min = $minToResize / 60;
            if ($min > 1) {
                $min = explode('.', $min);
                $hour = $hour + $min[0];
                if (isset($min[1])) {
                    $calcMin = 60 * ($min[1] / 10);
                    $minutes = $minutes + $calcMin;
                    if ($minutes > 59) {
                        $hour = $hour + 1;
                        $minutes = $minutes - 60;
                    } else {
                        $minutes = 60 - $minutes;
                    }
                } else {
                    $minutes = '00';
                }
            } else {
                $calcMin = 60 * $min;
                $minutes = $minutes + $calcMin;
                if ($minutes > 59) {
                    $hour = $hour + 1;
                    $minutes = $minutes - 60;
                }
            }
            $result['hour'] = ($hour);
            $result['minutes'] = ($minutes);
        } elseif ($minToResize < 0) {
            $min = abs($minToResize / 60);
            if ($min >= 1) {
                $min = explode('.', $min);
                $hour = $hour - $min[0];
                if (isset($min[1])) {
                    $calcMin = 60 * ($min[1] / 10);
                    $minutes = $minutes - $calcMin;
                    if ($minutes < 0) {
                        $hour = $hour - 1;
                        $minutes = abs($minutes);
                        $minutes = 60 - $minutes;
                    }
                } else {
                    $minutes = '00';
                }
            } else {
                $calcMin = 60 * $min;
                $minutes = $minutes - $min;
                if ($minutes > 0) {
                    $minutes = abs($minutes);
                    $hour = $hour - 1;
                    $minutes = 60 - $minutes;
                }
            }
            $result['hour'] = $hour;
            $result['minutes'] = $minutes;
        } else {
            $result = array();
        }

        return $result;
    }

    /**
     * writeXml
     *
     * This function write the changes into the expected xml file
     *
     * @param   string  $content  XML content
     * @return  bool
     */
    public function writeXml(?string $content = null): bool
    {
        $path = $this->getPath();
        if (is_null($content)) {
            return $this->getContent()->asXML($path) !== false;
        }

        $file = new \Yana\Files\Text($path);
        if (!$file->exists()) {
            return false;
        }
        $file->setContent("$content");
        try {
            $file->write();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * is empty
     *
     * Returns bool(true) if the calendar file has no events and bool(false) otherwise.
     *
     * @return  bool
     */
    public function isEmpty(): bool
    {
        try {
            $events = $this->getEvents();
            return empty($events);
        } catch (\Exception $e) {
            return true;
        }

    }
}
?>
