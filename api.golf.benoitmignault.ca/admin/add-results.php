<?php

// Ce fichier sera utilisé pour faire plusieurs opérations reliées à l'ajout de résultats d'un joueur à un événement
// On va faire les mêmes genre de vérification que dans le front-end pour s'assurer que les données reçues sont valides 
// avant de faire le insert dans la base de données.

// Étape 0 - Je vais devoir une MAJ (un snapshot) de la table players avant de faire le insert du résultat du joueur dans la table round_results, 
// pour aller mettre à jour la previous_position avec la requête SQL qui me permet d'afficher le classement général actuel
// La vérification sera faite avec la valeur du champ «is_open» de la table events. Si la valeur est à 1, on fait la MAJ des positions actuelles 
// vers le champ previous_position de la table players, sinon on ne fait pas la MAJ des positions vers le champ previous_position de la table players

// Étape 1 - Insérer le résultat du joueur dans la table round_results

// Étape 2 - De ce même joueur, on doit vérifier si on doit faire un recalcul de son handicap en fonction du nombre de rondes jouées 
// et faire un update de son handicap dans la table players

// Étape 3 - On doit recalculer la moyenne des scores bruts du joueur en question, après chaque ajout de résultat

// Étape 4 - Une fois que tous les joueurs du même événement ont leurs résultats ajoutés :
// Étape 4.1 - On doit faire un recalcul des current_position dans la table «player_event_history»
// Étape 4.2 - On doit fermer l'évenement pour permettre au système de passer à l'événement suivant

// Le classement général se recalcul tout seul à partir du fichier standing.php