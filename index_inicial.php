<?php
require_once 'init.php';
$theme = 'theme1';
new TSession;

ob_start();
$menu = TMenuBar::newFromXML('menu.xml');
$menu->show();

$menu_string = ob_get_clean();

if (isset($_REQUEST['class']) && $_REQUEST['class'] == 'LoginForm')
   $content  = file_get_contents("app/templates/{$theme}/login.html");
else
   $content  = file_get_contents("app/templates/{$theme}/layout.html");

$content  = str_replace('{LIBRARIES}', file_get_contents("app/templates/{$theme}/libraries.html"), $content);
$content  = str_replace('{class}', isset($_REQUEST['class']) ? $_REQUEST['class'] : '', $content);
$content  = str_replace('{template}', $theme, $content);
$content  = str_replace('{MENU}', $menu_string, $content);
$css      = TPage::getLoadedCSS();
$js       = TPage::getLoadedJS();
$content  = str_replace('{HEAD}', $css.$js, $content);


if (isset($_REQUEST['class']) && TSession::getValue('logged')){
    
    $user = TSession::getValue('username');
    $sair = "<a href='index.php?class=PerfilForm&method=onLoad'>".$user."</a>";
    $content  = str_replace('{opcao1}', $user, $content);
    
    $sair = "<a href='index.php?class=PaginaPrincipalForm&method=onLogout'>SAIR</a>";
    $content  = str_replace('{opcao2}', $sair, $content);
    
    
    echo $content;
    $method = isset($_REQUEST['method']) ? $_REQUEST['method'] : NULL;
    AdiantiCoreApplication::loadPage($_REQUEST['class'], $method, $_REQUEST);
    //logado
    echo "logado";
}
else {
    $login = "<a href='index.php?class=LoginForm'>LOGIN</a>";
    $content  = str_replace('{opcao1}', $login, $content);
    
    $cad = "<a href='index.php?class=CandidatoForm'>CADASTRE-SE</a>";
    $content  = str_replace('{opcao2}', $cad, $content);
    
    
    echo $content;
    if (isset($_REQUEST['class'])) {
      $method = isset($_REQUEST['method']) ? $_REQUEST['method'] : NULL;
      AdiantiCoreApplication::loadPage($_REQUEST['class'], $method, $_REQUEST);
      //nao logado e com classe requisitada 
      echo "nao logado e com classe requisitada";
    }else {
      AdiantiCoreApplication::loadPage('PaginaPrincipalForm', NULL, NULL);
    }    
    
}

