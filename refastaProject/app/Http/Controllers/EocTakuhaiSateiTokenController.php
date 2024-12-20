<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Models\Internet\Eoc_takuhai_satei_token;
use App\Models\Urlounge\Eoc_takuhai_satei_response;
use Exception;

class EocTakuhaiSateiTokenController extends Controller
{
    public function satei_response(Request $request, $Eoc_takuhai_id, $token, $response)
    {
        $Eoc_takuhai_satei_token = Eoc_takuhai_satei_token::where('Eoc_takuhai_id', $Eoc_takuhai_id)
            ->where('token', $token)
            ->where('is_responsed', 0)
            ->where('limit_at', '>', now())
            ->orderBy('id', 'desc')
            ->first();
        if (is_null($Eoc_takuhai_satei_token)) {
            // 有効なトークンがない場合は処理を中断する
            return '申し訳ございません。<br>受付処理が完了している、もしくは、URLが無効です。<br>お問い合わせの際は査定担当へ直接ご連絡いただきますようお願い申し上げます。';
        }

        if (Eoc_takuhai_satei_response::where('Eoc_takuhai_id', $Eoc_takuhai_id)->exists()) {
            // トークンに限らず、宅配取引IDに対してすでに返答データがある場合は処理を中断する
            return 'ご返答の受付完了済みです。<br>査定人からの連絡をお待ちください。';
        }

        try {
            $result = file_get_contents("https://rifa.life/evaProject/api/satei_response/{$Eoc_takuhai_id}/tokens/{$token}/{$response}");
            if ($result === 'success') {
                return '受付完了いたしました。';
            } else {
                return 'エラーが発生しました。';
            }
        } catch (Exception $e) {
            return 'エラーが発生しました。';
        }
    }
}
