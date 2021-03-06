/**
 * for internal use only
 *
 * @author   Thomas Meyer
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

function yanaAddItem(node)
{
    var item      = node.cloneNode(true);
    item.removeAttribute('id');
    node.parentNode.appendChild(item);
    return item;
}
function yanaRemoveItem(node)
{
    node.parentNode.parentNode.removeChild(node.parentNode);
}
function yanaApplyFilter(o, column, filter, txt)
{
    if (txt) {
        filter = window.prompt(txt, filter);
    }    
    if (filter == null) {
        return false;
    } else {
        o.href += '&' + column + '=' + filter;
        return true;
    }
}
function yanaAddCalendar(id, insertAfterId, day, month, year)
{
    if (Calendar) {
        if (!document.getElementById(id + '_calendar_btn')) {
            var o = document.getElementById(insertAfterId);
            if (o) {
                var spanNode = document.createElement('span');
                spanNode.setAttribute('class', 'gui_generator_calendar icon_calendar');
                spanNode.setAttribute('id', id + '_calendar_btn');
                if (o.nextSibling) {
                    o.parentNode.insertBefore(spanNode, o.nextSibling);
                } else {
                    o.parentNode.appendChild(spanNode);
                }
            }
        }
        Calendar.setup({
            date: new Date(year, month, day),
            button: id + '_calendar_btn',
            range: [year-5, year+4],
            firstDay: 1,
            onUpdate: function(o){yanaCalendarUpdate(id, o)}
        });
    }
}
function yanaCalendarUpdate(id, calendar)
{
    var date = calendar.date;
    var year = date.getYear();
    if (year < 1900) {
        year += 1900;
    }
    var month = date.getMonth() + 1;
    var day = date.getDate();
    var selectYear = document.getElementById(id + '_year');
    var selectMonth = document.getElementById(id + '_month');
    var selectDay = document.getElementById(id + '_day');
    if (selectYear) {
        selectYear.value = year;
    }
    if (selectMonth) {
        selectMonth.value = month;
    }
    if (selectDay) {
        selectDay.value = day;
    }
}
function yanaGuiToggleVisibility(id)
{
    var o = document.getElementById(id);
    if (o) {
        id = '#' + id;
        if (o.style.display == 'none') {
            $(id).slideDown();
        } else {
            $(id).slideUp();
        }
    }
    return false;
}
function yanaSlider(id, min, max, step, currentValue)
{
    if (!document.body) {
        window.setTimeout('yanaSlider("'+id+'", '+min+', '+max+', '+step+', '+currentValue+')', 500);
        return;
    } else {
        document.write('<div id="yanaSlider' + id + '" style="margin: 3px 5px 0px 50px;"></div>');
        var o = document.getElementById(id);
        if (o) {
            o.setAttribute('style', 'float: left;');
            /* validate value */
            o.onchange = function () {
                newValue = parseFloat(this.value);
                if (newValue < min || newValue > max || newValue % step > 0) {
                    this.value = currentValue;
                    return false;
                } else {
                    currentValue = newValue;
                    alert($('#yanaSlider' + id).slider('value', newValue));
                    return true;
                }
            };
            $('#yanaSlider' + id).slider({
                min: min,
                max: max,
                step: step,
                value: currentValue,
                slide: function(event, ui) {
                    o.value = ui.value;
                    return true;
                }
            });
        }
    }
}