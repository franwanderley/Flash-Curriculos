<?php
class VagasDetalhesView extends TPage
{
    public function __construct($param)
    {
        parent::__construct($param);
        
        
        
        try {
           TTransaction::open('con_ultimato');
            $v = new Vaga($param['key']);
           // echo "status = ". $v->status;
           // echo "</br>";
            
            $foto = "<img src='".$v->foto."' width=120 height=120></img>";
            
            //botao dentro de um formulario estatico para carregar a TQuestion
            $form = new TQuickForm('form');
            $btn = $form->addQuickAction('CADASTRAR-ME', new TAction(array($this, 'onInputDialog')), 'fa:check-circle-o green');      
            $btn->class = 'btn btn-success btn-lg';
            
                        
                //tabela interna do campo 1
                $table_c1 = new TTable;
                $table_c1->width = '100%';
                //$table_c1->border = 2;
                $linha_c1 = $table_c1->addRow();
                    $c11 = $linha_c1->addCell($foto);
                    $c11->width = '15%';  
                    $c12 = $linha_c1->addCell("<b>EMPRESA : </b>" .$v->nomeEmpresa ."</br>".
                                              "<b>SALÁRIO : </b>" .$v->mediaSalarial ."</br>".
                                              "<b>CARGO   : </b>" .$v->titulo
                                             );             
                    $c12->width = '60%';
                    $c13 = $linha_c1->addCell($btn); 
                
                
                //tabela interna do campo 2
                $table_c2 = new TTable;
                $table_c2->width = '100%';
                //$table_c2->border = 2;
                $linha_c2 = $table_c2->addRow();
                    $c21 = $linha_c2->addCell("<b>DESCRIÇÃO : </b>" .$v->descricao);
                $linha_c22 = $table_c2->addRow();      
                    $c22 = $linha_c22->addCell("<b>EXIGÊNCIAS : </b>" .$v->requisitosMinimos);
                    
                //tabela interna do campo 3
                $table_c3 = new TTable;
                $table_c3->width = '100%';
                //$table_c3->border = 2;
                $linha_c3 = $table_c3->addRow();
                    $c31 = $linha_c3->addCell("<b>JORNADA : </b>" . $v->jornada. "HORA / MÊS");
                
        
          TTransaction::close();
        }
        catch(Exception $e) {
          echo "error = ". $e->getMessage();
        } 
        
        
        
        
        //tabela layout da pagina
        $table_layout = new TTable;
        $table_layout->width = '100%';
        
        
        $linha1 = $table_layout->addRow();
        $linha2 = $table_layout->addRow();
        
        $campo1 = $linha1->addcell($table_c1);
        
        $campo1->colspan = '2';
        $campo1->height = '100px';
        //$campo3 foi apagado para o colspan do campo1
        $campo2 = $linha2->addcell($table_c2);
        $campo4 = $linha2->addcell($table_c3);
        
        $campo2->width = '55%';
        $campo4->width = '45%';
        
        $campo1->style = "border-bottom:1px solid #555555;";
        $campo2->style = "border-right:1px solid #555555;";
        
        $table_layout->style = "style = 'margin:10px'";
        $table_layout->height = '350px';
        $table_layout->style = "border-spacing:10px;";
        
        
        
        
        
        $frame = new TFrame('painel');
        
        $frame->add($table_layout);
        
        parent::add($frame);
    }
    
    
    public function onLoad() {  
    }
    
    public static function onInputDialog($param) {
        $formd = new TQuickForm('input_form');
        $formd->style = 'padding:20px';
        
        $questao = "AINDA NÃO É CADASTRADO ? </br>
                    CLIQUE NO BOTÃO, FAÇA SEU CADASTRO </br>
                    E SEU CURRÍCULO AGORA MESMO: ";
        $formd->add($questao);
        $formd->addQuickAction('CADASTRE-SE AQUI', new TAction(array('VagasDetalhesView', 'onAction1')), 'fa:save green');
        //$form->addQuickAction('Confirm 2', new TAction(array($this, 'onConfirm2')), 'fa:check-circle-o blue');
       
       /*$tq = new TQuestion('', $action1);*/ 
       new TInputDialog('FLASH CURRÍCULO', $formd);       
    }
    
    public static function onAction1($param)
    {
        AdiantiCoreApplication::loadPage('CandidatoForm');
    }
}
