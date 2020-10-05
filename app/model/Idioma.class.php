<?php
/**
 * Idioma Active Record
 * @author  <your-name-here>
 */
class Idioma extends TRecord
{
    const TABLENAME = 'idioma';
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

    
    /**
     * Method getNivel_Idiomas
     */
    public function getNivel_Idiomas()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('idioma_id', '=', $this->id));
        return Nivel_Idioma::getObjects( $criteria );
    }
    


}
