<?php  
class Controleur {
	protected $modele;

	function __construct($modele) {
		$this -> modele = new $modele();
	}
}
?>