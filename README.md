# description
tool helping in vm-manager.org usage


# status
PoC


# requirements
- webserver with .htaccess support (like Apache)
- PHP 8.4


# depencendies
- Fat Free Framework


# TODO
- get auth credential from an auth form (instead of static config)
- store data into a cache (or sqlite, mysql) ?



club -> staff technique => coaches (https://vm-manager.org/Ajax_handler.php?phpsite=view_body.php&action=Coaches)
-> changer (https://vm-manager.org/Ajax_handler.php?phpsite=view_body.php&action=CoachChange&coachId=389566&site=2)
filtrer les coaches avec des caractéristiques supérieures
trier par prix
mettre des filtres pour toutes les colonnes

équipe -> nom du joueur
	-> attributs offensif
	-> attributs défensifs
en partant d'un joueur, trouver dans "transferts" un qui soit meilleur (uniquement sur les caractéristiques propre à son poste, cf. tableau du manuel)
la taille fait un bonus / malus sur les caractéristiques attaques défense (cf. tableau)
