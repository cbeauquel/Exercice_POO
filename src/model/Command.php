<?php
require('ContactManager.php');
// Classe qui regroupe les comportements des commandes saisies dans le terminal

class Command
{
    
    //Méthode de la classe Command permettant de récupérer l'ensemble des contacts
    public function list(): void {
        $dbConnection = new DBConnect();
        $contactsRepository = new ContactManager($dbConnection);
        $contacts = $contactsRepository->findAll();
        foreach($contacts as $contact){
        $contact->toString();
        }
    }
    // méthode permettant l'affichage du détail d'un contact selon son ID
    public function detail(string $line): void {
        //On vérifie les données saisies
        $pattern = '/^detail\s+(\d+)$/';
        if (preg_match($pattern, $line, $matches)){
            //on convertit l'id en int pour le setter de la classe contact
            $dbConnection = new DBConnect();
            $contactRepository = new ContactManager($dbConnection);

            $contactId = intval($matches[1]);    
            $contact = $contactRepository->findById($contactId);
            //si la propriété name de l'objet contact est null alors on informe l'utilisateur en expliquant qu'il n'y a pas de contact avec l'identifiant saisi 
            if($contact !== null){
               $contact->toStringDetail();
            } else {
                //sinon on affiche les données en appelant la méthode toStringDetail().
                echo "Aucun contact avec cet identifiant, saisissez la commande \"list\" pour voir les identifiants disponibles \n";
            }
        } else {
        echo "L'identifiant doit être un chiffre et suivre le format \"detail [ID]\".\n";
        } 
    }

    // méthode pour créer un nouveau contact en base de donnée, la méthode a pour argument la variable $line qui stocke les données saisies par l'utilisateur
    public function create($line): void
    {
        //on crée les variables nécessaires pour stocker les informations de l'utilisateur qui sont une ligne de caractère stockée dans $line
        $name = null;
        $email = null;
        $phone = null;
        $valid = null;
        //on détecte le mot "create" et on le retire de la chaîne de caractère
        $pattern = '/^create\s+(.*)\s*[,]\s*(.*)\s*[,]\s*(.*)$/';
        if (preg_match($pattern, $line, $matches)){
            //on stocke les lignes du tableau aux variables créées au dessus pour préparer l'ajout du contact
            $name = $matches[1];
            $email = $matches[2];
            $phone = $matches[3];
            //si les variables sont vides ou null, on envoie un message d'erreur car il manque des données
            if (!preg_match('/^([a-zA-ZÀ-ÖØ-öø-ÿ\-]+)$/', $name, $match)){
                echo "Une erreur a été détectée dans le nom.\n";
             } elseif (!preg_match('/^(\d{3}[-\s]?\d{3}[-\s]?\d{4})$/', $phone, $match)) {
                 echo "Une erreur a été détectée dans le numéro de téléphone.\n";
             } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)){
                 echo "Une erreur a été détectée dans l'adresse e-mail.\n";
             } else {
                // Avant de lancer l'ajout en base de donnée, on affiche les données saisies par l'utilisateur et on demande la confirmation de l'ajout.
                $valid = readline("Vous avez saisi : $name, $email, $phone, saisissez \"oui\" pour confirmer votre commande : ");
            }
            //si l'utilisateur valide, on lance l'ajout en créant une nouvelle instance de la classe ContactManager et en créant une nouvelle instance de la classe DBConnect
            //enfin, on appelle la méthode createContact pour préparer et exécuter la requête
            if($valid === "oui"){
                $dbConnection = new DBConnect();
                $createRepository = new ContactManager($dbConnection);
                
                $createContact = $createRepository->createContact($name, $email, $phone);
            }
            //si l'utilisateur ne valide pas (saisie autre que "oui"), on propose à l'utilisateur de réessayer.
            else{
                echo "Ressaisissez votre contact en tapant la commande \"create\".\n";
            }
        //sinon (le nombre de virgules est inférieur à 2) on envoie le message d'erreur    
        } else {
            echo "Les données saisies ne sont pas conformes, veuillez refaire votre saisie.\n";
        }
    }

    /**
     * Méthode pour modifier un contact
     *
     * @param string $line
     * @return void
     */
    public function modify(string $line): void
    {
        //On vérifie les données saisies
        $pattern = '/modify\s+([0-9]+)$/';
        $contactId = 0;
        if (preg_match($pattern, $line, $matches)){
            $contactId = $matches[1];
            $dbConnection = new DBConnect();
            $updateRepository = new ContactManager($dbConnection);
            //on appelle la méthode findById avec en argument l'ID du contact à modifier pour afficher les données à modifier
            $oldContact = $updateRepository->findById($contactId);
            //On vérifie que le contact existe et on affiche les données à modifier
            if($oldContact === null) {
                echo "Aucun contact avec cet identifiant, saisissez la commande \"list\" pour voir les identifiants disponibles.\n";
            } else {
                $oldContactDetail = $oldContact->toStringDetail(); 
                $oldName = $oldContact->getName();
                $oldEmail = $oldContact->getEmail();
                $oldPhone = $oldContact->getPhone();
                //Pour éviter à l'utilisteur de saisir les données qui ne nécessitent pas de modification, on propose de saisir un tirer pour les données à laisser tel quel
                $rawUpdContact = readline("Pour modifier le contact, saisissez les données séparées par des virgules. Saisissez un tiret \"-\" pour les données que vous ne souhaitez pas modifier : ");
                //Comme pour la commande create, on prépare les variables qui vont stockées nos données modifiées 
                $name = null;
                $email = null;
                $phone = null;
                $updValid = null;
                $updContact = [];
                //On vérifie si la chaîne de caractère est conforme (nombre de virgules) et on met à jour les données avec la saisie de l'utilisateur. POur les données qui ne changent pas, on remet l'ancienne donnée dans la variable
                $pattern = '/^\s*(.*)\s*[,]\s*(.*)\s*[,]\s*(.*)$/';
                if (preg_match($pattern, $rawUpdContact, $updContact)){
                    if(trim($updContact[1]) === '-'){$name = $oldName;} else {$name = trim($updContact[1]);}
                    if(trim($updContact[2]) === '-'){$email = $oldEmail;} else {$email = trim($updContact[2]);}
                    if(trim($updContact[3]) === '-'){$phone = $oldPhone;} else {$phone = trim($updContact[3]);}
                    //Si une erreur est détectée sur l'une des variables, on envoie un message d'erreur indiquant d'où vient le problème
                    if (!preg_match('/^([a-zA-ZÀ-ÖØ-öø-ÿ\-]+)$/', $name, $match)){
                        echo "Une erreur a été détectée dans le nom.\n";
                     } elseif (!preg_match('/^(\d{3}[-\s]?\d{3}[-\s]?\d{4})$/', $phone, $match)) {
                         echo "Une erreur a été détectée dans le numéro de téléphone.\n";
                     } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)){
                         echo "Une erreur a été détectée dans l'adresse e-mail.\n";
                     } else {
                    //Avant de lancer la modification, on demande la confirmation à l'utilisateur en affichant les données qui vont être injectées
                    $updValid = readline("Vous avez saisi : $name, $email, $phone.\nSi vous souhaitez mettre à jour le contact avec ces données, saisissez \"oui\" :");
                    }
                    //Si l'utilisateur valide, on appelle la méthode updateContact de la classe contactManager pour mettre à jour les données validées
                    if ($updValid === "oui"){
                        $updateContact = $updateRepository->updateContact($contactId, $name, $email, $phone);
                    } else {
                    //Si l'utilisateur ne valide pas (saisie autre que oui), on affiche un message d'annulation et on invite l'utilisateur à recommencer.
                    echo "La modification n'a pas eu lieu. Vous pouvez recommencer votre modification en saisissant la commande \"modify\".\n";
                    }
                } else {
                    //Si l'utilisateur n'a pas bien saisi les données entre les virgules on envoie un message d'erreur
                    echo "Les données saisies ne sont pas conformes, veuillez refaire votre saisie.\n";
                }
            }
        } else {
             //Si l'utilisateur n'a pas saisi un id valide on informe l'utilisateur
            echo "Pour modifier un contact, vous devez saisir un Numéro valide.\n";
        }
    }
    //Méthode permettant la suppression d'un contact
    public function delete($line): void
    {
        //On vérifie les données saisies et on isole l'Id
        $pattern = '/^delete\s+(\d+)$/';
        if (preg_match($pattern, $line, $matches)){
            $contactId = intval($matches[1]);
        //Si l'ID est bien un chiffre, on crée une instance de la classe ContactManager et une instance de la classe DBConnect pour se connecter à la base de donnée
            $dbConnection = new DBConnect();
            $deleteRepository = new ContactManager($dbConnection);
            $contact = $deleteRepository->findById($contactId);
            //on vérifie que le contact est bien présent en BDD
            if($contact === null){
                echo "Aucun contact avec cet identifiant, saisissez la commande \"list\" pour voir les identifiants disponibles.\n"; 
            } else {
                //on affiche les données du contact choisi par l'utilisateur pour suppression
                $contact->toStringDetail();
                //on informe l'utilisateur du caractère définitif de l'action et on demande la validation   
                $delValid = readline("Cette action est irréversible, êtes-vous sûr ? (saisir \"oui\") : ");
                if ($delValid === "oui"){
                    $deleteContact = $deleteRepository->deleteContact($contactId);
                } else {
                    echo "Action annulée, aucun contact n'a été supprimé.\n";
                }
            }        
        } else {
            //si l'utilisateur n'a pas saisi un identifiant valide il reçoit un message d'erreur
            echo "L'identifiant doit être un chiffre seul.\n";
        }
    }
    //méthode qui affiche l'aide avec la commande "help"
    public function displayHelp(): void{
        echo "
        help : affiche cette aide\n
        list : liste les contacts\n
        detail [id] : affiche le détail d'un contact\n
        create [name], [email], [phone number] : crée un contact\n
        modify [id] : modifie un contact\n
        delete [id] : supprime un contact définitivement\n
        quit : quitte le programme\n
        ";
    }
}