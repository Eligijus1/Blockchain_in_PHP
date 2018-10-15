<?php

namespace ElygaCoinFinal;

class Pow
{
    public static function hash($message)
    {
        return hash('sha256', $message);
    }
    
    public static function findNonce($message, $frontString = '00000')
    {
        $nonce = 0;
        while (!self::isValidNonce($message, $nonce, $frontString))
        {
            ++$nonce;
        }
        return $nonce;
    }
    
    public static function isValidNonce($message, $nonce, $frontString = '00000')
    {
        // Difficulty - the number of zeros we want
        return 0 === strpos(hash('sha256',$message . $nonce), $frontString);
    }
}


