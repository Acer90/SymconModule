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