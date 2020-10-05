<?php
/**
 * Vaga Active Record
 * @author  <your-name-here>
 */
class Vaga extends TRecord
{
    const TABLENAME = 'vaga';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('titulo');
        parent::addAttribute('foto');
        parent::addAttribute('nomeEmpresa');
        parent::addAttribute('requisitosMinimos');
        parent::addAttribute('mediaSalarial');
        parent::addAttribute('jornada');
        parent::addAttribute('descricao');
        parent::addAttribute('beneficios');
        parent::addAttribute('status');
    }

    
    /**
     * Method getLaudos
     */
    public function getLaudos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('vaga_id', '=', $this->id));
        return Laudo::getObjects( $criteria );
    }
    


}
