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
    public static function build() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\TxBuilder
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\TxBuilder();
    }
    /**
     * @param TransactionInterface $transaction
     * @return TxMutator
     */
    public static function mutate(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface $transaction) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Mutator\TxMutator
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Mutator\TxMutator($transaction);
    }
    /**
     * @param string $hex
     * @return TransactionInterface
     * @throws \Exception
     */
    public static function fromHex(string $hex) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface
    {
        return self::fromBuffer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer::hex($hex));
    }
    /**
     * @param BufferInterface $buffer
     * @return TransactionInterface
     */
    public static function fromBuffer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $buffer) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface
    {
        return (new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction\TransactionSerializer())->parse($buffer);
    }
}
