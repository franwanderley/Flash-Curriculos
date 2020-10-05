<?php
class PaginaPrincipalForm extends TPage
{

    private $html;
    private $v = '';
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();
        
        
        
        
        TPage::include_css('app/resources/styles.css');
        $this->html = new THtmlRenderer('app/resources/wellcome.html');

        try {
          TTransaction::open('con_ultimato');
          
          //$vagas = Vaga::where('status', '=', '1'); // 1 para abertas....0 para fechadas
          // query criteria
            $criteria = new TCriteria; 
            $criteria->add(new TFilter('status', '=', 'ABERTA')); 
             
            
            // load using repository
            $repository = new TRepository('Vaga'); 
            $vagas = $repository->load($criteria);
          
          
          foreach ($vagas as $vaga) {
              
              $panel = new TPanelGroup('<b>'.$vaga->titulo.'</b>');
              $panel->style = "width:600px; height:300px";
              $table_master = new TTable;
              $table_inter = new TTable;
              
              
              if ($vaga->foto){
                $f = new TImage($vaga->foto);
              }
              $f->style = "max-width:180px";
              
              
              $table_inter->addRowSet('<b>TÍTULO:</b>', $vaga->titulo? $vaga->titulo : ' ');
              $table_inter->addRowSet('<b>EMPRESA:</b>', $vaga->nomeEmpresa? $vaga->nomeEmpresa : ' ');
              $table_inter->addRowSet('<b>MÉDIA SALARIAL:</b>', $vaga->mediaSalarial ? $vaga->mediaSalarial : ' ');
              $table_inter->addRowSet('<b>JORNADA:</b>', $vaga->jornada ? $vaga->jornada : ' ');
              $table_inter->addRowSet('<b>DESCRIÇÃO:</b>', $vaga->descricao ? $vaga->descricao : ' ');
              
              $row1 = $table_master->addRow();
              $c1 = $row1->addCell($f);
              $c2 = $row1->addCell($table_inter);
              
              
              
              
              
              //$table->style = 'border-collapse:collapse';
              //$table->width = '100%';
              
             
              
              
              
              
              
              
              
              $panel->add($table_master);
              
              $replace_vagas[] = array('vagas' => $panel, 'id' => $vaga->id);
          }
          
          TTransaction::close();
        }
        catch (Exception $e){
           echo "deu erro " . $e->getMessage();
        }
        
        // replace the main section variables
        $this->html->enableSection('main', '');
        $this->html->enableSection('vagas', $replace_vagas, TRUE);
        
        $aki = TSession::getValue('logged');
        
        
        
        // add the template to the page
        parent::add($this->html);
    }

    
    public function onLogout() {
      TSession::freeSession();
      AdiantiCoreApplication::gotoPage('PaginaPrincipalForm', '');
      
   
    }
    
}


