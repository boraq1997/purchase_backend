<?php 

namespace App\Services;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\VendorResource;

class VendorService {
    public function getAll(array $filters = [], int $perPage = 15, bool $paginate = true)
    {
        $query = Vendor::with('creator');

        if (!empty($filters['search'])) {
            $term = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('phone1', 'like', $term)
                  ->orWhere('phone2', 'like', $term)
                  ->orWhere('email', 'like', $term)
                  ->orWhere('address', 'like', $term);
            });
        }

        $query->latest('id');

        $result = $paginate ? $query->paginate($perPage) : $query->get();

        // إعادة Resource Collection
        return VendorResource::collection($result);
    }

    public function getAllWithRelation(array $filters = [], int $perPage = 15, bool $paginate = true)
    {
        $query = Vendor::with(['creator', 'estimates']);

        if (!empty($filters['search'])) {
            $term = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('phone1', 'like', $term)
                  ->orWhere('phone2', 'like', $term)
                  ->orWhere('email', 'like', $term)
                  ->orWhere('address', 'like', $term);
            });
        }

        $query->latest('id');

        $result = $paginate ? $query->paginate($perPage) : $query->get();

        // إعادة Resource Collection
        return VendorResource::collection($result);
    }

    public function getById(Vendor $vendor) {
        return $vendor->load(['creator', 'estimates']);
    }

    public function createNew(array $data)
    {
        return DB::transaction(function () use ($data) {
            $vendor = Vendor::create([
                'name'       => $data['name'],
                'phone1'     => $data['phone1'],
                'phone2'     => $data['phone2'] ?? null,
                'email'      => $data['email'],
                'address'    => $data['address'],
                'created_by' => auth()->id(),
            ]);

            // استخدام Resource لعرض البيانات مع الـcreator
            return new VendorResource($vendor->load('creator'));
        });
    }

    public function updateVendorInfo(Vendor $vendor, array $data) {
        return DB::transaction(function() use ($vendor, $data) {
            $vendor->update($data);
            return new VendorResource($vendor->load('creator'));
        });
    }

    public function deleteOne(Vendor $vendor) {
        return DB::transaction(fn() => $vendor->delete());
    }
}

