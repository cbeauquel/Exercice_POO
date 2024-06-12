<?php 
require_once('src/model/Command.php');
while(true){
    $line = readline("Entrez votre commande : ");
    if($line === "list"){
        $contact = new Command();
        $contact->list();
    }
    else{
    echo "Vous avez saisi : $line\n";
    }
}
