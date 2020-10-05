<?php
/**
 * Curriculo Active Record
 * @author  <your-name-here>
 */
class Curriculo extends TRecord
{
    const TABLENAME = 'curriculo';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    private $area_de_interesses;
    private $cursos_complementares;
    private $experiencia_profissionals;
    private $formacaos;
    private $nivel_idiomas;

    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('pretensaoSalarial');
    }

    
    /**
     * Method addareaDeInteresse
     * Add a areaDeInteresse to the Curriculo
     * @param $object Instance of areaDeInteresse
     */
    public function addAreaDeInteresse(areaDeInteresse $object)
    {
        $this->area_de_interesses[] = $object;
    }
    
    /**
     * Method getareaDeInteresses
     * Return the Curriculo' areaDeInteresse's
     * @return Collection of areaDeInteresse
     */
    public function getAreaDeInteresses()
    {
        return $this->area_de_interesses;
    }
    
    /**
     * Method addCursosComplementares
     * Add a CursosComplementares to the Curriculo
     * @param $object Instance of CursosComplementares
     */
    public function addCursosComplementares(CursosComplementares $object)
    {
        $this->cursos_complementares[] = $object;
    }
    
    /**
     * Method getCursosComplementaress
     * Return the Curriculo' CursosComplementares's
     * @return Collection of CursosComplementares
     */
    public function getCursosComplementares()
    {
        return $this->cursos_complementares;
    }
    
    /**
     * Method addExperienciaProfissional
     * Add a ExperienciaProfissional to the Curriculo
     * @param $object Instance of ExperienciaProfissional
     */
    public function addExperienciaProfissional(ExperienciaProfissional $object)
    {
        $this->experiencia_profissionals[] = $object;
    }
    
    /**
     * Method getExperienciaProfissionals
     * Return the Curriculo' ExperienciaProfissional's
     * @return Collection of ExperienciaProfissional
     */
    public function getExperienciaProfissionals()
    {
        return $this->experiencia_profissionals;
    }
    
    /**
     * Method addFormacao
     * Add a Formacao to the Curriculo
     * @param $object Instance of Formacao
     */
    public function addFormacao(Formacao $object)
    {
        $this->formacaos[] = $object;
    }
    
    /**
     * Method getFormacaos
     * Return the Curriculo' Formacao's
     * @return Collection of Formacao
     */
    public function getFormacaos()
    {
        return $this->formacaos;
    }
    
    /**
     * Method addNivel_Idioma
     * Add a Nivel_Idioma to the Curriculo
     * @param $object Instance of Nivel_Idioma
     */
    public function addNivel_Idioma(Nivel_Idioma $object)
    {
        $this->nivel_idiomas[] = $object;
    }
    
    /**
     * Method getNivel_Idiomas
     * Return the Curriculo' Nivel_Idioma's
     * @return Collection of Nivel_Idioma
     */
    public function getNivel_Idiomas()
    {
        return $this->nivel_idiomas;
    }

    /**
     * Reset aggregates
     */
    public function clearParts()
    {
        $this->area_de_interesses = array();
        $this->cursos_complementares = array();
        $this->experiencia_profissionals = array();
        $this->formacaos = array();
        $this->nivel_idiomas = array();
    }

    /**
     * Load the object and its aggregates
     * @param $id object ID
     */
    public function load($id)
    {
        $this->area_de_interesses = parent::loadAggregate('AreaDeInteresse','Area_e_curriculo', 'curriculo_id', 'areadeinteresses_id', $id);
        $this->cursos_complementares = parent::loadComposite('CursosComplementares', 'curriculo_id', $id);
        $this->experiencia_profissionals = parent::loadComposite('ExperienciaProfissional', 'curriculo_id', $id);
        $this->formacaos = parent::loadComposite('Formacao', 'curriculo_id', $id);
        $this->nivel_idiomas = parent::loadComposite('Nivel_Idioma', 'curriculo_id', $id);
    
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
    
    //    parent::saveAggregate('Area_e_curriculo', 'curriculo_id', 'areadeinteresses_id', $this->id, $this->area_de_interesses);
    //    parent::saveComposite('CursosComplementares', 'curriculo_id', $this->id, $this->cursos_complementares);
    //    parent::saveComposite('ExperienciaProfissional', 'curriculo_id', $this->id, $this->experiencia_profissionals);
    //    parent::saveComposite('Formacao', 'curriculo_id', $this->id, $this->formacaos);
    //    parent::saveComposite('Nivel_Idioma', 'curriculo_id', $this->id, $this->nivel_idiomas);
    }
    
    
    
    
    
    /**
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        $id = isset($id) ? $id : $this->id;
        parent::deleteComposite('Area_e_curriculo', 'curriculo_id', $id);
        parent::deleteComposite('CursosComplementares', 'curriculo_id', $id);
        parent::deleteComposite('ExperienciaProfissional', 'curriculo_id', $id);
        parent::deleteComposite('Formacao', 'curriculo_id', $id);
        parent::deleteComposite('Nivel_Idioma', 'curriculo_id', $id);
    
        // delete the object itself
        parent::delete($id);
    }


}
