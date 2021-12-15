<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Script\ScriptWitnessSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Transaction;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser;
class TransactionSerializer implements \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction\TransactionSerializerInterface
{
    const NO_WITNESS = 1;
    /**
     * @var \BitWasp\Buffertools\Types\Int32
     */
    protected $int32le;
    /**
     * @var \BitWasp\Buffertools\Types\Uint32
     */
    protected $uint32le;
    /**
     * @var \BitWasp\Buffertools\Types\VarInt
     */
    protected $varint;
    /**
     * @var TransactionInputSerializer
     */
    protected $inputSerializer;
    /**
     * @var TransactionOutputSerializer
     */
    protected $outputSerializer;
    /**
     * @var ScriptWitnessSerializer
     */
    protected $witnessSerializer;
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction\TransactionInputSerializer $inputSerializer = null, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction\TransactionOutputSerializer $outputSerializer = null, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Script\ScriptWitnessSerializer $witnessSerializer = null)
    {
        $this->int32le = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types::int32le();
        $this->uint32le = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types::uint32le();
        $this->varint = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Types::varint();
        if ($inputSerializer === null || $outputSerializer === null) {
            $opcodes = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes();
            if (!$inputSerializer) {
                $inputSerializer = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction\TransactionInputSerializer(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction\OutPointSerializer(), $opcodes);
            }
            if (!$outputSerializer) {
                $outputSerializer = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction\TransactionOutputSerializer($opcodes);
            }
        }
        if (!$witnessSerializer) {
            $witnessSerializer = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Script\ScriptWitnessSerializer();
        }
        $this->inputSerializer = $inputSerializer;
        $this->outputSerializer = $outputSerializer;
        $this->witnessSerializer = $witnessSerializer;
    }
    /**
     * @param Parser $parser
     * @return TransactionInterface
     */
    public function fromParser(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser $parser) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface
    {
        $version = (int) $this->int32le->read($parser);
        $vin = [];
        $vinCount = $this->varint->read($parser);
        for ($i = 0; $i < $vinCount; $i++) {
            $vin[] = $this->inputSerializer->fromParser($parser);
        }
        $vout = [];
        $flags = 0;
        if (\count($vin) === 0) {
            $flags = (int) $this->varint->read($parser);
            if ($flags !== 0) {
                $vinCount = $this->varint->read($parser);
                for ($i = 0; $i < $vinCount; $i++) {
                    $vin[] = $this->inputSerializer->fromParser($parser);
                }
                $voutCount = $this->varint->read($parser);
                for ($i = 0; $i < $voutCount; $i++) {
                    $vout[] = $this->outputSerializer->fromParser($parser);
                }
            }
        } else {
            $voutCount = $this->varint->read($parser);
            for ($i = 0; $i < $voutCount; $i++) {
                $vout[] = $this->outputSerializer->fromParser($parser);
            }
        }
        $vwit = [];
        if ($flags & 1) {
            $flags ^= 1;
            $witCount = \count($vin);
            for ($i = 0; $i < $witCount; $i++) {
                $vwit[] = $this->witnessSerializer->fromParser($parser);
            }
        }
        if ($flags) {
            throw new \RuntimeException('Flags byte was 0');
        }
        $lockTime = (int) $this->uint32le->read($parser);
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Transaction($version, $vin, $vout, $vwit, $lockTime);
    }
    /**
     * @param BufferInterface $data
     * @return TransactionInterface
     */
    public function parse(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $data) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface
    {
        return $this->fromParser(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser($data));
    }
    /**
     * @param TransactionInterface $transaction
     * @param int $opt
     * @return BufferInterface
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface $transaction, int $opt = 0) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        $parser = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser();
        $parser->appendBinary($this->int32le->write($transaction->getVersion()));
        $flags = 0;
        $allowWitness = !($opt & self::NO_WITNESS);
        if ($allowWitness && $transaction->hasWitness()) {
            $flags |= 1;
        }
        if ($flags) {
            $parser->appendBinary(\pack("CC", 0, $flags));
        }
        $parser->appendBinary($this->varint->write(\count($transaction->getInputs())));
        foreach ($transaction->getInputs() as $input) {
            $parser->appendBuffer($this->inputSerializer->serialize($input));
        }
        $parser->appendBinary($this->varint->write(\count($transaction->getOutputs())));
        foreach ($transaction->getOutputs() as $output) {
            $parser->appendBuffer($this->outputSerializer->serialize($output));
        }
        if ($flags & 1) {
            foreach ($transaction->getWitnesses() as $witness) {
                $parser->appendBuffer($this->witnessSerializer->serialize($witness));
            }
        }
        $parser->appendBinary($this->uint32le->write($transaction->getLockTime()));
        return $parser->getBuffer();
    }
}
