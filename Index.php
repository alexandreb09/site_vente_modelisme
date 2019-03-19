<html>
	<head>
		<title>Vente modélisme</title>

		<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0" />
		<link href="Projet.css" media="all" type="text/css" rel="stylesheet">
		
		<link rel="shortcut icon" type="image/png" href="img/favicon.ico">

		<?php require 'commande.php'; ?>
	</head>
	
	<body>
		<div class="titre"> 
			<img id="logo" src=".\img\logo_500px.gif" alt="Logo du site">
			<p id="description_titre" class="ma_police"> Le leader du modélisme en ligne </p>
		</div>

		<div id="authentification"> 
			Identifiant <br/>
			<input type="text" name="nom_utiliasteur"> <br/>
			Mot de passe <br/>
			<input type="text" name="mot_de_passe"> <br/>

			<button class="bouton" type="button">Se connecter</button>
			<button class="bouton" type="button">Créer un compte </button>
		</div>

		<div id="contenu">			

			<?php 
				if (isset($_GET['famille'])){
					afficheArticles($_GET['famille']);
				}
				else{
					afficheFamilles();
				}
			?>

		</div>


		<div id="panier"> 
			<img id="logo_panier" src="img/panier.gif" alt="logo panier">
			Panier 
			<hr>
			Mes articles
			<p id="panier_contenu">
				<?php 
					MAJPanier();					 
				?>
			</p>
			
			<form method="post">
				<input class="bouton" type="submit" name="viderPanier" value="vider panier" />
				<input class="bouton" type="button" value="commander">
			</form>
			


		</div>
		<div id="pied_de_page"> 
			TOPModelisme.com est enregistré au R.C.S sous le numéro 1234567890 <br/>
			13 avenue du Pré la Reine - 75007 Paris 
		</div>


		<script>
			function effacerDivPanier(){
				document.getElementById("panier_contenu").innerHTML = "";
			}
		</script>

	</body>
</html>