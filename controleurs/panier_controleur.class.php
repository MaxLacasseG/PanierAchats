<?php
class PanierControleur extends Controleur {

	/**
	 * Valider le panier d'achat de l'utilisateur si un numéro de panier est trouvé
	 * dans le témoin HTTP (cookie) correspondant ; sinon, créer un nouveau panier
	 * et inscrire son numéro dans un témoin, et sauvegarder l'identifiant du panier
	 * dans une variable de session.
	 * 
	 * @return Array Tableau associatif contenant l'info sur le panier d'achats 
	 *               de l'utilisateur
	 */
	public function assigner() {
		$panierNumero = isset($_COOKIE["panier_numero"])?$_COOKIE["panier_numero"]:"";
		$panier = $this -> modele -> assignerPanier($panierNumero);
		$_SESSION["panier_id"] = $panier["id"];
		setcookie("panier_numero", $panier["numero"], time() + 30*24*3600);
		return $panier;
	}

	/**
	 * Fonction appelant le modèle pour ajouter un article
	 * @param  array 	$param  	Contient l'identifiant de l'article
	 * @return bool   $etatAjout	Indique si la fonction s'est bien déroulée
	 */
	public function ajouterArticle($param) {
		$idArticle = $param[0];
		$idPanier = $_SESSION["panier_id"];

		$etatAjout = $this -> modele -> ajouterArticle($idArticle, $idPanier);

		return $etatAjout;
	}

	/**
	 * Fonction appelant le modèle pour afficher les article du panier
	 * @return 	mixed   $articles	   Soit un array contenant les éléments à afficher, soit false si la requête ne s'est pas effectuée
	 */
	public function afficherPanier() {
		$idPanier = $_SESSION["panier_id"];

		//Drapeau servant à indiquer qu'il faut afficher tous les éléments du panier
		$distinct = false;
		$articles = $this -> modele -> lireArticlesPanier($idPanier, $distinct);

		return $articles;
	}

	/**
	 * Fonction appelant le modèle pour modifier les article du panier
	 * @param  array 	$param  	Contient l'identifiant de l'article et la quantité
	 * @return bool   $etatMod	Indique si la fonction s'est bien déroulée
	 */
	public function modifierQteArticle($param) {
		$idArticle = $param[0];
		$qte = $param[1];
		$idPanier = $_SESSION["panier_id"];

		$etatMod = $this -> modele -> modifierQteArticle($qte, $idArticle, $idPanier);
		return $etatMod;
	}

	/**
	 * Fonction appelant le modèle pour supprimer les article du panier
	 * @param  array 	$param  	Contient l'identifiant de l'article
	 * @return bool   $etatSup	Indique si la fonction s'est bien déroulée
	 */
	public function supprimerArticle($param) {
		$idPanier = $_SESSION["panier_id"];
		$idArticle = $param[0];

		$etatSup = $this -> modele -> supprimerArticle($idPanier, $idArticle);
		return $etatSup;
	}

	/**
	 * Fonction appelant le modèle pour afficher le nombre d'articles distincts du panier
	 * @return 	mixed   $articlesDistincts    Soit un array contenant les éléments à afficher, soit false si la requête ne s'est pas effectuée
	 */
	public function maj_NbArticlesDistincts() {
		$idPanier = $_SESSION["panier_id"];

		//Drapeau servant à indiquer qu'il faut afficher les articles distincts du panier
		$distinct = true;
		$articlesDistincts = $this -> modele -> lireArticlesPanier($idPanier, $distinct);

		return $articlesDistincts;
	}
}
?>