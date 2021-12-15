<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Factory;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\ScriptAndSignData;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptFactory;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\SignData;
class MultisigScriptDataFactory extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Factory\KeyToScriptDataFactory
{
    /**
     * @var int
     */
    private $numSigners;
    /**
     * @var int
     */
    private $numKeys;
    /**
     * @var bool
     */
    private $sortKeys;
    public function __construct(int $numSigners, int $numKeys, bool $sortKeys, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface $pubKeySerializer = null)
    {
        $this->numSigners = $numSigners;
        $this->numKeys = $numKeys;
        $this->sortKeys = $sortKeys;
        parent::__construct($pubKeySerializer);
    }
    /**
     * @return string
     */
    public function getScriptType() : string
    {
        return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::MULTISIG;
    }
    /**
     * @param PublicKeyInterface ...$keys
     * @return ScriptAndSignData
     */
    protected function convertKeyToScriptData(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface ...$keys) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\ScriptAndSignData
    {
        if (\count($keys) !== $this->numKeys) {
            throw new \InvalidArgumentException("Incorrect number of keys");
        }
        $keyBuffers = [];
        for ($i = 0; $i < $this->numKeys; $i++) {
            $keyBuffers[] = $this->pubKeySerializer->serialize($keys[$i]);
        }
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\ScriptAndSignData(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptFactory::scriptPubKey()->multisigKeyBuffers($this->numSigners, $keyBuffers, $this->sortKeys), new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\SignData());
    }
}
