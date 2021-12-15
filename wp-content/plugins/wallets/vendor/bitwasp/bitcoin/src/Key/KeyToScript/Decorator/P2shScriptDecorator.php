<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Decorator;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\ScriptAndSignData;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\P2shScript;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\SignData;
class P2shScriptDecorator extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Decorator\ScriptHashDecorator
{
    /**
     * @var array
     */
    protected $allowedScriptTypes = [\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::MULTISIG, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2PKH, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2PK, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2WKH];
    /**
     * @var string
     */
    protected $decorateType = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2SH;
    /**
     * @param KeyInterface ...$keys
     * @return ScriptAndSignData
     * @throws \BitWasp\Bitcoin\Exceptions\P2shScriptException
     */
    public function convertKey(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface ...$keys) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\ScriptAndSignData
    {
        $redeemScript = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\P2shScript($this->scriptDataFactory->convertKey(...$keys)->getScriptPubKey());
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\ScriptAndSignData($redeemScript->getOutputScript(), (new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\SignData())->p2sh($redeemScript));
    }
}
