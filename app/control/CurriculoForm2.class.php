<?php

class CurriculoForm2 extends TPage {
    
    
    private $form; // form
    private $step;
    private $btn_add_nova_idioma;
    private $table_idioma;
    private $frame_foto;
    private $botao_next;
    private $candid;
        
    public function __construct( $param )
    {
        if ( TSession::getValue('tipo_usuario') == 'FUNCIONARIO' || TSession::getValue('tipo_usuario') == 'ADMINISTRADOR') {
          AdiantiCoreApplication::gotoPage('PaginaPrincipalForm');
        }
        
        parent::__construct();
        
        $id_curr = new THidden('id_curr');
        $id_curr->setSize('30%');
        $id_curr->setEditable(false);
        
        $this->step = new TBreadCrumb;
        $this->step->addItem('CANDIDATO', FALSE);
        $this->step->addItem('CURRÍCULO', FALSE);
        $this->step->addItem('USUÁRIO', TRUE);
        $this->step->select('CURRÍCULO');
        
        $table_geral = new TTable('tabela_geral');
        
        
        
        $del = new TButton('Del');
        $del->setImage('fa:trash-o red');
        $del->addFunction("ttable_remove_row(this)");
        
        
        //tabela foto----------------------------------------------------
        $table_foto = new TTable('tabela_foto');
        //$table_foto->border = 1;
        
        $table_foto->style = "width:100%; text-align:center;border-style: solid;border-bottom-width: 1px;border-top-width: 0;border-right-width: 0;border-left-width: 0;";
        
        $linha1_tabela_foto = $table_foto->addRow();
        
        $table_menor_foto = new TTable('table_menor_foto');
        $table_menor_foto->style = "width:100%;";
        
        $file_foto = new TFile('photo_path');
        
        $file_foto->setCompleteAction(new TAction(array($this, 'onComplete')));
        $file_foto->setAllowedExtensions(['gif', 'png', 'jpg', 'jpeg']);
        
        $this->frame_foto = new TElement('div');
        $this->frame_foto->id = "photo_frame";
        $this->frame_foto->style = "width:50px; min-height:60px; border:2px solid gray; display: block; margin: auto;";
         
        //$file_foto->addValidation('photo_path', new TRequiredValidator);
        
        $linha1_table_menor_foto = $table_menor_foto->addRow();
        $linha1_table_menor_foto->addCell('Foto do Perfil:');
        $linha1_table_menor_foto = $table_menor_foto->addRow();
        $linha1_table_menor_foto->addCell($this->frame_foto);
        $linha1_table_menor_foto = $table_menor_foto->addRow();
        $linha1_table_menor_foto->addCell($file_foto);
        
        $linha1_tabela_foto->addCell($id_curr)->width = '33%';
        $linha1_tabela_foto->addCell($table_menor_foto);
        $linha1_tabela_foto->addCell('')->width = '33%';
        
        //fim da tabela foto----------------------------------------------
        
        
        
        
        
        //objetivos tabela de area de interesse----------------------------------------------------------------------------------
        $table_area_de_interesse = new TTable('table_area_de_interesse');
        
        $table_area_de_interesse->style = "width:100%; text-align:center; border-style: solid; border-bottom-width: 1px; border-top-width: 0; border-right-width: 0; border-left-width: 0;";
        
        $panel_area_de_interesse = new TPanel(600,60);
        $id_area = new THidden('idareaECurriculo[]');
        
        $id_area->setSize('30%');
        //$id_area->setEditable(false);
        $id_area->class = "classe_idareaECurriculo";
        
        $area_de_interesse = new TDBCombo('area_de_interesse[]', 'con_ultimato', 'AreaDeInteresse', 'id', 'descricao');
        $area_de_interesse->class = "classe_area_de_interesse";
        $area_de_interesse->style = 'width:350px';
        $s_area_de_interesse = new TLabel('OBJETIVOS (cargo pretendido)');
        $s_area_de_interesse->style = 'color:green; font-weight: bold;';
        
        $panel_area_de_interesse->put($id_area, 0 , 0);
        $panel_area_de_interesse->put($s_area_de_interesse, 200,10);
        $panel_area_de_interesse->put($area_de_interesse, 150,30);
        
        $linha1_tabela_area_de_interesse = $table_area_de_interesse->addRow();
        //$linha1_tabela_area_de_interesse->addCell($id_area);
        
        $linha1_tabela_area_de_interesse->addCell($panel_area_de_interesse);
        //$del->id = $id_area->getValue(); nao deu
        $linha1_tabela_area_de_interesse->addCell($del);
        
        $table_area_de_interesse->addSection('tfoot');
        
        $btn_add_nova_area_de_interesse = new TButton('clone');
        $btn_add_nova_area_de_interesse->setId('btn_clone_area_de_interesse');
        $btn_add_nova_area_de_interesse->setImage('fa:plus-circle green');
        $btn_add_nova_area_de_interesse->addFunction('ttable_clone_previous_row(this)');//Duplicar a linha anterior
        $btn_add_nova_area_de_interesse->setLabel('Adicionar Area De Interesse');
        $btn_add_nova_area_de_interesse->setName('btn_clone_area_de_interesse');
        
        $table_area_de_interesse->addRowSet( $btn_add_nova_area_de_interesse );
        
        //fim de objetivos da tabela area de interesse----------------------------------------------------------------------------------
        
        //tabela pretensao -------------------------------------------------------------------------------
        
        $table_pretensao = new TTable('table_pretensao');
        
        
        $table_pretensao->style = "width:100%; text-align:center;border-style: solid;border-bottom-width: 1px;border-top-width: 0;border-right-width: 0;border-left-width: 0;";

        
        $panel_pretensao = new TPanel(600,60);
        
        $pretensao = new TEntry('pretensao');
        $pretensao->setNumericMask(2, ',', '.', true);
        
        $s_pretensao = new TLabel('PRETENSÃO');
        $s_pretensao->style = 'color:green; font-weight: bold;';
        
        //$pretensao->addValidation('pretensao', new TRequiredValidator);
        
        $panel_pretensao->put($s_pretensao, 250,10);
        $panel_pretensao->put($pretensao, 220,30);
        $panel_pretensao->put('R$', 200,35);
        
        $linha1_tabela_pretensao = $table_pretensao->addRow();
        $linha1_tabela_pretensao->addCell($panel_pretensao);
        
        
        //fim da tbela pretensao--------------------------------------------------------------------------
        
        
        //tabela formacao----------------------------------------------------------------------------------
        $table_formacao = new TTable('tabela_formacao');
        
        $table_formacao->style = "width:100%; text-align:center;border-style: solid;border-bottom-width: 1px;border-top-width: 0;border-right-width: 0;border-left-width: 0;";
        
        $id_formacao = new THidden('id_formacao[]');
        $id_formacao->setSize('30%');
        //$id_formacao->setEditable(false);
        $id_formacao->class = "classe_id_formacao";
        
        $panel = new TPanel(600,130);
        
        $instituicao = new TEntry('instituicao[]');
        $instituicao->class = "classe_instituicao";
        $instituicao->addValidation('instituicaoegua', new TRequiredValidator);
        $instituicao->placeholder = 'Digite aqui...';
        $grauDeEscolaridade = new TCombo('grauDeEscolaridade[]');
        $grauDeEscolaridade->class = "classe_grauDeEscolaridade";
        $grauDeEscolaridade->addItems(array( 'DOUTORADO'=> 'DOUTORADO', 'MESTRADO'=> 'MESTRADO','POS-GRADUADO'=> 'PÓS-GRADUADO',
         'SUPERIOR'=> 'SUPERIOR', 'MEDIO'=> 'MÉDIO', 'FUNDAMENTAL'=> 'FUNDAMENTAL', 'ANDAMENTO'=> 'EM ANDAMENTO'));
        
        $dataComeco = new TDate('dataComeco[]');
        $dataComeco->class = "classe_dataComeco";
        $dataComeco->setMask('dd/mm/yyyy');
        
        $dataFim = new TDate('dataFim[]');
        $dataFim->class = "classe_dataFim";
        $dataFim->setMask('dd/mm/yyyy');
        
        $curso_id = new TDBCombo('curso_id[]', 'con_ultimato', 'Cursos', 'id', 'nome');
        $curso_id->class = "classe_curso_id";
        $curso_id->setDefaultOption('curso');
        $panel->put($id_formacao, 0, 0);
        $panel->put('Grau de escolaridade', 10,10);
        $panel->put($grauDeEscolaridade, 10,30);
        $panel->put($curso_id, 10,70);
        
        $s_formacao = new TLabel('FORMACÃO:');
        $s_formacao->style = 'color:green; font-weight: bold;';
        
        //caso qualquer um dos itens esteja setado os outros deverão estar também.
        $instituicao->setExitAction(new TAction([$this, 'onChangeFormation']));
        
        $panel->put($s_formacao, 240,10);
        $panel->put('Instituição de Ensino:', 180,60);
        $panel->put($instituicao, 180,80);
        
        $panel->put('Data Início', 390,10);
        $panel->put($dataComeco, 390,30);
        $panel->put('Data de término', 390,70);
        $panel->put($dataFim, 390,90);
        
        
        $linha1_tabela_formacao = $table_formacao->addRow();
        $linha1_tabela_formacao->addCell($panel);
        
        $linha1_tabela_formacao->addCell($del);
        
        $table_formacao->addSection('tfoot');
        
        $btn_add_nova_formacao = new TButton('clone');
        $btn_add_nova_formacao->setId('btn_clone_formacao');
        $btn_add_nova_formacao->setImage('fa:plus-circle green');
        $btn_add_nova_formacao->addFunction('ttable_clone_previous_row(this)');//Duplicar a linha anterior
        $btn_add_nova_formacao->setLabel('Adicionar Nova Formação');
        $btn_add_nova_formacao->setName('btn_clone_formacao');
        
        
        $table_formacao->addRowSet( $btn_add_nova_formacao );
        
        //fim da tabela formacao---------------------------------------------------------------------------
        
        
        
        
        
        
        
        //tabela experiencia profissional
        $table_expprof = new TTable('table_expprof');
        //$table_expprof->border = 2;
        $table_expprof->style = "width:100%; text-align:center;border-style: solid;border-bottom-width: 1px;border-top-width: 0;border-right-width: 0;border-left-width: 0;";

        //$num_ram = $uniqid = mt_rand(1000000, 9999999);
        
        $id_expprof = new THidden('id_expprof[]');
        //$id_expprof->setSize('30%');
        //$id_expprof->setEditable(false);
        $id_expprof->class ="classe_id_expprof";
        
        $panel_expprof = new TPanel(600,130);
        $seu_cargo = new TEntry('seu_cargo[]');
        $seu_cargo->class = "classe_seu_cargo";
        $seu_cargo->placeholder = 'seu cargo';
        $empresa_em_que_trabalhou = new TEntry('empresa_em_que_trabalhou[]');
        $empresa_em_que_trabalhou->class = "classe_empresa_em_que_trabalhou";
        $empresa_em_que_trabalhou->placeholder = 'Empresa que trabalhou';
        $sua_exp_e_seus_projetos = new TEntry('sua_exp_e_seus_projetos[]');
        $sua_exp_e_seus_projetos->class = "classe_sua_exp_e_seus_projetos";
        //$sua_exp_e_seus_projetos->setId('textarea_'.$uniqid);
        $sua_exp_e_seus_projetos->placeholder = 'Escreva suas experiências e seus projetos';
        $sua_exp_e_seus_projetos->setSize('300', '50');
        
        
        $dataComeco_expprof = new TDate('dataComeco_expprof[]');
        $dataComeco_expprof->class = "classe_dataComeco_expprof";
        //$dataComeco_expprof->style = "position:absolute";
        $dataComeco_expprof->style = "width:90px";
        $dataComeco_expprof->setMask('dd/mm/yyyy');
        $dataFim_expprof = new TDate('dataFim_expprof[]');
        $dataFim_expprof->class = "classe_dataFim_expprof";
        $dataFim_expprof->setMask('dd/mm/yyyy');
        //$dataFim_expprof->style = "position:absolute";
        $dataFim_expprof->style = "width:90px";
        
        $panel_expprof->put($id_expprof, 0 ,0);
        $panel_expprof->put($seu_cargo, 10,30);
        $panel_expprof->put($sua_exp_e_seus_projetos, 10,70);
        
        $s_expprof = new TLabel('EXPERIÊNCIA PROFISSIONAL');
        $s_expprof->style = 'color:green; font-weight: bold;';
        
        
        $panel_expprof->put($s_expprof, 190,10);
        $panel_expprof->put($empresa_em_que_trabalhou, 190,30);
        
        $panel_expprof->put('Data Início', 390,10);
        $panel_expprof->put($dataComeco_expprof, 390,30);
        $panel_expprof->put('Data de término', 390,70);
        $panel_expprof->put($dataFim_expprof, 390,90);
        
        
        
        $linha1_tabela_expprof = $table_expprof->addRow();
        $linha1_tabela_expprof->addCell($panel_expprof);
        $linha1_tabela_expprof->addCell($del);
        
        $table_expprof->addSection('tfoot');
        
        $btn_add_nova_expprof = new TButton('clone');
        $btn_add_nova_expprof->setId('btn_clone_expprof');
        $btn_add_nova_expprof->setName('btn_clone_expprof');
        $btn_add_nova_expprof->setImage('fa:plus-circle green');
        $btn_add_nova_expprof->setLabel('Adicionar Exp. Profissional');
        $btn_add_nova_expprof->addFunction('ttable_clone_previous_row(this)');//Duplicar a linha anterior
        
        $table_expprof->addRowSet( $btn_add_nova_expprof );
        
        //FIM tabela experiencia profissional FIM
        
        
        
        
        
        
        
        
        
        //qualificacoes e atividades profissionais-------------------------------
        
        $table_qualif = new TTable('table_qualif');
        //$table_qualif->border = 1;
        $table_qualif->style = "width:100%; text-align:center;border-style: solid;border-bottom-width: 1px;border-top-width: 0;border-right-width: 0;border-left-width: 0;";

        $id_cursos_complem = new THidden('id_cursos_complem[]');
        $id_cursos_complem->setSize('30%');
        //$id_cursos_complem->setEditable(false);
        $id_cursos_complem->class = 'classe_id_cursos_complem';
        
        $panel_qualif = new TPanel(600,130);
        $nome_do_curso = new TEntry('nome_do_curso[]');
        $nome_do_curso->class="classe_nome_do_curso";
        $nome_do_curso->placeholder = 'Nome do curso';
        $empresa_de_ensino = new TEntry('empresa_de_ensino[]');
        $empresa_de_ensino->class="classe_empresa_de_ensino";
        $empresa_de_ensino->placeholder = 'Empresa de ensino';
        $carga_horaria = new TEntry('carga_horaria[]');
        $carga_horaria->class="classe_carga_horaria";
        
        $carga_horaria->setMask('9999');
        $dataComeco_qualif = new TDate('dataComeco_qualif[]');
        $dataComeco_qualif->class="classe_dataComeco_qualif";
        $dataComeco_qualif->setMask('dd/mm/yyyy');
        $dataFim_qualif = new TDate('dataFim_qualif[]');
        $dataFim_qualif->class="classe_dataFim_qualif";
        $dataFim_qualif->setMask('dd/mm/yyyy');
        
        $panel_qualif->put($id_cursos_complem,0,0);
        $panel_qualif->put($nome_do_curso, 10,30);
        $panel_qualif->put($empresa_de_ensino, 200,30);
        
        $s_qualif = new TLabel('QUALIFICAÇÕES E ATIVIDADES PROFISSIONAIS');
        $s_qualif->style = 'color:green; font-weight: bold;';
        
        
        $panel_qualif->put($s_qualif, 150,10);
        
        $panel_qualif->put('Data Início', 10,70);
        $panel_qualif->put($dataComeco_qualif, 10,90);
        $panel_qualif->put('Data de término', 200,70);
        $panel_qualif->put($dataFim_qualif, 200,90);
        $panel_qualif->put('Carga Horária', 390,40);
        $panel_qualif->put($carga_horaria, 390,60);
        
        
        $linha1_tabela_qualif = $table_qualif->addRow();
        $linha1_tabela_qualif->addCell($panel_qualif);
        $linha1_tabela_qualif->addCell($del);
        
        $table_qualif->addSection('tfoot');
        
        $btn_add_nova_qualif = new TButton('clone');
        $btn_add_nova_qualif->setId('btn_clone_qualif');
        $btn_add_nova_qualif->setName('btn_clone_qualif');
        $btn_add_nova_qualif->setImage('fa:plus-circle green');
        $btn_add_nova_qualif->addFunction('ttable_clone_previous_row(this)');//Duplicar a linha anterior
        $btn_add_nova_qualif->setLabel('Adicionar Quali. e Atividades Prof.');
        
        $table_qualif->addRowSet( $btn_add_nova_qualif );
        
        //FIM qualificacoes e atividades profissionais FIM
        
        
      
        //IDIOMASSSSSSSSSSS-----------------------------------------------------
        
        $this->table_idioma = new TTable('table_idioma');
        $this->table_idioma->addSection('tbody');
        $this->table_idioma->style = "width:100%; text-align:center;border-style: solid;border-bottom-width: 1px;border-top-width: 0;border-right-width: 0;border-left-width: 0;";

        $panel_idioma = new TPanel(600,90);
        $unique_idiomas = mt_rand(1000,9999);
        
        $id_nivelIdioma = new THidden('id_nivelIdioma[]');
        $id_nivelIdioma->setSize('30%');
        //$id_nivelIdioma->setEditable(false);
        $id_nivelIdioma->class = 'classe_nivelidioma';
        
        $idioma = new TDBCombo('idioma[]', 'con_ultimato', 'Idioma', 'id', 'nome');
        $idioma->class = 'tcombo_idiomas';
        
        //$idioma->enableSearch();
        
        $nivel = new TCombo('nivel[]');
        $nivel->class = 'tcombo_nivel';
        
        $nivel->addItems(array( 'BASICO'=> 'BÁSICO', 'INTERMEDIARIO'=> 'INTERMEDIÁRIO','AVANCADO'=> 'AVANÇADO'));
        
        $s_idioma = new TLabel('IDIOMAS');
        $s_idioma->style = 'color:green; font-weight: bold;';
        
        
        
        $panel_idioma->put($id_nivelIdioma,0,0);
        $panel_idioma->put($s_idioma, 250,10);
        
        $panel_idioma->put('IDIOMA', 130,30);
        $panel_idioma->put($idioma, 130,50);
        $panel_idioma->put('NIVEL', 300,30);
        $panel_idioma->put($nivel, 300,50);
        
        
        $linha1_tabela_idioma = $this->table_idioma->addRow();
        $linha1_tabela_idioma->addCell($panel_idioma);
        $linha1_tabela_idioma->addCell($del);
        
        $this->table_idioma->addSection('tfoot');
        
        $this->table_idioma->style = "text-align:center";
        $this->onClear_idiomas();
        //FIM  IDIOMAS FIM
        
        
        
        
        
        //adicionando todos os requisitos na tabela principal-------------------
        //$table_geral->border = 1;
        
        
        $table_geral->style = "text-align:center";
        
        $linha1_table_geral = $table_geral->addRow();
        $linha2_table_geral = $table_geral->addRow();
        $linha3_table_geral = $table_geral->addRow();
        $linha4_table_geral = $table_geral->addRow();
        $linha5_table_geral = $table_geral->addRow();
        $linha6_table_geral = $table_geral->addRow();
        $linha7_table_geral = $table_geral->addRow();
        
        
        $linha1_table_geral->addCell($table_foto);
        $linha2_table_geral->addCell($table_area_de_interesse);//objetivos
        $linha3_table_geral->addCell($table_pretensao);
        $linha4_table_geral->addCell($table_formacao);
        $linha5_table_geral->addCell($table_expprof);
        $linha6_table_geral->addCell($table_qualif);
        $linha7_table_geral->addCell($this->table_idioma);
        
        
         $this->form = new TQuickForm('form_curriculo');
       
         $this->form->add($table_geral);
         $this->botao_next = TButton::create('Confirm', [$this, 'onNextForm'], 'PRÓXIMO PASSO', 'fa:chevron-circle-right green');
         $botao_back = TButton::create('back', [$this, 'onBackForm'], 'VOLTAR PASSO', 'fa:chevron-circle-left red');
         
         //todos os campos passando pro setfields pra o formulario entender.
         
         $id_candidato = new THidden('id_candidato');
         if (isset($param['key']))
           $id_candidato->setValue(intval($param['key']));
         
         $this->form->add($id_candidato);
         $this->form->setFields([$id_candidato ,$id_curr, $id_area, $id_formacao, $id_expprof, $id_cursos_complem, $id_nivelIdioma,
                                 $this->botao_next, $botao_back, 
                                 $file_foto, 
                                 $area_de_interesse,
                                 $pretensao,
                                 $instituicao, $grauDeEscolaridade, $dataComeco, $dataFim, $curso_id, //formacao
                                 $seu_cargo,$empresa_em_que_trabalhou, $sua_exp_e_seus_projetos,$dataComeco_expprof,$dataFim_expprof, //exp prof
                                 $nome_do_curso,$empresa_de_ensino, $carga_horaria, $dataComeco_qualif, $dataFim_qualif, //qualif
                                 $idioma,$nivel //idiomas
                                 ,$del
                               ]); 
         
         
         
         $panel_final = new TPanelGroup('');
         $panel_final->add($this->form);
         
         //organizando botoes next e back
         $t = new TTable('');
         $t->style = 'width:100%';
         
         $linha1_t = $t->addRow();
         $linha1_t->addCell($botao_back)->style = 'text-align:left';
         $linha1_t->addCell($this->botao_next)->style = 'text-align:right';
         
         $panel_final->addFooter($t);
         
         
         $container = new TVBox;
         $container->style = 'display: flex; flex-direction: column; justify-content: center; align-items: center';
         $container->add($this->step);
         
         $container->add($panel_final);
        
        parent::add($container); 
    }
    
    
    
    
    
     public function onEdit($param) {
          //$this->form->validate();
          if (!TSession::getValue('logged') || TSession::getValue('tipo_usuario') != 'CANDIDATO') {
            AdiantiCoreApplication::gotoPage('PaginaPrincipalForm');
          }
          
          try{
            TTransaction::open('con_ultimato');
            
            if (isset($param['key']))
            {
                
                
                $cand      = new Candidato($param['key']); 
                $curriculo = new Curriculo($cand->curriculo_id);
                $key = $cand->curriculo_id;
                
                $data = new stdClass;
                
                $this->candid = $cand->id;
                $data->photo_path = $cand->foto;
                $data->id_curr = $key;
                   
                // load area de interesse
                $AreaECurriculos = Area_e_curriculo::where('curriculo_id', '=', $key)->load();
                    if (isset($AreaECurriculos))
                    {
                        foreach ($AreaECurriculos as $AreaECurriculo)
                        {
                            $area_de_interesse  = new AreaDeInteresse( $AreaECurriculo->areadeinteresses_id );
                            
                            $area[]             = $area_de_interesse->id;
                            $aec[] = $AreaECurriculo->id;
                            //$y_area[$AreaECurriculo->id] = $area_de_interesse->id;
                        }
                        //$data->y_area = $y_area;
                        $data->area_de_interesse = $area;
                        $data->idareaECurriculo = $aec;
                        
                    }
                //tem que ver esse daqui
                if (isset($curriculo->pretensaoSalarial))
                 $data->pretensao = $curriculo->pretensaoSalarial;
                      
                //load formacao
                $formacaos = Formacao::where('curriculo_id', '=', $key)->load();
                    if (isset($formacaos))
                    {
                        foreach ($formacaos as $formacao)
                        {
                            //new TMessage('info', 'formacao encontrado=>'.$formacao->id);
                            //$descricao[]          = $formacao->descricao;
                            $instituicao[]        = $formacao->instituicao;
                            $grauDeEscolaridade[] = $formacao->grauDeEscolaridade;
                            $dataComeco[]         = TDate::date2br( $formacao->dataComeco);
                            $dataFim[]            = TDate::date2br( $formacao->dataFim);
                            $curso_id[]           = $formacao->curso_id;
                            $data->id_formacao[] = $formacao->id;
                        }
                      if (isset($instituicao))  
                        $data->instituicao = $instituicao;
                      if (isset($grauDeEscolaridade))
                        $data->grauDeEscolaridade = $grauDeEscolaridade;
                      if (isset($dataComeco))
                        $data->dataComeco = $dataComeco;
                      if (isset($dataFim))
                        $data->dataFim = $dataFim;
                      if (isset($curso_id))
                        $data->curso_id = $curso_id;
          
                    }
                
                //load exp prof
                $expprofs = ExperienciaProfissional::where('curriculo_id', '=', $key)->load();
                    if (isset($expprofs))
                    {
                        foreach ($expprofs as $expprof)
                        {
                              $seu_cargo[]                = $expprof->cargo;
                              $empresa_em_que_trabalhou[] = $expprof->empresa;
                              $sua_exp_e_seus_projetos[]  = $expprof->detalhes;
                              $dataComeco_expprof[]       = TDate::date2br($expprof->dataComeco);
                              $dataFim_expprof[]          = TDate::date2br($expprof->dataFim);
                              $data->id_expprof[] = $expprof->id;
                              //ta ok
                        }
                        if (isset($seu_cargo))
                          $data->seu_cargo = $seu_cargo;
                        if (isset($empresa_em_que_trabalhou))
                          $data->empresa_em_que_trabalhou = $empresa_em_que_trabalhou;
                        if (isset($sua_exp_e_seus_projetos))
                          $data->sua_exp_e_seus_projetos = $sua_exp_e_seus_projetos;
                        if (isset($dataComeco_expprof))
                          $data->dataComeco_expprof = $dataComeco_expprof;
                        if (isset($dataFim_expprof))
                          $data->dataFim_expprof = $dataFim_expprof;
          
                    }        
                
                //load cursos complem
                $cursos_complems = CursosComplementares::where('curriculo_id', '=', $key)->load();
                    if (isset($cursos_complems))
                    {
                    
                        foreach ($cursos_complems as $cursos_complem)
                        {
                              //$cursos[] = new Cursos($cursos_complem->curso_id);
                            
                              $nome_do_curso[]     = $cursos_complem->decricao;
                              $empresa_de_ensino[] = $cursos_complem->instituicao;
                              $carga_horaria[]     = $cursos_complem->cargaHoraria;
                              $dataComeco_qualif[] = TDate::date2br($cursos_complem->dataComeco);
                              $dataFim_qualif[]    = TDate::date2br($cursos_complem->dataFim);
                              $data->id_cursos_complem[] = $cursos_complem->id;
                        }
                          if (isset($nome_do_curso))
                            $data->nome_do_curso = $nome_do_curso;
                          if (isset($empresa_de_ensino))
                            $data->empresa_de_ensino = $empresa_de_ensino;
                          if (isset($carga_horaria))
                            $data->carga_horaria = $carga_horaria;
                          if (isset($dataComeco_qualif))
                            $data->dataComeco_qualif = $dataComeco_qualif;
                          if (isset($dataFim_qualif))
                            $data->dataFim_qualif = $dataFim_qualif;
                    }
                
                //load idioma
                $nivelidiomas = Nivel_Idioma::where('curriculo_id', '=', $key)->load();
                    if (isset($nivelidiomas))
                    {
                    
                        foreach ($nivelidiomas as $nivelidioma)
                        {
                            $idiomass[] = $nivelidioma->idioma_id;
                            $nivelss[]  = strtoupper($nivelidioma->nivel);
                            
                            $data->id_nivelIdioma[] = $nivelidioma->id;
                        }
                      if (isset($idiomass))
                        $data->idioma = $idiomass;
                      
                      if (isset($nivelss))  
                        $data->nivel = $nivelss ;
                    }
                    
          
          TSession::setValue('form_step2_curriculo', $data);
          $this->onLoadFromSession();
                         
          TTransaction::close(); // close transaction
        }
            
        }
        catch (Exception $e){
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
       }
    
    
    
    
    
    
    
    
    
    
      
        public function onLoadFromSession($param = null){   //da sessão para o html
          //$this->form->validate();
          if (!TSession::getValue('logged') || TSession::getValue('tipo_usuario') != 'CANDIDATO') {
            AdiantiCoreApplication::gotoPage('PaginaPrincipalForm');
          }
          
          $data = TSession::getValue('form_step2_curriculo');
          //$d = TSession::getValue('form_step3_usuario');
                    
          if (isset($data)) {             //só serve pra dado unico sem []  se tiver sessao faz isso
            $this->form->setData($data); 
          }else {                  //se n tiver dados na sessao ...recebe zero pra nao entrar no if      
            $data = new stdClass;
            $data->photo_path = '';
            //$data->id_curr = '';
            //$data->idareaECurriculo[0] = '';
            //$data->id_cursos_complem[0] = '';
            //$data->id_formacao[0] = '';
            //$data->id_expprof[0] = '';
            $data->idioma[0] = '';
            $data->area_de_interesse[0] = '';
            $data->grauDeEscolaridade[0] = '';
            $data->seu_cargo[0] = '';
            $data->nome_do_curso[0] = '';
          }
          
           
          //puxa a foto da sessao e seta novamente no formulario html
          if (isset($data->photo_path)) {
            if (substr($data->photo_path, 0, 3) == 'app') {
              TScript::create("$('#photo_frame').append(\"<img style='width:49px; height:59px;' src='{$data->photo_path}'>\");");
            }
            else
              TScript::create("$('#photo_frame').append(\"<img style='width:49px; height:59px;' src='tmp/{$data->photo_path}'>\");");
          }
          
          
          //puxa a area da sessao e seta novamente no formulario html
         $n = 0; 
          if (isset($data->area_de_interesse[$n])){
            while (isset($data->area_de_interesse[$n])) { //contador de quantos valores serão carregados da sessão para os campos clones.
                $n++;
              }
              $response = new stdClass;
              $response->{'idareaECurriculo[]'} = $data->idareaECurriculo[0];
              
              $response->{'area_de_interesse[]'} = $data->area_de_interesse[0];
              $this->form->sendData('form_curriculo', $response);
              
              
              for ($i = 0; $i < $n-1; $i++) {   
                 $this->clicar_no_botao_de_clonagem('tbutton_btn_clone_area_de_interesse');
                 
                 
                 $s = new TScript();
                 
                 $msg = "$(document).ready( function() {
                           $('input[class=classe_idareaECurriculo]').each(function() {
                              if ($(this).val() == '') {"
                                ."$(this).val('".$data->idareaECurriculo[$i+1]."');"
                                ."return false;
                              }
                           });
                           
                           
                           
                           
                           $('select[class=classe_area_de_interesse]').each(function() {
                              if ($(this).val() == '') {"
                                ."$(this).val('".$data->area_de_interesse[$i+1]."');"
                                ."return false;
                              }
                           });
                           
                        });
                   ";
                $s->create($msg);
                 
              }
            
          }
         
          
          //puxa a FORMACAO da sessao e seta novamente no formulario html
          $n = 0;
          
          if (isset($data->grauDeEscolaridade[$n]) and $data->grauDeEscolaridade[$n] != ''){
          
            while (isset($data->grauDeEscolaridade[$n])) { //contador de quantos valores serão carregados da sessão para os campos clones.
                $n++;
              }
              $response = new stdClass;
              $response->{'id_formacao'} = $data->id_formacao[0];
              $response->{'grauDeEscolaridade[]'} = $data->grauDeEscolaridade[0];
              $response->{'instituicao[]'} = $data->instituicao[0];
              $response->{'dataComeco[]'} = ($data->dataComeco[0]);
              $response->{'dataFim[]'} = ($data->dataFim[0]);
              $response->{'curso_id[]'} = $data->curso_id[0];
              
              $this->form->sendData('form_curriculo', $response);
              
              for ($i = 0; $i < $n-1; $i++) {   
                 $this->clicar_no_botao_de_clonagem('tbutton_btn_clone_formacao');
                 
                 $s = new TScript();
                 
                 $msg = "$(document).ready( function() {
                           $('input[class=classe_id_formacao]').each(function() {
                              if ($(this).val() == '') {"
                                ."$(this).val('".$data->id_formacao[$i+1]."');"
                                ."return false;
                              }
                           });
                           
                           
                           $('input[class=classe_instituicao]').each(function() {
                              if ($(this).val() == '') {
                                $(this).val('".$data->instituicao[$i+1]."');
                                  return false;
                              }
                            });"
                                
                                ."$('select[class=classe_grauDeEscolaridade]').each(function() {
                                   if ($(this).val() == '') {
                                     $(this).val('".$data->grauDeEscolaridade[$i+1]."');
                                       return false;
                                   }
                                  });"
                                     
                                     ."$('select[class=classe_curso_id]').each(function() {
                                       if ($(this).val() == '') {
                                         $(this).val('".$data->curso_id[$i+1]."');
                                           return false;
                                        }
                                      });"
                                         
                                         ."$('input[class=classe_dataComeco]').each(function() {
                                           if ($(this).val() == '') {
                                             $(this).val('".($data->dataComeco[$i+1])."');
                                               return false;
                                            }
                                          });" 
                                          
                                             ."$('input[class=classe_dataFim]').each(function() {
                                               if ($(this).val() == '') {
                                                 $(this).val('".($data->dataFim[$i+1])."');
                                                   return false;
                                               }
                                              });
                      }); ";
                $s->create($msg);
              }
          }
         
          
          //puxa a EXP PROF da sessao e seta novamente no formulario html
          $n = 0;
          if (isset($data->seu_cargo[$n]) && $data->seu_cargo[$n] != ''){
            while (isset($data->seu_cargo[$n])) { //contador de quantos valores serão carregados da sessão para os campos clones.
                $n++;
            }
            
            
              $response = new stdClass;
              $response->{'id_expprof[]'} = $data->id_expprof[0];
              $response->{'seu_cargo[]'} = $data->seu_cargo[0];
              $response->{'empresa_em_que_trabalhou[]'} = $data->empresa_em_que_trabalhou[0];
              $response->{'sua_exp_e_seus_projetos[]'} = $data->sua_exp_e_seus_projetos[0];
              $response->{'dataComeco_expprof[]'} = ($data->dataComeco_expprof[0]);
              $response->{'dataFim_expprof[]'} = ($data->dataFim_expprof[0]);
              
              $this->form->sendData('form_curriculo', $response);
              
              
              for ($i = 1; $i < $n; $i++) {
                 
                 $this->clicar_no_botao_de_clonagem('tbutton_btn_clone_expprof');
                 
                 $s = new TScript();
                 
                 $msg = "$(document).ready( function() {
                           $('input[class=classe_id_expprof]').each(function() {
                              if ($(this).val() == '') {"
                                ."$(this).val('".$data->id_expprof[$i]."');"
                                ."return false;
                              }
                           });
                           
                           $('input[class=classe_seu_cargo]').each(function() {
                              if ($(this).val() == '') {
                                $(this).val('".$data->seu_cargo[$i]."');
                                  return false;
                              }
                            });"
                                
                                ."$('input[class=classe_empresa_em_que_trabalhou]').each(function() {
                                   if ($(this).val() == '') {
                                     $(this).val('".$data->empresa_em_que_trabalhou[$i]."');
                                       return false;
                                   }
                                  });"
                                     
                                     ."$('input[class=classe_sua_exp_e_seus_projetos]').each(function() {
                                       if ($(this).val() == '') {
                                         $(this).val('".$data->sua_exp_e_seus_projetos[$i]."');
                                           return false;
                                       }
                                     });"
                                         
                                         ."$('input[class=classe_dataComeco_expprof]').each(function() {
                                           if ($(this).val() == '') {
                                             $(this).val('".($data->dataComeco_expprof[$i])."');
                                               return false;
                                            }
                                          });" 
                                          
                                             ."$('input[class=classe_dataFim_expprof]').each(function() {
                                               if ($(this).val() == '') {
                                                 $(this).val('".($data->dataFim_expprof[$i])."');
                                                   return false;
                                               }
                                             });
                      }); ";
                $s->create($msg);
              }
          }
          
          
          //puxa a CURSOS COMPLEMENTARES da sessao e seta novamente no formulario html
          $n = 0;
          if (isset($data->nome_do_curso[$n]) && $data->nome_do_curso[$n] != ''){
            while (isset($data->nome_do_curso[$n])) { //contador de quantos valores serão carregados da sessão para os campos clones.
                $n++;
                
            }
              
              $response = new stdClass; 
              $response->{'id_cursos_complem[]'} = $data->id_cursos_complem[0];
              $response->{'nome_do_curso[]'} = $data->nome_do_curso[0];
              $response->{'empresa_de_ensino[]'} = $data->empresa_de_ensino[0];
              $response->{'carga_horaria[]'} = $data->carga_horaria[0];
              $response->{'dataComeco_qualif[]'} = ($data->dataComeco_qualif[0]);
              $response->{'dataFim_qualif[]'} = ($data->dataFim_qualif[0]);
              
              $this->form->sendData('form_curriculo', $response);
              
              for ($i = 1; $i < $n; $i++) {
                    
                 $this->clicar_no_botao_de_clonagem('tbutton_btn_clone_qualif');
                 
                 $s = new TScript();
                 
                 $msg = "$(document).ready( function() {
                           
                           $('input[class=classe_id_cursos_complem]').each(function() {
                              if ($(this).val() == '') {"
                                ."$(this).val('".$data->id_cursos_complem[$i]."');"
                                ."return false;
                              }
                           });

                           $('input[class=classe_nome_do_curso]').each(function() {
                              if ($(this).val() == '') {
                                $(this).val('".$data->nome_do_curso[$i]."');
                                  return false;
                              }
                            });"
                                
                                ."$('input[class=classe_empresa_de_ensino]').each(function() {
                                   if ($(this).val() == '') {
                                     $(this).val('".$data->empresa_de_ensino[$i]."');
                                       return false;
                                   }
                                  });"
                                     
                                     ."$('input[class=classe_carga_horaria]').each(function() {
                                       if ($(this).val() == '') {
                                         $(this).val('".$data->carga_horaria[$i]."');
                                           return false;
                                       }
                                     });"
                                         
                                         ."$('input[class=classe_dataComeco_qualif]').each(function() {
                                           if ($(this).val() == '') {
                                             $(this).val('".($data->dataComeco_qualif[$i])."');
                                               return false;
                                            }
                                          });" 
                                          
                                             ."$('input[class=classe_dataFim_qualif]').each(function() {
                                               if ($(this).val() == '') {
                                                 $(this).val('".($data->dataFim_qualif[$i])."');
                                                   return false;
                                               }
                                             });
                      }); ";
                $s->create($msg);
              }
          }
          
          //puxa o IDIOMA da sessao e seta novamente no formulario html
          $n = 0;
          if (isset($data->idioma[$n]) && $data->idioma[$n] != ''){
              while (isset($data->idioma[$n])) { //contador de quantos valores serão carregados da sessão para os campos clones.
                $n++;
              }
              
              //instanciando a linha inicial com o primeiro valor do idioma na sessão
              $response = new stdClass;
              $response->{'id_nivelIdioma[]'} = $data->id_nivelIdioma[0];
               
              $response->{'idioma[]'} = $data->idioma[0];
              $response->{'nivel[]'} = $data->nivel[0];
              $this->form->sendData('form_curriculo', $response);
              
              //for que clica no botao clone e faz um script jquery coloca os valores em seus respectivos campos.
              for ($i = 0; $i < $n-1; $i++) {   
                 $this->clicar_no_botao_de_clonagem('tbutton_btn_clone_idiomas');
                 
                 $s = new TScript();
                 
                 $msg = "$(document).ready( function() {
                           $('select[class=tcombo_idiomas]').each(function() {
                              if ($(this).val() == '') {"
                                ."$(this).val('".$data->idioma[$i+1]."');"
                                ."$('select[class=tcombo_nivel]').each(function() {
                                   if ($(this).val() == '') {"
                                     ."$(this).val('".$data->nivel[$i+1]."');
                                   return false;"
                                   ."}
                                });".
                                
                                "$('input[class=classe_nivelidioma]').each(function() {
                                      if ($(this).val() == '') {
                                        $(this).val('".$data->id_nivelIdioma[$i+1]."');
                                          return false;
                                      }
                                });"
                                ."return false;
                              }
                           });  
                        })";
                $s->create($msg);
                 
              }
              
          }
          
          
          
          
      }
      
      public function onClear_idiomas() {
        
        $this->btn_add_nova_idioma = new TButton('clone');
        $this->btn_add_nova_idioma->setName('btn_clone_idiomas');
        $this->btn_add_nova_idioma->setImage('fa:plus-circle green');
        $this->btn_add_nova_idioma->addFunction('ttable_clone_previous_row(this)');//Duplicar a linha anterior
        $this->btn_add_nova_idioma->setLabel('Adicionar IDIOMA');
        
        $alinhando_botao_idioma = $this->table_idioma->addRowSet( $this->btn_add_nova_idioma );    
      }
      public function clicar_no_botao_de_clonagem($id_botao_sem_cerquilha) {
         $s = new TScript;
         
         $msg = "$(document).ready(function() {
                     $('#".$id_botao_sem_cerquilha."').click();
                 })";
                                     
         $s->create($msg);
       }
       
       public static function onComplete($param){
          //new TMessage('info', 'caminho=>'.$param['photo_path']);
         
          TScript::create("$('#photo_frame').html('')");
          TScript::create("$('#photo_frame').append(\"<img style='width:49px; height:59px;' src='tmp/{$param['photo_path']}'>\");");
          
       }
       
       
      
       
             
      public static function onChangeFormation($param) {
        
      }
      
      public function onBackForm($param){
        try
        {
            //$this->form->validate();
            $data = $this->form->getData();
            
            // store data in the session
            TSession::setValue('form_step2_curriculo', $data);
            
            // Load another page
            AdiantiCoreApplication::loadPage('CandidatoForm', 'onLoadFromSession');
            
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
      }
      
    public function onNextForm($param){
        
        try
        {
            $data = $this->form->getData();
            $this->form->setData($data);    //mantem os dados após msg de validação
            
            
            TSession::setValue('form_step2_curriculo', $data);
            
            //new TMessage('info', 'chegou no onnextform');
            if (TSession::getValue('form_step3_usuario')) {
              AdiantiCoreApplication::gotoPage('UsuarioForm', 'onLoadFromSession');
            }
            else{
              if (TSession::getValue('fluxo')) {
                $p['key'] = intval($param['id_candidato']);
                AdiantiCoreApplication::gotoPage('UsuarioForm', 'onEdit', $p);
              }
              else {
                AdiantiCoreApplication::gotoPage('UsuarioForm');
              }
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            
        }
    }

          
      
}
