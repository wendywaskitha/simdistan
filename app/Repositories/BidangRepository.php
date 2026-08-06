<?php

namespace App\Repositories;

use App\Models\Bidang;
use Illuminate\Database\Eloquent\Model;

class BidangRepository extends BaseRepository implements BidangRepositoryInterface
{
    /**
     * BidangRepository constructor.
     *
     * @param Bidang $model
     */
    public function __construct(Bidang $model)
    {
        parent::__construct($model);
    }
}
