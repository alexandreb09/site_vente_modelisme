<?php

    function envoiRequete($requete_sql){
        $server = "localhost";                                                                                  // Nom server
        $login = "root";                                                                                        // Identifiant phpmyAdmin
        $mdp  = "";                                                                                             // Mot de passe phpMyAdmin
        $base_de_donnees= "vente_en_ligne";                                                                     // Nom base de données
        $db = mysqli_connect($server, $login, $mdp, $base_de_donnees) or die('Erreur : '.mysqli_error($db));    // Connexion à MySQL 
        $db->query('SET NAMES UTF8');                                                                           // Encodage
        $result = $db->query($requete_sql) or die('Erreur SQL !<br>'.$requete_sql.'<br>'.mysqli_error($db));    // Envoi la requête 
        mysqli_close($db);                                                                                      // Fermeture connexion
        return $result;
    }


    if(isset($_POST['viderPanier'])) {                                                                          // Si appui bouton suppression panier
        viderPanier();                                                                                          // Suppression panier
    }

    function MAJPanier(){                                                                                       // Fonction MAJ Panier
        if (isset($_GET['article']) && isset($_GET['famille']) && $_SERVER['REQUEST_METHOD'] == "GET"){         // Si article et famille renseigné en méthode get
            MAJ_DB_Panier();                                                                                    // MAJ de la base de données
        }
        MAJ_Affichage_Panier();                                                                                 // MAJ de l'affichage
    }

    function MAJ_DB_Panier(){                                                                                   // Fonction de MAj de BD
        $id_article = $_GET['article'];                                                                         // ID article passé dans l'URL
        $id_panier  = 1;                                                                                        // Attribut fixe


        $sql        = 'SELECT libelle, prix_ttc, id_tva FROM article WHERE id='.$id_article;                    // création requête SQL : article
        $result     = envoiRequete($sql);                                                                       // envoi la requête 
        $data       = mysqli_fetch_array($result);                                                              // résultat requête
        $prix_ttc   = $data["prix_ttc"];                                                                        // récupération prix_ttc
        $id_tva     = $data["id_tva"];                                                                          // récupération id_tva


        $sql_tva    = 'SELECT taux FROM tva WHERE id='.$id_tva;                                                 // création requete SQL : tva
        $result     = envoiRequete($sql_tva);                                                                   // envoi la requête 
        $data       = mysqli_fetch_array($result);                                                              // résultat requête
        $taux       = $data["taux"];                                                                            // récupération taux
        $prix_tva   = $prix_ttc*$taux/100;                                                                      // calcul prix_tva
        $prix_ht    = $prix_ttc - $prix_tva;                                                                    // calcul prix_ht


        // Recherche article déjà existant
        $sql_recherche_article = 'SELECT quantite FROM panier_article WHERE id_article='.$id_article;           // création requete SQL : quantité article dans panier
        $result = envoiRequete($sql_recherche_article);                                                         // envoi requête

        if ($result->num_rows == 0){                                                                            // Si non existant : insertion
            $quantite = 1;                                                                                      // initialisation quantité à 0
            $sql_ajout = 'INSERT INTO panier_article VALUES ('.$id_panier.','.$id_article.','                   // Insertion nouvelle article dans panier
                                        .$quantite.','.$prix_ht.','.$prix_tva.','.$prix_ttc.')';
        }
        else {                                                                                                  // Si article existant dans panier
            $data = mysqli_fetch_array($result);                                                                // resultat requête
            $quantite = $data["quantite"] + 1;                                                                  // Calculnouvelle quantité
            $sql_ajout = 'UPDATE panier_article SET quantite='.$quantite.' WHERE id_article='.$id_article;      // MAJ quantite
        }
        $result = envoiRequete($sql_ajout);                                                                     // MAJ Panier
    }

    function MAJ_Affichage_Panier(){
        $sql = 'SELECT quantite, panier_article.prix_ttc, id_article, detail                            
                FROM panier_article
                JOIN article ON panier_article.id_article = article.id';                                        // création requete SQL : données inter tables
        $result = envoiRequete($sql);                                                                           // envoi la requête 
        $prix_total = 0;                                                                                        // Initialisation prix total commande
        while($data = mysqli_fetch_array($result)) {                                                            // Pour chaue article du panier
            $prix_total_produit = $data["quantite"] * $data["prix_ttc"];                                        // Calcul prix total par produit
            $prix_total = $prix_total + $prix_total_produit;                                                    // MAJ prix total commande
            // Affichage
            echo '<div id="detail_panier">'.$data["detail"].
                    '</div>
                    <div id="prix_panier">'
                        .$data["quantite"].' x '.$data["prix_ttc"].' = '.$prix_total_produit.'€
                    </div>';
        }
        if ($result->num_rows > 0){                                                                             // Si le panier est non vide
            echo '<hr> <div id="prix_panier">Total : '.$prix_total.'€</div>';                                   // Affichage du prix total
        }
    }

    function viderPanier(){                                                                                     // Supprime le panier
        $sql = 'DELETE FROM panier_article';                                                                    // Requête SQL: suppression de toutes les lignes
        $result = envoiRequete($sql);                                                                           // envoi la requête 
        MAJ_Affichage_Panier();                                                                                 // MAJ Affichage
    }

    function afficheArticles($id_famille_select){
        $sql = 'SELECT libelle, image, prix_ttc, detail, id FROM article WHERE id_famille = '.$id_famille_select;   // Création requête SQL : articles par famille
        $result = envoiRequete($sql);                                                                               // Envoie requête 
        
        echo '<a class="bouton" type="button" href="index.php">Retour</a> </br>';                                   // Affichage bouton retour

        while($data = mysqli_fetch_array($result)) {                                                                // Pour chaque enregistrement
            // on affiche les informations de l'enregistrement en cours 
            echo '<table id="bloc_items"> 
                    <tr>
                        <td rowspan="4">
                            <img class="image_items" src="img_articles/'.$data['image'].'" alt='.$data['libelle'].'>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            '.$data['libelle'].
                        '</td>	
                    </tr>	
                    <tr>
                        <td colspan="2">
                            <font class="detail">'
                                .$data['detail'].
                            '</font>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <font id="prix">'
                                .$data['prix_ttc'].'€ 
                            </font>
                        </td>
                        <td>
                            <a href="index.php?article='.$data['id'].'&famille='.$_GET['famille'].'" 
                                id="btn_'.$data['id'].'" class="bouton" onClick="effacerDivPanier();">
                                Commander
                            </a>
                        </td>
                    </tr>
                </table>';
        }
    }

    function afficheFamilles(){
        $sql = 'SELECT libelle, image, id FROM famille ORDER BY ordre_affichage';                                   // Création requête SQL : familles
        $result = envoiRequete($sql);                                                                               // Envoie requête 

        while($data = mysqli_fetch_array($result)) {                                                                // Pour chaque famille
            // on affiche les informations de la famille en cours 
            echo '<a id="bloc_famille" href="index.php?famille='.$data['id'].'">
                    <img class="image_famille" src="img_familles/'.$data['image'].'" alt='.$data['libelle'].'>
                    '.$data['libelle']
                .'</a>';
        }
    }
?>