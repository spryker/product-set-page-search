<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductSetPageSearch\Communication\Plugin\Search;

use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\PageMapTransfer;
use Spryker\Shared\ProductSetPageSearch\ProductSetPageSearchConstants;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface;
use Spryker\Zed\Search\Dependency\Plugin\NamedPageMapInterface;

/**
 * @deprecated Will be removed without replacement.
 *
 * @method \Spryker\Zed\ProductSetPageSearch\Persistence\ProductSetPageSearchQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\ProductSetPageSearch\Communication\ProductSetPageSearchCommunicationFactory getFactory()
 * @method \Spryker\Zed\ProductSetPageSearch\Business\ProductSetPageSearchFacadeInterface getFacade()
 * @method \Spryker\Zed\ProductSetPageSearch\ProductSetPageSearchConfig getConfig()
 */
class ProductSetPageMapPlugin extends AbstractPlugin implements NamedPageMapInterface
{
    /**
     * @var array<string>
     */
    public const FILTERED_KEYS = [
        'locale',
        'store',
        'type',
    ];

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface $pageMapBuilder
     * @param array<string, mixed> $data
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return \Generated\Shared\Transfer\PageMapTransfer
     */
    public function buildPageMap(PageMapBuilderInterface $pageMapBuilder, array $data, LocaleTransfer $localeTransfer)
    {
        $pageMapTransfer = (new PageMapTransfer())
        ->setStore($data['store'])
        ->setLocale($localeTransfer->getLocaleName())
        ->setType('product_set');

        $pageMapBuilder->addIntegerSort($pageMapTransfer, 'weight', $data['weight']);
        $this->mapProductSetStorageTransfer($pageMapTransfer, $pageMapBuilder, $data);

        return $pageMapTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\PageMapTransfer $pageMapTransfer
     * @param \Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface $pageMapBuilder
     * @param array<string, mixed> $data
     *
     * @return void
     */
    protected function mapProductSetStorageTransfer(PageMapTransfer $pageMapTransfer, PageMapBuilderInterface $pageMapBuilder, array $data)
    {
        foreach ($data as $key => $value) {
            if ($value !== null && !in_array($key, static::FILTERED_KEYS)) {
                $pageMapBuilder->addSearchResultData($pageMapTransfer, $key, $value);
            }
        }
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return string
     */
    public function getName()
    {
        return ProductSetPageSearchConstants::PRODUCT_SET_RESOURCE_NAME;
    }
}
