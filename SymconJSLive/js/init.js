if (typeof load_Inctance !== 'function'){
    var script = document.createElement('script');
    script.onload = function () {
        new load_Inctance({INSTANCEID}, "{BOXID}", "{PW}", "{LINK}");
    };
    script.src = "/hook/JSLive/js/loader.js";
    document.head.appendChild(script);
}else{
    new load_Inctance({INSTANCEID}, "{BOXID}", "{PW}", "{LINK}");
}