/**
 * Slider [Range Element]
 * @name slider.js
 *
 * @description Example :
 *              new slider(id, width, min, max, step, value, inputName, background);
 *
 *              params :
 *              * id          -  A unique ID of the Element.
 *              * width       -  The value length of the element.
 *              * min         -  The expected lower bound for the element’s value.
 *              * max         -  The expected upper bound for the element’s value.
 *              * step        -  Specifies the value granularity of the element’s value.
 *              * value       -  Default value for set the start point of the element.
 *              * inputName   -  Name of the input field.
 *              * background  -  background-color of the slider (if no one choosen default will be use)
 *              allowed entries for background pamameter:
 *                  - '#BECFDF'
 *                  - 'red'
 *              note: the entry of a hex color have 6 digits!(RRGGBB)
 *              for exemple :[ valid #ffffff | invalid #fff ]
 */

function slider (id, width, min, max, step, value, inputName, background)
{

/**
 *  The maximum allowed value length of the element:
 *      - max length: 600px
 *      - min length: 30px
 *  if the entered width is out of range
 *  the default length (max or min) will be taken.
 */

if(width > 600) {
    width = 600;
} else if(width < 30) {
    width = 30;
}

/**
 * Default value for set the start point of the element.
 * if value is out of range :
 *      - value = min (if the number is lower than min)
 *      - value = max (if the number is greater than max)
 */

if(value < min) {
    value = min;
}
if(value > max) {
    value = max;
}

/* block for set all needed params */

/* unique id of the slider */
this.id = id;

/* name of the input element nedded for change slider position*/
this.inputName = inputName;

/**
 *  attributes for calculate slider position
 *
 *  value - Default value for set the start point of the element.
 *  min   - The expected lower bound for the element’s value.
 *  max   - The expected upper bound for the element’s value.
 *  step  - Specifies the value granularity of the element’s value.
 */
this.value = value;
this.min = min;
this.max = max;
this.step = step;

/**
 * calculate padding on the right site
 *
 * width - calculate the padding left and right for keep the moving element into the slider box
 * sliderCurrentPosition - defined the current position of the slieder
 * 
 */
this.width = width -13;
this.sliderCurrentPosition  = 0;

/**
 * background color of the slider
 *
 * the default value of this param is defined on the css stylesheet
 * if no param background is set the default will be taken
 * otherwise the param will be replace the default with the entered param
 * the allowed values for this param are :
 *      - '#BECFDF'
 *      - 'red'
 */
if(typeof background!='undefined') {
    this.background = "background:"+ background +";";
} else {
    this.background = "";
}

/**
 *  attributes for display slider
 *
 *  line  - represents the horizontal line in the slieder box
 *  range - represents the maximal length of the slider
 *  leftdifference - represents the difference between the slider element and the left side.
 *
 */
this.line = width - 18;
this.range = width;
this.leftdifference = 0;

/**
 * difined the slider object and inputField
 */
this.object = null;
this.inputField = null;


/* set elements for display the slider */
document.write("<div " +
                " onmouseup=\"slider.instance['" + this.id + "'].sliderStop();\"" +
                " onmousemove=\"slider.instance['" + this.id + "'].move(event,'" + this.id + "');\"" +
                " id=\""+ this.id + "\"" +
                "onclick=\"slider.instance['" + this.id + "'].sliderStart(this,'"+ this.id +"_input','" +
                this.id + "');slider.instance['" + this.id + "'].move(event,'" + this.id + "');" +
                "slider.instance['" + this.id + "'].sliderStop();\""+
                " class=\"slider_box\"" +
                " style=\"width:"+ this.range +"px;"+ this.background + "\">");
document.write("<div class=\"slider_line\" style=\"width:"+ this.line +"px;\"></div>");
document.write("<div id=\"slider_btn" + this.id +"\" class=\"slider_controler\" " +
                "onmousedown=\"slider.instance['" + this.id + "'].sliderStart(this,'" + this.id +
                "_input','" + this.id + "'); return false\"></div>");
document.write("</div>");

/**
 * slider instance save each slider object 
 */
slider.instance[this.id]=this;
}

/* start to define slider function */

slider.prototype = {
    /**
     *  slider Start(element,inputName,sliderID)
     *
     *  fill the params with the
     *
     *  element   - slider object
     *  inputName - [string] unique id of the input field element
     *  sliderID  - [string] unique id of the slider element
     *
     */
    sliderStart:function(element,inputName,sliderID) {
        var s = slider.instance[sliderID];
        s.object = element;
        s.inputField = document.getElementById(inputName);
        s.leftdifference = parseInt(document.getElementById(sliderID).offsetLeft);
    },

    /**
     *  slider Stop()
     *
     *  destroy the slider object
     */
    sliderStop:function() {
        this.object = null;
    },

    /**
     *  move(event, sliderID)
     *
     *  calculate the slider button position after onmove() event is detected
     *
     *  event
     *  sliderID  - [string] unique id of the slider element
     *
     */
    move:function(event, sliderID) {
        this.leftdifference = parseInt(document.getElementById(sliderID).offsetLeft);
        positionx = event.clientX;
        this.sliderCurrentPosition = (positionx - this.leftdifference);
        if(this.sliderCurrentPosition >= 0 && this.sliderCurrentPosition <= this.width) {
            if(this.object != null) {
                slider.prototype.calcPosition(this.sliderCurrentPosition,true,this.id);
            }
        }
    },

    /**
     *  set Slider(sliderID)
     *
     *  calculate the slider button position after the value in the input field has changed.
     *
     *  sliderID  - [string] unique id of the slider element
     *
     */
    setSlider:function(sliderID) {
        newValue = parseFloat(document.getElementById(sliderID).value);
        newValue = slider.prototype.calcPosition(newValue, false, this.id);
        document.getElementById(sliderID).value = slider.prototype.checkValue(newValue);
        this.object = null;
    },

    /**
     *  calcPosition(input,isMove,sliderID)
     *
     *  this function calculate the position of the slider button and update
     *  the input field for display the value.
     *  
     *  input     - [float|integer] the current position of the slider
     *  isMove    - [boolean] expected true or false
     *                * true  - if the onmove() event is detected
     *                * false - if the input field value has changed
     *  sliderID  - [string] unique id of the slider element
     *
     *  @return integer|float
     */
    calcPosition:function(input,isMove,sliderID) {
        if (isNaN(input)) {
            value = slider.instance[sliderID].min;
        } else {
            diff = input - slider.instance[sliderID].min;
            value = (diff - (diff % slider.instance[sliderID].step)) + slider.instance[sliderID].min;
        }        
        object = document.getElementById('slider_btn' + sliderID);
        
        if(value < slider.instance[sliderID].min) {
            value = slider.instance[sliderID].min;
        }
        if(value > slider.instance[sliderID].max) {
            value = slider.instance[sliderID].max;
        }
        object.style.left = value + "px";
        range = slider.instance[sliderID].max - slider.instance[sliderID].min;
        if(!isMove) {
            if(value >= slider.instance[sliderID].min && value <= slider.instance[sliderID].max) {
                set1 = value - slider.instance[sliderID].min;
                set2 = ( set1 * 100) / range;
                set3 = (slider.instance[sliderID].width * set2) / 100;
                object.style.left = set3 + "px";
            }
        } else {
                set1 = (slider.instance[sliderID].sliderCurrentPosition * 100) / slider.instance[sliderID].width;
                set2 = (range * set1) / 100;
                set3 = set2 + slider.instance[sliderID].min;
                object.style.left = slider.instance[sliderID].sliderCurrentPosition + "px";
                if (isNaN(set3)) {
                    value = slider.instance[sliderID].min;
                } else {
                    diff = set3 - slider.instance[sliderID].min;
                    value = (diff - (diff % slider.instance[sliderID].step)) + slider.instance[sliderID].min;
                }
                /*if the value is lower then the minimum number */
                if(value < slider.instance[sliderID].min) {
                   slider.prototype.calcPosition(value, false,sliderID);
                   slider.prototype.sliderStop(sliderID);
                } else {
                    slider.instance[sliderID].inputField.value = slider.prototype.checkValue(value);
                }
        }
        return value;
    },

    /**
     *   checkValue(input)
     *
     *   an integer will be returned when the entered value is an integer
     *   otherwise a float will be returned with one position after point for exeple :
     *          return 34,2
     *
     *   input - [integer|float] expected the the value of the input field or
     *           the calculated value of the slider button.
     *  @return integer|float
     */
    checkValue:function(input) {
        if(typeof(input)=='number'&&parseInt(input)==input) {
            return input;
        } else {
            return input.toFixed(1);
        }
    }
}
/* Array for instance */
slider.instance = new Object();