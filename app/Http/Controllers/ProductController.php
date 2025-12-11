<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request) {
        $query = Product::query();
        
        if ($request->search) {
            $query->where('kode_barang', 'like', "%{$request->search}%")
                  ->orWhere('nama_barang', 'like', "%{$request->search}%");
        }
        
        $products = $query->latest()->paginate(10)->withQueryString();
        return view('index', compact('products'));
    }

    public function store(Request $request) {
        $request->validate([
            'kode_barang' => 'required|unique:products,kode_barang',
            'nama_barang' => 'required',
            'stok' => 'required|integer|min:0'
        ]);

        DB::transaction(function() use ($request) {
            $product = Product::create($request->all());

            if ($request->stok > 0) {
                StockHistory::create([
                    'product_id' => $product->id,
                    'perubahan' => $request->stok,
                    'type' => 'awal'
                ]);
            }
        });

        return back()->with('success', 'Produk berhasil ditambahkan');
    }

    public function updateStock(Request $request) {
        $request->validate([
            'id' => 'required',
            'qty' => 'required|integer|min:1',
            'type' => 'required|in:masuk,keluar'
        ]);

        $product = Product::find($request->id);
        if (!$product) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        if ($request->type == 'keluar' && $product->stok < $request->qty) {
            return response()->json(['message' => 'Stok tidak mencukupi'], 400);
        }

        DB::transaction(function() use ($product, $request) {
            $perubahan = $request->type == 'masuk' ? $request->qty : -$request->qty;
            $product->stok += $perubahan;
            $product->save();

            StockHistory::create([
                'product_id' => $product->id,
                'perubahan' => $perubahan,
                'type' => $request->type
            ]);
        });

        return response()->json(['message' => 'Stok berhasil diupdate']);
    }

    public function destroy($id) {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
        }
        return response()->json(['message' => 'Produk dihapus']);
    }

    public function history($id) {
        $product = Product::with('histories')->findOrFail($id);
        return view('history', compact('product'));
    }
}