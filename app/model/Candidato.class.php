<?php
/**
 * Candidato Active Record
 * @author  <your-name-here>
 */
class Candidato extends TRecord
{
    const TABLENAME = 'candidato';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    private $cidade;
    private $curriculo;
    private $laudos;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nomePai');
        parent::addAttribute('nomeMae');
        parent::addAttribute('rg');
        parent::addAttribute('cpf');
        parent::addAttribute('dataNascimento');
        parent::addAttribute('sexo');
        parent::addAttribute('estadoCivil');
        parent::addAttribute('endereco');
        parent::addAttribute('cep');
        parent::addAttribute('numeroCasa');
        parent::addAttribute('complemento');
        parent::addAttribute('telefone');
        parent::addAttribute('fone');
        parent::addAttribute('possuiDeficiencia');
        parent::addAttribute('tipoDeficiencia');
        parent::addAttribute('avaliacao');
        parent::addAttribute('nome');
        parent::addAttribute('email');
        parent::addAttribute('senha');
        parent::addAttribute('cidade_id');
        parent::addAttribute('curriculo_id');
        parent::addAttribute('link_google');
        parent::addAttribute('link_facebook');
        parent::addAttribute('link_instagram');
        parent::addAttribute('link_linkedin');
        parent::addAttribute('reputacao');
        parent::addAttribute('foto');
    }

    
    /**
     * Method set_cidade
     * Sample of usage: $candidato->cidade = $object;
     * @param $object Instance of Cidade
     */
    public function set_cidade(Cidade $object)
    {
        $this->cidade = $object;
        $this->cidade_id = $object->id;
    }
    
    /**
     * Method get_cidade
     * Sample of usage: $candidato->cidade->attribute;
     * @returns Cidade instance
     */
    public function get_cidade()
    {
        // loads the associated object
        if (empty($this->cidade))
            $this->cidade = new Cidade($this->cidade_id);
    
        // returns the associated object
        return $this->cidade;
    }
    
    
    /**
     * Method set_curriculo
     * Sample of usage: $candidato->curriculo = $object;
     * @param $object Instance of Curriculo
     */
    public function set_curriculo(Curriculo $object)
    {
        $this->curriculo = $object;
        $this->curriculo_id = $object->id;
    }
    
    /**
     * Method get_curriculo
     * Sample of usage: $candidato->curriculo->attribute;
     * @returns Curriculo instance
     */
    public function get_curriculo()
    {
        // loads the associated object
        if (empty($this->curriculo))
            $this->curriculo = new Curriculo($this->curriculo_id);
    
        // returns the associated object
        return $this->curriculo;
    }
    
    
    /**
     * Method addLaudo
     * Add a Laudo to the Candidato
     * @param $object Instance of Laudo
     */
    public function addLaudo(Laudo $object)
    {
        $this->laudos[] = $object;
    }
    
    /**
     * Method getLaudos
     * Return the Candidato' Laudo's
     * @return Collection of Laudo
     */
    public function getLaudos()
    {
        return $this->laudos;
    }

    /**
     * Reset aggregates
     */
    public function clearParts()
    {
        $this->laudos = array();
    }

    /**
     * Load the object and its aggregates
     * @param $id object ID
     */
    public function load($id)
    {
        $this->laudos = parent::loadComposite('Laudo', 'candidato_id', $id);
    
        // load the object itself
        return parent::load($id);
    }

    /**
     * Store the object and its aggregates
     */
    public function store()
    {
        // store the object itself
        parent::store();
    
        parent::saveComposite('Laudo', 'candidato_id', $this->id, $this->laudos);
    }

    /**
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        $id = isset($id) ? $id : $this->id;
        parent::deleteComposite('Laudo', 'candidato_id', $id);
    
        // delete the object itself
        parent::delete($id);
    }


}
