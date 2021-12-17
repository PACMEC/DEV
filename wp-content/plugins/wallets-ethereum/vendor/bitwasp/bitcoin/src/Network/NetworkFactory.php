<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network;

class NetworkFactory
{
    /**
     * @return NetworkInterface
     * @throws \Exception
     */
    public static function bitcoin() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\Networks\Bitcoin();
    }
    /**
     * @return NetworkInterface
     * @throws \Exception
     */
    public static function bitcoinTestnet() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\Networks\BitcoinTestnet();
    }
    /**
     * @return NetworkInterface
     * @throws \Exception
     */
    public static function bitcoinRegtest() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\Networks\BitcoinRegtest();
    }
    /**
     * @return NetworkInterface
     */
    public static function litecoin() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\Networks\Litecoin();
    }
    /**
     * @return Networks\LitecoinTestnet
     */
    public static function litecoinTestnet() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\Networks\LitecoinTestnet();
    }
    /**
     * @return Networks\Viacoin
     */
    public static function viacoin() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\Networks\Viacoin();
    }
    /**
     * @return Networks\ViacoinTestnet
     */
    public static function viacoinTestnet() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\Networks\ViacoinTestnet();
    }
    /**
     * @return Networks\Dogecoin
     */
    public static function dogecoin() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\Networks\Dogecoin();
    }
    /**
     * @return Networks\DogecoinTestnet
     */
    public static function dogecoinTestnet() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\Networks\DogecoinTestnet();
    }
    /**
     * @return Networks\Dash
     */
    public static function dash() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\Networks\Dash();
    }
    /**
     * @return Networks\DashTestnet
     */
    public static function dashTestnet() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\NetworkInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\Networks\DashTestnet();
    }
    /**
     * @return NetworkInterface
     */
    public static function zcash()
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Network\Networks\Zcash();
    }
}
