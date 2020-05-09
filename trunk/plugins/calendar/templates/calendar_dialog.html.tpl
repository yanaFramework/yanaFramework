<div id="editable_event">
    <form id="dialog_event_form" name="dialog_event_form" method="post" action="$PHP_SELF" enctype="multipart/form-data">
        <!-- Begin label descriptions for input elements -->
        {if $advanced_fields}
            {import file="$advanced_fields"}
        {/if}
        <div class="multicol2">
            <div class="col_left">
                <label for="title">{lang id="calendar_fields.title"}:</label><br />
                <label for="category">{lang id="calendar_fields.category"}:</label><br />
                <label for="location">{lang id="calendar_fields.location"}:</label><br />
                <label>{lang id="calendar_fields.begin"}:</label><br />
                <label>{lang id="calendar_fields.end"}:</label><br />
                <label>{lang id="calendar_fields.allday"}:</label><br />
                <label for="description" class="label_description">{lang id="calendar_fields.description"}:</label><br />
            </div>
            <!-- End label descriptions for input elements -->
            <!-- Begin input elements declaration  -->
            <div class="col_right">
                <div class="event_field">
                    <input type="hidden" name="eventID" id="eventID" value=""/>
                    <input type="text" name="title" id="title" value=""/>
                    <select name="category" id="category">
                        {foreach from=$categories key=key item=item}
                            <option value="{$key}" {if $item.color}class="{$item.color}"{/if}>{$item.name}</option>
                        {/foreach}
                    </select>
                    <input type="text" name="location" id="location" value=""/>
                    <br class="clear_both" />
                    <div id="startDateSelector">
                        {selectDate name="start" id="startDate"}
                        {selectTime name="start" id="startTime"}
                    </div>
                    <br class="clear_both" />
                    <div id="endDateSelector">
                        {selectDate name="end" id="endDate"}
                        {selectTime name="end" id="endTime"}
                    </div>
                    <br class="clear_both"/>
                    <input type="checkbox" name="allDayEvent" id="allDayEvent"/>
                    <br class="clear_both"/>
                    <textarea rows="2" cols="15" name="description" id="description"></textarea>
                </div>
            </div>
            <!-- End input elements declaration  -->
        </div>
        {if $advanced_tab_fields}
            {import file="$advanced_tab_fields"}
        {/if}
        <!-- Begin Advanced options -->
        <div class="advanced_options" id="serie_options">
            <div class="advanced_options_height">
                <!-- Begin Select Frequency -->
                <div class="left">
                    <label for="freq">{lang id="calendar_fields.serial"}:
                        <select name="freq" id="freq">
                            {foreach from=$frequencyOptions item=item key=key}
                                <option value="{$item}">{lang id="calendar_fields.$item"}</option>
                            {/foreach}
                        </select>
                    </label>
                </div>
                <!-- End Select Frequency -->

                <!-- Begin Daily options -->
                <div class="days" id="DAILY">
                    <!-- more options for display days -->
                    <input type="radio" name="dayOption" id="dayInterval" value="byDay" />
                    <span class="left">{lang id="calendar_fields.all_day"}</span>

                    <input type="radio" name="dayOption" id="dayNoInterval" value="byWeekDays" />
                    <span class="left">{lang id="calendar_fields.by_workday"}</span>
                    <br class="clear_both"/>
                    <select name="allDayInterval" id="allDayInterval" class="allDayInterval">
                        {foreach from=$monthNumbers key=key item=item}
                            <option value="{$item}">{$item}</option>
                        {/foreach}
                    </select>
                </div>
                <!-- End Daily options -->

                <!-- Begin Weekly options -->
                <div class="days" id="WEEKLY">
                    {foreach from=$dayOptions item=item key=key}
                            <label class="weekly_days">
                                <input type="checkbox" name="week_days[{$key}]" id="week_days_{$key}" class="week_days" value="{$key}"/>
                                <br />
                                {$item.short}
                            </label>
                    {/foreach}
                </div>
                <!-- End Weekly options -->


                <!-- Begin Monthly options -->
                <div class="monthly" id="MONTHLY">
                    <label>
                        <input type="radio" name="monthly_options" id="monthly_default_opt" value="monthByDay"/>
                    </label>
                    <select name="monthRepeatInterval" id="select_weekInterval" class="monthly_select">
                    {foreach from=$monthRepeatOpt item=item key=key}
                            <option value="{$key}">{$item.name}</option>
                    {/foreach}
                    </select>
                    <select name="monthDayInterval" id="month_weekInterval" class="monthly_select">
                    {foreach from=$dayOptions item=item key=key}
                        <option value="{$key}">{$item.name}</option>
                    {/foreach}
                    </select>
                    <br class="clear_both" />
                    <input type="radio" name="monthly_options" id="monthly_repeat_opt" value="bymonthday"/>
                    <span class="left">{lang id="calendar_fields.recurs_in"}:</span>
                    <br class="clear_both" />
                    <div id="month_repeat_opt_visible">
                        {foreach from=$monthNumbers key=key item=item name="counter"}
                            <span class="month_nr">{$item}</span>
                            <span class="month_nr_box">
                                <input type="checkbox" name="day[{$key}]" id="day_{$key}" class="day_numbers" value="{$key}"/>
                            </span>
                            {if $smarty.foreach.counter.iteration %7 == 0 && $smarty.foreach.counter.iteration != 1}<br class="clear_both"/>{/if}
                        {/foreach}
                    </div>
                </div>
                <!-- End Monthly options -->

                <!-- Begin Year options -->
                <div class="years" id="YEARLY">
                    <input type="radio" name="y_opt" id="y_opt_1" value="yearMonthDay" class="year_option"/>
                    <select name="numbers" id="numbers" class="yearly_select_nr">
                        {foreach from=$monthNumbers key=key item=item}
                            <option value="{$key}">{$item}</option>
                        {/foreach}
                    </select>
                    <select name="month" id="month" class="yearly_select">
                        {foreach from=$monthOptions item=item key=key}
                            <option value="{$item.number}">{$item.name}</option>
                        {/foreach}
                    </select>
                    <br class="clear_both"/>
                    <br class="clear_both"/>
                    <input type="radio" name="y_opt" id="y_opt_2" value="yearMonthDayInterval" class="year_option" />
                    <select name="year_weekInterval" id="year_weekInterval" class="yearly_select">
                        {foreach from=$monthRepeatOpt item=item key=key}
                            <option value="{$key}">{$item.name}</option>
                        {/foreach}
                    </select>
                    <select name="year_day" id="year_day" class="yearly_select">
                        {foreach from=$dayOptions item=item key=key}
                            <option value="{$key}">{$item.name}</option>
                        {/foreach}
                    </select>
                    <select name="year_month" id="year_month" class="yearly_select">
                        {foreach from=$monthOptions item=item key=key}
                            <option value="{$item.number}">{$item.name}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <!-- End Year option -->
            <br class="clear_both" />
            <!-- Begin Standard options -->
            <div id="standard_option">
                <hr />
                <div class="standard_option_position">
                    <input type="radio" name="repeatOpt" id="endlose_serie" value="endlessSerial"/>
                    <span class="left">{lang id="calendar_fields.endless_serial"}</span>
                </div>
                <div class="standard_option_position">
                    <input type="radio" name="repeatOpt" id="counter" value="counter"/>
                    <span class="left">{lang id="calendar_fields.count"}</span>
                </div>
                <div class="standard_option_position">
                    <input type="radio" name="repeatOpt" id="until" value="until"/>
                    <span class="left">{lang id="calendar_fields.until"}</span>
                </div>
                <div id="until_visible">
                    <!-- input type="text" name="until_date" id="until_date" value="until"/ -->
                    {selectDate name="until_date" id="untilDate"}
                    {selectTime name="until_date" id="untilTime"}
                </div>
                <div id="counter_visible">
                    <input type="text" name="count_nr" id="count_nr" value="counter"/>
                </div>
            </div>
            <!-- End Standard options -->
            <br class="clear_both" />
        </div>
        <!-- End Advanced options -->
        {if $request_opt}
            {import file="$request_opt"}
        {/if}
    </form>
    {if $agenda_keys}
        {import file="$agenda_keys"}
    {/if}
</div>
