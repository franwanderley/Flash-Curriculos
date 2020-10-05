<?php
/**
 * Cursos Active Record
 * @author  <your-name-here>
 */
class Cursos extends TRecord
{
    const TABLENAME = 'cursos';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
    }


}
