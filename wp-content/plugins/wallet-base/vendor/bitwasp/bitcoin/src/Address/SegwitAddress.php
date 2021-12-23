<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\WitnessProgram;
class SegwitAddress extends Address implements Bech32AddressInterface
{
    /**
     * @var WitnessProgram
     */
    protected $witnessProgram;
    /**
     * SegwitAddress constructor.
     * @param WitnessProgram $witnessProgram
     */
    public function __construct(WitnessProgram $witnessProgram)
    {
        $this->witnessProgram = $witnessProgram;
        parent::__construct($witnessProgram->getProgram());
    }
    /**
     * @param NetworkInterface|null $network
     * @return string
     */
    public function getHRP(NetworkInterface $network = null) : string
    {
        $network = $network ?: Bitcoin::getNetwork();
        return $network->getSegwitBech32Prefix();
    }
    /**
     * @return WitnessProgram
     */
    public function getWitnessProgram() : WitnessProgram
    {
        return $this->witnessProgram;
    }
    /**
     * @return ScriptInterface
     */
    public function getScriptPubKey() : ScriptInterface
    {
        return $this->witnessProgram->getScript();
    }
    /**
     * @param NetworkInterface|null $network
     * @return string
     */
    public function getAddress(NetworkInterface $network = null) : string
    {
        $network = $network ?: Bitcoin::getNetwork();
        return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bech32\encodeSegwit($network->getSegwitBech32Prefix(), $this->witnessProgram->getVersion(), $this->witnessProgram->getProgram()->getBinary());
    }
}
