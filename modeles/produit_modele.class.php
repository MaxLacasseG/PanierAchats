<?php  
class ProduitModele extends Sql {
	// NE MODIFIEZ RIEN dans ce fichier !
	
	/**
	 * Obtenir la liste de tous les produits
	 * @return array|false  Tableau contenant les résultats sous forme d'objet PHP
	 *                      Ou la valeur booléenne false s'il n'y a pas de résultat
	 */
	function lireTout() {
		$resultat = $this -> lire("SELECT * FROM produit");
		if(count($resultat) > 0) {
			return $resultat;
		}
		else {
			return false;
		}
	}
}
?>