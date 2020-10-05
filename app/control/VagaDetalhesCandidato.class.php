<?php
//Autor: Lucas

  class VagaDetalhesCandidato extends TPage{
    private $vaga;
    private $form;
    private $form_editar;
    private $datagrid;
    private $pageNavigation;
    private $vagaId;
    private $cel;
    private $candidatoId;
    
    public function __construct(){
        parent::__construct();
        $this->form = new TForm('form');
        $this->form_editar = new TForm('form_editar');
        
        // CAPTURANDO ID DA VAGA -----------------------------------------------------------------------
        
        if (array_key_exists('key', $_GET)) {
          TSession::setValue('VagasFormCandidato_vaga_id', $_GET['key']);
          
          $this->vagaId = $_GET['key'];
        }else {
          if ( TSession::getValue('VagasFormCandidato_vaga_id')){ // Se vier da classe VagasForm
              $this->vagaId = TSession::getValue('VagasFormCandidato_vaga_id');
              //TSession::freeSession('VagasForm_vaga_id'); //destruindo sessÃÂ£o //AVALIAR
          }
          else {
              AdiantiCoreApplication::gotoPage('PaginaPrincipalForm');
          }
        }
      //capturar o id do candidato---------------------------------------------------------------
        if (TSession::getValue('id_candidato') ) {
          $this->candidatoId = TSession::getValue('id_candidato');
        }
      
      //----------------------------------------------------------------------------------------------//*/        
        
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
        
        //BOTÃO CANDIDATARSE
      
        //$obj = $repos->filter('SELECT id FROM laudo WHERE candidato_id= $this->candidatoId AND vaga_id=$this->vagaId');
        try {
          TTransaction::open('con_ultimato');
            
            $repos = new TRepository('Laudo');
            $obj = $repos->where('candidato_id', '=', $this->candidatoId)->where('vaga_id', '=', $this->vagaId)->load();
                         
          TTransaction::close();
        }
        catch(Exception $e){
          new TMessage('error', $e->getMessage());
        }
        
        if ($obj) {
          foreach($obj as $ob)
              $this->laudoId = $ob->id;
          
          if (isset($this->laudoId)) {
            //cria o botao aqui
            $ac2 = new TAction([$this, 'onDescandidatar']);
    
            $bt_candidatarse = new TButton('$bt_candidatarse');
            $bt_candidatarse->setAction($ac2, 'SAIR DA VAGA');
            $bt_candidatarse->setImage('fa:check-square green');
            $bt_candidatarse->id = 'btn_candidatarse';
            $bt_candidatarse->style = "background-color:green; color:white;";    
          }
          }else {
            $ac = new TAction([$this, 'onCandidatar']);
            $ac->setParameter('id_candidato', $this->candidatoId);
            $ac->setParameter('id_vaga', $this->vagaId);
            
            $bt_candidatarse = new TButton('$bt_candidatarse');
            $bt_candidatarse->setImage('fa:square blue');
            $bt_candidatarse->id = 'btn_candidatarse';
            $bt_candidatarse->setAction($ac, 'CANDIDATAR-SE');   
          }
    
        //espaÃÂ§o de cÃÂ©lula para ajuste do botÃÂ£o
        $cell1 = $row->addCell('');
        $cell2 = $row->addCell('');
        
        //Configurando Celulas da tabela1
        $cell1->width = '10%';
        $cell2->width = '60%';
        
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
        
        $cell3 = $row1->addCell($bt_candidatarse);
        $cell3->style = 'vertical-align: text-top';
                
        //Configurando Celulas da tabela2
        $cell1->width = '10%';
        $cell1->height = '40%';
        $cell2->width = '50%';
        
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
        
        //ADICIONANDO CAMPOS NO FORMULÁRIO
        $this->form->addField($bt_candidatarse);      
        
        //-------------------------------------------------------------------------------------------------------//*
        
        $vagas = new TPanelGroup('Vaga');
        $vagas->add($tabela2);
           
        $Vbox = new TVBox();
        $Vbox->style = 'width: 100%';
        $Vbox->add($tabela1);
        $Vbox->add($vagas);
        
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
    public function show(){ //Reescrevendo o mÃÂ©todo show para executar a funÃÂ§ÃÂ£o onReload
        parent::show();
    }
    //----------------------------------------------------------------------------------------------------------*/
    
    public static function onCancelar() {
      
    }
        
    public function onCandidatar($param){  
      try{
        TTransaction::open('con_ultimato');
        $this->laudo = new Laudo();
        $this->laudo->candidato_id = $this->candidatoId;
        $this->laudo->vaga_id = $this->vagaId;
        $this->laudo->store();
        
        new TMessage('info', 'Você se candidatou a vaga!');
        
        $btn = $this->form->getField('$bt_candidatarse');
        $btn->setImage('fa:check-square green');
        $btn->style = "background-color:green; color:white;";
        
        //criando nova acao para o candidato poder se DEScandidatar
        $ac2 = new TAction([$this, 'onDescandidatar']);
        $btn->setAction($ac2, 'SAIR DA VAGA');
          
        TTransaction::close();
      }
      catch(Exception $e){
        new TMessage('error', $e->getMessage() );
      }
    }
    
    public function onDescandidatar($param){
      try {
        TTransaction::open('con_ultimato');
          $laudo = new Laudo($this->laudoId);
          $laudo->delete();
        TTransaction::close();
      }
      catch(Exception $e){
        new TMessage('error', $e->getMessage() );
      }
      
      $btn = $this->form->getField('$bt_candidatarse');
      $btn->setImage('fa:square blue');
      
      $btn->style = "background-color:white; color:black;";
      
      //criando nova acao para o candidato poder se DEScandidatar
      $ac2 = new TAction([$this, 'onCandidatar']);
      $btn->setAction($ac2, 'CANDIDATAR-SE');
          
    }
  }
?>