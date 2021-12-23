<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface;
abstract class ScriptDataFactory
{
    /**
     * @param KeyInterface ...$keys
     * @return ScriptAndSignData
     */
    public abstract function convertKey(KeyInterface ...$keys) : ScriptAndSignData;
    /**
     * @return string
     */
    public abstract function getScriptType() : string;
}
