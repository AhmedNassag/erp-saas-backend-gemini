<?php

namespace Modules\Inventory\Repositories\Product;

use App\Repositories\Base\BaseRepository;
use App\Traits\ImageTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Modules\Inventory\Models\Product\Product;
use Modules\Inventory\Repositories\Product\ProductInterface;
use Modules\Inventory\Resources\Product\ProductResource;
use Modules\Core\Repositories\Warehouse\WarehouseRepository;
use Modules\Inventory\Repositories\ProductVariant\ProductVariantRepository;
use Modules\Inventory\Repositories\ProductWarehouse\ProductWarehouseRepository;

class ProductRepository extends BaseRepository implements ProductInterface
{
    use ImageTrait;

    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Product();
    }

    protected function getResourceClass(): string
    {
        return ProductResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Products';
    }

    protected function getSingularName(): string
    {
        return 'Product';
    }

    //return used repositories
    protected function getWarehouseRepository()    {
        return new WarehouseRepository();
    }

    protected function getProductVariantRepository()    {
        return new ProductVariantRepository();
    }

    protected function getProductWarehouseRepository()    {
        return new ProductWarehouseRepository();
    }



    public function store($request)
    {
        try {
            DB::beginTransaction();

            $product = $this->getModel()->create($request->validated());

            if ($request->hasFile('image')) {
                $product->clearMediaCollection('product');
                $this->uploadMedia($product, 'product', $request->file('image'));
            }
            if ($request->hasFile('images')) {
                $product->clearMediaCollection('product_images');
                foreach ($request->file('images') as $image) {
                    $this->uploadMedia($product, 'product_images', $image);
                }
            }

            //store product variants
            $isVariant = $request->input('is_variant') === 'true' || $request->input('is_variant') === true || $request->input('is_variant') == 1;
            if ($isVariant && $request->has('variants')) {
                $this->storeProductVariants($request, $product);
            }

            //store product warehouse
            $this->storeProductWarehouse($request, $product);

            DB::commit();

            return (new \App\Traits\API)
                ->isOk(__('Stored Successfully'))
                ->build();
        } catch (\Exception $e) {
            DB::rollBack();
            return (new \App\Traits\API)
                ->isError('An Error occured')
                ->setStatus(500)
                ->build();
        }
    }



    public function update($id, $request)
    {
        try {
            DB::beginTransaction();

            $product = $this->getModel()->findOrFail($id);
            $product->update($request->validated());

            if ($request->hasFile('image')) {
                $product->clearMediaCollection('product');
                $this->uploadMedia($product, 'product', $request->file('image'));
            }
            if ($request->hasFile('images')) {
                $product->clearMediaCollection('product_images');
                foreach ($request->file('images') as $image) {
                    $this->uploadMedia($product, 'product_images', $image);
                }
            }

            // Store Variants Product
            $isVariant = $request->input('is_variant') === 'true' || $request->input('is_variant') === true || $request->input('is_variant') == 1;
            if ($isVariant && $request->has('variants')) {
                // $oldVariants = ProductVariant::where('product_id', $id)->whereNull('deleted_at')->get();
                $oldVariants = $this->getProductVariantRepository()->getModel()->where('product_id', $id)->whereNull('deleted_at')->get();
                // $warehouses  = Warehouse::whereNull('deleted_at')->pluck('id')->toArray();
                $warehouses  = $this->getWarehouseRepository()->getModel()->whereNull('deleted_at')->pluck('id')->toArray();

                if ($oldVariants->isNotEmpty()) {
                    $newVariants_id = [];
                    $var = 'id';
                    foreach ($request['variants'] as $new_id) {
                        if (array_key_exists($var, $new_id)) {
                            $newVariants_id[] = $new_id['id'];
                        } else {
                            $newVariants_id[] = 0;
                        }
                    }
                    foreach ($oldVariants as $key => $value) {
                        $oldVariants_id[] = $value->id;
                        // Delete Variant
                        if (!in_array($oldVariants_id[$key], $newVariants_id)) {
                            // $productVariant             = ProductVariant::findOrFail($value->id);
                            $productVariant             = $this->getProductVariantRepository()->getModel()->findOrFail($value->id);
                            $productVariant->deleted_at = Carbon::now();
                            $productVariant->save();

                            // $productWarehouse = ProductWarehouse::where('product_variant_id', $value->id)->update(['deleted_at' => Carbon::now()]);
                            $productWarehouse = $this->getProductWarehouseRepository()->getModel()->where('product_variant_id', $value->id)->update(['deleted_at' => Carbon::now()]);
                        }
                    }
                    foreach ($request['variants'] as $key => $variant) {
                        if (array_key_exists($var, $variant)) {
                            // $productVariantData = new ProductVariant;
                            $productVariantData = $this->getProductVariantRepository()->getModel();
                            //-- Field Required
                            $productVariantData->product_id     = $variant['product_id'];
                            $productVariantData->name           = $variant['text'];
                            $productVariantData->qty            = $variant['qty'];
                            $productVariantUpdate['product_id'] = $variant['product_id'];
                            $productVariantUpdate['name']       = $variant['text'];
                            $productVariantUpdate['qty']        = $variant['qty'];

                        } else {
                            // $productVariantData = new ProductVariant;
                            $productVariantData = $this->getProductVariantRepository()->getModel();
                            //-- Field Required
                            $productVariantData->product_id     = $id;
                            $productVariantData->name           = $variant['text'];
                            $productVariantData->qty            = 0.00;
                            $productVariantUpdate['product_id'] = $id;
                            $productVariantUpdate['name']       = $variant['text'];
                            $productVariantUpdate['qty']        = 0.00;
                        }

                        if (!in_array($newVariants_id[$key], $oldVariants_id)) {
                            $productVariantData->save();
                            //--Store Product warehouse
                            if ($warehouses) {
                                $productWarehouse= [];
                                foreach ($warehouses as $warehouse) {
                                    $productWarehouse[] = [
                                        'product_id'         => $id,
                                        'warehouse_id'       => $warehouse,
                                        'product_variant_id' => $productVariantData->id,
                                    ];

                                }
                                // ProductWarehouse::insert($productWarehouse);
                                $this->getProductWarehouseRepository()->getModel()->insert($productWarehouse);
                            }
                        } else {
                            // ProductVariant::where('id', $variant['id'])->update($productVariantUpdate);
                            $this->getProductVariantRepository()->getModel()->where('id', $variant['id'])->update($productVariantUpdate);
                        }
                    }
                } else {
                    // $producttWarehouse = ProductWarehouse::where('product_id', $id)->update(['deleted_at' => Carbon::now()]);
                    $this->getProductWarehouseRepository()->getModel()->where('product_id', $id)->update(['deleted_at' => Carbon::now()]);
                    foreach ($request['variants'] as $variant) {
                        $productWarehouseData = [];
                        // $productVariantData   = new ProductVariant;
                        $productVariantData   = $this->getProductVariantRepository()->getModel();
                        //-- Field Required
                        $productVariantData->product_id = $id;
                        $productVariantData->name       = $variant['text'];
                        $productVariantData->save();
                        //-- Store Product warehouse
                        if ($warehouses) {
                            foreach ($warehouses as $warehouse) {
                                $productWarehouseData[] = [
                                    'product_id'         => $id,
                                    'warehouse_id'       => $warehouse,
                                    'product_variant_id' => $productVariantData->id,
                                ];
                            }
                            // ProductWarehouse::insert($productWarehouseData);
                            $this->getProductWarehouseRepository()->getModel()->insert($productWarehouseData);
                        }
                    }
                }

            } else {
                if ($oldVariants->isNotEmpty()) {
                    foreach ($oldVariants as $oldVariant) {
                        // $varOld = ProductVariant::where('product_id', $oldVariant['product_id'])->whereNull('deleted_at')->first();
                        $varOld = $this->getProductVariantRepository()->getModel()->where('product_id', $oldVariant['product_id'])->whereNull('deleted_at')->first();
                        $varOld->deleted_at = Carbon::now();
                        $varOld->save();

                        // $producttWarehouse = ProductWarehouse::where('product_variant_id', $oldVariant['id'])->update(['deleted_at' => Carbon::now()]);
                        $this->getProductWarehouseRepository()->getModel()->where('product_variant_id', $oldVariant['id'])->update(['deleted_at' => Carbon::now()]);
                    }
                    if ($warehouses) {
                        foreach ($warehouses as $warehouse) {
                            $productWarehouse[] = [
                                'product_id'         => $id,
                                'warehouse_id'       => $warehouse,
                                'product_variant_id' => null,
                            ];
                        }
                        // ProductWarehouse::insert($productWarehouse);
                        $this->getProductWarehouseRepository()->getModel()->insert($productWarehouse);
                    }
                }
            }
            
             //update product variants
            //  $this->updateProductVariants($id, $request);

             //update product warehouse
            //  $this->updateProductWarehouse($id, $request);

            DB::commit();

            return (new \App\Traits\API)
                ->isOk(__('Updated Successfully'))
                ->build();
        } catch (\Exception $e) {
            DB::rollBack();
            return (new \App\Traits\API)
                ->isError('An Error occured')
                ->setStatus(500)
                ->build();
        }
    }



    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $product = $this->getModel()->findOrFail($id);

            $singleMedia = $product->getMedia('product')->first();
            $multiMedia  = $product->getMedia('product_images')->all();
            if ($singleMedia) {
                $product->clearMediaCollection('product');
                $file_name = $singleMedia->file_name;
                $img_id    = $singleMedia->id;
                if ($img_id && $file_name) {
                    if (File::exists(public_path('storage/' . $img_id . '/' . $file_name))) {
                        unlink(public_path('storage/' . $img_id . '/' . $file_name));
                    }
                }
            }
            if ($multiMedia) {
                $product->clearMediaCollection('product_images');
                foreach ($multiMedia as $media) {
                    $file_name = $media->file_name;
                    $img_id    = $media->id;
                    if ($img_id && $file_name) {
                        if (File::exists(public_path('storage/' . $img_id . '/' . $file_name))) {
                            unlink(public_path('storage/' . $img_id . '/' . $file_name));
                        }
                    }
                }
            }

            $product->delete();

            // destroy product variants
            $this->destroyProductVariants($id);

            // destroy product warehouse
            $this->destroyProductWarehouse($id);

            DB::commit();

            return (new \App\Traits\API)
                ->isOk(__('Destroyed Successfully'))
                ->build();
        } catch (\Exception $e) {
            DB::rollBack();
            return (new \App\Traits\API)
                ->isError('An Error occured')
                ->setStatus(500)
                ->build();
        }
    }



    protected function storeProductVariants($request, $product)
    {
        foreach ($request->input('variants') as $variant) {
            $productVariantsData[] = [
                'product_id' => $product->id,
                'name'       => is_array($variant) ? $variant['text'] : $variant,
            ];
        }
        // ProductVariant::insert($productVariantsData);
        $this->getProductVariantRepository()->getModel()->insert($productVariantsData);
    }



    protected function storeProductWarehouse($request, $product)
    {
        // $warehouses = Warehouse::whereNull('deleted_at')->pluck('id')->toArray();
        $warehouses = $this->getWarehouseRepository()->getModel()->whereNull('deleted_at')->pluck('id')->toArray();
        if ($warehouses) {
            $isVariant = $request->input('is_variant') === 'true' || $request->input('is_variant') === true || $request->input('is_variant') == 1;
            foreach ($warehouses as $warehouse) {
                if ($isVariant) {
                    // $productVariants = ProductVariant::where('product_id', $product->id)->whereNull('deleted_at')->get();
                    $productVariants = $this->getProductVariantRepository()->getModel()->where('product_id', $product->id)->whereNull('deleted_at')->get();
                    foreach ($productVariants as $productVariant) {
                        $productWarehouse[] = [
                            'product_id'         => $product->id,
                            'warehouse_id'       => $warehouse,
                            'product_variant_id' => $productVariant->id,
                        ];
                    }
                } else {
                    $productWarehouse[] = [
                        'product_id'   => $product->id,
                        'warehouse_id' => $warehouse,
                    ];
                }
            }
            // ProductWarehouse::insert($productWarehouse);
            $this->getProductWarehouseRepository()->getModel()->insert($productWarehouse);
        }
    }



    protected function destroyProductVariants($productId)
    {
        // $productVariants = ProductVariant::where('product_id', $productId)->whereNull('deleted_at')->get();
        $productVariants = $this->getProductVariantRepository()->getModel()->where('product_id', $productId)->whereNull('deleted_at')->get();
        foreach ($productVariants as $productVariant) {
            $productVariant->deleted_at = Carbon::now();
            $productVariant->save();
        }
    }


    protected function destroyProductWarehouse($productId)
    {
        // ProductWarehouse::where('product_id', $productId)->whereNull('deleted_at')->update(['deleted_at' => Carbon::now()]);
        $this->getProductWarehouseRepository()->getModel()->where('product_id', $productId)->whereNull('deleted_at')->update(['deleted_at' => Carbon::now()]);
    }

    public function show($id, array $with = [])
    {
        $product           = $this->getModel()->with(['category', 'brand', 'unit', 'unitSale', 'unitPurchase', 'variants', 'productWarehouses.warehouse', 'productWarehouses.productVariant'])->findOrFail($id);
        $productWarehouses = $product->productWarehouses->whereNull('deleted_at')->groupBy('warehouse_id');

        $CountQTY          = [];
        $CountQTY_variants = [];
        foreach ($productWarehouses as $warehouseId => $pwItems) {
            $warehouse   = $pwItems->first()->warehouse;
            $noVariant   = $pwItems->whereNull('product_variant_id');
            $withVariant = $pwItems->whereNotNull('product_variant_id');

            if ($noVariant->isNotEmpty()) {
                $CountQTY[] = [
                    'mag' => $warehouse->name,
                    'qty' => $noVariant->sum('qty'),
                ];
            }

            foreach ($withVariant as $pw) {
                $CountQTY_variants[] = [
                    'mag'     => $warehouse->name,
                    'variant' => $pw->productVariant->name ?? '',
                    'qty'     => $pw->qty,
                ];
            }
        }

        return (new \App\Traits\API)->isOk(__('Product Details'))
            ->setData([
                'product'           => new ProductResource($product),
                'CountQTY'          => $CountQTY,
                'CountQTY_variants' => $CountQTY_variants,
            ])
            ->build();
    }



    public function getProductsByWarehouseId($warehouseId)
    {
        // $productIds = ProductWarehouse::where('warehouse_id', $warehouseId)
        //     ->whereNull('deleted_at')
        //     ->pluck('product_id')
        //     ->unique();

        // return Product::whereIn('id', $productIds)
        //     ->whereNull('deleted_at')
        //     ->where('status', 1)
        //     ->select('id', 'code', 'Type_barcode', 'name')
        //     ->get();

        
        $data = [];
        
        // $productWarehouseData = ProductWarehouse::with('warehouse', 'product', 'productVariant')->where('warehouse_id', $warehouseId)->where('deleted_at', '=', null)->get();
        $productWarehouseData = $this->getProductWarehouseRepository()->getModel()->with('warehouse', 'product', 'productVariant')->where('warehouse_id', $warehouseId)->where('deleted_at', '=', null)->get();
        
        foreach ($productWarehouseData as $productWarehouse) {

            if ($productWarehouse->product_variant_id) {
                $item['product_variant_id'] = $productWarehouse->product_variant_id;
                $item['code']               = $productWarehouse['productVariant']->name . '-' . $productWarehouse['product']->code;
                $item['Variant']            = $productWarehouse['productVariant']->name;
            } else {
                $item['product_variant_id'] = null;
                $item['Variant']            = null;
                $item['code']               = $productWarehouse['product']->code;
            }

            $item['id']               = $productWarehouse->product_id;
            $item['name']             = $productWarehouse['product']->name;
            $item['barcode']          = $productWarehouse['product']->code;
            $item['Type_barcode']     = $productWarehouse['product']->Type_barcode;
            $item['cost']             = $productWarehouse['product']->cost;
            $item['unit_purchase_id'] = $productWarehouse['product']->unit_purchase_id;
            $firstimage               = explode(',', $productWarehouse['product']->image);
            $item['image']            = $firstimage[0];

            if ($productWarehouse['product']['unitSale']->operator == '/') {
                $item['qte_sale'] = $productWarehouse->qty * $productWarehouse['product']['unitSale']->operator_value;
                $price            = $productWarehouse['product']->price / $productWarehouse['product']['unitSale']->operator_value;
            } else {
                $item['qte_sale'] = $productWarehouse->qty / $productWarehouse['product']['unitSale']->operator_value;
                $price            = $productWarehouse['product']->price * $productWarehouse['product']['unitSale']->operator_value;
            }

            if ($productWarehouse['product']['unitPurchase']->operator == '/') {
                $item['qte_purchase'] = round($productWarehouse->qty * $productWarehouse['product']['unitPurchase']->operator_value, 5);
            } else {
                $item['qte_purchase'] = round($productWarehouse->qty / $productWarehouse['product']['unitPurchase']->operator_value, 5);
            }

            $item['qty']          = $productWarehouse->qty;
            $item['unitSale']     = $productWarehouse['product']['unitSale']->ShortName;
            $item['unitPurchase'] = $productWarehouse['product']['unitPurchase']->ShortName;

            if ($productWarehouse['product']->TaxNet !== 0.0) {
                //Exclusive
                if ($productWarehouse['product']->tax_method == '1') {
                    $tax_price = $price * $productWarehouse['product']->TaxNet / 100;
                    $item['Net_price'] = $price + $tax_price;
                    // Inxclusive
                } else {
                    $item['Net_price'] = $price;
                }
            } else {
                $item['Net_price'] = $price;
            }

            $data[] = $item;
        }

        return response()->json($data);   
    }
}
