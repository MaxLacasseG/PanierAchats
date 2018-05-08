<?php  
class Sql {
	/*
		Classe qui représente une connexion à la BD associée au projet
	*/
	// Propriétés
	protected $connexion;
	protected $resultat;

	// Constructeur
	function __construct() {
		$this -> connexion = new MySQLi(HOTE, UTILISATEUR, MDP, BD);
		// Encodage UTF8
		$this -> connexion -> query('SET NAMES UTF8');
	}

	// Méthodes
	
	/**
	 * Retourne une valeur assainie selon les règles propre à MySQL
	 * @param  mixed $valeur Chaîne ou nombre à assainir
	 * @return mixed La chaîne ou le nombre assaini
	 */
	public function assainir($valeur) {
		if(is_numeric($valeur)) {
			return (float)$valeur;
		}
		return $this -> connexion -> real_escape_string($valeur);
	}

	/**
	 * Soumet une requête SQL
	 * @param  string $req Requête SQL à exécuter
	 * @return void
	 */
	private function soumettreRequete($req) {
		$this -> resultat = $this -> connexion -> query($req);
	}

	/**
	 * Soumet une requête SQL de type SELECT et retourne le résultat dans un tableau PHP
	 * @param  string $req Requête SQL de type SELECT
	 * @return void
	 */
	public function lire($req) {
		$this -> soumettreRequete($req);
		$res  = array();
		while($enreg = $this -> resultat -> fetch_object()) {
			$res[] = $enreg;
		}
		return $res;
	}

	public function inserer($req) {
		$this -> soumettreRequete($req);
		return $this -> connexion -> insert_id;
	}

	// Faire une requete de type "DELETE"
	public function supprimer($req) {
		$this -> soumettreRequete($req);
		return $this -> connexion -> affected_rows;
	}

	// Faire une requete de type "UPDATE"
	public function modifier($req) {
		$this -> soumettreRequete($req);
		return $this -> connexion -> affected_rows;
	}
}
?>