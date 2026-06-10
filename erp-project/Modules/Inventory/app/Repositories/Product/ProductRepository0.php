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

            $this->handleProductImages($product, $request);
            $this->storeProductVariants($request, $product);
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

            $this->handleProductImages($product, $request);
            $this->updateProductVariants($id, $request);
            $this->updateProductWarehouse($id, $request);

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

            $this->deleteProductImages($product);
            
            $product->delete();

            $this->destroyProductVariants($id);
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

    /**
     * Handle product images (single and multiple)
     */
    protected function handleProductImages($product, $request)
    {
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
    }

    /**
     * Delete all product images
     */
    protected function deleteProductImages($product)
    {
        $singleMedia = $product->getMedia('product')->first();
        $multiMedia  = $product->getMedia('product_images')->all();
        
        if ($singleMedia) {
            $product->clearMediaCollection('product');
            $this->deleteMediaFile($singleMedia);
        }
        
        if ($multiMedia) {
            $product->clearMediaCollection('product_images');
            foreach ($multiMedia as $media) {
                $this->deleteMediaFile($media);
            }
        }
    }

    /**
     * Delete media file from storage
     */
    protected function deleteMediaFile($media)
    {
        $file_name = $media->file_name;
        $img_id    = $media->id;
        if ($img_id && $file_name) {
            $filePath = public_path('storage/' . $img_id . '/' . $file_name);
            if (File::exists($filePath)) {
                unlink($filePath);
            }
        }
    }

    /**
     * Check if product has variants
     */
    protected function isVariantEnabled($request): bool
    {
        return $request->input('is_variant') === 'true' || 
               $request->input('is_variant') === true || 
               $request->input('is_variant') == 1;
    }

    /**
     * Store product variants (for create operation)
     */
    protected function storeProductVariants($request, $product)
    {
        if ($this->isVariantEnabled($request) && $request->has('variants')) {
            $productVariantsData = [];
            foreach ($request->input('variants') as $variant) {
                $productVariantsData[] = [
                    'product_id' => $product->id,
                    'name'       => $variant,
                ];
            }
            ProductVariant::insert($productVariantsData);                
        }
    }

    /**
     * Update product variants (for update operation)
     */
    protected function updateProductVariants($productId, $request)
    {
        $isVariant = $this->isVariantEnabled($request);
        $warehouses = Warehouse::whereNull('deleted_at')->pluck('id')->toArray();
        
        if ($isVariant && $request->has('variants')) {
            $this->syncVariants($productId, $request->input('variants'), $warehouses);
        } elseif (!$isVariant) {
            $this->removeAllVariants($productId, $warehouses);
        }
    }

    /**
     * Sync variants (add, update, delete)
     */
    protected function syncVariants($productId, array $newVariants, array $warehouses)
    {
        $oldVariants = ProductVariant::where('product_id', $productId)
            ->whereNull('deleted_at')
            ->get();

        if ($oldVariants->isNotEmpty()) {
            $this->syncExistingVariants($productId, $newVariants, $oldVariants, $warehouses);
        } else {
            $this->createVariantsForExistingProduct($productId, $newVariants, $warehouses);
        }
    }

    /**
     * Sync variants when product already has variants
     */
    protected function syncExistingVariants($productId, array $newVariants, $oldVariants, array $warehouses)
    {
        $oldVariantsIds = $oldVariants->pluck('id')->toArray();
        $newVariantsIds = $this->extractVariantIds($newVariants);
        
        // Delete removed variants
        $this->deleteRemovedVariants($oldVariantsIds, $newVariantsIds);
        
        // Update or create variants
        $this->updateOrCreateVariants($productId, $newVariants, $oldVariantsIds, $newVariantsIds, $warehouses);
    }

    /**
     * Extract IDs from variants array
     */
    protected function extractVariantIds(array $variants): array
    {
        return array_map(function ($variant) {
            return $variant['id'] ?? 0;
        }, $variants);
    }

    /**
     * Delete variants that are no longer needed
     */
    protected function deleteRemovedVariants(array $oldIds, array $newIds): void
    {
        foreach ($oldIds as $oldId) {
            if (!in_array($oldId, $newIds)) {
                ProductVariant::where('id', $oldId)->update(['deleted_at' => Carbon::now()]);
                ProductWarehouse::where('product_variant_id', $oldId)->update(['deleted_at' => Carbon::now()]);
            }
        }
    }

    /**
     * Update existing variants or create new ones
     */
    protected function updateOrCreateVariants($productId, array $variants, array $oldIds, array $newIds, array $warehouses)
    {
        foreach ($variants as $index => $variant) {
            $variantData = [
                'product_id' => $productId,
                'name'       => $variant['text'],
                'qty'        => $variant['qty'] ?? 0,
            ];

            $hasId = isset($variant['id']);
            $isExisting = $hasId && in_array($newIds[$index], $oldIds);

            if ($isExisting) {
                // Update existing variant
                ProductVariant::where('id', $variant['id'])->update($variantData);
            } else {
                // Create new variant
                $newVariant = ProductVariant::create($variantData);
                $this->createWarehouseEntriesForVariant($productId, $warehouses, $newVariant->id);
            }
        }
    }

    /**
     * Create variants for product that didn't have variants before
     */
    protected function createVariantsForExistingProduct($productId, array $variants, array $warehouses)
    {
        // Soft delete existing warehouse entries
        ProductWarehouse::where('product_id', $productId)->update(['deleted_at' => Carbon::now()]);
        
        foreach ($variants as $variant) {
            $newVariant = ProductVariant::create([
                'product_id' => $productId,
                'name'       => $variant['text'],
                'qty'        => 0,
            ]);
            
            $this->createWarehouseEntriesForVariant($productId, $warehouses, $newVariant->id);
        }
    }

    /**
     * Remove all variants from a product
     */
    protected function removeAllVariants($productId, array $warehouses)
    {
        $oldVariants = ProductVariant::where('product_id', $productId)
            ->whereNull('deleted_at')
            ->get();

        if ($oldVariants->isNotEmpty()) {
            // Soft delete all variants
            foreach ($oldVariants as $variant) {
                $variant->deleted_at = Carbon::now();
                $variant->save();
            }
            
            // Soft delete variant warehouse entries
            ProductWarehouse::where('product_id', $productId)
                ->whereNotNull('product_variant_id')
                ->update(['deleted_at' => Carbon::now()]);
            
            // Create warehouse entries without variants
            $this->createWarehouseEntriesWithoutVariants($productId, $warehouses);
        }
    }

    /**
     * Store product warehouse entries (for create operation)
     */
    protected function storeProductWarehouse($request, $product)
    {
        $warehouses = Warehouse::whereNull('deleted_at')->pluck('id')->toArray();
        
        if (empty($warehouses)) {
            return;
        }

        $isVariant = $this->isVariantEnabled($request);
        $productWarehouse = [];

        foreach ($warehouses as $warehouse) {
            if ($isVariant) {
                $productVariants = ProductVariant::where('product_id', $product->id)
                    ->whereNull('deleted_at')
                    ->get();
                    
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
        
        ProductWarehouse::insert($productWarehouse);
    }

    /**
     * Update product warehouse entries (for update operation)
     */
    protected function updateProductWarehouse($productId, $request)
    {
        $warehouses = Warehouse::whereNull('deleted_at')->pluck('id')->toArray();
        
        if (empty($warehouses)) {
            return;
        }

        $isVariant = $this->isVariantEnabled($request);
        
        if ($isVariant) {
            $this->updateWarehouseForVariantProduct($productId, $warehouses);
        } else {
            $this->updateWarehouseForNonVariantProduct($productId, $warehouses);
        }
    }

    /**
     * Update warehouse entries for product with variants
     */
    protected function updateWarehouseForVariantProduct($productId, array $warehouses)
    {
        $productVariants = ProductVariant::where('product_id', $productId)
            ->whereNull('deleted_at')
            ->get();
            
        foreach ($productVariants as $variant) {
            // Check if warehouse entries exist for this variant
            $existingEntries = ProductWarehouse::where('product_variant_id', $variant->id)
                ->whereNull('deleted_at')
                ->exists();
                
            if (!$existingEntries) {
                $this->createWarehouseEntriesForVariant($productId, $warehouses, $variant->id);
            }
        }
    }

    /**
     * Update warehouse entries for product without variants
     */
    protected function updateWarehouseForNonVariantProduct($productId, array $warehouses)
    {
        $existingEntries = ProductWarehouse::where('product_id', $productId)
            ->whereNull('product_variant_id')
            ->whereNull('deleted_at')
            ->exists();
            
        if (!$existingEntries) {
            $this->createWarehouseEntriesWithoutVariants($productId, $warehouses);
        }
    }

    /**
     * Create warehouse entries for a specific variant
     */
    protected function createWarehouseEntriesForVariant($productId, array $warehouses, int $variantId)
    {
        $entries = [];
        foreach ($warehouses as $warehouse) {
            $entries[] = [
                'product_id'         => $productId,
                'warehouse_id'       => $warehouse,
                'product_variant_id' => $variantId,
            ];
        }
        ProductWarehouse::insert($entries);
    }

    /**
     * Create warehouse entries without variant (for non-variant products)
     */
    protected function createWarehouseEntriesWithoutVariants($productId, array $warehouses)
    {
        $entries = [];
        foreach ($warehouses as $warehouse) {
            $entries[] = [
                'product_id'         => $productId,
                'warehouse_id'       => $warehouse,
                'product_variant_id' => null,
            ];
        }
        ProductWarehouse::insert($entries);
    }

    /**
     * Destroy product variants (soft delete)
     */
    protected function destroyProductVariants($productId)
    {
        $productVariants = ProductVariant::where('product_id', $productId)
            ->whereNull('deleted_at')
            ->get();
            
        foreach ($productVariants as $productVariant) {
            $productVariant->deleted_at = Carbon::now();
            $productVariant->save();
        }
    }

    /**
     * Destroy product warehouse entries (soft delete)
     */
    protected function destroyProductWarehouse($productId)
    {
        ProductWarehouse::where('product_id', $productId)
            ->whereNull('deleted_at')
            ->update(['deleted_at' => Carbon::now()]);
    }
}