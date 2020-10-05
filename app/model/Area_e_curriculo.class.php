<?php
/**
 * CurriculoAreaDeInteresse Active Record
 * @author  <your-name-here>
 */
class Area_e_curriculo extends TRecord
{
    const TABLENAME = 'area_e_curriculo';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('areadeinteresses_id');
        parent::addAttribute('curriculo_id');
    }


}
