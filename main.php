<?php 
require_once('src/model/ContactManager.php');
while(true){
    $line = readline("Entrez votre commande : ");
    if($line === "list"){
        $contactsRepository = new ContactManager();
        $contactsRepository->connection = new DBConnect();
        $contacts = $contactsRepository->findAll();
        foreach($contacts as $contact){
            echo "$contact->id\n";
            echo "$contact->name\n";
            echo "$contact->email\n";
            echo "$contact->phone\n";
        }
    }
    else{
    echo "Vous avez saisi : $line\n";
    }
}
