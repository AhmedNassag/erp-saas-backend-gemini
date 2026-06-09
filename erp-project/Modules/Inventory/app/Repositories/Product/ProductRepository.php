<?php

namespace Modules\Inventory\Repositories\Product;

use App\Repositories\Base\BaseRepository;
use App\Traits\ImageTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Modules\Inventory\Models\Product\Product;
use Modules\Inventory\Models\ProductVariant\ProductVariant;
use Modules\Inventory\Models\ProductWarehouse\ProductWarehouse;
use Modules\Inventory\Repositories\Product\ProductInterface;
use Modules\Inventory\Resources\Product\ProductResource;
use Modules\Core\Models\Warehouse\Warehouse;

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

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $product = $this->getModel()->create($request->validated());

            $isVariant = $request->input('is_variant') === 'true' || $request->input('is_variant') === true || $request->input('is_variant') == 1;

            if ($isVariant && $request->has('variants')) {
                $variantData = [];
                foreach ($request->input('variants') as $variant) {
                    $variantName = is_string($variant) ? $variant : ($variant['text'] ?? $variant['name'] ?? '');
                    if ($variantName) {
                        $variantData[] = [
                            'product_id' => $product->id,
                            'name'       => $variantName,
                            'qty'        => 0.00,
                        ];
                    }
                }
                if ($variantData) {
                    ProductVariant::insert($variantData);
                }
            }

            $warehouses = Warehouse::whereNull('deleted_at')->pluck('id')->toArray();
            if ($warehouses) {
                $pwData = [];
                if ($isVariant) {
                    $variants = ProductVariant::where('product_id', $product->id)->whereNull('deleted_at')->get();
                    foreach ($warehouses as $warehouseId) {
                        foreach ($variants as $variant) {
                            $pwData[] = [
                                'product_id'         => $product->id,
                                'warehouse_id'       => $warehouseId,
                                'product_variant_id' => $variant->id,
                                'qty'                => 0.00,
                            ];
                        }
                    }
                } else {
                    foreach ($warehouses as $warehouseId) {
                        $pwData[] = [
                            'product_id'         => $product->id,
                            'warehouse_id'       => $warehouseId,
                            'product_variant_id' => null,
                            'qty'                => 0.00,
                        ];
                    }
                }
                ProductWarehouse::insert($pwData);
            }

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

            $isVariant = $request->input('is_variant') === 'true' || $request->input('is_variant') === true || $request->input('is_variant') == 1;

            if ($isVariant && $request->has('variants')) {
                $incomingVariants = $request->input('variants');
                $existingVariants = ProductVariant::where('product_id', $id)->whereNull('deleted_at')->get();
                $existingIds = $existingVariants->pluck('id')->toArray();
                $incomingIds = [];

                foreach ($incomingVariants as $v) {
                    if (isset($v['id']) && $v['id']) {
                        $incomingIds[] = $v['id'];
                    }
                }

                foreach ($existingVariants as $ev) {
                    if (!in_array($ev->id, $incomingIds)) {
                        $ev->deleted_at = Carbon::now();
                        $ev->save();
                        ProductWarehouse::where('product_variant_id', $ev->id)
                            ->update(['deleted_at' => Carbon::now()]);
                    }
                }

                $warehouses = Warehouse::whereNull('deleted_at')->pluck('id')->toArray();

                foreach ($incomingVariants as $v) {
                    $variantName = $v['text'] ?? $v['name'] ?? '';
                    if (!$variantName) continue;

                    if (isset($v['id']) && $v['id'] && in_array($v['id'], $existingIds)) {
                        ProductVariant::where('id', $v['id'])->update([
                            'name' => $variantName,
                            'qty'  => $v['qty'] ?? 0.00,
                        ]);
                    } else {
                        $newVariant = ProductVariant::create([
                            'product_id' => $id,
                            'name'       => $variantName,
                            'qty'        => 0.00,
                        ]);

                        if ($warehouses) {
                            $pwData = [];
                            foreach ($warehouses as $whId) {
                                $pwData[] = [
                                    'product_id'         => $id,
                                    'warehouse_id'       => $whId,
                                    'product_variant_id' => $newVariant->id,
                                    'qty'                => 0.00,
                                ];
                            }
                            ProductWarehouse::insert($pwData);
                        }
                    }
                }
            } else {
                $existingVariants = ProductVariant::where('product_id', $id)->whereNull('deleted_at')->get();
                foreach ($existingVariants as $ev) {
                    $ev->deleted_at = Carbon::now();
                    $ev->save();
                    ProductWarehouse::where('product_variant_id', $ev->id)
                        ->update(['deleted_at' => Carbon::now()]);
                }
            }

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

            $variants = ProductVariant::where('product_id', $id)->whereNull('deleted_at')->get();
            foreach ($variants as $variant) {
                $variant->deleted_at = Carbon::now();
                $variant->save();
            }

            ProductWarehouse::where('product_id', $id)->whereNull('deleted_at')
                ->update(['deleted_at' => Carbon::now()]);

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
}
