<?php
/*
    Autor Francisco Wanderly
    faltar só baixar o curriculo
*/
class PerfilForm extends TPage{
    private $dados;
    private $candidato;
    private $curriculo;
    
    public function __construct(){
        parent::__construct();
        $pg;
        $pg1;
        $pg2;
        $pg3;
        $pg4;
        $pg5;
        $tablebutton;
        //Pega os dados da sessão
        $this->dados = TSession::getValue('username');
        
        //Verificando o tipo de Usuario
        if(TSession::getValue('tipo_usuario') == 'ESTAGIARIO'){
           AdiantiCoreApplication::gotoPage('PerfilFuncionario');
        }
        else if(TSession::getValue('tipo_usuario') == 'ADMINISTRADOR'){
           AdiantiCoreApplication::gotoPage('PerfilAdministrador');
        }
        else
        if($this->dados){            
            $criteria = new TCriteria();
            $criteria->add(new TFilter('nome', '=', $this->dados));
            try{
                TTransaction::open('con_ultimato');
                $repository = new TRepository('Candidato');
                $candidatos = $repository->load($criteria);
                foreach($candidatos as $c){
                    $this->candidato = $c;
                    break;
                }
           //TITULO
            $title = new TLabel('Perfil');
            $title->setFontSize(24);
            $title->setFontFace('Arial');
            $title->style = 'text-align : center;';
          
          // ===============================================FOTOS======================================================
             $tablefoto = new TTable;
             $tablefoto->width = '15%';
             $tablefoto->height = '100%';
             $tablefoto->style= "float :left";
             $col_foto = $this->candidato->foto;
             $row = $tablefoto->addRow();
             $image = new TImage($col_foto);
             $image->style = 'max-width: 300px';
             $row->addCell($image);
             
         // ===============================================Ações======================================================

                $button1 = new TButton('baixar');
                $button1->setAction(new TAction(array($this, 'onCurriculoToPdf')), 'Baixar');
                $button1->setImage('fa:download black');
                $button2 = new TButton('editar');
                $button2->setAction(new TAction(array($this, 'onEdit')), 'Editar');
                $button2->setImage('fa:edit black');
                $button3 = new TButton('remover');
                $button3->setAction(new TAction(array($this, 'onRemove')), 'Excluir');
                 $button3->setImage('fa:trash black');
                //Botões só funciona com formularios
                
                $tablebutton = new TTable;
                $tablebutton->style='width:100%;height:100%;';
                $tablebutton->addRowSet($button2, $button3, $button1 ); 
                $form = new TForm('form-botao');
                $form->add($tablebutton);
                $form->setFields(array($button1, $button2, $button3));
                
                //Juntando foto e butãos
                $tablefoto->addRowSet($form);
         // ===============================================DADOS PESSOAIS======================================================
                $cidade = new Cidade($this->candidato->cidade_id);
                $fone = empty($this->candidato->fone) ? 'Não possui' : $this->candidato->fone;
                
                $pg = new TPanelGroup('Dados Pessoais');
                $table =  new TTable;
                $table->addRowset('Nome ',$this->candidato->nome, 'Email', $this->candidato->email,'Cpf', $this->candidato->cpf);
                $table->addRow()->addCell('<br>');
                $table->addRowset('Nome do Pai',$this->candidato->nomePai, 'Nome da Mãe', $this->candidato->nomeMae,'Rg', $this->candidato->rg);
                $table->addRow()->addCell('<br>');
                $table->addRowset('Data de Nascimento ',$this->candidato->dataNascimento, 'Sexo', $this->candidato->sexo,'Cep', $this->candidato->cep);
                $table->addRow()->addCell('<br>');
                $table->addRowset('Endereço ',$this->candidato->endereco, 'Estado Civil', $this->candidato->estadoCivil, 'Telefone', $this->candidato->telefone);
                $table->addRow()->addCell('<br>');
                $table->addRowset('Cidade ', $cidade->nome_cidade, 'Estado  ', (new Estado($cidade->estado_id))->nome_estado,'2° Telefone', $fone);
                 $table->addRow()->addCell('<br>');
                //verifica se possui deficiencia
                $row = $table->addRow();
                $row->addCell('Deficiencia ');
                $col = !empty($this->candidato->tipoDeficiencia) ? $this->candidato->tipoDeficiencia : 'Não Possui';
                $row->addCell($col);
                //Verifica se o complemento é nulo
                $row->addCell('Complemento');
                $col = !empty($this->candidato->complemento) ? $this->candidato->complemento : 'Não Especificado';
                $row->addCell($col);
                $row->addCell('Numero da Casa');
                $row->addCell($this->candidato->numeroCasa);
                $table->width = '100%';
                
            
                $pg->add($table);
                //Juntando foto com dados pessoais
                $fotoedados = new TTable;
                $fotoedados->width = '100%';
                $t = $fotoedados->addRow();
                $tablefoto->style = 'text-align : center';
                $col = $t->addCell($title);
                $fotoedados->addRowSet($tablefoto,$pg);
//=====================================================CURRICULO=================================================================
                 $this->curriculo = $this->candidato->curriculo;
                 $pg1 = new TPanelGroup('Pretensão Salarial e Area de Interesse');
                 $table =  new TTable;
                 $row = $table->addRow();
                 $row->addMultiCell('Pretensão Salarial', $this->curriculo->pretensaoSalarial);
                 $row->addCell('Area de Interesse');
                 foreach($this->curriculo->getAreaDeInteresses() as $areaInt){
                     $row->addCell($areaInt->descricao);
                 }
                 $table->width = '100%';
                 $pg1->add($table);
                 
                 $pg2 = new TPanelGroup('Curso Complementar');
                 $table =  new TTable;
                 foreach($this->curriculo->getCursosComplementares() as $cc){
                     //var_dump($cc);
                     $row1 = $table->addRow();
                     $row1->addCell('Nome: ');
                     $row1->addCell($cc->decricao);
                     $row1->addCell('Instituiçao: ');
                     $row1->addCell($cc->instituicao);
                     $row1->addCell('Carga Horaria: ');
                     $row1->addCell($cc->cargaHoraria.'h');
                     $row1->addCell('Começou em: ');
                     $row1->addCell($cc->dataComeco);
                     $row1->addCell('Terminou em: ');
                     $row1->addCell($cc->dataFim);
                 }
                 
                 $table->width = '100%';
                 $pg2->add($table);
                 
                 if($this->curriculo->getExperienciaProfissionals()){
                     $pg3 = new TPanelGroup('Experiencia Profissional');
                     $table =  new TTable;
                     foreach($this->curriculo->getExperienciaProfissionals() as $ep){
                         $row1 = $table->addRow();
                         $row1->addCell('Cargo: ');
                         $row1->addCell($ep->cargo);
                         $row1->addCell('Empresa: ');
                         $row1->addCell($ep->empresa);
                         $row1->addCell('Começou em: ');
                         $row1->addCell($ep->dataComeco);
                         $row1->addCell('Terminou em: ');
                         $row1->addCell($ep->dataFim);
                         $row2 = $table->addRow();
                         $row2->addCell('Detalhe:');
                         $row2->addCell($ep->detalhes);
                        
                     }
                     
                     $table->width = '100%';
                     $pg3->add($table);
                 }
                 
                 if($this->curriculo->getFormacaos()){
                     $pg4 = new TPanelGroup('Formação Academica');
                     $table =  new TTable;
                     foreach($this->curriculo->getFormacaos() as $f){
                         //var_dump($f->cursos->nome);
                         $row1 = $table->addRow();
                         $row1->addCell('Curso: ');
                         $row1->addCell($f->cursos->nome);
                         $row1->addCell('Instituição: ');
                         $row1->addCell($f->instituicao);
                         $row1->addCell('Começou em: ');
                         $row1->addCell($f->dataComeco);
                         $row1->addCell('Terminou em: ');
                         $row1->addCell($f->dataFim);
                     }
                     
                     $table->width = '100%';
                     $pg4->add($table);
                 }
                 
                 if($this->curriculo->getNivel_Idiomas()){
                     $pg5 = new TPanelGroup('Idioma');
                     $table =  new TTable;
                     foreach($this->curriculo->getNivel_Idiomas() as $ni){
                         //var_dump($f->cursos->nome);
                         $row1 = $table->addRow();
                         $row1->addCell('Idioma: ');
                         $row1->addCell($ni->idioma->nome);
                         $row1->addCell('Nivel: ');
                         $row1->addCell($ni->nivel);
                         $row1 = $table->addRow();
                         $row1->addCell('<br>');
                     }
                     
                     $table->width = '100%';
                     $pg5->add($table);
                 }
                 
//=====================================================Laudos=================================================================
                 $laudos = $this->candidato->getLaudos();
                 $panel = new TPanelGroup('Laudos');
                 $table = new TTable;
                 $table->width = '80%';
                 
                 foreach($laudos as $l){
                     $vaga = new Vaga($l->vaga_id);
                     $table->addRowset('Nome ',$this->candidato->nome, 'Vaga ', $vaga->titulo, 'Descrição ', $l->descricao);
                     $table->addRowset('<br>');
                 }
                 $panel->add($table);
                 
                 TTransaction::close();
            }catch(Exception $e){
                new TMessage('error', $e->getMessage());
            } 
            $vbox = new TVBox;
            $vbox->style = 'width: 80%';
            //$vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__)); Não tá dando certo
            parent::add($fotoedados);
            $vbox->add($pg1);
            $vbox->add($pg5);
            parent::add($vbox);
            parent::add($pg2);
            parent::add($pg3);
            parent::add($pg4);
            parent::add($panel);
            
        }
        else
            new TMessage('error', 'È preciso estar logado!');   
    }
    
    public function onCurriculoToPdf(){
        
    }
    
    public function onEdit(){
        AdiantiCoreApplication::gotoPage('CandidatoForm');
    }
    
   public function onRemove($param){
        $action = new TAction( [$this, 'Delete'] );
        $action->setParameters($param);
        new TQuestion('Deseja excluir-se(Perderá todos os dados!)? ', $action);
    }
    
    public function Delete($param){
        try{
            TTransaction::open('con_ultimato');
            $this->candidato->delete();
            TTransaction::close();
            new TMessage('info', 'Apagado com Sucesso');
            AdiantiCoreApplication::gotoPage('CandidatoForm');
        }catch(Exception $e){
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
        
    }
}