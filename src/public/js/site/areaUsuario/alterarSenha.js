function Form(){

    var _objsU;

    this._gE = function (id) {
        return document.getElementById(id);
    }

    this._gerarHashSenha = function (string) {

        let hash = SHA512(string);

        hash = hash.split("").reverse().join("");

        return hash;
    }

    this._validar = function(){
        
        if(this._objsU['sea'].value == ""){
            return "Erro! Informe a senha atual";
        }

        if(this._objsU['nse'].value == ""){
            return "Erro! Informe o campo nova senha";
        }

        if(this._objsU['rnse'].value == ""){
            return "Erro! Informe o campo de repetição da nova senha";
        }

        if(this._objsU['nse'].value != this._objsU['rnse'].value){
            return "Erro! O campo (nova senha) não confere com o campo (repita nova senha)";
        }

        if(this._objsU['sea'].value == this._objsU['nse'].value){
            return "Todos os campos estão iguais";
        }

        return true;
            
    }

    this._configurarObjetos = function(){
        
        this._objsU['form'].classPai = this;

        this._objsU['form'].onsubmit = function(){
            this.classPai.submit();
        }

        this._objsU['form'].action = "javascript:void(0)";
    }

    this._carregarObjetosUteis = function(){

        this._objsU['form'] = this._gE("form");

        this._objsU['sea'] = this._gE("sea");
        this._objsU['nse'] = this._gE("nse");   
        this._objsU['rnse'] = this._gE("rnse"); 
    }

    this._construct = function(){
        this._objsU = [];
        this._carregarObjetosUteis();
        this._configurarObjetos();
    }

    this.submit = function(){

        let ret = this._validar();

        if(ret === true){

            //Criptografando senha.
            this._objsU['sea'].value = this._gerarHashSenha( this._objsU['sea'].value );

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

    this._construct();
}
var objForm = new Form();