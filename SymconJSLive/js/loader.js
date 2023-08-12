class load_Inctance {
    constructor(instanceID, boxID, password, link) {
        this.instanceID = instanceID;
        this.boxID = boxID;
        this.password = password;
        this.link = link;

        this.config_global = [];
        this.configuration = [];
        this.config_language = [];
        this.moduleID = "";

        this.err = false;
        this.run = true;

        this.curTasks = 0;
        this.allTasks = 0;

        this.UpdateStatus();

        //load Config
        this.curTasks = 0;
        this.allTasks = 0;
        this.detectRunningOn();
        this.loadGlobalConfig();
        this.loadConfig();
        //this.loadLanguage(); muss noch implementiert werden

        this.run = false;
    }

    async UpdateStatus(){
        this.setStatus("Loading");
        while((this.run || this.curTasks < this.allTasks) && !this.err){
            this.setStatus("Loading Config (" + this.curTasks + "/" + this.allTasks + ")");
            await this.sleep(100);
        }

        if(!this.err){
            //load Fonts
            this.curTasks = 0;
            this.allTasks = 0;
            this.run = true;
            await this.loadFont();

            if(!this.err){
                //lode Modules
                this.curTasks = 0;
                this.allTasks = 0;
                await this.loadModule();
            }
        }
    }

    async detectRunningOn() {
        //if(document.title.toLocaleLowerCase() == "ipsview"){
        if (window.self !== window.top) {
            this.setStatus("Running on IFrame");
        } else {
            this.setStatus("Running on Main");
        }
 
    }

    async loadGlobalConfig(){
        this.allTasks++;
        try{
            await $.getJSON(this.link+"/GetGlobalConfig?pw="+this.password, function(json, status){
                if(status === "success"){
                    this.config_global = json;
                    this.curTasks++;
                }else{
                    this.setERROR("Cant load Globel-Config");
                }

            }.bind(this));
        } catch(e){
            console.debug(e);
            this.setERROR(e.stack);
        }
    }
    async loadConfig(){
        this.allTasks++;
        await $.getJSON(this.link+"/getConfiguration?Instance="+this.instanceID+"&pw="+this.password, function(json, status){
            if(status === "success"){
                this.configuration = json.Config;
                this.moduleID = json.ModuleID;
                this.curTasks++;
            }else{
                this.setERROR("Cant load Globel-Config");
            }

        }.bind(this));
    }
    async loadLanguage(){
        this.allTasks++;
        await $.getJSON(this.link+"/getLanguage?Instance="+this.instanceID+"&pw="+this.password, function(json, status){
            if(status === "success"){
                this.configuration = json.Config;
                this.moduleID = json.ModuleID;
                this.curTasks++;
            }else{
                this.setERROR("Cant load Globel-Config");
            }

        }.bind(this));
    }

    async loadFont (){
        await $.getJSON(this.link+"/getFonts?Instance="+this.instanceID+"&pw="+this.password, function(json, status){
            if(status === "success"){
                this.allTasks = json.length;
                this.curTasks = 0;
                this.setStatus("Loading Fonts (" + this.curTasks + "/" + this.allTasks + ")");
                json.forEach((font) => {
                    var check_loaded = document.fonts.check('16px '+font);

                    if(!check_loaded){
                        console.log("Load Font " + font);
                        var head = document.getElementsByTagName('HEAD')[0];
                        var item = document.createElement("link");
                        item.rel = 'stylesheet';
                        item.type = 'text/css';
                        item.href = this.link+"/js/css/fonts/"+font+".css";
                        head.appendChild(item);
                        document.body.appendChild(head);

                        check_loaded = document.fonts.check('16px '+font);
                    }

                    this.curTasks++;
                    this.setStatus("Loading Fonts (" + this.curTasks + "/" + this.allTasks + ")");
                  });
            }else{
                this.setERROR("Cant load Globel-Config");
                go_next = false;
            }

        }.bind(this));
    }

    async loadModule(){
        if(status === "success"){
            if (typeof JSLive_Chart !== 'function'){
                this.setStatus("Loading Module ");

                var script = document.createElement('script');
                var head = document.getElementsByTagName('HEAD')[0];

                script.onload = function () {
                    new JSLive_Chart(this.instanceID, this.config_global, this.configuration);
                    this.disableLoader();
                };
                script.src = this.link+"/js/jslive/Chart.js";
                head.appendChild(script);
                document.body.appendChild(head);
            }else{
                new JSLive_Chart(this.instanceID, this.config_global, this.configuration);
                this.disableLoader();
            }
        }else{
            this.setERROR("Cant load Globel-Config");
            go_next = false;
        }
    }

    disableLoader(){
        document.getElementById("loaderText-"+this.boxID).style.display="none";
    }
    setStatus(strStatus){
        document.getElementById("loaderText-"+this.boxID).innerHTML=strStatus;
    }
    setERROR(strError){
        document.getElementById("loaderError-"+this.boxID).innerHTML=strError;
        this.err = true;
    }
    sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
}