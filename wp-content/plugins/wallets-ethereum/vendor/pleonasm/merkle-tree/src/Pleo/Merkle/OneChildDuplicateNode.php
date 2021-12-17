<?php

/**
 * @copyright 2013 Matthew Nagi
 * @license http://opensource.org/licenses/BSD-2-Clause BSD 2-Clause License
 */
namespace Ethereumico\EthereumWallet\Dependencies\Pleo\Merkle;

/**
 * Acts like a TwoChildrenNode but accepts one data item and duplicates it
 */
class OneChildDuplicateNode implements \Ethereumico\EthereumWallet\Dependencies\Pleo\Merkle\ITreeNode
{
    /**
     * @var TwoChildrenNode
     */
    private $node;
    /**
     * @param TwoChildrenNode $node
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\Pleo\Merkle\TwoChildrenNode $node)
    {
        $this->node = $node;
    }
    /**
     * @throws UnexpectedValueException
     * @return string|null
     */
    public function hash()
    {
        return $this->node->hash();
    }
    /**
     * @param string|ITreeNode $data
     * @throws LogicException
     * @throws InvalidArgumentException
     * @return null
     */
    public function data($data)
    {
        return $this->node->data($data, $data);
    }
}
