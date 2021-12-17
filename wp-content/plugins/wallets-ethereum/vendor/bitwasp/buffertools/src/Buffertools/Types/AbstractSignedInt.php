<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser;
abstract class AbstractSignedInt extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\AbstractType implements \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\SignedIntInterface
{
    /**
     * @param int $byteOrder
     */
    public function __construct(int $byteOrder = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::BE)
    {
        parent::__construct($byteOrder);
    }
    /**
     * @param int|string $integer
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
        $byteSize = $bitSize / 8;
        $bytes = $parser->readBytes($byteSize);
        $bytes = $this->isBigEndian() ? $bytes : $bytes->flip();
        $chars = $bytes->getBinary();
        $offsetIndex = 0;
        $isNegative = (\ord($chars[$offsetIndex]) & 0x80) != 0x0;
        $number = \gmp_init(\ord($chars[$offsetIndex++]) & 0x7f, 10);
        for ($i = 0; $i < $byteSize - 1; $i++) {
            $number = \gmp_or(\gmp_mul($number, 0x100), \ord($chars[$offsetIndex++]));
        }
        if ($isNegative) {
            $number = \gmp_sub($number, \gmp_pow(2, $bitSize - 1));
        }
        return \gmp_strval($number, 10);
    }
    /**
     * {@inheritdoc}
     * @see \BitWasp\Buffertools\Types\TypeInterface::write()
     */
    public function write($integer) : string
    {
        $bitSize = $this->getBitSize();
        if (\gmp_sign($integer) < 0) {
            $integer = \gmp_add($integer, \gmp_sub(\gmp_pow(2, $bitSize), 1));
            $integer = \gmp_add($integer, 1);
        }
        $binary = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer::hex(\str_pad(\gmp_strval($integer, 16), $bitSize / 4, '0', \STR_PAD_LEFT), $bitSize / 8);
        if (!$this->isBigEndian()) {
            $binary = $binary->flip();
        }
        return $binary->getBinary();
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
