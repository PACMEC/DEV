<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface;
abstract class BaseAddressCreator
{
    /**
     * @param string $strAddress
     * @param NetworkInterface|null $network
     * @return Address
     */
    public abstract function fromString(string $strAddress, NetworkInterface $network = null) : Address;
    /**
     * @param ScriptInterface $script
     * @return Address
     */
    public abstract function fromOutputScript(ScriptInterface $script) : Address;
}
