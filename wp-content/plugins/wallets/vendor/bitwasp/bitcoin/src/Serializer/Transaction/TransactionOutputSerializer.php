<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Script;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutput;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser;
class TransactionOutputSerializer
{
    /**
     * @var \BitWasp\Buffertools\Types\Uint64
     */
    private $uint64le;
    /**
     * @var \BitWasp\Buffertools\Types\VarString
     */
    private $varstring;
    /**
     * @var Opcodes
     */
    private $opcodes;
    /**
     * TransactionOutputSerializer constructor.
     * @param Opcodes|null $opcodes
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes $opcodes = null)
    {
        $this->uint64le = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types::uint64le();
        $this->varstring = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types::varstring();
        $this->opcodes = $opcodes ?: new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes();
    }
    /**
     * @param TransactionOutputInterface $output
     * @return BufferInterface
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface $output) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer($this->uint64le->write($output->getValue()) . $this->varstring->write($output->getScript()->getBuffer()));
    }
    /**
     * @param Parser $parser
     * @return TransactionOutputInterface
     * @throws \BitWasp\Buffertools\Exceptions\ParserOutOfRange
     */
    public function fromParser(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser $parser) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutput((int) $this->uint64le->read($parser), new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Script($this->varstring->read($parser), $this->opcodes));
    }
    /**
     * @param BufferInterface $string
     * @return TransactionOutputInterface
     * @throws \BitWasp\Buffertools\Exceptions\ParserOutOfRange
     */
    public function parse(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $string) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface
    {
        return $this->fromParser(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser($string));
    }
}
