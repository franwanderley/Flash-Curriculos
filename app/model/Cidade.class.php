<?php
/**
 * Cidade Active Record
 * @author  <your-name-here>
 */
class Cidade extends TRecord
{
    const TABLENAME = 'cidade';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    private $estado;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome_cidade');
        parent::addAttribute('estado_id');
    }

    
    /**
     * Method set_estado
     * Sample of usage: $cidade->estado = $object;
     * @param $object Instance of Estado
     */
    public function set_estado(Estado $object)
    {
        $this->estado = $object;
        $this->estado_id = $object->id;
    }
    
    /**
     * Method get_estado
     * Sample of usage: $cidade->estado->attribute;
     * @returns Estado instance
     */
    public function get_estado()
    {
        // loads the associated object
        if (empty($this->estado))
            $this->estado = new Estado($this->estado_id);
    
        // returns the associated object
        return $this->estado;
    }
    

    
    /**
     * Method getCandidatos
     */
    public function getCandidatos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('cidade_id', '=', $this->id));
        return Candidato::getObjects( $criteria );
    }
    


}
