<?php
namespace hiperesp\server\models;

use hiperesp\server\exceptions\DFException;
use hiperesp\server\vo\ItemVO;
use hiperesp\server\vo\ShopVO;

class ItemModel extends Model {

    const COLLECTION = 'item';
    const SHOP_ASSOCIATION = 'shop_item';

    public function getById(int $itemId): ItemVO {
        $item = $this->storage->select(self::COLLECTION, ['id' => $itemId]);
        if(isset($item[0]) && $item = $item[0]) {
            return new ItemVO($item);
        }
        throw DFException::fromCode(DFException::ITEM_NOT_FOUND);
    }

    public function getByShopAndId(ShopVO $shop, int $id): ItemVO {
        $item = $this->storage->select(self::SHOP_ASSOCIATION, ['shopId' => $shop->id, 'itemId' => $id]);
        if(isset($item[0]) && $item = $item[0]) {
            return new ItemVO($item);
        }
        throw DFException::fromCode(DFException::ITEM_NOT_FOUND);
    }

    /** @return array<ItemVO> */
    public function getByShop(ShopVO $shop): array {
        $itemIds = \array_map(function(array $item): int {
            return $item['itemId'];
        }, $this->storage->select(self::SHOP_ASSOCIATION, ['shopId' => $shop->id], null));

        return \array_map(function(array $item): ItemVO {
            return new ItemVO($item);
        }, $this->storage->select(self::COLLECTION, ['id' => $itemIds], null));
    }

}