<?php
  //Autor: Lucas
  
  class VagasForm extends TPage{
    private $form;
    private $datagrid;
    private $pageNavigation;
  
    public function __construct(){
      parent::__construct();
      
      $this->form = new TForm('form');
      
      $table  = new TTable(); // Tabela principal
      
//CRIAÇÃO DA TABELA/FORMULÁRIO DE FILTROS----------------------------------------------------------------------------------
      $filtros = new TTable();
          
    //Criação de label
      $lbl_filtro = new TLabel('Filtros de busca:');
      
    //Consfiguração de label
      $lbl_filtro->setFontSize(14);
      $lbl_filtro->setFontFace('Arial');
      
    //Criação dos campos
      $titulo             = new TEntry('titulo');
      $mediaSalarial      = new TEntry('mediaSalarial');
      $nomeEmpresa        = new TEntry('nomeEmpresa');
      $jornada            = new TEntry('jornada');        

      $filtrar            = TButton::create('filtrar', [$this, 'onFilter'], 'Filtrar', 'fa:filter green'); //Botão para filtrar
      
    //CONFIGURANDO OS CAMPOS
      $titulo->setSize('90%');
      $titulo->placeholder = 'Titulo'; 
      
      $mediaSalarial->setSize('90%');
      $mediaSalarial->placeholder = 'Média salarial';
      //$mediaSalarial->enableSearch();
      //$faixaSalarial->setDefaultOption('Faixa Salarial');
      
      $nomeEmpresa->setSize('90%');
      $nomeEmpresa->placeholder = 'Empresa';
      
      $jornada->setSize('90%');
      $jornada->placeholder = 'Jornada';
      
    //Adição dos campos
      $row  = $filtros->addRow();
      $cell = $row->addCell($lbl_filtro);
      $cell->colspan = 3;
      
      $filtros->addRowSet($titulo);
      $filtros->addRowSet($mediaSalarial);
      $filtros->addRowSet($nomeEmpresa);
      $filtros->addRowSet($jornada);

      $filtros->addRowSet($filtrar); //Adição do botão
      
    //consfiguração das tabelas
      $table->width = '100%';
      //$table->border = '1';
      //$table->cellpadding = '200';
      //$table1->border = '1px';
      $filtros->width = '100%';
      
    //Adição dos campos no formulário
      $this->form->addField($titulo);
      $this->form->addField($mediaSalarial);
      $this->form->addField($nomeEmpresa);
      $this->form->addField($jornada);
      
      $this->form->addField($filtrar); //BOTÃO
//------------------------------------------------------------------------------------------------------- 
      
//*CRIANDO DATAGRID---------------------------------------------------------------------------------------
      $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid() );
      $this->datagrid->style = 'width: 100%';//*/
      
    //*Criando colunas da datagrid
      $col_foto    = new TDataGridColumn('foto', 'Foto', 'left');
      $col_titulo  = new TDataGridColumn('titulo', 'Titulo', 'left');
      $col_empresa = new TDataGridColumn('nomeEmpresa', 'Empresa', 'center');
      $col_salario = new TDataGridColumn('mediaSalarial', 'Salário', 'center');
      $col_jornada = new TDataGridColumn('jornada', 'Jornada', 'center');
      //*/
      
    //Configurando as colunas
      $col_salario->setTransformer( function( $valor, $objeto, $linha ){
        if ( is_numeric($valor) ){
          return ( 'R$ '. number_format($valor, 2, ',', '.') );
        }
        else{
          return ( $valor );
        }
      });
      
      $col_jornada->setTransformer( function( $valor, $objeto, $linha ) {
        if ( is_numeric( $valor ) ){
          return ( $valor . ' H/Sem' );
        }
        else{
          return($valor);
        }
      });
      
      //*Adicionando colunas na datagrid
      $this->datagrid->addColumn($col_foto);
      $this->datagrid->addColumn($col_titulo);
      $this->datagrid->addColumn($col_empresa);
      $this->datagrid->addColumn($col_salario);
      $this->datagrid->addColumn($col_jornada);
      
      //Definindo o método de transformação sobre a imagem
      $col_foto->setTransformer( function($imagem){
        $imagem = new TImage($imagem);
        $imagem->style = 'max-width: 100px';
        return $imagem;
      });
      
      //Criando ações
      $detalhes = new TDataGridAction( [$this, 'vagaDetalhes'] );
      $detalhes->setLabel('Detalhes');
      $detalhes->setImage('fa:search blue');
      $detalhes->setField('id');

      //Adicionando as ações
      $this->datagrid->addAction( $detalhes );
      
      //Criando datagrid em memória
      $this->datagrid->createModel();
//-------------------------------------------------------------------------------------------------------//*      

//*CRIANDO ESTRUTURA DE PAGINAÇÃO-------------------------------------------------------------------------
      $this->pageNavigation = new TPageNavigation; //Método que faz a paginação da datagrid
      $this->pageNavigation->setAction( new TAction( [$this, 'onReload'] ) ); //Sempre que o usuário clicar na paginação a ação de carga da datagrid deve ser executada (onReload)
      
//-------------------------------------------------------------------------------------------------------//*       
          
    //Adição dos elementos na tabela principal
      $this->form->add($filtros);
      
      $row = $table->addRow();
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
    
    
    
    
    
    
    
    
//*MÉTODOS / FUNÇÕES ----------------------------------------------------------------------------------------\\   

//*---------------------------------------------------------------------------------------------------------    
    public function onFilter( $param ){
      $data = $this->form->getData();
      $this->form->setData($data);
      
     //Criação das variáveis de sessão
      TSession::setValue('VagasForm_filtro_titulo', null);
      TSession::setValue('VagasForm_filtro_mediaSalarial', null);
      TSession::setValue('VagasForm_filtro_nomeEmpresa', null);
      TSession::setValue('VagasForm_filtro_jornada', null);
        
     //Atribuindo Valores/Filtros para as variáveis de sessão
      if( !empty($data->titulo) ){
        $filter = new TFilter('titulo', 'like', "{$data->titulo}%");
        TSession::setValue('VagasForm_filtro_titulo', $filter);
      }
      
      if( !empty($data->mediaSalarial) ){
        $filter = new TFilter('mediaSalarial', '=', $data->mediaSalarial);
        TSession::setValue('VagasForm_filtro_mediaSalarial', $filter);
      }
            
      if( !empty($data->nomeEmpresa) ){
        $filter = new TFilter('nomeEmpresa', 'like', "$data->nomeEmpresa%");
        TSession::setValue('VagasForm_filtro_nomeEmpresa', $filter);
      }
      
      if( !empty($data->jornada) ){
        $filter = new TFilter('jornada', '=', $data->jornada);
        TSession::setValue('VagasForm_filtro_jornada', $filter);
      }

      TSession::setValue('Vagas_filtro_data', $data);
      
      $param = [];
      $param['offset'] = 0;
      $param['first_page'] = 1;
      
      $this->onReload( $param );
    }
//----------------------------------------------------------------------------------------------------------*/

//*---------------------------------------------------------------------------------------------------------
      public function onReload($param){
       try{
        TTransaction::open('con_ultimato');
        
          $repository = new TRepository('Vaga');
          $limit = 5;
          
          $criteria = new TCriteria();
          $criteria->setProperty('limit', $limit);
          $criteria->setProperties( $param ); //Lê a URL e extrai as informações de paginação (limit, ofset etc) e joga para dentro do critério que vai ser usado para carregar os registros da base de dados
          
        //CAPTURANDO OS FILTROS GUARDADOS EM SESSÃO---------------------------------------------------------   
          if( TSession::getValue('VagasForm_filtro_titulo') ){
            $criteria->add( TSession::getValue('VagasForm_filtro_titulo') );
          }
          
          if( TSession::getValue('VagasForm_filtro_mediaSalarial') ){
            $criteria->add( TSession::getValue('VagasForm_filtro_mediaSalarial') );
          }
          
          if( TSession::getValue('VagasForm_filtro_nomeEmpresa') ){
            $criteria->add( TSession::getValue('VagasForm_filtro_nomeEmpresa') );
          }
          
          if( TSession::getValue('VagasForm_filtro_jornada') ){
            $criteria->add( TSession::getValue('VagasForm_filtro_jornada') );
          }
          
          //MOSTRA APENAS AS VAGAS ABERTAS // AL 14/01/2020
          $filter = new TFilter('status', '=', 'ABERTA');
          $criteria->add( $filter );          
          
        //---------------------------------------------------------------------------------------------------
          
          $objetos = $repository->load($criteria, false); // Faz a busca utilizando os filtros captudados
          
          $this->datagrid->clear();
          
          if ($objetos){
            foreach ($objetos as $obj){
              $this->datagrid->addItem($obj);
            }
          }
          
          //confuso
          $criteria->resetProperties(); //Não entendi
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
    public function show(){ //Reescrevendo o método show para executar a função onReload
      $this->onReload( func_get_args() );
      parent::show();
    }
//----------------------------------------------------------------------------------------------------------*/


//*---------------------------------------------------------------------------------------------------------   
    public function vagaDetalhes($param){
      $id = $param['id'];
      
      TSession::setValue('VagasForm_vaga_id', $id);
      AdiantiCoreApplication::gotoPage('VagaDetalhes');
    }
//----------------------------------------------------------------------------------------------------------*/



//----------------------------------------------------------------------------------------------------------*/    
  }


?>
