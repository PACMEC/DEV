<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Key\Deterministic\Slip132;

use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Key\Deterministic\HdPrefix\ScriptPrefix;
use BitWasp\Bitcoin\Key\KeyToScript\ScriptDataFactory;
use BitWasp\Bitcoin\Key\KeyToScript\KeyToScriptHelper;

class Slip132
{
    /**
     * @var KeyToScriptHelper
     */
    private $helper;

    public function __construct(KeyToScriptHelper $helper = null)
    {
        $this->helper = $helper ?: new KeyToScriptHelper(Bitcoin::getEcAdapter());
    }

    /**
     * @param PrefixRegistry $registry
     * @param ScriptDataFactory $factory
     * @return ScriptPrefix
     * @throws \BitWasp\Bitcoin\Exceptions\InvalidNetworkParameter
     */
    private function loadPrefix(PrefixRegistry $registry, ScriptDataFactory $factory): ScriptPrefix
    {
        list ($private, $public) = $registry->getPrefixes($factory->getScriptType());
        return new ScriptPrefix($factory, $private, $public);
    }

    /**
     * xpub on bitcoin
     * @param PrefixRegistry $registry
     * @return ScriptPrefix
     * @throws \BitWasp\Bitcoin\Exceptions\InvalidNetworkParameter
     */
    public function p2pkh(PrefixRegistry $registry): ScriptPrefix
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
    public function p2shP2wpkh(PrefixRegistry $registry): ScriptPrefix
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
    public function p2shP2wshMultisig(int $m, int $n, bool $sortKeys, PrefixRegistry $registry): ScriptPrefix
    {
        return $this->loadPrefix($registry, $this->helper->getP2shP2wshFactory($this->helper->getMultisigFactory($m, $n, $sortKeys)));
    }

    /**
     * zpub on bitcoin
     * @param PrefixRegistry $registry
     * @return ScriptPrefix
     * @throws \BitWasp\Bitcoin\Exceptions\InvalidNetworkParameter
     */
    public function p2wpkh(PrefixRegistry $registry): ScriptPrefix
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
    public function p2wshMultisig(int $m, int $n, bool $sortKeys, PrefixRegistry $registry): ScriptPrefix
    {
        return $this->loadPrefix($registry, $this->helper->getP2wshFactory($this->helper->getMultisigFactory($m, $n, $sortKeys)));
    }

	// The below two methods are added here manually for compatibility with HD wallet addon 0.10.8 and below
	// TODO: Remove the two methods below once workaround for fatal error on hd wallet 0.10.8 and below is implemented.
	public function p2shP2wshP2pkh( PrefixRegistry $registry ) {
		try {
			return $this->p2shP2wshMultisig( 0, 0, false, $registry );
		} catch ( \BitWasp\Bitcoin\Exceptions\DisallowedScriptDataFactoryException | \BitWasp\Bitcoin\Exceptions\InvalidNetworkParameter $e ) {
			return $this->p2shP2wpkh( $registry );
		}
	}
	public function p2wshP2pkh( PrefixRegistry $registry ) {
		try {
			return $this->p2wshMultisig( 0, 0, false, $registry );
		} catch ( \BitWasp\Bitcoin\Exceptions\DisallowedScriptDataFactoryException | \BitWasp\Bitcoin\Exceptions\InvalidNetworkParameter $e ) {
			return $this->p2wpkh( $registry );
		}
	}
}
