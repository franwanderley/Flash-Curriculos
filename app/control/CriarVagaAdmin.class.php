<?php
  //Autor: Lucas
  
  class CriarVagaAdmin extends TPage{
  private $form;
  private $img;
  
  public function __construct($param){
    parent::__construct();
    
    if ( TSession::getValue('tipo_usuario') != 'ADMINISTRADOR' || TSession::getValue('tipo_usuario') == NULL) {
      AdiantiCoreApplication::gotoPage('PaginaPrincipalForm');
    }
    
    $this->form = new TQuickForm('form');
    $this->form->class = 'tform';
    $this->form->setFormTitle('Criar Nova Vaga');
        
  //CRIANDO CAMPOS DO FORMULÁRIO
    $id                = new THidden ('id');
    $titulo_vaga       = new TEntry ('titulo');
    $nome_empresa      = new TEntry ('nomeEmpresa');
  //$area_de_interesse = new TEntry ('area_de_interesse');
    $requisitos        = new TText ('requisitosMinimos');
    $media_salarial    = new TEntry ('mediaSalarial');
    $jornada           = new TEntry ('jornada');
    $foto_empresa      = new TFile  ('foto');
    $descricao         = new TText  ('descricao');
    $beneficios        = new TText ('beneficios');
    $stat              = new TCombo('status');
        
  //CONFIGURANDO CAMPOS DO FORMULÁRIO
    $foto_empresa->setSize(200);
    $jornada->setMask('9999');
    $id->setEditable(FALSE);
    $media_salarial->setNumericMask(2, ',', '.', true);
    //$stat->setDefaultOption('ABERTA');
    
    $stat->addItems(['ABERTA'=>'ABERTA', 'FECHADA'=>'FECHADA']);
    
    //$change_action = new TAction(array($this, 'onChangeAction'));
    //$stat->setChangeAction($change_action);
    
    
    
    
    
        
    
     // set sizes
        //$foto->setSize('30%');
        $id->setSize('10%');
        $titulo_vaga->setSize('100%');
        $nome_empresa->setSize('100%');
        $requisitos->setSize('100%');
        $media_salarial->setSize('100%');
        $jornada->setSize('100%');
        $descricao->setSize('100%');
        $beneficios->setSize('100%');
        $stat->setSize('30%');
    
    $fram = new TFrame(200, 220);
    
    $fram->class = "classe_frame_foto";
    
    $table_frame = new TTable('');
    $table_frame->border = 2;
    $table_frame->addRowSet($fram);
    
    $tableinter = new TTable('ti');
    $l1 = $tableinter->addRow()->addCell($table_frame)->border =1;
    
    
    $l2 = $tableinter->addRow()->addCell($foto_empresa);
    
    $table_form = new TTable('tf');
    //$table_form->border = 1;
    $table_form->width = '60%';
    $table_form->addRowSet('', $id);
    $table_form->addRowSet(new TLabel('Título'), $titulo_vaga);
    $table_form->addRowSet(new TLabel('Nome da Empresa'), $nome_empresa);
    $table_form->addRowSet(new TLabel('Requisítos'), $requisitos);
    $table_form->addRowSet(new TLabel('Média Salarial'), $media_salarial);
    $table_form->addRowSet(new TLabel('Jornada'), $jornada);
    $table_form->addRowSet(new TLabel('Descrição'), $descricao);
    $table_form->addRowSet(new TLabel('Benefícios'), $beneficios);
    $table_form->addRowSet(new TLabel('status'), $stat);
    
    $btn_save  = TButton::create('btn_save',  [$this, 'onSave' ], 'SALVAR',  'fa:save green');
    $btn_clear = TButton::create('btn_clear', [$this, 'onClear'], 'NOVA VAGA',  'fa:plus green');
    
    $table_form->addRowSet('', [$btn_clear, $btn_save]);
    
    
    
    
    //$r = $table_form->addRow();
    //$r->addCell();
    
    
    
    $tablemaster = new TTable('tm');
    $tablemaster->width = '100%';
    //$tablemaster->border = 2;
    $row = $tablemaster->addRow();
    $c1 = $row->addCell($tableinter);
    $c2 = $row->addCell($table_form);
    
    $c1->style = 'vertical-align:text-top; width:20%';
    $c2->style = "text-align:right";
    
    $this->form->add($tablemaster);
    
    $foto_empresa->setCompleteAction(new TAction(array($this, 'onComplete')));
    
    //validação
    
    $titulo_vaga->addValidation("titulo", new TRequiredValidator); 
    $nome_empresa->addValidation("nomeEmpresa", new TRequiredValidator);
    $requisitos->addValidation("requisitosMinimos", new TRequiredValidator); 
    $media_salarial->addValidation("mediaSalarial", new TRequiredValidator); 
    $jornada->addValidation("jornada", new TRequiredValidator); 
    //$foto_empresa->addValidation("foto", new TRequiredValidator); 
    //$descricao->addValidation("descricao", new TRequiredValidator); 
    //$beneficios->addValidation("beneficios", new TRequiredValidator); 
    $stat->addValidation("status", new TRequiredValidator); 
    
    
    
    
    
    
    //ADICIONANDOO CAMPOS NO FORMULÁRIO
    $this->form->setFields([$id, $titulo_vaga, $nome_empresa, $requisitos, $media_salarial,  $jornada, $foto_empresa, $descricao, $beneficios, $stat, $btn_clear, $btn_save]);
 
    
  //ADICIONANDO FORMULÁRIO DA PÁGINA
    parent::add($this->form);
    
  }
  
  public static function onComplete($param) {
    TScript::create("
          $(document).ready( function() {
              $('.classe_frame_foto').html('');
              $('.classe_frame_foto').append(\"<img src='tmp/{$param['foto']}' width='200px' height='220px'>\");
          });
    ");
  }
  
  

  public function onSave($param){    
    
    try{    
      $this->form->validate();
      
      TTransaction::open('con_ultimato');
        $data = $this->form->getData();
        
        
        if (isset($param['id']) && $param['id'] != "") {
          $vaga = new Vaga($param['id']); 
           
        }else {
          $vaga = new Vaga();
          
        }  
          
        
        
        $vaga->titulo              = $data->titulo;
        $vaga->nomeEmpresa         = $data->nomeEmpresa;
        ////$vaga->area_de_interesse = $data->area_de_interesse;
        $vaga->requisitosMinimos   = $data->requisitosMinimos;
        $vaga->mediaSalarial       = $data->mediaSalarial;
        $vaga->jornada             = $data->jornada;
        
        if (substr($data->foto, 0, 3) == 'app') {
          $vaga->foto                = $data->foto;
        }else {
          $vaga->foto                = 'app/images/vagas/'.$data->foto;
        }
        
        
        $vaga->descricao           = $data->descricao;
        $vaga->beneficios          = $data->beneficios;
        $vaga->status              = $data->status;
        //var_dump($vaga);
        $vaga->store();
        
        if (isset($param['id'])) { 
          if (file_exists('tmp/'.$data->foto)) {
            copy('tmp/' . $data->foto, $vaga->foto);
          }
          else {
            if (isset($data->foto)) {
              if (substr($data->foto, 0, 3) == 'app')
                $vaga->foto = substr($data->foto, 16);
            }
          }
        }
        
        new TMessage('info', 'Vaga salva com sucesso!');
        
        if (isset($param['id']) && $param['id'] != "") {
          $p['key'] = $param['id'];
        }else {
          $p['key'] = $vaga->id;
        }
        
        AdiantiCoreApplication::gotoPage('VagaDetalhesAdmin','', $p);
              
      TTransaction::close();    
    }
    catch(Exception $e){
      new TMessage('error', $e->getMessage());
      TTransaction::rollback();
    }    
  }
    
  public function onEdit($param){
    
    try{
      if ( isset($param['key']) ){
      
       
        $id = $param['key'];
      
        TTransaction::open('con_ultimato');
                  
          $vaga = new Vaga($id);
          
          //$this->form->setData($vaga);
          $response = new stdClass;
          $response->{'id'}            = $vaga->id;
          $response->{'titulo'}            = $vaga->titulo; 
          $response->{'nomeEmpresa'}       = $vaga->nomeEmpresa;
          $response->{'requisitosMinimos'} = $vaga->requisitosMinimos;
          $response->{'mediaSalarial'}     = $vaga->mediaSalarial;
          $response->{'jornada'}           = $vaga->jornada;
          $response->{'status'}            = $vaga->status;
          
          if ($vaga->foto) {
            TSession::setValue('foto_vaga', $vaga->foto);
            TScript::create("$('.classe_frame_foto').append(\"<img style='width:200px; height:220px;' src='{$vaga->foto}'>\");");
            $response->{'foto'}           = $vaga->foto;
          }else {
            TScript::create("$('.classe_frame_foto').append(\"<img style='width:200px; height:220px;' src='app/images/vagas/default.png'>\");");
          }
          
          
          
          $response->{'descricao'}         = $vaga->descricao;        
          $response->{'beneficios'}        = $vaga->beneficios;

          $this->form->sendData('form',$response);         
        TTransaction::close();
      }
    }    
    catch(Exception $e){
      new TMessage('error', $e->getMessage());
      TTransaction::rollback();
    }
    
  }
  
  
  public function onClear(){
    $this->form->clear( true ); //usando o True para manter os valores default
  } 
}
?>
