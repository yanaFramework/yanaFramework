<?php
/**
 * Recurrance Rule Rfc
 *
 * This class calculate the recurence rule of an event.
 * Set the event date and repat interval
 *
 * @author     Dariusz Josko
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

/**
 * Recurrance rules
 *
 * @package    yana
 * @subpackage plugins
 */
class RecurranceRule extends StdClass
{

    /* @var  string  */ protected  $id               = null;
    /* @var  string  */ protected  $freq             = null;
    /* @var  int     */ protected  $count            = 0;
    /* @var  int     */ protected  $interval         = 1;
    /* @var  string  */ protected  $weekStart        = 7;
    /* @var  string  */ protected  $rule             = null;
    /* @var  bool    */ protected  $allDay           = false;

    /* @var  array   */ protected  $weekDay          = array();
    /* @var  array   */ protected  $monthDay         = array();
    /* @var  int     */ protected  $yearDay          = 0;
    /* @var  int     */ protected  $year             = 0;
    /* @var  int     */ protected  $weekNumber       = 0;
    /* @var  int     */ protected  $month            = 0;

    /* @var  array   */ protected  $resultList       = array();
    /* @var  array   */ private    $rruleProperties  = array();

    /* @var  date    */ protected  $start;
    /* @var  date    */ protected  $end;
    /* @var  string  */ protected  $until;
    /* @var  array   */ protected  $exdate;
    /* @var  array   */ protected  $untilDate;
    /* @var  int     */ protected  $endlessSerial = 2;


    /* @var  bool    */ protected  $isUntil       = false;
    /* @var  bool    */ protected  $isFreq        = false;
    /* @var  bool    */ protected  $isCount       = false;
    /* @var  bool    */ protected  $isInterval    = false;
    /* @var  bool    */ protected  $isSecond      = false;
    /* @var  bool    */ protected  $isMinute      = false;
    /* @var  bool    */ protected  $isHour        = false;
    /* @var  bool    */ protected  $isDay         = false;
    /* @var  bool    */ protected  $isMonthDay    = false;
    /* @var  bool    */ protected  $isYearDay     = false;
    /* @var  bool    */ protected  $isWeekNo      = false;
    /* @var  bool    */ protected  $isMonth       = false;
    /* @var  bool    */ protected  $isYear        = false;
    /* @var  array   */ protected  $weekDayList   = array( 0 => 'SU', 1 => 'MO', 2 => 'TU',
                                                           3 => 'WE', 4 => 'TH', 5 => 'FR', 6 => 'SA' );
    /* @var  array   */ protected  $weekDays      = array( 0 => 'sun', 1 => 'mon', 2 => 'tue',
                                                           3 => 'wed', 4 => 'thu', 5 => 'fri', 6 => 'sat' );
    /* @var  array   */ protected  $monthList     = array( 1 => 'Jan', 2 => 'Feb', 3 => 'Mar',
                                                           4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul',
                                                           8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov',
                                                           12 => 'Dec');

    /**
     * __construct
     *
     * @access  public
     * @param   string  $id       uniqe id of the calendar
     * @param   string  $dtstart  begin date of the current event
     * @param   string  $dtend    end date of the current event
     * @param   string  $rrule    recurrance rule of the current event
     * @param   array   $exdate   array with dates too ignore
     * @return  bool
     */
    public function  __construct($id, $dtstart, $dtend, $rrule, $exdate = array())
    {
        $this->id = (string) $id;
        $this->start = (string) $dtstart;
        $this->end = (string) $dtend;
        $this->exdate = $exdate;
        $rrule = (string) $rrule;
        $this->setRuleProperties($rrule);
        $this->ini();
        $this->setResultList();
    }

    /**
     * int
     *
     * This function set all values of the current event
     *
     * @access  protected
     * @return  bool
     */
    protected function ini()
    {
        // check all properties and set values
        $this->_setFreq();
        $this->_setInterval();
        $this->_setUntil();
        $this->_setCount();

        $this->_setYear();
        $this->_setMonth();
        $this->_setDay();
        
        $this->_setYearDay();
        $this->_setMonthDay();
        $this->_setWeekNo();
    }

    /**
     * setRuleProperties
     *
     * This function prepair and set the rule properties of an event
     *
     * @access  protected
     * @param   string  $rule  recurrence rules of the current event
     * @return  bool
     */
    protected function setRuleProperties($rule)
    {
        assert('is_string($rule); // Wrong argument type argument 1. String expected');
        $properties = explode(';', $rule);
        $allProperties = array();
        if (!empty($properties)) {
            foreach ($properties as $property)
            {
                $rule = explode('=', $property);
                if (isset($rule[0]) && isset($rule[1])) {
                    $allProperties[$rule[0]] = $rule[1];
                }
            }
        }
        if (empty($allProperties)) {
            return false;
        }
        $this->rruleProperties = $allProperties;
        return true;
    }

    /**
     * getExdate
     *
     * This function get the event Exdate.
     * The exdate is only use if the event is an serial
     *
     * @access  public
     * @return  array
     */
    public function getExdate()
    {
        if (empty($this->exdate) || !isset($this->exdate[$this->id])) {
            $result = array();
        } else {
            $result = $this->exdate[$this->id];
        }
        return $result;
    }

    /**
     * isCount
     *
     * This function check if the event counter property is set
     *
     * @access  public
     * @return  bool
     */
    public function isCount()
    {
        $until = array_key_exists('COUNT', $this->rruleProperties);
        if ($until) {
            $this->isCount = true;
        }
        return $until;
    }

    /**
     * getCount
     *
     * This function get the event counter property
     *
     * @access  public
     * @return  string
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * _setCount
     *
     * This function set the event counter property
     *
     * @access  private
     * @return  string
     */
    private function _setCount()
    {
        if ($this->isCount()) {
            $this->count = (int)$this->rruleProperties['COUNT'];
        } else {
            $this->count = null;
        }
        return  $this->count;
    }

    /**
     * isUntil
     *
     * This function check if the event until property is set
     *
     * @access  public
     * @return  bool
     */
    public function isUntil()
    {
        $until = array_key_exists('UNTIL', $this->rruleProperties);
        if ($until) {
            $this->isUntil = true;
        }
        return $until;
    }

    /**
     * getUntil
     *
     * This function get the event until property
     *
     * @access  public
     * @return  string
     */
    public function getUntil()
    {
        return $this->until;
    }

    /**
     * getUntilasArray
     *
     * This function get the event until property as array
     *
     * @access  public
     * @return  string
     */
    public function getUntilAsArray()
    {
        return $this->untilDate;
    }

    /**
     * setUntil
     *
     * This function set the event until property
     *
     * @access  private
     * @return  string
     */
    private function _setUntilAsArray()
    {
        $result = array();
        if ($this->isUntil) {
            $dataset = $this->prepairDataset();
            if (isset($dataset['until'])) {
                $until = $dataset['until'];
                if (!empty($until)) {
                    $result['year'] = $until[1];
                    $month = $until[2];
                    if (strlen($month) == 1) {
                        $month = '0'.$month;
                    }
                    $result['month'] = $month;
                    $day = $until[3];
                    if (strlen($day) == 1) {
                        $day = '0'.$day;
                    }
                    $result['day'] = $day;
                    $result['hour'] = $until[4];
                    $result['minutes'] = $until[5];
                    $result['second'] = $until[6];
                }
            }            
        }
        $this->untilDate = $result;
        if (!empty($result)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * setUntil
     *
     * This function set the event until property
     *
     * @access  private
     * @return  string
     */
    private function _setUntil()
    {
        if ($this->isUntil()) {
            $this->until = $this->rruleProperties['UNTIL'];
            $this->_setUntilAsArray();
        } else {
            $this->until = null;
        }
        return  $this->until;
    }

    /**
     * isFreq
     *
     * This function check if the event frequncy property is set
     *
     * @access  public
     * @return  bool
     */
    public function isFreq()
    {
        $freq = array_key_exists('FREQ', $this->rruleProperties);
        if ($freq) {
            $this->isFreq = true;
        }
        return $freq;
    }

    /**
     * getFreq
     *
     * This function get the event frequency property
     *
     * @access  public
     * @return  string
     */
    public function getFreq()
    {
        return $this->freq;
    }

    /**
     * setFreq
     *
     * This function set the event frequency property
     *
     * @access  private
     * @return  string
     */
    private function _setFreq()
    {
        if ($this->isFreq()) {
            $this->freq = $this->rruleProperties['FREQ'];
        } else {
            $this->freq = null;
        }
        return  $this->freq;
    }

    /**
     * isInterval
     *
     * This function check if the event interval property is set
     *
     * @access      public
     * @return      bool
     */
    public function isInterval()
    {
        $interval = array_key_exists('INTERVAL', $this->rruleProperties);
        if ($interval) {
            $this->isInterval = true;
        }
        return $interval;
    }

    /**
     * getInterval
     *
     * This function get the event interval property
     *
     * @access      public
     * @return      integer
     */
    public function getInterval()
    {
        return (int) $this->interval;
    }

    /**
     * setInterval
     *
     * This function set the event interval property
     *
     * @access      private
     * @return      integer
     */
    private function _setInterval()
    {
        if ($this->isInterval()) {
            $this->interval = (int) $this->rruleProperties['INTERVAL'];
        } else {
            $this->interval = 0;
        }
        return $this->interval;
    }

    /**
     * isByDay
     *
     * This function check if the event day property is set
     *
     * @access      public
     * @return      bool
     */
    public function isDay()
    {
        $byDay = array_key_exists('BYDAY', $this->rruleProperties);
        if ($byDay) {
            $this->isDay = true;
        }
        return $byDay;
    }

    /**
     * getDay
     *
     * This function get the event day property
     *
     * @access      public
     * @return      array
     */
    public function getDay()
    {
        return $this->weekDay;
    }

    /**
     * setDay
     *
     * This function set the event day property
     *
     * @access      private
     * @return      array
     */
    private function _setDay()
    {
        if ($this->isDay()) {
            $weekdays = explode(',', $this->rruleProperties['BYDAY']);
            $this->weekDay = $weekdays;
        } else {
            $this->weekDay = array();
        }
        return $this->weekDay;
    }

    /**
     * _setDayWithoutRule
     *
     * This function set the event day property if noone is present.
     *
     * @access  private
     * @param   string  $day  weekday
     * @return  array
     */
    private function _setDayWithoutRule($day)
    {
        $search = array_flip($this->weekDays);
        $result = array();
        if (array_key_exists($day, $search)) {
            $result[$search[$day]] = $this->weekDayList[$search[$day]];
        }
        $this->weekDay = $result;
        return $this->weekDay;
    }

    /**
     * isByMonthDay
     *
     * This function check if the event monthday property is set
     *
     * @access  public
     * @return  bool
     */
    public function isMonthDay()
    {
        $byMonthDay = array_key_exists('BYMONTHDAY', $this->rruleProperties);
        if ($byMonthDay) {
            $this->isMonthDay = true;
        }
        return $byMonthDay;
    }

    /**
     * getMonthDay
     *
     * This function get the event monthday property
     *
     * @access  public
     * @return  array
     */
    public function getMonthDay()
    {
        return $this->monthDay;
    }

    /**
     * setMonthDay
     *
     * This function set the event monthday property
     *
     * @access  private
     * @return  array
     */
    private function _setMonthDay()
    {
        if ($this->isMonthDay()) {
            $monthDays = explode(',', $this->rruleProperties['BYMONTHDAY']);
            $this->monthDay = $monthDays;
        } else {
            $this->monthDay = array();
        }
        return $this->monthDay;
    }

    /**
     * isByYearDay
     *
     * This function check if the event yearday property is set
     *
     * @access      public
     * @return      bool
     */
    public function isYearDay()
    {
        $byYearDay = array_key_exists('BYYEARDAY', $this->rruleProperties);
        if ($byYearDay) {
            $this->isYearDay = true;
        }
        return $byYearDay;
    }

    /**
     * getYearDay
     *
     * This function get the event yearday property
     *
     * @access      public
     * @return      integer
     */
    public function getYearDay()
    {
        return (int) $this->yearDay;
    }

    /**
     * setYearDay
     *
     * This function set the event yearday property
     *
     * @access      private
     * @return      integer
     */
    private function _setYearDay()
    {
        if ($this->isYearDay()) {
            $this->yearDay = (int) $this->rruleProperties['BYYEARDAY'];
        } else {
            $this->yearDay = 0;
        }
        return $this->yearDay;
    }

    /**
     * isByWeekNo
     *
     * This function check if event week number property is set
     *
     * @access      public
     * @return      bool
     */
    public function isWeekNo() 
    {
        $byWeekNo = array_key_exists('BYWEEKNO', $this->rruleProperties);
        if ($byWeekNo) {
            $this->isWeekNo = true;
        }
        return $byWeekNo;
    }

    /**
     * getWeekNo
     *
     * This function get the event week number property
     *
     * @access      public
     * @return      integer
     */
    public function getWeekNo()
    {
        return (int) $this->weekNumber;
    }

    /**
     * setWeekNo
     *
     * This function set the event week number property
     *
     * @access      private
     * @return      integer
     */
    private function _setWeekNo()
    {
        if ($this->isWeekNo()) {
            $this->weekNumber = (int) $this->rruleProperties['BYWEEKNO'];
        } else {
            $this->weekNumber = 0;
        }
        return $this->weekNumber;
    }

    /**
     * isByMonth
     *
     * This function check if the event month property is set
     *
     * @access      public
     * @return      bool
     */
    public function isMonth()
    {
        $byMonth = array_key_exists('BYMONTH', $this->rruleProperties);
        if ($byMonth) {
            $this->isMonth = true;
        }
        return $byMonth;
    }

    /**
     * getMonth
     *
     * This function get the event month property
     *
     * @access      public
     * @return      integer
     */
    public function getMonth()
    {
        return (int) $this->month;
    }

    /**
     * setMonth
     *
     * This function set the event month property
     *
     * @access      private
     * @return      integer
     */
    private function _setMonth()
    {
        if ($this->isMonth()) {
            $this->month = (int) $this->rruleProperties['BYMONTH'];
        } else {
            $this->month = 0;
        }
        return $this->month;
    }

    /**
     * isByYear
     *
     * This function check if the event year property is set
     *
     * @access      public
     * @return      bool
     */
    public function isYear()
    {
        $byYear = array_key_exists('BYYEAR', $this->rruleProperties);
        if ($byYear) {
            $this->isYear = true;
        }
        return $byYear;
    }

    /**
     * isExdate
     *
     * This function check if the exdate is set
     *
     * @access      public
     * @return      bool
     */
    public function isExdate()
    {
        $exdate = $this->getExdate();
        if (!empty($exdate)) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }



    /**
     * getYear
     *
     * This function get the event year property
     *
     * @access      public
     * @return      integer
     */
    public function getYear()
    {
        return (int) $this->year;
    }

    /**
     * getYear
     *
     * This function set the event year property
     *
     * @access      private
     * @return      integer
     */
    private function _setYear()
    {
        if ($this->isYear()) {
            $this->year = (int) $this->rruleProperties['BYYEAR'];
        } else {
            $this->year = 0;
        }
        return $this->year;
    }

    /**
     * getWeekDays
     *
     * This function get the defined week day list
     *
     * @access      public
     * @return      array
     */
    public function getWeekDays()
    {
        return $this->weekDayList;
    }

    /**
     * getStartDate
     *
     * This function get the event begin date (timestamp)
     *
     * @access      public
     * @return      string
     */
    public function getStartDate()
    {
        return $this->start;
    }

    /**
     * getEndDate
     *
     * This function get the event end date (timestamp)
     *
     * @access      public
     * @return      string
     */
    public function getEndDate()
    {
        return $this->end;
    }

    /**
     * getEventDates
     *
     * This function get the result set of the event
     *
     * @access      public
     * @return      array
     */
    protected function getEventDates()
    {
         return $this->_calculateDate();
    }

    /**
     * setResultList
     *
     * This function set the event result set
     *
     * @access      protected
     * @return      bool
     */
    protected function setResultList()
    {
        $list = $this->getEventDates();
        $this->resultList = $list;
        return true;
    }

    /**
     * getResultList
     *
     * This function get the event result list. If an unique id is given the result set will be the current event list,
     * otherweise an empty array.
     *
     * @access      private
     * @param       string   $id   unique id of the expected event
     * @return      array
     */
    public function getResultList($id = null)
    {
        if (empty($id)) {
            return array();
        } else {
            return $this->resultList[$id];
        }
    }

    /**
     * calculateDate
     *
     * This function is checking which frequency is set for the event and call the expected function. The result set is
     * an event data list which all dates for them.
     *
     * @access      private
     * @return      array
     */
    private function _calculateDate()
    {
        $frequency = $this->getFreq();
        $result = array();
        $eventDates = array();
        switch ($frequency)
        {
            case 'DAILY':
                $result = $this->calculateDailyDateOptions(
                    $this->getStartDate(),
                    $this->getEndDate(),
                    $this->getInterval(),
                    $this->getDay()
                );
            break;
            case 'WEEKLY':
                $result = $this->calculateWeeklyDateOptions(
                    $this->getStartDate(),
                    $this->getEndDate(),
                    $this->getInterval(),
                    $this->getDay()
                );
            break;
            case 'MONTHLY':
                $result = $this->calculateMonthlyDateOptions(
                    $this->getStartDate(),
                    $this->getEndDate(),
                    $this->getInterval(),
                    $this->getMonth(),
                    $this->getMonthDay(),
                    $this->getDay()
                );
            break;
            case 'YEARLY':
                $result = $this->calculateYearlyDateOptions(
                    $this->getStartDate(),
                    $this->getEndDate(),
                    $this->getInterval(),
                    $this->getMonth(),
                    $this->getMonthDay()
                );
            break;
            default:
                $result = $this->calculateDefaultDate(
                    $this->getStartDate(),
                    $this->getEndDate()
                );
            break;
        }
        $eventDates[$this->id] = $result;
        return $eventDates;
    }

    /**
     * checkForExdate
     *
     * This function check if the dates are set on exdate.
     * If a match be found the date will be not shown on the result list.
     *
     * @access  protected
     * @param   int   $year    year
     * @param   int   $month   month
     * @param   int   $day     day
     * @return  array
     */
    protected function checkForExdate($year, $month, $day)
    {
        $year =  (int) $year;
        $month = (int) $month;
        $day =  (int)  $day;
        $calculateDate = mktime(0, 0, 0, $month, $day, $year);
        $year =  (int) date('Y', $calculateDate);
        $month = (int) date('m', $calculateDate);
        $day =  (int)  date('d', $calculateDate);
        $result = true;        
          
        if ($this->isExdate()) {
            $exdate = $this->getExdate();             
            if (is_array($exdate)) {
                $match = array();
                foreach ($exdate as $date => $name)
                {
                    if (preg_match('/(\d{4})(\d{2})(\d{2})T(\d{2})(\d{2})(\d{2})(\w*)/', $date, $eTime)) {
                        if ($year == (int)$eTime[1] && $month == (int)$eTime[2] && $day == (int)$eTime[3]) {
                            $match[] = false;
                        } else {
                            $match[] = true;
                        }
                    } else {
                        // no match
                        $match[] = true;
                    }
                }
                if (in_array(false, $match)) {
                    $result = false;
                }
            } else {
                $result = true;
            }
        } else {
            $result = true;
        }
        return $result;
    }

    /**
     * prepairDataset
     *
     * This function prepair the date timestamp and return an array with dates.
     *
     * @access      protected
     * @param       string   $start   begin date of the event (timestamp)
     * @param       string   $end     end date of the event   (timestamp)
     * @return      array
     */
    protected function prepairDataset($start = "", $end = "")
    {
        $data = array();
        if (preg_match('/(\d{4})(\d{2})(\d{2})T(\d{2})(\d{2})(\d{2})(\w*)/', $start, $beginData)) {
            $data['start'] = $beginData;
        } elseif (preg_match('/(\d{4})(\d{2})(\d{2})/', $start, $beginData)) {
            $data['start'] = $beginData;
            $data['start'][4] = 0;
            $data['start'][5] = 0;
            $data['start'][6] = 0;
        } else {
            $data['start'] = 0;
        }

        if (preg_match('/(\d{4})(\d{2})(\d{2})T(\d{2})(\d{2})(\d{2})(\w*)/', $end, $endData)) {
            $data['end'] = $endData;
        } elseif (preg_match('/(\d{4})(\d{2})(\d{2})/', $end, $endData)) {
            $data['end'] = $endData;
            $data['end'][4] = 0;
            $data['end'][5] = 0;
            $data['end'][6] = 0;
        } else {
            $data['end'] = 0;
        }

        if ($this->isUntil()) {
            if (preg_match('/(\d{4})(\d{2})(\d{2})T(\d{2})(\d{2})(\d{2})(\w*)/', $this->getUntil(), $until)) {
                $data['until'] = $until ;
            }
        }        
        return $data;
    }

    /**
     * checkForAllDay
     *
     * Check if the current event is an allDay event. An event is AllDay if the hours and minutes are "0"
     *
     * @access      protected
     * @param       array     $dateArray      array with start and end date for an event
     */
    protected function checkForAllDay(array $dateArray)
    {
        $start = false;
        $end = false;
        if (!empty ($dateArray)) {
            if (isset($dateArray['start']) && $dateArray['start'][4] == 0 && $dateArray['start'][5] == 0) {
                $start = true;
            }
            if (isset($dateArray['end']) && $dateArray['end'][4] == 0 && $dateArray['end'][5] == 0) {
                $end = true;
            }
        }
        if ($start && $end) {
            $this->setAllDay(true);
        } else {
            $this->setAllDay(false);
        }        
    }

    /**
     * isAllDay
     *
     * Get the Information for an allDay event
     *
     * @access  public
     * @return  bool
     */
    public function isAllDay()
    {
        if ($this->allDay) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * setAllDay
     *
     * Set the allDay information for an event.
     *
     * @access  protected
     * @param   bool    $allDay
     */
    protected function setAllDay($allDay)
    {
        assert('is_bool($allDay); // Wrong argument type argument 1. Boolean expected');
        $this->allDay = $allDay;

    }

    /**
     * mappingWeekDays
     *
     * This function get the expected weekdays for display, the result set is the start and end date for an expected
     * event
     *
     * @access      protected
     * @param       array     $days         days for merge
     * @param       array     $beginDate    event start date
     * @param       integer   $endDate      event end date
     * @param       integer   $diff         difference between start and end date in days
     * @param       bool      $isCount      (true = isCount, false otherweise)
     * @param       bool      $isUntil      (true = isUntil, false otherweise)
     * @param       bool      $isInterval   (true = isInterval, false otherweise)
     * @return      array     return an formated result array for event
     */
    protected function mappingWeekDays(array $days, $beginDate, $endDate, $diff = 0, $isCount = false, $isUntil = false, $isInterval = false)
    {
        $defList = $this->getWeekDays();
        $days = $days;
        // prepair def list
        $displayDayList = array();
        foreach ($defList as $key => $item)
        {
            if (in_array($item, $days)) {
                $displayDayList[$key] = $item;
            }
        }
        // block for calculate start date
        $dataset = $this->prepairDataset($beginDate, $endDate);
        $start = $dataset['start'];
        $end = $dataset['end'];
        $result = array();
        if ($isInterval) {
            $weekInterval = $this->getInterval();
        }
        if (!empty($dataset)) {
            if ($isUntil) {
                // end date for compare
                $untilArrayDate = $this->getUntilAsArray();
                $untilDate = $untilArrayDate['year'].$untilArrayDate['month'].$untilArrayDate['day'];
                $count = $diff;
                $addDays = 0;
                $addWeek = 0;
                $countDisplay = $displayDayList;                
                for($i=0;$i<=$count;)
                {
                    $daysToAdd = $addDays;
                    if (isset($weekInterval)) {
                        $w = ($weekInterval * 7) * $addWeek;
                        $daysToAdd = $w + $addDays;
                    } else {
                        $w =  7 * $addWeek;
                        $daysToAdd = $w + $addDays;
                    }
                    $valid = mktime($start[4], $start[5], $start[6], $start[2], $start[3] + $daysToAdd, $start[1]);
                    if (array_key_exists(date('w', $valid), $displayDayList)) {
                        if (!empty($countDisplay)) {
                            unset($countDisplay[(int)date('w', $valid)]);
                        }
                        if ($this->checkForExdate($start[1], $start[2], $start[3] + $daysToAdd)) {
                            $e['start'] = mktime(
                                $start[4],
                                $start[5],
                                $start[6],
                                $start[2],
                                $start[3] + $daysToAdd,
                                $start[1]
                            );
                            $e['end'] = mktime(
                                $end[4],
                                $end[5],
                                $end[6],
                                $end[2],
                                $end[3] + $daysToAdd,
                                $end[1]
                            );
                            $currentDate = date('Y', $e['start']) . date('m', $e['start']) . date('d', $e['start']);
                            
                            $e['start'] *= 1000;
                            $e['end'] *= 1000;
                            $result[$i]   = $e;

                            $calculate = $untilDate - $currentDate;
                            if ($calculate < 0) {
                                unset($result[$i]);
                                break;
                            }
                        }                        

                        if (count($countDisplay) == 0) {
                            $addWeek ++;
                            $addDays = 0;
                        }
                        $i ++;
                    }

                    if (!empty($countDisplay)) {
                        $addDays ++;
                    } else {
                        $countDisplay = $displayDayList;
                    }
                }
            } elseif ($isCount) {
                $count = $this->getCount() - 1;
                $addDays = 0;
                $addWeek = 0;
                $countDisplay = $displayDayList;
                for ($i = 0; $i <= $count;)
                {
                    $daysToAdd = $addDays;
                    if (isset($weekInterval)) {
                        $w = ($weekInterval * 7) * $addWeek;
                        $daysToAdd = $w + $addDays;
                    } else {
                        $w = 7 * $addWeek;
                        $daysToAdd = $w + $addDays;
                    }
                    $valid = mktime($start[4], $start[5], $start[6], $start[2], $start[3] + $daysToAdd, $start[1]);
                    if (array_key_exists(date('w', $valid), $displayDayList)) {
                        if (!empty($countDisplay)) {
                            unset($countDisplay[(int)date('w', $valid)]);
                        }
                        if ($this->checkForExdate($start[1], $start[2], $start[3] + $daysToAdd)) {
                            $e['start'] = mktime(
                                $start[4], $start[5], $start[6], $start[2],
                                $start[3] + $daysToAdd, $start[1]
                            );
                            $e['end'] = mktime(
                                $end[4], $end[5], $end[6], $end[2], $end[3] + $daysToAdd, $end[1]
                            );
                            
                            $e['start'] *= 1000;
                            $e['end'] *= 1000;
                            $result[]   = $e;
                        }
                        if (count($countDisplay) == 0) {
                            $addWeek ++;
                            $addDays = 0;
                        }
                        $i ++;
                    }
                    if (!empty($countDisplay)) {
                        $addDays ++;
                    } else {
                        $countDisplay = $displayDayList;
                    }
                }
            } else {
                $count = $diff;
                $addDays = 0;
                $addWeek = 0;
                $countDisplay = $displayDayList;
                for ($i=0;$i<=$count;)
                {
                    $daysToAdd = $addDays;
                    if (isset($weekInterval)) {
                        $w = ($weekInterval * 7) * $addWeek;
                        $daysToAdd = $w + $addDays;
                    } else {
                        $w = 7 * $addWeek;
                        $daysToAdd = $w + $addDays;
                    }
                    $valid = mktime($start[4], $start[5], $start[6], $start[2], $start[3] + $daysToAdd, $start[1]);
                    if (array_key_exists(date('w', $valid), $displayDayList)) {
                        if (!empty($countDisplay)) {
                            unset($countDisplay[(int)date('w', $valid)]);
                        }
                        if ($this->checkForExdate($start[1], $start[2], $start[3] + $daysToAdd)) {
                            $e['start'] = mktime(
                                $start[4],
                                $start[5],
                                $start[6],
                                $start[2],
                                $start[3] + $daysToAdd,
                                $start[1]
                            );
                            $e['end'] = mktime(
                                $end[4],
                                $end[5],
                                $end[6],
                                $end[2],
                                $end[3] + $daysToAdd,
                                $end[1]
                            );

                            $e['start'] *= 1000;
                            $e['end'] *= 1000;
                            $result[]   = $e;
                        }
                        if (count($countDisplay) == 0) {
                            $addWeek ++;
                            $addDays = 0;
                        }
                        $i ++;
                    }
                    if (!empty($countDisplay)) {
                        $addDays ++;
                    } else {
                        $countDisplay = $displayDayList;
                    }
                }
            }
        }
        return $result;
    }


    /**
     * mappingDays
     *
     * This function get the expected weekdays for display, the result set is the start and end date for an expected
     * event
     *
     * @access      protected
     * @param       array     $days         days for merge
     * @param       array     $beginDate    event start date
     * @param       integer   $endDate      event end date
     * @param       integer   $diff         difference between start and end date in days
     * @param       bool      $isCount      (true = isCount , false otherweise)
     * @param       bool      $isUntil      (true = isUntil , false otherweise)
     * @return      array     return an formated result array for event
     */
    protected function mappingDays(array $days, $beginDate, $endDate, $diff = 0, $isCount = false, $isUntil = false)
    {
        $defList = $this->getWeekDays();
        $days = $days;
        // prepair def list
        $displayDayList = array();
        foreach ($defList as $key => $item)
        {
            if (in_array($item, $days)) {
                $displayDayList[$key] = $item;
            }
        }
        // block for calculate start date
        $dataset = $this->prepairDataset($beginDate, $endDate);
        $start = $dataset['start'];
        $end = $dataset['end'];
        $result = array();
        if (!empty($dataset)) {
            if ($isUntil) {
                $count = $diff;
                for ($i = 0; $i <= $count; $i++)
                {
                    $valid = mktime($start[4], $start[5], $start[6], $start[2], $start[3] + $i, $start[1]);
                    if (array_key_exists(date('w', $valid), $displayDayList)) {
                        if ($this->checkForExdate($start[1], $start[2], $start[3] + $i)) {
                            $e['start'] = mktime(
                                $start[4],
                                $start[5],
                                $start[6],
                                $start[2],
                                $start[3] + $i,
                                $start[1]
                            );
                            $e['end'] = mktime(
                                $end[4],
                                $end[5],
                                $end[6],
                                $end[2],
                                $end[3] + $i,
                                $end[1]
                            );

                            $e['start'] *= 1000;
                            $e['end'] *= 1000;
                            $result[] = $e;
                        }
                    }
                }
            } elseif ($isCount) {
                $count = $this->getCount() - 1;
                $addDays = 0;
                for ($i = 0; $i <= $count;)
                {   
                    $valid = mktime($start[4], $start[5], $start[6], $start[2], $start[3] + $addDays, $start[1]);
                    if (array_key_exists(date('w', $valid), $displayDayList)) {
                        if ($this->checkForExdate($start[1], $start[2], $start[3] + $addDays)) {
                            $e['start'] = mktime(
                                $start[4],
                                $start[5],
                                $start[6],
                                $start[2],
                                $start[3] + $addDays,
                                $start[1]
                            );
                            $e['end'] = mktime(
                                $end[4],
                                $end[5],
                                $end[6],
                                $end[2],
                                $end[3] + $addDays,
                                $end[1]
                            );

                            $e['start'] *= 1000;
                            $e['end'] *= 1000;
                            $result[] = $e;
                        }
                        $i ++;
                    }
                    $addDays ++;
                }
            } else {               
                $count = $diff;
                $addDays = 0;
                for($i=0;$i<=$count;)
                {
                    $valid = mktime($start[4], $start[5], $start[6], $start[2], $start[3] + $addDays, $start[1]);
                    if (array_key_exists(date('w', $valid), $displayDayList)) {
                        if ($this->checkForExdate($start[1], $start[2], $start[3] + $addDays)) {
                            $e['start'] = mktime(
                                $start[4],
                                $start[5],
                                $start[6],
                                $start[2],
                                $start[3] + $addDays,
                                $start[1]
                            );
                            $e['end'] = mktime(
                                $end[4],
                                $end[5],
                                $end[6],
                                $end[2],
                                $end[3] + $addDays,
                                $end[1]
                            );

                            $e['start'] *= 1000;
                            $e['end'] *= 1000;
                            $result[] = $e;
                        }
                        $i ++;
                    }
                    $addDays ++;
                }
            }
        } 
        return $result;
    }

    /**
     * calculateDailyDateOptions
     *
     * This function calculate all daily options for the current event.
     *
     * @access      protected
     * @param       string    $beginDate      event begin date (timestamp)
     * @param       string    $endDate        event end date   (timestamp)
     * @param       integer   $interval       event interval
     * @param       array     $byDay          event days
     * @return      array
     */
    protected function calculateDailyDateOptions($beginDate, $endDate, $interval = 0, $byDay = array())
    {  
        $dateResult = $this->prepairDataset($beginDate, $endDate);
        // check if the daily event is allDay
        $this->checkForAllDay($dateResult);        
        $start = $dateResult['start'];
        $end = $dateResult['end'];
        $result = array();
        $dates = array();
        if ($this->isUntil()) {
            $diff = $this->diffDay($start, $dateResult['until']);
            if ($this->isInterval()) {
                $count = round($diff / $this->getInterval()) - 1;
                for($i=0;$i<=$count;$i++) {
                    $day = $this->getInterval() * $i;
                    if ($this->checkForExdate($start[1], $start[2], $start[3] + $day)) {
                        $dates[$i]['start'] = mktime(
                            $start[4],
                            $start[5],
                            $start[6],
                            $start[2],
                            $start[3] + $day,
                            $start[1]
                        );
                        $dates[$i]['end'] = mktime(
                            $end[4],
                            $end[5],
                            $end[6],
                            $end[2],
                            $end[3] + $day,
                            $end[1]
                        );

                        $dates[$i]['start'] *= 1000;
                        $dates[$i]['end'] *= 1000;
                    }
                }
            } elseif ($this->isDay) {
                $dates = $this->mappingDays($this->getDay(), $beginDate, $endDate, $diff, false, true);
            } else {
                $count = $diff;
                for($i=0;$i<=$count;$i++) {
                    $day = $i;
                    if ($this->checkForExdate($start[1], $start[2], $start[3] + $day)) {
                        $dates[$i]['start'] = mktime($start[4], $start[5], $start[6], $start[2], $start[3] + $day, $start[1]);
                        $dates[$i]['end'] = mktime($end[4], $end[5], $end[6], $end[2], $end[3] + $day, $end[1]);

                        $dates[$i]['start'] *= 1000;
                        $dates[$i]['end'] *= 1000;
                    }
                }
            }
        } elseif($this->isCount()) {
            if ($this->isInterval()) {
                $count = $this->getCount() - 1;
                $interval = $this->getInterval();
                for($i=0;$i<=$count;$i++) {
                    $day = $this->getInterval() * $i;
                    if ($this->checkForExdate($start[1], $start[2], $start[3] + $day)) {
                        $dates[$i]['start'] = mktime($start[4], $start[5], $start[6], $start[2], $start[3] + $day , $start[1]);
                        $dates[$i]['end'] = mktime($end[4], $end[5], $end[6], $end[2], $end[3] + $day, $end[1]);

                        $dates[$i]['start'] *= 1000;
                        $dates[$i]['end'] *= 1000;
                    }
                }
            } elseif ($this->isDay) {
                $dates = $this->mappingDays($this->getDay(), $beginDate, $endDate, 0, true);
            } else {
                $count = $this->getCount();
                for($i=1;$i<=$count;$i++) {
                    $day = $i;
                    if ($this->checkForExdate($start[1], $start[2], $start[3] + $day)) {
                        $dates[$i]['start'] = mktime($start[4], $start[5], $start[6], $start[2], $start[3] + $day, $start[1]);
                        $dates[$i]['end'] = mktime($end[4], $end[5], $end[6], $end[2], $end[3] + $day, $end[1]);

                        $dates[$i]['start'] *= 1000;
                        $dates[$i]['end'] *= 1000;
                    }
                }
            }
        } else {
            // options for endless Serial
            if ($this->isDay) {
                $diff = $this->endlessSerial($start);                
                $dates = $this->mappingDays($this->getDay(), $beginDate, $endDate, $diff);
            } else {
                if ($this->isInterval()) {
                    $interval = $this->getInterval();
                }
                if (!isset($interval) || $interval == 0) {
                    $interval = 1;
                }
                $count = $this->endlessSerial($start);
                for($i=0;$i<=$count;$i++) {
                    $day = $interval * $i;
                    if ($this->checkForExdate($start[1], $start[2], $start[3] + $day)) {
                        $dates[$i]['start'] = mktime($start[4], $start[5], $start[6], $start[2], $start[3] + $day, $start[1]);
                        $dates[$i]['end'] = mktime($end[4], $end[5], $end[6], $end[2], $end[3] + $day, $end[1]);

                        $dates[$i]['start'] *= 1000;
                        $dates[$i]['end'] *= 1000;
                    }
                }
            }
        }
        $result[$this->id] =$dates;
        return $result;
    }

    /**
     * diffDay
     *
     * This function calculate the difference beetwen the start and end date (until).
     *
     * @access  protected
     * @param   array    $start      event begin date
     * @param   array    $until      event until date
     * @return  integer  difference betwen begin and start date in days
     */
    protected function diffDay($start, $until)
    {
        $startdate = mktime($start[4], $start[5], $start[6], $start[2], $start[3], $start[1]);
        $enddate = mktime($until[4], $until[5], $until[6], $until[2], $until[3], $until[1]);        
        $diff = (int) (round((($enddate-$startdate)/86400), 0));
        return $diff;
    }

    /**
     * endlessSerial
     *
     * This function calculate the dates for an endless Serial option.
     * This steep calulate an serial but with an until date.
     * It calculate dates for the next 5 years. U can set this up if u initialize the calendar, for example:
     *  $calendar = Calendar::getInstance();
     *  $calendar->setEndlessSerialEndDate(10);
     *
     * @access  public
     * @param   array  $start   event start date
     * @return  int
     */
    protected function endlessSerial(array $start)
    {
        assert('is_array($start); // Wrong argument type argument 1. Array expected');

        // get the endles serial end date
        $endlessSerial = $this->getEndlessSerialEndDate();
        $interval = 0;
        if ($this->isInterval()) {
            $interval = $this->getInterval();
        }

        // set the end date for this serial
        $until = array('', $start[1] + $endlessSerial, $start[2], $start[3], $start[4], $start[5], $start[6]);
        $diff = $this->diffDay($start, $until);
        assert('is_int($diff); // Wrong argument type argument 1. Integer expected');
        $result = $diff;
        return $result;
    }

    /**
     * setEndlessSerialEndDate
     *
     * This function set the calendar endless serial end date. 
     *
     * @access  public
     * @param   int  $number
     * @return  bool
     */
    public function setEndlessSerialEndDate($number)
    {
        assert('is_int($number); // Wrong argument type argument 1. Integer expected');
        $this->endlessSerial = $number;
        return true;
    }

    /**
     * getEndlessSerialEndDate
     *
     * This function get the calendar endless serial End date.
     *
     * @access  public
     * @return  int
     */
    public function getEndlessSerialEndDate()
    {
        return $this->endlessSerial;
    }

    /**
     * calculateWeeklyDateOptions
     *
     * This function calculate all weekly options for the current event.
     *
     * @access      protected
     * @param       string    $beginDate      event begin date (timestamp)
     * @param       string    $endDate        event end date   (timestamp)
     * @param       integer   $interval       event interval
     * @param       array     $byDay          event days
     * @return      array
     */
    protected function calculateWeeklyDateOptions($beginDate, $endDate, $interval = 0, $byDay = array())
    {
        $dateResult = $this->prepairDataset($beginDate, $endDate);
        // check if the daily event is allDay
        $this->checkForAllDay($dateResult);
        $start = $dateResult['start'];
        $end = $dateResult['end'];
        $days = 7;
        $result = array();
        $dates = array();
        if ($this->isUntil()) {
            $until = $dateResult['until'];
            $diff = $this->diffDay($start, $until);
            if ($this->isInterval()) {
                $interval = $this->getInterval();
                $count = round(($diff / $days) / $this->getInterval());
               
                for($i=0;$i<=$count;$i++) {
                     $weeks = ($interval * $days) * $i;
                     if ($this->checkForExdate($start[1], $start[2], $start[3] + $weeks)) {
                         $dates[$i]['start'] = mktime($start[4], $start[5], $start[6], $start[2], $start[3] + $weeks, $start[1]);
                         $dates[$i]['end'] = mktime($end[4], $end[5], $end[6], $end[2], $end[3] + $weeks, $end[1]);

                         $dates[$i]['start'] *= 1000;
                         $dates[$i]['end'] *= 1000;
                     }
                }
                if ($this->isDay()) {
                    $dates = $this->mappingWeekDays($this->getDay(), $beginDate, $endDate, $diff, false, true, true);
                } else {
                    /* added for fill an until day if noone is given */
                    $day = strtolower($this->getUntilEventDay($start, 'D'));
                    $setDay = $this->_setDayWithoutRule($day);
                }
            } else { 
                if ($this->isDay()) {
                    $dates = $this->mappingWeekDays($this->getDay(), $beginDate, $endDate, $diff, false, true, false);
                } else {
                    $count = round($diff / $days);
                    /* added for fill an until day if noone is given */
                    $day = strtolower($this->getUntilEventDay($start, 'D'));
                    $setDay = $this->_setDayWithoutRule($day);
                    for($i=0;$i<=$count;$i++) {
                         $weeks = $i;
                         if ($this->checkForExdate($start[1], $start[2], $start[3] + $weeks)) {
                             $dates[$i]['start'] = mktime($start[4], $start[5], $start[6], $start[2], $start[3] + $weeks , $start[1]);
                             $dates[$i]['end'] = mktime($end[4], $end[5], $end[6], $end[2], $end[3] + $weeks, $end[1]);

                             $dates[$i]['start'] *= 1000;
                             $dates[$i]['end'] *= 1000;
                         }
                    }
                }
            }
        } elseif ($this->isCount()) {
            $count = $this->getCount();
            if ($this->isInterval()) {
                $interval = $this->getInterval() * $days;
                if ($this->isDay()) {
                    $dates = $this->mappingWeekDays($this->getDay(), $beginDate, $endDate, 0, true, false, true);
                } else {
                    $count = $count - 1;
                    /* added for fill an until day if noone is given */
                    $day = strtolower($this->getUntilEventDay($start, 'D'));
                    $setDay = $this->_setDayWithoutRule($day);
                    for($i=0;$i<=$count;$i++) {
                        $week = $interval * $i;
                        if ($this->checkForExdate($start[1], $start[2], $start[3] + $week)) {
                            $dates[$i]['start'] = mktime($start[4], $start[5], $start[6], $start[2], $start[3] + $week, $start[1]);
                            $dates[$i]['end'] = mktime($end[4], $end[5], $end[6], $end[2], $end[3] + $week, $end[1]);

                            $dates[$i]['start'] *= 1000;
                            $dates[$i]['end'] *= 1000;
                        }
                    }
                }
            } else {
                if ($this->isDay()) {
                    $dates = $this->mappingWeekDays($this->getDay(), $beginDate, $endDate, 0, true, false, false);
                } else {
                    $count = $count - 1;
                    /* added for fill an until day if noone is given */
                    $day = strtolower($this->getUntilEventDay($start, 'D'));
                    $setDay = $this->_setDayWithoutRule($day);

                    for($i=0;$i<=$count;$i++) {
                        $week = $i * $days;
                        if ($this->checkForExdate($start[1], $start[2], $start[3] + $week)) {
                            $dates[$i]['start'] = mktime($start[4], $start[5], $start[6], $start[2], $start[3] + $week, $start[1]);
                            $dates[$i]['end'] = mktime($end[4], $end[5], $end[6], $end[2], $end[3] + $week, $end[1]);

                            $dates[$i]['start'] *= 1000;
                            $dates[$i]['end'] *= 1000;
                        }
                    }
                }
            }
        } else {
            if ($this->isDay()) {
                $diff = $this->endlessSerial($start);
                $dates = $this->mappingWeekDays($this->getDay(), $beginDate, $endDate, $diff);
            } else {
                $dates[0]['start'] = mktime($start[4], $start[5], $start[6], $start[2], $start[3], $start[1]);
                $dates[0]['end'] = mktime($end[4], $end[5], $end[6], $end[2], $end[3], $end[1]);

                $dates[0]['start'] *= 1000;
                $dates[0]['end'] *= 1000;
            }
        }
        if (empty($dates)) {
            return false;
        }
        $result[$this->id] = $dates;
        return $result;

    }

    /**
     * calculateMonthlyDateOptions
     *
     * This function calculate all monthly options for the current event.
     *
     * @access      protected
     * @param       string    $begin      event begin date (timestamp)
     * @param       string    $end        event end date   (timestamp)
     * @param       integer   $interval   event interval
     * @param       integer   $byMonth    event month
     * @param       integer   $byWeekDay  event week day
     * @param       integer   $byday      event week number
     * @return      array
     */
    protected function calculateMonthlyDateOptions($begin, $end, $interval = 0, $byMonth = 0, $byWeekDay = 0, $byDay = 0)
    {

        $dateResult = $this->prepairDataset($begin, $end);
        // check if the daily event is allDay
        $this->checkForAllDay($dateResult);
        $start = $dateResult['start'];
        $end = $dateResult['end'];
        $days = 7;
        $dates = array();
        $result = array();

        if ($this->isUntil()) {
            $until = $dateResult['until'];
            $diff = $this->diffDay($start, $until);
            if ($this->isInterval()) {
                $interval = $this->getInterval();
                if ($this->isDay()) {
                    // until + interval + day
                } elseif ($this->isMonthDay()) {
                    // until + interval + monthday
                    $startMonth = $this->getUntilEventMonth($start);
                    $endMonth = $this->getUntilEventMonth($until);
                    $count = ($endMonth - $startMonth) - 1;
                    $monthdays = $this->getMonthDay();
                    for($i=0;$i<=$count;$i++) {
                         $dates[] = $this->getNextMonthlyDate($start, $end, $monthdays, $i, true);
                    }
                    // prepair result
                    foreach ($dates as  $dataset)
                    {
                        foreach ($dataset as $key => $dates)
                        if (!empty($dates)) {
                            $result[] =  $dates;
                        }
                    }
                } else {
                    // left blank
                }
            } else {
                if ($this->isMonthDay()) {
                    $startMonth = $this->getUntilEventMonth($start);
                    $endMonth = $this->getUntilEventMonth($until);
                    $count = ($endMonth - $startMonth);
                    $monthdays = $this->getMonthDay();
                    for($i=0;$i<=$count;$i++) {
                         $dates[] = $this->getNextMonthlyDate($start, $end, $monthdays, $i);
                    }
                    // prepair result
                    foreach ($dates as  $dataset)
                    {
                        foreach ($dataset as $key => $dates)
                        if (!empty($dates)) {
                            $result[] =  $dates;
                        }
                    }
                } elseif ($this->isDay()) {
                    $specialDays = $this->getSpecialDay($this->getDay());                    
                    $startMonth = $this->getUntilEventMonth($start);
                    $endMonth = $this->getUntilEventMonth($until);
                    $count = ($endMonth - $startMonth);
                    if ($count < 0 ) {
                        $startYear = $this->getUntilEventYear($start);
                        $endYear = $this->getUntilEventYear($until);
                        $countYear = ($endYear - $startYear);
                        if ($countYear != 0) {
                            $count = ((12 * $countYear) - $startMonth) * 7;
                        }
                    }                    
                    foreach ($specialDays as $key => $data)
                    {
                        for ($i=0;$i<=$count;$i++)
                        {
                            $dates[] = $this->getSpecialDayResult($start, $end, $data['dayRepeatInterval'], $data['weekday'], $i, 0, false, true);
                        }
                    }                    
                    // prepair result
                    foreach ($dates as  $dataset)
                    {
                        foreach ($dataset as $key => $dates)
                        if (!empty($dates)) {
                            $result[] =  $dates;
                        }
                    }
                }
            }
        } elseif ($this->isCount()) {
            $count = $this->getCount();
            if ($this->isInterval()) {
                $interval = $this->getInterval();
                if ($this->isDay()) {
                    //  count + interval + day
                    $specialDays = $this->getSpecialDay($this->getDay());
                    foreach ($specialDays as $key => $data)
                    {
                        for ($i=0;$i<=$count;$i++)
                        {
                            $dates[] = $this->getSpecialDayResult($start, $end, $data['dayRepeatInterval'], $data['weekday'], $i, 0, false, true);
                        }
                    }
                    // prepair result
                    foreach ($dates as  $dataset)
                    {
                        foreach ($dataset as $key => $dates)
                        if (!empty($dates)) {
                            $result[] =  $dates;
                        }
                    }
                } elseif ($this->isMonthDay()) {
                    // count + interval + monthday
                    $count = $count - 1;
                    $monthdays = $this->getMonthDay();
                    for($i=0;$i<=$count;$i++) {
                         $dates[] = $this->getNextMonthlyDate($start, $end, $monthdays, $i, true);
                    }
                    // prepair result
                    foreach ($dates as  $dataset)
                    {
                        foreach ($dataset as $key => $dates)
                        if (!empty($dates)) {
                            $result[] =  $dates;
                        }
                    }
                } else {
                    //left blank
                }
            } else {
                //count + monthday
                $count = $count - 1;
                if($this->isMonthDay) {
                    $monthdays = $this->getMonthDay();
                    for($i=0;$i<=$count;$i++) {
                         $dates[] = $this->getNextMonthlyDate($start, $end, $monthdays, $i);
                    }
                    // prepair result
                    $counter = 0;
                    foreach ($dates as  $dataset)
                    {
                        foreach ($dataset as $key => $dates)
                        {
                            if ($count >= $counter) {
                                $result[] =  $dates;
                                $counter ++;
                            }
                        }
                    }
                } elseif ($this->isDay()) {
                    $specialDays = $this->getSpecialDay($this->getDay());                    
                    foreach ($specialDays as $key => $data)
                    {
                        for ($i=0;$i<=$count;$i++)
                        {
                            $dates[] = $this->getSpecialDayResult($start, $end, $data['dayRepeatInterval'], $data['weekday'], $i, 0, true);
                        }
                    }
                    // prepair result
                    foreach ($dates as  $dataset)
                    {
                        foreach ($dataset as $key => $dates)
                        if (!empty($dates)) {
                            $result[] =  $dates;
                        }
                    }
                }
            }
        } else {
            // this steep is for endless Series
            //count + monthday
            $count = $this->endlessSerial($start);
            if($this->isMonthDay) {
                $monthdays = $this->getMonthDay();
                // month nummber to add
                $addMonth = 0;
                $addYear = 0;
                for($i=0;$i<=$count;$i++) {
                     $dates[] = $this->getNextMonthlyDate($start, $end, $monthdays, $addMonth, false, $addYear);
                     if ($addMonth == 12) {
                        $addMonth = 0;
                        $addYear++;
                     } else {
                        $addMonth ++;
                     }
                }
                // prepair result
                $counter = 0;
                foreach ($dates as  $dataset)
                {
                    foreach ($dataset as $key => $dates)
                    {
                        if ($count >= $counter) {
                            $result[] =  $dates;
                            $counter ++;
                        }
                    }
                }
            } elseif ($this->isDay()) {
                $specialDays = $this->getSpecialDay($this->getDay());
                // month nummber to add
                $addMonth = 1;
                $addYear = 0;
                foreach ($specialDays as $key => $data)
                {
                    for ($i=0;$i<=$count;$i++)
                    {
                        $dates[] = $this->getSpecialDayResult($start, $end, $data['dayRepeatInterval'], $data['weekday'], $addMonth, $addYear);
                        if ($addMonth == 12) {
                            $addMonth = 0;
                            $addYear++;
                        } else {
                            $addMonth ++;
                        }
                    }
                }
                // prepair result
                foreach ($dates as  $dataset)
                {
                    foreach ($dataset as $key => $dates)
                    {
                        if (!empty($dates)) {
                            $result[] =  $dates;
                        }
                    }
                }
            }
            // end endless series
        }
        $result[$this->id] = $result;
        return $result;
    }
    
    /**
     * getSpecialDayResult
     *
     * This function calculate all monthly options for the current event.
     *
     * @access      protected
     * @param       string    $start                 start date of an event
     * @param       string    $end                   end date of an event
     * @param       string    $repeatInterval        event repeat interval
     * @param       integer   $day                   event repat day
     * @param       integer   $addmonth              addmonth number
     * @param       integer   $addYear               addyear number
     * @param       bool      $isCount               (true = isCount, false otherweise)
     * @param       bool      $isUntil               (true = isUntil, false otherweise)
     * @return      array
     */
    protected function getSpecialDayResult($start, $end, $repeatInterval, $day = 0, $addmonth = 0, $addYear = 0, $isCount = false, $isUntil = false)
    {
        assert('is_array($start);          // Wrong argument type argument 1. Array expected');
        assert('is_array($end);            // Wrong argument type argument 2. Array expected');
        assert('is_string($repeatInterval);   // Wrong argument type argument 3. String expected');
        assert('is_int($day);              // Wrong argument type argument 4. Integer expected');
        assert('is_int($addmonth);         // Wrong argument type argument 5. Integer expected');
        assert('is_bool($isCount);         // Wrong argument type argument 6. Boolean expected');
        assert('is_bool($isCount);         // Wrong argument type argument 7. Boolean expected');
        $addToDay = 0;
        if($this->isAllDay()) {
            $addToDay = 1;
        }
        if ($repeatInterval == '-1') {
            // this options are for last day in month (selected by day)
            $weekList = $this->weekDays;
            $result = array();

            $beginDay = strtolower($weekList[$day]);
            $monthListNr = $this->getUntilEventMonth($start, 'm') + $addmonth;
            //add month for calculate the last selected day by month
            $monthListNr = $monthListNr + 1;
            $addYear = $addYear;
            for ($i=$monthListNr;$i>12;)
            {
                $i -= 12;
                $addYear++;
            }
            $monthListNr = $i;
            unset($i);

            $beginMonth = $this->monthList[$monthListNr];
            $beginYear = $this->getUntilEventYear($start) + $addYear;

            $endDay = strtolower($weekList[$day]);
            $monthEndListNr = $this->getUntilEventMonth($end, 'm') + $addmonth;
            //add month for calculate the last selected day by month
            $monthEndListNr = $monthEndListNr + 1;
            for ($i=$monthEndListNr;$i>12;)
            {
                $i -= 12;
            }
            $monthEndListNr = $i;
            unset($i);
            
            $endMonth = $this->monthList[$monthEndListNr];
            $endYear = $this->getUntilEventYear($end) + $addYear;
            $data = array(); 
            if ($this->checkForExdate($beginYear, $beginMonth, $beginDay)) {
                $startDate = strtotime("last ".$beginDay." ".$beginMonth." ".$beginYear);                
                $startDate = mktime($start[4], $start[5], $start[6], date('m',$startDate), date('d',$startDate) , date('Y',$startDate));

                $endDate = strtotime("last ".$endDay." ".$endMonth." ".$endYear);
                $endDate = mktime($end[4], $end[5], $end[6], date('m',$endDate), date('d',$endDate) + $addToDay , date('Y',$endDate));

                $data['start'] = $startDate * 1000;
                $data['end'] = $endDate * 1000;
            }
            $result[] = $data;            
        } elseif(empty($repeatInterval) || $repeatInterval == '0') {
            // this options are for each day in month (selected by day)
            $weekList = $this->weekDays;
            $result = array();            
            $addDays = 7 * $addmonth;
            $start[3] = $start[3] + $addDays;
            $end[3] = $end[3] + $addDays;
            $beginDay = strtolower($weekList[$day]);
            $beginDayNumber = $this->getUntilEventDay($start);
            $monthListNr = $this->getUntilEventMonth($start, 'm');
            $addYear = $addYear;

            for ($i=$monthListNr;$i>12;)
            {
                $i -= 12;
                $addYear++;
            }
            $beginMonth = $i;
            unset($i);
            $beginYear = $this->getUntilEventYear($start) + $addYear;

            $endDayNumber = $this->getUntilEventDay($end);
            $endDay = strtolower($weekList[$day]);
            $monthEndListNr = $this->getUntilEventMonth($end, 'm');
            for ($i=$monthEndListNr;$i>12;)
            {
                $i -= 12;
            }
            $endMonth = $i;
            unset($i);     
            $endYear = $this->getUntilEventYear($end) + $addYear;
            $data = array();
            // set the next event date for compare with existing exdates for this event
            $setStartDate = strtotime("next ".$beginDay." ".$beginYear.$beginMonth.$beginDayNumber);
            if ($this->checkForExdate(date('Y', $setStartDate), date('m', $setStartDate), date('d', $setStartDate))) {
                $startDate = $setStartDate;
                $startDate = mktime($start[4], $start[5], $start[6], date('m',$startDate), date('d',$startDate) , date('Y',$startDate));

                $endDate = strtotime("next ".$endDay." ".$endYear.$endMonth.$endDayNumber);
                $endDate = mktime($end[4], $end[5], $end[6], date('m',$endDate), date('d',$endDate) + $addToDay, date('Y',$endDate));

                $data['start'] = $startDate * 1000;
                $data['end'] = $endDate * 1000;
            }
            $result[] = $data;
        } else {
            $count = $repeatInterval - 1;
            $weekList = $this->weekDays;
            $result = array();

            $monthListNr = $this->getUntilEventMonth($start, 'm') + $addmonth;
            $addYear = $addYear;
            for ($i=$monthListNr;$i>12;)
            {
                $i -= 12;
                $addYear++;
            }
            $monthListNr = $i;
            unset($i);
            $beginDay = strtolower($weekList[$day]);
            $beginMonth = $this->monthList[$monthListNr];            
            $beginYear = $this->getUntilEventYear($start) + $addYear;
            $endDay = strtolower($weekList[$day]);

            $monthEndListNr = $this->getUntilEventMonth($end, 'm') + $addmonth;
            for ($i=$monthEndListNr;$i>12;)
            {
                $i -= 12;
            }
            $monthEndListNr = $i;
            unset($i);
            $endMonth = $this->monthList[$monthEndListNr];
            
            $endYear = $this->getUntilEventYear($end) + $addYear;
            $data = array();
            $nextStartDate = strtotime("+".$count." week ".$beginDay." ".$beginMonth." ".$beginYear);
            if ($this->checkForExdate(date('Y', $nextStartDate), date('m', $nextStartDate), date('d', $nextStartDate))) {
                $startDate = strtotime("+".$count." week ".$beginDay." ".$beginMonth." ".$beginYear);
                $startDate = mktime($start[4], $start[5], $start[6], date('m',$startDate), date('d',$startDate) , date('Y',$startDate));

                $endDate = strtotime("+".$count." week ".$endDay." ".$endMonth." ".$endYear);                
                $endDate = mktime($end[4], $end[5], $end[6], date('m',$endDate), date('d',$endDate) + $addToDay, date('Y',$endDate));

                $data['start'] = $startDate * 1000;
                $data['end'] = $endDate * 1000;
            }
            $result[] = $data;
        }       
        return $result;
    }


    /**
     * getNextMonthlyDate
     *
     * This function calculate all monthly options for the current event.
     *
     * @access      protected
     * @param       array     $start         event begin date
     * @param       array     $end           event end date
     * @param       array     $days          event interval
     * @param       integer   $month         event month
     * @param       integer   $isInterval    event interval (true = isInterval , false otherweise)
     * @param       integer   $yearToAdd     number of years too add
     * @return      array
     */
    protected function getNextMonthlyDate(array $start, array $end, array $days, $month, $isInterval = false, $yearToAdd = 0)
    {        
        /****************/
        $start = $start;
        $end = $end;
        $days = $days;
        /****************/
        $addToDay = 0;
        if($this->isAllDay()) {
            $addToDay = 1;
        }
        if ($isInterval) {
            $interval = $this->getInterval();
        } else {
            $interval = 1;
        }
        $date = array();
        $addToMonth = $interval * $month;
        $yearToAdd = $yearToAdd;
        $result = array();       
        foreach ($days as $day)
        {
            if (!empty($day)) {
                if ($this->checkForExdate($start[1] + $yearToAdd, $start[2] + $addToMonth, $day)) {
                    $date['start'] = mktime($start[4], $start[5], $start[6], $start[2] + $addToMonth, $day, $start[1] + $yearToAdd);
                    $date['end'] = mktime($end[4], $end[5], $end[6], $end[2] + $addToMonth, $day + $addToDay, $end[1] + $yearToAdd);

                    $date['start'] *= 1000;
                    $date['end'] *= 1000;
                    $result[] = $date;
                }
            }
        }
        return $result;
    }

    /**
     * getSpecialDay
     *
     * This function
     *
     * @access      protected
     * @param       array     $days   days
     * @return      array
     */
    protected function getSpecialDay(array $days)
    {
        if (empty($days)) {
            return false;
        }
        $definedWeekDays = array_flip($this->getWeekDays());
        $result = array();
        foreach ($days as $value)
        {
            $weekDay = preg_split("/[\d{1,2}]+/", $value);
            $weekNr = preg_split("/[a-zA-Z]+/", $value);
            if (!empty($weekNr[0])) {
                $nr = $weekNr[0];
            } else {
                $nr = '';
            }
            if (isset($weekDay[1]) && array_key_exists($weekDay[1], $definedWeekDays)) {
                $day['weekday'] = $definedWeekDays[$weekDay[1]];
                $day['dayRepeatInterval'] = $nr;
            } elseif(!empty($weekDay[0]) && array_key_exists($weekDay[0], $definedWeekDays)) {
                $day['weekday'] = $definedWeekDays[$weekDay[0]];
                //only when selecte mothly each day
                $day['dayRepeatInterval'] = '0';
            }
            if (isset($day)) {
                $result[]= $day;
            }
        }     
        return $result;
    }

    /**
     * getUntilEventDay
     *
     * This function get the until Day of the current event.
     *
     * @access      protected
     * @param       array     $untilDay   event until date
     * @param       string    $format     date format
     * @return      mixed
     */
    public function getUntilEventDay($untilDay, $format = 'd')
    {
        if (empty($untilDay)) {
            return null;
        }
        $valid = mktime($untilDay[4], $untilDay[5], $untilDay[6], $untilDay[2], $untilDay[3] , $untilDay[1]);
        $day = date($format, $valid);
        
        return $day;
    }

    /**
     * getUntilEventMonth
     *
     * This function get the until Month of the current event.
     *
     * @access      protected
     * @param       array     $untilMonth   event until date
     * @param       string    $format     date format
     * @return      mixed
     */
    public function getUntilEventMonth($untilMonth, $format = 'm')
    {
        if (empty($untilMonth)) {
            return null;
        }
        $valid = mktime($untilMonth[4], $untilMonth[5], $untilMonth[6], $untilMonth[2], $untilMonth[3] , $untilMonth[1]);
        $month = date($format, $valid);

        return $month;
    }

    /**
     * getUntilEventYear
     *
     * This function get the until Year of the current event.
     *
     * @access      protected
     * @param       array     $untilYear   event until date
     * @param       string    $format     date format
     * @return      mixed
     */
    public function getUntilEventYear($untilYear, $format = 'Y')
    {
        if (empty($untilYear)) {
            return null;
        }
        $valid = mktime($untilYear[4], $untilYear[5], $untilYear[6], $untilYear[2], $untilYear[3] , $untilYear[1]);
        $year = date($format, $valid);

        return $year;
    }

    /**
     * calculateYearlyDateOptions
     *
     * This function calculate all yearly options for the current event.
     *
     * @access      protected
     * @param       string    $begin       event begin date (timestamp)
     * @param       string    $end         event end date   (timestamp)
     * @param       integer   $interval    event interval
     * @param       integer   $byMonth     event month
     * @param       integer   $byMonthDay  event month day
     * @return      array
     */
    protected function calculateYearlyDateOptions($begin, $end, $interval = 0, $byMonth = 0, $byMonthDay = 0)
    {
        $dateResult = $this->prepairDataset($begin, $end);
        // check if the daily event is allDay
        $this->checkForAllDay($dateResult);
        $start = $dateResult['start'];
        $end = $dateResult['end'];
        $dates = array();
        $result = array();
        if ($this->isUntil()) {
            $until = $dateResult['until'];
            if ($this->isInterval()) {
                $interval = $this->getInterval();
                if ($this->isDay() && $this->isMonth() && !$this->isMonthDay()) {
                    // until + interval + day + month
                    $day = $this->getSpecialDay($this->getDay());
                    $month = $this->getMonth();
                    $specialDays = $this->getSpecialDay($this->getDay());
                    $startYear = $this->getUntilEventYear($start);
                    $endYear = $this->getUntilEventYear($until);
                    $count = ($endYear - $startYear);
                    foreach ($specialDays as $key => $data)
                    {
                        for ($i=0;$i<=$count;$i++)
                        {
                            $dates[] = $this->getSpecialDayResult($start, $end, $data['dayRepeatInterval'], $data['weekday'], 0, $i, false, true);
                        }
                    }
                    // prepair result
                    foreach ($dates as  $dataset)
                    {
                        foreach ($dataset as $key => $dates)
                        if (!empty($dates)) {
                            $result[] =  $dates;
                        }
                    }
                } elseif (!$this->isDay() && $this->isMonthDay() && $this->isMonth()) {
                    // until + interval + month + monthday
                    $monthDay = $this->getMonthDay();
                    if (is_array($monthDay) && isset($monthDay[0])) {
                        $monthDay = (int) $monthDay[0];
                    } else {
                        continue;
                    }
                    $month = $this->getMonth();
                    $result = $this->getSpecialYearResult($dateResult, $month, $monthDay, $this->isInterval(), $this->isCount(), $this->isUntil());
                }
            } else {
                if ($this->isDay() && $this->isMonth() && !$this->isMonthDay()) {
                    // until + day + month
                    $day = $this->getSpecialDay($this->getDay());
                    $month = $this->getMonth();
                    $specialDays = $this->getSpecialDay($this->getDay());
                    $startYear = $this->getUntilEventYear($start);
                    $endYear = $this->getUntilEventYear($until);
                    $count = ($endYear - $startYear);
                    foreach ($specialDays as $key => $data)
                    {
                        for ($i=0;$i<=$count;$i++)
                        {
                            $dates[] = $this->getSpecialDayResult($start, $end, $data['dayRepeatInterval'], $data['weekday'], 0, $i, false, true);
                        }
                    }
                    // prepair result
                    foreach ($dates as  $dataset)
                    {
                        foreach ($dataset as $key => $dates)
                        if (!empty($dates)) {
                            $result[] =  $dates;
                        }
                    }
                } elseif (!$this->isDay() && $this->isMonthDay() && $this->isMonth()) {
                    // until + day + month + monthday
                    $monthDay = $this->getMonthDay();
                    if (is_array($monthDay) && isset($monthDay[0])) {
                        $monthDay = (int) $monthDay[0];
                    } else {
                        continue;
                    }
                    $month = $this->getMonth();
                    $dates = array();
                    $result = $this->getSpecialYearResult($dateResult, $month, $monthDay, $this->isInterval(), $this->isCount(), $this->isUntil());
                }
            }
        } elseif ($this->isCount()) {
            $count = $this->getCount();
            if ($this->isInterval()) {
                $interval = $this->getInterval();
                if ($this->isDay() && $this->isMonth() && !$this->isMonthDay()) {
                    // count + interval + day + month
                    $day = $this->getSpecialDay($this->getDay());
                    $month = $this->getMonth();
                    $specialDays = $this->getSpecialDay($this->getDay());
                    $count = $this->getCount() - 1;
                    foreach ($specialDays as $key => $data)
                    {
                        for ($i=0;$i<=$count;$i++)
                        {
                            $dates[] = $this->getSpecialDayResult($start, $end, $data['dayRepeatInterval'], $data['weekday'], 0, $i, true);
                        }
                    }
                    // prepair result
                    foreach ($dates as  $dataset)
                    {
                        foreach ($dataset as $key => $dates)
                        if (!empty($dates)) {
                            $result[] =  $dates;
                        }
                    }
                } elseif (!$this->isDay() && $this->isMonthDay() && $this->isMonth()) {
                    // count + interval + day + month + monthday
                    $monthDay = $this->getMonthDay();
                    if (is_array($monthDay) && isset($monthDay[0])) {
                        $monthDay = (int) $monthDay[0];
                    } else {
                        continue;
                    }
                    $month = $this->getMonth();
                    $count = $this->getCount() - 1;
                    $dates = array();
                    $result = $this->getSpecialYearResult($dateResult, $month, $monthDay, $this->isInterval(), $this->isCount(), $this->isUntil());
                }
            } else {
                if ($this->isDay() && $this->isMonth() && !$this->isMonthDay()) {
                    // count + day + month
                    $day = $this->getSpecialDay($this->getDay());
                    $month = $this->getMonth();
                    $specialDays = $this->getSpecialDay($this->getDay());
                    $count = $this->getCount() - 1;                    
                    foreach ($specialDays as $key => $data)
                    {
                        for ($i=0;$i<=$count;$i++)
                        {
                            $dates[] = $this->getSpecialDayResult($start, $end, $data['dayRepeatInterval'], $data['weekday'], 0, $i, true);
                        }
                    }
                    
                    // prepair result
                    foreach ($dates as  $dataset)
                    {
                        foreach ($dataset as $key => $dates)
                        if (!empty($dates)) {
                            $result[] =  $dates;
                        }
                    }
                } elseif (!$this->isDay() && $this->isMonthDay() && $this->isMonth()) {
                    // count + day + month + monthday
                    $monthDay = $this->getMonthDay();
                    if (is_array($monthDay) && isset($monthDay[0])) {
                        $monthDay = (int) $monthDay[0];
                    } else {
                        continue;
                    }
                    $month = $this->getMonth();                    
                    $dates = array();
                    $result = $this->getSpecialYearResult($dateResult, $month, $monthDay, $this->isInterval(), $this->isCount(), $this->isUntil());
                } 
            }
        } else {            
                $diff = $this->getEndlessSerialEndDate();
                if ($this->isDay() && $this->isMonth() && !$this->isMonthDay()) {
                    // count + day + month
                    $day = $this->getSpecialDay($this->getDay());
                    $month = $this->getMonth();
                    $specialDays = $this->getSpecialDay($this->getDay());
                    $count = $diff;
                    foreach ($specialDays as $key => $data)
                    {
                        for ($i=0;$i<=$count;$i++)
                        {
                            $dates[] = $this->getSpecialDayResult($start, $end, $data['dayRepeatInterval'], $data['weekday'], 0, $i);
                        }
                    }
                    // prepair result
                    foreach ($dates as  $dataset)
                    {
                        foreach ($dataset as $key => $dates)
                        if (!empty($dates)) {
                            $result[] =  $dates;
                        }
                    }
                } elseif (!$this->isDay() && $this->isMonthDay() && $this->isMonth()) {
                    // count + day + month + monthday
                    $monthDay = $this->getMonthDay();
                    if (is_array($monthDay) && isset($monthDay[0])) {
                        $monthDay = (int) $monthDay[0];
                    } else {
                        continue;
                    }
                    $month = $this->getMonth();
                    $dates = array();
                    $result = $this->getSpecialYearResult($dateResult, $month, $monthDay);
                }
            // left blank
        }
        $result[$this->id] = $result;
        return $result;
    }
    /**
     * getSpecialDayResult
     *
     * This function calculate all monthly options for the current event.
     *
     * @access      protected
     * @param       array     $dateSet               start|end|unil date of an event
     * @param       string    $end                   end date of an event
     * @param       integer   $month                 event month
     * @param       integer   $day                   event month Day
     * @param       bool      $isInterval            (true = isCount, false otherweise)
     * @param       bool      $isCount               (true = isCount, false otherweise)
     * @param       bool      $isUntil               (true = isUntil, false otherweise)
     * @return      array
     */
    protected function getSpecialYearResult($dateSet, $month, $monthDay = 0, $isInterval = false ,$isCount = false, $isUntil = false)
    {
        assert('is_array($dateSet);          // Wrong argument type argument 1. Array expected');
        assert('is_int($month);            // Wrong argument type argument 2. Integer expected');
        assert('is_int($monthDay);         // Wrong argument type argument 3. Integer expected');
        assert('is_bool($isInterval);         // Wrong argument type argument 4. Boolean expected');
        assert('is_bool($isCount);         // Wrong argument type argument 5. Boolean expected');
        assert('is_bool($isUntil);         // Wrong argument type argument 5. Boolean expected');

        $addToDay = 0;
        if($this->isAllDay()) {
            $addToDay = 1;
        }
        $start = $dateSet['start'];
        $end = $dateSet['end'];
        if ($isCount) {
            $count = $this->getCount() - 1;
        }

        if ($isUntil) {
            $startDate = $start[1];
            $until = $dateSet['until'][1];
            $count = $until - $startDate;
        }

        if (!$isCount && !$isUntil) {
            $count = $this->getEndlessSerialEndDate();
        }
        $month = $month;
        $monthDay = $monthDay;
        $interval = 0;
        $result = array();
        for($i=0;$i<=$count;$i++)
        {
            if ($i > 0) {
                if ($isInterval) {
                    $interval = $this->getInterval() * $i;
                } else {
                    $interval = $i;
                }
            }
            $beginDay = $monthDay;
            if (strlen($beginDay) == 1) {
                $beginDay = '0'.$beginDay;
            }
            $beginMonth = $month;
            if (strlen($beginMonth) == 1) {
                $beginMonth = '0'.$beginMonth;
            }
            $beginYear = $this->getUntilEventYear($start) + $interval;

            $endDay = $monthDay;
            if (strlen($endDay) == 1) {
                $endDay = '0'.$endDay;
            }
            $endMonth = $month;
            if (strlen($endMonth) == 1) {
                $endMonth = '0'.$endMonth;
            }
            $endYear = $this->getUntilEventYear($end) + $interval;
            if ($this->checkForExdate($beginYear, $beginMonth, $beginDay)) {
                $startDate = mktime($start[4], $start[5], $start[6], $beginMonth, $beginDay, $beginYear);
                $endDate = mktime($end[4], $end[5], $end[6], $endMonth, $endDay + $addToDay, $endYear);

                $result[$i]['start'] = $startDate * 1000;
                $result[$i]['end'] = $endDate * 1000;
            }
        }
        return $result;
    }

    /**
     * calculateDefaultDate
     *
     * This function calculate the default date for an event if no frequency is set.
     *
     * @access      protected
     * @param       string    $begin       event begin date (timestamp)
     * @param       string    $end         event end date   (timestamp)
     * @return      array
     */
    protected function calculateDefaultDate($begin, $end)
    {
        $dateResult = $this->prepairDataset($begin, $end);
        $start = $dateResult['start'];
        $end = $dateResult['end'];

        $start  = mktime(0, 0, 0, $start[2], $start[3], $start[1]);
        $start *= 1000;
        $end    = mktime(0, 0, 0, $end[2], $end[3], $end[1]);
        $end   *= 1000;

        $date['start'] = $start;
        $date['end'] = $end;
        return $result[$this->id] = $date;
    }
}
?>