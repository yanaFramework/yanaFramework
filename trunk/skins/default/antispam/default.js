/**
 * for internal use only
 *
 * @author   Thomas Meyer
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

function userSettings(state)
{
    if (state) {
        document.getElementById('antispam_user_settings').style.display = "block";
    } else {
        document.getElementById('antispam_user_settings').style.display = "none";
    }
}
function wordFilterSettings(state)
{
    if (state) {
        document.getElementById('antispam_words').style.display = "block";
    } else {
        document.getElementById('antispam_words').style.display = "none";
    }
}
function ieShowCss(state, o)
{
    if (o.childNodes[1].className == 'description') {
        if (state) {
            o.childNodes[1].style.display='block';
        } else {
            o.childNodes[1].style.display='none';
        }
    }
}
