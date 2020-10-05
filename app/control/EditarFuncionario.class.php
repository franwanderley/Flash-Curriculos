<?php
  //Autor: Lucas
  
  class EditarFuncionario extends TPage{
    private $form;
    private $funcionario;
    private $idFuncionario = 3; //RECEBER DE ALGUM LUGAR
    private $imagem;
    
    use Adianti\Base\AdiantiFileSaveTrait; //Utilizado para fazer o salvamento de arquivos
    
    public function __construct(){
      parent::__construct();
           
      try{
        TTransaction::open('con_ultimato');
          $this->funcionario = new Funcionario($this->idFuncionario);
        TTransaction::close();
      }
      catch(Exception $e){
        new TMessage('error', $e->getMessage());
      }
          
      $this->form = new TQuickForm('form');
      //$this->form->class = 'tform';
      
      //CRIANDO OS CAMPOS
      $nome = new TEntry('nome');
      $email = new TEntry('email');
      $telefone = new TEntry('telefone');
      //$dt_nascimento = new TDate('dt_nascimento'); //Ainda não tem no banco
      //$sexo = new TCombo('sexo'); //Ainda não tem no banco
      $senha = new TPassword('senha');
      $confirmarSenha = new TPassword('confirmarSenha');
      $foto = new TFile('foto');
      
      $this->imagem = new TImage($this->funcionario->foto);
      
      //CONFIGURANDO CAMPOS
      //$sexo->addItems( ['M' => 'Masculino', 'F' => 'Feminino'] );
      $this->imagem->style = 'max-width: 250px';
      $foto->setAllowedExtensions(['gif', 'png', 'jpg', 'jpeg']); //Permitir apenas essas extensões 
      $foto->enableFileHandling();
      
      //"SETANDO" O VALOR DOS CAMPOS
      $nome->          setValue($this->funcionario->nome);
      $email->         setValue($this->funcionario->email);
      $telefone->      setValue($this->funcionario->telefone);
      $foto->setValue($this->funcionario->foto);
      //$dt_nascimento-> setValue($this->funcionario->dataNascimento);
      //$sexo->          setValue($this->funcionario->sexo);
      
      
      //ADIÇÃO DOS CAMPOS NO FORMULÁRIO
      //$this->form->addQuickField('imagem', $imagem);
      $this->form->addQuickField('Nome: ', $nome);
      $this->form->addQuickField('Email: ', $email);
      $this->form->addQuickField('Telefone: ', $telefone);
      //$this->form->addQuickField('Dt. Nascimento: ', $dt_nascimento);
      //$this->form->addQuickField('Sexo: ', $sexo);
      $this->form->addQuickField('Foto de Perfil: ', $foto);
      $this->form->addQuickField('Senha: ', $senha);
      $this->form->addQuickField('Confirmar Senha: ', $confirmarSenha);      
      
      $this->form->addQuickAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
            
      //ADICIONANDO INFORMAÇÕES NA PÁGINA
      $table = new TTable('table');
      $row1 = $table->addRow();
      $cell1 = $row1->addCell($this->imagem);
      $cell1->style = 'vertical-align: text-top';
      $cell2 = $row1->addCell($this->form);
            
      $panel = new TPanelGroup('Perfil');
      $panel->add($table);
      
      parent::add($panel);
    }
    
    public function onSave(){
      $data = $this->form->getData();
     
      //*
      try{
        TTransaction::open('con_ultimato');
          $funcionario = new Funcionario($this->idFuncionario);
          
          $funcionario->nome = $data->nome;
          $funcionario->email = $data->email;
          $funcionario->telefone = $data->telefone;
          //$funcionario->dataNascimento = $data->dt_nascimento;
          //$funcionario->sexo = $data->sexo;  
          $funcionario->foto = $data->foto;        
                    
          if ($data->senha == $data->confirmarSenha){
            $funcionario->senha = $data->senha;
          }
          else{
            throw new Exception('As Senhas são diferentes!');
          }
                
          $funcionario->store();
          new TMessage('info', 'Perfil salvo com sucesso');
          
          $this->saveFile($funcionario, $data, 'foto', 'app/images/funcionarios');// salvo na pasta
          
          //$data->id = $funcionario->id;
          //$this->form->setData($data);
          AdiantiCoreApplication::gotoPage('EditarFuncionario');
        TTransaction::close();
      }
      catch(Exception $e){
        new TMessage('error', $e->getMessage());
        TTransaction::rollback();
      }//*/
    }
  }

?>
