<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{lang id="PROGRAM_TITLE"}</title>
    </head>
    <body>
        <!--   Begin render calendar   -->
        <div id="calendar">
            {import file="calendar_dialog.html.tpl"}
            <div id="eventToolTip" class="eventToolTip"></div>
        </div>

        <!--   Begin calendar list     -->
        {if $calendarList}
        <div class="calendar_list">
            <div class="header">{lang id="calendar.list"}</div>
            <form id="user_calendar_list" method="post" action="{$PHP_SELF}" enctype="multipart/form-data" accept-charset="UTF-8">
                <input type="hidden" name="action" value="set_calendar_view"/>
                <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
                <input type="hidden" name="id" value="{$ID}"/>
                {foreach from=$calendarList key=key item=item}
                    {if $defaultCalendarID == $key}
                    <div class="calendar_list_label_position">
                        <label>
                            <input type="radio" name="current_calendar" value="{$key}" checked="checked"/>
                            {$item.NAME|truncate:15:"…":true}</label>
                    </div>
                    <div class="calendar_list_action_position">
                        <a class="buttonize calendar_export" href={"action=calendar_download_file&key=$key"|href}>
                            <span class="icon_download">&nbsp;</span>
                        </a>
                    </div>
                    {if $item.SUBSCRIBE == true}
                        <div style="float:right;margin:0 5px 0 5px;">
                            <a class="buttonize calendar_export" href={"action=refresh_calendar_subscribe&key=$key"|href}>
                                <span class="icon_change">&nbsp;</span>
                            </a>
                        </div>
                    {/if}
                    <br class="clear_both" />

                    {else}
                    <div class="calendar_list_label_position">
                        <label>
                            <input type="radio" name="current_calendar" value="{$key}" onclick = 'this.form.submit()'/>
                        {$item.NAME|truncate:15:"…":true} </label>
                    </div>
                    <div class="calendar_list_action_position">
                        <a class="buttonize calendar_export" href={"action=calendar_download_file&key=$key"|href}>
                            <span class="icon_download">&nbsp;</span></a>
                        <a class="buttonize calendar_delete" href={"action=remove_user_calendar&key=$key"|href}>
                            <span class="icon_delete">&nbsp;</span></a>
                        <input type="checkbox" name="calendar_file_id[{$key}]" class="calendar_file_id" value="{$key}"/>
                    </div>
                     <br class="clear_both"/>
                    {/if}
                {/foreach}
            </form>
            {if $userDataset}
                <hr />
                    <div style="padding-left:5px;">
                    {foreach from=$userDataset key=calid item=user}
                        <label>
                            <input type="checkbox" name="calendar_file_id[{$calid}]" class="calendar_file_id" value="{$calid}"/>
                            {$user|truncate:15:"…":true}
                        </label>
                        <br />
                    {/foreach}
                    </div>
                <hr />
            {/if}
        <!-- begin new import container -->
        <ul class="create_options">
            <li id="open_subscribe_calendar">
                <span class="icon_favorites">&nbsp;</span>
                <span class="padding_icon_options">
                    {lang id="calendar.subscribe_calendar"}
                </span>
            </li>
            <li id="open_import_calendar">
                <span class="icon_pointer">&nbsp;</span>
                <span class="padding_icon_options">
                    {lang id="calendar.import_calendar"}
                </span>
            </li>
            <li id="open_create_calendar">
                <span class="icon_new">&nbsp;</span>
                <span class="padding_icon_options">
                    {lang id="calendar.create_calendar"}
                </span>
            </li>
        </ul>
        </div>
        {/if}
        <!--   End calendar list       -->
        <!--   End render calendar     -->

        <!--   Begin import formular   -->
        <div class="clear_both">
            <form style="display:none;" id="subscriptions" method="post" action="{$PHP_SELF}" enctype="multipart/form-data" accept-charset="UTF-8">
                <input type="hidden" name="action" value="subscribe_calendar"/>
                <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
                <input type="hidden" name="id" value="{$ID}"/>
                <label>URL
                    <input type="text" size="42" name="new_calendar_abo" id="new_calendar_abo" />
                </label>
                <input type="submit" name="button" value="{lang id='ok'}" id="import_refresh"/>
            </form>
            <form style="display:none;" id="import_new_calendar" method="post" action="{$PHP_SELF}" enctype="multipart/form-data" accept-charset="UTF-8">
                <input type="hidden" name="action" value="set_xcal"/>
                <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
                <input type="hidden" name="id" value="{$ID}"/>
                <p class="calendar_title_text">{lang id="HELP.NEW_CALENDAR_DESCRIPTION"}</p>
                <div class="calender_import">
                    <label>{lang id="calendar.upload_name"}
                        <input type="text" size="20" name="calendar_name" id ="calendar_name" />
                    </label>
                    <input class="calendar_input" type="file" size="10" name="file" accept="text/*" />
                    <input type="submit" name="button" value='{lang id="ok"}' id="new_calenda_save"/>
                </div>
            </form>
            {*if no calendar files exist create a new one*}
            <form style="display:none;" class="calendar_list" id="new_calendar_create" method="post" action="{$PHP_SELF}" enctype="multipart/form-data" accept-charset="UTF-8">
                <input type="hidden" name="action" value="new_calendar"/>
                <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
                <input type="hidden" name="id" value="{$ID}"/>
                <div>
                    <label>{lang id="calendar.upload_name"}
                        <input type="text" size="15" name="new_calendar_name" id ="new_calendar_name" />
                    </label>
                    <input type="submit" name="button" value='{lang id="ok"}' id="create_calendar"/>
                </div>
            </form>
        </div>
        <!--   End import formular   -->
        <!-- BEGIN calendar js-->
        <script type='text/javascript'>
        <!--
{literal}
                $(document).ready(function() {
                        var date = new Date();
                        var d = date.getDate();
                        var m = date.getMonth();
                        var y = date.getFullYear();
                        var isShow = true;
                        var agendaEditCounter = 0;
                        // validate function for check if the required fileds are not empty
                        function validateEvent() {
                            var title = $("#title").val();
                            var location = $("#location").val();
                            var result = false;
                            if (title == '') {
                                var category = $('#category').val();
                                $('#title').val(category);
                            }
                            return result;
                        };
                        // Dialog to create a new appointment
                        function addDialogOpen(dayDate, allDay, jsEvent, view ) {
                            $('#editable_event').dialog( 'open' );
                            $("div#editable_event").dialog('option', 'title', '{lang id="calendar_fields.new_appointment"}');
                            $("div#editable_event").dialog('option', 'buttons',
                            {
                                "{lang id='button_abort'}": function()
                                {
                                    $(this).dialog("close");
                                },
                                '{lang id="calendar_buttons.save"}': function()
                                {
                                    $check = validateEvent();
                                    if ($check == false) {
                                        saveEvent();
                                        $(this).dialog("close");
                                    }
                                }
                            });
                            clearForm($('#dialog_event_form'));
                            $('#category').removeClass();
                            $("#standard_option").css({"display":"none"});

                            // set date
                            var da = dayDate.getDate() < 10 ? "0" + String( dayDate.getDate() ) : String( dayDate.getDate() );
                            var mo = dayDate.getMonth() < 10 ? "0" + String( dayDate.getMonth() + 1 ) : String( dayDate.getMonth() + 1 );
                            var ye = String( dayDate.getFullYear() );

                            $("#startDate_year").val(Math.ceil(ye));
                            $("#startDate_month").val(Math.ceil(mo));
                            $("#startDate_day").val(Math.ceil(da));

                            $("#endDate_year").val(Math.ceil(ye));
                            $("#endDate_month").val(Math.ceil(mo));
                            $("#endDate_day").val(Math.ceil(da));

                            $("#untilDate_year").val(ye);
                            $("#untilDate_month").val(mo);
                            $("#untilDate_day").val(da);

                            if( view.name == 'agendaDay' || view.name == 'agendaWeek' ) {
                                var ho = dayDate.getHours() < 10 ? "0" + String( dayDate.getHours() ) : String( dayDate.getHours() );
                                var mi = dayDate.getMinutes() < 10 ? "0" + String( dayDate.getMinutes() ) : String( dayDate.getMinutes() );
                                var se = dayDate.getSeconds() < 10 ? "0" + String( dayDate.getSeconds() ) : String( dayDate.getSeconds() );

                                $("#startTime_hour").val(ho);
                                $("#startTime_minute").val(Math.ceil(mi));
                                $("#endTime_hour").val(Math.ceil(ho) + 1);
                                $("#endTime_minute").val(Math.ceil(mi));
                                $("#untilTime_hour").val(Math.ceil(ho));
                                $("#untilTime_minute").val(Math.ceil(mi));
                            } else {
                                $("#startTime_hour").val(['8']);
                                $("#startTime_minute").val(['0']);
                                $("#endTime_hour").val(['17']);
                                $("#endTime_minute").val(['0']);
                                $("#untilTime_hour").val(['17']);
                                $("#untilTime_minute").val(['0']);
                            }
                            // if frequency is selected set counter as visible (default number 1) -  as default option
                            $("#counter:radio").val(["counter"]);
                            $("#count_nr").val(1);
                            $("#count_nr, #counter_visible").show();
                            $("#until_visible").hide();
                            // unlock time fields
                            $('#startTime_hour, #startTime_minute, #endTime_hour, #endTime_minute').attr('disabled', false);
                            // set until 
                            $("#untilDate_year").val(Math.ceil(ye));
                            $("#untilDate_month").val(Math.ceil(mo));
                            $("#untilDate_day").val(Math.ceil(da));
                        };
                        // Open a new window for download - export the current event
                        function openNewPopup (eventID) {
                            action = 'action=calendar_send_event';
                            id = 'id=' + window.yanaProfileId;
                            session = window.yanaSessionName + '=' + window.yanaSessionId;
                            event = eventID;
                            url = 'index.php?' + session + '&' + id + '&' + action + '&' + 'eventid=' + event;
                            newWindow = window.open(url, "sendWindow", "width=500,height=100,scrollbars=no");
                            newWindow.focus();
                        };

                        // this function clear dialog form elements
                        function clearForm(form) {
                            $(':input', form).each(function() {
                                var type = this.type;
                                var tag = this.tagName.toLowerCase(); // normalize case
                                if (type == 'text' || type == 'password' || tag == 'textarea') {
                                    this.value = "";
                                } else if (type == 'checkbox' || type == 'radio') {
                                    this.checked = false;
                                } else if (tag == 'select') {
                                    this.selectedIndex = 0;
                                }
                                $("#eventID").val([""]);
                            });
                            if ($('#freq :selected').val() == 'NONE') {
                                   $("#DAILY, #WEEKLY, #MONTHLY, #YEARLY").hide();
                            };

                            if ($('#freq :selected').val() == 'DAILY') {
                                $("#DAILY").show();
                                $("#dayInterval").val(["byDay"]);
                                $("#endlose_serie").val(["endlessSerial"]);
                            };
                        };
                        // this function cancle the dialog
                        function cancleEventDialog() {
                            $(this).dialog("close");
                        };
                        // this function save the dialog entry
                        function saveEvent() {
                            args = $('#dialog_event_form').serializeArray();
                            //var userID = $('#newAppointmentForUser :selected').val();
                            // $("input:radio:checked[name='current_calendar']").val() + ',';
                            var defaultID = '';
                            var userDefault = false;
                            var userID = '';
                            $('#newAppointmentForUser option:selected').each( function() {
                                if ($(this).val() != 0) {
                                    userID += $(this).val() + ',';
                                } else {
                                    defaultID = $("input:radio:checked[name='current_calendar']").val();
                                    userDefault = true;
                                }
                            });
                            if (userID == '') {
                                userDefault = true;
                            }
                            var requestParams = {
                                is_ajax_request: true,
                                action: 'set_calendar_event_save',
                                event: args,
                                user_id: userID,
                                current_user_calendar_id: defaultID,
                                insert_for_default: userDefault,
                                id: window.yanaProfileId
                            };
                            requestParams[window.yanaSessionName] = window.yanaSessionId;
                            $.post(
                                php_self,
                                requestParams,
                                function(data)
                                {
                                    $('#calendar').fullCalendar('refetchEvents');
                                },
                                "json"
                            );
                        };
                        // this function send the event
                        function sendEvent(form) {
                            eventID = $('#eventID').val();
                            openNewPopup(eventID);
                        };
                        // this function remove the event
                        function removeEvent() {
                            eventID = $('#eventID').val();
                            var requestParams = {
                                is_ajax_request: true,
                                action: 'remove_calendar_event',
                                eventID: eventID,
                                id: window.yanaProfileId
                            };
                            requestParams[window.yanaSessionName] = window.yanaSessionId;
                            $.post(
                                php_self,
                                requestParams,
                                function(data)
                                {
                                    $('#calendar').fullCalendar('refetchEvents');
                                },
                                "json"
                            );
                            $(this).dialog("close");
                        };
                        // this function remove an serial element
                        function removeSerialEvent() {
                            args = $('#dialog_event_form').serializeArray();
                            var requestParams = {
                                is_ajax_request: true,
                                action: 'calendar_delete_serial_entry',
                                event : args,
                                id: window.yanaProfileId
                            };
                            requestParams[window.yanaSessionName] = window.yanaSessionId;
                            $.post(
                                php_self,
                                requestParams,
                                function()
                                {
                                    $('#calendar').fullCalendar('refetchEvents');
                                },
                                "json"
                            );
                        };
                        // function for edit an event or create a new one
                        function editForm(calEvent) {
                            clearForm($('#dialog_event_form'));
                            if (calEvent.readonly == true) {
                                $('#calendar').fullCalendar('refetchEvents');
                            } else if (calEvent.frequency == '') {
                                $("div#editable_event").dialog('open');
                                $("div#editable_event").dialog('option', 'title', '{lang id="calendar_fields.edit_appointment"}');
                                $("div#editable_event").dialog('option', 'buttons',
                                {
                                    "{lang id='button_abort'}": function()
                                    {
                                        $(this).dialog("close");
                                    },
                                    "{lang id='button_delete_one'}": function()
                                    {
                                        removeEvent();
                                        $(this).dialog("close");
                                    },
                                    '{lang id="button_send"}': function()
                                    {
                                        sendEvent();
                                        $(this).dialog("close");
                                    },
                                    '{lang id="calendar_buttons.save"}': function()
                                    {
                                        $check = validateEvent();
                                        if ($check == false) {
                                            saveEvent();
                                            $(this).dialog("close");
                                        }
                                    }
                                });
                            } else {
                                $("div#editable_event").dialog('open');
                                $("div#editable_event").dialog('option', 'title', '{lang id="calendar_fields.edit_appointment"}');
                                $("div#editable_event").dialog('option', 'buttons',
                                {
                                    "{lang id='button_abort'}": function()
                                    {
                                        $(this).dialog("close");
                                    },
                                    '{lang id="button_delete_one"}': function()
                                    {
                                        removeEvent();
                                        $(this).dialog("close");
                                    },
                                    '{lang id="calendar_buttons.remove_serial_element"}': function()
                                    {
                                        removeSerialEvent();
                                        $(this).dialog("close");
                                    },
                                    '{lang id="button_send"}': function()
                                    {
                                        sendEvent();
                                        $(this).dialog("close");
                                    },
                                    '{lang id="calendar_buttons.save"}': function()
                                    {
                                        $check = validateEvent();
                                        if ($check == false) {
                                            saveEvent();
                                            $(this).dialog("close");
                                        }
                                    }
                                });
                            }
                            // set current event values
                            $("#eventID").val(calEvent.id);
                            $("#title").val(calEvent.title);
                            $("#location").val(calEvent.location);
                            $("#category").val(calEvent.categories);
                            $("#description").val(calEvent.description);
                            $("#freq").val(calEvent.frequency);
                            $("#count_nr").val(["0"]);
                            $("#standard_option").css({"display":"block"});
                            if (calEvent.frequency == 'DAILY') {
                                $("#DAILY").css({"display":"block"});
                                $("#WEEKLY").css({"display":"none"});
                                $("#MONTHLY").css({"display":"none"});
                                $("#YEARLY").css({"display":"none"});
                                if (calEvent.workDays == false) {
                                    $("#dayInterval").val(["byDay"]);
                                    $("#allDayInterval").val(calEvent.interval);
                                    $("#allDayInterval").attr('disabled', false);
                                }
                                if (calEvent.workDays == true) {
                                    $("#dayNoInterval").val(["byWeekDays"]);
                                    $("#dayNoInterval").attr('disabled', false);
                                    $("#allDayInterval").attr('disabled', true);
                                }
                                if (calEvent.endlessSerial == true) {
                                    $('#endlose_serie').val(["endlessSerial"]);
                                }
                            }

                            if ($('#freq :selected').val() == 'NONE') {
                                   $("#DAILY").css({"display":"none"});
                                   $("#WEEKLY").css({"display":"none"});
                                   $("#MONTHLY").css({"display":"none"});
                                   $("#YEARLY").css({"display":"none"});
                                   $("#standard_option").css({"display":"none"});
                            };

                            if (calEvent.frequency == 'WEEKLY') {
                                $("#WEEKLY").css({"display":"block"});
                                $("#DAILY").css({"display":"none"});
                                $("#MONTHLY").css({"display":"none"});
                                $("#YEARLY").css({"display":"none"});

                                var sun = $('#week_days_0').attr('checked');
                                if (sun == false) {
                                    if (calEvent.su == 0) {
                                        $("#week_days_0:checkbox").val(["0"]);
                                    }
                                } else {
                                    if (calEvent.su == null) {
                                        $("#week_days_0").val(["0"]);
                                    }
                                }

                                var mon = $('#week_days_1').attr('checked');
                                if (mon == false) {
                                    if (calEvent.mo == 1) {
                                        $("#week_days_1").val(["1"]);
                                    }
                                } else {
                                    if (calEvent.mo == null) {
                                        $("#week_days_1").val(["0"]);
                                    }
                                }

                                var tue = $('#week_days_2').attr('checked');
                                if (tue == false) {
                                    if (calEvent.tu == 2) {
                                        $("#week_days_2").val(["2"]);
                                    }
                                } else {
                                    if (calEvent.tu == null) {
                                        $("#week_days_2").val(["1"]);
                                    }
                                }

                                var wed = $('#week_days_3').attr('checked');
                                if (wed == false) {
                                    if (calEvent.we == 3) {
                                        $("#week_days_3").val(["3"]);
                                    }
                                } else {
                                    if (calEvent.we == null) {
                                        $("#week_days_3").val(["1"]);
                                    }
                                }

                                var thu = $('#week_days_4').attr('checked');
                                if (thu == false) {
                                    if (calEvent.th == 4) {
                                        $("#week_days_4").val(["4"]);
                                    }
                                } else {
                                    if (calEvent.th == null) {
                                        $("#week_days_4").val(["1"]);
                                    }
                                }

                                var fri = $('#week_days_5').attr('checked');
                                if (fri == false) {
                                    if (calEvent.fr == 5) {
                                        $("#week_days_5").val(["5"]);
                                    }
                                } else {
                                    if (calEvent.fr == null) {
                                        $("#week_days_5").val(["1"]);
                                    }
                                }

                                var sat = $('#week_days_6').attr('checked');
                                if (sat == false) {
                                    if (calEvent.sa == 6) {
                                        $("#week_days_6").val(["6"]);
                                    }
                                } else {
                                    if (calEvent.sa == null) {
                                        $("#week_days_6").val(["1"]);
                                    }
                                }

                                if(calEvent.endlessSerial == true) {
                                    $('#endlose_serie').val(["endlessSerial"]);
                                }
                            }
                            if ( calEvent.frequency == 'MONTHLY') {
                                $("#MONTHLY").css({"display":"block"});
                                $("#WEEKLY, #DAILY, #YEARLY").css({"display":"none"});
                                if (calEvent.workDays == false) {
                                    $("#monthly_repeat_opt:radio").val(["bymonthday"]);
                                    $("#month_repeat_opt_visible").css({"display":"block"});
                                    $("#select_weekInterval, #month_weekInterval").attr('disabled', true);
                                    $.each(calEvent.monthdays, function(index, value) {
                                        var dayID = '#day_' + index;
                                        if (value == true) {
                                            $(dayID).attr('checked', true);
                                        }
                                        if (value == false) {
                                            $(dayID).attr('checked', false);
                                        }
                                    });
                                }
                                if (calEvent.workDays == true) {
                                    $("#monthly_default_opt:radio").val(["monthByDay"]);
                                    $("#select_weekInterval, #month_weekInterval").attr('disabled', false);
                                    $("#month_repeat_opt_visible").css({"display":"none"});
                                    $("#select_weekInterval").val(calEvent.repeatPosition);
                                    $("#month_weekInterval").val(calEvent.monthEachWeekDay);
                                }
                                if(calEvent.endlessSerial == true) {
                                    $('#endlose_serie').val(["endlessSerial"]);
                                }
                            }
                            if (calEvent.frequency == 'YEARLY') {
                                $("#YEARLY").css({"display":"block"});
                                $("#WEEKLY, #MONTHLY, #DAILY").css({"display":"none"});
                                if (calEvent.workDays == false) {
                                    $("#y_opt_1").val(["yearMonthDay"]);
                                    $("#year_weekInterval, #year_day, #year_month").attr('disabled', true);
                                    $("#numbers, #month").attr('disabled', false);
                                    $.each(calEvent.monthdays, function(index, value) {
                                        if (value == true) {
                                            $("#numbers").val(index);
                                        }
                                    });
                                    $("#month").val(calEvent.month);
                                }
                                if (calEvent.workDays == true) {
                                    $("#y_opt_2").val(["yearMonthDayInterval"]);
                                    $("#year_weekInterval, #year_day, #year_month").attr('disabled', false);
                                    $("#numbers, #month").attr('disabled', true);
                                    $("#year_weekInterval").val(calEvent.repeatPosition);
                                    $("#year_day").val(calEvent.monthEachWeekDay);
                                    $("#year_month").val(calEvent.month);
                                }
                                if(calEvent.endlessSerial == true) {
                                    $('#endlose_serie').val(["endlessSerial"]);
                                }
                            }
                            // disable counter
                            $("#counter_visible").css({"display":"none"});
                            if(calEvent.count != 0 && calEvent.count != null) {
                                $("#counter:radio").val(["counter"]);
                                $("#count_nr").val(calEvent.count);
                                $("#counter_visible").css({"display":"block"});
                                $("#until_visible").css({"display":"none"});
                            }
                            $("#endTime_minute").val(["00"]);
                            $("#startTime_minute").val(["00"]);

                            // set event date
                            syear  = $.fullCalendar.formatDate( calEvent.start, 'yyyy' );
                            smonth = $.fullCalendar.formatDate( calEvent.start, 'MM' );
                            sday   = $.fullCalendar.formatDate( calEvent.start, 'dd' );
                            shour  = $.fullCalendar.formatDate( calEvent.start, 'HH' );
                            smin   = $.fullCalendar.formatDate( calEvent.start, 'mm' );
                            $("#startDate_year").val(syear);
                            $("#startDate_month").val(Math.ceil(smonth));
                            $("#startDate_day").val(Math.ceil(sday));
                            $("#startTime_hour").val(Math.ceil(shour));
                            $("#startTime_minute").val(Math.ceil(smin));

                            eyear  = $.fullCalendar.formatDate( calEvent.end, 'yyyy' );
                            emonth = $.fullCalendar.formatDate( calEvent.end, 'MM' );
                            eday   = $.fullCalendar.formatDate( calEvent.end, 'dd' );
                            ehour  = $.fullCalendar.formatDate( calEvent.end, 'HH' );
                            emin   = $.fullCalendar.formatDate( calEvent.end, 'mm' );
                            $("#endDate_year").val(eyear);
                            $("#endDate_month").val(Math.ceil(emonth));
                            $("#endDate_day").val(Math.ceil(eday));
                            $("#endTime_hour").val(Math.ceil(ehour));
                            $("#endTime_minute").val(Math.ceil(emin));

                            // disable until
                            $("#until_visible").css({"display":"none"});
                            if (calEvent.until != null) {
                                $("#until:radio").val(["until"]);
                                $("#untilDate_year").val(calEvent.until.year);
                                $("#untilDate_month").val(Math.ceil(calEvent.until.month));
                                $("#untilDate_day").val(Math.ceil(calEvent.until.day));
                                $("#untilTime_hour").val(Math.ceil(calEvent.until.hour));
                                $("#untilTime_minute").val(Math.ceil(calEvent.until.minutes));
                                $("#counter_visible").css({"display":"none"});
                                $("#until_visible").css({"display":"block"});
                            } else {
                                $("#untilDate_year").val(eyear);
                                $("#untilDate_month").val(Math.ceil(emonth));
                                $("#untilDate_day").val(Math.ceil(eday));
                                $("#untilTime_hour").val(Math.ceil(ehour));
                                $("#untilTime_minute").val(Math.ceil(emin));
                            }

                            // additional cc options
                            if (calEvent.memo) {
                                $("#memo").val(calEvent.memo);
                            } else {
                                $("#memo").val([""]);
                            }
                            if (calEvent.agenda) {
                                $("#agenda").val(calEvent.agenda);
                            } else {
                                $("#agenda").val([""]);
                            }
                            // set class for category
                            $('#category').removeClass();
                            $('#category').addClass('selected_' + calEvent.className);
                            // check if all day is set
                            var showTime = false;
                            if (calEvent.allDay == true) {
                                $('#allDayEvent').attr('checked', true);
                                $('#startTime_hour').val(['00']);
                                $('#startTime_minute').val(['00']);
                                $('#endTime_hour').val(['00']);
                                $('#endTime_minute').val(['00']);
                                showTime = true;
                            } else {
                                showTime = false;
                            }
                            $('#startTime_hour, #startTime_minute, #endTime_hour, #endTime_minute').attr('disabled', showTime);

                        };
                        
                        
                        // calendar render function
                        $('#calendar').fullCalendar({
                            eventClick: function(calEvent, jsEvent, view) {
                                editForm(calEvent, jsEvent, view);
                            },
                            header: {
                                left: 'prev,next today',
                                center: 'title',
                                right: 'month,agendaWeek,agendaDay'
                            },
                            titleFormat: {
                                day: "dddd, dd. MMM yyyy",
                                week: "dddd, dd. MMM yyyy"
                            },
                            monthNames: [ '{lang id="month_names.january"}', '{lang id="month_names.february"}', '{lang id="month_names.march"}', '{lang id="month_names.april"}', '{lang id="month_names.may"}', '{lang id="month_names.june"}', '{lang id="month_names.july"}', '{lang id="month_names.august"}', '{lang id="month_names.september"}', '{lang id="month_names.october"}', '{lang id="month_names.november"}', '{lang id="month_names.december"}' ],
                            monthNamesShort: [ '{lang id="short_month_names.january"}', '{lang id="short_month_names.february"}', '{lang id="short_month_names.march"}', '{lang id="short_month_names.april"}', '{lang id="short_month_names.may"}', '{lang id="short_month_names.june"}', '{lang id="short_month_names.july"}', '{lang id="short_month_names.august"}', '{lang id="short_month_names.september"}', '{lang id="short_month_names.october"}', '{lang id="short_month_names.november"}', '{lang id="short_month_names.december"}' ],
                            dayNames: [ '{lang id="day_names.sunday"}', '{lang id="day_names.monday"}', '{lang id="day_names.tuesday"}', '{lang id="day_names.wednesday"}', '{lang id="day_names.thursday"}', '{lang id="day_names.friday"}', '{lang id="day_names.saturday"}' ],
                            dayNamesShort: [ '{lang id="short_day_names.sunday"}', '{lang id="short_day_names.monday"}', '{lang id="short_day_names.tuesday"}', '{lang id="short_day_names.wednesday"}', '{lang id="short_day_names.thursday"}', '{lang id="short_day_names.friday"}', '{lang id="short_day_names.saturday"}' ],
                            columnFormat: {
                                day: "dddd dd.MM.",
                                week: "ddd dd."
                            },
                            buttonText: {
                                today: '{lang id="calendar.today"}',
                                month: '{lang id="calendar.month"}',
                                week: '{lang id="calendar.week"}',
                                day: '{lang id="calendar.day"}'
                            },
                            minTime: '00:00',
                            maxTime: '24:00',
                            axisFormat: 'HH:mm',
                            allDayText: '{lang id="calendar.all_day"}',
                            timeFormat: 'H:mm',
                            editable: true,
                            lazyFetching: false,
                            theme: true,
                            firstDay: 1,
                            // load events
                            events: function(start, end, callback) {
                                var newCalendarID = '';
                                $('.calendar_file_id').each( function( index, value ) {
                                    if ($(this).attr('checked')) {
                                        newCalendarID += $(this).val() + ',';
                                    }
                                });
                                var defaultID = $("input:radio:checked[name='current_calendar']").val();
                                var requestParams = {
                                    is_ajax_request: true,
                                    action: 'display_calendar',
                                    start: start.getTime() / 1000,
                                    end: end.getTime() / 1000,
                                    calendar_id: newCalendarID,
                                    current_calendar_id: defaultID,
                                    id: window.yanaProfileId
                                };
                                requestParams[window.yanaSessionName] = window.yanaSessionId;
                                var events = new Array();
                                $.post(
                                    php_self,
                                    requestParams,
                                    function(result)
                                    {
                                        $.each(result, function() {
                                            if (typeof this.id != 'undefined') {
                                                events.push({
                                                    id: this.id,
                                                    title: this.title,
                                                    start: this.start,
                                                    end: this.end,
                                                    location: this.location,
                                                    categories: this.categories,
                                                    description: this.description,
                                                    frequency: this.frequency,
                                                    interval: this.interval,
                                                    count: this.count,
                                                    until: this.until,
                                                    workDays: this.workDays,
                                                    className : this.className,
                                                    su: this.SU,
                                                    mo: this.MO,
                                                    tu: this.TU,
                                                    we: this.WE,
                                                    th: this.TH,
                                                    fr: this.FR,
                                                    sa: this.SA,
                                                    monthdays: this.monthdays,
                                                    monthEachWeekDay: this.monthEachWeekDay,
                                                    month: this.month,
                                                    repeatPosition: this.repeat_position,
                                                    endlessSerial: this.endlessSerial,
                                                    readonly: this.readonly,
                                                    createdBy: this.created_by
                                                });
                                            }
                                        });
                                        callback(events);
                                    },
                                    "json"
                                );
                            },
                            // create new event
                            dayClick: addDialogOpen,
                            // save event by Dragging
                            eventDrop: function(event, dayDelta, minuteDelta) {
                                if (event.readonly == true) {
                                    $('#calendar').fullCalendar('refetchEvents');
                                }
                                var endDate = 0;
                                endDate = (event.end.getTime() / 1000);
                                eventID = event.id;
                                if (event.frequency == '') {
                                    var requestParams = {
                                        is_ajax_request: true,
                                        action: 'update_event_by_drop',
                                        eventID: eventID,
                                        end: endDate,
                                        resize: dayDelta,
                                        min: minuteDelta,
                                        id: window.yanaProfileId
                                    };
                                    requestParams[window.yanaSessionName] = window.yanaSessionId;
                                    $.post(
                                        php_self,
                                        requestParams,
                                        function()
                                        {
                                            $('#calendar').fullCalendar('refetchEvents');
                                        },
                                        "json"
                                    );
                                } else {
                                    $('#calendar').fullCalendar('refetchEvents');
                                }

                            },
                            // this function is checking if the event has an serial (by existing serial a edit dialog will be open)
                            eventDragStart: function(event, jsEvent, ui, view) {
                                if (event.readonly == true) {
                                    $('#calendar').fullCalendar('refetchEvents');
                                }
                                if (event.frequency != '') {
                                    editForm(event);
                                }
                            },
                            // this function is checking if the event has an serial (by existing serial a edit dialog will be open)
                            eventResizeStart: function(event, jsEvent, ui, view) {
                                if (event.readonly == true) {
                                    $('#calendar').fullCalendar('refetchEvents');
                                }
                                if (event.frequency != '') {
                                    editForm(event);
                                }
                            },
                            // save event by Resizing
                            eventResize: function( event, dayDelta, minuteDelta ) {
                                if (event.readonly == true) {
                                    $('#calendar').fullCalendar('refetchEvents');
                                }
                                var endDate = 0;
                                dayDelta = dayDelta;
                                minuteDelta = minuteDelta;
                                endDate = (event.end.getTime() / 1000);
                                eventID = event.id;
                                if (event.frequency == '') {
                                    var requestParams = {
                                        is_ajax_request: true,
                                        action: 'update_event_by_resize',
                                        eventID: eventID,
                                        end: endDate,
                                        resize: dayDelta,
                                        min: minuteDelta,
                                        id: window.yanaProfileId
                                    };
                                    requestParams[window.yanaSessionName] = window.yanaSessionId;
                                    $.post(
                                        php_self,
                                        requestParams,
                                        function(data)
                                        {
                                            $('#calendar').fullCalendar('refetchEvents');
                                        },
                                        "json"
                                    );
                                } else {
                                    $('#calendar').fullCalendar('refetchEvents');
                                }
                            },

                            // Tooltip einblenden
                            eventMouseover : function( calEvent, jsEvent, view ) {
                               var newToolTip = '';
                               eventdate = '';
                               // set the start and end date of the current event
                               if(calEvent.allDay == true) {
                                   eventdate = $.fullCalendar.formatDate( calEvent.start, 'dd.MM.yy' ) +
                                   ' - ' + $.fullCalendar.formatDate( calEvent.end, 'dd.MM.yy' );
                               } else {
                                   eventdate = $.fullCalendar.formatDate( calEvent.start, 'dd.MM.yy HH:mm' ) +
                                   ' - ' + $.fullCalendar.formatDate( calEvent.end, 'dd.MM.yy HH:mm' );
                               }

                               newToolTip += '<table><tr><td colspan="2" class="headline">'+ calEvent.title + '<\/td><\/tr>';
                               newToolTip += '<tr><td>{lang id="calendar_fields.title"}:<\/td><td>' + calEvent.title + '<\/td><\/tr>';
                               newToolTip += '<tr><td>{lang id="calendar_fields.location"}:<\/td><td>' + calEvent.location + '<\/td><\/tr>';
                               newToolTip += '<tr><td>{lang id="calendar_fields.date"}:<\/td><td>' + eventdate + '<\/td><\/tr>';
                               newToolTip += '<tr><td>{lang id="calendar_fields.content"}:<\/td><td>' + calEvent.description + '<\/td><\/tr>';
                               newToolTip += '<tr><td>{lang id="calendar_fields.created_by"}:<\/td><td>' + calEvent.createdBy + '<\/td><\/tr>';

                                $('#eventToolTip').html( newToolTip ).css({
                                    'top' : jsEvent.pageY + 15,
                                    'left' : jsEvent.pageX + 15
                                }).show();

                                window.setTimeout( '$("#eventToolTip").fadeOut("slow")', 10000 );
                            },

                            // Tooltip ausblenden, wenn außerhalb von Termin
                            eventMouseout : function( calEvent, jsEvent, view ) {
                                $('#eventToolTip').hide();
                            }
                        });
                        $("#editable_event").dialog(
                           {
                                modal: true,
                                autoOpen: false,
                                width: 560,
                                show: 'slide',
                                resizable: false
                            }
                        );
                        $(".calendar_file_id").click(function () {
                            $('#calendar').fullCalendar('refetchEvents');
                        });
                        $("#monthly_default_opt").click(function () {
                            $("#month_repeat_opt_visible").hide();
                            $("#select_weekInterval, #month_weekInterval").attr('disabled', false);
                        });
                        $("#monthly_repeat_opt").click(function () {
                            $("#month_repeat_opt_visible").show();
                            $("#select_weekInterval, #month_weekInterval").attr('disabled', true);
                        });
                        $("#dayInterval").click(function () {
                            $("#allDayInterval").attr('disabled', false);
                        });
                        $("#dayNoInterval").click(function () {
                            $("#allDayInterval").attr('disabled', true);
                        });
                        var dialogOptions = {
                            autoOpen     : true,
                            modal        : true,
                            minHeight    : 80,
                            maxHeight    : 800,
                            resizable    : false,
                            draggable    : true
                        };
                        $("#open_import_calendar").click(function () {
                            dialogOptions.title = '{lang id="calendar.import_calendar"}';
                            dialogOptions.width = 500;
                            $('#import_new_calendar').dialog(dialogOptions);
                        });
                        $("#new_calendar_create").submit(function () {
                            var value = $("#new_calendar_name").val();
                            var result = value.length > 2;
                            if (!result) {
                                alert('{lang id="calendar.messagename"}');
                                $("#new_calendar_name").focus();
                            }
                            return result;
                        });
                        dialogOptions.minHeight = 60;
                        dialogOptions.width = 400;
                        $("#open_create_calendar").click(function () {
                            dialogOptions.title = '{lang id="calendar.create_calendar"}';
                            $('#new_calendar_create').dialog(dialogOptions);
                        });
                        $("#open_subscribe_calendar").click(function () {
                            dialogOptions.title = '{lang id="calendar.subscribe_calendar"}';
                            $('#subscriptions').dialog(dialogOptions);
                        });
                        $("#endlose_serie").click(function () {
                            $("#counter_visible").hide();
                            $("#until_visible").hide();
                        });
                        $("#counter").click(function () {
                            $("#counter_visible").show();
                            $("#until_visible").hide();
                        });
                        $("#until").click(function () {
                            $("#counter_visible").hide();
                            $("#until_visible").show();
                        });
                        $("#y_opt_1").click(function () {
                            $('#year_weekInterval, #year_day, #year_month').attr('disabled', true);
                            $('#numbers,#month').attr('disabled', false);
                        });
                        $("#y_opt_2").click(function () {
                            $('#numbers, #month').attr('disabled', true);
                            $('#year_weekInterval, #year_day, #year_month').attr('disabled', false);
                        });
                        $("#category").change(function () {
                            $('#category').removeClass();
                            var setTitle = $('#category option:selected').text();
                            $('#title').val(setTitle);

                        });
                        // load|refresh feiertage
                        $("#import_refresh").click(function () {
                            value = $('#new_calendar_abo').val();
                            var requestParams = {
                                is_ajax_request: true,
                                action: 'subscribe_calendar',
                                url_path: value,
                                id: window.yanaProfileId
                            };
                            requestParams[window.yanaSessionName] = window.yanaSessionId;
                            $.post(
                                php_self,
                                requestParams,
                                function( data )
                                {
                                    $(".calendar_list").load();
                                    $('#calendar').fullCalendar('refetchEvents');
                                },
                                "json"
                            );
                        });
                        // allDay event check
                        $("#allDayEvent").click(function () {
                            var showTime = false;
                            if (this.checked == true) {
                                $('#startTime_hour').val(['00']);
                                $('#startTime_minute').val(['00']);
                                $('#endTime_hour').val(['00']);
                                $('#endTime_minute').val(['00']);
                                showTime = true;
                            } else {
                                showTime = false;
                            }
                            $('#startTime_hour, #startTime_minute, #endTime_hour, #endTime_minute').attr('disabled', showTime);
                        });
                        // remove user calendar
                        $("#calendar_delete").click(function () {
                            var key = $('#calendar_to_remove').val();
                            var requestParams = {
                                is_ajax_request: true,
                                action: 'remove_user_calendar',
                                id: window.yanaProfileId,
                                key: key
                            };
                            requestParams[window.yanaSessionName] = window.yanaSessionId;
                            $.post(
                                php_self,
                                requestParams,
                                function( data )
                                {
                                    $('#calendar').fullCalendar('refetchEvents');
                                },
                                "json"
                            );
                        });
                        // fill data after id
                        $("#freq").change(function () {
                            var name = $('#freq :selected').val();
                            $("#DAILY, #WEEKLY, #MONTHLY, #YEARLY").hide();
                            $("#standard_option").show();
                            if ( name == 'DAILY') {
                                $("#DAILY").show();
                                $("#dayInterval:radio").val(["byDay"]);
                            }
                            if ( name == 'WEEKLY') {
                                $("#WEEKLY").show();
                            }
                            if ( name == 'MONTHLY') {
                                $("#MONTHLY").show();
                                $("#monthly_default_opt:radio").val(["monthByDay"]);
                            }
                            if ($('#freq :selected').val() == 'YEARLY') {
                                $("#standard_option").css({"display":"block"});
                                $("#y_opt_1:radio").val(["yearMonthDay"]);
                            }
                            if ($('#freq :selected').val() == 'NONE') {
                                $("#standard_option").hide();
                            }
                        });
                });
        //-->{/literal}
        </script>
        <!-- END calendar js-->
    </body>
</html>
