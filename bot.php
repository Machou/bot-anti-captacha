<?php
/* Fonction pour enregistrer une image à partir d'une URL */

function extrac_image($url,$image){
    $ch = curl_init ($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
    $raw=curl_exec($ch);
    curl_close ($ch);
    if(file_exists($image)){
        unlink($image);
    }
    $fp = fopen($image,'x');
    fwrite($fp, $raw);
    fclose($fp);
}


/*création du fichier cookie à vide */

file_put_contents('testcookie.txt','');


/* première page : page d'inscription du site et récupération du cookie */

$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, "http://www.communauty.info/enregistrement.html");
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION,1);
curl_setopt($curl, CURLOPT_COOKIEJAR,'testcookie.txt');
curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; fr; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13');
$page=curl_exec($curl);


/* Ecriture du code source de la page d'inscription dans un fichier html */

$fp = fopen('inscription.html', 'w');
fwrite($fp, $page);
fclose($fp);


/* Extraction du numéro aléatoire de l'image */

$rand_portion = mb_strcut( $page, strpos($page, 'rand='), strpos($page, ';') );
$rand_portion2 = mb_strcut($rand_portion, 5, strpos($rand_portion, 'w') );
$rand_portion3 = mb_strcut($rand_portion2, 0, strpos($rand_portion, ')') );
$id_aleatoire = mb_strcut($rand_portion3, 0, strpos($rand_portion3, '"') );


/* Extraction du PHPSSSID */

$php_ssid_portion = mb_strcut( $page, strpos($page, 'PHPSESSID='), strpos($page, 'ISO-8859-1') );
$php_ssid_portion2 = mb_strcut( $php_ssid_portion, strpos($php_ssid_portion,"="), strpos($php_ssid_portion, '&') );
$php_ssid = mb_strcut( $php_ssid_portion2, 1, -9 );


/* URL de l'image du Captcha */

$url= "http://www.communauty.info/index.php?PHPSESSID=$php_ssid&amp;action=verificationcode;rand=$id_aleatoire";

$fp = fopen('url_image.txt', 'w');
fwrite($fp, $url);
fclose($fp);


/* Enregistrement de l'image dans le dossier courant */

extrac_image($url,"image");


/* execute la commande pour convertir en image noir et blanc */

shell_exec('convert image -monochrome image.tif');


/* excute la commande pour reconnaitre les caractère contenu dans image.tif */

shell_exec('tesseract image.tif image');
?>
