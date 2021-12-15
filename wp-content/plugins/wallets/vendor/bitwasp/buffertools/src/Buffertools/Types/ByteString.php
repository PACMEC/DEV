<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser;
class ByteString extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\AbstractType
{
    /**
     * @var int|string
     */
    private $length;
    /**
     * @param int           $length
     * @param int           $byteOrder
     */
    public function __construct(int $length, int $byteOrder = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::BE)
    {
        $this->length = $length;
        parent::__construct($byteOrder);
    }
    /**
     * @param BufferInterface $string
     * @return string
     * @throws \Exception
     */
    public function write($string) : string
    {
        if (!$string instanceof \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface) {
            throw new \InvalidArgumentException('FixedLengthString::write() input must implement BufferInterface');
        }
        $data = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer($string->getBinary(), $this->length);
        if (!$this->isBigEndian()) {
            $data = $data->flip();
        }
        return $data->getBinary();
    }
    /**
     * @param Parser $parser
     * @return BufferInterface
     * @throws \BitWasp\Buffertools\Exceptions\ParserOutOfRange
     */
    public function read(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser $parser) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        $data = $parser->readBytes($this->length);
        if (!$this->isBigEndian()) {
            $data = $data->flip();
        }
        return $data;
    }
}
