<?php
/**
 * Nivel_Idioma Active Record
 * @author  <your-name-here>
 */
class Nivel_Idioma extends TRecord
{
    const TABLENAME = 'nivel_idioma';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    private $idioma;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nivel');
        parent::addAttribute('idioma_id');
        parent::addAttribute('curriculo_id');
    }

    
    /**
     * Method set_idioma
     * Sample of usage: $nivel_idioma->idioma = $object;
     * @param $object Instance of Idioma
     */
    public function set_idioma(Idioma $object)
    {
        $this->idioma = $object;
        $this->idioma_id = $object->id;
    }
    
    /**
     * Method get_idioma
     * Sample of usage: $nivel_idioma->idioma->attribute;
     * @returns Idioma instance
     */
    public function get_idioma()
    {
        // loads the associated object
        if (empty($this->idioma))
            $this->idioma = new Idioma($this->idioma_id);
    
        // returns the associated object
        return $this->idioma;
    }
    


}
