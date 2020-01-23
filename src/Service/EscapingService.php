<?php
/**
 * Created by PhpStorm.
 * User: jacop
 * Date: 22-Jan-20
 * Time: 10:44 AM
 */

namespace App\Service;


class EscapingService
{
 const ESCAPE_CHAR = '_';

     private $safe;

     public function __construct()
     {
         $this->safe = EscapingService::generateStandardSet();
     }

     public static function generateStandardSet(){
         $numbers = range(0,9);
         $letters = range('a','z');
         $capitalLetters = range('A','Z');

         return array_merge(array_merge($numbers, $letters), $capitalLetters);
     }

    public function escape_char($c, $escape_char = EscapingService::ESCAPE_CHAR) {

     $buf = [];
     $encodedC = utf8_encode($c);

     for($i=0; $i< strlen($encodedC); $i++){
         $buf[] = $escape_char;
         $buf[] = dechex(ord($encodedC[$i]));

     }
     return implode("",$buf);

 }

 public function escape($to_escape, $escape_char = EscapingService::ESCAPE_CHAR){

     $chars = [];


    for ($c = 0; $c <strlen($to_escape); $c++){
        if(in_array($to_escape[$c], $this->safe, true)){
            $chars[] = $to_escape[$c];
        }else{

            $result = $this->escape_char($to_escape[$c], $escape_char);
            $chars[] = $result;
        }
    }

    return implode("", $chars);


 }


}