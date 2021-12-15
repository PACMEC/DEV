<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Script;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptWitness;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptWitnessInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser;
class ScriptWitnessSerializer
{
    /**
     * @var \BitWasp\Buffertools\Types\VarString
     */
    private $varstring;
    /**
     * @var \BitWasp\Buffertools\Types\VarInt
     */
    private $varint;
    public function __construct()
    {
        $this->varstring = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types::varstring();
        $this->varint = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types::varint();
    }
    /**
     * @param Parser $parser
     * @return ScriptWitnessInterface
     */
    public function fromParser(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser $parser) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptWitnessInterface
    {
        $size = $this->varint->read($parser);
        $entries = [];
        for ($j = 0; $j < $size; $j++) {
            $entries[] = $this->varstring->read($parser);
        }
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptWitness(...$entries);
    }
    /**
     * @param ScriptWitnessInterface $witness
     * @return BufferInterface
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptWitnessInterface $witness) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        $binary = $this->varint->write($witness->count());
        foreach ($witness as $value) {
            $binary .= $this->varstring->write($value);
        }
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer($binary);
    }
}
