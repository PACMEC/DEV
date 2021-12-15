<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\Slip132;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\Slip132\PrefixRegistry;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType;
class BitcoinRegistry extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\Slip132\PrefixRegistry
{
    protected static $table = [[
        ["0488ade4", "0488b21e"],
        /* xpub */
        [\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2PKH],
    ], [
        ["0488ade4", "0488b21e"],
        /* xpub */
        [\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2SH, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::MULTISIG],
    ], [
        ["049d7878", "049d7cb2"],
        /* ypub */
        [\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2SH, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2WKH],
    ], [
        ["0295b005", "0295b43f"],
        /* Ypub */
        [\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2SH, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2WSH, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::MULTISIG],
    ], [
        ["04b2430c", "04b24746"],
        /* zpub */
        [\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2WKH],
    ], [
        ["02aa7a99", "02aa7ed3"],
        /* Zpub */
        [\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2WSH, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::MULTISIG],
    ]];
    public function __construct()
    {
        $map = [];
        foreach (static::$table as list($prefixList, $scriptType)) {
            $type = \implode("|", $scriptType);
            $map[$type] = $prefixList;
        }
        parent::__construct($map);
    }
}
