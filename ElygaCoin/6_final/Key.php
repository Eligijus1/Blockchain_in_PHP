<?php

declare(strict_types=1);

namespace ElygaCoinFinal;

class Key
{
    public $name;
    public $privateKey;
    public $publicKey;

    public function __construct(string $name)
    {
        $this->name = $name;
        [$this->privateKey, $this->publicKey] = Pki::generateKeyPair();
        $this->save();
    }

    private function save(): void
    {
        file_put_contents(self::file($this->name), serialize($this));
    }

    private static function file($name)
    {
        return __DIR__ . '/data/' . trim($name) . '.key';
    }

    public static function load($name): self
    {
        return unserialize(file_get_contents(self::file($name)));
    }
}
