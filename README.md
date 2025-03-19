# description
tool helping in vm-manager.org usage


# status
PoC


# requirements
- webserver with .htaccess support (like Apache)
- PHP 8.4


# depencendies
- Fat Free Framework


# install
- git clone
- composer i
- npm i
- fill in config file


# TODO
- store final data into a in-memory DB (sqlite, mariadb ?) each execution


changement coach :
- filtrer les coaches avec des caractéristiques supérieures
- trier par prix
- mettre des filtres pour toutes les colonnes

équipe -> nom du joueur
	-> attributs offensif
	-> attributs défensifs
en partant d'un joueur, trouver dans "transferts" un qui soit meilleur (uniquement sur les caractéristiques propre à son poste, cf. tableau du manuel)
la taille fait un bonus / malus sur les caractéristiques attaques défense (cf. tableau)



coach change : manque les ID
VM : mettre en cache les requêtes brutes por pouvoir modifier le code de transformation des données