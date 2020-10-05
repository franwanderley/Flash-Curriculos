<?php
  // Autor: LUCAS
  
  class EsqueciMinhaSenhaForm extends TPage{
    protected $form;

    function __construct($param){
      parent::__construct();
        
      $ini  = AdiantiApplicationConfig::get();
        
      $this->style = 'clear:both';
      $this->form = new BootstrapFormBuilder('form_login');
      $this->form->setFormTitle( '<img src="app/templates/theme1/images/logoPreta.png" width="80" height="30"></img>' );
        
      //CRIANDO OS CAMPOS DO FORMULÁRIO
      $login = new TEntry('login');
    
      //CONFIGURANDO OS CAMPOS   
      $login->setSize('70%', 40);
      $login->style = 'height:35px; font-size:14px;float:left;border-bottom-left-radius: 0;border-top-left-radius: 0;';  
      $login->placeholder ='DIGITE SEU EMAIL'; 
      $login->autofocus = 'autofocus';

      $user = '<span style="float:left;margin-left:44px;height:35px;" class="login-avatar"><span class="glyphicon glyphicon-user"></span></span>';
      $locker = '<span style="float:left;margin-left:44px;height:35px;" class="login-avatar"><span class="glyphicon glyphicon-lock"></span></span>';
      $unit = '<span style="float:left;margin-left:44px;height:35px;" class="login-avatar"><span class="fa fa-university"></span></span>';
    
      //ADICIONANDO OS CAMPOS NO FORMULÁRIO  
      $this->form->addFields( [$user, $login] );
                
      $btn = $this->form->addAction('Enviar', new TAction(array($this, 'onSend')), '');
      $btn->class = 'btn btn-primary';
      $btn->style = 'height: 40px;width: 90%;display: block;margin: auto;font-size:17px;';
        
      $wrapper = new TElement('div');
      $wrapper->style = 'margin:auto; margin-top:100px;max-width:460px;';
      $wrapper->id    = 'login-wrapper';
      $wrapper->add($this->form);
        
      parent::add($wrapper);
    }
    
    public static function onSend($param){
      $existeEmail = 'NAO';
      
      try{
        TTransaction::open('con_ultimato');
          //TTransaction::setLogger(new TLoggerTXT('./tmp/log.txt'));
              
          $criteria = new TCriteria; 
          $criteria->add(new TFilter('email', '=', $param['login'])); 
              
          $repository = new TRepository('Funcionario'); 
          $funcis = $repository->load($criteria);
    
          if ($funcis) { // Se encontrou um funcionario
            foreach ($funcis as $funci){ 
              $nome =  $funci->nome;
              $email = $funci->email;
              $existeEmail = 'SIM';
            }
          }
          else { // Se nao existe um funcionario então vamos procurar um candidato pra ver se existe
            $repository = new TRepository('Candidato'); 
            $candidatos = $repository->load($criteria);
                 
            if ($candidatos) { // se encontrou um candidato
              foreach ($candidatos as $candidato){           
                $nome =  $candidato->nome;
                $email = $candidato->email;
                $existeEmail = 'SIM';
              }
            }
            else {
              new TMessage ('info', 'E-mail não encontrado!');
            }
            
            // SE EXISITIR O EMAIL NO BANCO =================================================
            if ($existeEmail == 'SIM'){
              $mail = new TMail; // Email do Adianti
              $link = 'http://localhost/system/index.php?class=NovaSenha'; // APENAS PARA TESTE!!!
              $mensagem = 'Clique no link para redefinir sua senha, caso não tenha feito essa solicitação desconsidere essa mensagem. ';
              $mensagem = $mensagem.$link.'&email='.$email;            
          
              //$mail->setReplyTo($ini['repl']);      
              $mail->addAddress($email, $nome);
              $mail->setFrom('flashcurriculos@gmail.com', 'Flash Curriculos');
              $mail->setSubject('Redefinir a Senha');
              $mail->setHtmlBody($mensagem);
              $mail->SetUseSmtp();
              $mail->SetSmtpHost('smtp.gmail.com', '465'); // 465 porta com criptografia
              $mail->SetSmtpUser('flashcurriculos@gmail.com', '@123curriculos');
              $mail->send(); // enviar
              new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
            }
            //===============================================================================
          }        
        TTransaction::close();      
      } 
      catch(Exception $e) {
        new TMessage('info','SEM CONEXÃO </br> Procure o programador do Site ligue (xx)xxxxx-xxxx. ERROR = '. $e->getMessage());
      }
    }
  }
?>
