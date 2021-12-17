<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Exceptions\ParserOutOfRange;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser;
class VarString extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\AbstractType
{
    /**
     * @var VarInt
     */
    private $varint;
    /**
     * @param VarInt $varInt
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\VarInt $varInt)
    {
        $this->varint = $varInt;
        parent::__construct($varInt->getByteOrder());
    }
    /**
     * {@inheritdoc}
     * @see \BitWasp\Buffertools\Types\TypeInterface::write()
     */
    public function write($buffer) : string
    {
        if (!$buffer instanceof \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface) {
            throw new \InvalidArgumentException('Must provide a buffer');
        }
        $binary = $this->varint->write($buffer->getSize()) . $buffer->getBinary();
        return $binary;
    }
    /**
     * {@inheritdoc}
     * @see \BitWasp\Buffertools\Types\TypeInterface::write()
     * @param Parser $parser
     * @return \BitWasp\Buffertools\BufferInterface
     * @throws \BitWasp\Buffertools\Exceptions\ParserOutOfRange
     * @throws \Exception
     */
    public function read(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser $parser) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        $length = $this->varint->read($parser);
        if ($length > $parser->getSize() - $parser->getPosition()) {
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Exceptions\ParserOutOfRange("Insufficient data remaining for VarString");
        }
        if (\gmp_cmp(\gmp_init($length, 10), \gmp_init(0, 10)) == 0) {
            return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer();
        }
        return $parser->readBytes((int) $length);
    }
}
