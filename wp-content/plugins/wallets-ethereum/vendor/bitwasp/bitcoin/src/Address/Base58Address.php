<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Base58;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
abstract class Base58Address extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\Address implements \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\Base58AddressInterface
{
    /**
     * @param NetworkInterface|null $network
     * @return string
     */
    public function getAddress(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network = null) : string
    {
        $network = $network ?: \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getNetwork();
        $payload = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer($this->getPrefixByte($network) . $this->getHash()->getBinary());
        return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Base58::encodeCheck($payload);
    }
}
