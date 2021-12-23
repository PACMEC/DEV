<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction\TransactionSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\TxBuilder;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Mutator\TxMutator;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class TransactionFactory
{
    /**
     * @return TxBuilder
     */
    public static function build() : TxBuilder
    {
        return new TxBuilder();
    }
    /**
     * @param TransactionInterface $transaction
     * @return TxMutator
     */
    public static function mutate(TransactionInterface $transaction) : TxMutator
    {
        return new TxMutator($transaction);
    }
    /**
     * @param string $hex
     * @return TransactionInterface
     * @throws \Exception
     */
    public static function fromHex(string $hex) : TransactionInterface
    {
        return self::fromBuffer(Buffer::hex($hex));
    }
    /**
     * @param BufferInterface $buffer
     * @return TransactionInterface
     */
    public static function fromBuffer(BufferInterface $buffer) : TransactionInterface
    {
        return (new TransactionSerializer())->parse($buffer);
    }
}
