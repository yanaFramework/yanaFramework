/**
 * for internal use only
 *
 * @author   Thomas Meyer
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

/* initialize */
var over=false;
var sat=0;
var sat_alt=0;
var hue=0;
var hue_alt=0;
var light=0;
var light_alt=0;
var red=255;
var blue=0;
var green=0;
var redHex='FF';
var blueHex='00';
var greenHex='00';
var hexadez  = '#%REDHEX%%GREENHEX%%BLUEHEX%';

function setHue(value)
{
    if (check('hue', value)) {
        parent.hue=value;
    }
    calcColor();
    showColor();
}

function setLight(value)
{
    if (check('light', value)) {
        parent.light=value;
    }
    calcColor();
    showColor();
}

function setSat(value)
{
    if (check('sat', value)) {
        parent.sat=255-value;
    }
    calcColor();
    showColor();
}

function setRed(value)
{
    if (check('red', value)) {
        parent.red=value*1;
    }
    showColor();
}

function setGreen(value)
{
    if (check('green', value)) {
        parent.green=value*1;
    }
    showColor();
}

function setBlue(value)
{
    if (check('blue', value)) {
        parent.blue=value*1;
    }
    showColor();
}

function setColor(message)
{
    if (parent.over) {
        var myColor = prompt(message, '#000000');
        if (check('hex', myColor)) {
            calcRGB(myColor);
            fixColor();
        }
    }
}

function check(typ, wert)
{
    switch (typ)
    {
        case 'hue':
            if (isNaN(wert) || wert<0 || wert>240) { return false; } else { return true; }
        break;
        case 'sat':
            if (isNaN(wert) || wert<0 || wert>255) { return false; } else { return true; }
        break;
        case 'light':
            if (isNaN(wert) || wert<-128 || wert>128) { return false; } else { return true; }
        break;
        case 'red':
            if (isNaN(wert) || wert<0 || wert>255) { return false; } else { return true; }
        break;
        case 'green':
            if (isNaN(wert) || wert<0 || wert>255) { return false; } else { return true; }
        break;
        case 'blue':
            if (isNaN(wert) || wert<0 || wert>255) { return false; } else { return true; }
        break;
        case 'hex':
            if (!wert || isNaN(parseInt("0x"+wert.substring(1,3))) || isNaN(parseInt("0x"+wert.substring(3,5))) || isNaN(parseInt("0x"+wert.substring(5,7)))) { return false; } else { return true; }
        break;
        default:
            return true;
        break;
    }

    return true;
}

function moveColor(event)
{
    if (event.offsetX != null) {
        hue = parseInt(event.offsetX);
        sat = parseInt(event.offsetY);
    } else {
        hue = parseInt(event.layerX)-parseInt(event.target.x);
        sat = parseInt(event.layerY)-parseInt(event.target.y);
    }

    hue = (hue<0) ? 0 : hue; hue = (hue>240) ? 240 : hue;
    sat = (sat<0) ? 0 : sat; sat = (sat>255) ? 255 : sat;

    calcColor();
    showColor();
}

function moveSw(event)
{
    if (event.offsetX != null) {
        light = 128 - parseInt(event.offsetY);
    } else {
        light = -event.layerY+event.target.y+128;
    }

    light = (light<-128) ? -128 : light; light = (light>128) ? 128 : light;

    calcColor();
    showColor();
}

function pickColor(event)
{
    if (event.offsetX != null) {
        hue = parseInt(event.offsetX);
        sat = parseInt(event.offsetY);
    } else {
        hue = parseInt(event.layerX)-parseInt(event.target.x);
        sat = parseInt(event.layerY)-parseInt(event.target.y);
    }
    
    hue = (hue<0) ? 0 : hue; hue = (hue>240) ? 240 : hue;
    sat = (sat<0) ? 0 : sat; sat = (sat>255) ? 255 : sat;
    hue_alt = hue; sat_alt = sat; light_alt = light;

    calcColor();
    fixColor();
}

function pickSw(event)
{
    if (event.offsetX != null) {
        light = 128 - parseInt(event.offsetY);
    } else {
        light = -event.layerY+event.target.y+128;
    }
    
    light = (light<-128) ? -128 : light; light = (light>128) ? 128 : light;
    hue_alt = hue; sat_alt = sat; light_alt = light;

    calcColor();
    fixColor();
}

function calcColor()
{
    hue = (hue<0) ? 0 : hue;

    switch (Math.floor(hue / 40))
    {
        case 0: // hue= 0-40
             red   = 255;
             green = Math.round((6*hue/240)*255);
             blue  = 0;
        break;
        case 1: // hue= 40-80
             red   = Math.round(255-(6*(hue-40)/240)*255);
             green = 255;
             blue  = 0;
        break;
        case 2: // hue= 80-120
             red   = 0;
             green = 255;
             blue  = Math.round((6*(hue-80)/240)*255);
        break;
        case 3: // hue= 120-160
             red   = 0;
             green = Math.round(255-(6*(hue-120)/240)*255);
             blue  = 255;
        break;
        case 4: // hue= 160-200
             red   = Math.round((6*(hue-160)/240)*255);
             green = 0;
             blue  = 255;
        break;
        case 5: // hue= 200-240
             red   = 255;
             green = 0;
             blue  = Math.round(255-(6*(hue-200)/240)*255);
        break;
        default:
        break;
    }

    red = (red<0) ? 0 : red;
    green = (green<0) ? 0 : green;
    blue = (blue<0) ? 0 : blue;

    // calculate Lightness
    red   += (light<0) ? Math.round(((red+sat)*(light))/128):light*2;
    green += (light<0) ? Math.round(((green+sat)*(light))/128):light*2;
    blue  += (light<0) ? Math.round(((blue+sat)*(light))/128):light*2;
    red   = (red   > 255) ? 255 : red;   red   = (red   < 0) ? 0 : red;
    green = (green > 255) ? 255 : green; green = (green < 0) ? 0 : green;
    blue  = (blue  > 255) ? 255 : blue;  blue  = (blue  < 0) ? 0 : blue;

    // calculate Saturation
    var midtone = (((red+green+blue)/3)*light)/256;
    red   = Math.round((midtone*(sat/256) + red*((256-sat)/256)  ));
    green = Math.round((midtone*(sat/256) + green*((256-sat)/256)));
    blue  = Math.round((midtone*(sat/256) + blue*((256-sat)/256) ));

    red   = (red   > 255) ? 255 : red;   red   = (red   < 0) ? 0 : red;
    green = (green > 255) ? 255 : green; green = (green < 0) ? 0 : green;
    blue  = (blue  > 255) ? 255 : blue;  blue  = (blue  < 0) ? 0 : blue;

}

function calcRGB(hexFarbe)
{
    redHex   = hexFarbe.substring(1,3);
    greenHex = hexFarbe.substring(3,5);
    blueHex  = hexFarbe.substring(5,7);
    red   = parseInt("0x"+redHex);
    green = parseInt("0x"+greenHex);
    blue  = parseInt("0x"+blueHex);
}

function calcHex()
{
    redHex   = red.toString(16);
    greenHex = green.toString(16);
    blueHex  = blue.toString(16);
    redHex   = (redHex.length < 2)   ? "0"+redHex   : redHex;
    greenHex = (greenHex.length < 2) ? "0"+greenHex : greenHex;
    blueHex  = (blueHex.length < 2)  ? "0"+blueHex  : blueHex;
}

function showColor()
{
    document.getElementById('hue').value = hue;
    document.getElementById('sat').value = sat;
    document.getElementById('light').value = light;
    document.getElementById('red').value = red;
    document.getElementById('green').value = green;
    document.getElementById('blue').value = blue;

    calcHex();

    var disp = document.getElementById('color');
    disp.style.backgroundColor = "#"+redHex+greenHex+blueHex;

    disp = document.getElementById('hex_new');
    txt = hexadez;
    txt = txt.replace(/%REDHEX%/i, redHex);
    txt = txt.replace(/%GREENHEX%/i, greenHex);
    txt = txt.replace(/%BLUEHEX%/i, blueHex);

    disp.innerHTML=txt;
}

function fixColor()
{
    calcHex();

    var preview = document.getElementById('color_preview');

    preview.style.backgroundColor = "#"+redHex+greenHex+blueHex;

    preview = document.getElementById('hex_old');
    var txt = hexadez;
    txt = txt.replace(/%REDHEX%/i, redHex);
    txt = txt.replace(/%GREENHEX%/i, greenHex);
    txt = txt.replace(/%BLUEHEX%/i, blueHex);

    preview.innerHTML=txt;
}

function resetColor()
{
    hue = hue_alt; sat = sat_alt; light = light_alt;
    calcColor();
    showColor();
}

/**
 * @author Mathias Weitz
 */
function showColorPicker(e, elemName)
{
    var currentColor = document.getElementById(elemName).value;
    var colorSubmit = document.getElementById('color_submit');
    if (colorSubmit) {
        colorSubmit.onclick = function () {
            document.getElementById(elemName).value = '#' + redHex + greenHex + blueHex;
            closeColorPicker();
        }
    }

    var colorAbort = document.getElementById('color_abort');
    if (colorAbort) {
        colorAbort.onclick = function () {
            closeColorPicker();
        }
    }
    var left = e.x;
    var top = e.y;
    if (!left) {
        left = e.pageX;
    }
    if (!top) {
        top = e.pageY;
    }
    var o = document.getElementById('_colorpicker');
    if (o.style.display !== 'block') {
        o.style.position = 'absolute';
        o.style.left = left + 'px';
        o.style.top = (top - 50) + 'px';
        o.style.width = '450px';
        o.style.display = 'block';
    } else {
        o.style.display = 'none';
    }
    if (check('hex', currentColor)) {
        calcRGB(currentColor);
        fixColor();
    }
}
/**
 * @author Mathias Weitz
 */
function closeColorPicker()
{
    var o = document.getElementById('_colorpicker');
    if (o) {
        o.style.display = 'none';
    }
}