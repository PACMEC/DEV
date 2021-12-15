<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptFactory;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class ScriptHashAddress extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\Base58Address
{
    /**
     * ScriptHashAddress constructor.
     * @param BufferInterface $data
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $data)
    {
        if ($data->getSize() !== 20) {
            throw new \RuntimeException("P2SH address hash should be 20 bytes");
        }
        parent::__construct($data);
    }
    /**
     * @param NetworkInterface $network
     * @return string
     */
    public function getPrefixByte(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network = null) : string
    {
        $network = $network ?: \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getNetwork();
        return \pack("H*", $network->getP2shByte());
    }
    /**
     * @return ScriptInterface
     */
    public function getScriptPubKey() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface
    {
        return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptFactory::scriptPubKey()->payToScriptHash($this->getHash());
    }
}
