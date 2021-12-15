<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Base58;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\UnrecognizedAddressException;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Classifier\OutputClassifier;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\P2shScript;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\WitnessProgram;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\WitnessScript;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class AddressCreator extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\BaseAddressCreator
{
    /**
     * @param string $strAddress
     * @param NetworkInterface $network
     * @return Base58Address|null
     */
    protected function readBase58Address(string $strAddress, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network)
    {
        try {
            $data = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Base58::decodeCheck($strAddress);
            $prefixByte = $data->slice(0, $network->getP2shPrefixLength())->getHex();
            if ($prefixByte === $network->getP2shByte()) {
                return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\ScriptHashAddress($data->slice(1));
            } else {
                if ($prefixByte === $network->getAddressByte()) {
                    return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\PayToPubKeyHashAddress($data->slice($network->getAddressPrefixLength()));
                }
            }
        } catch (\Exception $e) {
            // Just return null
        }
        return null;
    }
    /**
     * @param string $strAddress
     * @param NetworkInterface $network
     * @return SegwitAddress|null
     */
    protected function readSegwitAddress(string $strAddress, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network)
    {
        try {
            list($version, $program) = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bech32\decodeSegwit($network->getSegwitBech32Prefix(), $strAddress);
            if (0 === $version) {
                $wp = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\WitnessProgram::v0(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer($program));
            } else {
                $wp = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\WitnessProgram($version, new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer($program));
            }
            return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\SegwitAddress($wp);
        } catch (\Exception $e) {
            // Just return null
        }
        return null;
    }
    /**
     * @param ScriptInterface $outputScript
     * @return Address
     */
    public function fromOutputScript(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $outputScript) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\Address
    {
        if ($outputScript instanceof \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\P2shScript || $outputScript instanceof \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\WitnessScript) {
            throw new \RuntimeException("P2shScript & WitnessScript's are not accepted by fromOutputScript");
        }
        $wp = null;
        if ($outputScript->isWitness($wp)) {
            /** @var WitnessProgram $wp */
            return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\SegwitAddress($wp);
        }
        $decode = (new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Classifier\OutputClassifier())->decode($outputScript);
        switch ($decode->getType()) {
            case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2PKH:
                /** @var BufferInterface $solution */
                return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\PayToPubKeyHashAddress($decode->getSolution());
            case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptType::P2SH:
                /** @var BufferInterface $solution */
                return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\ScriptHashAddress($decode->getSolution());
            default:
                throw new \RuntimeException('Script type is not associated with an address');
        }
    }
    /**
     * @param string $strAddress
     * @param NetworkInterface|null $network
     * @return Address
     * @throws UnrecognizedAddressException
     */
    public function fromString(string $strAddress, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network = null) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\Address
    {
        $network = $network ?: \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getNetwork();
        if ($base58Address = $this->readBase58Address($strAddress, $network)) {
            return $base58Address;
        }
        if ($bech32Address = $this->readSegwitAddress($strAddress, $network)) {
            return $bech32Address;
        }
        throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\UnrecognizedAddressException();
    }
}
