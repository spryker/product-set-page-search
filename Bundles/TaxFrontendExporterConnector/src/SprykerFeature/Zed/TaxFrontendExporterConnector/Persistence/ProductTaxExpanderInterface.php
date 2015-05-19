<?php

namespace SprykerFeature\Zed\TaxFrontendExporterConnector\Persistence;

use Propel\Runtime\ActiveQuery\ModelCriteria;

interface ProductTaxExpanderInterface
{
    /**
     * @param ModelCriteria $expandableQuery
     *
     * @return ModelCriteria
     */
    public function expandQuery(ModelCriteria $expandableQuery);
}