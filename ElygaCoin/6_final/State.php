<?php

namespace ElygaCoinFinal;

class State
{
    public $name;
    public $peers;
    public $blockChain;

    /**
     * @param $name
     * @param $peers
     * @param $blockChain
     */
    public function __construct(string $name, ?Blockchain $blockChain, array $peers = [])
    {
        $this->name = $name;
        $this->blockChain = $blockChain;
        $this->peers = $peers;
        $this->save();

    }

    public static function createState(string $name, ?Blockchain $blockChain, array $peers = [])
    {
        if (file_exists(file($name))) {
            return self::load($name);
        } else {
            return new State($name, $blockChain, $peers);
        }
    }

    public function save(): void
    {
        file_put_contents(self::file($this->name), serialize($this));
    }

    public static function load($name): self
    {
        return unserialize(file_get_contents(self::file($name)));
    }

    private static function file($name)
    {
        return __DIR__ . '/data/' . trim($name) . '.json';
    }

    public function update(State $state): void
    {
        if ($this->blockChain) {
            $this->blockChain->update($state->blockChain);//Communicating not first time
        } else {
            $this->blockChain = $state->blockChain;//Initial first time communication
        }
        foreach (array_keys($state->peers) as $peer) {
            $this->peers[$peer] = true;
        }
        $this->save();
    }

    public function reload()
    {
        if ($state = self::load($this->name)) // !!!
        {
            $this->blockChain = $state->blockChain;
            $this->peers = $state->peers;
        }
    }
}
