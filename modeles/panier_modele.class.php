<?php  
class PanierModele extends Sql {
	/**
	 * Assigner un panier d'achat à un utilisateur.
	 * @return Array Tableau contenant l'identifiant et le numéro du panier.
	 */
	function assignerPanier($panierNumero) {
		$panierNumero = $this -> assainir($panierNumero);
		$panier = array("id" => "", "numero" => "");
		$resultat = $this -> lire("SELECT * FROM panier WHERE numero='$panierNumero'");
		if(count($resultat) > 0) {
			$panier["id"] = $resultat[0] -> id;
			$panier["numero"] = $resultat[0] -> numero;
			// Mettre à jour la date de dernière modification du panier.
			$this -> modifier("UPDATE panier SET date_modif=CURDATE() WHERE id=".$resultat[0] -> id);
		}
		else {
			$panierNumero = uniqid("pw3panier", true);
			$panierId = $this -> inserer("INSERT INTO panier VALUES (0, '$panierNumero', CURDATE())");
			$panier["id"] = $panierId;
			$panier["numero"] = $panierNumero;
		}
		return $panier;
	}

	// Vos méthodes du modèle du panier d'achats ci-dessous...
	/**
	 * Fonction retournant la liste des éléments du panier
	 * @param  int 	$idPanier 	le id du panier
	 * @param  bool 	$distinct 	Indique si l'on cherche les éléments ou le nombre d'éléments
	 * @return array 	$articles  	Le tableau contenant les données à afficher
	 * @return false 			La requête n'a pas été effectuée
	 */
	public function lireArticlesPanier($idPanier, $distinct) {
		//La requête pour le nb d'articles distincts ou tous les produits
		if($distinct == true){
			$req = "SELECT COUNT(DISTINCT id) AS qte FROM panier_article WHERE panier_id = '$idPanier'";
		}else {
			$req = "SELECT pr.id, pr.nom, pr.description, pr.prix, pr.stock, pa.quantite FROM panier_article AS pa
				JOIN produit AS pr ON pa.produit_id = pr.id
				WHERE pa.panier_id = '$idPanier'";
		}
		
		$articles = $this -> lire($req);

		if(Count($articles) === 0){
			return false;
		}else{
			return $articles;
		}
	}

	/**
	 * Fonction contenant la requête SQL pour ajouter un article au panier
	 * @param  int 	$idArticle  L'identifiant de l'article
	 * @param  int 	$idPanier  	L'identifiant de l'utilisateur 
	 * @return true           	La requête s'est bien effectuée
	 * @return false 		   	La requête n'a pas été complétée
	 */
	public function ajouterArticle($idArticle, $idPanier) {
		//Protection contre les injections sql
		$idArticle = $this -> assainir($idArticle);
		$idPanier = $this -> assainir($idPanier);

		//La requête pour ajouter un article
		$req = "INSERT INTO panier_article (quantite, produit_id, panier_id) VALUES ( '1', '$idArticle', '$idPanier')";
		$res = $this -> inserer($req);

		if($res == 0){
			return false;
		}else{
			return true;
		}
	}

	/**
	 * Fonction contenant la requête SQL pour modifier un article au panier
	 * @param  int 	$qte 	   	La quantité à modifier
	 * @param  int 	$idArticle  L'identifiant de l'article
	 * @param  int 	$idPanier  	L'identifiant de l'utilisateur 
	 * @return true           	La requête s'est bien effectuée
	 * @return false 		   	La requête n'a pas été complétée
	 */
	public function modifierQteArticle($qte, $idArticle, $idPanier) {
		//Protection contre les injections sql
		$qte = $this -> assainir($qte);
		$idArticle = $this -> assainir($idArticle);
		
		//La requête pour modifier un article
		$req = "UPDATE panier_article SET quantite = '$qte' WHERE produit_id = '$idArticle' AND panier_id = '$idPanier'";
		$res = $this -> modifier($req);

		if($res == 0){
			return false;
		}else{
			return true;
		}
	}


	/**
	 * Fonction contenant la requête SQL pour supprimer un article au panier
	 * @param  int 	$idArticle  L'identifiant de l'article
	 * @param  int 	$idPanier  	L'identifiant de l'utilisateur 
	 * @return true           	La requête s'est bien effectuée
	 * @return false 		   	La requête n'a pas été complétée
	 */
	public function supprimerArticle($idPanier, $idArticle) {
		//Protection contre les injections sql		
		$idArticle = $this -> assainir($idArticle);

		//La requête pour supprimer un article
		$req = "DELETE FROM panier_article WHERE produit_id = '$idArticle' AND panier_id = '$idPanier'";
		$res = $this -> supprimer($req);

		if($res == 0){
			return false;
		}else{
			return true;
		}
	}
}
?>