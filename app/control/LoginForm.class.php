<?php
/**
 * LoginForm
 *
 * @version    1.0
 * @package    control
 * @subpackage admin
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class LoginForm extends TPage
{
    protected $form; // form
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct($param)
    {
        parent::__construct();
        
        $ini  = AdiantiApplicationConfig::get();
        
        $this->style = 'clear:both';
        // creates the form
        $this->form = new BootstrapFormBuilder('form_login');
        $this->form->setFormTitle( '<img src="app/templates/theme1/images/logoPreta.png" width="80" height="30"></img>' );
        
        // create the form fields
        $login = new TEntry('login');
        $password = new TPassword('password');
        
        // define the sizes
        $login->setSize('70%', 40);
        $password->setSize('70%', 40);

        $login->style = 'height:35px; font-size:14px;float:left;border-bottom-left-radius: 0;border-top-left-radius: 0;';
        $password->style = 'height:35px;font-size:14px;float:left;border-bottom-left-radius: 0;border-top-left-radius: 0;';
        
        $login->placeholder ='DIGITE SEU EMAIL';
        $password->placeholder = 'DIGITE SUA SENHA';
        
        $login->autofocus = 'autofocus';

        $user = '<span style="float:left;margin-left:44px;height:35px;" class="login-avatar"><span class="glyphicon glyphicon-user"></span></span>';
        $locker = '<span style="float:left;margin-left:44px;height:35px;" class="login-avatar"><span class="glyphicon glyphicon-lock"></span></span>';
        $unit = '<span style="float:left;margin-left:44px;height:35px;" class="login-avatar"><span class="fa fa-university"></span></span>';
        
        $this->form->addFields( [$user, $login] );
        $this->form->addFields( [$locker, $password] );
        
        
        $btn = $this->form->addAction('ENTRAR', new TAction(array($this, 'onLogin')), '');
        $btn->class = 'btn btn-primary';
        $btn->style = 'height: 40px;width: 90%;display: block;margin: auto;font-size:17px;';
        
        $wrapper = new TElement('div');
        $wrapper->style = 'margin:auto; margin-top:100px;max-width:460px;';
        $wrapper->id    = 'login-wrapper';
        $wrapper->add($this->form);
        
        // add the form to the page
        parent::add($wrapper);
    }
    
    public static function onLogin($param){
      //aqui vamos fazer o login dos usuarios...
      
      try{
            TTransaction::open('con_ultimato');
            //TTransaction::setLogger(new TLoggerTXT('./tmp/log.txt'));
            
            $criteria = new TCriteria; 
            $criteria->add(new TFilter('email', '=', $param['login'])); 
            $criteria->add(new TFilter('senha', '=', $param['password'])); 
            
            // load using repository
            $repository = new TRepository('Funcionario'); 
            $funcis = $repository->load($criteria);
            
            
            if ($funcis) { // se encontrou um funcionario
                foreach ($funcis as $funci){ 
                     TSession::setValue('logged', true);
                     TSession::setValue('username', $funci->nome);
                     if ($funci->id == 3) {
                       TSession::setValue('tipo_usuario', 'ADMINISTRADOR');
                     }
                     else {
                       TSession::setValue('tipo_usuario', 'FUNCIONARIO');
                     }
                     AdiantiCoreApplication::gotoPage('PaginaPrincipalForm'); // reload
                }
            }
            else {//se nao existe um funcionario enetao vamos procurar um candidato pra ver se existe
               $repository = new TRepository('Candidato'); 
               $candidatos = $repository->load($criteria);
               
                if ($candidatos) { // se encontrou um candidato
                    foreach ($candidatos as $candidato){
                        TSession::setValue('logged', true);
                        TSession::setValue('username', $candidato->nome);
                        TSession::setValue('tipo_usuario', 'CANDIDATO');
                        TSession::setValue('key', $candidato->id);
                        TSession::setValue('id_candidato', $candidato->id);
                        AdiantiCoreApplication::gotoPage('PaginaPrincipalForm'); // reload
                    }
                }
                else {
                  new TMessage ('info', 'USUÁRIO NÃO ENCONTRADO.');
                }
               
               
            }
       
            TTransaction::close();
      
      }
      catch(Exception $e) {
        new TMessage('info','SEM CONEXÃO </br> Procure o programador do Site ligue (xx)xxxxx-xxxx. ERROR = '. $e->getMessage());
      }
      
      
      
      
      
    }
    
    public static function onLogout(){
      
    }
}
