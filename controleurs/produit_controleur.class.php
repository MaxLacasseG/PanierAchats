<?php
class ProduitControleur extends Controleur {
	// NE MODIFIEZ RIEN dans ce fichier !

	/**
	 * Retourne la liste de tous les produits disponibles.
	 * @return Array Tableau contenant des objets représentants les produits.
	 */
	public function afficher() {
		return $this -> modele -> lireTout();
	}
}
?>