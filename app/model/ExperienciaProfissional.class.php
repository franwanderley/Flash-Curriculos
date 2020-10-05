<?php
/**
 * ExperienciaProfissional Active Record
 * @author  <your-name-here>
 */
class ExperienciaProfissional extends TRecord
{
    const TABLENAME = 'experiencia_profissional';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cargo');
        parent::addAttribute('empresa');
        parent::addAttribute('detalhes');
        parent::addAttribute('dataComeco');
        parent::addAttribute('dataFim');
        parent::addAttribute('curriculo_id');
    }


}
