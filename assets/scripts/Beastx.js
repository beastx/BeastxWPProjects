jQuery.fn.center = function () {
    this.css("position","absolute");
    this.css("top", ( jQuery(window).height() - this.height() ) / 2+jQuery(window).scrollTop() + "px");
    this.css("left", ( jQuery(window).width() - this.width() ) / 2+jQuery(window).scrollLeft() + "px");
    return this;
}


Beastx = {};
    
Beastx.log = function(msg, label) {
    if (console && console.log) {
        console.log(msg, label);
    } else {
        alert((label ? label + ': ' : '') + msg);
    }
}

function New(classRef, constructorArgs, events) {
    var obj = new classRef;
    classRef.prototype.element = function(tagname, attributes, childs) {
        return DOM.createElement(tagname, attributes, childs);
    }
    classRef.prototype.replaceContent = function(parent, newContent) {
        while (parent.firstChild) {
            parent.removeChild(parent.firstChild);
        }
        parent.appendChild(newContent.widget ? newContent.widget : newContent);
    }
    classRef.prototype.removeChild = function(container, element) {
        if (element.nodeType == 1) {
            container.removeChild(element);
        } else if (element.widget) {
            container.removeChild(element.widget);
        }
    }
    classRef.prototype.appendChild = function(container, element) {
        if (typeof element == 'string' || typeof element == 'number') {
            container.appendChild(document.createTextNode(element));
        } else if (element.nodeType == 1) {
            container.appendChild(element);
        } else if (element.widget) {
            container.appendChild(element.widget);
        }
    }
    classRef.prototype.caller = function(callback, params) {
        return DOM.createCaller(this, callback, params);
    }
    obj.listeners = [];
    classRef.prototype.addListener = function(eventName, callback) {
        return this.listeners.push({eventName: eventName.substring(2), callback: callback});
    }
    classRef.prototype.dispatchEvent = function(eventName, params) {
        for (var i = 0; i < this.listeners.length; ++i) {
            if (this.listeners[i].eventName == eventName) {
                this.listeners[i].callback(params);
            }
        }
    }
    if (classRef.prototype.toString.call(obj) == '[object Object]') {
        classRef.prototype.toString = function() {
            return this.scriptName ? this.scriptName : '[object Object]';
        }
    }
    obj.classRef = classRef;
    if (constructorArgs) {
        obj.init.apply(obj, constructorArgs);
    } else if (obj.init) {
        obj.init();
    }
    if (events) {
        for (var event in events) {
            obj.addListener(event, events[event]);
        }
    }
    return obj;
}

function getQueryString(ji, fromString) {
    hu = fromString ? fromString : window.location.search.substring(1);
    gy = hu.split("&");
    for (i=0;i<gy.length;i++) {
        ft = gy[i].split("=");
        if (ft[0] == ji) {
            return ft[1];
        }
    }
    return null;
}

DOM = {};
    
DOM.xpath = function(query) {
    return document.evaluate(query, document, null,XPathResult.UNORDERED_NODE_SNAPSHOT_TYPE, null);
}

DOM.getNode = function(path) {
    var node = DOM.xpath(path);
    if (node.snapshotLength == 1) {
        return node.snapshotItem(0);
    }
    return null;
}

DOM.getNodeValue = function(path, defaultValue, forceToUseTextContent) {
    var node = DOM.getNode(path);
    if (node != null) {
        if (node.value && !forceToUseTextContent) {
            return node.value;
        } else {
            return node.textContent;
        }
    }
    return defaultValue;
}

DOM.getNodes = function(query) {
    return document.evaluate(query, document, null,XPathResult.UNORDERED_NODE_SNAPSHOT_TYPE, null);
};

DOM.getFirstNode = function(path) {
    var value = this.getNodes(path);
    if (value.snapshotLength == 1) {
        return value.snapshotItem(0);
    }
    return null;
};

DOM.getFirstNodeValue = function(path, defaultValue) {
    var value = this.getFirstNode(path);
    if (value != null) {
        return value.value;
    }
    else return defaultValue;
};

DOM.getFirstNodeTextContent = function(path, defaultValue) {
    var value = this.getFirstNode(path);
    if (value != null) {
        return value.textContent;
    }
    else return defaultValue;
};

DOM.createCaller = function(object, methodName, params) {
    var f;
    if (params) {
        f = function() {
            if (!object[methodName]) {
                debugger;
            }
            return object[methodName].apply(object, params);
        }
    } else {
        f = function() {
            if (!object[methodName]) {
                debugger;
            }
            return object[methodName].apply(object, arguments);
        }
    }
    return f;
};

DOM.createElement = function(tagName, attributes, childNodes) {
    var element = document.createElement(tagName);
    
    if (attributes) {
        for (var attribute in attributes) {
            type = typeof attributes[attribute];
            if (type == 'function') {
                if (attribute.substr(0, 2) != 'on') {
                    throw new Error('function attributes must begin with on');
                }
                DOM.addListener(element, attribute.substr(2), attributes[attribute]);
            } else if (type == 'boolean') {
                element[attribute] = attributes[attribute];
            } else if (attribute == 'style' && typeof attributes[attribute] == 'object') {
                styleProperties = attributes[attribute];
                for (item in styleProperties) {
                    if (styleProperties[item] !== null) {
                        element.style[item] = styleProperties[item];
                    }
                }
            } else if (attribute == 'class') {
                element.className = attributes[attribute];
            } else if (attributes[attribute] === null) {
                continue;
            } else if (tagName != 'input' || (attributes[attribute] != 'type' && attributes[attribute] != 'name')) {
                element.setAttribute(attribute, attributes[attribute]);
            }
        }
    }
    
    if (childNodes) {
        for (var i = 0; i < childNodes.length; ++i) {
            if (childNodes[i]) {
                if (typeof childNodes[i] == 'string' || typeof childNodes[i] == 'number') {
                    element.appendChild(document.createTextNode(childNodes[i]));
                } else if (childNodes[i].nodeType == 1) {
                    element.appendChild(childNodes[i]);
                } else if (childNodes[i].widget) {
                    element.appendChild(childNodes[i].widget);
                } else {
                    if (Beastx.debugMode) {
                        Beastx.log('falta implementar otros tipos de datos en core.js createElement');
                    }
                }
            }
        }
    }
    return element;
};

DOM.insertAfter = function(newElement, targetElement) {
    var parent = targetElement.parentNode;
    if (parent.lastchild == targetElement) {
        parent.appendChild(newElement);
    } else {
        parent.insertBefore(newElement, targetElement.nextSibling);
    }
}

DOM.addListener = function(element, eventString, caller) {
    element.addEventListener(eventString, caller, true);
};

DOM.removeListener = function(element, eventString, caller) {
    element.removeEventListener(eventString, caller, true);
};

DOM.isChild = function(containerElement, containedElement) {
    // Element.prototype.DOCUMENT_POSITION_CONTAINS == 8
    return (containedElement.compareDocumentPosition(containerElement) & 8) == 8;
}

DOM.hasClass = function(element, className) {
    return !!element.className && VAR.hasWord(element.className, className)
}

DOM.addClass = function(element, className) {
    element.className = VAR.addWord(element.className, className)
}

DOM.removeClass = function(element, className) {
    element.className = VAR.removeWord(element.className, className)
}

DOM.toggleClass = function(element, className) {
    if (DOM.hasClass(element, className)) {
        DOM.removeClass(element, className);
    } else {
        DOM.addClass(element, className);
    }
}

DOM.setHasClass = function(element, className, addIfTrueRemoveIfFalse) {
    if (addIfTrueRemoveIfFalse) {
        DOM.addClass(element, className);
    } else {
        DOM.removeClass(element, className);
    }
}

DOM.preventDefault = function(event) {
    if (event.preventDefault) {
        event.preventDefault();
    } else {
        event.returnValue = false;
    }
}

DOM.stopPropagation = function(event) {
    if (event.stopPropagation) {
        event.stopPropagation();
    } else {
        event.cancelBubble = true;
    }
}

DOM.cancelEvent = function(event) {
    DOM.preventDefault(event);
    DOM.stopPropagation(event);
}

DOM.appendChildNodes = function(element, childs) {
    for (var i = 0; i < childs.length; ++i) {
        element.appendChild(childs[i]);
    }
}

DOM.removeAllChildNodes = function(element) {
    while (element.childNodes.length > 0) {
        element.removeChild(element.firstChild);
    }
}

DOM.cleanChildNodes = function(element) {
    if (element.nodeType == 1) {
        return element;
    } else {
        return null;
    }
}

DOM.getPosition = function(element, dontWrapBody) {
    var left = 0;
    var top = 0;
    var obj = element;
    while (true) {
        left += obj.offsetLeft;
        top += obj.offsetTop;
        if (!obj.offsetParent) {
            break;
        }
        obj = obj.offsetParent;
    }
    var parentNode = element.parentNode;
    while (parentNode && parentNode != document.body) {
        left -= parentNode.scrollLeft;
        top -= parentNode.scrollTop;
        parentNode = parentNode.parentNode;
    }
    return { x: left, y: top };
}


String.prototype.trim = function() {  
    return this.replace(/^\s+|\s+$/g,"");  
}  

String.prototype.ltrim = function() {  
    return this.replace(/^\s+/,"");  
}  

String.prototype.rtrim = function() {  
    return this.replace(/\s+$/,"");  
}

VAR = {};
    
VAR.whitespaceCharacters = " \t\n\r";

VAR.isNumber = function(s, min, max) {
    var n = Number(s);
    return !VAR.isWhitespace(s) &&
        !isNaN(n) &&
        (min === undefined || n >= min) &&
        (max === undefined || n <= max)
    ;
}

VAR.isWhitespace = function(s) {
    for (var i = 0; i < s.length; ++i) {
        if (VAR.whitespaceCharacters.indexOf(s.charAt(i)) == -1) {
            return false;
        }
    }
    return true;
}

VAR.isArray = function(a) {
    return typeof a == 'object' && a.constructor == Array;
}

VAR.inArrayWithCallBack = function(array, callBack) {
    for (var i = 0; i < array.length; ++i) {
        if (callBack(array[i])) {
            return true;
        }
    }
    return false;
}

VAR.inArray = function(array, item) {
    for (var i = 0; i < array.length; ++i) {
        if (array[i] == item) {
            return true;
        }
    }
    return false;
}

VAR.trim = function(s) {
    var i;
    for (i = 0; i < s.length; ++i) {
        if (VAR.whitespaceCharacters.indexOf(s.charAt(i)) == -1) {
            break;
        }
    }
    s = s.substr(i);
    for (i=s.length; i > 0; --i) {
        if (VAR.whitespaceCharacters.indexOf(s.charAt(i)) == -1) {
            break;
        }
    }
    s = s.substr(0, i + 1);
    return s;
}

// Returns a string with no whitespaces
VAR.removeAllWhitespace = function(s) {
    var tmp = '';
    for (var i = s.length; i > 0; --i) {
        var c = s.charAt(i);
        if (VAR.whitespaceCharacters.indexOf(c) == -1) {
            tmp += c;
        }
    }
    return tmp;
}

VAR.addWord = function(str, word) {
    if (VAR.hasWord(str, word)) {
        return str;
    } else {
        return str + ' ' + word;
    }
}

VAR.splitBySpaces = function(str) {
    var words = [];
    var start = 0, end;
    while (true) {
        end = str.indexOf(' ', start);
        if (end != -1) {
            if (end > start) {
                words.push(str.substring(start, end));
            }
            start = end + 1;
        } else {
            if (str.length > start) {
                words.push(str.substring(start));
            }
            return words;
        }
    }
}

VAR.getUncamelized = function(str) {
    var newStr = '';
    for (var i=0; i < str.length; ++i) {
        if (str.charAt(i) == str.charAt(i).toUpperCase()) {
            newStr += ' ' + str.charAt(i).toLowerCase();
        } else {
            newStr += str.charAt(i);
        }
    }
    return VAR.ucfirst(newStr);
}

VAR.removeWord = function(str, word) {
    var str2 = '', c = 0;
    var list = VAR.splitBySpaces(str);
    for (var i = 0; i < list.length; ++i) {
        if (list[i] != word) {
            if (c) {
                str2 += ' ';
            }
            str2 += list[i];
            ++c;
        }
    }
    return str2;
}

VAR.hasWord = function(str, word) {
    var words = [];
    var start = 0, end;
    while (true) {
        end = str.indexOf(' ', start);
        if (end != -1) {
            if (end > start) {
                if (word == str.substring(start, end)) {
                    return true;
                }
            }
            start = end + 1;
        } else {
            if (str.length > start) {
                if (word == str.substring(start)) {
                    return true;
                }
            }
            return false;
        }
    }
}

VAR.endsWith = function(complete_string, part) {
    var pos = complete_string.length - part.length;
    if (complete_string.substr(pos) == part) {
        return complete_string.substr(0, pos);
    } else {
        return false;
    }
}

VAR.startsWith = function(complete_string, part) {
    if (complete_string.substr(0, part.length) == part) {
        return complete_string.substr(part.length);
    } else {
        return false;
    }
}

// Removes an element from an array (it returns true if the element was there)
VAR.remove = function(arr, item) {
    var index = VAR.indexOf(arr, item);
    if (index == -1) {
        return false;
    } else {
        arr.splice(index, 1);
        return true;
    }
}
// Ensures an element is present on an array depending on the "contains" argument
//  if returns contains == true it returns the index of the item, otherwise it returns true of it was there or false if it wasn't
VAR.setContains = function(arr, item, contains) {
    var index = VAR.indexOf(arr, item);
    var isContained = index != -1;
    if (isContained == contains) {
        return index;
    }
    if (contains) {
        arr.push(item);
        return arr.length - 1;
    } else {
        return VAR.remove(arr, item);
    }
}

VAR.forEach = function(list, callback) {
    for (var i = 0; i < list.length; ++i) {
        callback(list[i]);
    }
}

VAR.filter = function(list, callback) {
    var newList = [];
    for (var i = 0; i < list.length; ++i) {
        var itemFiltered = callback(list[i]);
        if (itemFiltered !== null) {
            newList.push(itemFiltered);
        }
    }
    return newList;
}

VAR.camelCase = function(stringVar) {
    var wordArray = stringVar.split(' ');
    var returnString = '';
    for (var i = 0; i < wordArray.length; ++i) {
        returnString += VAR.ucfirst(wordArray[i]);
    }
    return returnString;
}

VAR.ucfirst = function(word) {
    return word.substr(0, 1).toUpperCase() + word.substring(1).toLowerCase();
}

VAR.serialize = function(object) {
    return JSN.serialize(object);
}

VAR.unserialize = function(string) {
    return JSN.unserialize(string);
}

VAR.serializeObject = function(object) {
    var returnString = '';
    for (var i in object) {
        if (returnString != '') {
            returnString += ',';
        } else {
            if (object.length) {
                returnString += '[';
            } else {
                returnString += '{';
            }
        }
        if (!object.length) {
            returnString += '"' + i + '":';
        }
        if (typeof object[i] == 'object') {
            returnString += VAR.serializeObject(object[i]);
        } else if (typeof object[i] == 'string' || typeof object[i] == 'number') {
            returnString += '"' + object[i] + '"';
        }
    }
    if (returnString != '') {
        if (object.length) {
            returnString += ']';
        } else {
            returnString += '}';
        }
    } else {
        returnString += '""';
    }
    return returnString;
}


VAR.addCommas = function(string) {
    string += '';
    var x = string.split('.');
    var x1 = x[0];
    var x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}
    
VAR.formatMilliseconds = function(milliseconds) {
    return IkaTools.formatSeconds(Math.floor(milliseconds/1000));
}

VAR.formatSeconds = function(seconds) {
    var hours = seconds > 3600 ? Math.floor(seconds / 3600) : 0;
    var minutes = Math.floor((seconds % 3600)/ 60);
    minutes = (hours > 0 && minutes < 10) ? '0' + minutes.toString() : minutes;
    seconds = seconds % 60;
    seconds = seconds < 10 ? '0' + seconds.toString() : seconds;
    var text = minutes + ':' + seconds;
    text = hours > 0 ? hours + ':' + text : text;
    return text;
}

VAR.formatNumberToIkariam = function(number) {
    var numberString = String(number);
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(numberString)) {
        numberString = numberString.replace(rgx, '$1' + ',' + '$2');
    }
    return numberString;
}

VAR.formatNumberToShortIkariam = function(number) {
    if (number > 999999) {
        var isMillon = true;
    }
    if (isMillon) {
        var string = new String(number/1000/1000);
    } else {
        var string = new String(number/1000);
    }
    if (!string.indexOf('.')) {
        var fullString = string + (isMillon ? 'M' : 'K');
    } else {
        var fullString = string.substr(0, string.indexOf('.'));
        var decimalPart = string.substr(string.indexOf('.') + 1, 1);
        if (decimalPart != '0') {
            fullString += '.' + decimalPart; 
        }
        fullString += (isMillon ? 'M' : 'K');
    }
    return fullString;
}

VAR.cutText = function(text, length, addColons) {
    if (!length) { length = 100; }
    var cutText = text.substr(0,length);
    if (addColons) {
        if (text.length > length) {
            return cutText + '...';
        } else {
            return cutText;
        }
    } else  {
        return cutText;
    }
}

VAR.getFormatedTimeFromSconds = function(seconds) {
    function addCeroIfNecesary(value) {
        return value < 10 ? '0' + value : value;
    }
    
    if (seconds <= 60) {
        return seconds + 's';
    }
    var mins = parseInt(seconds / 60);
    if (mins > 60) {
        var hours = parseInt(mins / 60);
        if (hours > 24) {
            var days = parseInt(hours/24);
            var restHours = hours % 24;
            var restMins = mins % 60;
            var restSeconds = seconds % 60;
            return days + 'd ' + (restHours > 0 ? restHours + 'h ' : '') + (restMins > 0 ? addCeroIfNecesary(restMins) + 'm ' : '') + (restSeconds > 0 ? addCeroIfNecesary(Math.ceil(restSeconds)) + 's' : '');
        } else {
            var restMins = mins % 60;
            var restSeconds = seconds % 60;
            return hours + 'h ' + (restMins > 0 ? addCeroIfNecesary(restMins) + 'm ' : '') + (restSeconds > 0 ? addCeroIfNecesary(Math.ceil(restSeconds)) + 's' : '');
        }
    } else {
        var restSeconds = seconds % 60;
        return mins + 'm ' + (restSeconds > 0 ? addCeroIfNecesary(Math.ceil(restSeconds)) + 's' : '');
    }
}

VAR.replaceURLWithHTMLLinks = function(text, target) {
    var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gi;
    return text.replace(exp,"<a class='parsedLink' target='_blank' href='$1'>$1</a>"); 
}

VAR.getDateFromText = function(dateText) {
    var fullDateParts = dateText.split(' ');
    var date = fullDateParts[0];
    var time = fullDateParts[1];
    var dateParts = date.split('.');
    var timeParts = time.split(':');
    var dateObj = new Date();
    dateObj.setFullYear(dateParts[2], parseInt(dateParts[1]) - 1, dateParts[0]);
    dateObj.setHours(timeParts[0]);
    dateObj.setMinutes(timeParts[1]);
    dateObj.setSeconds(timeParts[2]);
    return dateObj;
}

VAR.replaceUTCDateWithLocalDate = function(utcDateText) {
    var utcDate = VAR.getDateFromText(utcDateText);
    utcDate.setHours(utcDate.getHours() - 4);
    return VAR.timeAgoFormat(utcDate, true);
}

VAR.timeAgoFormat = function(date, showDate) {
    var localDateObj = new Date();
    var localDateString = 'Hace ';
    var timeAgo = (localDateObj.getTime() - date.getTime()) / 1000;
    if (timeAgo <= 3600) {
        var diff = parseInt(timeAgo / 60);
        localDateString +=  diff >  1 ? diff + ' minutos' : diff + ' minuto';
    } else if (timeAgo <= 86400) {
        var diff = parseInt(timeAgo / 3600);
        localDateString +=  diff >  1 ? diff + ' horas' : diff + ' hora';
    } else {
        var diff = parseInt(timeAgo / 86400);
        localDateString +=  diff >  1 ? diff + ' dias' : diff + ' dia';
    }
    if (showDate) {
        localDateString += ' (' + date.getDate() + '.' + (date.getMonth() + 1) + '.' + date.getFullYear() + '-';
        localDateString += date.getHours() + ':' + date.getMinutes() + ')';
    }
    return localDateString;
}

VAR.getDateStringShortFormat = function(date) {
    return date.getDate() + '-' + (date.getMonth() + 1) + '-' + date.getFullYear();
}

VAR.getDateString = function(date) {
    return date.getDate() + '/' + (date.getMonth() + 1) + '/' + date.getFullYear() + ' ' + date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds();
}







var JSN = {};

JSN.serialize = function (value) {
    var m = {
            '\b': '\\b',
            '\t': '\\t',
            '\n': '\\n',
            '\f': '\\f',
            '\r': '\\r',
            '"' : '\\"',
            '\\': '\\\\'
        };
    var s = {
        array: function (x) {
            var a = ['['], b, f, i, l = x.length, v;
            for (i = 0; i < l; i += 1) {
                v = x[i];
                f = s[typeof v];
                if (f) {
                    v = f(v);
                    if (typeof v == 'string') {
                        if (b) {
                            a[a.length] = ',';
                        }
                        a[a.length] = v;
                        b = true;
                    }
                }
            }
            a[a.length] = ']';
            return a.join('');
        },
        'boolean': function (x) {
            return String(x);
        },
        'null': function (x) {
            return "null";
        },
        number: function (x) {
            return isFinite(x) ? String(x) : 'null';
        },
        object: function (x) {
            if (x) {
                if (x instanceof Array) {
                    return s.array(x);
                }
                var a = ['{'], b, f, i, v;
                for (i in x) {
                    v = x[i];
                    f = s[typeof v];
                    if (f) {
                        v = f(v);
                        if (typeof v == 'string') {
                            if (b) {
                                a[a.length] = ',';
                            }
                            a.push(s.string(i), ':', v);
                            b = true;
                        }
                    }
                }
                a[a.length] = '}';
                return a.join('');
            }
            return 'null';
        },
        string: function (x) {
            if (/["\\\x00-\x1f]/.test(x)) {
                x = x.replace(/([\x00-\x1f\\"])/g, function(a, b) {
                    var c = m[b];
                    if (c) {
                        return c;
                    }
                    c = b.charCodeAt();
                    return '\\u00' +
                        Math.floor(c / 16).toString(16) +
                        (c % 16).toString(16);
                });
            }
            return '"' + x + '"';
        }
    };
    return s[typeof value](value);
}

JSN.unserialize = function(str) {
    return eval('(' + str + ')');
}
