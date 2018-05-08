(function() {
    // Événement DOMReady (la page est prête)
    $(function() {
        /* 
        	A) Code à exécuter au chargement de la page
        */

        // Obtenir et afficher la liste de tous les produits disponibles
        obtenirProduits();

        // Assigner un panier d'achat à l'utilisateur.
        $.ajax({
            url: "index.php?route=panier/assigner"
        }).done(function(panier) {
            //console.log(panier);
        });

        //========================================================
        // MÀJ le nb d'articles dans l'onglet du menu au démarrage
        afficherNbArticlesDistincts();

        //=================================================
        //GESTIONNAIRES D'ÉVÉNEMENTS DIRECTS
        //SECTION PRODUIT
        //=================================================

        // Clic sur l'article de menu "Produit"
        $("header span.menu-produits").on("click", function() {
            $this = $(this);
            // Activer/désactiver les liens du menu
            $this.siblings("span").removeClass("actif");
            $this.addClass("actif");
            // Cacher/Afficher les sections
            $("#panier").hide();
            $("#produits").show();
            // Obtenir et afficher les produits
            obtenirProduits();
        });


        //=================================================
        //GESTIONNAIRES D'ÉVÉNEMENTS DIRECTS
        //SECTION PANIER
        //=================================================
        //Clic sur l'onglet du menu "Panier"
        $(".menu-panier").on("click", function() {
            $this = $(this);
            // Activer/désactiver les liens du menu
            $this.siblings("span").removeClass("actif");
            $this.addClass("actif");
            // Cacher/Afficher les sections
            $("#produits").hide();
            $("#panier").show();

            afficherPanier();
        });


        //=================================================
        //GESTIONNAIRES D'ÉVÉNEMENTS INDIRECTS
        //SECTION PRODUIT
        //=================================================
        //Clic sur le bouton "ajouter" dans  la  section produit
        $("#liste-produits").on("click", ".ajouter", function() {
            $this = $(this);
            //Le id de l'article à ajouter
            $idArticle = $this.parent().attr("id");
            ajouterAuPanier($idArticle);
            //Fonctionnalité à ajouter: griser un élément déjà ajouté
        });


        //=================================================
        //GESTIONNAIRES D'ÉVÉNEMENTS INDIRECTS
        //SECTION PANIER
        //=================================================
        //Clic sur le bouton "modifier" dans  la  section panier
        $("#liste-articles").on("click", ".modifier", function() {
            $this = $(this);
            //La quantité indiquée dans le champ input
            $qte = $this.siblings(".quantite").find("input").val();
            //Le id de l'article
            $idArticle = $this.parent().attr("id");

            //Si la quantité = 0, on supprime le produit du panier
            //Sinon, on modifie la quantité de l'article
            if ($qte === 0) {
                supprimerArticle($idArticle);
            } else {
                modifierQteArticle($idArticle, $qte);
            }
        });

        //Clic sur le bouton "supprimer" dans la section panier
        $("#liste-articles").on("click", ".supprimer", function() {
            $this = $(this);
            //Le id de l'article
            $idArticle = $this.parent().attr("id");

            supprimerArticle($idArticle);
        });

    });

    //=================================================
    //FONCTIONS
    //SECTION PRODUIT
    //=================================================
    /**
     * Obtient la liste des produits
     * @return  {Array} Tableau contenant les produits sous forme d'objets
     *                  simples JavaScript
     */
    function obtenirProduits() {
        $.ajax({
            url: "index.php?route=produit/afficher"
        }).done(afficherProduit);
    }

    /**
     * Affiche la liste des produits
     * @param  {Array} produits Tableau contenant les produits sous forme d'objets
     *                          simples JavaScript
     */
    function afficherProduit(produits) {
        var $produitClone = null;
        // On commence par vider la liste des produits affichés
        // Faire attention de ne pas enlever le gabarit !!
        $("#liste-produits article:not('.gabarit')").remove();
        // Puis on crée et affiche dans le DOM chaque produit successivement.
        for (var i = 0; i < produits.length; i++) {
            $produitClone = $("#liste-produits article.gabarit").clone().removeClass("gabarit");
            $produitClone.attr("id", produits[i].id);
            $produitClone.find(".nom").text(produits[i].nom);
            $produitClone.find(".desc").text(produits[i].description);
            $produitClone.find(".prix").text(produits[i].prix);
            $("#liste-produits").append($produitClone);
        }
    }

    //=================================================
    //FONCTIONS
    //SECTION PANIER
    //=================================================
    /**
     * Fonction servant à aller chercher les éléments du panier d'achat
     * Lorsque terminé, appel de la fonction afficherArticles
     */
    function afficherPanier() {
        $.ajax({
            method: "POST",
            url: "index.php?route=panier/afficherPanier"
        }).done(afficherArticles);
    }

    /**
     * Fonction servant à insérer les données retournées par la fonction afficherPanier
     * La fonction màj le soustotal et le total
     * @param  {array} articles Un tableau contenant les articles du panier
     */
    function afficherArticles(articles) {
        //Si le panier ne contient aucun article, afficher le message qu'il est vide
        //Sinon, afficher le panier
        if (!articles) {
            $("#liste-articles").hide();
            $(".panier-vide").show();
            $(".total-panier").hide();
        } else {
            $(".panier-vide").hide();
            $("#liste-articles").show();
            $(".total-panier").show();

            //Le clone du gabarit pour chaque article
            var $articleClone = null;

            //On enlève le panier avant de le rafraîchir
            $("#liste-articles article:not('.gabarit')").remove();

            // Puis on crée et affiche dans le DOM chaque article successivement.
            var nbArticles = articles.length;
            var soustotal, total;

            for (var i = 0; i < nbArticles; i++) {
                $articleClone = $("#liste-articles article.gabarit").clone().removeClass("gabarit");
                $articleClone.attr("id", articles[i].id);
                $articleClone.find(".nom").text(articles[i].nom);
                $articleClone.find(".prix").text(articles[i].prix);
                $articleClone.find("[name = 'qte']").val(articles[i].quantite).attr("max", articles[i].stock);

                //On met à jour le sous-total pour chaque article
                soustotal = calculerSousTotal(articles[i].quantite, articles[i].prix);
                soustotal = soustotal.toFixed(2);
                $articleClone.find(".soustotal").text(soustotal);

                $("#liste-articles").append($articleClone);
            }
            //On met le total à jour
            total = calculerTotal();
            total = total.toFixed(2);
            $("#montant-total").text(total);
        }
        afficherNbArticlesDistincts();
    }

    /**
     * Fonction servant à ajouter un item au panier
     * Lorsque terminé, appel de la fonction afficherNbArticleDistincts
     * @param  {int}    $articleId      Le id de l'item à ajouter
     */
    function ajouterAuPanier($articleId) {
        $.ajax({
            url: "index.php?route=panier/ajouterArticle/" + $articleId
        }).done(afficherNbArticlesDistincts);
    }

    /**
     * Fonction servant à modifier la quantité d'un article d'un produit
     * Lorsque terminée, appel de la fonction afficherPanier pour màj l'affichage
     * @param  {int}    $articleId      Le id du produit
     * @param  {int}    $qte            La quantité 
     */
    function modifierQteArticle($articleId, $qte) {
        $.ajax({
            url: "index.php?route=panier/modifierQteArticle/" + $articleId + "/" + $qte,
        }).done(afficherPanier);
    }

    /**
     * Fonction servant à supprimer un article de la liste
     * Lorsque terminée, appel de la fonction afficherPanier pour màj l'affichage
     * @param  {int}    $articleId      Le id de l'article
     */
    function supprimerArticle($articleId) {
        $.ajax({
            url: "index.php?route=panier/supprimerArticle/" + $articleId
        }).done(afficherPanier);
    }

    /**
     * Fonction servant à afficher le nb d'articles dans le menu
     */
    function afficherNbArticlesDistincts() {
        $.ajax({
            url: "index.php?route=panier/maj_NbArticlesDistincts",
            method: "POST",
        }).done(majNbArticlesDistincts);
    }

    /**
     * Fonction servant à mettre à jour le menu
     * @param  {int}    nbArticles      La quantité 
     */
    function majNbArticlesDistincts(nbArticles) {
        $("#nombre-articles-dans-panier").text(nbArticles[0].qte);
    }

    /**
     * Fonction servant à calculer le sous-total
     * @param  {int} qte  Quantité
     * @param  {float}  prixUnitaire    Prix de chaque article
     * return {float}
     */
    function calculerSousTotal(qte, prixUnitaire) {
        return qte * prixUnitaire;
    }

    /**
     * Fonction servant à calculer le total
     * return {float}   total   Le total avant taxes et livraison
     */
    function calculerTotal() {
        var total = 0,
            tempSousTotal,
            $tabSousTotal = $("article:not('.gabarit') .soustotal");

        for (i = 0; i < $tabSousTotal.length; i++) {
            tempSousTotal = parseFloat($tabSousTotal[i].innerText);
            total += tempSousTotal;
        }
        return total.toFixed(2);
    }
}());