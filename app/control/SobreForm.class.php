<?php
class SobreForm extends TPage
{
    public function __construct()
    {
        parent::__construct();
        
        $msg =  "
          A Flash Currículos é uma empresa criada com o intuito de estreitar os laços entre: empregado e o empregador, facilitando a contratação de funcionários como também facilitando a procura de vagas pelo candidato.

A empresa surgiu da necessidade do fundador e diretor principal - Doutor Freud, psicólogo clínico - em querer melhorar sua jornada de trabalho entre analisar currículos e encaminhar os participantes para uma entrevista de emprego, sem muita demora na seleção inicial. Isso tomava bastante tempo, visto que ele deveria conferir um por um dos currículos à ele entregues e ele não conseguia um resultado satisfatório por dia.

Com a ideia do sistema, tanto houve melhora de efetividade e rapidez no trabalho, como também foi possível agregar e contribuir para uma melhor interação entre empresa e pessoas à procura de emprego, que agora podem cadastrar vagas de emprego, visualizar as ofertas de emprego, se candidatar à vaga, tudo com praticidade e no conforto de casa, sem contar os impactos que beneficiam o meio ambiente, por não desperdiçar papéis na entrega de currículos físicos.

Enfim, a Flash Currículos trouxe as melhores das intenções, e, de uma simples ideia foi possível fazer um sistema cheio de funcionalidades. Sintam-se bem-vindo à nossa plataforma. Bons negócios!
        ";
        
        $panel = new TPanelGroup('FLASH CURRÍCULOS');
        $panel->add($msg);
        
        
        parent::add($panel);
    }
}
