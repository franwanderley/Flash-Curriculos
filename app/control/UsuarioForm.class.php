<?php
  class UsuarioForm extends TPage{
    
    protected $form;
    private $step;
    
    public function __construct( $param ){
      parent::__construct();
      
      if ( TSession::getValue('tipo_usuario') == 'FUNCIONARIO' || TSession::getValue('tipo_usuario') == 'ADMINISTRADOR') {
          AdiantiCoreApplication::gotoPage('PaginaPrincipalForm');
      }
      
      $this->step = new TBreadCrumb;
      $this->step->addItem('DADOS PESSOAIS', FALSE);
      $this->step->addItem('CURRÍCULO', FALSE);
      $this->step->addItem('USUÁRIO', TRUE);
      $this->step->select ('USUÁRIO');
      
      //CRIAR O OBJETO FORMULÁRIO
      $this->form = new TQuickForm('form_usuario');
      
      //CRIARÇÃO DOS CAMPOS PARA O FORMULÁRIO
      $email        = new TEntry('email');             // conferir no baco
      $senha        = new TPassword('senha');             // conferir no banco
      $valida_senha = new TPassword('valida_senha');      // conferir no banco
      $google       = new TEntry('link_google');       // conferir no banco
      $facebook     = new TEntry('link_facebook');     // conferir no banco
      $instagram    = new TEntry('link_instagram');    // conferir no banco
      $linkedim     = new TEntry('link_linkedin');     // conferir no banco
      
      $exit_email_action = new TAction(array($this, 'onExitAction'));
      $exit_validaSenha_action = new TAction(array($this, 'onExitValidaSenhaAction'));
      $exit_Senha_action = new TAction(array($this, 'onExitSenhaAction'));
      
      $email->setExitAction($exit_email_action);
      $valida_senha->setExitAction($exit_validaSenha_action);
      $senha->setExitAction($exit_Senha_action);
      
      //placeholders
      $google->placeholder = 'google plus';       
      $facebook->placeholder = 'facebook';
      $instagram->placeholder = 'instagram';    
      $linkedim->placeholder = 'linkedim';
      
      $email->addValidation('email', new TEmailValidator);
      $senha->addValidation('senha', new TRequiredValidator);
      
      
      $table1 = new TTable('');//tabela de cima
      //$table1->border = '2';
      $table1->width = '100%';
      $t1_l1 = $table1->addRow();
         $t1_l1_c1 =$t1_l1->addCell('Email:');
         $t1_l1_c2 =$t1_l1->addCell($email);  
      $t1_l2 = $table1->addRow();
         $t1_l2_c1 =$t1_l2->addCell('Senha:');
         $t1_l2_c2 =$t1_l2->addCell($senha);
      $t1_l3 = $table1->addRow();
         $t1_l3_c1 =$t1_l3->addCell('Confirme sua Senha:');
         $t1_l3_c2 =$t1_l3->addCell($valida_senha);
         
      $table2 = new TTable('');//tabela de baixo
      //$table2->border = '1';
      $table2->width = '100%';
      $t2_l1 = $table2->addRow();
         $t2_l1_c1 =$t2_l1->addCell('REDES SOCIAIS');
         $t2_l1_c1->style = 'text-align:center;';
         $t2_l1_c1->colspan = '4';
      $t2_l2 = $table2->addRow();
         $t2_l2_c1 =$t2_l2->addCell(new TImage('fa:google-plus-square'));
         $t2_l2_c2 =$t2_l2->addCell($google);
         $t2_l2_c3 =$t2_l2->addCell(new TImage('fa:facebook-square'));
         $t2_l2_c4 =$t2_l2->addCell($facebook);
      $t2_l3 = $table2->addRow();
         $t2_l3_c1 =$t2_l3->addCell(new TImage('fa:instagram'));
         $t2_l3_c2 =$t2_l3->addCell($instagram);
         $t2_l3_c3 =$t2_l3->addCell(new TImage('fa:linkedin-square'));
         $t2_l3_c4 =$t2_l3->addCell($linkedim);
         
      $table = new TTable('');//tabela principal
      $table->width = '600px';
      $t_l1 = $table->addRow();
      
      $t_l2 = $table->addRow();
     
      
      $t_l1_c1 = $t_l1->addCell($table1);
           $t_l1_c1->style = "border-bottom:1px solid #555555;";
      $t_l2_c1 = $t_l2->addCell($table2);
      
      
      
      $this->form->add($table);
      
      $botao_save = TButton::create('Send', [$this, 'onSave'], 'FINALIZAR CADASTRO', 'fa:save blue');
      $botao_back = TButton::create('Confirm', [$this, 'onBackForm'], 'VOLTAR', 'fa:chevron-circle-left red');
        
      $this->form->setFields([$botao_save, $botao_back, $email, $valida_senha, 
      $senha, $google, $facebook, $instagram, $linkedim]);//tem que passar todos os campos por aqui..........................................
      
      
      
      
      
      //CONFIGURAR TAMANHO DOS CAMPOS
      $email->setSize('100%');
      $senha->setSize('100%');
      $valida_senha->setSize('100%');
      $google->setSize('100%');
      $facebook->setSize('100%');
      $instagram->setSize('100%');
      $linkedim->setSize('100%');
      
      
      //panel ao redor da tabela
      $panel = new TPanelGroup('');
         $t = new TTable('');
         $t->width = '100%';
         
         $linha1_t = $t->addRow();
         $linha1_t->addCell($botao_back)->style = 'text-align:left';
         $linha1_t->addCell($botao_save)->style = 'text-align:right';
         
         $panel->addFooter($t);
      $panel->add($this->form);
      
      
      //ADICIONAR FORMULÁRIO E STEP EM UM CONTAINER 
      $container = new TVBox;
      $container->style = 'display: flex; flex-direction: column; justify-content: center; align-items: center';
      
      
      
      $container->add($this->step);
      $container->add($panel);
        
      parent::add($container);
    }
    
    
    //FUNÇÃO PARA LIMPAR DADOS DO FORMULÁRIO
    public function onClear( $param ){
      $this->form->clear(TRUE);
    }
    
    
    //FUNÇÃO PARA CARREGAR UMA NOVA SESSÃO
    public function onLoadFromSession(){
      if (!TSession::getValue('logged') || TSession::getValue('tipo_usuario') != 'CANDIDATO') {
        AdiantiCoreApplication::gotoPage('PaginaPrincipalForm');
      }
      
      $data = TSession::getValue('form_step3_usuario');
      $this->form->setData($data);
    }
    
    
    public function onBackForm(){
        try
        {
            //$this->form->validate();
            $data = $this->form->getData();
            
            // store data in the session
            TSession::setValue('form_step3_usuario', $data);
            
            // Load another page
            AdiantiCoreApplication::loadPage('CurriculoForm2', 'onLoadFromSession');
            
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
      }
      
      public static function onExitAction($param) {
        
        try{
          TTransaction::open('con_ultimato');
            $repos = new TRepository('Candidato');
            $candidato = $repos->where('email', '=', $param['email'])->load();
            
            if ($candidato)
              new TMessage('info', 'ESTE EMAIL JÁ CONSTA EM NOSSA BASE DE DADOS.');
              
          TTransaction::close();
        }
        catch (Exception $e){
            new TMessage('error', $e->getMessage());
        }
      }
      
      
      
      public static function onExitValidaSenhaAction($param) {
        if ($param['senha'] != $param['valida_senha']) {
          new TMessage('info', 'AS SENHAS DEVEM SER IGUAIS.');
        }
      }
      
      public static function onExitSenhaAction($param) {
        if ($param['senha'] != $param['valida_senha'] && $param['valida_senha'] != '') {
          new TMessage('info', 'AS SENHAS DEVEM SER IGUAIS.');
        }
      }
      
      //FUNÇÃO PARA CARREGAR E EDITAR DADOS DO FORMULÁRIO
    public function onEdit( $param ){
      if (!TSession::getValue('logged') || TSession::getValue('tipo_usuario') != 'CANDIDATO') {
            AdiantiCoreApplication::gotoPage('PaginaPrincipalForm');
      }
      
      try{
        if (isset($param['key'])){
          $key = $param['key'];
          
          TTransaction::open('con_ultimato');
            $response = new stdClass;
            
            $cand = new Candidato($key);
            
            $this->form->setData($cand);
            $response->{'valida_senha'} = $cand->senha;
            
            $this->form->sendData('form_usuario', $response);
             
            
            
          TTransaction::close();
        }
        else{
          $this->form->clear(TRUE);
        }
      }
      catch (Exception $e){
        new TMessage('Erro', $e->getMessage());
        TTransaction::rollback();
      }
    }
    
        //SALVAR DADOS DO FORMULÁRIO
    public function onSave( $param ){
      TSession::setValue('form_step3_usuario', $this->form->getData());
      
      try{
          TTransaction::open('con_ultimato');
          
          $data_candidato = TSession::getValue('form_step1_candidato');
          $data_curriculo = TSession::getValue('form_step2_curriculo');
          $data_usuario   = TSession::getValue('form_step3_usuario');
          
          
          //new TMessage('info', '<pre>'.str_replace(json_encode(print_r($data_curriculo))).'</pre>');
          
                    
          if ($data_curriculo->id_curr) {
            
            $curriculo = new Curriculo($data_curriculo->id_curr);
            
          }else {
            $curriculo = new Curriculo;
          }
          
          $curriculo->pretensaoSalarial = $data_curriculo->pretensao;
          
          $curriculo->store();
          //error aquiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii
          if ($data_candidato->id) {
            $candidato = new Candidato($data_candidato->id);
          }else {
            $candidato = new Candidato;
          }
          
          $candidato->nome              = $data_candidato->nome;
          $candidato->nomeMae           = $data_candidato->nomeMae;
          $candidato->nomePai           = $data_candidato->nomePai;
          
          
          
          if (file_exists('tmp/'.$data_curriculo->photo_path)) {
            copy('tmp/'.$data_curriculo->photo_path, 'app/images/candidato/'.$data_curriculo->photo_path);
            $candidato->foto = 'app/images/candidato/'.$data_curriculo->photo_path;
          }
          else {
            if (isset($data_curriculo->photo_path)) {
              if (substr($data_curriculo->photo_path, 0, 3) == 'app')
                $candidato->foto = substr($data_curriculo->photo_path, 21);
            } 
          }
           
          
          $candidato->rg                = $data_candidato->rg;
          $candidato->cpf               = $data_candidato->cpf;
          $candidato->dataNascimento    = TDate::date2us( ($data_candidato->dataNascimento) );
          $candidato->sexo              = $data_candidato->sexo;
          $candidato->estadoCivil       = $data_candidato->estadoCivil;
          $candidato->endereco          = $data_candidato->endereco;
          $candidato->cep               = $data_candidato->cep;
          $candidato->numeroCasa        = $data_candidato->numeroCasa;
          $candidato->complemento       = $data_candidato->complemento;
          $candidato->telefone          = $data_candidato->telefone;
          $candidato->fone              = $data_candidato->fone;
          $candidato->possuiDeficiencia = $data_candidato->possuiDeficiencia; 
          $candidato->tipoDeficiencia   = $data_candidato->tipoDeficiencia;
          $candidato->cidade_id         = $data_candidato->cidade_id;
          $candidato->curriculo_id      = $curriculo->id;
          //vindo da sessao usuario
          $candidato->email          = $data_usuario->email;
          $candidato->senha          = $data_usuario->senha;
          $candidato->link_google    = $data_usuario->link_google;
          $candidato->link_facebook  = $data_usuario->link_facebook;
          $candidato->link_instagram = $data_usuario->link_instagram;
          $candidato->link_linkedin  = $data_usuario->link_linkedin;
          $candidato->store();
          
          
          //objetivos ou area de interesse
          if ($data_curriculo->area_de_interesse[0]) {
              $cont = 0;
              
              foreach ($data_curriculo->area_de_interesse as $a => $v) {
                $cont++;
                $array_da_sessao[] = intval($v);
              }
              
              $res = new TRepository('Area_e_curriculo');
              $r = $res->where('curriculo_id', '=', $curriculo->id)->load();
              foreach ($r as $f) {
                $array_do_banco[$f->id] = intval($f->areadeinteresses_id);
              }
              
              $resultante = $this->array_result($array_do_banco, $array_da_sessao);
              
              foreach($resultante as $chave => $valor) {
                 
                $r = new Area_e_curriculo(intval($chave));
                $r->delete();
              }
              
              
              for ($i = 0; $i < $cont; $i++) {
                  
                  
                  if ($data_curriculo->idareaECurriculo[$i]) {
                    
                    //uptade
                    
                      $objetivos[$i] = new Area_e_curriculo($data_curriculo->idareaECurriculo[$i]);
                    
                    
                  }else {
                    //cria novo
                    $objetivos[$i] = new Area_e_curriculo;
                  }
                    
                    $objetivos[$i]->areadeinteresses_id = $data_curriculo->area_de_interesse[$i];
                    $objetivos[$i]->curriculo_id        = $curriculo->id;
                    $objetivos[$i]->store();
              }
              //poderia ver o que sobrou do banco e deletar
          }else {
            $repos = new TRepository('Area_e_curriculo');
            $obj = $repos->where('curriculo_id', '=', $curriculo->id)->delete();
          }
          
          //formacao
          if ($data_curriculo->instituicao[0]) {
              $cont = 0;
              
              //a partir daqui
              foreach ($data_curriculo->instituicao as $f) {
                $cont++;
              }
              
              $repos = new TRepository('Formacao');
              $obj = $repos->where('curriculo_id', '=', $curriculo->id)->load();
              
              foreach ($obj as $c) {
                $array_banco_formacao[] = $c->id;
              }
              
              $resultante_formacao = $this->array_result($array_banco_formacao, $data_curriculo->id_formacao);
              //var_dump($resultante_formacao);
              
              foreach($resultante_formacao as $chave => $valor) {
                 
                $r = new Formacao(intval($valor));
                $r->delete();
              }
              
              for ($i = 0; $i < $cont; $i++) {
                 if ($data_curriculo->id_formacao[$i]) {
                   $formacao[$i] = new Formacao($data_curriculo->id_formacao[$i]);
                 }else {
                   $formacao[$i] = new Formacao;
                 }
                    
                    $formacao[$i]->instituicao        = $data_curriculo->instituicao[$i];
                    $formacao[$i]->grauDeEscolaridade = $data_curriculo->grauDeEscolaridade[$i]; 
                    $formacao[$i]->dataComeco         = TDate::date2us( $data_curriculo->dataComeco[$i] );
                    $formacao[$i]->dataFim            = TDate::date2us( $data_curriculo->dataFim[$i] );
                    $formacao[$i]->curso_id           = $data_curriculo->curso_id[0];
                    $formacao[$i]->curriculo_id       = $curriculo->id;
                    $formacao[$i]->store();
              }
          }else {
            $repos = new TRepository('Formacao');
            $obj = $repos->where('curriculo_id', '=', $curriculo->id)->delete();
          }
         
         
         
          if ($data_curriculo->seu_cargo[0]) {
              $cont = 0;
              foreach ($data_curriculo->seu_cargo as $exp) {
                $cont++;
              }
              
              
              $repos = new TRepository('ExperienciaProfissional');
              $obj = $repos->where('curriculo_id', '=', $curriculo->id)->load();
              
              foreach ($obj as $c) {
                $array_banco_expprof[] = $c->id;
              }
              
              $resultante_expprof = $this->array_result($array_banco_expprof, $data_curriculo->id_expprof);
              
              foreach($resultante_expprof as $chave => $valor) {
                 
                $r = new ExperienciaProfissional(intval($valor));
                $r->delete();
              }
              
              
              
              
              for ($i = 0; $i < $cont; $i++) {
                  if ($data_curriculo->id_expprof[$i]) {
                    $exp_prof[$i] = new ExperienciaProfissional($data_curriculo->id_expprof[$i]);
                  }else {
                    $exp_prof[$i] = new ExperienciaProfissional;
                  }
                  
                      
                      $exp_prof[$i]->cargo        = $data_curriculo->seu_cargo[$i];
                      $exp_prof[$i]->empresa      = $data_curriculo->empresa_em_que_trabalhou[$i];
                      $exp_prof[$i]->detalhes     = $data_curriculo->sua_exp_e_seus_projetos[$i];
                      $exp_prof[$i]->dataComeco   = TDate::date2us( $data_curriculo->dataComeco_expprof[$i] );
                      $exp_prof[$i]->dataFim      = TDate::date2us( $data_curriculo->dataFim_expprof[$i] );
                      $exp_prof[$i]->curriculo_id = $curriculo->id;
                      $exp_prof[$i]->store();
              }
          }else {
            $repos = new TRepository('ExperienciaProfissional');
            $obj = $repos->where('curriculo_id', '=', $curriculo->id)->delete();
          }
          
          if ($data_curriculo->nome_do_curso[0]) {
              $cont = 0;
              foreach ($data_curriculo->nome_do_curso as $qualif) {
                $cont++;
              }
              
              //deletando itens tirado do formulario
              $repos = new TRepository('CursosComplementares');
              $obj = $repos->where('curriculo_id', '=', $curriculo->id)->load();
              
              foreach ($obj as $c) {
                $array_banco_cursoscomplem[] = $c->id;
              }
              
              $resultante_cursoscomplem = $this->array_result($array_banco_cursoscomplem, $data_curriculo->id_cursos_complem);
              
              foreach($resultante_cursoscomplem as $chave => $valor) {
                //new TMessage('info', 'valor = '.$valor); 
                $r = new CursosComplementares(intval($valor));
                $r->delete();
              }
              
              
              for ($i = 0; $i < $cont; $i++) {
                  if ($data_curriculo->id_cursos_complem[$i]) {
                    $qualif_prof[$i] = new CursosComplementares($data_curriculo->id_cursos_complem[$i]);
                  }else {
                    $qualif_prof[$i] = new CursosComplementares;
                  }
                      
                      $qualif_prof[$i]->decricao     = $data_curriculo->nome_do_curso[$i];
                      $qualif_prof[$i]->instituicao  = $data_curriculo->empresa_de_ensino[$i];
                      $qualif_prof[$i]->cargaHoraria = $data_curriculo->carga_horaria[$i];
                      $qualif_prof[$i]->dataComeco   = TDate::date2us( $data_curriculo->dataComeco_qualif[$i] );
                      $qualif_prof[$i]->dataFim      = TDate::date2us( $data_curriculo->dataFim_qualif[$i] );
                      $qualif_prof[$i]->curriculo_id = $curriculo->id; 
                      $qualif_prof[$i]->store();
              }
          }else {
            $repos = new TRepository('CursosComplementares');
            $obj = $repos->where('curriculo_id', '=', $curriculo->id)->delete();
          }
          
          if ($data_curriculo->nivel[0]) {
              $cont = 0;
              foreach ($data_curriculo->nivel as $idi) {
                $cont++;
              }
              
              //deletandoitens retirado do formulario
              //deletando itens tirado do formulario
              $repos = new TRepository('Nivel_Idioma');
              $obj = $repos->where('curriculo_id', '=', $curriculo->id)->load();
              
              foreach ($obj as $c) {
                $array_banco_ni[] = $c->id;
              }
              
              $resultante_ni = $this->array_result($array_banco_ni, $data_curriculo->id_nivelIdioma);
              
              foreach($resultante_ni as $chave => $valor) {
                 
                $r = new Nivel_Idioma(intval($valor));
                $r->delete();
              }
              
              
              
              for ($i = 0; $i < $cont; $i++) {
                  if ($data_curriculo->id_nivelIdioma[$i]) {
                    $nivel_idioma[$i] = new Nivel_Idioma($data_curriculo->id_nivelIdioma[$i]);
                  }else {
                    $nivel_idioma[$i] = new Nivel_Idioma;
                  }
                      
                      $nivel_idioma[$i]->nivel        = $data_curriculo->nivel[$i];
                      $nivel_idioma[$i]->idioma_id    = $data_curriculo->idioma[$i];
                      $nivel_idioma[$i]->curriculo_id = $curriculo->id;
                      $nivel_idioma[$i]->store();
              }
          }else {
            $repos = new TRepository('Nivel_Idioma');
            $obj = $repos->where('curriculo_id', '=', $curriculo->id)->delete();
          }
          
          
          
          new TMessage('info', 'SALVO COM SUCESSO!');
          TSession::setValue('form_step1_candidato', null);
          TSession::setValue('form_step2_curriculo', null);
          TSession::setValue('form_step3_usuario',   null);
          
          if (TSession::getValue('logged')) {
            AdiantiCoreApplication::loadPage('PaginaPrincipalForm', '');
          }else {
            TSession::freeSession();
            AdiantiCoreApplication::loadPage('LoginForm', '');
          }
          
        TTransaction::close();
        
      }
      catch (Exception $e){
        new TMessage('Error', $e->getMessage());
        $this->form->setData( $this->form->getData());
        TTransaction::rollback();
      }
    }
    
    
    function array_result($array_do_banco, $array_da_sessao) {      
      foreach ($array_do_banco as $chave => $valor) {
         foreach($array_da_sessao as $c => $v) {
           if ($valor == $v) {
             unset($array_do_banco[$chave]);
             unset($array_da_sessao[$c]);
           }
         }
      }
      return $array_do_banco;
    }
    
    
    
    
      
}
?>