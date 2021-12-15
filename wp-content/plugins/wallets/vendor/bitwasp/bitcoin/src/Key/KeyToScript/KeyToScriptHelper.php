<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Decorator\P2shP2wshScriptDecorator;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Decorator\P2shScriptDecorator;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Decorator\P2wshScriptDecorator;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Factory\KeyToScriptDataFactory;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Factory\MultisigScriptDataFactory;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Factory\P2pkhScriptDataFactory;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Factory\P2wpkhScriptDataFactory;
class KeyToScriptHelper
{
    /**
     * @var PublicKeySerializerInterface
     */
    private $pubKeySer;
    /**
     * Slip132PrefixRegistry constructor.
     * @param EcAdapterInterface $ecAdapter
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface $ecAdapter)
    {
        $this->pubKeySer = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer::getSerializer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface::class, \true, $ecAdapter);
    }
    /**
     * @return P2pkhScriptDataFactory
     */
    public function getP2pkhFactory() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Factory\P2pkhScriptDataFactory
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Factory\P2pkhScriptDataFactory($this->pubKeySer);
    }
    /**
     * @param int $numSignatures
     * @param int $numKeys
     * @param bool $sortCosignKeys
     * @return MultisigScriptDataFactory
     */
    public function getMultisigFactory(int $numSignatures, int $numKeys, bool $sortCosignKeys) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Factory\MultisigScriptDataFactory
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Factory\MultisigScriptDataFactory($numSignatures, $numKeys, $sortCosignKeys, $this->pubKeySer);
    }
    /**
     * @return P2wpkhScriptDataFactory
     */
    public function getP2wpkhFactory() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Factory\P2wpkhScriptDataFactory
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Factory\P2wpkhScriptDataFactory($this->pubKeySer);
    }
    /**
     * @param KeyToScriptDataFactory $scriptFactory
     * @return ScriptDataFactory
     * @throws \BitWasp\Bitcoin\Exceptions\DisallowedScriptDataFactoryException
     */
    public function getP2shFactory(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Factory\KeyToScriptDataFactory $scriptFactory) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\ScriptDataFactory
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Decorator\P2shScriptDecorator($scriptFactory);
    }
    /**
     * @param KeyToScriptDataFactory $scriptFactory
     * @return ScriptDataFactory
     * @throws \BitWasp\Bitcoin\Exceptions\DisallowedScriptDataFactoryException
     */
    public function getP2wshFactory(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Factory\KeyToScriptDataFactory $scriptFactory) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\ScriptDataFactory
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Decorator\P2wshScriptDecorator($scriptFactory);
    }
    /**
     * @param KeyToScriptDataFactory $scriptFactory
     * @return ScriptDataFactory
     * @throws \BitWasp\Bitcoin\Exceptions\DisallowedScriptDataFactoryException
     */
    public function getP2shP2wshFactory(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Factory\KeyToScriptDataFactory $scriptFactory) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\ScriptDataFactory
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Decorator\P2shP2wshScriptDecorator($scriptFactory);
    }
}
