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
        $contactRepository = new ContactManager();
        $contactRepository->connection = new DBConnect();
        $contact = $contactRepository->findById($contactId);
        if($contact->getName() === null){
            echo "Aucun contact avec cet identifiant, saisissez la commande \"list\" pour voir les identifiants disponibles \n";
        } else {
            $contact->toString();
        }
    }
}