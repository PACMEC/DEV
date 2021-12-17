<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Decorator;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\ScriptAndSignData;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\P2shScript;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\WitnessScript;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\SignData;
class P2shP2wshScriptDecorator extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Decorator\ScriptHashDecorator
{
    /**
     * @var string[]
     */
    protected $allowedScriptTypes = [\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::MULTISIG, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2PKH, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2PK];
    /**
     * @var string
     */
    protected $decorateType = "scripthash|witness_v0_scripthash";
    /**
     * @param KeyInterface ...$keys
     * @return ScriptAndSignData
     * @throws \BitWasp\Bitcoin\Exceptions\P2shScriptException
     * @throws \BitWasp\Bitcoin\Exceptions\WitnessScriptException
     */
    public function convertKey(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface ...$keys) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\ScriptAndSignData
    {
        $witnessScript = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\WitnessScript($this->scriptDataFactory->convertKey(...$keys)->getScriptPubKey());
        $redeemScript = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\P2shScript($witnessScript);
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\ScriptAndSignData($redeemScript->getOutputScript(), (new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\SignData())->p2sh($redeemScript)->p2wsh($witnessScript));
    }
}
