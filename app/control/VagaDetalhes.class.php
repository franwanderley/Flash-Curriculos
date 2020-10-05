<?php
  //Autor: Lucas
  
  class VagaDetalhes extends TPage{
    private $vaga;
    private $form;
    private $vagaId;
    private $candidatoId;
    private $laudo;
    
    //TSession::setValue('VagaDetalhesCandidato_id_candidato', $this->vagaId);
    //TSession::setValue('VagaDetalhesCandidato_id_vaga', $this->candidatoId);    
    
    public function __construct(){
      parent::__construct();
      
        
      
      
      // CAPTURANDO ID DA VAGA -----------------------------------------------------------------------
      if (array_key_exists('key', $_GET)) {
         TSession::setValue('VagasForm_vaga_id', $_GET['key']);
        
        $this->vagaId = $_GET['key'];
      }else {
        if ( TSession::getValue('VagasForm_vaga_id')){ // Se vier da classe VagasForm
            $this->vagaId = TSession::getValue('VagasForm_vaga_id');
            //TSession::freeSession('VagasForm_vaga_id'); //destruindo sessão //AVALIAR
        }
        else {
          AdiantiCoreApplication::gotoPage('PaginaPrincipalForm');
        }
      }
      
      
      
      
      //----------------------------------------------------------------------------------------------//*/
      
      $this->form = new TForm('form');
      $this->form_editar = new TForm('form_editar');
      
      /*CRIANDO E CONFIGURANDO CAMPOS DO FORMULÁRIO------------------ // Campo de pesquisa no topo da página
      $pesquisa = new TEntry('pesquisa');
      $bt_pesquisar = TButton::create('bt_pesquisar', [$this, 'onPesquisar'], '', 'fa:search green');
      //--------------------------------------------------------------//*/   
      
      //*CARREGANDO UMA VAGA DO BANCO
      try{
        TTransaction::open('con_ultimato');
          $vaga = new Vaga($this->vagaId);
        TTransaction::close();
      }
      catch(Exception $e){
        new TMessage('error', $e->getMessage());
      }
      //--------------------------------------------------------------//*/ 
      
      //*Criando Tabel1-----------------------------------------------
      $tabela1 = new TTable();
      
      $row = $tabela1->addRow();
      
      $bt_candidatarse = TButton::create('$bt_candidatarse', [$this, 'onCandidatar'], 'Candidatar-se', 'fa:check-square green'); 
      
      /*$cell1 = $row->addCell($pesquisa);
      $cell2 = $row->addCell($bt_pesquisar);*/
      
      //Configurando Celulas da tabela1
      /*$cell1->width = '10%';
      $cell2->width = '60%';*/
 
      $tabela1->width = '100%';
      //$tabela1->border = '1';
      //$tabela1->cellpadding = '5'; //não funciona?
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
      $cell1->colspan = 3;
      
      $row3 = $tabela2->addRow();
      $cell1 = $row3->addCell($vaga->descricao);
      $cell1->colspan = 3;
      
      $row4 = $tabela2->addRow(); //Linhas em Branco
      $cell1 = $row4->addCell('');
      $cell1->height = '20px';
      
      $lbl_exigencias = new TLabel('Exigências:');
      $lbl_exigencias->setFontStyle('b');
      
      $row5 = $tabela2->addRow();
      $cell1 = $row5->addCell($lbl_exigencias);
      $cell1->colspan = 2;
      $row5->addCell('');
      
      $row6 = $tabela2->addRow();
      $cell1 = $row6->addCell($vaga->requisitosMinimos);
      $cell1->colspan = 2;
      $row6->addCell('');
      
      $tabela2->width = '100%';
      //$tabela2->border = '1';    
      //$tabela2->cellpadding = '20'; //Não funciona?  
      //--------------------------------------------------------------//*/ 
        
      //*ADICIONANDO CAMPOS NO FORMULÁRIO-----------------------------
      //$this->form->addField($pesquisa);
      //$this->form->addField($bt_pesquisar);
      $this->form->addField($bt_candidatarse);          
      //--------------------------------------------------------------//*/       
    
      $vagas = new TPanelGroup('Vaga');
      $vagas->add($tabela2);
           
      $Vbox = new TVBox();
      $Vbox->style = 'width: 100%';
      $Vbox->add($tabela1);
      //$Vbox->add('  .'); // Tentando representar uma linha em branco
      $Vbox->add($vagas);
      
      parent::add($this->form_editar);
      parent::add($Vbox);
    
    }
    
    public function onCandidatar(){
      $mensagem = 'Você ainda não é cadastrado, clique no botão e cadastre-se para conseguir se candidatar em uma vaga!';
      $action = new TAction([$this, 'goPage']);
      
      new TMessage('info', $mensagem, $action);
    }
    
    public function goPage(){
      AdiantiCoreApplication::gotoPage('CandidatoForm'); //Enviar para página de cadsatro
       //new TMessage('info', 'Alterar método goPage para direcionar para página de cadastro');
    }
    
    public function onPesquisar(){
      new TMessage('info', 'Fazer uma pesquisa');
    }    
  }
?>