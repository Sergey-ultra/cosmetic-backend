<?php

use App\Http\Controllers\Api\Admin\Parser\ReviewParserController;
use App\Http\Controllers\Api\Admin\RejectedReasonController;
use App\Http\Controllers\Api\Admin\SettingController;
use App\Http\Controllers\Api\ArticleCommentController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ClientMessagesController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\FAQController;
use App\Http\Controllers\Api\FavoritesController;
use App\Http\Controllers\Api\FilterController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\MainController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\PriceHistoryController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\RouteController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SkuController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\SkuVideoController;
use App\Http\Controllers\Api\AuthSocialController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\TelegramController;
use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\Api\UserController;



use App\Http\Controllers\Api\Supplier\AuthController as SupplierAuthController;
use App\Http\Controllers\Api\Supplier\StoreController as SupplierStoreController;



use App\Http\Controllers\Api\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Api\Admin\ArticleCommentController as AdminArticleCommentController;
use App\Http\Controllers\Api\Admin\SkuController as AdminSkuController;
use App\Http\Controllers\Api\Admin\StoreController as AdminStoreController;
use App\Http\Controllers\Api\Admin\IngredientController as AdminIngredientController;
use App\Http\Controllers\Api\Admin\CountryController as AdminCountryController;
use App\Http\Controllers\Api\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Api\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Api\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\TrackingController as AdminTrackingController;
use App\Http\Controllers\Api\Admin\TagController as AdminTagController;
use App\Http\Controllers\Api\Admin\SkuVideoController as AdminSkuVideoController;
use App\Http\Controllers\Api\Admin\MessageController as AdminMessageController;


use App\Http\Controllers\Api\Admin\VisitStaticticsController;
use App\Http\Controllers\Api\Admin\LinkClickController;
use App\Http\Controllers\Api\Admin\SitemapController;
use App\Http\Controllers\Api\Admin\SupplierController;
use App\Http\Controllers\Api\Admin\Parser\LinkOptionController;
use App\Http\Controllers\Api\Admin\Parser\ProductLinksParserController;
use App\Http\Controllers\Api\Admin\Parser\ParsingLinkController;
use App\Http\Controllers\Api\Admin\Parser\PriceOptionController;
use App\Http\Controllers\Api\Admin\Parser\PriceParserController;
use App\Http\Controllers\Api\Admin\Parser\ProductOptionController;
use App\Http\Controllers\Api\Admin\Parser\ProductParserController;
use App\Http\Controllers\Api\Admin\Parser\Old\ParserControllerProxy;


use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//Route::middleware('throttle:5,1')->group(function () {
//
//});



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::get('/login/{service}', [AuthSocialController::class, 'redirect'])
    ->whereIn('service', AuthSocialController::AVAILABLE_SERVICES);
Route::get('/login/{service}/callback', [AuthSocialController::class, 'callback'])
    ->whereIn('service', AuthSocialController::AVAILABLE_SERVICES);

Route::group(['prefix' => '/admin'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware(['auth:api']);
});

Route::get('/users/best', [UserController::class, 'bestUsers']);
Route::post('/users/update-telegram-user', [TelegramController::class, 'updateTelegramUser']);

Route::post('/supplier/signin', [App\Http\Controllers\Api\Supplier\AuthController::class, 'login']);
Route::post('/supplier/signup', [App\Http\Controllers\Api\Supplier\AuthController::class, 'register']);
Route::post('/supplier/logout', [SupplierAuthController::class, 'logout'])->middleware(['auth:api', 'role:supplier']);



Route::get('/faq/menu', [FAQController::class, 'menu']);


Route::get('/client-messages', [ClientMessagesController::class, 'index']);
Route::post('/client-messages', [ClientMessagesController::class, 'store']);


Route::post('/add-to-tracking', [TrackingController::class, 'addToTracking']);
Route::post('/supplier/register', [AuthController::class, 'register']);
Route::post('/supplier/login', [AuthController::class, 'login']);
//Route::post('/subscription', [SubscriptionController::class, 'store']);

Route::get('/my-location', [UserController::class, 'getMyLocation']);
Route::get('/location-list', [LocationController::class, 'index']);


Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/last', [ArticleController::class, 'last']);
Route::get('/articles/by-tag/{tag}', [ArticleController::class, 'byTag']);
Route::get('/articles/by-category-id/{id}', [ArticleController::class, 'byCategoryId']);
Route::get('/articles/by-slug/{slug}', [ArticleController::class, 'show']);



Route::get('/main/statistics', [MainController::class, 'index']);

Route::get('/banner', [BannerController::class, 'index']);
Route::get('/filter', [FilterController::class, 'index']);
Route::get('filter/receipts', [FilterController::class, 'receipts']);
Route::get('/price-history', [PriceHistoryController::class, 'index']);

Route::get('/brands/by-code/{code}', [BrandController::class, 'byCode']);
Route::get('/brands/popular', [BrandController::class, 'popular']);
Route::get('/brands/by-letters', [BrandController::class, 'byLetters']);
Route::get('/brands/all', [BrandController::class, 'all']);


Route::get('/categories/by-code/{code}', [CategoryController::class, 'byCode']);
Route::get('/categories/all', [CategoryController::class, 'all']);
Route::get('/categories/popular-categories', [CategoryController::class, 'popularCategories']);


Route::get('/show-compared-skus', [SkuController::class, 'showComparedSkus']);
Route::get('/skus/by-sku-id/{skuId}', [SkuController::class, 'bySkuId']);
Route::get('/skus/main', [SkuController::class, 'mainIndex']);
Route::get('/skus/viewed', [SkuController::class, 'viewed']);
Route::get('/skus/popular', [SkuController::class, 'popularTenSkus']);


Route::get('/suggest', [SearchController::class, 'index']);



Route::get('/reviews/last', [ReviewController::class, 'last']);
Route::get('/reviews/by-sku-id/{id}', [ReviewController::class, 'bySkuId']);
Route::get('/reviews/{id}', [ReviewController::class, 'show'])->where(['id' => '[0-9]+']);
Route::get('/reviews/additional-info-by-sku-id/{id}', [ReviewController::class, 'additionalInfoBySkuId']);

Route::post('/likes/{id}', [LikeController::class, 'createOrUpdate'])->where(['id' => '[0-9]+']);

Route::get('/comments', [CommentController::class, 'byReviewId']);

Route::get('/questions', [QuestionController::class, 'bySkuId']);


Route::post('/files', [FileController::class, 'storeAsForm']);

Route::get('/routes/skus-queries', [RouteController::class, 'skusQueries']);
Route::get('/routes/articles', [RouteController::class, 'articles']);
Route::get('/routes/categories', [RouteController::class, 'categories']);
Route::get('/routes/brands', [RouteController::class, 'brands']);
Route::get('/routes/reviews', [RouteController::class, 'reviews']);


Route::group(['middleware' => ['auth:api']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::get('/notification-bot', [TelegramController::class, 'startNotificationBot']);
    Route::get('/notification/telegram/account', [TelegramController::class, 'account']);
    Route::post('/notification/telegram/account/unsubscribe', [TelegramController::class, 'unsubscribe']);

    Route::get('/skus/my', [SkuController::class, 'mySkus']);
    Route::post('/skus', [SkuController::class, 'store']);


    Route::post('/brands', [BrandController::class, 'store']);

    Route::get('/articles/my', [ArticleController::class, 'my']);
    Route::get('/articles/categories', [ArticleController::class, 'articleCategories']);
    Route::get('/articles/tags', [ArticleController::class, 'tags']);

    Route::get('/article-comments/my', [ArticleCommentController::class, 'my']);
    Route::post('/article-comments', [ArticleCommentController::class, 'store']);
    Route::delete('/article-comments/{id}', [ArticleCommentController::class, 'destroy']);

    Route::get('/comments/my', [CommentController::class, 'my']);
    Route::post('/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->where(['id' => '[0-9]+']);

    Route::get('/videos/my', [SkuVideoController::class, 'my']);


    Route::get('/reviews/my', [ReviewController::class, 'my']);
    Route::get('/reviews/my-drafts', [ReviewController::class, 'myDrafts']);
    Route::get('/reviews/my-moderated', [ReviewController::class, 'myModeratedReviews']);
    Route::get('/reviews/my-rejected', [ReviewController::class, 'myRejectedReviews']);
    Route::post('/review/check-existing-review', [ReviewController::class, 'checkExistingReview']);
    Route::post('/reviews', [ReviewController::class, 'updateOrCreate']);
    Route::put('/reviews/{id}', [ReviewController::class, 'updatePublished']);
    Route::post('/reviews/add-video', [SkuVideoController::class, 'addOrUpdateVideoWithBase64Data']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->where(['id' => '[0-9]+']);

    Route::get('/questions/my', [QuestionController::class, 'my']);
    Route::post('/questions', [QuestionController::class, 'store']);
    Route::delete('/questions/{id}', [QuestionController::class, 'destroy'])->where(['id' => '[0-9]+']);

    Route::get('/favorites', [FavoritesController::class, 'index']);
    Route::get('/show-favorite-skus', [FavoritesController::class, 'showFavoriteSkus']);
    Route::post('/favorites', [FavoritesController::class, 'store']);
    Route::delete('/favorites/{id}', [FavoritesController::class, 'destroy'])->where(['id' => '[0-9]+']);

    Route::get('/me', [UserController::class, 'me']);

    Route::post('/users/me', [UserController::class, 'updateMe']);
    Route::post('/users/me/avatar', [UserController::class, 'updateAvatar']);
    Route::get('/users/charges', [UserController::class, 'getUserCharges']);
    Route::get('/users/wallets', [UserController::class, 'wallets']);
    Route::post('/users/wallets', [UserController::class, 'storeWallet']);
    Route::post('/users/charge-money', [UserController::class, 'charge']);

    Route::get('/chats', [MessageController::class, 'myMessages']);
    Route::get('/chats/{id}', [MessageController::class, 'chat']);
    Route::post('/messages', [MessageController::class, 'sendMessage'])->middleware( 'throttle:3,10');



    Route::group([
        'middleware' => 'role:admin,review_editor',
        'prefix' => '/admin',
    ], function() {
        Route::get('/users/my', [AdminUserController::class, 'my']);
        Route::get('/categories', [AdminCategoryController::class, 'index']);
        Route::get('/suggest', [SearchController::class, 'index']);
        Route::get('/categories/tree', [AdminCategoryController::class, 'tree']);
        Route::get('/brands', [AdminBrandController::class, 'index']);
        Route::post('/brands', [AdminBrandController::class, 'store']);

        Route::post('/files', [FileController::class, 'storeAsForm']);

        Route::post('/skus', [AdminSkuController::class, 'store']);
        Route::post('/reviews', [AdminReviewController::class, 'store']);

        Route::group(['prefix' => '/parser/review'], function () {
            Route::get('/links', [ReviewParserController::class, 'links']);
            Route::get('/parsed-links', [ReviewParserController::class, 'parsedLinks']);
            Route::get('/parsed-links/{id}', [ReviewParserController::class, 'showParsedLink']);
            Route::post('/parsed-links/set-published/{id}', [ReviewParserController::class, 'setPublished']);
            Route::post('/parsed-links/set-archived/{id}', [ReviewParserController::class, 'setArchived']);
            Route::get('/link-option', [ReviewParserController::class, 'linkOptions']);
            Route::post('/link-option', [ReviewParserController::class, 'updateOrCreate']);
            Route::post('/parse-links', [ReviewParserController::class, 'parseLinks']);
            Route::post('/parse-by-link-ids', [ReviewParserController::class, 'parseByLinkIds']);

        });
    });



    //доступ только у админа
    Route::group([
        'middleware' => ['role:admin'],
        'prefix' => '/admin'
    ], function () {

        Route::prefix('/parser')->group(function () {

//            Route::get('/save-brand', [ParserControllerProxy::class, 'saveBrand']);
//            Route::get('/save-category', [ParserControllerProxy::class, 'saveCategory']);
//            Route::get('/save-country', [ParserControllerProxy::class, 'saveCountry']);
//            Route::get('/save-link', [ParserControllerProxy::class, 'saveLink']);
//            Route::get('/parser/save-product', [ParserControllerProxy::class, 'saveProduct']);
//            Route::get('/save-sku', [ParserControllerProxy::class, 'saveSku']);
//            Route::get('/save-price-history', [ParserControllerProxy::class, 'savePriceHistory']);
//            Route::get('/save-current-price', [ParserControllerProxy::class, 'saveCurrentPrice']);
//            Route::get('/save-store', [ParserControllerProxy::class, 'saveStore']);
//            Route::get('/save-ingredient', [ParserControllerProxy::class, 'saveIngredient']);
//            Route::get('/save-ingredient-product', [ParserControllerProxy::class, 'saveIngredientProduct']);

            Route::group(['prefix' => '/parsed-links'], function() {
                Route::get('/', [ParsingLinkController::class, 'index']);
                Route::get('/stores-with-links-count', [ParsingLinkController::class, 'storesWithLinksCount']);
                Route::delete('/delete-body-from-parsing-link/{id}', [ParsingLinkController::class, 'deleteBodyFromParsingLink']);
            });




            Route::get('/product-option', [ProductOptionController::class, 'index']);
            Route::post('/product-option', [ProductOptionController::class, 'updateOrCreate']);


            Route::get('/price-option', [PriceOptionController::class, 'index']);
            Route::post('/price-option', [PriceOptionController::class, 'updateOrCreate']);

            Route::get('/link-option', [LinkOptionController::class, 'index']);
            Route::post('/link-option', [LinkOptionController::class, 'updateOrCreate']);

            Route::post('/product/parse-product-by-link-ids', [ProductParserController::class, 'parseProductByLinkIds']);
            Route::get('/product/compress-all-uncompressed-images', [ProductParserController::class, 'compressAllUncompressedImages']);

            Route::get('/price/max-link-count-per-store', [PriceParserController::class, 'maxLinkCountPerStore']);
            Route::get('/price/get-min-hour-count', [PriceParserController::class, 'getMinHourCount']);
            Route::get('/price/get-max-hour-count', [PriceParserController::class, 'getMaxHourCount']);
            Route::post('/price/set-min-hour-count', [PriceParserController::class, 'setMinHourCount']);
            Route::post('/price/set-max-hour-count', [PriceParserController::class, 'setMaxHourCount']);
            Route::post('/price/parse-prices-by-link-ids', [PriceParserController::class, 'parsePricesByLinkIds']);
            Route::post(
                '/price/parse-prices-from-actual-price-parsing-table',
                [PriceParserController::class, 'parsePricesFromActualPriceParsingTable']
            );

            Route::post('/link/parse-links', [ProductLinksParserController::class, 'parseLinks']
            );
        });



        Route::get('/sitemap', [SitemapController::class, 'create']);
        Route::get('/settings/get-is-required-email-verification', [SettingController::class, 'getIsRequiredEmailVerification']);
        Route::post('/settings/set-is-required-email-verification', [SettingController::class, 'setIsRequiredEmailVerification']);


        //Route::delete('/images/{image}',[FileController::class, 'destroy']);


        Route::get('/articles/categories', [AdminArticleController::class, 'articleCategories']);
        Route::post('/articles/publish/{id}', [AdminArticleController::class, 'publish'])->where(['id' => '[0-9]+']);
        Route::post('/articles/withdraw-from-publication/{id}', [AdminArticleController::class, 'withdrawFromPublication']);
        Route::apiResource('/articles', AdminArticleController::class);
        Route::get('/tags/tree', [AdminTagController::class, 'tree']);
        Route::apiResource('/tags', AdminTagController::class);
        Route::apiResource('/skus', AdminSkuController::class)->except('store');
        Route::apiResource('/brands', AdminBrandController::class)->except(['index', 'store']);

        Route::apiResource('/categories', AdminCategoryController::class)->except('index');

        Route::get('/ingredients/show-available-active-ingredients-groups', [AdminIngredientController::class, 'showAvailableActiveIngredientsGroups']);
        Route::apiResource('/ingredients', AdminIngredientController::class);


        Route::apiResource('/countries', AdminCountryController::class);

        Route::post('/stores/change-price-parsing-status', [AdminStoreController::class, 'changePriceParsingStatus']);
        Route::post('/stores/change-checking-image-count-status', [AdminStoreController::class, 'changeCheckingImageCountStatus']);
        Route::apiResource('/stores', AdminStoreController::class);


        Route::get('/suppliers', [SupplierController::class, 'index']);
        Route::post('/suppliers/set-status/{id}', [SupplierController::class, 'setStatus'])->where(['id' => '[0-9]+']);



        Route::get('/rejected-reasons', [RejectedReasonController::class, 'index']);
        Route::post('/reviews/set-status/{id}', [AdminReviewController::class, 'setStatus'])->where(['id' => '[0-9]+']);
        Route::post('/reviews/reject/{id}', [AdminReviewController::class, 'reject'])->where(['id' => '[0-9]+']);
        Route::get('/reviews/dynamics', [AdminReviewController::class, 'dynamics']);
        Route::apiResource('/reviews', AdminReviewController::class)->except('store');

        Route::post('/questions/set-status/{id}', [AdminQuestionController::class, 'setStatus'])->where(['id' => '[0-9]+']);
        Route::get('/questions', [AdminQuestionController::class, 'index']);

        Route::post('/videos/set-status/{id}', [AdminSkuVideoController::class, 'setStatus'])->where(['id' => '[0-9]+']);
        Route::get('/videos', [AdminSkuVideoController::class, 'index']);


        Route::post('/comments/set-status/{id}', [AdminCommentController::class, 'setStatus'])->where(['id' => '[0-9]+']);
        Route::apiResource('/comments', AdminCommentController::class);

        Route::post('/article-comments/set-status/{id}', [AdminArticleCommentController::class, 'setStatus'])->where(['id' => '[0-9]+']);
        Route::apiResource('/article-comments', AdminArticleCommentController::class);

        Route::get('/users/show-available-roles', [AdminUserController::class, 'showAvailableRoles']);

        Route::get('/users/master-password', [AdminUserController::class, 'getMasterPassword']);
        Route::post('/users/save-bots', [AdminUserController::class, 'saveBots']);
        Route::apiResource('/users', AdminUserController::class);

        Route::get('/my-messages', [AdminMessageController::class, 'adminMessage']);
        Route::post('/messages', [AdminMessageController::class,'sendMessage'])->middleware( 'throttle:3,10');
        Route::get('/chats/{id}', [AdminMessageController::class, 'chat']);

        Route::get('/trackings', [AdminTrackingController::class, 'index']);
        Route::get('/trackings/dynamics', [AdminTrackingController::class, 'dynamics']);
        Route::get('/rating/dynamic', [AdminReviewController::class, 'dynamic']);
        Route::get('/visit-statistics', [VisitStaticticsController::class, 'index']);

        Route::get('/links/by-store', [LinkClickController::class, 'clicksByStore']);
        Route::get('/links/by-date', [LinkClickController::class, 'index']);
    });



    //доступ только у поставщика
    Route::group([
        'middleware' => ['role:supplier'],
        'prefix' => '/supplier'
    ], function () {
        Route::post('/stores/add-price-file/{id}', [SupplierStoreController::class, 'addPriceFile']);
        Route::post('/stores', [SupplierStoreController::class, 'store']);
        Route::put('/stores/{id}', [SupplierStoreController::class, 'update']);
        Route::get('/stores', [SupplierStoreController::class, 'index']);
        Route::get('/stores/my', [SupplierStoreController::class, 'my']);
    });

});



