<?php

namespace Modules\Desktopapp\Http\Controllers\Api;

use App\Models\Product;
use App\Models\SellingPriceGroup;
use App\Models\Transaction;
use App\Models\Variation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Desktopapp\Transformers\CommonResource;
use Modules\Desktopapp\Transformers\NewProductResource;
use Modules\Desktopapp\Transformers\VariationResource;

/**
 * @group Product management
 * @authenticated
 *
 * APIs for managing products
 */
class NewProductController extends ApiController
{
    public function index()
    {
        $user = Auth::user();

        $business_id = $user->business_id;

        $filters = request()->only(['brand_id', 'category_id', 'location_id', 'sub_category_id', 'per_page']);
        //$filters['selling_price_group'] = request()->input('selling_price_group') == 1 ? true : false;
        $filters['selling_price_group'] = true;

        $search = request()->only(['sku', 'name']);

        //order
        $order_by = null;
        $order_direction = null;

        if(!empty(request()->input('order_by'))){
            $order_by = in_array(request()->input('order_by'), ['product_name', 'newest']) ? request()->input('order_by') : null;
            $order_direction = in_array(request()->input('order_direction'), ['asc', 'desc']) ? request()->input('order_direction') : 'asc';
        }
        
        $products = $this->__getProducts($business_id, $filters, $search, true, $order_by, $order_direction); 

        return NewProductResource::collection($products);
    }

    public function show($product_ids)
    {
        $user = Auth::user();

        // if (!$user->can('api.access')) {
        //     return $this->respondUnauthorized();
        // }

        $business_id = $user->business_id;
        $filters['selling_price_group'] = request()->input('selling_price_group') == 1 ? true : false;

        $filters['product_ids'] = explode(',', $product_ids);

        $products = $this->__getProducts($business_id, $filters);

        return NewProductResource::collection($products);
    }

    /**
     * Function to query product
     * @return Response
     */
    private function __getProducts($business_id, $filters = [], $search = [], $pagination = false, $order_by = null, $order_direction = null)
    {
        $query = Product::where('business_id', $business_id);

        $with = ['product_variations.variations.variation_location_details', 'brand:id,name', 'unit:id,business_id,actual_name,short_name', 'unit', 'category:id,name', 'sub_category', 'product_tax', 'product_variations.variations.media', 'product_locations'];

        if (!empty($filters['category_id'])) {
            $category_ids = explode(',', $filters['category_id']);
            $query->whereIn('category_id', $category_ids);
        }

        if (!empty($filters['sub_category_id'])) {
            $sub_category_id = explode(',', $filters['sub_category_id']);
            $query->whereIn('sub_category_id', $sub_category_id);
        }

        if (!empty($filters['brand_id'])) {
            $brand_ids = explode(',', $filters['brand_id']);
            $query->whereIn('brand_id', $brand_ids);
        }

        if (!empty($filters['selling_price_group']) && $filters['selling_price_group'] == true) {
            $with[] = 'product_variations.variations.group_prices';
        }
        if (!empty($filters['location_id'])) {
            $location_id = $filters['location_id'];
            $query->whereHas('product_locations', function($q) use($location_id) {
                $q->where('product_locations.location_id', $location_id);
            });

            $with['product_variations.variations.variation_location_details'] = function($q) use($location_id) {
                $q->where('location_id', $location_id);
            };

            $with['product_locations'] = function($q) use($location_id) {
                $q->where('product_locations.location_id', $location_id);
            };
        }

        if (!empty($filters['product_ids'])) {
            $query->whereIn('id', $filters['product_ids']);
        }

        if (!empty($search)) {
            $query->where(function ($query) use ($search) {

                if (!empty($search['name'])) {
                    $query->where('products.name', 'like', '%' . $search['name'] .'%');
                }
                
                if (!empty($search['sku'])) {
                    $sku = $search['sku'];
                    $query->orWhere('sku', 'like', '%' . $sku .'%');
                    $query->orWhereHas('variations', function($q) use($sku) {
                        $q->where('variations.sub_sku', 'like', '%' . $sku .'%');
                    });
                }
            });
        }

        //Order by
        if(!empty($order_by)){
            if($order_by == 'product_name'){
                $query->orderBy('products.name', $order_direction);
            }

            if($order_by == 'newest'){
                $query->orderBy('products.id', $order_direction);
            }
        }

        $query->with($with);

        $perPage = !empty($filters['per_page']) ? $filters['per_page'] : $this->perPage;
        if ($pagination && $perPage != -1) {
            $products = $query->paginate($perPage);
            $products->appends(request()->query());
        } else{
            $products = $query->get();
        }

        return $products;
    }

   
    public function listVariations($variation_ids = null)
    {
        $user = Auth::user();

        $business_id = $user->business_id;

        $query = Variation::join('products AS p', 'variations.product_id', '=', 'p.id')
                ->join('product_variations AS pv', 'variations.product_variation_id', '=', 'pv.id')
                ->leftjoin('units', 'p.unit_id', '=', 'units.id')
                ->leftjoin('tax_rates as tr', 'p.tax', '=', 'tr.id')
                ->leftjoin('brands', function ($join) {
                    $join->on('p.brand_id', '=', 'brands.id')
                        ->whereNull('brands.deleted_at');
                })
                ->leftjoin('categories as c', 'p.category_id', '=', 'c.id')
                ->leftjoin('categories as sc', 'p.sub_category_id', '=', 'sc.id')
                ->where('p.business_id', $business_id)
                ->select(
                    'variations.id',
                    'variations.name as variation_name',
                    'variations.sub_sku',
                    'p.id as product_id',
                    'p.name as product_name',
                    'p.sku',
                    'p.type as type',
                    'p.business_id', 
                    'p.barcode_type',
                    'p.expiry_period',
                    'p.expiry_period_type',
                    'p.enable_sr_no',
                    'p.weight',
                    'p.product_custom_field1',
                    'p.product_custom_field2',
                    'p.product_custom_field3',
                    'p.product_custom_field4',
                    'p.image as product_image',
                    'p.product_description',
                    'p.warranty_id',
                    'p.brand_id',
                    'brands.name as brand_name',
                    'p.unit_id',
                    'p.enable_stock',
                    'p.not_for_selling',
                    'units.short_name as unit_name',
                    'units.allow_decimal as unit_allow_decimal',
                    'p.category_id',
                    'c.name as category',
                    'p.sub_category_id',
                    'sc.name as sub_category',
                    'p.tax as tax_id',
                    'p.tax_type',
                    'tr.name as tax_name',
                    'tr.amount as tax_amount',
                    'variations.product_variation_id',
                    'variations.default_purchase_price',
                    'variations.dpp_inc_tax',
                    'variations.profit_percent',
                    'variations.default_sell_price',
                    'variations.sell_price_inc_tax',
                    'pv.id as product_variation_id',
                    'pv.name as product_variation_name'
                );

        $with = [
                    'variation_location_details', 
                    'media', 
                    'group_prices',
                    'product',
                    'product.product_locations'
                ];

        if (!empty(request()->input('category_id'))) {
            $query->where('category_id', request()->input('category_id'));
        }

        if (!empty(request()->input('sub_category_id'))) {
            $query->where('p.sub_category_id', request()->input('sub_category_id'));
        }

        if (!empty(request()->input('brand_id'))) {
            $query->where('p.brand_id', request()->input('brand_id'));
        }

        if (request()->has('not_for_selling')) {
            $not_for_selling = request()->input('not_for_selling') == 1 ? 1 : 0;
            $query->where('p.not_for_selling', $not_for_selling);
        }
        $filters['selling_price_group'] = request()->input('selling_price_group') == 1 ? true : false;

        if (!empty(request()->input('location_id'))) {
            $location_id = request()->input('location_id');
            $query->whereHas('product.product_locations', function($q) use($location_id) {
                $q->where('product_locations.location_id', $location_id);
            });

            $with['variation_location_details'] = function($q) use($location_id) {
                $q->where('location_id', $location_id);
            };

            $with['product.product_locations'] = function($q) use($location_id) {
                $q->where('product_locations.location_id', $location_id);
            };
        }

        $search = request()->only(['sku', 'name']);

        if (!empty($search)) {
            $query->where(function ($query) use ($search) {

                if (!empty($search['name'])) {
                    $query->where('p.name', 'like', '%' . $search['name'] .'%');
                }
                
                if (!empty($search['sku'])) {
                    $sku = $search['sku'];
                    $query->orWhere('p.sku', 'like', '%' . $sku .'%')
                        ->where('variations.sub_sku', 'like', '%' . $sku .'%');
                }
            });
        }

        //filter by variations ids
        if (!empty($variation_ids)) {
            $variation_ids = explode(',', $variation_ids);
            $query->whereIn('variations.id', $variation_ids);
        }

        //filter by product ids
        if (!empty(request()->input('product_id'))) {
            $product_ids = explode(',', request()->input('product_id'));
            $query->whereIn('p.id', $product_ids);
        }

        $query->with($with);

        $perPage = !empty(request()->input('per_page')) ? request()->input('per_page') : $this->perPage;
        if ($perPage == -1) {
            $variations = $query->get();
        } else {
            //paginate
            $variations = $query->paginate($perPage);
            $variations->appends(request()->query());
        }

        return VariationResource::collection($variations);
    }

    
    public function getSellingPriceGroup()
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $price_groups = SellingPriceGroup::where('business_id', $business_id)
                                        ->get();

        return CommonResource::collection($price_groups);
    }

    public function recentProducts(Request $request)
    {
        $user = Auth::user();

        $business_id = $user->business_id;
        $filter_date = $request->input('filter_date');
        
        $products = $this->__getRecentProducts($business_id, $filter_date); 

        return NewProductResource::collection($products);
    }

    /**
     * Function to query product
     * @return Response
     */
    private function __getRecentProducts($business_id, $filter_date)
    {

        $sellTransactions = Transaction::leftJoin('transaction_sell_lines AS ts', 'transactions.id', '=', 'ts.transaction_id')
                                            ->where('transactions.business_id', $business_id)
                                            ->where('transactions.updated_at', '>=', $filter_date)
                                            ->whereIn('transactions.type', ['sell', 'sell_transfer'])
                                            ->orderBy('transactions.updated_at', 'desc')
                                            ->pluck('ts.product_id')
                                            ->all();

        $stockAdjustmentTransactions = Transaction::leftJoin('stock_adjustment_lines AS sa', 'transactions.id', '=', 'sa.transaction_id')
                                            ->where('transactions.business_id', $business_id)
                                            ->where('transactions.type', 'stock_adjustment')
                                            ->where('transactions.updated_at', '>=', $filter_date)
                                            ->orderBy('transactions.updated_at', 'desc')
                                            ->pluck('sa.product_id')
                                            ->all();

        $purchaseTransactions = Transaction::leftJoin('purchase_lines AS pl', 'transactions.id', '=', 'pl.transaction_id')
                                            ->where('transactions.business_id', $business_id)
                                            ->where('transactions.updated_at', '>=', $filter_date)
                                            ->whereIn('transactions.type', ['purchase', 'opening_stock'])
                                            ->orderBy('transactions.updated_at', 'desc')
                                            ->take(200)
                                            ->pluck('pl.product_id')
                                            ->all();


        $productChangeTransactions = Product::where('business_id', $business_id)->where('updated_at', '>=', $filter_date)->orderBy('updated_at', 'desc')->get(['id'])->pluck('product_id')->toArray();

        $productIds = array_unique(array_values(array_merge($sellTransactions, $stockAdjustmentTransactions, $purchaseTransactions, $productChangeTransactions)));

        $query = Product::where('business_id', $business_id)->whereIn('id', $productIds);

        $with = ['product_variations.variations.variation_location_details', 'brand:id,name', 'unit:id,business_id,actual_name,short_name', 'unit', 'category:id,name', 'sub_category', 'product_tax', 'product_variations.variations.media', 'product_variations.variations.group_prices', 'product_locations'];
        

        $query->with($with);

        $products = $query->take(200)->get();

        return $products;
    }
}
