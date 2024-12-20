<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/debug-sentry', function () {
  throw new Exception('My first Sentry error!本番で反映');
});

Route::get('/test', 'TestController@index');
// Route::get('/set/tb_price_group_by_month', 'Tb_priceController@set_month');
// Route::get('/urltest', 'TestController@urltest');


//グラフ作成
Route::get('/make_graph', 'GraphController@make_graph');

// アプリ利用
// 金額取得
Route::get('/get/tb_price_data/{item}', 'Tb_priceController@get_tb_price_data');

//アプリ利用
// 相場表取得
Route::get('/make_price_list/{gold_type}', 'PriceListController@make');

// youtubeのURL取得
Route::get('/youtube_list/get_url', 'YoutubeListController@get_url');

// iframeのhtml取得
Route::get('/iframe/{url}', 'IframeController@get');

// sagawa api
Route::get('/sagawa/shuukairai/{is_test?}', 'SagawaController@shuukairai');
Route::post('/sagawa/shuukairai/{is_test?}', 'SagawaController@shuukairai');




//ローソク作成
Route::get('/make_candle_graph', 'CandleGraphController@make_candle');
Route::get('/make_candle_graph_nishitani', 'CandleGraphController_nishitani@make_candle');

//WP投稿取得API
Route::get('/get_wp_posts/{id}', 'WpPostsController@get_wp_posts');


//リキャプチャテスト
Route::get('/recaptha', 'googleReCapthaController@index');
Route::post('/recaptha', 'googleReCapthaController@submit');



//位置情報取得＋海外は403
Route::get('/kaigaiiphanbetsu/{ip}', 'kaigaiiphanbetsuController@index');



//インタビュー
Route::get('/interview_list', 'InterviewController@make_list_contents');
Route::get('/interview_detail/{interview_url}', 'InterviewController@make_detail_contents');
Route::get('/interview_detail/get_hero_area/{interview_url}', 'InterviewController@get_hero_area');
Route::get('/interview_detail/get_title_and_description/{interview_url}', 'InterviewController@get_title_and_description');
Route::get('/get_interview_block', 'InterviewController@make_block_contents');

//YouTube動画
Route::get('/get_videos_block', 'VideosController@make_block_contents');

//RAPAPORT API取得
Route::get('/rapa_price', 'RapaApiController@get_rapa_price');
//RAPAPORT API　ダイヤ価格為替計算
Route::get('/rapa_diamond_price', 'RapaApiController@dummy');
Route::post('/rapa_diamond_price', 'RapaApiController@diamond_price')->middleware('ip.companies');
//ダイヤ該当カラット　平均値計算
Route::get('/get_diamond_price', 'RapaApiController@getDiamond');

Route::get('/test_tamiya', 'TestController@test_func');

Route::get('/voice', 'VoiceController@index');

Route::get('/letter', 'LetterController@index');

Route::get('/month_campaign', 'Month_campaignController@index');

// ブランド数の取得
Route::get('/get_number_of_brands', 'GetNumberOfBrandsController@report');

Route::get('/mailgun/receive_event', 'MailgunApiController@receive_event');

Route::post('/mailgun/receive_event', 'MailgunApiController@receive_post_event');

Route::get('/mailgun/convert_from_mailgun_log_to_send_log/{mailgun_message_id?}', 'MailgunApiController@convert_from_mailgun_log_to_send_log')->middleware('ip.companies');

//ブランドグレーアウト非表示
Route::get('/hide_gray_out', 'Hide_gray_outController@index');

//インゴット 買取価格相場表
Route::get('/ingot_price_list', 'Ingot_price_listController@make_list');
Route::get('/make/brand_hide_flag','BrandController@make_hide_flag');

//ダイヤモンドシミュレーションツール
Route::get('/diamond_simulation_tool', 'Diamond_simulation_toolController@make_tool');
Route::get('/diamond_simulation_tool_graph', 'Diamond_simulation_toolController@get_graph');

//佐川住所取得API
Route::get('/get_sagawa_address', 'GetSagawaController@get_address');

// 規約HTMLをファイルキャッシュ
Route::get('/tasks/put_kiyaku_html', 'Tasks\TaskController@put_kiyaku_html')->middleware('ip.companies');

// ゴールドコイン
Route::get('goldcoin_list', 'GoldcoinListController@index');

//サイトマップ　カテゴリーの一覧
Route::get('sitemaps', 'SitemapController@category_list');

//サイトマップ　カテゴリー別ページURL一覧
Route::get('sitemaps/category', 'SitemapController@index_by_category_id');

//サイトマップ バッチ処理
Route::get('auto_update_sitemap', 'AutoUpdateSitemapController@index');

//サイトマップ　バッチ処理　カテゴリー一覧の取得
Route::get('auto_update_sitemap/get_all_categories', 'AutoUpdateSitemapController@get_all_categories');

//量産ページ
Route::get('brand_page_settings', 'BrandPageSettingsController@index');

//brandlist
//ブランドデータを取得する
Route::get('brandlist/get_brand', 'BrandlistController@get_brand');
// 全てのブランドリストを表示する
Route::get('brandlist/view_all_brand', 'BrandlistController@view_all_brand');

//vuitton/pricelist
// カテゴリー・ライン・フリーワードでの検索
Route::get('vuitton_pricelist/search_prices', 'VuittonPricelistController@search_prices');
// 型番で検索
Route::get('vuitton_pricelist/get_price_by_kataban', 'VuittonPricelistController@get_price_by_kataban');

//ダイヤ重量ごとの寸法や最大･最小･平均の買取相場一覧表
Route::get('diamond/list_of_diamond_size_and_market/search_diamond_price_by_select', 'ListOfDiamondSizeAndMarketController@search_diamond_price_by_select');

//アリそで無かった！ダイヤモンドの直径とカラット
Route::get('4c/sizetable', 'Diamond4cSizeTableController@index');

//コラム移管バッチ処理テスト
Route::get('wp_batch', 'WpBatchController@batch');

//GoogleChatSpaceにチャットを送信
Route::get('pushGoogleChatSpace/{space_id}', 'pushGoogleChatSpaceController@send_chat');
Route::post('pushGoogleChatSpace/{space_id}', 'pushGoogleChatSpaceController@send_chat');

//リアルチャット
Route::get('realchat', 'RealChatController@index');
//リアルチャット　詳細ページ
Route::get('realchat_detail', 'RealChatDetailController@index');
Route::get('realchat_detail/get_title_and_description/{id}', 'RealChatDetailController@get_title_and_description');

//daily金とプラチナの日別買取相場価格表
Route::get('/daily', 'DailyController@index');

//monthly金,プラチナの国内公表買取価格の月次価格推移
Route::get('/monthly', 'MonthlyController@index');

//GoldCandlestick,金ローソク足
Route::get('/metaltable', 'GetGoldCandlestickController@index');

//金相場高騰更新対応
Route::get('/get_just_time_gold_price', 'GoldPrice_updateController@getJustTimeGoldPrice');

//金・プラチナ価格取得JSON
Route::get('/get_just_time_gold_platinum_price_fv', 'GoldPlatinumPrice_fv_updateController@getJustTimeGoldPlatinumPrice_fv');

//PlatinumCandlestick,プラチナローソク足
Route::get('/metalplatinumtable', 'GetPlatinumCandlestickController@index');

//量産ページ
Route::get('colorstone_page_settings', 'ColorstonePageSettingsController@index');

//リファスタはサイト設立周年
Route::get('/refasta_anniversary', 'Refasta_AnniversaryController@index');

//リファスタ金相場のTwitterBot
// Route::get('/twitter_bot', 'TwitterBot\TwitterBotController@sendTweet');

//地域お客様の買取実績
Route::get('/purchased_result', 'PurchasedResultController@index');
Route::get('/purchased_area', 'PurchasedAreaSettingController@index');
Route::get('/purchased_voice', 'PurchasedVoiceController@index');
Route::get('/purchased_cityname', 'AreaCollectController@index');

//リファスタ管理画面 gold_platinum_market
Route::get('/admininfo_market_year', 'DbMarketYearController@index')->middleware('ip.companies');

// 全てのキャッシュクリア
Route::get('/clear_cache', 'CacheController@clearAllCache');
// 買取実績用の画像リサイズ
Route::get('/image_resize/{result_id}/{image_width?}', 'ImageResizeController@index');
//パン屑対応
Route::get('/pankuzu/{rs_purchased_result_id}', 'PankuzuController@index');
// 買取実績 詳細ページ作成
Route::get('/purchased_result_detail/{product_id}', 'PurchasedResultController@purchased_result_detail');
Route::get('/admininfo_market_year', 'DbMarketYearController@index')->middleware('ip.companies');

//ダイヤモンドシミュレーションツール最新
Route::get('/diamond_simulation', 'Diamond_simulation_Controller@index')->name('index');
Route::get('/diamond_simulation_tool_graph', 'Diamond_simulation_Controller@get_graph');

// 査定メールの返答
Route::get('/satei_response/{Eoc_takuhai_id}/tokens/{token}/{response}','EocTakuhaiSateiTokenController@satei_response');

//ダイヤモンドシミュレーションツールVer02最新
Route::get('/diamond_simulationVer02', 'Diamond_simulationVer02_Controller@index')->name('index');
Route::get('/diamond_simulation_tool_graph', 'Diamond_simulationVer02_Controller@get_graph');