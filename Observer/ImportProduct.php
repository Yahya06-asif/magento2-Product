<?php

namespace Update\Product\Observer;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

class ImportProduct implements ObserverInterface
{

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    protected $_productRepository;

    protected $date;

    protected $username;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        ResourceConnection $resourceConnection,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        DateTime $date,
        Session $username,
        array $data = []
    ) {
        $this->_productRepository = $productRepository;
        $this->resourceConnection = $resourceConnection;
        $this->date = $date;
        $this->username = $username;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $bunch = $observer->getData()['bunch'];
        $connection  = $this->resourceConnection->getConnection();
        $tableName   = $connection->getTableName('updated_product');
        foreach ($bunch as $item) {
            $sku = $item['sku'];
            $product = $this->_productRepository->get($sku);
            $data = $product->getData();
            $quantity = $data['quantity_and_stock_status']['qty'];
            $newData = [
                'updated_at' => $this->date->gmtDate(),
                'product_type' => $product->getTypeId(),
                'product_id' => $product->getId(),
                'sku' => $product->getsku(),
                'url_key' => $product->getUrlKey(),
                'admin_name' => $this->username->getUser()->getUsername()
            ];
            $compareArray = ['name', 'status', 'special_price', 'price', 'qty'];
            foreach ($compareArray as $value) {
                $data[$value] = $product->getData($value);
                if ($value == 'price') {
                    $decPrice = $product->getData($value);
                    $decData = number_format($decPrice, 0, ".", '');
                    $data[$value] = $decData;
                }
                if ($value == 'special_price') {
                    $decPrice = $product->getData($value);
                    if ($decPrice == null) {
                        $data[$value] = $decPrice;
                    } else {
                        $itemValue = $item[$value];
                        if ($itemValue == null) {
                            $item[$value] = null;
                        } else {
                            $originalItem = number_format($itemValue, 0, ".", '');
                            $item[$value] = $originalItem;
                        }
                        $decData = number_format($decPrice, 0, ".", '');
                        $data[$value] = $decData;
                    }
                }
                if ($value == 'qty') {
                    $data[$value] = $quantity;
                }
                if ($data[$value] != $item[$value]) {
                    $newValue[$value] = $item[$value];
                }
            }
            if (isset($newValue)) {
                foreach ($newValue as $key => $value) {
                    $temp = $data[$key];
                    $newData[$key] = $newValue[$key];
                    $newData['old_' . $key] = $temp;
                }
                unset($newValue);
                $this->resourceConnection->getConnection()
                    ->insert(
                        $tableName,
                        $newData
                    );
            }
        }
    }
}
