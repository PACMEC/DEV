<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Collection\CollectionInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\SerializableInterface;
interface ScriptWitnessInterface extends CollectionInterface, SerializableInterface
{
    /**
     * @return BufferInterface[]
     */
    public function all() : array;
    /**
     * @param ScriptWitnessInterface $witness
     * @return bool
     */
    public function equals(ScriptWitnessInterface $witness) : bool;
}
