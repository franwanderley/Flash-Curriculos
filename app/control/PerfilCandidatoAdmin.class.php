<?php
/*
    Autor Francsco Wandery
    Pagina onde o administrador e funcionario ver o perfil do candidato $pdf->setFont('Arial', 'I', 11);
*/

class PerfilCandidatoAdmin extends TPage {
  private $datagrid;
  private $pageNavigation;
  private $candidato;
  private $form;
  private $curriculo;
  private $id; //Apenas para teste
  
  public function __construct(){
    parent::__construct();
    
    if ( TSession::getValue('tipo_usuario') == 'CANDIDATO' || TSession::getValue('tipo_usuario') == NULL) {
        AdiantiCoreApplication::gotoPage('PaginaPrincipalForm');
    }
    
    $this->form = new BootstrapFormBuilder;
    $this->form->Style = 'width : 100%';
      
    /*Pega os dados com _GET*/
    if( array_key_exists('idcandidato',$_GET) ){
     $this->id = $_GET['idcandidato'];
     //Criar Sessão
     TSession::setValue('idcandidato', $this->id); 
    }
    else
      $this->id = null;
      
    //Pega os dados da url
    if($this->id == null){
      $this->id = TSession::getValue('idcandidato');                                                                                                                                                                                                             
    }
      
    try{
      TTransaction::open('con_ultimato');
      $this->candidato = new Candidato( $this->id );
      //Verificando o tipo de Usuario
      if(TSession::getValue('tipo_usuario') == 'CANDIDATO' || ! TSession::getValue('tipo_usuario')){
        new TMessage('warning', 'É preciso estar logado como administrador!', new TAction( [$this, 'redirecionar'] ));
        
        AdiantiCoreApplication::gotoPage('PaginaPrincipalForm');
      }
      
    
    // =============================================== FOTOS ======================================================
      $tablefoto = new TTable;
      
      $tablefoto->width = '15%';
      $tablefoto->height = '100%';
      $tablefoto->style= "float :left";
      
      $col_foto = $this->candidato->foto;
      
      $row = $tablefoto->addRow();
      $image = new TImage($col_foto);
      $image->style = 'max-width: 300px';
      $row->addCell($image);
       
    //=============================================== AÇÕES ======================================================
      
      $button1 = new TButton('baixar');
      $button1->setAction(new TAction(array($this, 'onCurriculoToPdf')), 'Imprimir Curriculo');
      $button1->setImage('fa:download black');
      $button1->style = 'padding : 1.5%; font-size : 15px';
      //Botões só funciona com formularios
      
      $tablebutton = new TTable;
      
      $tablebutton->style='width:100%;height:100%;';
      $tablebutton->addRowSet( $button1 ); 
      $form1 = new TForm('form-botao');
      $form1->add($tablebutton);
      $form1->setFields(array($button1));
      
      //Juntando foto e butãos
      $tablefoto->addRowSet($form1);
      
    //=============================================== DADOS PESSOAIS ======================================================
      $cidade = new Cidade($this->candidato->cidade_id);
      $fone = empty($this->candidato->fone) ? 'Não possui' : $this->candidato->fone;
      
      $pg = new TPanelGroup('Dados Pessoais');
      
      $table = new TTable;
      $table->addRowset('Nome ', $this->candidato->nome ? $this->candidato->nome : ' ',                                   'Email', $this->candidato->email ? $this->candidato->email : ' ',                                               'Cpf', $this->candidato->cpf ? $this->candidato->cpf : ' ');
      $table->addRow()->addCell('<br>');
      $table->addRowset('Nome do Pai', $this->candidato->nomePai ? $this->candidato->nomePai : ' ',                       'Nome da Mãe', $this->candidato->nomeMae ? $this->candidato->nomeMae : ' ',                                     'Rg', $this->candidato->rg ? $this->candidato->rg : ' ');
      $table->addRow()->addCell('<br>');
      $table->addRowset('Data de Nascimento ', $this->candidato->dataNascimento ? $this->candidato->dataNascimento : ' ', 'Sexo', $this->candidato->sexo ? $this->candidato->sexo : ' ',                                                  'Cep', $this->candidato->cep ? $this->candidato->cep : ' ');
      $table->addRow()->addCell('<br>');
      $table->addRowset('Endereço ', $this->candidato->endereco ? $this->candidato->endereco : ' ',                       'Estado Civil', $this->candidato->estadoCivil ? $this->candidato->estadoCivil : ' ',                            'Telefone', $this->candidato->telefone ? $this->candidato->telefone : ' ');
      $table->addRow()->addCell('<br>');
      $table->addRowset('Cidade ', $cidade->nome_cidade ? $cidade->nome_cidade : ' ',                                    'Estado  ', (new Estado($cidade->estado_id))->nome_estado ? (new Estado($cidade->estado_id))->nome_estado : ' ', '2° Telefone', $fone);
      $table->addRow()->addCell('<br>');
      
      //Verifica se possui deficiencia
      $row = $table->addRow();
      $row->addCell('Deficiencia ');
      $col = !empty($this->candidato->tipoDeficiencia) ? $this->candidato->tipoDeficiencia : 'Não Possui';
      $row->addCell($col);
      
      //Verifica se o complemento é nulo
      $row->addCell('Complemento');
      $col = !empty($this->candidato->complemento) ? $this->candidato->complemento : 'Não Especificado';
      $row->addCell($col);
      $row->addCell('Numero da Casa');
      $row->addCell($this->candidato->numeroCasa ? $this->candidato->numeroCasa : '');
      $table->width = '100%';      
      
      $pg->add($table);
      
      //Juntando foto com dados pessoais
      $fotoedados = new TTable;
      $fotoedados->width = '100%';
      $t = $fotoedados->addRow();
      $tablefoto->style = 'text-align : center';
      $fotoedados->addRowSet($tablefoto,$pg);
      
    //===================================================== CURRICULO =================================================================
      $this->curriculo = $this->candidato->curriculo;
      
      //================= Pretensão Salarial e Area de Interesse =======================//
      $pg1 = new TPanelGroup('Pretensão Salarial e Area de Interesse');
      $table =  new TTable;
      $row = $table->addRow();
      
      if ($this->curriculo->pretensaoSalarial){        
        $row->addMultiCell('Pretensão Salarial', $this->curriculo->pretensaoSalarial);
      }
      else{
        $row->addMultiCell('Pretensão Salarial', '0');
      }
      
      $row->addCell('Area de Interesse');
      if ($this->curriculo->getAreaDeInteresses()){
        foreach($this->curriculo->getAreaDeInteresses() as $areaInt){
          $row->addCell($areaInt->descricao);
        }
      }
      else{
        $row->addCell('Não tem area de interesse');
      }
      
      $table->width = '100%';
      $pg1->add($table);
        
      //============================ Cursos Complementar ================================//
      $pg2 = new TPanelGroup('Cursos Complementar');
      $table =  new TTable;
      
      if ( $this->curriculo->getCursosComplementares() ){
        foreach($this->curriculo->getCursosComplementares() as $cc){
          $row1 = $table->addRow();
          $row1->addCell('Nome: '); 
          $row1->addCell( $cc->decricao ? $cc->decricao : ' '); //AL
          $row1->addCell('Instituiçao: ');
          $row1->addCell( $cc->instituicao ? $cc->instituicao : ' ');
          $row1->addCell('Carga Horaria: ');
          $row1->addCell( $cc->cargaHoraria ? $cc->cargaHoraria.'h' : ' ');
          $row1->addCell('Começou em: ');
          $row1->addCell( $cc->dataComeco ? $cc->dataComeco : ' ');
          $row1->addCell('Terminou em: '); 
          $row1->addCell( $cc->dataFim ? $cc->dataFim : ' ');
        }
      }
      else{
        $row1 = $table->addRow();
        $row1->addCell('Não tem cursos complementares');
      }
        
      $table->width = '100%';
      $pg2->add($table);
      
      
    //============================ Experiencia Profissional ================================//
      $pg3 = new TPanelGroup('Experiencia Profissional');
      $table =  new TTable;
       
      if($this->curriculo->getExperienciaProfissionals()){             
        foreach($this->curriculo->getExperienciaProfissionals() as $ep){
          $row1 = $table->addRow();
          
          $row1->addCell('Cargo: ');
          $row1->addCell( $ep->cargo ? $ep->cargo : ' ');
          $row1->addCell('Empresa: ');
          $row1->addCell($ep->empresa ? $ep->empresa : ' ');
          $row1->addCell('Começou em: ');
          $row1->addCell($ep->dataComeco ? $ep->dataComeco : ' ');
          $row1->addCell('Terminou em: ');
          $row1->addCell($ep->dataFim ? $ep->dataFim : ' ');
          
          $row2 = $table->addRow();
          $row2->addCell('Detalhe:');
          $row2->addCell($ep->detalhes ? $ep->detalhes : ' ');          
        }  
      }
      else{
        $row1 = $table->addRow();
        $row1->addCell('Não tem experiência profissional');
      }
      
      $table->width = '100%';
      $pg3->add($table);
      
    //============================ Formação Academica ================================//
      $pg4 = new TPanelGroup('Formação Academica');
      $table =  new TTable;
      
      if($this->curriculo->getFormacaos()){        
        foreach($this->curriculo->getFormacaos() as $f){
          $row1 = $table->addRow();
          
          $row1->addCell('Curso: ');
          $row1->addCell($f->cursos->nome ? $f->cursos->nome : ' ');
          $row1->addCell('Instituição: ');
          $row1->addCell($f->instituicao ? $f->instituicao : ' ');
          $row1->addCell('Grau de Escolaridade: ');
          $row1->addCell($f->grauDeEscolaridade ? $f->grauDeEscolaridade : ' ');
          $row1->addCell('Começou em: ');
          $row1->addCell($f->dataComeco ? $f->dataComeco : ' ');
          $row1->addCell('Terminou em: ');
          $row1->addCell($f->dataFim ? $f->dataFim : ' ');
        }  
      }
      else{
        $row1 = $table->addRow();
        $row1->addCell('Não tem formação acadêmica');
      }
      
      $table->width = '100%';
      $pg4->add($table);
      
    //============================ Idioma ================================// 
      $pg5 = new TPanelGroup('Idioma');
      $table =  new TTable;
      
      if($this->curriculo->getNivel_Idiomas()){
        foreach($this->curriculo->getNivel_Idiomas() as $ni){
          $row1 = $table->addRow();
          $row1->addCell('Idioma: ');
          $row1->addCell($ni->idioma->nome ? $ni->idioma->nome : ' ');
          $row1->addCell('Nivel: ');
          $row1->addCell($ni->nivel ? $ni->nivel : ' ');
          $row1 = $table->addRow();
          $row1->addCell('<br>');
        }
      }
      else{
        $row1 = $table->addRow();
        $row1->addCell('Não dominio em outros idiomas');
      }
      
      $table->width = '100%';
      $pg5->add($table);
       
       
    //===================================================== PÁGINA LAUDOS =================================================================
        
      $button2 = new TButton('baixarLaudo');
      $action2 = new TAction(array($this, 'baixarLaudo'));
      $action2->setParameters([ 'idcandidato' => $this->id ]);
      $button2->setAction($action2, 'Baixar todos os laudos');
      $button2->setImage('fa:download');
      $button2->style = ' padding : 2%;font-size : 15px; margin : 1%';
      //Botões só funciona com formularios
        
      $tablebutton = new TTable;
      $tablebutton->style='width:35%;height:100%;margin : 2% 0';
      $tablebutton->addRowSet( $button1,$button2 ); 
      $form1 = new TForm('form-botao');
      $form1->add($tablebutton);
      $form1->setFields(array($button1, $button2));
        
      //Datagrid de laudo
      $panel = new TPanelGroup('Laudos');
      $this->datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
      $this->datagrid->width = '60%';
         
      //add as colunas
      $col_cand = $this->datagrid->addQuickColumn('Candidato','candidato_id', 'center');
      $this->datagrid->addQuickColumn('Vaga',    'vaga->titulo', 'center');
      $this->datagrid->addQuickColumn('Detalhes', 'descricao', 'left');
        
      //Add açôes no datagrid

      $action1 = new TDataGridAction(array($this, 'onEdit'), ['id' => '{id}']);
      $action1->setUseButton(TRUE);
      $action1->setButtonClass('btn btn-default');
      $action1->setImage('fa:edit blue');
      $action1->setParameters( ['idcandidato' => $this->id] );
      $action1->setDisplayCondition( array($this, 'botaoCondicao') );
        
      $action2 = new TDataGridAction(array($this, 'onDelete'), ['id' => '{id}']);
      $action2->setUseButton(TRUE);
      $action2->setButtonClass('btn btn-default');
      $action2->setImage('fa:trash red');
      $action2->setParameters( ['idcandidato' => $this->id] );
      $action2->setDisplayCondition( array($this, 'botaoCondicao') );
      
      $action3 = new TDataGridAction(array($this, 'BaixarUMLaudo'), ['id' => '{id}']);
      $action3->setUseButton(TRUE);
      $action3->setButtonClass('btn btn-default');
      $action3->setImage('fa:download blue');
      $action3->setParameters( ['laudo' => $this->id] );

      //Botão cadastra novo laudo
      $action = new TDataGridAction(array($this, 'onNewLaudo'), ['id' => '{id}']);
      $action->setButtonClass('btn btn-default');
      $action->setUseButton(TRUE);
      $action->setImage('fa:plus-square green');
      $action->setParameters(['param' => $this->candidato->nome, 'idcandidato' => $this->id]);
      $action->setDisplayCondition( array($this, 'botaoCondicao') );

      $this->datagrid->addQuickAction('Novo', $action, 'id');
      $this->datagrid->addQuickAction('Apagar', $action2, 'id');
      $this->datagrid->addQuickAction('Editar', $action1, 'id');
      $this->datagrid->addQuickAction('Baixar', $action3, 'id');

      // add the actions to the datagrid
        //$this->datagrid->addActionGroup($action_group);      
        
      //Mudando a coluna candidato 
      $col_cand->setTransformer( function($candid){
        $candid = $this->candidato->nome;
        return $candid;
      });
      $this->datagrid->createModel();
         
      //Criação do PageNavigation
      $this->pageNavigation = new TPageNavigation;
      $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
      $this->pageNavigation->setWidth($this->datagrid->getWidth());
         
      $panel->add( TPanelGroup::pack('',$this->datagrid,$this->pageNavigation) );
         
    //==================================== PÁGINA REPUTAÇÃO ======================================================================
    
      $img = new TImage($this->candidato->foto);
      $img->style = 'max-width: 300px; margin : 1.5%';
      
      $formrepu = new BootstrapFormBuilder;
      $formrepu->setFormTitle('Reputação');
      $formrepu->addAction('Salvar', new TAction(array($this, 'salvarreputacao')), 'fa:check-circle-o green');
      
      //Caixa de Seleção
      $radio = new TRadioGroup('reputacao');
      $items = ['1' => 'Otimo', '2' => 'Bom', '3' => 'Regular', '4' => 'Ruim', '5' => 'Pessimo'];
      $radio->addItems($items);
      $radio->setValue( $this->candidato->reputacao );
      $formrepu->addFields( [$radio] );
      
      //colocar as opções vertical
      $formrepu->style = 'width : 100%';
       
      TTransaction::close();      
    }
    catch(Exception $e){    
      new TMessage('error', $e->getMessage());
    }
      
  //====================================== Juntando tudo em três telas de notebook ====================================================
    $vbox = new TVBox;
    $vbox->style = 'width: 80%';
    $vbox->add($pg1); //Pretensão Salarial e Area de Interesse
    $vbox->add($pg5); //Idioma
    
    //Pagina 1) curriculo
    $page1 = new TTable;
    $page1->width = '100%';
    $page1->addRowset($fotoedados);
    $page1->addRowset($vbox);
    $page1->addRowset($pg2);
    $page1->addRowset($pg3);
    $page1->addRowset($pg4);
      
    //Pagina 2) Laudo
    $page2 = new TTable;
    $page2->width = '100%';
    $page2->addRowSet($form1);
    $page2->addRowSet($panel);
      
    //Pagina 3) Avaliação
    $page3 = new TTable;
    $page3->addRowSet($img);
    $page3->addRowSet($formrepu);
      
    // Criando o notebook
    $notebook = new BootstrapNotebookWrapper( new TNotebook(600,400) );
    
    // adds two pages in the notebook
    $notebook->appendPage('Curriculo', $page1);
    $notebook->appendPage('Laudo', $page2);
    $notebook->appendPage('Reputação', $page3);
      
    parent::add($notebook);
  }        
  //============================================ MÉTODOS ===========================================================================
  
  public function baixarLaudo($param){
      try{
          TTransaction::open('con_ultimato');        
         $laudos = $this->candidato->getLaudos();
         
         //Iniciar o fpdf
         $pdf = new FPDF('P','cm', 'A4');
         $pdf->addPage();
         //Header
         $pdf->setFont('Arial','B',16);
         $pdf->Cell(2,2,' Todos os Laudos' ,0,1,'L');//Largura,Altura,valor,borda 0) Não, pular linha 1) Sim, font-align L left
         $pdf->Image( realpath("app/images/logoPreta.png") , 14, 1.5, 2, 1,'PNG');//x, y, Largura, altura, type, url  
         //$pdf->Ln(1);      
         //Tabela
         $pdf->setFont('Arial','',9);
         $pdf->cell(9,1,'Candidato',1,0, 'C');
         $pdf->cell(9,1,'Vaga',1,0, 'C');
         $pdf->ln();
         foreach($laudos as $laudo){
             $pdf->cell(9,1, utf8_decode($this->candidato->nome),1,0, 'C');
             $pdf->cell(9,1, utf8_decode($laudo->vaga->titulo),1,0, 'C');
             $pdf->ln();
            $pdf->Cell(3,1, utf8_decode('Descrição'),1,0,'C');
            $pdf->Cell(15,1, utf8_decode($laudo->descricao),1,0,'C');
             $pdf->Ln();
         }        
         $pdf->output('app/output/teste.pdf');
         parent::openFile('app/output/teste.pdf');         
         TTransaction::close();
      }catch(Exception $e){
          new TMessage('error', $e->getMessage());
      }
  }

  //Baixa apenas um laudo
  public function baixarUMLaudo($param){
      try{
          TTransaction::open('con_ultimato');        
         $laudo = new Laudo($param['id']);
         
         //Iniciar o fpdf
         $pdf = new FPDF('P','cm', 'A4');
         $pdf->addPage();
         //Header
         $pdf->setFont('Arial','B',16);
         $pdf->Cell(2,2,' Todos os Laudos' ,0,1,'L');//Largura,Altura,valor,borda 0) Não, pular linha 1) Sim, font-align L left
         $pdf->Image( realpath("app/images/logoPreta.png") , 14, 1.5, 2, 1,'PNG');//x, y, Largura, altura, type, url  
         //$pdf->Ln(1);      
         //Tabela
         $pdf->setFont('Arial','',9);
         $pdf->cell(9,1,'Candidato',1,0, 'C');
         $pdf->cell(9,1,'Vaga',1,0, 'C');
         $pdf->ln();
        
         $pdf->cell(9,1, utf8_decode($this->candidato->nome),1,0, 'C');
         $pdf->cell(9,1, utf8_decode($laudo->vaga->titulo),1,0, 'C');
         $pdf->ln();
         $pdf->Cell(3,1, utf8_decode('Descrição'),1,0,'C');
         $pdf->Cell(15,1, utf8_decode($laudo->descricao),1,0,'C');
         $pdf->Ln();
         $pdf->output('app/output/teste.pdf');
         parent::openFile('app/output/teste.pdf');         
         TTransaction::close();
      }catch(Exception $e){
          new TMessage('error', $e->getMessage());
      }
  }

 public function onCurriculoToPdf(){

    try{
      TTransaction::open('con_ultimato');

      $fone            = empty($this->candidato->fone) ? utf8_decode('Não possui') : $this->candidato->fone;//Caso seja Nulo
      $complemento     = empty($this->candidato->complemento) ? utf8_decode('Não possui') : $this->candidato->complemento;//Caso seja Nulo
      $deficiencia     = !empty($this->candidato->tipoDeficiencia) ? $this->candidato->tipoDeficiencia : utf8_decode('Não possui');
      $cidade          = new Cidade( $this->candidato->cidade_id );
      $estado          = new Estado( $cidade->estado_id );
      $this->curriculo = $this->candidato->curriculo;

      $pdf = new FPDF('P','cm', 'A4');
      $pdf->addPage();
      $pdf->setMargins(1,4,1);//Define a margin left, top, right
      //Header
      $pdf->setFont('Arial', 'B', 16);
      $pdf->Cell(20,2, $this->candidato->nome, 0,1,'C');
      $pdf->line(1,3,20,3);
      //Dados Pessoais
      $pdf->setFont('Arial', 'I', 13);
      $pdf->Cell(20,2, 'Dados Pessoais', 0,1,'C');
      $pdf->line(1,5,20,5);  
      $pdf->setFont('Arial', '', 9);
      $pdf->Cell(6,1, 'Nome do Pai: '. utf8_decode($this->candidato->nomePai ? $this->candidato->nomePai : ''), 0,0,'L');
      $pdf->Cell(6,1, utf8_decode('Nome da Mãe: '). utf8_decode($this->candidato->nomeMae ? $this->candidato->nomeMae : ''), 0,0,'L');
      if($this->candidato->foto)
       $pdf->image(realpath($this->candidato->foto) , 16, 5.2, 4, 4.5,'JPG');//x, y, Largura, altura, type, url  
      $pdf->ln();

      $pdf->Cell(6,1, 'Email: '. utf8_decode($this->candidato->email ? $this->candidato->email : ''),0,0,'L');
      $pdf->Cell(6,1, 'Telefone: '. ($this->candidato->telefone ? $this->candidato->telefone : ''), 0,0,'L');
      $pdf->ln();

      $pdf->Cell(6,1, 'RG: '. ($this->candidato->rg ? $this->candidato->rg  : ''), 0,0,'L');
      $pdf->Cell(6,1, 'CPF: '. ($this->candidato->cpf ? $this->candidato->cpf : ''), 0,0,'L');
      $pdf->ln();
        
      $pdf->Cell(6,1, utf8_decode('2°Telefone: '. utf8_decode($fone)), 0,0,'L');
      $pdf->Cell(4.5,1, 'Estado Civil: '. utf8_decode($this->candidato->estadoCivil ? $this->candidato->estadoCivil : ''));
      $pdf->Cell(3,1, 'Sexo: '. ($this->candidato->sexo ? $this->candidato->sexo : ''));
      $pdf->ln();

      $pdf->Cell(6,1, utf8_decode('Deficiencia: '. $deficiencia));
      $pdf->Cell(5,1, utf8_decode('Data de Nascimento: '). ($this->candidato->dataNascimento ? $this->candidato->dataNascimento : ''));
      $pdf->Cell(5,1, 'Estado: '. utf8_decode(  $estado->nome_estado ? $estado->nome_estado : '' ) );
      $pdf->ln();

      $pdf->Cell(6,1, utf8_decode('Endereço: '. ($this->candidato->endereco ? $this->candidato->endereco : '')));
      $pdf->Cell(5,1, utf8_decode('Complemento: '. utf8_decode($complemento ? $complemento : '')));
      $pdf->Cell(5,1, 'Numero de Casa: '. ($this->candidato->numeroCasa ? $this->candidato->numeroCasa : ''));
      $pdf->line(1,11,20,11);

      //Pretensão e Area de Atuação
      $pdf->ln();
      $pdf->setFont('Arial', 'I', 11);
      $pdf->Cell(9,1, 'Objetivos', 0,0,'C');
      $pdf->Cell(9,1, utf8_decode('Pretensão Salarial'), 0,0,'C');
      
      $pdf->ln();
      $pdf->setFont('Arial', '', 9);
      if ($this->curriculo->getAreaDeInteresses()){
        $pdf->setFont('Arial', '', 9);
        $j = 1;
        $c = '';
        foreach($this->curriculo->getAreaDeInteresses() as $areaInt){
          $c .= '  '. $areaInt->descricao;
          if($c > 36*$j){
            $c .= '<br>';
            $j++;
          }
        }
        $pdf->Cell( 10 ,1,utf8_decode($c),0,0,'C');
      }
      else{
        $pdf->Cell(9,1,'Não tem area de interesse',0,0,'C');
      }

      //Pretensão Salarial
      if ( !empty($this->curriculo->pretensaoSalarial) ){
        $pdf->setFont('Arial', '', 9);        
        $pdf->Cell(8,1, utf8_decode('Media Salarial R$ '). $this->curriculo->pretensaoSalarial ,0,0,'C');
      }
      else{
        $pdf->Cell(9,1, utf8_decode('Media Salarial  R$ 0'),0,0,'C');
      }
      $pdf->ln();
      $pdf->Line(10,11.1,10,14);//Linha vertical

      //Cursos Complementares
      $pdf->line(1,14.1,20,14.1);
      $pdf->ln();
      $pdf->setFont('Arial', 'I', 12);
      $pdf->Cell(18,1, 'Cursos Complementares', 0,1,'C');
      $cursosComp = $this->curriculo->getCursosComplementares();
      if ( $cursosComp ){
         $pdf->setFont('Arial', '', 9);
         $pdf->cell(5,1,'Nome',1,0, 'C');
         $pdf->cell(5,1,utf8_decode('Instituição '),1,0, 'C');
         $pdf->cell(3,1, utf8_decode('Carga Horaria'),1,0, 'C');
         $pdf->cell(3,1,utf8_decode('Começou em'),1,0, 'C');
         $pdf->cell(3,1,utf8_decode('Terminou em'),1,0, 'C');

         $pdf->ln();
         $i = count($cursosComp) + 2;//Somando as pontas
        foreach($cursosComp as $cc){
          $pdf->Cell(5,1, utf8_decode($cc->decricao ? $cc->decricao : ''), 1,0,'C');
          $pdf->Cell(5,1,utf8_decode( $cc->instituicao ? $cc->instituicao : ''),1,0,'C' );
          $pdf->Cell(3,1, ($cc->cargaHoraria ? $cc->cargaHoraria : ''),1,0,'C');
          $pdf->Cell(3,1, ($cc->dataComeco ? $cc->dataComeco : ''),1,0,'C');
          $pdf->Cell(3,1, ($cc->dataFim ? $cc->dataFim : ''),1,0,'C'); 
          $pdf->ln();
        }
        $pdf->line(1,( 15+$i) ,20,( 15+$i ));
      }
      else{
        $pdf->Cell(20,1,utf8_decode('Não tem cursos complementares'),0,0,'C');
        $pdf->ln();
        $i = 2;
       $pdf->line(1,( 15+$i) ,20,( 15+$i ));
      }
      
      //Experiencia Profissional
      $pdf->ln();
      //$pdf->line(1,18,20,18);
      $pdf->setFont('Arial', 'I', 12);
      $pdf->Cell(18,1, 'Experiencia Profissional', 0,1,'C');
      $exp = $this->curriculo->getExperienciaProfissionals();
      if ( $exp ){
         $pdf->setFont('Arial', '', 9);
         $pdf->cell(5,1,'Cargo',1,0, 'C');
         $pdf->cell(5,1, 'Empresa ',1,0, 'C');
         $pdf->cell(4.5,1,utf8_decode('Começou em'),1,0, 'C');
         $pdf->cell(4.5,1,utf8_decode('Terminou em'),1,0, 'C');
         $i+= count($exp) * 2 + 2;//Vai somar para as linhas ficarem corretas caso tenha mais de um valor e caso não tenha valor
         $pdf->ln();
        foreach($exp as $ep){
          $pdf->Cell(5,1, utf8_decode($ep->cargo ? $ep->cargo : ''), 1,0,'C');
          $pdf->Cell(5,1,utf8_decode( $ep->empresa ? $ep->empresa : ''),1,0,'C' );
          $pdf->Cell(4.5,1, ($ep->dataComeco ? $ep->dataComeco : ''),1,0,'C');
          $pdf->Cell(4.5,1, ($ep->dataFim ? $ep->dataFim : ''),1,0,'C'); 
          $pdf->ln();
          $pdf->Cell(3,1, 'Detalhes',1,0,'C');
          $pdf->Cell(16,1, utf8_decode($ep->detalhes ? $ep->detalhes : ''),1,0,'C');
          $pdf->ln();
        }
        $pdf->line(1,(16+$i),20,(16+$i));
      }
      else{
        $pdf->Cell(20,1,utf8_decode('Não tem experiência Profissionais'),0,0,'C');
        $i+= 2;
        $pdf->line(1,(16+$i),20,(16+$i));
        $pdf->ln();
      }
      $i = $i+17; //3 da margem da pagina
        $k = 17;
        if($i > 27){
          $i-=27;
          print('Primeiro'.$i);
          $i += 4;//margin top
          $k = 0;
        }

      //Idioma
      $pdf->ln();
      $pdf->setFont('Arial', 'I', 12);
      $pdf->Cell(20,1, 'Idioma', 0,1,'C');
      $nidioma = $this->curriculo->getNivel_Idiomas();
      if ( $nidioma ){
         $pdf->setFont('Arial', '', 9);
         $pdf->Cell(5,1,'',0,0,'C');
         $pdf->cell(5,1,'Idioma',1,0, 'C');
         $pdf->cell(5,1, 'Nivel ',1,0, 'C');
         $pdf->ln();
         $i+= count($nidioma) + 2;
        foreach($nidioma as $ep){
          $pdf->Cell(5,1,'',0,0,'C');
          $pdf->Cell(5,1, utf8_decode($ep->idioma->nome ? $ep->idioma->nome : ''), 1,0,'C');
          $pdf->Cell(5,1,utf8_decode( $ep->nivel ? $ep->nivel : ''),1,0,'C' );
          $pdf->ln();
        }
        //Verifica se tem outra pagina
       
        if($i > 27){
          $i-=27;
          $i += 4;//margin top
          $k = 0;
          print('Segundo'.$i);
        }
        $pdf->line(1,$i,20,$i);
      }
      else{
        $pdf->Cell(20,1,'Apenas Portugues Brasileiro',0,0,'C');
        $i+= 2;
        $pdf->ln();
        if($i > 27){
          $i-=27;
          $i+=4;
          $k = 0;
        }

        $pdf->line(1,$i,20,$i);
      }

      //Formação Academica
      $pdf->ln();
     // $pdf->line(1,6,20,6);
      $pdf->setFont('Arial', 'I', 12);
      $pdf->Cell(18,1, utf8_decode('Formação Academica'), 0,1,'C');
      $forma = $this->curriculo->getFormacaos();
      if ( $this->curriculo->getFormacaos() ){
          $i += count($forma) + 2;
         $pdf->setFont('Arial', '', 9);
         $pdf->cell(5,1,'Curso',1,0, 'C');
         $pdf->cell(4,1,utf8_decode('Instituição'),1,0, 'C');
         $pdf->cell(4,1,utf8_decode('Grau de Escolaridade '),1,0, 'C');
         $pdf->cell(3,1,utf8_decode('Começou em'),1,0, 'C');
         $pdf->cell(3,1,utf8_decode('Terminou em'),1,0, 'C');

         $pdf->ln();
        foreach($this->curriculo->getFormacaos() as $cc){
          $pdf->Cell(5,1, utf8_decode($cc->cursos->nome ? $cc->cursos->nome : ''), 1,0,'C');
          $pdf->Cell(4,1,utf8_decode( $cc->instituicao ? $cc->instituicao : ''),1,0,'C' );
          $pdf->Cell(4,1, ($cc->grauDeEscolaridade ? $cc->grauDeEscolaridade : ''),1,0,'C');
          $pdf->Cell(3,1, ($cc->dataComeco ? $cc->dataComeco : ''),1,0,'C');
          $pdf->Cell(3,1, ($cc->dataFim ? $cc->dataFim : ''),1,0,'C'); 
          $pdf->ln();
        }
      }else{
        $pdf->Cell(20,1,utf8_decode('Não possui formações Academicas'),0,0,'C');
        $i+= 2;
      }

      //Verifica se tem outra pagina
        
        if($i > 27){
          $i-=27;
          $i += 4;// 4 margin top
          print('Terceiro'.$i);

        }

      //Footer
        //print 'Imagem'. $i;
      $pdf->ln();
      $pdf->setFont('Arial', 'I', 12);
      $pdf->Cell(10,1,'Curriculo feito por Flash Curriculos',0,0,'C');
       $pdf->Image( realpath("app/images/logoPreta.png") , 11, $i+1, 3, 1,'PNG');//x, y, Largura, altura, type, url

      //Execução
      $pdf->output('app/output/teste.pdf');
     parent::openFile('app/output/teste.pdf');       

      TTransaction::close();
     }catch(Exception $e){
         new TMessage('error', $e->getMessage() );
     } 
  }
  
  public function salvarreputacao($param){
    if(TSession::getValue('tipo_usuario') == 'FUNCIONARIO'){
      new TMessage('error','Só administradores podem salvar e editar reputação ');
    }
    else{
      try{
        TTransaction::open('con_ultimato');
         
        if($this->candidato->reputacao != $param['reputacao']){
          $this->candidato->reputacao = $param['reputacao'];
          $this->candidato->store();
        }
        new TMessage('info', 'Reputação salvo com sucesso!');
         
        TTransaction::close();
      }
      catch(Exception $e){
        new TMessage('error', $e->getMessage());
      }
    }    
  }
  
  public function botaoCondicao(){
    $aux = TSession::getValue('tipo_usuario') == 'FUNCIONARIO' ? FALSE : TRUE;
    return $aux;
  }
  
  public function onNewLaudo($param){
    $id        = new TLabel('Id: ');
    $v         = new TLabel('Vaga: ');
    $vaga      = new TEntry('vaga');
    $c         = new TLabel('Candidato: ');
    $cand      = new TEntry('candidato');
    $desc      = new TLabel('Descrição: ');
    $descricao = new TText('descricao');
    
    $vaga1 = NULL;
    try{
      TTransaction::open('con_ultimato');
      $laudo = new Laudo($param['id']);
      $vaga1 = $laudo->vaga;
      TTransaction::close();
    }catch(Exception $e){
      new TMessage('error', $e->getMessage());
    }
    //var_dump($vaga1);
    $vaga->setValue($vaga1->titulo);
    $vaga->setSize('70%');
    $cand->setEditable(false);
    $vaga->setEditable(FALSE);
    $id->setEditable(FALSE);
    $cand->setValue($param['param']);
    $cand->setSize('70%');
    $descricao->setSize('100%');
      
    $this->form->addFields([$v], [$vaga]);
    $this->form->addFields([$c], [$cand]);
    $this->form->addFields( [$descricao] );
    $action = new TAction(array($this, 'onSave'));
    //var_dump($param);
    $action->setParameters($param);
    $this->form->addAction('Salva', $action, 'fa:save green');
    new TInputDialog('Novo Laudo', $this->form);      
  }
  
  public function onSave($param){
    
    try{
      TTransaction::open('con_ultimato');
      $laudo = new Laudo($param['id']);
      $laudo->descricao = $param['descricao'];
      $laudo->store();
      TTransaction::close();
    }
    catch(Exception $e){
      new TMessage('error', $e->getMessage());
    }
    
    new TMessage('info','Salvo com Sucesso');    
    $this->onReload();  
  }
  
   public function redirecionar(){
       AdiantiCoreApplication::gotoPage('PaginaPrincipalForm');
  }
  
  public function onEdit($param){
    if ( TSession::getValue('tipo_usuario') == 'CANDIDATO' || TSession::getValue('tipo_usuario') == NULL) {
        AdiantiCoreApplication::gotoPage('PaginaPrincipalForm');
    }
    
    try{
      
      TTransaction::open('con_ultimato');
        $laudo = new Laudo($param['id']);
        $candidato = new Candidato( $laudo->candidato_id);
        $id         = new TLabel('Id: ');
        $cand       = new TEntry('id');
        $v          = new TLabel('Vaga: ');
        $vaga       = new TEntry('vaga');
        $c          = new TLabel('Candidato: ');
        $cand       = new TEntry('candidato');
        $desc       = new TLabel('Descrição: ');
        $descricao  = new TText('descricao');
        

        $vaga1 = NULL;
        $laudo = new Laudo($param['id']);
        $vaga1 = $laudo->vaga;
        //var_dump($vaga1);
        $vaga->setValue($vaga1->titulo);  
        $vaga->setSize('70%');
        $cand->setEditable(false);
        $vaga->setEditable(FALSE);
        $id->setEditable(FALSE);
        $cand->setValue($candidato->nome);
        $cand->setSize('70%');
        $descricao->setValue($laudo->descricao);
        $descricao->setSize('100%');
          
        $this->form->addFields([$v], [$vaga]);
        $this->form->addFields([$c], [$cand]);
        $this->form->addFields( [$descricao] );
        $action = new TAction(array($this, 'onSave'));
        $action->setParameters($param);
        $this->form->addAction('Salva', $action, 'fa:save green');
        new TInputDialog('Editar Laudo', $this->form);
      TTransaction::close();
    }
    catch(Exception $e){
      new TMessage('error', $e->getMessage());
    }
  }
  
  public function onDelete($param){
    $action = new TAction( [$this, 'Delete'] );
    $action->setParameters($param);
    new TQuestion('Deseja excluir o Laudo? ', $action);
  }
      
  public function Delete($param){
    try{
      TTransaction::open('con_ultimato');
      $laudo = new Laudo($param['id']);
      $laudo->descricao = NULL;
      $laudo->store();
      TTransaction::close();
      //Limpar o GET
        
      $this->onReload();
      new TMessage('info', 'Apagado com Sucesso');
    }
    catch(Exception $e){
      new TMessage('error', $e->getMessage());
      TTransaction::rollback();
    }      
  }
  
  public function onReload($param = NULL){
    try{
      TTransaction::open('con_ultimato');
        $this->datagrid->clear();
        $limit = 5;
        //creates a criteria
        $criteria = new TCriteria;
        $criteria->add(new TFilter('candidato_id', '=', $this->candidato->id));
        $criteria->setProperties($param); // order, offset
        $criteria->setProperty('limit', $limit);
        $repository = new TRepository('Laudo');//Pega todos os registro 
        $dados = $repository->load($criteria);
        
        foreach($dados as $dado){
          $this->datagrid->addItem( $dado );
        }
        
        $criteria->resetProperties(); 
        $count= $repository->count($criteria);//Pega o inicio de cada pagina
        $this->pageNavigation->setCount($count); // count of records
        $this->pageNavigation->setProperties($param); // order, page
        $this->pageNavigation->setLimit($limit); // limit
      TTransaction::close();
    }
    catch(Exception $e){
      new TMessage('error', $e->getMessage());
    }
  }
  
  public function show(){
    $this->onReload(func_get_args() );
    parent::show();
  } 
}