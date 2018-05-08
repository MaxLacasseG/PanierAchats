<?php 
session_start();

if(isset($_GET["route"]) && $_GET["route"] != "") {
  $route = $_GET["route"];
  require_once('config/config.php');
  $routeur = new Routeur($route); 
  header("Content-Type: application/json");
  echo json_encode($routeur -> invoquerRoute());
}

class Routeur {
	private $route = "";

	function __construct($r) {
		$this -> route = $r;
		spl_autoload_register(array($this,"chargementClasses"));
	}

	/**
	 * Permet le chargement automatique des fichiers contenant les définitions des classes utilisées
	 * @param  string $nomClasse Nom de la classe
	 * @return void
	 */
  private function chargementClasses($nomClasse) {
    $nomFichier = strtolower(preg_replace("/(.)([A-Z])/", "$1_$2", $nomClasse));
    if(file_exists("modeles/$nomFichier.class.php")) {
      require_once("modeles/$nomFichier.class.php");
    }
    else if(file_exists("lib/$nomFichier.lib.php")) {
      require_once("lib/$nomFichier.lib.php");
    }
    else if(file_exists("controleurs/$nomFichier.class.php")) {
      require_once("controleurs/$nomFichier.class.php");
    }
  }

  public function invoquerRoute() {
    $param = array();
    $routeTab = explode("/", $this -> route);
    $module = array_shift($routeTab);
    if(count($routeTab) > 0) {
  	 $action = array_shift($routeTab);
  	 $param = $routeTab;
    }
    $nomControleur = ucfirst($module)."Controleur";
  	$nomModele = ucfirst($module)."Modele";
    if(class_exists($nomControleur) && class_exists($nomModele)) {
  		$controleur = new $nomControleur($nomModele);
  		if(isset($action) && method_exists($controleur, $action)) {
  			return $controleur -> $action($param);
  		}
      else {
        return false;
      }
  	}
  }
}
?>