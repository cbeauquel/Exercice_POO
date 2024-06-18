<?php
require('ContactManager.php');
class Command
{

    public function list(): void {
        $contactsRepository = new ContactManager();
        $contactsRepository->connection = new DBConnect();
        $contacts = $contactsRepository->findAll();
        foreach($contacts as $contact){
        $contact->toString();
        }
    }

    public function detail($line): void {
        $lineVerif = preg_match('/detail/', $line, $matches);
        $contactId = trim(str_replace($matches, '',$line));
        if (is_numeric($contactId)){
            $contactRepository = new ContactManager();
            $contactRepository->connection = new DBConnect();
            $contact = $contactRepository->findById($contactId);
            if($contact->getName() === null){
                echo "Aucun contact avec cet identifiant, saisissez la commande \"list\" pour voir les identifiants disponibles \n";
            } else {
                $contact->toStringDetail();
            }
        } else {
        echo "L'identifiant doit être un chiffre.\n";          
        } 
    }

    public function create(): void
    {
        $name = null;
        $email = null;
        $phone = null;
        $valid = null;
        $rawCreateContact = readline("Saisissez le nom, l'adresse e-mail et le numéro de téléphone, séparés par des virgules : ");
        $createContact = [];
        if (substr_count($rawCreateContact, ',', 0) === 2){
            $createContact = explode(',', $rawCreateContact, 3);
            $name = trim($createContact[0]);
            $email = trim($createContact[1]);
            $phone = trim($createContact[2]);
            if (
                empty($name)||
                empty($email)||
                empty($phone)||
                is_null($name)||
                is_null($email)||
                is_null($phone)){
                echo "Tous les champs sont obligatoire pour créer un contact.\n";
                } elseif (preg_match('/\d/i', $name)){
                    echo "! ERREUR ! Le nom ne doit comporter que des lettres\n";
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))  {
                    echo "! ERREUR ! Saissez une adresse e-mail valide\n";
                } elseif (strlen($phone) < 10){
                    echo "! ERREUR ! Saissez un numéro de téléphone valide\n";
                }
            else {
                $valid = readline("Vous avez saisi : $name, $email, $phone, saisissez \"oui\" pour confirmer votre commande : ");
            }
            if($valid === "oui"){
                $createRepository = new ContactManager();
                $createRepository->connection = new DBConnect();
                $createContact = $createRepository->createContact($name, $email, $phone);
            }
            else{
                echo "Ressaisissez votre contact en tapant la commande \"create\".\n";
            }
        } else {
            echo "Les données saisies ne sont pas conformes, veuillez refaire votre saisie.\n";
        }
    }

    public function modify($line): void
    {
        $lineVerif = preg_match('/modify/', $line, $matches);
        $contactId = trim(str_replace($matches, '',$line));
        if (is_numeric($contactId)){
            $updateRepository = new ContactManager();
            $updateRepository->connection = new DBConnect();
            $oldContact = $updateRepository->findById($contactId);
            if($oldContact->getName() === null) {
                echo "Aucun contact avec cet identifiant, saisissez la commande \"list\" pour voir les identifiants disponibles.\n";
            } else {
                $oldContactDetail = $oldContact->toStringDetail(); 
                $oldName = $oldContact->getName();
                $oldEmail = $oldContact->getEmail();
                $oldPhone = $oldContact->getPhone();
                $rawUpdContact = readline("Pour modifier le contact saisissez les données séparées par des virgules. Saisissez un tiret \"-\" pour les données que vous ne souhaitez pas modifier : ");
                $name = null;
                $email = null;
                $phone = null;
                $updValid = null;
                $updContact = [];
                if (substr_count($rawUpdContact, ',', 0) === 2){
                    $updContact = explode(',', $rawUpdContact, 3);
                    if(trim($updContact[0] === '-')){$name = $oldName;} else {$name = trim($updContact[0]);}
                    if(trim($updContact[1] === '-')){$email = $oldEmail;} else {$email = trim($updContact[1]);}
                    if(trim($updContact[2] === '-')){$phone = $oldPhone;} else {$phone = trim($updContact[2]);}                
                    if(preg_match('/\d/i', $name) && $name !== null || $name ==='' || empty($name)){
                    echo "! ERREUR ! Le nom ne doit comporter que des lettres : \n";
                    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) && $email !== null)  {
                        echo "! ERREUR ! Saissez une adresse e-mail valide, \n";
                    } elseif (strlen($phone) < 10 && $phone !== null){
                    echo "! ERREUR ! Saissez un numéro de téléphone valide, \n";
                    } else {
                    $updValid = readline("Vous avez saisi : $name, $email, $phone.\nSi vous souhaitez mettre à jour le contact avec ces données, saisissez \"oui\" :");
                    }
                    if ($updValid === "oui"){
                        $updateContact = $updateRepository->updateContact($contactId, $name, $email, $phone);
                    } else {
                    echo "La modification n'a pas eu lieu. Vous pouvez recommencer votre modification en saisissant la commande \"modify\".\n";
                    }
                } else {
                    echo "Les données saisies ne sont pas conformes, veuillez refaire votre saisie.\n";
                }
            }
        } else {
            echo "Vous avez saisi : $contactId.\n Pour modifier un contact, vous devez saisir un Numéro valide.\n";
        }
    }

    public function delete($line): void
    {
        $idDeleteContact = (trim(substr($line,7)));
        $contactId = $idDeleteContact;
        if (is_numeric($idDeleteContact)){
            $deleteRepository = new ContactManager();
            $deleteRepository->connection = new DBConnect();
            $deleteContactName = $deleteRepository->findById($contactId);
            if ($deleteContactName->getName() === null){
                echo "Aucun contact avec cet identifiant, saisissez la commande \"list\" pour voir les identifiants disponibles.\n"; 
            } else {
                $Contact = new Command();
                $Contact->detail($idDeleteContact);      
                $delValid = readline("Cette action est irréversible, êtes-vous sûr ?");
                if ($delValid === "oui"){
                    $lineVerif = preg_match('/delete/', $line, $matches);
                    $idDeleteContact = trim(str_replace($matches, '',$line));
                    $deleteContact = $deleteRepository->deleteContact($idDeleteContact);
                } else {
                    echo "Action annulée, aucun contact n'a été supprimé.\n";
                }
            }        
        } else {
            echo "L'identifiant doit être un chiffre seul.\n";
        }
    }

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