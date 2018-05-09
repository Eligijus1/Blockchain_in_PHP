<?php

namespace Blockchain\Model;

/**
 * Class Block
 *
 * Data that will be written to the blockchain
 *
 * @package Blockchain\Model
 */
class Block
{
    /**
     * The position of the data record in the blockchain.
     *
     * @var int
     */
	public $index;
	
    /**
     * Is automatically determined and is the time the data is written.
     *
     * @var string
     */
	public $timestamp;
	
    /**
     * Beats per minute - pulse rate.
     *
     * @var int
     */
	public $bpm;
	
    /**
     * SHA256 identifier representing this data record.
     *
     * @var string
     */
	public $hash;
	
    /**
     * SHA256 identifier of the previous record in the chain.
     *
     * @var string
     */
	public $prevHash;
}

