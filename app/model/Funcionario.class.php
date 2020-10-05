<?php
/**
 * Funcionario Active Record
 * @author  <your-name-here>
 */
class Funcionario extends TRecord
{
    const TABLENAME = 'funcionario';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('email');
        parent::addAttribute('telefone');
        parent::addAttribute('senha');
        parent::addAttribute('foto');
        parent::addAttribute('tipo_usuario');
    }


}
