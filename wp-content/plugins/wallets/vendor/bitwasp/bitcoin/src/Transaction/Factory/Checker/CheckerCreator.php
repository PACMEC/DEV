<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\Checker;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\DerSignatureSerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\CheckerBase;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Checker;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Signature\TransactionSignatureSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface;
class CheckerCreator extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\Checker\CheckerCreatorBase
{
    public static function fromEcAdapter(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface $ecAdapter)
    {
        $derSigSer = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer::getSerializer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\DerSignatureSerializerInterface::class);
        $txSigSer = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Signature\TransactionSignatureSerializer($derSigSer);
        $pkSer = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer::getSerializer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface::class);
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\Checker\CheckerCreator($ecAdapter, $txSigSer, $pkSer);
    }
    /**
     * @param TransactionInterface $tx
     * @param int $nInput
     * @param TransactionOutputInterface $txOut
     * @return CheckerBase
     */
    public function create(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInterface $tx, int $nInput, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface $txOut) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\CheckerBase
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Checker($this->ecAdapter, $tx, $nInput, $txOut->getValue(), $this->txSigSerializer, $this->pubKeySerializer);
    }
}
