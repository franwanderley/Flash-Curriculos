<?php
/*
    autor Francisco Wanderly
    Editar funcionario.
*/
class FuncionarioEditar extends TPage {
    private $dados;
    private $form;
    private $foto;
    private $link; //Para qual pagina vai depois de atualizar
    private $funcionario;
    
    public function __construct(){
        parent::__construct();
        
        if ( TSession::getValue('tipo_usuario') == 'CANDIDATO' || TSession::getValue('tipo_usuario') == NULL) {
          AdiantiCoreApplication::gotoPage('PaginaPrincipalForm');
        }
        
        //Verificando se está logado com admin
        $this->dados = TSession::getValue('username');
        if(! $this->dados ||  TSession::getValue('tipo_usuario') == 'CANDIDATO' )
            new TMessage('warning', 'É preciso estar logado como administrador ou estagiario!', new TAction( [$this, 'redirecionar'] ));
        
        //Pegando a foto por get
        if( array_key_exists('foto',$_GET) ){
             TSession::setValue('foto', $_GET['foto']); 
        }
        else if(array_key_exists('id',$_GET)){
            try{
              TTransaction::open('con_ultimato');
              $this->funcionario = new Funcionario($_GET['id']);
              TSession::setValue('foto', $this->funcionario->foto);
              TTransaction::close();
            }catch(Exception $e){
              new TMessage('error', $e->getMessage());
            } 
        }        
        //Titulo
        $title = new TLabel('Editar Funcionario');
        $title->setFontSize(22);
        $title->setFontFace('Arial');
        $title->style = 'text-align: center; margin-bottom: 5%';
        
        //Criação dos input
        $id           = new TEntry('id');
        $foto         = new TFile('foto');
        $nome         = new TEntry('nome');
        $email        = new TEntry('email');
        $senha        = new TPassword('senha');
        $telefone     = new TEntry('telefone');
        $tipo_usuario = new TEntry('tipo_usuario');
        
        //Modificando os input
        $id->setEditable(false);
        $tipo_usuario->setEditable(false);
        $telefone->setMask('99-999999999');
        $foto->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg'] );//Aceita só está extensões
        //Tipo de usuario constante
        $tipo_usuario->setValue('ESTAGIARIO');
        
        //Diminuir o tamanho
        $id->setSize('10%');
        $nome->setSize('80%');
        $email->setSize('80%');
        $senha->setSize('80%');
        $telefone->setSize('80%');
        $foto->setSize('80%');
        $tipo_usuario->setSize('80%');
        
        //Validação dos Input
        $nome->addValidation('nome', new TMinLengthValidator, array(10));// Parametro nome da classe, tipo de validador, numero em array
        $senha->addValidation('senha', new TMinLengthValidator, array(8));
        $email->addValidation('email', new TEmailValidator);
        $telefone->addValidation('telefone', new TRequiredValidator);
        $nome->addValidation('nome', new TRequiredValidator);
        $email->addValidation('email', new TRequiredValidator);
        $tipo_usuario->addValidation('tipo_usuario', new TRequiredValidator);
        $senha->addValidation('senha', new TRequiredValidator);
        //Formulario
        $this->form = new BootstrapFormBuilder;
        //$this->form->setFormTitle('Funcionario');
        $this->form->addFields([ new TLabel('Id: ') ], [$id]);
        $this->form->addFields([new TLabel('Foto: ')], [$foto , TSession::getValue('foto')]);
        $this->form->addFields([new TLabel('Nome: ')], [$nome]);
        $this->form->addFields([new TLabel('Email: ')], [$email]);
        $this->form->addFields([new TLabel('Senha: ')], [$senha]);
        $this->form->addFields([new TLabel('Telefone: ')], [$telefone]);
        $this->form->addFields([new TLabel('Cargo: ')], [$tipo_usuario]);
        $this->form->addAction('Save', new TAction(array($this, 'onSave') ), 'fa:save green');
        
        $vbox = new TVBox;
        $vbox->style = 'width: 80%';
        //$vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__)); Não tá dando certo
        parent::add($title);
        $vbox->add($this->form);
        parent::add($vbox);
        
    }
    
    public function onSave($param){
        $data = $this->form->getData();
        $this->form->setData(NULL);
        try{
            $this->form->validate();
            
            TTransaction::open('con_ultimato');
            $funcionario = new Funcionario;
            $funcionario->fromArray( (array) $data);//não ta dando certo pois o nome da foto esta diferente do funcionario.
            
            
            //Caso seja outra foto
            //var_dump($data->foto);
            if(  $data->foto){
                if(file_exists(TSession::getValue('foto')))
                  unlink(TSession::getValue('foto'));
                //Configuração da Foto
                $lugar_guardado = 'tmp/'.$funcionario->foto;
                $lugar_salvo    = 'app/images/funcionarios/'. $funcionario->foto;
                $finfo          = new Finfo(FILEINFO_MIME_TYPE);
            
                rename($lugar_guardado, $lugar_salvo);
                $funcionario->foto = $lugar_salvo;
            }
            else if(TSession::getValue('foto')){
                $funcionario->foto = TSession::getValue('foto');
                print 'passou por foto';
            }    
            $funcionario->store();
            //Alterando a sessão
            new TMessage('info', 'Novo funcionario cadastrado com Sucesso');
            

            
            //Voltando para o seu perfil
            //var_dump(TSession::getValue('tipo_usuario'));
            
            
            if(TSession::getValue('tipo_usuario') == 'ADMINISTRADOR' && $funcionario->tipo_usuario == 'FUNCIONARIO')
              AdiantiCoreApplication::gotoPage('ListagemFuncionario');
            else if(TSession::getValue('tipo_usuario') == 'ADMINISTRADOR'){
                TSession::setValue('username', $funcionario->nome);
                AdiantiCoreApplication::gotoPage('PerfilAdministrador');
            }
            else if(TSession::getValue('tipo_usuario') == 'FUNCIONARIO'){
              TSession::setValue('username', $funcionario->nome);
              AdiantiCoreApplication::gotoPage('perfilFuncionario');
            }

            
              TTransaction::close();
            
        }catch(Exception $e){
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
     public function redirecionar(){
         AdiantiCoreApplication::gotoPage('PaginaPrincipalForm');
    }
    
    public function onEdit($id){
    //Modo de editar
    if($id){
        try{
            TTransaction::open('con_ultimato');
             $this->funcionario = new Funcionario($id['id']);
             //Verificando o tipo de Usuario
            if(TSession::getValue('tipo_usuario') != 'ADMINISTRADOR'){
               if(TSession::getValue('username') != $this->funcionario->nome)
                   AdiantiCoreApplication::gotoPage('PaginaPrincipalForm');
            }
             $this->funcionario->foto = NULL;
             $this->form->setData($this->funcionario);
            TTransaction::close();
        }catch(Exception $e){
            new TMessage('error', $e->getMessage());
        }
    }
    }
}

