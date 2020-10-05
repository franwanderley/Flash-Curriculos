<?php
/**
 * VagaSelectionList Record selection
 * @author  <your name here>
 */
class VagaSelectionList extends TPage
{
    protected $form;     // search form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    use Adianti\base\AdiantiStandardListTrait;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('con_ultimato');            // defines the database
        $this->setActiveRecord('Vaga');   // defines the active record
        $this->setDefaultOrder('id', 'asc');         // defines the default order
        // $this->setCriteria($criteria) // define a standard filter

        $this->addFilterField('id', '=', 'id'); // filterField, operator, formField
        $this->addFilterField('titulo', 'like', 'titulo'); // filterField, operator, formField
        $this->addFilterField('foto', 'like', 'foto'); // filterField, operator, formField
        $this->addFilterField('nomeEmpresa', 'like', 'nomeEmpresa'); // filterField, operator, formField
        $this->addFilterField('requisitosMinimos', 'like', 'requisitosMinimos'); // filterField, operator, formField
        $this->addFilterField('mediaSalarial', 'like', 'mediaSalarial'); // filterField, operator, formField
        $this->addFilterField('jornada', 'like', 'jornada'); // filterField, operator, formField
        $this->addFilterField('descricao', 'like', 'descricao'); // filterField, operator, formField
        $this->addFilterField('status', 'like', 'status'); // filterField, operator, formField
        $this->addFilterField('beneficios', 'like', 'beneficios'); // filterField, operator, formField
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_Vaga');
        $this->form->setFormTitle('Vaga');
        

        // create the form fields
        $id = new TEntry('id');
        $titulo = new TEntry('titulo');
        $foto = new TEntry('foto');
        $nomeEmpresa = new TEntry('nomeEmpresa');
        $requisitosMinimos = new TEntry('requisitosMinimos');
        $mediaSalarial = new TEntry('mediaSalarial');
        $jornada = new TEntry('jornada');
        $descricao = new TEntry('descricao');
        $status = new TEntry('status');
        $beneficios = new TEntry('beneficios');


        // add the fields
        $this->form->addFields( [ new TLabel('Id') ], [ $id ] );
        $this->form->addFields( [ new TLabel('Titulo') ], [ $titulo ] );
        $this->form->addFields( [ new TLabel('Foto') ], [ $foto ] );
        $this->form->addFields( [ new TLabel('Nomeempresa') ], [ $nomeEmpresa ] );
        $this->form->addFields( [ new TLabel('Requisitosminimos') ], [ $requisitosMinimos ] );
        $this->form->addFields( [ new TLabel('Mediasalarial') ], [ $mediaSalarial ] );
        $this->form->addFields( [ new TLabel('Jornada') ], [ $jornada ] );
        $this->form->addFields( [ new TLabel('Descricao') ], [ $descricao ] );
        $this->form->addFields( [ new TLabel('Status') ], [ $status ] );
        $this->form->addFields( [ new TLabel('Beneficios') ], [ $beneficios ] );


        // set sizes
        $id->setSize('100%');
        $titulo->setSize('100%');
        $foto->setSize('100%');
        $nomeEmpresa->setSize('100%');
        $requisitosMinimos->setSize('100%');
        $mediaSalarial->setSize('100%');
        $jornada->setSize('100%');
        $descricao->setSize('100%');
        $status->setSize('100%');
        $beneficios->setSize('100%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_titulo = new TDataGridColumn('titulo', 'Titulo', 'left');
        $column_foto = new TDataGridColumn('foto', 'Foto', 'left');
        $column_nomeEmpresa = new TDataGridColumn('nomeEmpresa', 'Nomeempresa', 'left');
        $column_requisitosMinimos = new TDataGridColumn('requisitosMinimos', 'Requisitosminimos', 'left');
        $column_mediaSalarial = new TDataGridColumn('mediaSalarial', 'Mediasalarial', 'right');
        $column_jornada = new TDataGridColumn('jornada', 'Jornada', 'right');
        $column_descricao = new TDataGridColumn('descricao', 'Descricao', 'left');
        $column_status = new TDataGridColumn('status', 'Status', 'left');
        $column_beneficios = new TDataGridColumn('beneficios', 'Beneficios', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_titulo);
        $this->datagrid->addColumn($column_foto);
        $this->datagrid->addColumn($column_nomeEmpresa);
        $this->datagrid->addColumn($column_requisitosMinimos);
        $this->datagrid->addColumn($column_mediaSalarial);
        $this->datagrid->addColumn($column_jornada);
        $this->datagrid->addColumn($column_descricao);
        $this->datagrid->addColumn($column_status);
        $this->datagrid->addColumn($column_beneficios);

        $column_id->setTransformer([$this, 'formatRow'] );
        
        // creates the datagrid actions
        $action1 = new TDataGridAction([$this, 'onSelect'], ['id' => '{id}', 'register_state' => 'false']);
        //$action1->setUseButton(TRUE);
        $action1->setButtonClass('btn btn-default');
                
        // add the actions to the datagrid
        $this->datagrid->addAction($action1, 'Select', 'far:square fa-fw black');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        
        $panel = new TPanelGroup;
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);
        $panel->addHeaderActionLink( 'Show results', new TAction([$this, 'showResults']), 'far:check-circle' );
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel);
        
        parent::add($container);
    }
    
    /**
     * Save the object reference in session
     */
    public function onSelect($param)
    {
        // get the selected objects from session 
        $selected_objects = TSession::getValue(__CLASS__.'_selected_objects');
        
        TTransaction::open('con_ultimato');
        $object = new Vaga($param['key']); // load the object
        if (isset($selected_objects[$object->id]))
        {
            unset($selected_objects[$object->id]);
        }
        else
        {
            $selected_objects[$object->id] = $object->toArray(); // add the object inside the array
        }
        TSession::setValue(__CLASS__.'_selected_objects', $selected_objects); // put the array back to the session
        TTransaction::close();
        
        // reload datagrids
        $this->onReload( func_get_arg(0) );
    }
    
    /**
     * Highlight the selected rows
     */
    public function formatRow($value, $object, $row)
    {
        $selected_objects = TSession::getValue(__CLASS__.'_selected_objects');
        
        if ($selected_objects)
        {
            if (in_array( (int) $value, array_keys( $selected_objects ) ) )
            {
                $row->style = "background: #abdef9";
                
                $button = $row->find('i', ['class'=>'far fa-square fa-fw black'])[0];
                if ($button)
                {
                    $button->class = 'far fa-check-square fa-fw black';
                }
            }
        }
        
        return $value;
    }
    
    /**
     * Show selected records
     */
    public function showResults()
    {
        $datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $datagrid->width = '100%';
        $datagrid->addColumn( new TDataGridColumn('id',  'Id',  'right') );
        $datagrid->addColumn( new TDataGridColumn('titulo',  'Titulo',  'left') );
        $datagrid->addColumn( new TDataGridColumn('foto',  'Foto',  'left') );
        $datagrid->addColumn( new TDataGridColumn('nomeEmpresa',  'Nomeempresa',  'left') );
        $datagrid->addColumn( new TDataGridColumn('requisitosMinimos',  'Requisitosminimos',  'left') );
        $datagrid->addColumn( new TDataGridColumn('mediaSalarial',  'Mediasalarial',  'right') );
        $datagrid->addColumn( new TDataGridColumn('jornada',  'Jornada',  'right') );
        $datagrid->addColumn( new TDataGridColumn('descricao',  'Descricao',  'left') );
        $datagrid->addColumn( new TDataGridColumn('status',  'Status',  'left') );
        $datagrid->addColumn( new TDataGridColumn('beneficios',  'Beneficios',  'left') );
        
        // create the datagrid model
        $datagrid->createModel();
        
        $selected_objects = TSession::getValue(__CLASS__.'_selected_objects');
        ksort($selected_objects);
        if ($selected_objects)
        {
            $datagrid->clear();
            foreach ($selected_objects as $selected_object)
            {
                $datagrid->addItem( (object) $selected_object );
            }
        }
        
        $win = TWindow::create('Results', 0.6, 0.6);
        $win->add($datagrid);
        $win->show();
    }
}
