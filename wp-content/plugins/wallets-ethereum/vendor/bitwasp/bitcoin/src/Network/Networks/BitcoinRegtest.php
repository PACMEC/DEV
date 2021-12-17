<?php

namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\Networks;

class BitcoinRegtest extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\Networks\BitcoinTestnet
{
    protected $p2pMagic = "dab5bffa";
}
