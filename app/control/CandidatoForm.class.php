<?php
/**
 * CandidatoForm Form
 * @author  <your name here>
 */
class CandidatoForm extends TPage
{
    protected $form; // form
    private $step;
    private $botao_next;
    private $tipoDeficiencia;
    
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        
        if ( TSession::getValue('tipo_usuario') == 'FUNCIONARIO' || TSession::getValue('tipo_usuario') == 'ADMINISTRADOR') {
          AdiantiCoreApplication::gotoPage('PaginaPrincipalForm');
        }
        
        
        if (!isset($param['method'])) {  //se nao tiver requisicao de metodo na url...libera as sessoes
          //TSession::freeSession();
          TSession::setValue('form_step1_candidato', null);
          TSession::setValue('form_step2_curriculo', null);
          TSession::setValue('form_step3_usuario', null);
        }
        
        
        $this->step = new TBreadCrumb;
        $this->step->addItem('CANDIDATO', FALSE);
        $this->step->addItem('CURRÍCULO', FALSE);
        $this->step->addItem('USUÁRIO', TRUE);
        $this->step->select('CANDIDATO');
        
        
        
        // creates the form
        $this->form = new TQuickForm('form_Candidato');
        
        
        // create the form fields
        $id = new THidden('id');
        
        if (isset($param['key'])) {
          $id->setValue($param['key']);
        }
        
        $nome = new TEntry('nome');
        $nomePai = new TEntry('nomePai');
        $nomeMae = new TEntry('nomeMae');
        $rg = new TEntry('rg');
        $cpf = new TEntry('cpf');
        $dataNascimento = new TDate('dataNascimento');
        $sexo = new TCombo('sexo');
        $estadoCivil = new TCombo('estadoCivil');
        $endereco = new TEntry('endereco');
        $cep = new TEntry('cep');
        $numeroCasa = new TEntry('numeroCasa');
        $complemento = new TEntry('complemento');
        $telefone = new TEntry('telefone');
        $fone = new TEntry('fone');
        $possuiDeficiencia = new TRadioGroup('possuiDeficiencia');
        $this->tipoDeficiencia = new TEntry('tipoDeficiencia');
        $this->tipoDeficiencia->setEditable(FALSE);
        
        
        $avaliacao = new THidden('avaliacao');
        $estado_id = new TDBCombo('estado_id', 'con_ultimato', 'Estado', 'id', 'nome_estado');
        $estado_id->class = 'classe_estado';
        $cidade_id = new TCombo('cidade_id');
        $cidade_id->class = 'classe_cidade';
        
         $nome->addValidation("nome", new TRequiredValidator);
        $cpf->addValidation("cpf", new TCPFValidator);
        $rg->addValidation("rg", new TRequiredValidator);
        $dataNascimento->addValidation("dataNascimento", new TRequiredValidator);
        $sexo->addValidation("sexo", new TRequiredValidator);
        $estadoCivil->addValidation("estadoCivil", new TRequiredValidator);
        $endereco->addValidation("endereco", new TRequiredValidator);
        $cep->addValidation("cep", new TRequiredValidator);
        $telefone->addValidation("telefone", new TRequiredValidator);
        $possuiDeficiencia->addValidation("possuiDeficiencia", new TRequiredValidator);
        
        $estado_id->addValidation("estado_id", new TRequiredValidator);
        $cidade_id->addValidation("cidade_id", new TRequiredValidator);
        
        
        //maskaras e detalhes nas formatações dos campos
        $cpf->setMask('999.999.999-99');
        $cpf->placeholder = 'Ex.:000.000.000-00';
        
        $dataNascimento->class = 'classe_dtNasc';
        $dataNascimento->setMask('dd/mm/yyyy');
        $rg->setMask('99999999999999');
        
        $sexo->addItems(array( 'M' => 'MASCULINO', 'F' => 'FEMININO'));
        $telefone->setMask('(99)9.9999-9999');
        $telefone->placeholder = 'Ex.:(00)00000-0000';
        $fone->setMask('(99)9.9999-9999');
        $fone->placeholder = 'Ex.:(00)00000-0000';
        $cep->setMask('99999-999');
        $cep->placeholder="Ex.: 00000-000";
        $numeroCasa->setMask('99999999');
        $options = [1 => 'SIM', 0 => 'NÃO'];
        
        $possuiDeficiencia->addItems($options);
        //$possuiDeficiencia->setLayout('horizontal');
        $possuiDeficiencia->setLayout('horizontal');
        //$possuiDeficiencia->setBooleanMode();
        
        
        $estadoCivil->addItems(array( 'CASADO' => 'CASADO(A)', 'SOLTEIRO' => 'SOLTEIRO(A)',
         'UNIAO-ESTAVEL' => 'UNIÃO ESTÁVEL', 'DIVORCIADO' => 'DIVORCIADO(A)', 'VIUVO' => 'VIÚVO(A)'));
        
        $change_action = new TAction(array($this, 'onChangeAction'));
        $estado_id->setChangeAction($change_action);
        
        $change_radio_action = new TAction(array($this, 'onRadioChangeAction'));
        $possuiDeficiencia->setChangeAction($change_radio_action);
        
        

        // set sizes
        $nome->setSize('100%');
        $nomePai->setSize('100%');
        $nomeMae->setSize('100%');
        $rg->setSize('100%');
        $cpf->setSize('100%');
        $dataNascimento->setSize('100%');
        $sexo->setSize('100%');
        $estadoCivil->setSize('100%');
        $endereco->setSize('100%');
        $cep->setSize('100%');
        $numeroCasa->setSize('100%');
        $complemento->setSize('100%');
        $telefone->setSize('100%');
        $fone->setSize('100%');
        $this->tipoDeficiencia->setSize('80%');
        $estado_id->setSize('100%');
        $cidade_id->setSize('100%');
        //$avaliacao->setSize('100%');
 
         
        $table = new TTable('');
        
        
        $l1 = $table->addRow();
           
           $l1->addCell(new TLabel('Nome:'))->style='text-align:right;';
           $l1->addCell($nome)->colspan = 3;
           
           
        $l2 = $table->addRow();
           $l2->addCell(new TLabel('Nome Pai:'))->style='text-align:right';
           $l2->addCell($nomePai)->colspan = 3;
           
           
        $l3 = $table->addRow();
           $l3->addCell(new TLabel('Nome Mãe:'))->style='text-align:right';
           $l3->addCell($nomeMae)->colspan = 3;;
           
           
        $l4 = $table->addRow();
           $l4->addCell(new TLabel('Rg :'))->style='text-align:right';
           $l4->addCell($rg);
           $l4->addCell(new TLabel('Cpf :'))->style='text-align:right';
           $l4->addCell($cpf);
           
           
        $l5 = $table->addRow();
           $l5->addCell(new TLabel('Data Nascimento :'))->style='text-align:right';
           $l5->addCell($dataNascimento);
           $l5->addCell(new TLabel('Sexo :'))->style='text-align:right';
           $l5->addCell($sexo);
           
           
        $l6 = $table->addRow();
           $l6->addCell(new TLabel('Estado Civil :'))->style='text-align:right';
           $l6->addCell($estadoCivil);
           
           
        $l7 = $table->addRow();
           $l7->addCell(new TLabel('Endereço :'))->style='text-align:right';
           $l7->addCell($endereco);
           $l7->addCell(new TLabel('Cep :'))->style='text-align:right';
           $l7->addCell($cep);
           
           
        $l8 = $table->addRow();
           $l8->addCell(new TLabel('Número :'))->style='text-align:right';
           $l8->addCell($numeroCasa);
           $l8->addCell(new TLabel('Complemento :'))->style='text-align:right';
           $l8->addCell($complemento);
           
           
        $l9 = $table->addRow();
           $l9->addCell(new TLabel('Telefone :'))->style='text-align:right';
           $l9->addCell($telefone);
           $l9->addCell(new TLabel('Fone :'))->style='text-align:right';
           $l9->addCell($fone);
           
           
        $l10 = $table->addRow();
           $l10->addCell(new TLabel('Estado :'))->style='text-align:right';
           $l10->addCell($estado_id);
           $l10->addCell(new TLabel('Cidade :'))->style='text-align:right';
           $l10->addCell($cidade_id);
           
           
        $l11 = $table->addRow();
           $l11->addCell(new TLabel('Possui deficiencia :'))->style='text-align:right';
           $l11->addCell($possuiDeficiencia);
           
           
        $l12 = $table->addRow();
          $l12->addCell(new TLabel('Qual :'))->style='text-align:right';
          $l12->addCell($this->tipoDeficiencia)->colspan = 2;
          
          
      $this->form->add($table);
      
      $this->botao_next = TButton::create('Send', [$this, 'onNextForm'], 'PRÓXIMO PASSO', 'fa:chevron-circle-right green');
      
      //$this->botao_next
      //$this->form->addQuickAction('label', $ac = new TAction([$this, 'onNextEdit']), 'fa:chevron-circle-right green');
      //$ac->setParameter('key', 2);
      $this->form->add($id);
      $this->form->setFields([$id , $nome, $nomePai, $nomeMae, $rg, $cpf, $dataNascimento,
                                 $sexo, $estadoCivil, $endereco, $numeroCasa, $complemento, $telefone,$cep,
                                 $fone, $estado_id, $cidade_id, $possuiDeficiencia, $this->tipoDeficiencia
                                 ,$this->botao_next
                              ]);//tem que passar todos os campos por aqui..........................................
         
         
         
         //panel ao redor da tabela
         $panel = new TPanelGroup('');
         $t = new TTable('');
         $t->width = '100%';
         
         $linha1_t = $t->addRow();
         
         $linha1_t->addCell($this->botao_next)->style = 'text-align:right';
         
         $panel->addFooter($t);
         $panel->add($this->form);
      
        
        
        
        $container = new TVBox;
        $container->style = 'display: flex; flex-direction: column; justify-content: center; align-items: center;';
        $container->add($this->step);
        $container->add($panel);
        
        parent::add($container);
        
    }
    
    
    public function onLoadFromSession($param)
    {
        
        
        if (!TSession::getValue('logged') || TSession::getValue('tipo_usuario') != 'CANDIDATO') {
          AdiantiCoreApplication::gotoPage('PaginaPrincipalForm');
        }
        
        
        $data = TSession::getValue('form_step1_candidato');
        $this->form->setData($data);
        
        try {
           TTransaction::open('con_ultimato');
             if (isset($data->estado_id)) {
               $est = new Estado( $data->estado_id );
               $e['estado_id'] = $est->id;
               $this->onChangeAction($e);
               
                 $s2 =  new TScript;
                 $msg2 = "$(document).ready( function() {
                              
                                $('select[class=classe_cidade]').val('".$data->cidade_id."');
                                
                            });";
                 
                 $s2->create($msg2);
    
               
             }
           TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
        
    }
    
    
    public function onNextForm($param)
    {
    
    
        try
        {
            $this->form->validate();
            $data = $this->form->getData();
            
            // store data in the session
            TSession::setValue('form_step1_candidato', $data);
            
            if (TSession::getValue('form_step2_curriculo')) {
                  AdiantiCoreApplication::gotoPage('CurriculoForm2', 'onLoadFromSession');
            }else {
               if (TSession::getValue('fluxo')) {
      
                  $p['key'] = intval($param['id']);
                  
                  AdiantiCoreApplication::gotoPage('CurriculoForm2', 'onEdit', $p);
               }else {
                 AdiantiCoreApplication::gotoPage('CurriculoForm2');
               }     
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
   public static function onChangeAction($param)
    { 
        try {
          
          TTransaction::open('con_ultimato');
          $repos = new TRepository('Cidade');
          $options = $repos->where('estado_id', '=', $param['estado_id'])->load();
          
          foreach($options as $option) {
             $cidades[$option->id] = $option->nome_cidade;
          }
          
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
        if (isset($cidades))
          TCombo::reload('form_Candidato', 'cidade_id', $cidades);
        else
          TCombo::reload('form_Candidato', 'cidade_id', '');  
    }
    
    
    
    public static function onRadioChangeAction($param)
    { 
          if ($param['possuiDeficiencia'] == 1)
            TEntry::enableField('form_Candidato', 'tipoDeficiencia');
          else {
            TEntry::disableField('form_Candidato', 'tipoDeficiencia');
            TEntry::clearField('form_Candidato', 'tipoDeficiencia');
          }
    }
    
    public function onEdit( $param ){
       
        
        
        if (!TSession::getValue('logged') || TSession::getValue('tipo_usuario') != 'CANDIDATO') {
          AdiantiCoreApplication::gotoPage('PaginaPrincipalForm');
        }
       
       try{
          if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('con_ultimato'); // open a transaction
                $object = new Candidato($key); // instantiates the Active Record
                $this->form->setData($object); // fill the form
                $city = new Cidade( $object->cidade_id );
                $est = new Estado( $city->estado_id );
                
                
                $d =  new TScript;
                 $msg = "$(document).ready( function() {
                              
                                $('input[class=classe_dtNasc]').val('".TDate::date2br($object->dataNascimento)."');
                                
                            });";
                 
                 $d->create($msg);
                 
                
                
                
                 $s =  new TScript;
                 $msg = "$(document).ready( function() {
                              
                                $('select[class=classe_estado]').val('".$est->id."');
                                
                            });";
                 
                 $s->create($msg);
                 
                 $e['estado_id'] = $est->id;             
                 $this->onChangeAction($e);
                  
                 
                 $s2 =  new TScript;
                 $msg2 = "$(document).ready( function() {
                              
                                $('select[class=classe_cidade]').val('".$city->id."');
                                
                            });";
                 
                 $s2->create($msg2);
                 
                if ($object->possuiDeficiencia == 1) {
                  $this->tipoDeficiencia->setEditable(TRUE);
                }
                
                TSession::setValue('fluxo', 'onEdit');
                
                
                TTransaction::close(); // close the transaction
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    
        
}
