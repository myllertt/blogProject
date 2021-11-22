function Form(){

    var _objsU;

    var _arrayObjsTodasPerms;

    this._gE = function (id) {
        return document.getElementById(id);
    }

    this._validar = function(){

        return true;
            
    }

    //--Específicos --------

    //Identifica e coleta todos os elementos que fazem parte dos objetos input de permissões
    this._coletarObjetosInputPermissoes = function(){
        
        let objs = document.getElementsByTagName("input");

        for(let i = 0; i < objs.length; i++){
            if(objs[i].getAttribute("acPerm") === "true"){
                this._arrayObjsTodasPerms[ objs[i].id ] = objs[i];
            }
        }  
    }

    //Gera uma string concatenada de todos os campos marcados
    this._gerarStrPermissoesCampos = function(){

        //string de retorno.
        let strRet = "";

        //Apenas para saber se houve elementos
        var checkedEle = false;

        for(let key in this._arrayObjsTodasPerms){
            
            //Capturado objetos marcados
            if(this._arrayObjsTodasPerms[key].checked == true){
                strRet += this._arrayObjsTodasPerms[key].value+",";
                checkedEle = true;
            }
            
        }

        if(checkedEle){
            //removendo ultima string
            strRet = strRet.slice(0,-1);
        }

        return strRet;

    }

    //Atualizando valores do campo final de acordo com as permissões marcadas.
    this._atualizarCampo_strCatPerms = function(){
        this._objsU['strCatPerms'].value = this._gerarStrPermissoesCampos();
    }

    //---------------------

    this._configurarObjetos = function(){
    
        this._objsU['form'].classPai = this;

        this._objsU['form'].onsubmit = function(){
            this.classPai.submit();
        }
        
        this._objsU['opc_marDesTodos'].classPai = this;
        this._objsU['opc_marDesTodos'].onchange = function(val){
            
            if(this.checked == true){
                this.classPai.marcarTodosRegistros();
            } else {
                this.classPai.desmarcarTodosRegistros();
            }

        }

        this._objsU['form'].action = "javascript:void(0)";
    }

    this._carregarObjetosUteis = function(){

        this._objsU['form'] = this._gE("form");

        this._objsU['regBase'] = this._gE("regBase");

        this._objsU['strCatPerms'] = this._gE("strCatPerms");

        //Botão marcar, desmarcar (todos)
        this._objsU['opc_marDesTodos'] = this._gE("opc_marDesTodos");

        this._coletarObjetosInputPermissoes();
        
    }

    this._construct = function(){
        this._objsU = [];
        this._arrayObjsTodasPerms = [];
        this._carregarObjetosUteis();
        this._configurarObjetos();
    }

    this.marcarTodosRegistros = function(){

        for(let key in this._arrayObjsTodasPerms){
            this._arrayObjsTodasPerms[ key ].checked = true;
        }
    }

    this.desmarcarTodosRegistros = function(){

        for(let key in this._arrayObjsTodasPerms){
            this._arrayObjsTodasPerms[ key ].checked = false;
        }

    }

    this.marcarPorCodigo = function(codigo){
        if(codigo == ""){
            return false;
        }

        if(this._arrayObjsTodasPerms[ codigo ]){
            this._arrayObjsTodasPerms[ codigo ].checked = true;
        }

        return false;
    }

    this.submit = function(){

        let ret = this._validar();

        if(ret === true){

            //Atualizando campo gerador da concatenação
            this._atualizarCampo_strCatPerms();

            //Permitindo o submit
            this._objsU['form'].action = "";
            this._objsU['form'].onsubmit = null;
            this._objsU['form'].submit();

        } else { //Erro formulário
            if(ret != "")
                alert(ret);
        }
    }

    this.setCampoRegraBase = function(valor){
        this._objsU['regBase'].value = valor;
    }

    this._construct();
}
var objForm = new Form();