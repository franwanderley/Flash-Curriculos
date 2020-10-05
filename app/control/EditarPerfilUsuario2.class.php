<?php
  //Autor: Lucas
  
  class EditarPerfilUsuario2 extends TPage{
    private $form;
    private $candidato;
    private $idCandidato; //RECEBER DE ALGUM LUGAR
    private $imagem;
    
    use Adianti\Base\AdiantiFileSaveTrait; //Utilizado para fazer o salvamento de arquivos
    
    public function __construct(){
      parent::__construct();
           
           
           
      try{
        TTransaction::open('con_ultimato');
          $this->candidato = new Candidato($this->idCandidato);
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
      $dt_nascimento = new TDate('dt_nascimento');
      $sexo = new TCombo('sexo');
      $senha = new TPassword('senha');
      $confirmarSenha = new TPassword('confirmarSenha');
      $foto = new TFile('foto');
      
      $this->imagem = new TImage($this->candidato->foto);
      
      //CONFIGURANDO CAMPOS
      $sexo->addItems( ['M' => 'Masculino', 'F' => 'Feminino'] );
      $this->imagem->style = 'max-width: 250px';
      $foto->setAllowedExtensions(['gif', 'png', 'jpg', 'jpeg']); //Permitir apenas essas extensões 
      $foto->enableFileHandling();
      
      //"SETANDO" O VALOR DOS CAMPOS
      $nome->          setValue($this->candidato->nome);
      $email->         setValue($this->candidato->email);
      $dt_nascimento-> setValue($this->candidato->dataNascimento);
      $sexo->          setValue($this->candidato->sexo);
      $foto->setValue($this->candidato->foto);
      
      //ADIÇÃO DOS CAMPOS NO FORMULÁRIO
      //$this->form->addQuickField('imagem', $imagem);
      $this->form->addQuickField('Nome: ', $nome);
      $this->form->addQuickField('Email: ', $email);
      $this->form->addQuickField('Dt. Nascimento: ', $dt_nascimento);
      $this->form->addQuickField('Sexo: ', $sexo);
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
          $candidato = new Candidato($this->idCandidato);
          
          $candidato->nome = $data->nome;
          $candidato->email = $data->email;
          $candidato->dataNascimento = $data->dt_nascimento;
          $candidato->sexo = $data->sexo;  
          $candidato->foto = $data->foto;        
                    
          if ($data->senha == $data->confirmarSenha){
            $candidato->senha = $data->senha;
          }
          else{
            throw new Exception('As Senhas são diferentes!');
          }
                
          $candidato->store();
          new TMessage('info', 'Perfil salvo com sucesso');
          
          $this->saveFile($candidato, $data, 'foto', 'app/images/candidato');// salvo na pasta
          
          //$data->id = $candidato->id;
          //$this->form->setData($data);
          AdiantiCoreApplication::gotoPage('EditarPerfilUsuario2');
        TTransaction::close();
      }
      catch(Exception $e){
        new TMessage('error', $e->getMessage());
        TTransaction::rollback();
      }//*/
    }
  }

?>
