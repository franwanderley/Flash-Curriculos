<?php
  //Autor: Lucas
  
  class SelecionarCandidatos extends TPage{
    private $form; // Formulário para os filtros
    private $datagrid; //Para exibiri os resultados dos filtros
    private $pageNavigation; //Para "paginar" os resultados
    private $candidatos;
    private $idCandidatosSelecionados;
    private $formDatagrid;
  
    public function __construct(){
      parent::__construct();
      
      
      if ( TSession::getValue('tipo_usuario') == 'CANDIDATO' || TSession::getValue('tipo_usuario') == NULL) {
        AdiantiCoreApplication::gotoPage('PaginaPrincipalForm');
      }
      
      
      $this->form = new TForm('form');
      $this->formDatagrid = new TForm('formDatagrid');
      
      $table  = new TTable(); // Tabela principal para estruturar/organizar toda a página
      
//CRIAÇÃO DA TABELA/FORMULÁRIO DE FILTROS----------------------------------------------------------------------------------
      $filtros = new TTable(); // Estruturar/Organizar os campos de filtragem
          
    //Criação de label
      $lbl_filtro = new TLabel('Filtros de busca:');
      
    //Configuração de label
      $lbl_filtro->setFontSize(14);
      $lbl_filtro->setFontFace('Arial');
      
    //Criação dos campos de FILTRO
      $estado           = new TEntry('estado');
      $cidade           = new TEntry('cidade');
      $areaDeInteresse  = new TEntry('areaDeInteresse');
      $formacao         = new TEntry('formacao');     
      $idade1           = new TEntry('idade1');
      $op_idade1        = new TCombo('op_idade1');  
      $idade2           = new TEntry('idade2');
      $op_idade2        = new TCombo('op_idade2');    
      $idioma           = new TEntry('idioma');    
      $faixaSalarial    = new TEntry('faixaSalarial');         

      $filtrar          = TButton::create('filtrar', [$this, 'onFilter'], 'Filtrar', 'fa:filter green');
      $selecionar       = TButton::create('selecionar', [$this, 'onSelect'], 'Selecionar', 'fa:check-square green'); 
      
//--------//$actionSelecionar = $selecionar->getAction(); // Vou enviar por sessão /// TESTAR AQUI
      //$actionSelecionar->setParameter('key', $I);
      
    //CONFIGURANDO OS CAMPOS DE FILTRO
      $estado->setSize('90%');
      $estado->placeholder = 'Estado'; 
      
      $cidade->setSize('90%');
      $cidade->placeholder = 'Cidade';
      
      $areaDeInteresse->setSize('90%');
      $areaDeInteresse->placeholder = 'Área de Interesse';
      
      $formacao->setSize('90%');
      $formacao->placeholder = 'Formação';
      
      $idade1->setSize('85%');
      $idade1->placeholder = 'Idade';
      
      $op_idade1->setSize('100%');
      $op_idade1->addItems(['ma' => 'maior', 'me' => 'menor', 'ig' => 'igual']);
      $op_idade1->setValue('ma');
      
      /*$idade2->setSize('85%');
      $idade2->placeholder = 'Idade';
      
      $op_idade2->setSize('100%');
      $op_idade2->addItems(['ma' => 'maior', 'me' => 'menor', 'ig' => 'igual']);
      $op_idade2->setValue('ma');*/
      
      $idioma->setSize('90%');
      $idioma->placeholder = 'Idioma';
      
      $faixaSalarial->setSize('90%');
      $faixaSalarial->placeholder = 'Faixa Salarial';
      
    //Adição dos campos
      $row  = $filtros->addRow();
      $cell = $row->addCell($lbl_filtro);
      $cell->colspan = 2;
      
      $row = $filtros->addRow();
      $cell = $row->addCell($estado);
      $cell->colspan = 2;
      
      $row = $filtros->addRow();
      $cell = $row->addCell($cidade);
      $cell->colspan = 2;
      
      $row = $filtros->addRow();
      $cell = $row->addCell($areaDeInteresse);
      $cell->colspan = 2;
      
      $row = $filtros->addRow();
      $cell = $row->addCell($formacao);
      $cell->colspan = 2;
      
      $row = $filtros->addRow();
      $row->addCell($op_idade1);
      $row->addCell($idade1);
      
      /*$row = $filtros->addRow();
      $row->addCell($op_idade2);
      $row->addCell($idade2);*/
      
      $row = $filtros->addRow();
      $cell = $row->addCell($idioma);
      $cell->colspan = 2;
      
      $row = $filtros->addRow();
      $cell = $row->addCell($faixaSalarial);
      $cell->colspan = 2;

      $filtros->addRowSet($filtrar); //Adição do botão
      $filtros->addRowSet($selecionar); //Adição do botão
      
    //Consfiguração das Tabelas
      $table->width = '100%';
      //$table->border = '1';
      //$table->cellpadding = '200';
      //$filtros->border = '1px';
      $filtros->width = '100%';
      
    //Adição dos campos no formulário
      $this->form->addField($estado);
      $this->form->addField($cidade);
      $this->form->addField($areaDeInteresse);
      $this->form->addField($formacao);
      $this->form->addField($idade1);
      $this->form->addField($op_idade1);
      //$this->form->addField($idade2);
      //$this->form->addField($op_idade2);
      $this->form->addField($idioma);
      $this->form->addField($faixaSalarial);

      $this->form->addField($filtrar); //BOTÃO
      $this->formDatagrid->addField($selecionar); //BOTÃO
//-------------------------------------------------------------------------------------------------------- 
      
//*CRIANDO DATAGRID---------------------------------------------------------------------------------------
      $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid() );
      $this->datagrid->style = 'width: 100%';//*/
      
    //*Criando colunas da datagrid
      $col_id              = new TDataGridColumn('id', 'ID', 'left');
      $col_foto            = new TDataGridColumn('foto', 'Foto', 'left');
      $col_nome            = new TDataGridColumn('nome', 'Nome', 'left');
      $col_dataNascimento  = new TDataGridColumn('dataNascimento', 'Data de nascimento', 'left');
      $col_sexo            = new TDataGridColumn('sexo', 'Sexo', 'left');
      $col_cidade          = new TDataGridColumn('cidade->nome_cidade', 'Cidade', 'left');
      $col_estado          = new TDataGridColumn('cidade->estado->nome_estado', 'Estado', 'left');
      //*/
      
    //Configurando as colunas da DATAGRID
      $col_foto->setTransformer( function($imagem){ //Definindo o método de transformação sobre a imagem
        $imagem = new TImage($imagem);
        $imagem->style = 'max-width: 100px';
        return $imagem;
      });//*/
    
    //Criando e adicionando as Ações 
      //$action1 = new TDataGridAction([$this, 'onCheck'], ['id' => '{id}']);
      //$this->datagrid->addAction($action1, 'Selecionar', 'far:square fa-fw black');
      
      //*
      $action1 = new TDataGridAction( [$this, 'onCheck'] );
      $action1->setLabel('Selecionar');
      $action1->setImage('fa:square fa-fw black');
      $action1->setField('id');
      $this->datagrid->addAction( $action1 );//*/
      
    //*Adicionando colunas na datagrid
      $this->datagrid->addColumn($col_id);
      $this->datagrid->addColumn($col_foto);
      $this->datagrid->addColumn($col_nome);
      $this->datagrid->addColumn($col_dataNascimento);
      $this->datagrid->addColumn($col_sexo);
      $this->datagrid->addColumn($col_cidade);
      $this->datagrid->addColumn($col_estado);
      
      $col_id->setTransformer([$this, 'formatRow'] );
           
    //Criando datagrid em memória
      $this->datagrid->createModel();
//-------------------------------------------------------------------------------------------------------//*      

//*CRIANDO ESTRUTURA DE PAGINAÇÃO-------------------------------------------------------------------------
      $this->pageNavigation = new TPageNavigation; //Método que faz a paginação da datagrid
      $this->pageNavigation->setAction( new TAction( [$this, 'onReload'] ) ); //Sempre que o usuário clicar na paginação a ação de carga da datagrid deve ser executada (onReload)
      
//-------------------------------------------------------------------------------------------------------//*       
     
    //Até aqui foi criado o FORMULÁRIO DE FILTROS, a DATAGRID e o objeto de PAGINAÇÃO dos resultados, agora irá ser feita a adição
    //desses elementos na tabela principal, para que todos sejam organizados na página.
          
    //Adição dos elementos na tabela principal
      $this->form->add($filtros);
      $this->formDatagrid->add($this->datagrid);// teste
            
      $row   = $table->addRow();
      $cell1 = $row->addCell($this->form);
      $cell2 = $row->addCell($this->formDatagrid);
      
      $cell1->width = '20%';
      $cell1->style = 'vertical-align: top'; // Colocar o conteúdo no topo da célula
      $cell2->width = '80%';
      
      $row = $table->addRow();
      $row->addCell('');
      $row->addCell($this->pageNavigation);
      
      parent::add($table);
    }
    
    
    
    
    
    
    
    
//*MÉTODOS / FUNÇÕES --------------------------------------------------------------------------------------- 

//*---------------------------------------------------------------------------------------------------------    
    public function onFilter( $param ){      
      $data = $this->form->getData();
      $this->form->setData($data);
      
    //Limpando variáveis de sessão
      TSession::setValue('SelecionarCandidatos_filtro_estado', null);
      TSession::setValue('SelecionarCandidatos_filtro_cidade', null);
      TSession::setValue('SelecionarCandidatos_filtro_areaDeInteresse', null);
      TSession::setValue('SelecionarCandidatos_filtro_formacao', null);
      TSession::setValue('SelecionarCandidatos_filtro_idade', null);
      TSession::setValue('SelecionarCandidatos_filtro_idioma', null);
      TSession::setValue('SelecionarCandidatos_filtro_faixaSalarial', null);
      
    
    //O FILTRO VAI SER REALIZADO NA TABELA/CLASSE >> CANDIDATO <<   
    //Atribuindo Valores/Filtros para as VARIÁVEIS DE SESSÃO
      
      if ( !empty($data->cidade) ){
        $filter = new TFilter('cidade_id', 'IN', "(SELECT id FROM cidade WHERE nome_cidade LIKE '{$data->cidade}%')" );

        TSession::setValue('SelecionarCandidatos_filtro_cidade', $filter);        
      }
    
      if ( !empty($data->estado) ){
        $filter = new TFilter('cidade_id', 'IN',
                              "(SELECT id FROM cidade WHERE estado_id IN (
                                 SELECT id FROM estado WHERE nome_estado LIKE '{$data->estado}%'
                               ))"
                              );
        TSession::setValue('SelecionarCandidatos_filtro_estado', $filter);
      }
      
      
      if ( !empty($data->areaDeInteresse) ){
        $area = $data->areaDeInteresse;
        
        $filter = new TFilter('curriculo_id', 'IN',
                              "(SELECT curriculo_id FROM area_e_curriculo WHERE areadeinteresses_id IN (
                                  SELECT id FROM area_de_interesse WHERE descricao LIKE '{$area}%'
                              ))"
                              );
        TSession::setValue('SelecionarCandidatos_filtro_areaDeInteresse', $filter);
      }
      
      if ( !empty($data->formacao) ){
        $formacao = $data->formacao;
        
        $filter = new TFilter('curriculo_id', 'IN',
                              "(SELECT id FROM formacao WHERE curso_id IN (
                                  SELECT id FROM cursos WHERE nome LIKE '{$formacao}%'
                             ))");
                             
        TSession::setValue('SelecionarCandidatos_filtro_formacao', $filter);
      }
      
      if ( !empty($data->idade1) ){
        $op1 = '>';
        $idade1 = $data->idade1;
        
        if ( !empty($data->op_idade1) ){
          if ($data->op_idade1 == 'ma'){ //ou Maior
            $op1 = '>';
          }
          else
          if ($data->op_idade1 == 'me'){
            $op1 = '<';
          }
          else
          if ($data->op_idade1 == 'ig'){
            $op1 = '=';
          }
        }
                
        $filter = new TFilter('dataNascimento', 'IN',
                              "(SELECT dataNascimento from candidato c where (
                               TIMESTAMPDIFF(YEAR, c.dataNascimento, CURDATE()) {$op1} {$idade1}
                               ))");
                             
        TSession::setValue('SelecionarCandidatos_filtro_idade', $filter);
      }
      
      if ( !empty($data->idioma) ){
        $idioma = $data->idioma;
        
        $filter = new TFilter('curriculo_id', 'IN',
                              "(SELECT curriculo_id FROM nivel_idioma WHERE idioma_id IN (
                                SELECT id FROM idioma WHERE nome LIKE '{$idioma}%'
                              ))");
                              
        TSession::setValue('SelecionarCandidatos_filtro_idioma', $filter);
      }
      
      if ( !empty($data->faixaSalarial) ){
        $filter = new TFilter('curriculo_id', 'IN', "(SELECT id FROM curriculo WHERE pretensaoSalarial >= {$data->faixaSalarial})");
        
        TSession::setValue('SelecionarCandidatos_filtro_faixaSalarial', $filter);
      }
  
      TSession::setValue('Candidatos_filtro_data', $data); //Guardando todos o valores do formulário em uma ÚNICA variável de sessão
      
      //Passando informações para o pageNavigation através da URL
      $param = [];
      $param['offset'] = 0;
      $param['first_page'] = 1;
      
      $this->onReload( $param ); // executando o método OnReload que vai fazer uso dos filtros criados em sessão e executalos de fato
    }
//----------------------------------------------------------------------------------------------------------*/

//*---------------------------------------------------------------------------------------------------------
    public function onReload($param){
                
      if( TSession::getValue('VagaDetalhesAdmin_vaga_id') ){
        $vagaId = TSession::getValue('VagaDetalhesAdmin_vaga_id');
            
        //Filtrar Candidatos que se candidataram a vaga
        $filter = new TFilter('id', 'IN', "(SELECT candidato_id FROM laudo WHERE vaga_id = {$vagaId})");
        TSession::setValue('SelecionarCandidatos_filtro_vagaId', $filter);
      }
      
      try{
        TTransaction::open('con_ultimato');
        
          $repository = new TRepository('Candidato');
          $limit = 5;
          
          $criteria = new TCriteria();
          $criteria->setProperty('limit', $limit);
          $criteria->setProperties( $param ); //Lê a URL e extrai as informações de paginação (limit, ofset etc) e joga para dentro do critério que vai ser usado para carregar os registros da base de dados
          
        //*CAPTURANDO OS FILTROS GUARDADOS EM SESSÃO---------------------------------------------------------
          if( TSession::getValue('SelecionarCandidatos_filtro_vagaId') ){
            $criteria->add( TSession::getValue('SelecionarCandidatos_filtro_vagaId') );
          }
          
          if( TSession::getValue('SelecionarCandidatos_filtro_estado') ){
            $criteria->add( TSession::getValue('SelecionarCandidatos_filtro_estado') );
          }
          
          if( TSession::getValue('SelecionarCandidatos_filtro_cidade') ){
            $criteria->add( TSession::getValue('SelecionarCandidatos_filtro_cidade') );
          }
          
          if( TSession::getValue('SelecionarCandidatos_filtro_areaDeInteresse') ){
            $criteria->add( TSession::getValue('SelecionarCandidatos_filtro_areaDeInteresse') );
          }
          
          if( TSession::getValue('SelecionarCandidatos_filtro_formacao') ){
            $criteria->add( TSession::getValue('SelecionarCandidatos_filtro_formacao') );
          }
          
          if( TSession::getValue('SelecionarCandidatos_filtro_idade') ){
            $criteria->add( TSession::getValue('SelecionarCandidatos_filtro_idade') );
          } 
          
          if( TSession::getValue('SelecionarCandidatos_filtro_idioma') ){
            $criteria->add( TSession::getValue('SelecionarCandidatos_filtro_idioma') );
          }
          
          if( TSession::getValue('SelecionarCandidatos_filtro_faixaSalarial') ){
            $criteria->add( TSession::getValue('SelecionarCandidatos_filtro_faixaSalarial') );
          }   
          
        //---------------------------------------------------------------------------------------------------//*/
          
          $objetos = $repository->load($criteria, false); // Faz a busca utilizando os filtros captudados
          
          $this->datagrid->clear();               
                  
          if ($objetos){
            $this->datagrid->disableDefaultClick();
            
            foreach ($objetos as $candidato){
              $this->datagrid->addItem($candidato);        
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

//*---------------------------------------------------------------------------------------------------------    
    public function onSelect(){
      $vaga_id = TSession::getValue('VagaDetalhesAdmin_vaga_id');
      $candidatos_selecionados = TSession::getValue(__CLASS__.'_selected_objects');
      
      foreach($candidatos_selecionados as $key => $candidato){     
        $this->idCandidatosSelecionados[] = $key;
      }
      
      // Eu quero carregar todos os candidatos seleciondados 
      // X = lista de candidatos selecionados
      // Y = Selecionar todos os laudos da vaga em questão
      // 
      // Verificar todos os laudos 
      // Para cada Laudo verificar todos candidatos
      try{
        TTransaction::open('con_ultimato');
          //$repository = new TRepository('Laudo');
          //$selecionados = $repository->where('vaga_id', '=', $vaga_id)->where('')load;
          //Criar o array
          //$laudosseslecionados = [];
          
          if (isset($this->idCandidatosSelecionados)) {
            
            foreach($this->idCandidatosSelecionados as $idCSel){
              $repos = new TRepository('Laudo');
              $sel = $repos->where('candidato_id','=',$idCSel)->where('vaga_id','=',$vaga_id)->load();
              $sel[0]->selecionados = true;
              $sel[0]->store();
              //var_dump($sel);
            }
          }else {
            //$repos = new TRepository('Laudo');
            //$sel = $repos->where('candidato_id','=',$idCSel)->where('vaga_id','=',$vaga_id)->load();
            new TMessage('info', 'entrou no else 462');
            
          }
        
        TTransaction::close();
      }
      catch(Exception $e){
        new TMessage('error', $e->getMessage() );
      }//*/

      //FILTRO CONTENDO O ID DOS CANDIDATOS QUE SE INSCREVERAM NA VAGA      
      //$filter = new TFilter('id', 'IN', $this->idCandidatosSelecionados );
      //TSession::setValue('SelecionarCandidatos_filtro_selecionados', $filter);
      
      if (TSession::getValue('VagaDetalhesAdmin_vaga_id') ){
        $vagaId = TSession::getValue('VagaDetalhesAdmin_vaga_id');
        TSession::setValue('SelecionarCandidatos_vaga_id', $vagaId);
      }
      
      AdiantiCoreApplication::gotoPage('NotificarCandidatos');
    }
//----------------------------------------------------------------------------------------------------------*/

//*---------------------------------------------------------------------------------------------------------     
    public function onCheck ($param){
      $idCandidato = $param['id'];
            
      $candidatos_selecionados = TSession::getValue(__CLASS__.'_selected_objects');

      TTransaction::open('con_ultimato');
        $candidato = new Candidato($idCandidato); //Carrega o candidato

        if (isset( $candidatos_selecionados[$candidato->id] )){
          unset( $candidatos_selecionados[$candidato->id] );
        }
        else{
          //$candidatos_selecionados[$candidato->id] = $candidato->toArray();
          $candidatos_selecionados[$candidato->id] = $candidato;
        }

        TSession::setValue(__CLASS__.'_selected_objects', $candidatos_selecionados);
        
        /*TESTE
        $can = TSession::getValue(__CLASS__.'_selected_objects'); //Capturando variável de sessão contendo os candidatos selecionados
        
        //new TMessage('info', $can[$candidato->id]->nome);
        
        foreach ($can as $key => $c){
          echo $key.'  -  '; //Aparentemente ele pega bem a key
        }
        //*/
        
      TTransaction::close();
     
      $this->onReload( func_get_arg(0) );
    }
//----------------------------------------------------------------------------------------------------------*/

//*---------------------------------------------------------------------------------------------------------     
    public function formatRow($value, $object, $row){
      $selected_objects = TSession::getValue(__CLASS__.'_selected_objects');
        
      if ($selected_objects){
        if (in_array( (int) $value, array_keys( $selected_objects ) ) ){
          $row->style = "background: #abdef9";
                
          //$button = $row->find('i', ['class'=>'far fa-square fa-fw black'])[0];
                
          //if ($button){
            //$button->class = 'far fa-check-square fa-fw black';
          //}
        }
      }
        
      return $value;
    }


//----------------------------------------------------------------------------------------------------------*/    
  }


?>
