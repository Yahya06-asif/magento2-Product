<?php

namespace Update\Product\Observer;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

class UpdatedProduct implements ObserverInterface
{
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Update\Product\Helper\Data
     */
    protected $helper;

    protected $date;

    protected $username;

    public function __construct(
        ResourceConnection $resourceConnection,
        DateTime $date,
        \Update\Product\Helper\Data $helper,
        Session $username
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->date = $date;
        $this->helper = $helper;
        $this->username = $username;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getProduct();
        $specialPrice = $product->getSpecialPrice();
        $data = [];
        $quantity = $product->getStockData()['qty'];
        $connection = $this->resourceConnection->getConnection();
        $tableName = $connection->getTableName('updated_product');
        $newData = [
            'updated_at' => $this->date->gmtDate(),
            'product_type' => $product->getTypeId(),
            'product_id' => $product->getId(),
            'sku' => $product->getsku(),
            'url_key' => $product->getUrlKey(),
            'admin_name' => $this->username->getUser()->getUsername()
        ];
        $helper = $this->helper;
        $values = $helper->getProductHistory(
            $newData,
            $tableName,
            $specialPrice,
            $quantity,
            $product
        );
    }
}
