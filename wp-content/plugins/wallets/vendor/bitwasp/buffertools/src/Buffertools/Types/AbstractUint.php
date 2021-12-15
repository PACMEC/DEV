<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser;
abstract class AbstractUint extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\AbstractType implements \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\UintInterface
{
    /**
     * @param int $byteOrder
     */
    public function __construct(int $byteOrder = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::BE)
    {
        parent::__construct($byteOrder);
    }
    /**
     * @param int|string $integer - decimal integer
     * @return string
     */
    public function writeBits($integer) : string
    {
        return \str_pad(\gmp_strval(\gmp_init($integer, 10), 2), $this->getBitSize(), '0', \STR_PAD_LEFT);
    }
    /**
     * @param Parser $parser
     * @return int|string
     * @throws \BitWasp\Buffertools\Exceptions\ParserOutOfRange
     * @throws \Exception
     */
    public function readBits(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser $parser)
    {
        $bitSize = $this->getBitSize();
        $bits = \str_pad(\gmp_strval(\gmp_init($parser->readBytes($bitSize / 8)->getHex(), 16), 2), $bitSize, '0', \STR_PAD_LEFT);
        $finalBits = $this->isBigEndian() ? $bits : $this->flipBits($bits);
        $integer = \gmp_strval(\gmp_init($finalBits, 2), 10);
        return $integer;
    }
    /**
     * {@inheritdoc}
     * @see \BitWasp\Buffertools\Types\TypeInterface::write()
     */
    public function write($integer) : string
    {
        return \pack("H*", \str_pad(\gmp_strval(\gmp_init($this->isBigEndian() ? $this->writeBits($integer) : $this->flipBits($this->writeBits($integer)), 2), 16), $this->getBitSize() / 4, '0', \STR_PAD_LEFT));
    }
    /**
     * {@inheritdoc}
     * @see \BitWasp\Buffertools\Types\TypeInterface::read()
     */
    public function read(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser $parser)
    {
        return $this->readBits($parser);
    }
}
