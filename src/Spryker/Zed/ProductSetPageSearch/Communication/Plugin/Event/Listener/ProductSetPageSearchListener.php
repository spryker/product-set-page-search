<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductSetPageSearch\Communication\Plugin\Event\Listener;

use Spryker\Zed\Event\Dependency\Plugin\EventBulkHandlerInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\ProductSet\Dependency\ProductSetEvents;
use Spryker\Zed\PropelOrm\Business\Transaction\DatabaseTransactionHandlerTrait;

/**
 * @deprecated Use `\Spryker\Zed\ProductSetPageSearch\Communication\Plugin\Event\Listener\ProductSetPageSearchPublishListener` and `\Spryker\Zed\ProductSetPageSearch\Communication\Plugin\Event\Listener\ProductSetPageSearchUnpublishListener` instead.
 *
 * @method \Spryker\Zed\ProductSetPageSearch\Persistence\ProductSetPageSearchQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\ProductSetPageSearch\Communication\ProductSetPageSearchCommunicationFactory getFactory()
 * @method \Spryker\Zed\ProductSetPageSearch\Business\ProductSetPageSearchFacadeInterface getFacade()
 * @method \Spryker\Zed\ProductSetPageSearch\ProductSetPageSearchConfig getConfig()
 */
class ProductSetPageSearchListener extends AbstractPlugin implements EventBulkHandlerInterface
{
    use DatabaseTransactionHandlerTrait;

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\EventEntityTransfer[] $eventTransfers
     * @param string $eventName
     *
     * @return void
     */
    public function handleBulk(array $eventTransfers, $eventName)
    {
        $this->preventTransaction();
        $productSetIds = $this->getFactory()->getEventBehaviorFacade()->getEventTransferIds($eventTransfers);

        if (
            $eventName === ProductSetEvents::ENTITY_SPY_PRODUCT_SET_DELETE ||
            $eventName === ProductSetEvents::ENTITY_SPY_PRODUCT_SET_DATA_DELETE ||
            $eventName === ProductSetEvents::PRODUCT_SET_UNPUBLISH
        ) {
            $this->getFacade()->unpublish($productSetIds);

            return;
        }

        $this->getFacade()->publish($productSetIds);
    }
}
