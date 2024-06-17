<?php 
require_once('src/model/Command.php');
while(true){

    $line = readline("Entrez votre commande (list, detail, create, modify, delete, help, quit) :");

    if($line === "list"){
        $contacts = new Command();
        $contacts->list();

    } elseif (substr($line, 0, 6) === "detail" && ctype_alpha($line) === false){
        $contact = new Command();
        $contact->detail($line);

    } elseif ($line === "create"){
        $createContact = new Command();
        $createContact->create();    

    } elseif (substr($line, 0, 6) === "modify" && ctype_alpha($line) === false){
        $updateContact = new Command();
        $updateContact->modify($line);    

    } elseif (substr($line, 0, 6) === "delete" && ctype_alpha($line) === false) {
        $deleteContact = new Command();
        $deleteContact->delete($line); 

    } elseif ($line === "help") {
        $help = new Command();
        $help->displayHelp(); 

    } elseif ($line === "quit") {
        echo "Vous avez quitté le programme, merci de l'avoir utilisé !";
        break;
    } else {
    echo "Vous avez saisi : $line\n";
    }
}
