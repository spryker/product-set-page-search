<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Product\Dependency\Plugin;

use Generated\Shared\Transfer\ProductAbstractTransfer;

interface ProductAbstractPluginUpdateInterface
{

    /**
     * Specification:
     * - Executed before and after an abstract product is updated.
     * - Can be used for persisting other abstract product related information to database or execute any other logic.
     * - To inject instances of the plugin @see \Spryker\Zed\Product\ProductDependencyProvider.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductAbstractTransfer $productAbstractTransfer
     *
     * @return \Generated\Shared\Transfer\ProductAbstractTransfer
     */
    public function update(ProductAbstractTransfer $productAbstractTransfer);

}