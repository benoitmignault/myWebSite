<?php
function traduction($champs){ 
    if ($champs["typeLangue"] === 'francais') { 
        $btn_new = "Ajouter le nouveau joueur";
        $newJoueur = "Nouveau joueur";
        $title = "Page de gestion du poker et login";
        $h1 = "Bienvenue à la page de gestion des utilisateurs et des statistiques du poker.";
        $h3 = "Formulaire pour ajouter les statistiques d'un joueur.";
        $option = "À sélectionner";
        $joueur = "Joueur : ";
        $resultat = "Résultat du classement : ";
        $gain = "Gain Net";
        $victoire = "Victoire";
        $fini2e = "Fini 2e";
        $autre = "Autre";
        $killer = "Prix killer";
        $citron = "Prix citron";
        $noId = "Id du tournoi";
        $btn_add = "Ajouter"; 
        $btn_erase = "Effacer"; 
        $btn_loginPoker = "Voir les statistique";
        $btn_login = "Retour à page de connexion";
        $btn_return = "Retour à l'accueuil";        
    } elseif ($champs["typeLangue"] === 'english') {
        $btn_new = "Add the new player";
        $newJoueur = "New player";
        $title = "Poker management page and login";
        $h1 = "Welcome to the User Management and Poker Statistics page.";
        $h3 = "Form to add the statistics of a player.";
        $option = "Select";
        $resultat = "Ranking result : ";
        $gain = "Profit";
        $victoire = "Victory";
        $killer = "Killer price";
        $citron = "Lemons price";
        $fini2e = "Runner-Up";
        $autre = "Other"; 
        $joueur = "Player : ";
        $noId = "Tournament Id";
        $btn_add = "Add"; 
        $btn_erase = "Erase"; 
        $btn_loginPoker = "View statistics";
        $btn_login = "Back to login page";
        $btn_return = "Back to Home";
    }
    $arrayMots = ['btn_new'=>$btn_new,'title'=>$title,'killer'=>$killer,'citron'=>$citron,'newJoueur'=>$newJoueur,'gain'=>$gain,'h1'=>$h1,'victoire'=>$victoire,'fini2e'=>$fini2e,'h3'=>$h3,'autre'=>$autre,'noId'=>$noId,'option'=>$option,'joueur'=>$joueur,'resultat'=>$resultat,'btn_add'=>$btn_add,'btn_erase'=>$btn_erase,'btn_loginPoker'=>$btn_loginPoker,'btn_login'=>$btn_login,'btn_return'=>$btn_return];
    return $arrayMots;
}

function initialisation(){
    $valid_Champ = ["invalid_Gain"=>false,"invalid_New"=>false,"invalid_Id"=>false,"invalid_Date"=>false,"tous_champs_Vide"=>false,"longueur_inval_Gain"=>false,"longueur_inval_Id"=>false,"longueur_inval_New"=>false,"longueur_inval_Date"=>false,"vide_Gain"=>false,"vide_Joueur"=>false,"vide_position"=>false,"vide_Date"=>false,"vide_Id"=>false,"vide_NewJoueur"=>false,"doublon_new_Joueur"=>false,"vide_Killer"=>false,"vide_Citron"=>false,"longueur_inval_Killer"=>false,"longueur_inval_Citron"=>false,"invalid_Killer"=>false,"invalid_Citron"=>false];
    return $valid_Champ;
}

function initialisation_Champs(){
    $champs =["typeLangue"=>$_SESSION['typeLangue'], "listeJoueur"=>"", "gain"=>"", "position"=>"","numTournoi"=>"","date"=>"","newJoueur"=>"","message"=>"","killer"=>"","citron"=>""];

    return $champs;
}

function remplissageChamps($champs){    
    if (isset($_POST['newJoueur'])){
        $champs["newJoueur"] = $_POST['newJoueur'];        
    } else {
        if (isset($_POST['listeJoueur'])){
            $champs["listeJoueur"] = $_POST['listeJoueur'];
        }
        if (isset($_POST['gain'])){
            $champs["gain"] = $_POST['gain'];
        }
        if (isset($_POST['position'])){
            $champs["position"] = $_POST['position'];
        }
        if (isset($_POST['numTournoi'])){
            $champs["numTournoi"] = $_POST['numTournoi'];
        }
        if (isset($_POST['date'])){
            $champs["date"] = $_POST['date'];
        }
        if (isset($_POST['killer'])){
            $champs["killer"] = $_POST['killer'];
        }
        if (isset($_POST['citron'])){
            $champs["citron"] = $_POST['citron'];
        }
    }
    return $champs;
}

function creationListe($connMYSQL, $arrayMots, $champ){
    $sql = "select joueur from benoitmignault_ca_mywebsite.joueur order by joueur";
    $result = $connMYSQL->query($sql);

    if ($_SERVER['REQUEST_METHOD'] === 'GET' || empty($champ['listeJoueur']) ){
        $listeJoueurs = "<option value='' selected>{$arrayMots['option']}</option>";
        foreach($result as $row){        
            $listeJoueurs .= "<option value=\"{$row['joueur']}\">{$row['joueur']}</option>"; 
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST'){
        $listeJoueurs = "<option value=''>{$arrayMots['option']}</option>";
        foreach($result as $row){
            if ($champ['listeJoueur'] === $row['joueur']){
                $listeJoueurs .= "<option value=\"{$row['joueur']}\" selected>{$row['joueur']}</option>"; 
            } else {
                $listeJoueurs .= "<option value=\"{$row['joueur']}\">{$row['joueur']}</option>"; 
            }
        }
    }

    return $listeJoueurs;
}

function validation($champs, $valid_Champ, $connMYSQL){
    if (isset($_POST['ajouter'])){
        if (empty($champs['listeJoueur']) || empty($champs['gain']) || empty($champs['citron']) || 
            empty($champs['killer']) || empty($champs['position']) || empty($champs['numTournoi']) || 
            empty($champs['date'])){
            // sous condition propre à chaque champ
            if (empty($champs['listeJoueur'])){
                $valid_Champ['vide_Joueur'] = true;
            }
            if (empty($champs['position'])){
                $valid_Champ['vide_position'] = true;
            }
            if (empty($champs['numTournoi'])){
                $valid_Champ['vide_Id'] = true;
            }
            if (empty($champs['date'])){
                $valid_Champ['vide_Date'] = true;
            }
            // Les champs killer, citron et gain peuvent être de zéro, docn je ne peux pas les évoluer individuellement...            
            if ($valid_Champ['vide_Joueur'] && $valid_Champ['vide_Killer'] && $valid_Champ['vide_Citron'] && 
                $valid_Champ['vide_Gain'] && $valid_Champ['vide_position'] && $valid_Champ['vide_Id'] && $valid_Champ['vide_Date']){
                $valid_Champ['tous_champs_Vide'] = true;
            }
        }
    } elseif (isset($_POST['ajouterNouveau'])){
        if (empty($champs['newJoueur'])){
            $valid_Champ['vide_NewJoueur'] = true;
            $valid_Champ['tous_champs_Vide'] = true;
        } else { 
            $sql = "select joueur from benoitmignault_ca_mywebsite.joueur order by joueur";
            $result = $connMYSQL->query($sql);
            foreach($result as $row){ 
                if ($row['joueur'] === $champs['newJoueur']){
                    $valid_Champ['doublon_new_Joueur'] = true;
                }
            }            
        }
    }

    $longueurGain = strlen($champs['gain']);
    $longueurDate = strlen($champs['date']);
    $longueurid = strlen($champs['numTournoi']);
    $longueurnewJoueur = strlen($champs['newJoueur']);
    $longueurKiller = strlen($champs['killer']);
    $longueurCitron = strlen($champs['citron']);

    if (isset($_POST['ajouter'])){
        if ($longueurGain > 4){
            $valid_Champ['longueur_inval_Gain'] = true;
        }
        if ($longueurDate > 10){
            $valid_Champ['longueur_inval_Date'] = true;
        }
        if ($longueurid > 4){
            $valid_Champ['longueur_inval_Id'] = true;
        }
        if ($longueurKiller > 3){
            $valid_Champ['longueur_inval_Killer'] = true;
        }
        if ($longueurCitron > 1){
            $valid_Champ['longueur_inval_Citron'] = true;
        }
    } elseif (isset($_POST['ajouterNouveau'])){        
        if ($longueurnewJoueur > 25){
            $valid_Champ['longueur_inval_New'] = true;
        } 
    }

    $patternNewJoueur = "#^[A-Z]([a-z]{0,11})([-]{0,1})([A-Z]{0,1})([a-z]{1,9})([ ]{0,1})[a-zA-Z]$#";
    $patternGain = "#^[-]{0,1}([0-9]{1,3})$#";
    $patternID = "#^[0-9]{1,4}$#"; 
    $patternDate = "#^([0-9]{4})[-]([0-9]{2})[-]([0-9]{2})$#";
    $patternKillerCitron = "#^([0-9]{1})([.][5]){0,1}$#"; 

    if (isset($_POST['ajouter'])){
        if (!preg_match($patternGain, $champs['gain'])){
            $valid_Champ['invalid_Gain'] = true;
        }
        if (!preg_match($patternID, $champs['numTournoi'])){
            $valid_Champ['invalid_Id'] = true;
        }
        if (!preg_match($patternDate, $champs['date'])){
            $valid_Champ['invalid_Date'] = true;
        }
        if (!preg_match($patternKillerCitron, $champs['killer'])){
            $valid_Champ['invalid_Killer'] = true;
        }
        if (!preg_match($patternKillerCitron, $champs['citron'])){
            $valid_Champ['invalid_Citron'] = true;
        }

    } elseif (isset($_POST['ajouterNouveau'])){
        if (!preg_match($patternNewJoueur, $champs['newJoueur'])){
            $valid_Champ['invalid_New'] = true;
        }
    } 
    return $valid_Champ;
}

function situation($champs, $valid_Champ){
    if (isset($_POST['ajouter'])){
        // Nous commençons par la section si la page est en anglais
        if ($champs['typeLangue'] === "francais"){
            if ($valid_Champ['tous_champs_Vide']){            
                $champs['message'] .= "Tous les champs sont vide.<br>"; 
            } else {
                // vérification au niveau du champ joueur
                if ($valid_Champ['vide_Joueur']){
                    $champs['message'] .= "Le champ du joueur est vide.<br>"; 
                } 
                // vérification au niveau du champ gain
                if ($valid_Champ['vide_Gain']){
                    $champs['message'] .= "Le champ du gain est vide.<br>"; 
                } else {
                    if ($valid_Champ['invalid_Gain']){
                        $champs['message'] .= "Le gain est invalide.<br>"; 
                    } 
                    if ($valid_Champ['longueur_inval_Gain']){
                        $champs['message'] .= "La longueur du gain est invalide.<br>"; 
                    }
                }
                // vérification au niveau du champ killer
                if ($valid_Champ['vide_Killer']){
                    $champs['message'] .= "Le champ du killer est vide.<br>"; 
                } else {
                    if ($valid_Champ['invalid_Killer']){
                        $champs['message'] .= "Le nombre de killer est invalide.<br>"; 
                    } 
                    if ($valid_Champ['longueur_inval_Killer']){
                        $champs['message'] .= "La longueur du killer est invalide.<br>"; 
                    }
                }
                // vérification au niveau du champ citron
                if ($valid_Champ['vide_Citron']){
                    $champs['message'] .= "Le champ du prix citron est vide.<br>"; 
                } else {
                    if ($valid_Champ['invalid_Citron']){
                        $champs['message'] .= "L'attribution du prix citron est invalide.<br>"; 
                    } 
                    if ($valid_Champ['longueur_inval_Citron']){
                        $champs['message'] .= "La longueur du prix citron est invalide.<br>"; 
                    }
                }   
                // vérification au niveau du champ position
                if ($valid_Champ['vide_position']){
                    $champs['message'] .= "Le champ de la position est vide.<br>"; 
                } 
                // vérification au niveau du numéro de tournoi
                if ($valid_Champ['vide_Id']){
                    $champs['message'] .= "Le champ du no. tournoi est vide.<br>"; 
                } else {
                    if ($valid_Champ['invalid_Id']){
                        $champs['message'] .= "Le champ no. du tournoi est invalide.<br>"; 
                    } 
                    if ($valid_Champ['longueur_inval_Id']){
                        $champs['message'] .= "La longueur du no. tournoi est invalide.<br>"; 
                    }  
                }            
                // vérification au niveau de la date
                if ($valid_Champ['vide_Date']){
                    $champs['message'] .= "Le champ de la date est vide.<br>"; 
                } else {
                    if ($valid_Champ['invalid_Date']){
                        $champs['message'] .= "Le champ de la date est invalide.<br>"; 
                    } 
                    if ($valid_Champ['longueur_inval_Date']){
                        $champs['message'] .= "Le champ de la date est invalide.<br>"; 
                    }
                }
            }
            // nous sommes rendu à la section si la page est en anglais
        } elseif ($champs['typeLangue'] === "english"){
            if ($valid_Champ['tous_champs_Vide']){            
                $champs['message'] .= "All fields are empty.<br>"; 
            } else {
                // vérification au niveau du champ joueur
                if ($valid_Champ['vide_Joueur']){
                    $champs['message'] .= "The player's field is empty.<br>"; 
                } 
                // vérification au niveau du champ gain
                if ($valid_Champ['vide_Gain']){
                    $champs['message'] .= "The gain field is empty.<br>"; 
                } else {
                    if ($valid_Champ['invalid_Gain']){
                        $champs['message'] .= "The gain is invalid.<br>"; 
                    } 
                    if ($valid_Champ['longueur_inval_Gain']){
                        $champs['message'] .= "The length of the gain is invalid.<br>"; 
                    }
                }
                // vérification au niveau du champ killer
                if ($valid_Champ['vide_Killer']){
                    $champs['message'] .= "The killer field is empty.<br>"; 
                } else {
                    if ($valid_Champ['invalid_Killer']){
                        $champs['message'] .= "The number of killer is invalid.<br>"; 
                    } 
                    if ($valid_Champ['longueur_inval_Killer']){
                        $champs['message'] .= "The length of the killer is invalid.<br>"; 
                    }
                }
                // vérification au niveau du champ citron
                if ($valid_Champ['vide_Citron']){
                    $champs['message'] .= "The field of the lemon price is empty.<br>"; 
                } else {
                    if ($valid_Champ['invalid_Citron']){
                        $champs['message'] .= "The award of the lemon prize is invalid.<br>"; 
                    } 
                    if ($valid_Champ['longueur_inval_Citron']){
                        $champs['message'] .= "The length of the lemon price is invalid.<br>"; 
                    }
                } 
                // vérification au niveau du champ position
                if ($valid_Champ['vide_position']){
                    $champs['message'] .= "The position field is empty.<br>"; 
                } 
                // vérification au niveau du numéro de tournoi
                if ($valid_Champ['vide_Id']){
                    $champs['message'] .= "The field of no. tournament is empty.<br>"; 
                } else { 
                    if ($valid_Champ['invalid_Id']){
                        $champs['message'] .= "The field no. of the tournament is invalid.<br>"; 
                    } 
                    if ($valid_Champ['longueur_inval_Id']){
                        $champs['message'] .= "The length of the no. tournament is invalid.<br>"; 
                    }      
                } 
                // vérification au niveau de la date
                if ($valid_Champ['vide_Date']){
                    $champs['message'] .= "The date field is empty.<br>"; 
                } else {
                    if ($valid_Champ['invalid_Date']){
                        $champs['message'] .= "The date field is invalid.<br>"; 
                    } 
                    if ($valid_Champ['longueur_inval_Date']){
                        $champs['message'] .= "The date field is invalid.<br>"; 
                    }
                }
            }
        }
    } elseif (isset($_POST['ajouterNouveau'])){
        if ($champs['typeLangue'] === "francais"){
            if ($valid_Champ['vide_NewJoueur']){            
                $champs['message'] .= "Le champ du nouveau joueur est vide.<br>"; 
            } else {
                if ($valid_Champ['invalid_New']){
                    $champs['message'] .= "Le nom du nouveau joueur n'est pas valide.<br>"; 
                } 
                if ($valid_Champ['longueur_inval_New']){
                    $champs['message'] .= "Le nom du nouveau joueur est trop long.<br>"; 
                }
                if ($valid_Champ['doublon_new_Joueur']){
                    $champs['message'] .= "Le nom du nouveau joueur est déjà présent dans la BD.<br>"; 
                }
            }
        } elseif ($champs['typeLangue'] === "english"){
            if ($valid_Champ['vide_NewJoueur']){            
                $champs['message'] .= "The new player's field is empty.<br>"; 
            } else {
                if ($valid_Champ['invalid_New']){
                    $champs['message'] .= "The name of the new player is invalid.<br>"; 
                } 
                if ($valid_Champ['longueur_inval_New']){
                    $champs['message'] .= "The name of the new player is too long.<br>"; 
                }
                if ($valid_Champ['doublon_new_Joueur']){
                    $champs['message'] .= "The name of the new player is already present in the BD.<br>"; 
                }
            }
        }
    } elseif (isset($_POST['effacer'])){
        if ($champs['typeLangue'] === "francais"){
            $champs['message'] = "Tous les champs ont été remis à null et tous les flag de validations ont été remis à faux.<br>"; 
        } elseif ($champs['typeLangue'] === "english"){
            $champs['message'] = "All fields have been reset and all validation flags have been overwritten.<br>"; 
        }                
    }
    return $champs;
}
/* Fonction pour vérifier si on affiche le message en vert ou en rouge */
function verificationTout_Champs($valid_Champ){
    $autorisation = true;
    foreach($valid_Champ as $eachFlag => $info){
        if ($info == true){
            $autorisation = false;
        }
    }
    return $autorisation;
}

/* Fonction pour vérifier si le champ Gain est invalide et le résultat va mettre la bordure et l'intérieur rouge */
function verifChampGain($valid_Champ){
    if ($valid_Champ['invalid_Gain'] || $valid_Champ['longueur_inval_Gain'] || $valid_Champ['vide_Gain']){
        return true;
    }
    return false;
}
/* Fonction pour vérifier si le champ ID est invalide et le résultat va mettre la bordure et l'intérieur rouge */
function verifChampId($valid_Champ){
    if ($valid_Champ['invalid_Id'] || $valid_Champ['longueur_inval_Id'] || $valid_Champ['vide_Id']){
        return true;
    }
    return false;
}
/* Fonction pour vérifier si le champ ID est invalide et le résultat va mettre la bordure et l'intérieur rouge */
function verifChampDate($valid_Champ){
    if ($valid_Champ['invalid_Date'] || $valid_Champ['longueur_inval_Date'] || $valid_Champ['vide_Date']){
        return true;
    }
    return false;
}
/* Fonction pour vérifier si le champ DATE est invalide et le résultat va mettre la bordure et l'intérieur rouge */
function verifChampJoueur($valid_Champ){
    if ($valid_Champ['vide_Joueur']){
        return true;
    }
    return false;
}
/* Fonction pour vérifier si le champ Killer est invalide et le résultat va mettre la bordure et l'intérieur rouge */
function verifChampKiller($valid_Champ){
    if ($valid_Champ['invalid_Killer'] || $valid_Champ['longueur_inval_Killer'] || $valid_Champ['vide_Killer']){
        return true;
    }
    return false;
}
/* Fonction pour vérifier si le champ Killer est invalide et le résultat va mettre la bordure et l'intérieur rouge */
function verifChampCitron($valid_Champ){
    if ($valid_Champ['invalid_Citron'] || $valid_Champ['longueur_inval_Citron'] || $valid_Champ['vide_Citron']){
        return true;
    }
    return false;
}
/* Fonction pour vérifier si le champ nouveau joueur est invalide et le résultat va mettre la bordure et l'intérieur rouge */
function verifChampNouveau($valid_Champ){
    if ($valid_Champ['invalid_New'] || $valid_Champ['longueur_inval_New'] || $valid_Champ['vide_NewJoueur']){
        return true;
    }
    return false;
}
/* Fonction pour vérifier si le champ position est invalide et le résultat va mettre la bordure et l'intérieur rouge */
function verifChampPosition($valid_Champ){
    if ($valid_Champ['vide_position']){
        return true;
    }
    return false;
}
/* Fonction pour ouvrir une connexion à la BD */
function connexionBD(){    
    $host = "benoitmignault.ca.mysql";
    $user = "benoitmignault_ca_mywebsite";
    $password = "d-&47mK!9hjGC4L-";
    $bd = "benoitmignault_ca_mywebsite";
    $connMYSQL = new mysqli($host, $user, $password, $bd);
    /*
    $host = "localhost";
    $user = "zmignaub";
    $password = "Banane11";
    $bd = "benoitmignault_ca_mywebsite";   
    $connMYSQL = mysqli_connect($host, $user, $password, $bd); 
    */
    return $connMYSQL;
}
/* Fonction pour valider si le user et son mdp dans les variables sessions sont bonnes */
function verificationUser($connMYSQL){
    $sql = "select user, password from benoitmignault_ca_mywebsite.login";
    $result = $connMYSQL->query($sql);

    foreach($result as $row){
        if ($_SESSION['user'] === "admin"){
            if (password_verify($_SESSION['password'], $row['password'])){
                return true; // dès qu'on trouve notre user admin + son bon mdp on exit de la fct
            }
        }
        // la fin de la vérification pour trouver notre user dans la BD et ainsi que la vérification de son mdp  
    }
    return false;
}

function redirection($champs){
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        session_destroy();
        header("Location: /erreur/erreur.php");          
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (isset($_POST['stats'])){               
            header("Location: /login/statsPoker/poker.php");

        } elseif (isset($_POST['login'])){
            session_destroy();             
            header("Location: /login/login.php?langue={$champs["typeLangue"]}");

        } elseif (isset($_POST['accueuil'])){
            session_destroy();
            if ($typeLangue == 'english'){
                header("Location: /english/english.html");
            } else {
                header("Location: /index.html");
            } 
        }  
    }
    exit; // pour arrêter l'éxecution du code php
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    session_start();
    $champs["typeLangue"] = "francais";
    if (isset($_SESSION['user']) && isset($_SESSION['password']) && 
        isset($_SESSION['typeLangue'])){        
        $connMYSQL = connexionBD();
        $verificationUser = verificationUser($connMYSQL);         
        $champs = initialisation_Champs(); 
    } else {
        redirection($champs);
    }    

    // on vérifier si notre user existe en bonne éduforme
    if (!$verificationUser){
        redirection($champs);
    } elseif ($champs["typeLangue"] !== "francais" && $champs["typeLangue"] !== "english"){
        redirection($champs);
    } else {        
        $champs = initialisation_Champs();
        $valid_Champ = initialisation();
        $arrayMots = traduction($champs);  
        $listeJoueurs = creationListe($connMYSQL, $arrayMots, $champs);
    }
    $connMYSQL->close(); 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $champs["typeLangue"] = "francais";
    if (isset($_SESSION['user']) && isset($_SESSION['password']) && 
        isset($_SESSION['typeLangue'])){        
        $connMYSQL = connexionBD();
        $verificationUser = verificationUser($connMYSQL);         
        $champs = initialisation_Champs();   
    } else {
        redirection($champs);
    }    

    // on vérifier si notre user existe en bonne éduforme
    if (!$verificationUser){
        redirection($champs);
    } elseif ($champs["typeLangue"] !== "francais" && $champs["typeLangue"] !== "english"){
        redirection($champs);
    } else {
        if (isset($_POST['stats']) || isset($_POST['login']) || isset($_POST['accueuil'])){
            redirection($champs);
        } elseif (isset($_POST['effacer'])) {
            $champs = initialisation_Champs();
            $valid_Champ = initialisation();
            $champs = situation($champs, $valid_Champ);  
            $verif_tous_flag = verificationTout_Champs($valid_Champ);            
        } elseif (isset($_POST['ajouter'])) {            
            $champs = initialisation_Champs();
            $valid_Champ = initialisation();
            $champs = remplissageChamps($champs);
            $valid_Champ = validation($champs, $valid_Champ, $connMYSQL);
            $champs = situation($champs, $valid_Champ);
            if ($champs['message'] === ""){
                $victoire = "";
                $fini2e = "";
                if ($champs["position"] === "victoire"){
                    $victoire = "X";
                } elseif ($champs["position"] === "fini2e"){
                    $fini2e = "X";
                } 
                $killerFloat = floatval($champs["killer"]);
                $insert = "INSERT INTO benoitmignault_ca_mywebsite.poker (joueur,gain,victoire,fini_2e,id_tournoi,date,id,killer,prixCitron) VALUES ";
                $insert .= "('".$champs["listeJoueur"]."',
                             '".$champs["gain"]."',
                             '".$victoire."',
                             '".$fini2e."',
                             '".$champs["numTournoi"]."',
                             '".$champs["date"]."',
                             NULL,
                             '".$killerFloat."',
                             '".$champs["citron"]."')";
                $connMYSQL->query($insert);
                if ($champs['typeLangue'] === "francais"){
                    $messageAjout = "Les informations du joueur {$champs["listeJoueur"]} a été ajouté à la BD.";
                } elseif ($champs['typeLangue'] === "english"){
                    $messageAjout = "The player information {$champs["listeJoueur"]} has been added to the BD.";
                }
                $champs = initialisation_Champs();
                $champs['message'] = $messageAjout;                
            }           
            $verif_tous_flag = verificationTout_Champs($valid_Champ);
        } elseif (isset($_POST['ajouterNouveau'])) {     
            $champs = initialisation_Champs();
            $valid_Champ = initialisation();
            $champs = remplissageChamps($champs);
            $valid_Champ = validation($champs, $valid_Champ, $connMYSQL);
            $champs = situation($champs, $valid_Champ);
            if ($champs['message'] === ""){
                $insert = "INSERT INTO benoitmignault_ca_mywebsite.joueur (joueur,id) VALUES ";
                $insert .= "('".$champs["newJoueur"]."', NULL)";
                $connMYSQL->query($insert);
                if ($champs['typeLangue'] === "francais"){
                    $messageAjout = "Le nouveau joueur {$champs["listeJoueur"]} a été ajouté à la BD.";
                } elseif ($champs['typeLangue'] === "english"){
                    $messageAjout = "The player information {$champs["listeJoueur"]} has been added to the BD.";
                }
                $champs = initialisation_Champs();
                $champs['message'] = $messageAjout;                
            } 
            $verif_tous_flag = verificationTout_Champs($valid_Champ);
        }
        $arrayMots = traduction($champs);
        $listeJoueurs = creationListe($connMYSQL, $arrayMots, $champs);
    }
    $connMYSQL->close();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <!-- https://pixabay.com/fr/fichier-ic%C3%B4ne-web-document-2389211/ -->
        <link rel="shortcut icon" href="admin.png">		
        <link rel="stylesheet" type="text/css" href="admin.css"> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $arrayMots['title']; ?></title> 
        <style>
            body{
                margin:0;    
                /* Fichier photoPoker.jpg est une propriété du site https://pixabay.com/fr/cha%C3%AEne-de-blocs-personnels-2850276/ 
                sous licence libre */
                background-image: url("background.jpg");
                background-position: center;
                background-attachment: fixed;
                background-size: 100%;
            }   
        </style>
    </head>
    <body>
        <h1><?php echo $arrayMots['h1']; ?></h1>
        <div class="container"> 
            <h2><?php echo $arrayMots['h3']; ?></h2>                   
            <form method="post" action="admin.php">
                <div class='formulaire_joueur'>         
                    <div class="joueur <?php if (verifChampJoueur($valid_Champ)) { echo "erreur";} ?>">
                        <label for="joueur"><?php echo $arrayMots['joueur']; ?></label>
                        <select id="joueur" name="listeJoueur">
                            <?php echo $listeJoueurs; ?>
                        </select>
                    </div>   
                    <div class="position">
                        <label for="position"><?php echo $arrayMots['resultat']; ?></label>
                        <div>
                            <input type="radio" <?php if ($champs['position'] === "victoire"){
    echo "checked"; } ?> name="position" id="victoire" value="victoire">
                            <label for="victoire"><?php echo $arrayMots['victoire']; ?></label>
                        </div>
                        <div>
                            <input type="radio" <?php if ($champs['position'] === "fini2e"){
    echo "checked"; } ?> name="position" id="fini2e" value="fini2e">
                            <label for="fini2e"><?php echo $arrayMots['fini2e']; ?></label>
                        </div>
                        <div>
                            <input type="radio" <?php if ($champs['position'] === "autre"){
    echo "checked"; } ?> name="position" id="autre" value="autre">
                            <label for="autre"><?php echo $arrayMots['autre']; ?></label>  
                        </div>
                    </div>
                    <div class="gain <?php if (verifChampGain($valid_Champ)) { echo "erreur";} ?>">
                        <label for="gain"><?php echo $arrayMots['gain']; ?></label>
                        <input maxlength="4" type="text" id="gain" name="gain" value="<?php echo $champs['gain']?>">
                    </div>
                    <div class="numero <?php if (verifChampId($valid_Champ)){ echo "erreur";} ?>">
                        <label for="numTournoi"><?php echo $arrayMots['noId']; ?></label>
                        <input maxlength="4" type="text" id="numTournoi" name="numTournoi" value="<?php echo $champs['numTournoi']?>">
                    </div>
                    <div class="date <?php if (verifChampDate($valid_Champ)){ echo "erreur";} ?>">
                        <label for="date">Date</label>
                        <input maxlength="10" type="text" id="date" name="date" value="<?php echo $champs['date']?>">
                    </div>
                    <div class="killer <?php if (verifChampKiller($valid_Champ)){ echo "erreur";} ?>">
                        <label for="killer"><?php echo $arrayMots['killer']; ?></label>
                        <input maxlength="3" type="text" id="killer" name="killer" value="<?php echo $champs['killer']?>"> 
                    </div>  
                    <div class="citron <?php if (verifChampCitron($valid_Champ)){ echo "erreur";} ?>">
                        <label for="citron"><?php echo $arrayMots['citron']; ?></label>
                        <input maxlength="1" type="text" id="citron" name="citron" value="<?php echo $champs['citron']?>">
                    </div> 
                    <div class="bas_formulaire">
                        <input class="bouton" type="submit" name="ajouter" value="<?php echo $arrayMots['btn_add']; ?>">
                        <input class="bouton" type="submit" name="effacer" value="<?php echo $arrayMots['btn_erase']; ?>">
                    </div>
                    <div class="bas_formulaire">
                        <p class="<?php if ( ( isset($_POST['effacer']) || isset($_POST['ajouter']) || isset($_POST['ajouterNouveau']) ) && $verif_tous_flag == true) {echo "avert";} else {echo "erreur";} ?>"> <?php echo $champs['message']; ?> </p>
                    </div>
                </div>
            </form>            
            <form method="post" action="admin.php">
                <div class="formulaire_Nouveau">
                    <div class="<?php if (verifChampNouveau($valid_Champ)) { echo "erreur";} ?>">
                        <label for="newJoueur"><?php echo $arrayMots['newJoueur']; ?></label>
                        <input maxlength="25" type="text" id="newJoueur" name="newJoueur" value="<?php echo $champs['newJoueur']?>">
                    </div>
                    <div>
                        <input class="bouton" type="submit" name="ajouterNouveau" value="<?php echo $arrayMots['btn_new']; ?>"> 
                    </div>                               
                </div>
            </form>
            <form method="post" action="admin.php">
                <div class="footer">
                    <div class="btn_footer">
                        <input class="bouton" type="submit" name="stats" value="<?php echo $arrayMots['btn_loginPoker']; ?>">
                    </div>
                    <div class="btn_footer">
                        <input class="bouton" type="submit" name="login" value="<?php echo $arrayMots['btn_login']; ?>">
                    </div>
                    <div class="btn_footer">
                        <input class="bouton" type="submit" name="accueuil" value="<?php echo $arrayMots['btn_return']; ?>">
                    </div>
                </div>
            </form> 
        </div>
    </body>
</html>