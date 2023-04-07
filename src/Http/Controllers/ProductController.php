<?php

namespace Fleetbase\Storefront\Http\Controllers\Storefront;

use ErrorException;
use Exception;
use Fleetbase\Http\Controllers\RESTController;
use Fleetbase\Imports\ProductsImport;
use Fleetbase\Jobs\DownloadProductImageUrl;
use Fleetbase\Models\Category;
use Fleetbase\Models\File;
use Fleetbase\Models\Storefront\Product;
use Fleetbase\Models\Storefront\ProductAddonCategory;
use Fleetbase\Models\Storefront\ProductVariant;
use Fleetbase\Models\Storefront\ProductVariantOption;
use Fleetbase\Models\Storefront\Store;
use Fleetbase\Support\Resp;
use Fleetbase\Support\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends RESTController
{
    /**
     * The resource to query
     *
     * @var string
     */
    public string $resource = 'products';

    /**
     * The namespace for the resource
     *
     * @var string
     */
    public string $namespace = 'Fleetbase\\Models\\Storefront\\';

    /**
     * Creates a record with request payload
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createRecord(Request $request)
    {
        return $this->model::createRecordFromRequest($request, null, function (&$request, Product &$product) {
            $addonCategories = $request->input('product.addon_categories');
            $variants = $request->input('product.variants');
            $files = $request->input('product.files');

            // save addon categories
            foreach ($addonCategories as $addonCategory) {
                $addonCategory['product_uuid'] = $product->uuid;

                ProductAddonCategory::create(Arr::except($addonCategory, ['category']));
            }

            // save product variants
            foreach ($variants as $variant) {
                $variant['created_by_uuid'] = session('user');
                $variant['company_uuid'] = session('company');
                $variant['product_uuid'] = $product->uuid;

                $productVariant = ProductVariant::create(Arr::except($variant, ['options']));

                foreach ($variant['options'] as $option) {
                    $option['product_variant_uuid'] = $productVariant->uuid;
                    ProductVariantOption::create($option);
                }
            }

            // set keys on files
            foreach ($files as $file) {
                $fileRecord = File::where('uuid', $file['uuid'])->first();
                $fileRecord->setKey($product);
            }
        });
    }

    /**
     * List all activity options for current order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function processImports(Request $request)
    {
        $store = $request->input('store');
        $category = $request->input('category');
        $files = $request->input('files');
        $files = File::whereIn('uuid', $files)->get();
        $validFileTypes = ['csv', 'tsv', 'xls', 'xlsx'];
        $imports = collect();

        if ($category) {
            $category = Category::find($category);
        }

        if ($store) {
            $store = Store::find($store);
        }

        foreach ($files as $file) {
            // validate file type
            if (!Str::endsWith($file->path, $validFileTypes)) {
                return Resp::error('Invalid file uploaded, must be one of the following: ' . implode(', ', $validFileTypes));
            }

            try {
                $data = Excel::toArray(new ProductsImport(), $file->path, 's3');
            } catch (Exception $e) {
                return Resp::error('Invalid file, unable to proccess.');
            }

            $data = Arr::first($data);
            $imports = $imports->merge($data);
        }

        // track imported products
        $products = [];

        foreach ($imports as $row) {
            if (empty($row) || empty(array_values($row))) {
                continue;
            }

            // $importId = (string) Str::uuid();
            $name = Utils::or($row, ['name', 'product_name', 'entry_name', 'entity_name', 'entity', 'item_name', 'item', 'service', 'service_name']);
            $description = Utils::or($row, ['description', 'product_description', 'details', 'info', 'about', 'item_description']);
            $tags = Utils::or($row, ['tags']);
            $sku = Utils::or($row, ['sku', 'internal_id', 'stock_number']);
            $price = Utils::or($row, ['price', 'cost', 'value']);
            $salePrice = Utils::or($row, ['sale_price', 'sale_cost', 'sale_value']);
            $isService = Utils::or($row, ['is_service'], false);
            $isBookable = Utils::or($row, ['is_bookable', 'bookable'], false);
            $isOnSale = Utils::or($row, ['on_sale', 'is_on_sale'], false);
            $isAvailable = Utils::or($row, ['available', 'is_available'], true);
            $isRecommended = Utils::or($row, ['recommended', 'is_recommended'], false);
            $canPickup = Utils::or($row, ['can_pickup', 'is_pickup', 'is_pickup_only'], false);
            $youtubeUrls = Utils::or($row, ['youtube', 'youtube_urls', 'youtube_videos']);
            $images = Utils::or($row, ['photos', 'images', 'image', 'photo', 'primary_image', 'product_image', 'thumbnail', 'photo1', 'image1']);

            $products[] = $product = Product::create(
                [
                    'company_uuid' => session('company'),
                    'created_by_uuid' => session('user'),
                    'store_uuid' => $store->uuid,
                    'name' => Utils::unicodeDecode($name),
                    'description' => Utils::unicodeDecode($description),
                    'sku' => $sku,
                    'tags' => explode(',', $tags),
                    'youtube_urls' => explode(',', $youtubeUrls),
                    'price' => $price,
                    'sale_price' => $salePrice,
                    'currency' => $store->currency,
                    'is_service' => $isService,
                    'is_bookable' => $isBookable,
                    'is_on_sale' => $isOnSale,
                    'is_available' => $isAvailable,
                    'is_recommended' => $isRecommended,
                    'can_pickup' => $canPickup,
                    'category_uuid' => $category ? $category->uuid : null,
                    'status' => 'published'
                ]
            );

            $images = explode(',', $images);

            foreach ($images as $imageUrl) {
                dispatch(new DownloadProductImageUrl($product, $imageUrl));
            }
        }

        return response()->json($products);
    }

    /**
     * Updates a record with request payload
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateRecord(Request $request)
    {
        return $this->model::updateRecordFromRequest($request, function (&$request, Product &$product) {
            $productAddonCategories = $request->input('product.addon_categories');
            $variants = $request->input('product.variants');

            // update addon categories
            foreach ($productAddonCategories as $productAddonCategory) {
                if (!empty($productAddonCategory['uuid'])) {
                    ProductAddonCategory::where('uuid', $productAddonCategory['uuid'])->update(Arr::except($productAddonCategory, ['uuid', 'name', 'category']));
                    continue;
                }

                // add new addon category
                $productAddonCategory['product_uuid'] = $product->uuid;
                ProductAddonCategory::create(Arr::except($productAddonCategory, ['category']));
            }

            // update product variants
            foreach ($variants as $variant) {
                if (!empty($variant['uuid'])) {
                    // update product variante
                    ProductVariant::where('uuid', $variant['uuid'])->update(Arr::except($variant, ['uuid', 'options']));

                    // update product variant options
                    foreach ($variant['options'] as $option) {
                        if (!empty($option['uuid'])) {
                            // make sure additional cost is always numbers only
                            if (isset($option['additional_cost'])) {
                                $option['additional_cost'] = Utils::numbersOnly($option['additional_cost']);
                            }

                            $updateAttrs = Arr::except($option, ['uuid']);

                            ProductVariantOption::where('uuid', $option['uuid'])->update($updateAttrs);
                            continue;
                        }

                        $option['product_variant_uuid'] = $variant['uuid'];
                        ProductVariantOption::create($option);
                    }
                    continue;
                }

                // create new variant
                $variant['created_by_uuid'] = session('user');
                $variant['company_uuid'] = session('company');
                $variant['product_uuid'] = $product->uuid;

                $productVariant = ProductVariant::create(Arr::except($variant, ['options']));

                foreach ($variant['options'] as $option) {
                    $option['product_variant_uuid'] = $productVariant->uuid;
                    ProductVariantOption::create($option);
                }
            }
        });
    }
}
