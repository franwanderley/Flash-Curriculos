<?php
//Autor: Lucas

  class VagaDetalhesAdmin extends TPage{
    private $vaga;
    private $form;
    private $form_editar;
    private $datagrid;
    private $pageNavigation;
    private $vagaId;
    private $cel;
    
    public function __construct(){
    parent::__construct();
    
    if ( TSession::getValue('tipo_usuario') != 'ADMINISTRADOR' || TSession::getValue('tipo_usuario') == NULL) {
      AdiantiCoreApplication::gotoPage('PaginaPrincipalForm');
    }
    
    $this->form = new TForm('form');
    $this->form_editar = new TForm('form_editar');
    
    // CAPTURANDO ID DA VAGA -----------------------------------------------------------------------

    if (array_key_exists('key', $_GET)) {
      TSession::setValue('VagasFormAdmin_vaga_id', $_GET['key']);
      
      $this->vagaId = $_GET['key'];
    }else {
      if ( TSession::getValue('VagasFormAdmin_vaga_id')){ // Se vier da classe VagasForm
        $this->vagaId = TSession::getValue('VagasFormAdmin_vaga_id');
        //TSession::freeSession('VagasForm_vaga_id'); //destruindo sessÃÂ£o //AVALIAR
      }
      else {
        AdiantiCoreApplication::gotoPage('PaginaPrincipalForm');
      }
    }

    //----------------------------------------------------------------------------------------------//*/
    
    /*CRIANDO E CONFIGURANDO CAMPOS DO FORMULÃÂRIO------------------
     $pesquisa = new TEntry('pesquisa');
     $bt_pesquisar = TButton::create('bt_pesquisar', [$this, 'onPesquisar'], '', 'fa:search green');
     //--------------------------------------------------------------//*/
    
    //*CARREGANDO UMA VAGA DO BANCO
    try{
        TTransaction::open('con_ultimato');
        $vaga = new Vaga($this->vagaId);
        $status = $vaga->status;
        $this->vaga = $vaga;
        
        TTransaction::close();
    }
    catch(Exception $e){
        new TMessage('error', $e->getMessage());
    }
    //--------------------------------------------------------------//*/
    
    //*Criando Tabel1-----------------------------------------------
    $tabela1 = new TTable();
    
    $row = $tabela1->addRow();
    
    $bt_finalizarVaga = TButton::create('bt_finalizarVaga', [$this, 'onFinalizarVaga'], 'Finalizar Vaga', 'fa:expeditedssl red');
    $bt_criarVaga     = TButton::create('bt_criarVaga', [$this, 'onCriarVaga'], 'Criar Vaga', 'fa:plus-circle green');
    
    //$bt_editarVaga    = TButton::create('bt_editarVaga', ['CriarVagaAdmin', 'onEdit'], 'Editar Vaga', 'fa:edit green');
    $ac_editarVaga = new TAction(['CriarVagaAdmin', 'onEdit']);
    $ac_editarVaga->setParameter('key', $this->vagaId);
    
    
    $bt_editarVaga = new TButton('bt_editarVaga');
    $bt_editarVaga->setAction($ac_editarVaga, 'Editar Vaga');
    $bt_editarVaga->setImage('fa:edit green');
    
    //* http://www.adianti.com.br/forum/pt/view_5439?como-enviar-uma-variavel-na-acaotaction-de-um-botao
    //$action_editarVaga = $bt_editarVaga->getAction(); //Capturando aÃÂ§ÃÂ£o do botÃÂ£o
    //$action_editarVaga->setParameter('key', $this->vagaId); //*/
    
    //espaÃÂ§o de cÃÂ©lula para ajuste do botÃÂ£o
    $cell1 = $row->addCell('');
    $cell2 = $row->addCell('');
    
    $cell3 = $row->addCell($bt_finalizarVaga);
    $cell4 = $row->addCell($bt_editarVaga);
    $cell5 = $row->addCell($bt_criarVaga);
    
    //Configurando Celulas da tabela1
    $cell1->width = '10%';
    $cell2->width = '60%';
    $cell3->width = '10%';
    $cell4->width = '10%';
    $cell5->width = '10%';
    
    $tabela1->width = '100%';
    //$tabela1->border = '1';
    //$tabela1->cellpadding = '5'; //nÃÂ£o funciona?
    //--------------------------------------------------------------//*/
    
    //*Criando TabelInfo-----------------------------------------------
    $tabelaInfo = new TTable();
    
    $row1 = $tabelaInfo->addRow();
    $cell1 = $row1->addCell('Empresa:');
    $cell2 = $row1->addCell($vaga->nomeEmpresa);
    
    $row2 = $tabelaInfo->addRow();
    $cell1 = $row2->addCell('Cargo:');
    $cell2 = $row2->addCell($vaga->titulo);
    $cell1->width = '10%';
    $cell2->width = '70%';
    
    $salario = $vaga->mediaSalarial;
    
    if( is_numeric($salario) ){
        $salario = 'R$ '. number_format($salario, 2, ',', '.');
    }
    
    $row3 = $tabelaInfo->addRow();
    $cell1 = $row3->addCell('Salário:');
    $cell2 = $row3->addCell($salario);
    
    $row4 = $tabelaInfo->addRow();
    $cell1 = $row4->addCell('Jornada:');
    $cell2 = $row4->addCell($vaga->jornada. ' Horas por semana');
    
    $tabelaInfo->width = '100%';
    //$tabelaInfo->border = '1';
    
    //--------------------------------------------------------------//*/
    
    //*Criando Tabela2-----------------------------------------------
    $tabela2 = new TTable();
    
    $row1 = $tabela2->addRow();
    
    $cell1 = $row1->addCell($imagem = new TImage($vaga->foto));
    $imagem->style = 'max-width: 150px'; //definindo o tamanho da imagem
    
    $cell2 = $row1->addCell($tabelaInfo);
    $cell2->style = 'vertical-align: text-top';
    
    
    $bt_candidatos_selecionados = new TButton('bt_candidatos_selecionados');
    $bt_candidatos_selecionados->setImage('fa:check-circle green');
    $ac_bt_candidatos_selecionados = new TAction([$this, 'onCandidatos']);
    $ac_bt_candidatos_selecionados->setParameter('status', $status);
    $bt_candidatos_selecionados->setAction($ac_bt_candidatos_selecionados, 'Candidatos Selecionados');
    
    $bt_selecionar_candidatos = TButton::create('bt_selecionar_candidatos', [$this, 'onSelecionarCandidatos'], 'Selecionar Candidatos (Filtro)', 'fa:check green');
    
    //$action_selecionar_candidatos = $bt_selecionar_candidatos->getAction();
    //$action_selecionar_candidatos->setParameter('key', $this->vagaId); //Passando por parÃÂ¢metro o vagaID
    
    $cell3 = $row1->addCell($bt_candidatos_selecionados);
    $cell4 = $row1->addCell($bt_selecionar_candidatos);
    $cell3->style = 'vertical-align: text-top';
    $cell4->style = 'vertical-align: text-top';
    
    //Configurando Celulas da tabela2
    $cell1->width = '10%';
    $cell1->height = '40%';
    $cell2->width = '50%';
    $cell3->width = '20%';
    $cell4->width = '20%';
    
    $row0 = $tabela2->addRow(); //Linhas em Branco
    $cell1 = $row0->addCell('');
    $cell1->height = '20px';
    
    $lbl_descricao = new TLabel('Descrição:');
    $lbl_descricao->setFontStyle('b');
    
    $row2 = $tabela2->addRow();
    $cell1 = $row2->addCell($lbl_descricao);
    $cell1->colspan = 2;
    $ben = new TLabel('Benefícios:');
    $ben->setFontStyle('b');
    $celbenef = $row2->addCell($ben);
    
    $row3 = $tabela2->addRow();
    $cell1 = $row3->addCell( $vaga->descricao ? $vaga->descricao : ' ');
    $cell1->colspan = 2;
    $celbenef = $row3->addCell($vaga->beneficios ? $vaga->beneficios : ' ');
    
    $row4 = $tabela2->addRow(); //Linhas em Branco
    $cell1 = $row4->addCell('');
    $cell1->height = '20px';
    
    $lbl_exigencias = new TLabel('Exigências:');
    $lbl_exigencias->setFontStyle('b');
    
    $row5 = $tabela2->addRow();
    $cell1 = $row5->addCell($lbl_exigencias);
    //$cell1->colspan = 2;
    //$row5->addCell('');
    $cell2 = $row5->addCell(' ');
    
    
    //procurando quantos incritos tem nessa vaga)id
    try{
        TTransaction::open('con_ultimato');
        $repos = new TRepository('Laudo');
        
        $qtd = $repos->where('vaga_id', '=', $vaga->id)->load();
        
        $count = 0;
        foreach($qtd as $q) {
            $count++;
        }

        TTransaction::close();
    }
    catch(Exception $e){
        new TMessage('error', $e->getMessage());
    }
    
    
   
    
    $num_esc = new TLabel('Número de Inscritos : '. intval($count));
    $num_esc->setFontStyle('b');
    $cell3 = $row5->addCell($num_esc);
    
    $row6 = $tabela2->addRow();
    //$row6->width = '100%';
    $cell1 = $row6->addCell($vaga->requisitosMinimos ? $vaga->requisitosMinimos: ' ');
    $cell1->colspan = 2;
    $lmsg = new TLabel('VAGA '.$status);
    
    if ($status == "ABERTA") {
      $lmsg->style = 'color:green; font-size:20px;';
      
    }else {
      $lmsg->style = 'color:red; font-size:20px;';
    }
    
    $this->cel = $row6->addCell($lmsg);
    $this->cel->colspan = 2;
    $this->cel->style = "text-align:right";
    
    
    $tabela2->width = '100%';
    //$tabela2->border = '1';
    //$tabela2->cellpadding = '20'; //NÃÂ£o funciona?
    //--------------------------------------------------------------//*/
    
    //*ADICIONANDO CAMPOS NO FORMULÃÂRIO-----------------------------
    //$this->form->addField($pesquisa);
    //$this->form->addField($bt_pesquisar);
    $this->form->addField($bt_candidatos_selecionados);
    $this->form->addField($bt_selecionar_candidatos);
    $this->form->addField($bt_finalizarVaga);
    $this->form->addField($bt_editarVaga);
    $this->form->addField($bt_criarVaga);
    //--------------------------------------------------------------//*/
    
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
    //*/
    
    //Configurando as colunas da DATAGRID
    $col_foto->setTransformer(function($imagem){ //Definindo o mÃÂ©todo de transformaÃÂ§ÃÂ£o sobre a imagem
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
    
    //Criando datagrid em memÃÂ³ria
    $this->datagrid->createModel();
    //-------------------------------------------------------------------------------------------------------//*
    
    //*CRIANDO ESTRUTURA DE PAGINAÃÂÃÂO-------------------------------------------------------------------------
    $this->pageNavigation = new TPageNavigation; //MÃÂ©todo que faz a paginaÃÂ§ÃÂ£o da datagrid
    $this->pageNavigation->setAction( new TAction( [$this, 'onReload'] ) ); //Sempre que o usuÃÂ¡rio clicar na paginaÃÂ§ÃÂ£o a aÃÂ§ÃÂ£o de carga da datagrid deve ser executada (onReload)
    
    //-------------------------------------------------------------------------------------------------------//*
    
    $vagas = new TPanelGroup('Vaga');
    $vagas->add($tabela2);
    
    $candidatos = new TPanelGroup('Candidatos a Vaga:');
    $candidatos->add($this->datagrid);
    
    $Vbox = new TVBox();
    $Vbox->style = 'width: 100%';
    $Vbox->add($tabela1);
    //$Vbox->add('  .'); // Tentando representar uma linha em branco
    $Vbox->add($vagas);
    $Vbox->add($candidatos);
    $Vbox->add($this->pageNavigation);
    
    if ($status == 'FECHADA') {
      $btn = $this->form->getField('bt_editarVaga');
      $btn->style = "display:none";
      
      $btn = $this->form->getField('bt_selecionar_candidatos');
      $btn->style = "display:none";
      
      $btn = $this->form->getField('bt_finalizarVaga');
      //$btn->setLabel('Reativar Vaga');
      $btn->setImage('fa:lock green');
      $btn->setAction(new TAction([$this, 'onReativar']), 'Reativar Vaga');
    }
    
    parent::add($this->form_editar);
    parent::add($Vbox);     
  }
    
    public function onCandidatos($param){
      //TSession::setValue('VagaDetalhesAdmin_status', '');
      $p['status'] = $param['status'];
      
      AdiantiCoreApplication::gotoPage("NotificarCandidatos", '', $p);
    }
      
    public function onSelecionarCandidatos(){
      //TSession::setValue('VagaDetalhesAdmin_vaga_id', NULL);
      TSession::setValue('VagaDetalhesAdmin_vaga_id', $this->vagaId);
      AdiantiCoreApplication::gotoPage('SelecionarCandidatos');
    }
  
    public function onNotificar() {
      try{
        TTransaction::open('con_ultimato');
          
        $vaga = new Vaga($this->vagaId);
        $vaga->status = "FECHADA";
        $vaga->store();
          
        /* $btn = $this->form->getField('bt_editarVaga');
        $btn->style = "display:none";
          
        $btn = $this->form->getField('bt_selecionar_candidatos');
        $btn->style = "display:none";
         */
        $repository = new TRepository('Candidato');
  
        $criteria = new TCriteria();
        //*DEFININDO FILTROS para que sejam buscados apenas os candidatos relacionados com essa vaga -----------
        
        $filter = new TFilter('id', 'IN', "(SELECT candidato_id FROM laudo WHERE vaga_id = {$this->vagaId})");
        $criteria->add($filter);
        
        $candidatos = $repository->load($criteria, false); // candidatos dessa vaga que vao ser notificados que a vaga fechou.
  
        /*$mail = new TMail;
          
        foreach ($candidatos as $candidato){
        $mail->addBCC($candidato->email, $candidato->nome);
        }            
        
        //$mail->setReplyTo($ini['repl']);      
        //$mail->addAddress($candidato->email, $candidato->nome);
        $mail->setFrom('flashcurriculos@gmail.com', 'Flash Curriculos');
        $mail->setSubject('vaga de emprego');
        $mail->setHtmlBody('A Vaga : '.$this->vaga->nome. '. foi finalizada!');
        $mail->SetUseSmtp();
        $mail->SetSmtpHost('smtp.gmail.com', '465'); // 465 porta com criptografia
        $mail->SetSmtpUser('flashcurriculos@gmail.com', '@123curriculos');
        $mail->send(); // enviar */
        new TMessage('info', 'VAGA FINALIZADA COM SUCESSO!');
          
        TTransaction::close();
      }
      catch (Exception $e){
        new TMessage('error', '<b>Error</b> ' . $e->getMessage() );
      }
        
      AdiantiCoreApplication::gotoPage('VagaDetalhesAdmin'); 
    }
  
    public function onCriarVaga(){
      AdiantiCoreApplication::gotoPage('CriarVagaAdmin');
    }
    
    public function onPesquisar(){
      new TMessage('info', 'Fazer uma pesquisa');
    }
      
    //*---------------------------------------------------------------------------------------------------------
    public function onReload($param){
      //$data = $this->form->getData();
      //$this->form->setData($data);
          
      try{
        TTransaction::open('con_ultimato');
        
        $repository = new TRepository('Candidato');
        $limit = 3;
        
        $criteria = new TCriteria();
        $criteria->setProperty('limit', $limit); //O limit vai ser carregado em baixo de novo????
        $criteria->setProperties( $param ); //LÃÂª a URL e extrai as informaÃÂ§ÃÂµes de paginaÃÂ§ÃÂ£o (limit, ofset etc) e joga para dentro do critÃÂ©rio que vai ser usado para carregar os registros da base de dados
        
        //*DEFININDO FILTROS para que sejam buscados apenas os candidatos relacionados com essa vaga -----------
        
        $filter = new TFilter('id', 'IN', "(SELECT candidato_id FROM laudo WHERE vaga_id = {$this->vagaId})");
        $criteria->add($filter);
        
        //---------------------------------------------------------------------------------------------------//*/
        
        $objetos = $repository->load($criteria, false); // Faz a busca utilizando os filtros captudados
        
        $this->datagrid->clear();
        
        if ($objetos){
            foreach ($objetos as $obj){
                $this->datagrid->addItem($obj);
            }
        }
        
        //confuso
        $criteria->resetProperties(); //NÃÂ£o entendi
        $count = $repository->count( $criteria );
        $this->pageNavigation->setCount( $count );  //Quantos objetos foram carregados
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
    public function show(){ //Reescrevendo o mÃÂ©todo show para executar a funÃÂ§ÃÂ£o onReload
      $this->onReload( func_get_args() );
      parent::show();
    }
    //----------------------------------------------------------------------------------------------------------*/
      
      
    public function onFinalizarVaga($param){
      try {
        TTransaction::open('con_ultimato');
        //aki
        $form = new TQuickForm('form');
        
        $msg = new TLabel('DESEJA FINALIZAR ESTA VAGA ?');
        
        $table = new TTable('table');
        $row = $table->addRow();
        $c = $row->addCell($msg);
        $c->style = 'text-align:center';
        //$table->border = 2;
        $table->width = '100%';
        $table->height = '100px';
        //$form->addQuickField( '', $msg, 400);
        $form->add($table);
        
        $form->addQuickAction('FINALIZAR VAGA E NOTIFICAR CANDIDATOS', new TAction([$this, 'onNotificar']), 'fa:save green');
        $form->addQuickAction('CANCELAR', new TAction([$this, 'onCancelar']), 'fa:save red');
        
        new TInputDialog('NOTIFICAÇÃO DE CANDIDATOS', $form);
        
        TTransaction::close();
      }
      catch(Exception $e){
        new TMessage('error', $e->getMessage());
      }
    }
      
    public static function onCancelar() { } //Não fazer nada
      
    public function onReativar($param) {
      try {
        TTransaction::open('con_ultimato');
          $this->vaga->status = "ABERTA";
          $this->vaga->store();
      
          AdiantiCoreApplication::gotoPage('VagaDetalhesAdmin');
        TTransaction::close();
      }
      catch(Exception $e) {
        new TMessage('info', $e->getMessage());
      }
    } 
  }
?>