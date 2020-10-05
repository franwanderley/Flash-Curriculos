<?php
  //Autor: Lucas
  
  class NotificarCandidatos extends TPage{
    //A Fazer:
    //Mostrar apenas os condidatos que foram marcados no checkdatelist (Feito)
    //Enviar email de notificação para candidados marcados como selecionados
  
    private $datagrid;
    private $form;
    private $candidatos;
    
    public function __construct(){
      parent::__construct();
      
      if ( TSession::getValue('tipo_usuario') == 'CANDIDATO' || TSession::getValue('tipo_usuario') == NULL) {
        AdiantiCoreApplication::gotoPage('PaginaPrincipalForm');
      }
      
      $this->form = new TForm('form');
      
      $btn_voltarVaga = TButton::create('bt_voltarVaga', [$this, 'voltar'], 'Voltar para Vaga', 'fa:arrow-left green');
      $btn_notificar  = TButton::create('bt_notificar', [$this, 'notificar'], 'Notificar Candidatos', 'fa:envelope green');
      
      $this->form->addField($btn_voltarVaga);
      $this->form->addField($btn_notificar);
      
      $this->form->add($btn_voltarVaga);
      $this->form->add($btn_notificar);
      
    //*CRIANDO DATAGRID---------------------------------------------------------------------------------------
      $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid() );
      $this->datagrid->style = 'width: 100%';//*/
      
    //*Criando colunas da datagrid
      $col_foto            = new TDataGridColumn('foto', 'Foto', 'left');
      $col_nome            = new TDataGridColumn('nome', 'Nome', 'left');
      $col_dataNascimento  = new TDataGridColumn('dataNascimento', 'Data de nascimento', 'left');
      $col_sexo            = new TDataGridColumn('sexo', 'Sexo', 'left');
      $col_cidade          = new TDataGridColumn('cidade->nome_cidade', 'Cidade', 'left');
      $col_estado          = new TDataGridColumn('cidade->estado->nome_estado', 'Estado', 'left');
      $col_laudo           = new TDataGridColumn('laudo', 'Laudo', 'left');                              //AL
      //*/
    
    //AÇÕES DA DATAGRID
      //Ação enviar
      $acao_edit = new TDataGridAction( [$this, 'onSend'] );
      $acao_edit->setLabel('Enviar');
      $acao_edit->setImage('fa:pencil-square-o blue');
      $acao_edit->setField('id'); //Campo vai ser passado para o método onEdit
      $this->datagrid->addAction( $acao_edit );
      
      //Ação excluir
      $acao_excluir = new TDataGridAction( [$this, 'onExcluir'] );
      $acao_excluir->setLabel('Excluir');
      $acao_excluir->setImage('fa:trash-o red');
      $acao_excluir->setField('id'); //Campo vai ser passado para o método onexcluir
      $this->datagrid->addAction( $acao_excluir );
      
      
    //Configurando as colunas da DATAGRID
      $col_foto->setTransformer(function($imagem){ //Definindo o método de transformação sobre a imagem
        $imagem = new TImage($imagem);
        $imagem->style = 'max-width: 100px';
        return $imagem;
      });//*/
      
      
      
    //*Adicionando colunas na datagrid
      //$this->datagrid->addColumn($col_foto);
      $this->datagrid->addColumn($col_foto);
      $this->datagrid->addColumn($col_nome);
      $this->datagrid->addColumn($col_dataNascimento);
      $this->datagrid->addColumn($col_sexo);
      $this->datagrid->addColumn($col_cidade);
      $this->datagrid->addColumn($col_estado);
      $this->datagrid->addColumn($col_laudo);                                                            //AL
           
    //Criando datagrid em memória
      $this->datagrid->createModel();
    //-------------------------------------------------------------------------------------------------------//*

    //*CRIANDO ESTRUTURA DE PAGINAÇÃO-------------------------------------------------------------------------
      $this->pageNavigation = new TPageNavigation; //Método que faz a paginação da datagrid
      $this->pageNavigation->setAction( new TAction( [$this, 'onReload'] ) ); //Sempre que o usuário clicar na paginação a ação de carga da datagrid deve ser executada (onReload)      
    //-------------------------------------------------------------------------------------------------------//*
      
      
      $candidatos = new TPanelGroup('Candidatos Selecionados:');
      $candidatos->add($this->datagrid);
           
      $Vbox = new TVBox();
      $Vbox->style = 'width: 100%';
      $Vbox->add($this->form);
      $Vbox->add($candidatos);
      $Vbox->add($this->pageNavigation);
      
      parent::add($Vbox);
    }




    public function onExcluir($param){
        $ac1 = new TAction([$this, 'excluir']);
        $ac1->setParameter('candidato_id', $param['id']);
        $ac1->setParameter('vaga_id', TSession::getValue('SelecionarCandidatos_vaga_id'));
        
        $tq = new TQuestion('Deseja Retirar Este Candidato da Seleção? ', $ac1);
    }
    
    public function excluir($param) {
      try {
        TTransaction::open('con_ultimato');
        
        $l = new TRepository('Laudo');
        $obj = $l->where('candidato_id', '=', $param['candidato_id'])
                 ->where('vaga_id',      '=', $param['vaga_id'])->load();
        
        foreach($obj as $l) {
          $l->selecionados = false;
          $l->store();
        }
        
        AdiantiCoreApplication::gotoPage('NotificarCandidatos');
        
        TTransaction::close();
      }catch (Exception $e) {
        new TMessage('error', $e->getMessage());
      }
    }
    


 
//*---------------------------------------------------------------------------------------------------------
    public function onReload($param = null){
      
      try{
        TTransaction::open('con_ultimato');        
          $repository = new TRepository('Candidato');
          $limit = 5;
            
          $criteria = new TCriteria();
          $criteria->setProperty('limit', $limit);
          $criteria->setProperties( $param );

          $vagaId = TSession::getValue('VagaDetalhesAdmin_vaga_id');

          $filter = new TFilter('id', 'IN', "(SELECT candidato_id FROM laudo WHERE vaga_id = '{$vagaId}' AND selecionados = '1')");
          $criteria->add($filter);
          $objetos = $repository->load($criteria, false);
            
          $this->datagrid->clear();          
                    
          if ($objetos){
            foreach ($objetos as $obj){
              $candidato = new stdClass;
              $candidato = $obj;
              $laudo = new TImage('fa:check-square green');
                                      
              $this->datagrid->addItem($candidato);
              $this->candidatos[] = $candidato;
            }
          }
            
          $criteria->resetProperties();
          $count = $repository->count( $criteria );
          $this->pageNavigation->setCount( $count );
          $this->pageNavigation->setProperties( $param ); 
          $this->pageNavigation->setLimit( $limit ); 
 
        TTransaction::close();
      }
      catch(Exception $e){
        new TMessage('error', $e->getMessage());
      }
    }
//----------------------------------------------------------------------------------------------------------*/    

//*---------------------------------------------------------------------------------------------------------   
    public function show(){ //Reescrevendo o método show para executar a função onReload
      $this->onReload( func_get_args() );
      parent::show();
    }
//----------------------------------------------------------------------------------------------------------*/  

    public function voltar(){
      //new TMessage('info', 'Voltar para Vaga');
      if (TSession::getValue('tipo_usuario') == 'ADMINISTRADOR')
        AdiantiCoreApplication::gotoPage('VagaDetalhesAdmin');
      else 
        AdiantiCoreApplication::gotoPage('VagaDetalhesFuncionario');
    }
    
    public function notificar(){
      $idVaga = TSession::getValue('SelecionarCandidatos_vaga_id');
      //echo $idVaga;
      
      TTransaction::open('con_ultimato');
        $vaga = new Vaga($idVaga);
      TTransaction::close();
      
      /* Estava funcionando porém muito lerdo!
      foreach ($this->candidatos as $candidato){             
        try{
          $mail = new TMail;
          $mail->setFrom('flashcurriculos@gmail.com');
          $mail->setSubject('vaga de emprego');
          $mail->setHtmlBody('Você foi selecionado para a entrevista de empregoa referente a vaga: '.$vaga->titulo. '. Solicitamos que entre em contato com a empresa para agendar a entrevista!');
          $mail->addAddress($candidato->email, $candidato->nome);
          $mail->SetUseSmtp();
          $mail->SetSmtpHost('smtp.gmail.com', '465'); // 465 porta com criptografia
          $mail->SetSmtpUser('flashcurriculos@gmail.com', '@123curriculos');
          //$mail->setReplyTo($ini['repl']);
          $mail->send(); // enviar
          new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e){
          new TMessage('error', '<b>Error</b> ' . $e->getMessage() );
        }
      }//*/
      
      //*      
      try{
        $mail = new TMail;
      
        foreach ($this->candidatos as $candidato){
          $mail->addBCC($candidato->email, $candidato->nome);
        }            

        //$mail->setReplyTo($ini['repl']);      
        //$mail->addAddress($candidato->email, $candidato->nome);
        $mail->setFrom('flashcurriculos@gmail.com', 'Flash Curriculos');
        $mail->setSubject('vaga de emprego');
        $mail->setHtmlBody('Você foi selecionado para a entrevista de empregoa referente a vaga: '.$vaga->titulo. '. Solicitamos que entre em contato com a empresa para agendar a entrevista!');
        $mail->SetUseSmtp();
        $mail->SetSmtpHost('smtp.gmail.com', '465'); // 465 porta com criptografia
        $mail->SetSmtpUser('flashcurriculos@gmail.com', '@123curriculos');
        $mail->send(); // enviar
        new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
      }
      catch (Exception $e){
        new TMessage('error', '<b>Error</b> ' . $e->getMessage() );
      }//*/
    }
    
    public function onSend($param){
      $id['idcandidato'] = $param['id'];
      
      TSession::setValue('NotoficarCandidato_vaga_id', $id);
      AdiantiCoreApplication::gotoPage('PerfilCandidatoAdmin', '', $id);
    }
  }
?>