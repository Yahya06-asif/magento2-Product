<?php
namespace Update\Product\Helper;

use Magento\Framework\App\Helper\Context;
//use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\ResourceConnection;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $resourceConnection;

    /**
     * @param Context $context
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Context $context,
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context);
    }

    /**
     * @param $newData
     * @param $tableName
     * @param $specialPrice
     * @param $quantity
     * @param $product
     * @return void|array
     */
    public function getProductHistory(
        $newData,
        $tableName,
        $specialPrice,
        $quantity,
        $product
    ) {
        $compareArray = ['name', 'status','special_price','price','qty'];
        foreach ($compareArray as $value) {

            $orignalData[$value] = $product->getOrigData($value);
            $data[$value] = $product->getData($value);
            if ($value == 'qty') {
                $orignalData[$value] = $product->getOrigData()['quantity_and_stock_status'][$value];
                $data[$value] = $quantity;
            }
            if ($value == 'price') {
                $decOrigPrice = $product->getOrigData($value);
                $decriment = number_format($decOrigPrice, 2, ".", ",");
                $orignalData[$value] = $decriment;
                $decPrice = $product->getData($value);
                $decData = number_format($decPrice, 2, ".", ",");
                $data[$value] = $decData;
            }
            if ($value == 'special_price') {
                // getting old special price value
                if ($specialPrice == null) {
                    $data[$value] = $specialPrice;
                } else {
                    $decOrigPrice = $product->getOrigData($value);
                    if ($decOrigPrice == null) {
                        $orignalData[$value] = null;
                    } else {
                        $decriment = number_format($decOrigPrice, 2, ".", ",");
                        $orignalData[$value] = $decriment;
                    }
                    $decPrice = $product->getData($value);
                    $decData = number_format($decPrice, 2, ".", ",");
                    $data[$value] = $decData;
                }
            }
            if ($orignalData[$value] != $data[$value]) {
                $newValue[$value] = $data[$value];
            }
        }
        if (isset($newValue)) {
            foreach ($newValue as $key => $value) {
                $temp =  $orignalData[$key];
                $newData[$key] = $newValue[$key];
                $newData['old_' . $key] = $temp;
            }
            $this->resourceConnection->getConnection()
                ->insert(
                    $tableName,
                    $newData
                );
        }
    }
}
