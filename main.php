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
    } elseif ($line === "create"){
            $name = null;
            $email = null;
            $phone = null;
        while(true){
            $name = readline("saisissez le nom : ");
            if(ctype_alpha($name)){
                $email = readline("Saisissez l'adresse e-mail :");
                break;
            } else {
                echo "! ERREUR ! Le nom ne doit comporter que des lettres\n";
            }
        }
        while(true){
            if (filter_var($email, FILTER_VALIDATE_EMAIL)){
                $phone = readline("Saisissez un numéro de téléphone :");
                break;
            } else {
                echo "! ERREUR ! Saissez une adresse e-mail valide\n";
                $email = readline("Saisissez l'adresse e-mail :");
            }
        }
        while(true){
            if (strlen($phone) >= 10){
                echo "vous avez saisi :\n";
                echo "$name\n";
                echo "$email\n";
                echo "$phone\n";
                $valid = readline("Est-ce correct ?");
                break;
            } else {
                echo "! ERREUR ! Saissez un numéro de téléphone valide\n";
                $phone = readline("Saisissez un numéro de téléphone :");
            }
        }
        if($valid === "oui"){
            $createContact = new Command();
            $createContact->create($name, $email, $phone);
        }
        else{
            echo "Ressaisissez votre contact en tapant la commande \"create\"\n";
        }
    } elseif ($line === "delete") {
        $idDeleteContact = readline("Saisissez le numéro du contact à supprimer (vous pouvez saisir la commande list pour visualiser les contacts) : ");
        while(true){
            if (is_numeric($idDeleteContact)){
                $line = $idDeleteContact;
                $contact = new Command();
                $contact->detail($line);    
                $valid = readline("Cette action est irréversible, êtes-vous sûr ?");
            break;
            } else {
                var_dump($idDeleteContact);
                echo "Vous avez saisi : $idDeleteContact\n";
                $idDeleteContact = Readline("Pour supprimer un contact, vous devez saisir un Numéro valide :");
            }
        }
        if ($valid === "oui"){
            $deleteContact = new Command();
            $deleteContact->delete($idDeleteContact);
        } 

    }
    else{
    echo "Vous avez saisi : $line\n";
    }
}
