function filterKeys(obj, func) {
    return Array.prototype.filter.call(Object.keys(obj), func, obj);
}
function someKeys(obj, func) {
    return Array.prototype.some.call(Object.keys(obj), func, obj);
}
function atLeastOnePropertyMatches(obj, requiredProp) {
    return someKeys(obj, function (prop) {
        if (requiredProp.hasOwnProperty(prop)) {
            return this[prop] === requiredProp[prop];
        }
    });
}
function getMatchingKeys(obj, requiredProp) {
    return filterKeys(obj, function (prop) {
        return atLeastOnePropertyMatches(this[prop], required);
    });
}
Array.prototype.insert = function ( index, item ) {
    this.splice( index, 0, item );
};
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}
function colorTemperatureToRGB(kelvin){
    var temp = kelvin / 100;
    var red, green, blue;

    if( temp <= 66 ){
        red = 255;
        green = temp;
        green = 99.4708025861 * Math.log(green) - 161.1195681661;

        if( temp <= 19){
            blue = 0;
        } else {
            blue = temp-10;
            blue = 138.5177312231 * Math.log(blue) - 305.0447927307;
        }
    } else {
        red = temp - 60;
        red = 329.698727446 * Math.pow(red, -0.1332047592);

        green = temp - 60;
        green = 288.1221695283 * Math.pow(green, -0.0755148492 );

        blue = 255;
    }

    return {
        r : clamp(red,   0, 255),
        g : clamp(green, 0, 255),
        b : clamp(blue,  0, 255)
    }
}
function clamp( x, min, max ) {
    if(x<min){ return min; }
    if(x>max){ return max; }

    return x;
}
function hexToRgb(bigint) {
    var r = (bigint >> 16) & 255;
    var g = (bigint >> 8) & 255;
    var b = bigint & 255;

    return {"r": r, "g": g, "b": b};
}
function hexToInt(rgb){
    var erg = rgb.r*256*256 + rgb.g*256 + rgb.b;
    console.log(Date.now() + " >> hexToInt => " + JSON.stringify(rgb) + " | " + erg);
    return erg;
}

function KelvinToMired(value){
    return Math.floor(1000000 / value);
}
function MiredToKelvin(value){
    return Math.floor(1000000 / value);
}

function Get_WindowWidth(){
    var r_val = window.innerWidth;
    if(configuration.overrideWidth > 0) r_val = configuration.overrideWidth;
    return r_val;
}

function Get_WindowHeight(){
    var r_val = window.innerHeight;
    if(configuration.overrideHeight > 0) r_val = configuration.overrideHeight;
    return r_val;
}

function number_format(number, decimals, dec_point, thousands_sep) {
    // Strip all characters but numerical ones.
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

/**
 * This Function is Created bei IPSymcon => PostionTracking Modul (Source: https://github.com/paresy/PositionTracking/blob/master/PositionTracking/map.html)
 * Thanks to Paresy to create this
 * @returns {{protocol: *, host: *}|{protocol: string, host: string}}
 */
window.detectLocation = function () {
    // Jump through some hoops and loops for IPSStudio
    if (window.location.href.substr(0, 5) === "data:") {
        let data = decodeURI(window.location.href);
        let match = data.match(/<base href="(http:|https:)\/\/(.*)\/">/);
        if (match) {
            return {
                'protocol': match[1],
                'host': match[2]
            }
        } else {
            document.write("Cannot detect protocol/host on IPSStudio Client!");
            throw 'Cannot detect protocol/host on IPSStudio Client!';
        }
    } else {
        // Use the simple way for WebFront + Symcon Apps
        return {
            'protocol': window.location.protocol,
            'host': window.location.host
        }
    }
}