<?php


namespace App\Services;


use App\Helpers\Helper;
use App\Models\Purchase;
use App\Models\Reward;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserServiceImpl extends BaseServiceImpl implements UserService
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function addReward($id, array $reward_attributes)
    {
        return Reward::create(array_merge($reward_attributes, [
            'target_user_id' => $id
        ]));
    }

    public function updateOrAddReward($id, array $params, array $reward_attributes)
    {
        return Reward::updateOrCreate(array_merge($params, [
            'target_user_id' => $id
        ]), $reward_attributes);
    }

    /**
     * @inheritDoc
     */
    public function awardReferrersAfterPurchase(Purchase $purchase)
    {
        DB::beginTransaction();

        try {

            $purchase->loadMissing('user.referrer_recursive', 'purchasable');

            if (empty($purchase->user->referrer)) {
                return;
            }

            $purchasable = $purchase->purchasable;

            $all_referrers = Helper::flat_all_referrers($purchase->user);

            foreach ($all_referrers as $referrer) {
                $is_direct = $purchase->user->referrer_id == $referrer->id;
                $this->updateOrAddReward($referrer->id, [
                    'purchase_id' => $purchase->id,
                ],[
                    'sum' => $is_direct
                        ? $purchasable->getAwardSum()
                        : $purchasable->getAwardSum() * $referrer->fee_percent / 100,
                    'is_direct' => $is_direct,
                    'points' => $purchasable->isAwardable()
                        ? Purchase::$DIRECT_POINTS_PER_PURCHASE
                        : null
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
}