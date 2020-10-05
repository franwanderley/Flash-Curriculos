<?php
/*
    Autor Francisco Wanderly
    feito
*/

class ListagemFuncionario extends TPage{
    private $datagrid;
    private $form;
    private $pageNavigation;
    
    public function __construct(){
        parent::__construct();
        
        //Verifica o tipo de usuario
        //Pega os dados da sessão
        $this->dados = TSession::getValue('username');
        
        //Verificando o tipo de Usuario
        if(TSession::getValue('tipo_usuario') != 'ADMINISTRADOR'){
            new TMessage('warning', 'É preciso estar logado como administrador!', new TAction( [$this, 'redirecionar'] ));
        }
        
            //Titulo
            $title = new TLabel('Funcionarios');
            $title->setFontSize(24);
            $title->setFontFace('Arial');
            $title->style = 'text-align: center; margin-bottom: 5%';
            
            //Botão Novo Funcionario
            $bnovo = TButton::create('novofuncionario', [$this, 'novoFuncionario'], 'Novo Funcionario', 'fa:plus-square green');
            $formnovo = new TForm('botaonovo');
            $tablenovo = new TTable;
            $tablenovo->addRowSet($bnovo);
            $formnovo->add($tablenovo);
            $formnovo->addField($bnovo);
            //=========================Filtro=======================================================
                $filtros = new TTable();
          
                //Criação de label
                  $lbl_filtro = new TLabel('Filtros de busca:');
                  
                //Consfiguração de label
                  $lbl_filtro->setFontSize(16);
                  $lbl_filtro->setFontFace('Arial');
                  
                //Criação dos campos
                  $cargo = new TCombo('cargo');
                  $l     = new TLabel('Cargo');
                  $items = ['FUNCIONARIO'=>'Estagiario', 'ADMINISTRADOR'=>'Administrador'];
                  $cargo->addItems($items);
            
                  $filtrar = TButton::create('filtrar', [$this, 'onFilter'], 'Filtrar', 'fa:filter green'); //Botão para filtrar
                  
                //Adição dos campos
                  $row  = $filtros->addRow();
                  $cell = $row->addCell($lbl_filtro);
                  $cell->colspan = 3;
                  
                  $filtros->addRowSet($l,$cargo);
                  $filtros->addRowSet($filtrar); //Adição do botão
                  
                //consfiguração das tabelas
                 
                  //$table->border = '1';
                  //$table->cellpadding = '200';
                  //$table1->border = '1px';
                  $filtros->width = '100%';
                  
                //Adição dos campos no formulário
                  $this->form = new TForm('filtro');
                  $this->form->addField($cargo);
                  $this->form->addField($filtrar); //BOTÃO
                  $this->form->add($filtros);
            //==============================Criação do datagrid============================================================
            $this->datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
            $this->datagrid->setHeight(500);
            $col_foto = $this->datagrid->addQuickColumn('Foto','foto','center', '30%');
            $this->datagrid->addQuickColumn('Nome','nome','left', '20%');
            $this->datagrid->addQuickColumn('Email','email','left', '20%');
            $this->datagrid->addQuickColumn('Telefone','telefone','left', '30%');
            $this->datagrid->addQuickColumn('Cargo','tipo_usuario','left', '30%');
            
             $col_foto->setTransformer( function($imagem){
                $imagem = new TImage($imagem);
                $imagem->style = 'max-width: 100px; max-height: 100px';
                return $imagem;
             });
             
            //adicionar ação do data grid
            $action1 = new TDataGridAction(array('FuncionarioEditar', 'onEdit'));
            $action1->setParameters(['pag' => 'L']);
            $action1->setUseButton(TRUE);
            $action1->setButtonClass('btn btn-default');
            $action1->setImage('fa:edit blue');
            
            $action2 = new TDataGridAction(array($this, 'onDelete'));
            $action2->setUseButton(TRUE);
            $action2->setButtonClass('btn btn-default');
            $action2->setImage('fa:trash red');
            $action2->setDisplayCondition( array($this, 'botaoCondicao') );
            
            
            $this->datagrid->addQuickAction('Editar', $action1, 'id');
            $this->datagrid->addQuickAction('Apagar', $action2, 'id');
            $this->datagrid->actionWidth = '20px';
            
            //cria o datagrid
            $this->datagrid->createModel();
            
            //Criação do PageNavigation
            $this->pageNavigation = new TPageNavigation;
        
            $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
            $this->pageNavigation->setWidth($this->datagrid->getWidth());
            
            // ==========================Caixa Vertical========================================================
            $vbox = new TVBox;
            $vbox->style = 'width: 100%';
            //  $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $vbox->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
            
            //Juntando datagrid e filtro
              $table = new TTable;
              $row = $table->addRow();
              $cell = $row->addCell($title);
              $row->addCell($formnovo);
              $cell->style = "text-align : center;";
              $cell->colspan = 5;
              $row = $table->addRow();
              $cell1 = $row->addCell($this->form);
              $cell2 = $row->addCell($vbox);
              $cell1->width = '20%';
              $cell1->style = 'vertical-align: top'; // Colocar o conteúdo no topo da célula
              $cell2->width = '80%';
              $row = $table->addRow();
              $row->addCell('');
            
            // add the table inside the page
            parent::add($table);
       
    }
    
    public function botaoCondicao($object){
        return $object->tipo_usuario == 'FUNCIONARIO' ? TRUE : FALSE;
    }
    
    public function redirecionar(){
         AdiantiCoreApplication::gotoPage('PaginaPrincipalForm');
    }
    
    public function onReload($param){
        try{
            TTransaction::open('con_ultimato');
            $this->datagrid->clear();
             $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            //Filtro de cargo
            if( TSession::getValue('VagasForm_filtro_cargo') ){
                $criteria->add( TSession::getValue('VagasForm_filtro_cargo') );
            }
            
            $repository = new TRepository('Funcionario');//Pega todos os registro 
            $dados = $repository->load($criteria, FALSE);
            foreach($dados as $dado){
               $this->datagrid->addItem( $dado);
            }
            
            $criteria->resetProperties();
            $count= $repository->count($criteria);//Pega o inicio de cada pagina
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
            TTransaction::close();
        }catch(Exception $e){
             new TMessage('error', $e->getMessage());
        }   
        
    }
    
     public function onFilter( $param ){
      $data = $this->form->getData();
      $this->form->setData($data);
      
    //Criação das variáveis de sessão
      TSession::setValue('VagasForm_filtro_cargo', null);
        
    //Atribuindo Valores/Filtros para as variáveis de sessão
      if( !empty($data->cargo) ){
        $filter = new TFilter('tipo_usuario', '=', "{$data->cargo}");
        TSession::setValue('VagasForm_filtro_cargo', $filter);
      }

      TSession::setValue('Vagas_filtro_data', $data);
      
      $param = [];
      $param['offset'] = 0;
      $param['first_page'] = 1;
      
      $this->onReload( $param );
    }
    
    public function onDelete($param){
        $action = new TAction( [$this, 'Delete'] );
        $action->setParameters($param);
            new TQuestion('Deseja excluir o funcionario? ', $action);
        }
        
        public function Delete($param){
            try{
                TTransaction::open('con_ultimato');
                $funcionario = new Funcionario($param['id']);
                //Apagar a foto
                unlink($funcionario->foto);
                $funcionario->delete();
                TTransaction::close();
                new TMessage('info', 'Apagado com Sucesso');
                $this->onReload(NULL);
            }catch(Exception $e){
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
            
        }
    public function novoFuncionario(){
        AdiantiCoreApplication::gotoPage('FuncionarioForm');
    }
    
    public function show(){
        //onde tudo começa
        $this->onReload( func_get_args() );
        parent::show();
    }
}
