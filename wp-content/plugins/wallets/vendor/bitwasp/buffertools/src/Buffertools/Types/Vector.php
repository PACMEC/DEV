<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser;
class Vector extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\AbstractType
{
    /**
     * @var VarInt
     */
    private $varint;
    /**
     * @var callable
     */
    private $readFxn;
    /**
     * @param VarInt   $varInt
     * @param callable $readFunction
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\VarInt $varInt, callable $readFunction)
    {
        $this->varint = $varInt;
        $this->readFxn = $readFunction;
        parent::__construct($varInt->getByteOrder());
    }
    /**
     * {@inheritdoc}
     * @see \BitWasp\Buffertools\Types\TypeInterface::write()
     */
    public function write($items) : string
    {
        if (\false === \is_array($items)) {
            throw new \InvalidArgumentException('Vector::write() must be supplied with an array');
        }
        $parser = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser();
        return $parser->writeArray($items)->getBuffer()->getBinary();
    }
    /**
     * {@inheritdoc}
     * @see \BitWasp\Buffertools\Types\TypeInterface::read()
     * @param Parser $parser
     * @return array
     * @throws \Exception
     */
    public function read(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser $parser) : array
    {
        $results = array();
        $handler = $this->readFxn;
        $varInt = $this->varint->read($parser);
        for ($i = 0; $i < $varInt; $i++) {
            $results[] = $handler($parser);
        }
        return $results;
    }
}
