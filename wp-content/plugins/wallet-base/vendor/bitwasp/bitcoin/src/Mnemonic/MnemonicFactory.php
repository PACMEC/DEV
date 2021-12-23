<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Mnemonic;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Mnemonic\Bip39\Bip39Mnemonic;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Mnemonic\Bip39\Bip39WordListInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Mnemonic\Electrum\ElectrumMnemonic;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Mnemonic\Electrum\ElectrumWordListInterface;
class MnemonicFactory
{
    /**
     * @param ElectrumWordListInterface $wordList
     * @param EcAdapterInterface $ecAdapter
     * @return ElectrumMnemonic
     */
    public static function electrum(ElectrumWordListInterface $wordList = null, EcAdapterInterface $ecAdapter = null) : ElectrumMnemonic
    {
        return new ElectrumMnemonic($ecAdapter ?: Bitcoin::getEcAdapter(), $wordList ?: new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Mnemonic\Electrum\Wordlist\EnglishWordList());
    }
    /**
     * @param \BitWasp\Bitcoin\Mnemonic\Bip39\Bip39WordListInterface $wordList
     * @param EcAdapterInterface $ecAdapter
     * @return Bip39Mnemonic
     */
    public static function bip39(Bip39WordListInterface $wordList = null, EcAdapterInterface $ecAdapter = null) : Bip39Mnemonic
    {
        return new Bip39Mnemonic($ecAdapter ?: Bitcoin::getEcAdapter(), $wordList ?: new Bip39\Wordlist\EnglishWordList());
    }
}
