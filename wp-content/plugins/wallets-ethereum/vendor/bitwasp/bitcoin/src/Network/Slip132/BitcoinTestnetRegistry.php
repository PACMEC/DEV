<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\Slip132;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\Slip132\PrefixRegistry;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType;
class BitcoinTestnetRegistry extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\Slip132\PrefixRegistry
{
    public function __construct()
    {
        $map = [];
        foreach ([
            // private, public
            [
                ["04358394", "043587cf"],
                /* xpub */
                [\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2PKH],
            ],
            [
                ["04358394", "043587cf"],
                /* xpub */
                [\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2SH, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2PKH],
            ],
            [
                ["044a4e28", "044a5262"],
                /* ypub */
                [\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2SH, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2WKH],
            ],
            [
                ["045f18bc", "045f1cf6"],
                /* zpub */
                [\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2WKH],
            ],
            [
                ["02575048", "02575483"],
                /* Zpub */
                [\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2WSH, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2PKH],
            ],
        ] as $row) {
            list($prefixList, $scriptType) = $row;
            $type = \implode("|", $scriptType);
            $map[$type] = $prefixList;
        }
        parent::__construct($map);
    }
}
