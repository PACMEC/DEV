<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Decorator;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\ScriptAndSignData;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\WitnessScript;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\SignData;
class P2wshScriptDecorator extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Decorator\ScriptHashDecorator
{
    /**
     * @var array
     */
    protected $allowedScriptTypes = [\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::MULTISIG, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2PKH, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2PK];
    /**
     * @var string
     */
    protected $decorateType = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2WSH;
    /**
     * @param KeyInterface ...$keys
     * @return ScriptAndSignData
     * @throws \BitWasp\Bitcoin\Exceptions\WitnessScriptException
     */
    public function convertKey(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface ...$keys) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\ScriptAndSignData
    {
        $witnessScript = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\WitnessScript($this->scriptDataFactory->convertKey(...$keys)->getScriptPubKey());
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\ScriptAndSignData($witnessScript->getOutputScript(), (new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\SignData())->p2wsh($witnessScript));
    }
}
