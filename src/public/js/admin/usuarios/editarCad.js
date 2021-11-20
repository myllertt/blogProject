function Form(){

    var _objsU;

    this._gE = function (id) {
        return document.getElementById(id);
    }

    this._validar = function(){

        if(this._objsU['sts'].value == '0'){
            if(!confirm("Atenção! Você está prestes a desativar o seu próprio usuário! Caso faça isso será desconectado imediatamente. Você confirma esta operação?"))
                return "";
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
        this._objsU['form'] = this._gE("form");
        this._objsU['gen'] = this._gE("gen");   
        this._objsU['sts'] = this._gE("sts");      
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
            if(ret != "")
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