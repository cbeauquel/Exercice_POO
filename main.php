<?php 
require_once('src/model/Command.php');
while(true){
    $line = readline("Entrez votre commande : ");
    if($line === "list"){
        $contacts = new Command();
        $contacts->list();
    } elseif (substr($line, 0, 6) === "detail" && ctype_alpha($line) === false){
        $contact = new Command();
        $contact->detail($line);
    } elseif (substr($line, 0, 6) === "detail" && ctype_alpha($line) === false){
        $contact = new Command();
        $contact->detail($line);
    }
    else{
    echo "Vous avez saisi : $line\n";
    }
}
