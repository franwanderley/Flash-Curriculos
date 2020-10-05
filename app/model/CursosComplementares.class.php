<?php
/**
 * CursosComplementares Active Record
 * @author  <your-name-here>
 */
class CursosComplementares extends TRecord
{
    const TABLENAME = 'cursos_complementares';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('decricao');
        parent::addAttribute('instituicao');
        parent::addAttribute('cargaHoraria');
        parent::addAttribute('dataComeco');
        parent::addAttribute('dataFim');
        parent::addAttribute('curriculo_id');
    }


}
