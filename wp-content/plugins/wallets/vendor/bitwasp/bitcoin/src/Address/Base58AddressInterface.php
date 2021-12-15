<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface;
interface Base58AddressInterface extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\AddressInterface
{
    /**
     * @param NetworkInterface $network
     * @return string
     */
    public function getPrefixByte(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network = null) : string;
}
