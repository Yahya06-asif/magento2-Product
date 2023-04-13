<?php
namespace Update\Product\Ui\DataProvider\Reviews;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult
{
    protected function _initSelect()
    {
        $this->addFilterToMap('entity_id', 'mainTable.entity_id');
        $this->addFilterToMap('product_id', 'mainTable.product_id');
        $this->addFilterToMap('admin_name', 'mainTable.admin_name');
        $this->addFilterToMap('name', 'mainTable.name');
        $this->addFilterToMap('old_name', 'mainTable.old_name');
        $this->addFilterToMap('qty', 'mainTable.qty');
        $this->addFilterToMap('old_qty', 'mainTable.old_qty');
        $this->addFilterToMap('status', 'mainTable.status');
        $this->addFilterToMap('old_status', 'mainTable.old_status');
        $this->addFilterToMap('url_key', 'mainTable.url_key');
        $this->addFilterToMap('product_type', 'mainTable.product_type');
        $this->addFilterToMap('price', 'mainTable.price');
        $this->addFilterToMap('old_price', 'mainTable.old_price');
        $this->addFilterToMap('special_price', 'mainTable.special_price');
        $this->addFilterToMap('old_special_price', 'mainTable.old_special_price');
        $this->addFilterToMap('created_at', 'mainTable.created_at');
        parent::_initSelect();
    }
}
