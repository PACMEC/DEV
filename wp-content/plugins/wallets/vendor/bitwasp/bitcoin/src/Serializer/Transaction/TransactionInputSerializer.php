<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Script;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInput;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInputInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser;
class TransactionInputSerializer
{
    /**
     * @var OutPointSerializerInterface
     */
    private $outpointSerializer;
    /**
     * @var \BitWasp\Buffertools\Types\VarString
     */
    private $varstring;
    /**
     * @var \BitWasp\Buffertools\Types\Uint32
     */
    private $uint32le;
    /**
     * @var Opcodes
     */
    private $opcodes;
    /**
     * TransactionInputSerializer constructor.
     * @param OutPointSerializerInterface $outPointSerializer
     * @param Opcodes|null $opcodes
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction\OutPointSerializerInterface $outPointSerializer, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes $opcodes = null)
    {
        $this->outpointSerializer = $outPointSerializer;
        $this->varstring = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types::varstring();
        $this->uint32le = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types::uint32le();
        $this->opcodes = $opcodes ?: new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes();
    }
    /**
     * @param TransactionInputInterface $input
     * @return BufferInterface
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInputInterface $input) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer($this->outpointSerializer->serialize($input->getOutPoint())->getBinary() . $this->varstring->write($input->getScript()->getBuffer()) . $this->uint32le->write($input->getSequence()));
    }
    /**
     * @param Parser $parser
     * @return TransactionInputInterface
     * @throws \BitWasp\Buffertools\Exceptions\ParserOutOfRange
     * @throws \Exception
     */
    public function fromParser(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser $parser) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInputInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInput($this->outpointSerializer->fromParser($parser), new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Script($this->varstring->read($parser), $this->opcodes), (int) $this->uint32le->read($parser));
    }
    /**
     * @param BufferInterface $string
     * @return TransactionInputInterface
     * @throws \BitWasp\Buffertools\Exceptions\ParserOutOfRange
     * @throws \Exception
     */
    public function parse(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $string) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInputInterface
    {
        return $this->fromParser(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser($string));
    }
}
