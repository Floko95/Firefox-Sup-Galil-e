<?php

function chaineAleatoire($taille){
  do {
    $code = bin2hex(openssl_random_pseudo_bytes($taille, $cstrong));
  }
  while ($code == false);
  return $code;
}

function connexionCookie(){

}
