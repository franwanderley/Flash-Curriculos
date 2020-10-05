<?php
/**
 * AreaDeInteresse Active Record
 * @author  <your-name-here>
 */
class AreaDeInteresse extends TRecord
{
    const TABLENAME = 'area_de_interesse';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
    }


}
