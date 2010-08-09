/**
 * for internal use only
 *
 * @author   Thomas Meyer
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

function yanaSelectAction(actionName)
{
    var o1 = document.getElementById('db_sync');
    var o2 = document.getElementById('db_install');
    var o3 = document.getElementById('db_backup');
    var o4 = document.getElementById('db_options');
    var o5 = document.getElementById('list_of_databases');
    var o6 = document.getElementById('list_of_settings');
    var o7 = document.getElementById('title_of_databases');
    var o8 = document.getElementById('title_of_settings');

    o1.style.display = 'none';
    o2.style.display = 'none';
    o3.style.display = 'none';
    o4.style.display = 'none';
    o5.style.display = 'none';
    o6.style.display = 'none';
    o7.style.display = 'none';
    o8.style.display = 'none';

    switch (actionName)
    {
        case 'db_install':
            o2.style.display = 'block';
            o5.style.display = 'block';
            o7.style.display = 'block';
        break;
        case 'db_sync':
            o1.style.display = 'block';
            o5.style.display = 'block';
            o7.style.display = 'block';
        break;
        case 'db_backup':
            o3.style.display = 'block';
            o4.style.display = 'block';
            o5.style.display = 'block';
            o7.style.display = 'block';
        break;
        default:
            o6.style.display = 'block';
            o8.style.display = 'block';
        break;
    }
}
