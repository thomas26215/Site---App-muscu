<?php
require_once 'UserBase.php';
$base = new UserBase();

function afficherMessageErreur($message){
	return "<p>".$message."</p>";
}

/**
 *
 * Cache n lettres d'un chaîne de charactères : monTest => monT***
 * @param $text Le texte origininal
 * @param $n Le nombre de lettres à cacher
 * @return string Ma nouvelle chaîne de charactères avec n lettres cachées
 */

function hideLettersInText($text, $n){

	$length = strlen($text);

	// Si $n est supérieur ou égal à la longueur du texte, on cache tout
    if ($n >= $length) {
        return str_repeat('*', $length);
    }
    
    // On prend la partie du texte à garder visible
    $visiblePart = substr($text, 0, $length - $n);
    
    // On ajoute les astérisques pour la partie à cacher
    $hiddenPart = str_repeat('*', $n);
    
    // On combine les deux parties
    return $visiblePart . $hiddenPart;
}