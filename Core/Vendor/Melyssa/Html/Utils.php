<?php

namespace Melyssa\Html;

class Utils
{

    public function priceNumber($value)
    {
        if (is_numeric($value) && $value > 0) {
            $val = substr($value, 0, strlen($value) - 2);
            $dec = substr($value, -2, 2);
            if (strlen($val) > 3) {
                $mil = substr($val, -3, 3);
                $val = substr($val, 0, strlen($val) - 3) . '.' . $mil;
            }
            if (strlen($val) > 7) {
                $mill = substr($val, -7, 7);
                $val = substr($val, 0, strlen($val) - 7) . '.' . $mill;
            }
            return $val . ',' . $dec;
        } else {
            return 'Consulte';
        }
    }
    
    public function link($text, $anchor, $attributes = array())
    {
        $tag = '<a href="' . $anchor . '"';
        foreach ($attributes as $attribute => $value){
            $tag .= ' ' . $attribute . '="' . $value . '"';
        }
        $tag .= '>' . $text;
        $tag .= '</a>';
        
        return $tag;
    }
    
    public function phoneNumber($value)
    {
        $value = str_replace(array('(', ')', '-', ' ', '.', ','), '', $value);
        $areaCode = substr($value, 0, 2);
        // Criando o telefone do jeito certo:
        $phoneNumber = substr($value, 2);
        if(strlen(substr($value, 2)) === 8){
            $firstNumbers = substr($phoneNumber, 0, 4);
            $lastNumbers = substr($phoneNumber, 4);
            $phoneNumber = $firstNumbers . '-' . $lastNumbers;
        }elseif(strlen(substr($value, 2)) === 9){
            $firstNumbers = substr($phoneNumber, 0, 5);
            $lastNumbers = substr($phoneNumber, 5);
            $phoneNumber = $firstNumbers . '-' . $lastNumbers;
        }
        
        return '(' . $areaCode . ') ' . $phoneNumber;
    }
    
    public function cleanupForLink($value)
    {
        $newValue = str_replace(array(' ', '_', '.', ',', '/', '\\'), '-', $value);
        return strtolower($newValue);
    }
    
    public function parseDate($value)
    {
        $date = explode(' ', $value);
        return $date[0] . ' às ' . $date[1];
    }
    
    public function limitWords($sentence, $words)
    {
        $newSentence = explode(' ', $sentence);
        $finalSentence = '';
        for($i=0;$i<=$words-1;$i++){
            $finalSentence .= $newSentence[$i] . ' ';
        }
        
        return $finalSentence;
    }
    
    public function addPoint($value){
        
    }

}
