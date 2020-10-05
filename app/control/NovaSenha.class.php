<?php
  // Autor: LUCAS
  
  class NovaSenha extends TPage{
    protected $form;
    private $email;

    function __construct($param){
      parent::__construct();
        
      //* CAPTURANDO EMAIL DIRETO DO LINK(URL)
      if( array_key_exists('email',$_GET) ){
        $this->email = $_GET['email'];
      }//*/
      
      //$this->email = 'lucaslima211996@gmail.com'; //TESTE
       
      $this->style = 'clear:both';
      $this->form = new BootstrapFormBuilder('form_login');
      $this->form->setFormTitle( '<img src="app/templates/theme1/images/logoPreta.png" width="80" height="30"></img>' );
        
      //CRIANDO OS CAMPOS DO FORMULÁRIO
      $password1 = new TPassword('password1');
      $password2 = new TPassword('password2');
      $camp_email = new THidden('camp_email');
    
      //CONFIGURANDO OS CAMPOS   
      $password1->setSize('70%', 40);
      $password2->setSize('70%', 40);
      $camp_email->setValue($this->email);

      $password1->style = 'height:35px;font-size:14px;float:left;border-bottom-left-radius: 0;border-top-left-radius: 0;';
      $password2->style = 'height:35px;font-size:14px;float:left;border-bottom-left-radius: 0;border-top-left-radius: 0;';
        
      $password1->placeholder = 'Digite uma senha';
      $password2->placeholder = 'Confirme a senha';
        
      $locker = '<span style="float:left;margin-left:44px;height:35px;" class="login-avatar"><span class="glyphicon glyphicon-lock"></span></span>';

      //ADICIONANDO OS CAMPOS NO FORMULÁRIO  
      $this->form->addFields( [$locker, $password1] );
      $this->form->addFields( [$locker, $password2] );
      $this->form->add($camp_email);
                
      $btn = $this->form->addAction('Definir Nova Senha', new TAction(array($this, 'onDefine')), '');
      $btn->class = 'btn btn-primary';
      $btn->style = 'height: 40px;width: 90%;display: block;margin: auto;font-size:17px;';
        
      $wrapper = new TElement('div');
      $wrapper->style = 'margin:auto; margin-top:100px;max-width:460px;';
      $wrapper->id    = 'login-wrapper';
      $wrapper->add($this->form);
      
      parent::add($wrapper);
    }
    
    public function onDefine($param){      
      $email = $param['camp_email'];
      
      if ($param['password1'] == $param['password2']){ //Se as senhas forem iguais      
        try{
          TTransaction::open('con_ultimato');
            
            $criteria = new TCriteria; 
            $criteria->add(new TFilter('email', '=', $email)); 
            
            //---
            $repository = new TRepository('Funcionario'); 
            $funcis = $repository->load($criteria);
      
            if ($funcis) { // SE FOR UM FUNCIONÁRIO OU ADMIN
              foreach ($funcis as $funci){ 
                $id = $funci->id;
                
                $funcionario = new Funcionario($id);
                $funcionario->senha = $param['password1'];
                $funcionario->store();
                
                new TMessage('info', 'Senha alterada com sucesso!', new TAction( [$this, 'goPage'] ) );
              }
            }
            //---
            else { // SE FOR UM CANDIDATO
              $repository = new TRepository('Candidato'); 
              $candidatos = $repository->load($criteria);
                   
              if ($candidatos) { // se encontrou um candidato
                foreach ($candidatos as $candi){           
                  $id = $candi->id;
                  
                  $candidato = new Candidato($id);
                  $candidato->senha = $param['password1'];
                  $candidato->store();
                  
                  new TMessage('info', 'Senha alterada com sucesso!', new TAction([$this, 'goPage']) );
                }
              }
              //---
              else {
                new TMessage ('info', 'E-mail não encontrado!');
              }
            }        
          TTransaction::close();      
        } 
        catch(Exception $e) {
          new TMessage('info','SEM CONEXÃO </br> Procure o programador do Site ligue (xx)xxxxx-xxxx. ERROR = '. $e->getMessage());
        }
      }
      else{
        new TMessage('error', 'Senhas Diferentes!');
      }
    }
    
    public function goPage(){
      AdiantiCoreApplication::gotoPage('LoginForm');
    }
    
  }
?>