<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\SegwitAddress;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\WitnessScriptException;
class WitnessScript extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Script
{
    /**
     * @var ScriptInterface
     */
    private $outputScript;
    /**
     * @var \BitWasp\Buffertools\BufferInterface
     */
    protected $witnessScriptHash;
    /**
     * @var WitnessProgram|null
     */
    private $witnessProgram;
    /**
     * @var SegwitAddress
     */
    private $address;
    /**
     * WitnessScript constructor.
     * @param ScriptInterface $script
     * @param Opcodes|null $opcodes
     * @throws WitnessScriptException
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes $opcodes = null)
    {
        if ($script instanceof self) {
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\WitnessScriptException("Cannot nest V0 P2WSH scripts.");
        } else {
            if ($script instanceof \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\P2shScript) {
                throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\WitnessScriptException("Cannot embed a P2SH script in a V0 P2WSH script.");
            }
        }
        parent::__construct($script->getBuffer(), $opcodes);
        $this->witnessScriptHash = $script->getWitnessScriptHash();
        $this->outputScript = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptFactory::scriptPubKey()->p2wsh($this->witnessScriptHash);
    }
    /**
     * @return WitnessProgram
     */
    public function getWitnessProgram() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\WitnessProgram
    {
        if (null === $this->witnessProgram) {
            $this->witnessProgram = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\WitnessProgram::v0($this->witnessScriptHash);
        }
        return $this->witnessProgram;
    }
    /**
     * @return SegwitAddress
     */
    public function getAddress() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\SegwitAddress
    {
        if (null === $this->address) {
            $this->address = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\SegwitAddress($this->getWitnessProgram());
        }
        return $this->address;
    }
    /**
     * @return ScriptInterface
     */
    public function getOutputScript() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface
    {
        return $this->getWitnessProgram()->getScript();
    }
}
