<?php
  //Autor: Lucas
  
  class CurriculosViewAdmin extends TPage{
    private $form; // Formulário para os filtros
    private $datagrid; //Para exibiri os resultados dos filtros
    private $pageNavigation; //Para "paginar" os resultados
  
    public function __construct(){
      parent::__construct();
      
      if ( TSession::getValue('tipo_usuario') == 'CANDIDATO' || TSession::getValue('tipo_usuario') == NULL) {
        AdiantiCoreApplication::gotoPage('PaginaPrincipalForm');
      }
      
      
      $this->form = new TForm('form');
      
      $table  = new TTable(); // Tabela principal para estruturar/organizar toda a página
      
//CRIAÇÃO DA TABELA/FORMULÁRIO DE FILTROS----------------------------------------------------------------------------------
      $filtros = new TTable(); // Estruturar/Organizar os campos de filtragem
          
      //Criação de label
      $lbl_filtro = new TLabel('Filtros de busca:');
      
      //Configuração de label
      $lbl_filtro->setFontSize(14);
      $lbl_filtro->setFontFace('Arial');
      
      //Criação dos campos de FILTRO
      $nome             = new TEntry('nome');
      $estado           = new TEntry('estado');
      $cidade           = new TEntry('cidade');
      $areaDeInteresse  = new TEntry('areaDeInteresse');
      $formacao         = new TEntry('formacao');     
      $idade1           = new TEntry('idade1');
      $op_idade1        = new TCombo('op_idade1');  
      //$idade2           = new TEntry('idade2');
      //$op_idade2        = new TCombo('op_idade2');    
      $idioma           = new TEntry('idioma');    
      $faixaSalarial    = new TEntry('faixaSalarial');         

      $filtrar          = TButton::create('filtrar', [$this, 'onFilter'], 'Filtrar', 'fa:filter green'); //Botão para filtrar
      
      //CONFIGURANDO OS CAMPOS DE FILTRO
      $nome->setSize('90%');                                                                                          
      $nome->placeholder = 'Nome';
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
      
      //$idade2->setSize('85%');
      //$idade2->placeholder = 'Idade';
      
      //$op_idade2->setSize('100%');
      //$op_idade2->addItems(['ma' => 'maior', 'me' => 'menor', 'ig' => 'igual']);
      //$op_idade2->setValue('ma');
      
      $idioma->setSize('90%');
      $idioma->placeholder = 'Idioma';
      
      $faixaSalarial->setSize('90%');
      $faixaSalarial->placeholder = 'Faixa Salarial';
      
      
      
      //Adição dos campos
      
      $row  = $filtros->addRow();
      $cell = $row->addCell($lbl_filtro);
      $cell->colspan = 2;
      
      $row = $filtros->addRow();                                                                        
      $cell = $row->addCell($nome);
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
      
      //$row = $filtros->addRow();
      //$row->addCell($op_idade2);
      //$row->addCell($idade2);
      
      $row = $filtros->addRow();
      $cell = $row->addCell($idioma);
      $cell->colspan = 2;
      
      $row = $filtros->addRow();
      $cell = $row->addCell($faixaSalarial);
      $cell->colspan = 2;

      $filtros->addRowSet($filtrar); //Adição do botão
      
      //Consfiguração das Tabelas
      $table->width = '100%';
      //$table->border = '1';
      //$table->cellpadding = '200';
      //$filtros->border = '1px';
      $filtros->width = '100%';
      
      //Adição dos campos no formulário
      $this->form->addField($nome);
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
//-------------------------------------------------------------------------------------------------------- 
      
//*CRIANDO DATAGRID---------------------------------------------------------------------------------------
      $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid() );
      $this->datagrid->style = 'width: 100%';//*/
      
      //*Criando colunas da datagrid
      //$col_id              = new TDataGridColumn('id', 'ID', 'left');
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
      
      //*Adicionando colunas na datagrid
      //$this->datagrid->addColumn($col_id);
      $this->datagrid->addColumn($col_foto);
      $this->datagrid->addColumn($col_nome);
      $this->datagrid->addColumn($col_dataNascimento);
      $this->datagrid->addColumn($col_sexo);
      $this->datagrid->addColumn($col_cidade);
      $this->datagrid->addColumn($col_estado);

      //*CRIANDO E ADICIONANDO AS AÇÕES >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    
      //*AÇÃO DETALHES
      $detalhes = new TDataGridAction( [$this, 'candidatoDetalhes'] );
      $detalhes->setLabel('Detalhes');
      $detalhes->setImage('fa:search blue');
      $detalhes->setField('id');
      $this->datagrid->addAction( $detalhes );
 
      //*/>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
           
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
      
      $row   = $table->addRow();
      $cell1 = $row->addCell($this->form);
      $cell2 = $row->addCell($this->datagrid);
      
      $cell1->width = '20%';
      $cell1->style = 'vertical-align: top'; // Colocar o conteúdo no topo da célula
      $cell2->width = '80%';
      
      $row = $table->addRow();
      $row->addCell('');
      $row->addCell($this->pageNavigation);
      
      parent::add($table);
    }
    
    
    
    
    
    
    
    
//*MÉTODOS / FUNÇÕES --------------------------------------------------------------------------------------- 

/*----------------------------APENAS PARA TESTE------------------------------------------------------------
    public function onFilter( $param ){
      new TMessage('info', 'Você clicou no filtro');
    }
//----------------------------------------------------------------------------------------------------------*/

//*---------------------------------------------------------------------------------------------------------    
    public function onFilter( $param ){
      $data = $this->form->getData();
      $this->form->setData($data);
      
      //Criação das variáveis de sessão VAZIAS
      TSession::setValue('CurriculosViewAdmin_filtro_nome', null);                                                  
      TSession::setValue('CurriculosViewAdmin_filtro_estado', null);
      TSession::setValue('CurriculosViewAdmin_filtro_estado', null);
      TSession::setValue('CurriculosViewAdmin_filtro_cidade', null);
      TSession::setValue('CurriculosViewAdmin_filtro_areaDeInteresse', null);
      TSession::setValue('CurriculosViewAdmin_filtro_formacao', null);
      TSession::setValue('CurriculosViewAdmin_filtro_idade', null);
      TSession::setValue('CurriculosViewAdmin_filtro_idioma', null);
      TSession::setValue('CurriculosViewAdmin_filtro_faixaSalarial', null);
    
    //O FILTRO VAI SER REALIZADO NA TABELA/CLASSE >> CANDIDATO <<   
    //Atribuindo Valores/Filtros para as VARIÁVEIS DE SESSÃO
      
      /*if ( !empty($data->cidade) ){
        $filter = new TFilter('(SELECT nome_cidade 
                                FROM cidade 
                                WHERE cidade.id = candidato.cidade_id)',
                                'like', "{$data->cidade}%"
                              );
        TSession::setValue('CurriculosViewAdmin_filtro_cidade', $filter);        
      }*/
      if ( !empty($data->nome) ){                                                                                                
        $filter = new TFilter('nome', 'IN', "(SELECT nome FROM candidato WHERE nome LIKE '{$data->nome}%')" );
        TSession::setValue('CurriculosViewAdmin_filtro_nome', $filter);        
      }
      
      if ( !empty($data->cidade) ){
        $filter = new TFilter('cidade_id', 'IN', "(SELECT id FROM cidade WHERE nome_cidade LIKE '{$data->cidade}%')" );

        TSession::setValue('CurriculosViewAdmin_filtro_cidade', $filter);        
      }
    
      if ( !empty($data->estado) ){
        $filter = new TFilter('cidade_id', 'IN',
                              "(SELECT id FROM cidade WHERE estado_id IN (
                                 SELECT id FROM estado WHERE nome_estado LIKE '{$data->estado}%'
                               ))"
                              );
        TSession::setValue('CurriculosViewAdmin_filtro_estado', $filter);
      }
      
      
      if ( !empty($data->areaDeInteresse) ){
        $area = $data->areaDeInteresse;
        
        $filter = new TFilter('curriculo_id', 'IN',
                              "(SELECT curriculo_id FROM area_e_curriculo WHERE areadeinteresses_id IN (
                                  SELECT id FROM area_de_interesse WHERE descricao LIKE '{$area}%'
                              ))"
                              );
        TSession::setValue('CurriculosViewAdmin_filtro_areaDeInteresse', $filter);
      }
      
      if ( !empty($data->formacao) ){
        $formacao = $data->formacao;
        
        $filter = new TFilter('curriculo_id', 'IN',
                              "(SELECT id FROM formacao WHERE curso_id IN (
                                  SELECT id FROM cursos WHERE nome LIKE '{$formacao}%'
                             ))");
                             
        TSession::setValue('CurriculosViewAdmin_filtro_formacao', $filter);
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
                             
        TSession::setValue('CurriculosViewAdmin_filtro_idade', $filter);
      }
      
      if ( !empty($data->idioma) ){
        $idioma = $data->idioma;
        
        $filter = new TFilter('curriculo_id', 'IN',
                              "(SELECT curriculo_id FROM nivel_idioma WHERE idioma_id IN (
                                SELECT id FROM idioma WHERE nome LIKE '{$idioma}%'
                              ))");
                              
        TSession::setValue('CurriculosViewAdmin_filtro_idioma', $filter);
      }
      
      if ( !empty($data->faixaSalarial) ){
        $filter = new TFilter('curriculo_id', 'IN', "(SELECT id FROM curriculo WHERE pretensaoSalarial >= {$data->faixaSalarial})");
        
        TSession::setValue('CurriculosViewAdmin_filtro_faixaSalarial', $filter);
      }    
  
      TSession::setValue('Vagas_filtro_data', $data); //Guardando todos o valores do formulário em uma ÚNICA variável de sessão
      
      //Passando informações para o pageNavigation através da URL
      $param = [];
      $param['offset'] = 0;
      $param['first_page'] = 1;
      
      $this->onReload( $param ); // executando o método OnReload que vai fazer uso dos filtros criados em sessão e executalos de fato
    }
//----------------------------------------------------------------------------------------------------------*/


/*-------------------------APENAS PARA TESTES--------------------------------------------------------------
      public function onReload($param){
      
      }
//----------------------------------------------------------------------------------------------------------*/

//*---------------------------------------------------------------------------------------------------------
      public function onReload($param){
      //$data = $this->form->getData();
      //$this->form->setData($data);
      
      try{
        TTransaction::open('con_ultimato');
        
          $repository = new TRepository('Candidato');
          $limit = 5;
          
          $criteria = new TCriteria();
          $criteria->setProperty('limit', $limit);
          $criteria->setProperties( $param ); //Lê a URL e extrai as informações de paginação (limit, ofset etc) e joga para dentro do critério que vai ser usado para carregar os registros da base de dados
          
        //*CAPTURANDO OS FILTROS GUARDADOS EM SESSÃO---------------------------------------------------------   
          if( TSession::getValue('CurriculosViewAdmin_filtro_nome') ){
            $criteria->add( TSession::getValue('CurriculosViewAdmin_filtro_nome') );                               
          }
          
          if( TSession::getValue('CurriculosViewAdmin_filtro_estado') ){
            $criteria->add( TSession::getValue('CurriculosViewAdmin_filtro_estado') );
          }
          
          if( TSession::getValue('CurriculosViewAdmin_filtro_cidade') ){
            $criteria->add( TSession::getValue('CurriculosViewAdmin_filtro_cidade') );
          }
          
          if( TSession::getValue('CurriculosViewAdmin_filtro_areaDeInteresse') ){
            $criteria->add( TSession::getValue('CurriculosViewAdmin_filtro_areaDeInteresse') );
          }
          
          if( TSession::getValue('CurriculosViewAdmin_filtro_formacao') ){
            $criteria->add( TSession::getValue('CurriculosViewAdmin_filtro_formacao') );
          }
          
          if( TSession::getValue('CurriculosViewAdmin_filtro_idade') ){
            $criteria->add( TSession::getValue('CurriculosViewAdmin_filtro_idade') );
          } 
          
          if( TSession::getValue('CurriculosViewAdmin_filtro_idioma') ){
            $criteria->add( TSession::getValue('CurriculosViewAdmin_filtro_idioma') );
          }
          
          if( TSession::getValue('CurriculosViewAdmin_filtro_faixaSalarial') ){
            $criteria->add( TSession::getValue('CurriculosViewAdmin_filtro_faixaSalarial') );
          } 
        //---------------------------------------------------------------------------------------------------//*/
          
          $objetos = $repository->load($criteria, false); // Faz a busca utilizando os filtros captudados
          
          $this->datagrid->clear();          
                  
          if ($objetos){
            foreach ($objetos as $obj){
              $this->datagrid->addItem($obj);
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
    public function candidatoDetalhes($param){
      //$id = $param['id'];
      
      TSession::setValue('CurriculosViewAdmin_candidato_id', $param['id']);
      $id['idcandidato'] = $param['id'];
      AdiantiCoreApplication::gotoPage('PerfilCandidatoAdmin', '', $id);
    }
//----------------------------------------------------------------------------------------------------------*/


//----------------------------------------------------------------------------------------------------------*/    
  }


?>
