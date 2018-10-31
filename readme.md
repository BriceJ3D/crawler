#### Crawler 
## Par Brice Jaugin

L'application necessite pour l'instant d'être demarrée grace à la commande console "php bin/console server:run"

Une fois le serveur lancé, l'application est disponible via navigateur a l'addresse "http://localhost:8000" (ou 8001 selon ce qu'indique la console) 


Les differentes options de l'outil sont gerrées dans le fichier .env
les options a modifier sont :
MAX_CRAWL: nb d'url crawl au maximum
CONCURENCY: nb d'url crawl en parallèle
 
Ce fichier contient aussi les clés des API ainsi que l'adresse de l'API gandi. Lors du passage sur l'API SEO il suffira de remplacer les clés actuelles par les clés correspondantes. 
(Pour les test actuellement les clés GANDI sont les clés de DEV, mais les clés de PROD sont fournies et doivent être decommentées pour être utilisées.)

###Les differents onglets : 

##Accueil
Page de recherche principale, c'est ici que l'utilisateur peut crééer ses recherches. 

#Tags
Le bouton "Ajouter ville" permet de rajouter automatiquement les villes au tag entré.
Le bouton "Recuperer les url" permet de recuperer le top 50 des urls pour chaque ligne de tag dans la zone de tag.

#Recherche
Le bouton "bannir les urls connues" permet de supprimer de la recherche les url bannies
le bouton "recrawler les urls déjà crawl" permet de relancer les crawl sur des urls déjà crawl auparavant, (ce qui n'est pas automatique)
les urls a crawler sont disponible dans la zone de texte, qui peut etre modifiée en taille.
les tags sont presents pour renseigner la recherche par tag.
cliquer sur envoyer les url pour creer la recherche.
Attention, la recherche créée n'est pas lancée, l'utilisateur peut ainsi créer plusieurs recherches et les lancer plus tard.


##Domaines
La page qui recense les domaines disponibles trouvés.

La premiere zone de texte permet de copier la liste des sites dans le presse papier grace au bouton.

le tableau en dessous presente les resultats qui peuvent etre triés par colone:
*le nom de domaine (avec le bouton de copie dans le presse papier)
*les TrustFlow
*les TrustMetrics
*Les RefIP
*Le title de la page
*Les topical trustflow
*les tags
*les liens vers les sites SEO
*les liens vers les sites d'achat
*la date de crawl

le tableau est paginé et peut faire l'objet d'une recherche globale sur les resultats.

La page est dotée d'une seconde recherche, qui retournera les resultats superieurs aux valeurs entrées. si la valeur est laissée vide, elle est equivalente à 0.


##Recherches
La page des recherches qui recapitule toutes les recherches créées et les recherches à lancer.

Le tableau indique:
*la date de la recherche
*les tags de la recherche
*le pourcentage d'avancement de la recherche
*Le nombre d'url crawlé
*le nombre de domaines trouvés
La derniere colone présente le bouton de lancement de la recherche. en cliquant dessus, la recherche se lance en fond (et bloque pour l'instant le chargement des autres pages mais je ne vois pas comment regler ce soucis.)

Un clic sur la date de la recherche permet d'acceder au resultats et aux domaines trouvés sur cette recherche (description de la page de resultats equivalente à la page Domaines)


##Tags
La page qui recense les tags et qui permet d'acceder a tous les domaines correspondant a un tag en cliquand sur le tag (description de la page de resultats equivalente à la page Domaines)

##Url Bannies
La page qui recense les url a ne pas crawl lorsque la case est cochée a la création de la recherche. Pour ajouter une Url il suffit de l'entrer dans le champ texte, et de cliquer sur le bouton "Ajouter l'url"

##Villes
La page qui recense les ajoutées aux tags lors du clic sur le bouton correspondant. Pour ajouter une ville il suffit de l'entrer dans le champ texte, et de cliquer sur le bouton "Ajouter la ville"

