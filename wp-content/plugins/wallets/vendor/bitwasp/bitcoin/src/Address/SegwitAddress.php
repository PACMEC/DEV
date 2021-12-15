<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\WitnessProgram;
class SegwitAddress extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\Address implements \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\Bech32AddressInterface
{
    /**
     * @var WitnessProgram
     */
    protected $witnessProgram;
    /**
     * SegwitAddress constructor.
     * @param WitnessProgram $witnessProgram
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\WitnessProgram $witnessProgram)
    {
        $this->witnessProgram = $witnessProgram;
        parent::__construct($witnessProgram->getProgram());
    }
    /**
     * @param NetworkInterface|null $network
     * @return string
     */
    public function getHRP(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network = null) : string
    {
        $network = $network ?: \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getNetwork();
        return $network->getSegwitBech32Prefix();
    }
    /**
     * @return WitnessProgram
     */
    public function getWitnessProgram() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\WitnessProgram
    {
        return $this->witnessProgram;
    }
    /**
     * @return ScriptInterface
     */
    public function getScriptPubKey() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface
    {
        return $this->witnessProgram->getScript();
    }
    /**
     * @param NetworkInterface|null $network
     * @return string
     */
    public function getAddress(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface $network = null) : string
    {
        $network = $network ?: \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getNetwork();
        return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bech32\encodeSegwit($network->getSegwitBech32Prefix(), $this->witnessProgram->getVersion(), $this->witnessProgram->getProgram()->getBinary());
    }
}
