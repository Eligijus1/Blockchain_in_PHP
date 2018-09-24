<?php

class Pow
{
    public static function hash()
    {
        return hash('sha256', $message);
    }
    
    public static function findNonce($message, $frontString = '000000')
    {
        $nonce = 0;
        while (!self::isValidNonce($message, $nonce, $frontString))
        {
            ++$nonce;
        }
        return $nonce;
    }
    
    public static function isValidNonce($message, $nonce, $frontString = '000000')
    {
        // Difficulty - the number of zeros we want
        return 0 === strpos(hash('sha256',$message . $nonce), $frontString);
    }
}


