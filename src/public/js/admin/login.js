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
        
        if(this._objsU['usu'].value == ""){
            return "Erro! informe o campo usuário!";
        }

        if(this._objsU['sen'].value == ""){
            return "Erro! informe o campo senha!";
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
        this._objsU['usu'] = this._gE("usu");
        this._objsU['sen'] = this._gE("sen");    
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
            this._objsU['sen'].value = this._gerarHashSenha( this._objsU['sen'].value );

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