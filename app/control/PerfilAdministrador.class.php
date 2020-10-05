<?php
/*
    Autor Francisco Wanderly
*/
class PerfilAdministrador extends TPage {
    
    private $funcionario;
    
    public function __construct(){
        parent::__construct();
        
        //Pega os dados da Sessão
        $this->dados = TSession::getValue('username');
        $tipo = TSession::getValue('tipo_usuario');
        if($tipo == 'ADMINISTRADOR'){
             $criteria = new TCriteria();
             $criteria->add(new TFilter('nome', '=', $this->dados));
             try{
                TTransaction::open('con_ultimato');
                $repository = new TRepository('Funcionario');
                $funcionarios = $repository->load($criteria);
                foreach($funcionarios as $c){
                    $this->funcionario = $c;
                    break;
                }
                
//===============================================Dados Pessoais====================================================================
                
                 //TITULO
                $title = new TLabel('Perfil');
                $title->setFontSize(24);
                $title->setFontFace('Arial');
                
                 $tablefoto = new TTable;
                 $tablefoto->height = '80%';
                 $tablefoto->style= "float :left";
                 $col_foto = $this->funcionario->foto;
                 $row = $tablefoto->addRow();
                 $image = new TImage($col_foto);
                 $image->style = 'max-width: 300px';
                 $row->addCell($image);
                 
                $button2 = new TButton('editar');
                $action = new TAction( array('FuncionarioEditar', 'onEdit') );
                $action->setParameters( ['id' => $this->funcionario->id, 'foto' => $this->funcionario->foto] );
                $button2->setAction($action, 'Editar');
                $button2->setImage('fa:edit black');
                $button2->width =  '100%';
                //Botões só funciona com formularios
                
                $tablebutton = new TTable;
                $tablebutton->style='width:100%;height:100%;';
                $tablebutton->addRowSet( $button2 ); 
                $form = new TForm('form-botao');
                $form->add($tablebutton);
                $form->setFields(array($button2));
                
                //Juntando foto e butãos
                $tablefoto->addRowSet($form);  
                
                $pg = new TPanelGroup('Dados Pessoais');
                $table =  new TTable;
                $table->addRowset('Nome ',$this->funcionario->nome ? $this->funcionario->nome : '');
                $table->addRow()->addCell('<br>');
                $table->addRowset('Email ',$this->funcionario->email ? $this->funcionario->email : '');
                $table->addRow()->addCell('<br>');
                $table->addRowset('Telefone ',$this->funcionario->telefone ? $this->funcionario->telefone : '');
                 $table->addRow()->addCell('<br>');
                $table->addRowset('Perfil ',$this->funcionario->tipo_usuario ? $this->funcionario->tipo_usuario : '');
                $table->width = '100%';
                $pg->add($table);
                
                //JUntando foto e dados pessoais             
                $fotoedados = new TTable;
                $fotoedados->width = '60%';
                $fotoedados->addRowSet(' ',$title);
                $fotoedados->addRowSet($tablefoto,$pg);
                parent::add($fotoedados);
             }catch(Exception $e){
                 new TMessage('error',$e->getMessage());
             
             }
        }
        else
            new TMessage('warning', 'É preciso estar logado como administrador!', new TAction( [$this, 'redirecionar'] ));
            
    }
    
    public function redirecionar(){
         AdiantiCoreApplication::gotoPage('PaginaPrincipalForm');
    }
}
