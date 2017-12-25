<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductLabelStorage\Communication\Plugin\Event\Listener;

use \ArrayObject;
use Generated\Shared\Transfer\ProductLabelDictionaryItemTransfer;
use Generated\Shared\Transfer\ProductLabelDictionaryStorageTransfer;
use Orm\Zed\ProductLabel\Persistence\SpyProductLabelLocalizedAttributes;
use Orm\Zed\ProductLabelStorage\Persistence\SpyProductLabelDictionaryStorage;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Spryker\Zed\ProductLabelStorage\Persistence\ProductLabelStorageQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\ProductLabelStorage\Communication\ProductLabelStorageCommunicationFactory getFactory()
 */
class AbstractProductLabelDictionaryStorageListener extends AbstractPlugin
{

    /**
     * @return void
     */
    protected function publish()
    {
        $spyProductLabelLocalizedAttributeEntities = $this->findProductLabelLocalizedEntities();
        $productLabelDictionaryItems = [];
        foreach ($spyProductLabelLocalizedAttributeEntities as $spyProductLabelLocalizedAttributeEntity) {
            $localeName = $spyProductLabelLocalizedAttributeEntity->getSpyLocale()->getLocaleName();
            $productLabelDictionaryItems[$localeName][] = $this->mapProductLabelDictionaryItem($spyProductLabelLocalizedAttributeEntity);
        }

        $this->storeData($productLabelDictionaryItems);
    }

    /**
     * @return void
     */
    protected function unpublish()
    {
        $spyProductStorageEntities = $this->findProductLabelDictionaryStorageEntities();
        foreach ($spyProductStorageEntities as $spyProductStorageEntity) {
            $spyProductStorageEntity->delete();
        }
    }

    /**
     * @param ProductLabelDictionaryItemTransfer[] $productLabelDictionaryItems
     *
     * @return void
     */
    protected function storeData(array $productLabelDictionaryItems)
    {
        $spyProductLabelStorageEntities = $this->findProductLabelDictionaryStorageEntities();

        foreach ($productLabelDictionaryItems as $localeName => $productLabelDictionaryItemTransfers) {
            $productLabelDictionaryStorageTransfer = new ProductLabelDictionaryStorageTransfer();
            $productLabelDictionaryStorageTransfer->setItems(new ArrayObject($productLabelDictionaryItemTransfers));

            if (isset($spyProductLabelStorageEntities[$localeName]))  {
                $this->storeDataSet($productLabelDictionaryStorageTransfer, $localeName, $spyProductLabelStorageEntities[$localeName]);

                continue;
            }

            $this->storeDataSet($productLabelDictionaryStorageTransfer, $localeName);
        }
    }

    /**
     * @param ProductLabelDictionaryStorageTransfer $productLabelDictionaryStorageTransfer
     * @param string $localeName
     * @param \Orm\Zed\ProductLabelStorage\Persistence\SpyProductLabelDictionaryStorage|null $spyProductLabelStorageEntity
     *
     * @return void
     */
    protected function storeDataSet(
        ProductLabelDictionaryStorageTransfer $productLabelDictionaryStorageTransfer,
        $localeName,
        SpyProductLabelDictionaryStorage $spyProductLabelStorageEntity = null
    ) {
        if ($spyProductLabelStorageEntity === null) {
            $spyProductLabelStorageEntity = new SpyProductLabelDictionaryStorage();
        }

        $spyProductLabelStorageEntity->setData($productLabelDictionaryStorageTransfer->modifiedToArray());
        $spyProductLabelStorageEntity->setStore($this->getStoreName());
        $spyProductLabelStorageEntity->setLocale($localeName);
        $spyProductLabelStorageEntity->save();
    }

    /**
     * @return SpyProductLabelLocalizedAttributes[]
     */
    protected function findProductLabelLocalizedEntities()
    {
        return $this->getQueryContainer()->queryProductLabelLocalizedAttributes()->find();
    }

    /**
     * @param SpyProductLabelLocalizedAttributes $spyProductLabelLocalizedAttributeEntity
     *
     * @return ProductLabelDictionaryItemTransfer
     */
    protected function mapProductLabelDictionaryItem(SpyProductLabelLocalizedAttributes $spyProductLabelLocalizedAttributeEntity)
    {
        $productLabelDictionaryStorageTransfer = new ProductLabelDictionaryItemTransfer();
        $productLabelDictionaryStorageTransfer
            ->setName($spyProductLabelLocalizedAttributeEntity->getName())
            ->setIdProductLabel($spyProductLabelLocalizedAttributeEntity->getFkProductLabel())
            ->setKey($spyProductLabelLocalizedAttributeEntity->getSpyProductLabel()->getName())
            ->setIsExclusive($spyProductLabelLocalizedAttributeEntity->getSpyProductLabel()->getIsExclusive())
            ->setPosition($spyProductLabelLocalizedAttributeEntity->getSpyProductLabel()->getPosition())
            ->setFrontEndReference($spyProductLabelLocalizedAttributeEntity->getSpyProductLabel()->getFrontEndReference());

        return $productLabelDictionaryStorageTransfer;
    }

    /**
     * @return array
     */
    protected function findProductLabelDictionaryStorageEntities()
    {
        $productAbstractStorageEntities = $this->getQueryContainer()->queryProductLabelDictionaryStorage()->find();
        $productAbstractStorageEntitiesByIdAndLocale = [];
        foreach ($productAbstractStorageEntities as $productAbstractStorageEntity) {
            $productAbstractStorageEntitiesByIdAndLocale[$productAbstractStorageEntity->getLocale()] = $productAbstractStorageEntity;
        }

        return $productAbstractStorageEntitiesByIdAndLocale;
    }

    /**
     * @return string
     */
    protected function getStoreName()
    {
        return $this->getFactory()->getStore()->getStoreName();
    }

}