function Form(){

    var _objsU;

    this._gE = function (id) {
        return document.getElementById(id);
    }

    this._validar = function(){
        
        if(this._objsU['sen'].value == ""){
            return "Erro! complete o campo senha!";
        }

        if(this._objsU['rse'].value == ""){
            return "Erro! complete o campo de repetição da senha!";
        }

        if(this._objsU['sen'].value != this._objsU['rse'].value){
            return "Erro! Os campos senha e repetição da senha não conferem";
        }

        return true;
            
    }

    this._configurarObjetos = function(){
        
        this._objsU['gen'].value = "";

        this._objsU['sts'].value = "";

        this._objsU['form'].classPai = this;

        this._objsU['form'].onsubmit = function(){
            this.classPai.submit();
        }

        this._objsU['form'].action = "javascript:void(0)";
    }

    this._carregarObjetosUteis = function(){
        this._objsU['sts'] = this._gE("sts");   
        this._objsU['form'] = this._gE("form");
        this._objsU['sen'] = this._gE("sen");
        this._objsU['rse'] = this._gE("rse"); 
        this._objsU['gen'] = this._gE("gen");   
          
    }

    this._construct = function(){
        this._objsU = [];
        this._carregarObjetosUteis();
        this._configurarObjetos();
    }

    this.submit = function(){

        let ret = this._validar();

        if(ret === true){

            //Permitindo o submit
            this._objsU['form'].action = "";
            this._objsU['form'].onsubmit = null;
            this._objsU['form'].submit();

        } else { //Erro formulário
            alert(ret);
        }
    }

    this.setCampoGenero = function(valor){
        this._objsU['gen'].value = valor;
    }

    this.setCampoStatus = function(valor){
        this._objsU['sts'].value = valor;
    }

    this._construct();
}
var objForm = new Form();