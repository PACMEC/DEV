<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface;
interface Bech32AddressInterface extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\AddressInterface
{
    /**
     * @param NetworkInterface $network
     * @return string
     */
    public function getHRP(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network = null) : string;
}
