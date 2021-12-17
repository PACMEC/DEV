<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\Slip132;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HdPrefix\ScriptPrefix;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\ScriptDataFactory;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\KeyToScriptHelper;
class Slip132
{
    /**
     * @var KeyToScriptHelper
     */
    private $helper;
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\KeyToScriptHelper $helper = null)
    {
        $this->helper = $helper ?: new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\KeyToScriptHelper(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getEcAdapter());
    }
    /**
     * @param PrefixRegistry $registry
     * @param ScriptDataFactory $factory
     * @return ScriptPrefix
     * @throws \BitWasp\Bitcoin\Exceptions\InvalidNetworkParameter
     */
    private function loadPrefix(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\Slip132\PrefixRegistry $registry, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\ScriptDataFactory $factory) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HdPrefix\ScriptPrefix
    {
        list($private, $public) = $registry->getPrefixes($factory->getScriptType());
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HdPrefix\ScriptPrefix($factory, $private, $public);
    }
    /**
     * xpub on bitcoin
     * @param PrefixRegistry $registry
     * @return ScriptPrefix
     * @throws \BitWasp\Bitcoin\Exceptions\InvalidNetworkParameter
     */
    public function p2pkh(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\Slip132\PrefixRegistry $registry) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HdPrefix\ScriptPrefix
    {
        return $this->loadPrefix($registry, $this->helper->getP2pkhFactory());
    }
    /**
     * ypub on bitcoin
     * @param PrefixRegistry $registry
     * @return ScriptPrefix
     * @throws \BitWasp\Bitcoin\Exceptions\DisallowedScriptDataFactoryException
     * @throws \BitWasp\Bitcoin\Exceptions\InvalidNetworkParameter
     */
    public function p2shP2wpkh(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\Slip132\PrefixRegistry $registry) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HdPrefix\ScriptPrefix
    {
        return $this->loadPrefix($registry, $this->helper->getP2shFactory($this->helper->getP2wpkhFactory()));
    }
    /**
     * Ypub on bitcoin
     * @param int $m
     * @param int $n
     * @param bool $sortKeys
     * @param PrefixRegistry $registry
     * @return ScriptPrefix
     * @throws \BitWasp\Bitcoin\Exceptions\DisallowedScriptDataFactoryException
     * @throws \BitWasp\Bitcoin\Exceptions\InvalidNetworkParameter
     */
    public function p2shP2wshMultisig(int $m, int $n, bool $sortKeys, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\Slip132\PrefixRegistry $registry) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HdPrefix\ScriptPrefix
    {
        return $this->loadPrefix($registry, $this->helper->getP2shP2wshFactory($this->helper->getMultisigFactory($m, $n, $sortKeys)));
    }
    /**
     * zpub on bitcoin
     * @param PrefixRegistry $registry
     * @return ScriptPrefix
     * @throws \BitWasp\Bitcoin\Exceptions\InvalidNetworkParameter
     */
    public function p2wpkh(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\Slip132\PrefixRegistry $registry) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HdPrefix\ScriptPrefix
    {
        return $this->loadPrefix($registry, $this->helper->getP2wpkhFactory());
    }
    /**
     * Zpub on bitcoin
     * @param int $m
     * @param int $n
     * @param bool $sortKeys
     * @param PrefixRegistry $registry
     * @return ScriptPrefix
     * @throws \BitWasp\Bitcoin\Exceptions\DisallowedScriptDataFactoryException
     * @throws \BitWasp\Bitcoin\Exceptions\InvalidNetworkParameter
     */
    public function p2wshMultisig(int $m, int $n, bool $sortKeys, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\Slip132\PrefixRegistry $registry) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\HdPrefix\ScriptPrefix
    {
        return $this->loadPrefix($registry, $this->helper->getP2wshFactory($this->helper->getMultisigFactory($m, $n, $sortKeys)));
    }
}
