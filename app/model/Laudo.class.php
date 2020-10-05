<?php
/**
 * Laudo Active Record
 * @author  <your-name-here>
 */
class Laudo extends TRecord
{
    const TABLENAME = 'laudo';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    private $vaga;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
        parent::addAttribute('vaga_id');
        parent::addAttribute('candidato_id');
        parent::addAttribute('selecionados');
    }

    
    /**
     * Method set_vaga
     * Sample of usage: $laudo->vaga = $object;
     * @param $object Instance of Vaga
     */
    public function set_vaga(Vaga $object)
    {
        $this->vaga = $object;
        $this->vaga_id = $object->id;
    }
    
    /**
     * Method get_vaga
     * Sample of usage: $laudo->vaga->attribute;
     * @returns Vaga instance
     */
    public function get_vaga()
    {
        // loads the associated object
        if (empty($this->vaga))
            $this->vaga = new Vaga($this->vaga_id);
    
        // returns the associated object
        return $this->vaga;
    }
    


}
