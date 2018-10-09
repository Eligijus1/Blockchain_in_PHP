<?php

namespace ElygaCoinFinal;

class Transaction
{
    public $from;
    public $to;
    public $amount;
    public $signature;

    /**
     * @param string|null $from       Public key of money sender. NOTE: nullable because in "Generic transaction" no
     *                                sender.
     * @param string      $to         Public key of money receiver.
     * @param int         $amount     Amount of coins to send.
     * @param string      $privateKey Sender private key to sign transaction.
     */
    public function __construct(?string $from, string $to, int $amount, string $privateKey)
    {
        $this->from = $from;
        $this->to = $to;
        $this->amount = $amount;
        $this->signature = Pki::encrypt($this->message(), $privateKey);
    }

    public function message()
    {
        return Pow::hash($this->from . $this->to . $this->amount);
    }

    public function __toString()
    {
        return ($this->from ? substr($this->from, 72, 7) : 'NONE' . '->' . substr($this->to, 72,
                7) . ': ' . $this->amount);
    }

    public function isValid()
    {
        return (!$this->from || Pki::isValid($this->message(), $this->signature, $this->from));
    }
}
