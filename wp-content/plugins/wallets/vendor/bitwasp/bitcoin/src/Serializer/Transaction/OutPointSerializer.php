<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\OutPoint;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\OutPointInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser;
class OutPointSerializer implements \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction\OutPointSerializerInterface
{
    /**
     * @var \BitWasp\Buffertools\Types\ByteString
     */
    private $txid;
    /**
     * @var \BitWasp\Buffertools\Types\Uint32
     */
    private $vout;
    public function __construct()
    {
        $this->txid = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types::bytestringle(32);
        $this->vout = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types::uint32le();
    }
    /**
     * @param OutPointInterface $outpoint
     * @return BufferInterface
     * @throws \Exception
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\OutPointInterface $outpoint) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer($this->txid->write($outpoint->getTxId()) . $this->vout->write($outpoint->getVout()));
    }
    /**
     * @param Parser $parser
     * @return OutPointInterface
     * @throws \BitWasp\Buffertools\Exceptions\ParserOutOfRange
     */
    public function fromParser(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser $parser) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\OutPointInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\OutPoint(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer(\strrev($parser->readBytes(32)->getBinary()), 32), \unpack("V", $parser->readBytes(4)->getBinary())[1]);
    }
    /**
     * @param BufferInterface $data
     * @return OutPointInterface
     * @throws \BitWasp\Buffertools\Exceptions\ParserOutOfRange
     */
    public function parse(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $data) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\OutPointInterface
    {
        return $this->fromParser(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser($data));
    }
}
