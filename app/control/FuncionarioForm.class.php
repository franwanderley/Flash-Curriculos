<?php
/*
    autor Francisco Wanderly
    O que falta fazer confirmar senha e tamanho da senha fazer isso com js
*/
class FuncionarioForm extends TPage {
    private $dados;
    private $form;
    private $funcionario;
    
    public function __construct(){
        parent::__construct();
        //Verificando se está logado com admin
        $this->dados = TSession::getValue('username');
        if(! $this->dados ||  TSession::getValue('tipo_usuario') == 'CANDIDATO' )
            new TMessage('warning', 'É preciso estar logado como administrador!', new TAction( [$this, 'redirecionar'] ));
        
        //Titulo
        $title = new TLabel('Criar Funcionario');
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
        $tipo_usuario->setValue('FUNCIONARIO');
        
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
        $this->form->addFields([new TLabel('Foto: ')], [$foto]);
        $this->form->addFields([new TLabel('Nome: ')], [$nome]);
        $this->form->addFields([new TLabel('Email: ')], [$email]);
        $this->form->addFields([new TLabel('Senha: ')], [$senha]);
        $this->form->addFields([new TLabel('Telefone: ')], [$telefone]);
        $this->form->addFields([new TLabel('Cargo: ')], [$tipo_usuario]);
        $this->form->addAction('Save', new TAction(array($this, 'onSave') ), 'fa:save green');
        $this->form->addAction('Novo', new TAction(array($this, 'onClear') ), 'fa:plus-square blue');
        
        $vbox = new TVBox;
        $vbox->style = 'width: 60%';
        //$vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__)); Não tá dando certo
        parent::add($title);
        $vbox->add($this->form);
        parent::add($vbox);
        
    }
    
    public function onSave($param){
        $data = $this->form->getData();
        try{
            $this->form->validate();
            $this->form->setData(NULL);
            
            TTransaction::open('con_ultimato');
            $funcionario = new Funcionario;
            $funcionario->fromArray( (array) $data);
            
            //Configuração da Foto
            //var_dump($funcionario->foto); ===Não pode usar caracteres especiais====
            $lugar_guardado = 'tmp/'.$funcionario->foto;
            $lugar_salvo    = 'app/images/funcionarios/'. $funcionario->foto;
            $finfo          = new Finfo(FILEINFO_MIME_TYPE);
        
            rename($lugar_guardado, $lugar_salvo);
            $funcionario->foto = $lugar_salvo;
            $funcionario->store();
            new TMessage('info', 'Novo funcionario cadastrado com Sucesso');
            AdiantiCoreApplication::gotoPage('ListagemFuncionario');
            TTransaction::close();
            
        }catch(Exception $e){
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    public function onClear(){
        $this->form->setData(NULL);
    }
    
     public function redirecionar(){
         AdiantiCoreApplication::gotoPage('PaginaPrincipalForm');
    }
    
    
}

