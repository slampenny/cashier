<?php

namespace Laravel\Cashier;

use Illuminate\Support\Facades\Config;
use Laravel\Cashier\Contracts\Billable as BillableContract;

class EloquentBillableRepository implements BillableRepositoryInterface
{
    /**
     * Find a Billable implementation by Stripe ID.
     *
     * @param  string  $gatewayId
     * @return \Laravel\Cashier\Contracts\Billable
     */
    public function find($gatewayId)
    {
        $billableModels = Config::get('services.gateway.model');

        if (! is_array($billableModels)) {
            $billableModels = [$billableModels];
        }

        foreach ($billableModels as $billableModel) {
            $model = $this->createCashierModel($billableModel);

            if ($result = $model->where($model->getGatewayIdName(), $gatewayId)->first()) {
                return $result;
            }
        }
    }

    /**
     * Create a new instance of the Auth model.
     *
     * @param  string  $class
     * @return \Laravel\Cashier\Contracts\Billable
     */
    protected function createCashierModel($class)
    {
        $model = new $class;

        if (! $model instanceof BillableContract) {
            throw new \InvalidArgumentException('Model does not implement Billable.');
        }

        return $model;
    }
}
