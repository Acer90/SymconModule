class load_Inctance {
    constructor(instanceID, boxID) {
        this.instanceID = instanceID;
        this.boxID = boxID;

        this.config_global = [];
        this.configuration = [];

        this.loadLanguage();
    }

    loadConfig(){

    }

    loadLanguage(){
        this.setStatus("load Language");
        this.loadFont();
    }

    loadFont (){
        this.setStatus("load Font");
        this.detectRunningOn();
    }

    detectRunningOn() {
        //if(document.title.toLocaleLowerCase() == "ipsview"){
        if (window.self !== window.top) {
            this.setStatus("Running on IFrame");
        } else {
            this.setStatus("Running on Main");
        }

        this.startModule();
    }

    startModule(){
        this.setStatus("Start Chart Module");
        if (typeof JSLive_Chart !== 'function'){
            var script = document.createElement('script');
            script.onload = function () {
                new JSLive_Chart(48111, this.config_global, this.configuration);
                this.disableLoader();
            };
            script.src = "/hook/JSLive/js/jslive/Chart.js";
            document.head.appendChild(script);
        }else{
            new JSLive_Chart(48111, this.config_global, this.configuration);
            this.disableLoader();
        }
    }

    disableLoader(){
        document.getElementById("loaderText-"+this.boxID).style.display="none";
    }




    setStatus(strStatus){
        document.getElementById("loaderText-"+this.boxID).innerHTML=strStatus;
    }


    async checkRunning(){

    }

    reloadInstance(){

    }
}