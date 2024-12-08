<?php

namespace App\Http\Controllers;

use App\Export\ProductsExport;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with('Supplier');


        if ($request->has('search') && $request->search != '') {


            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', '%' . $search . '%');
            });
        }
 
        $products = $query->paginate(2);
        //return $products;

        return view("master-data.product-master.index-product", compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        return view("master-data.product-master.create-product",compact('suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validasi_data = $request->validate([
            'product_name'=> 'required|string|max:255',
            'unit'=> 'required|string|max:50',
            'type'=> 'required|string|max:50',
            'information'=> 'nullable|string',
            'qty'=> 'required|integer',
            'producer'=> 'required|string|max:255',
            'supplier_id'=>'required|exists:suppliers,id',
        ]);

        Product::create($validasi_data);

        return redirect()->back()->with('success','Product created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        return view('master-data.product-master.detail-product', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        $suppliers = Supplier::all();
        return view('master-data.product-master.edit-product', compact('product','suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    $request->validate([
        'product_name' => 'required|string|max:255',
        'unit' => 'required|string|max:255',
        'type' => 'required|string|max:255',
        'information' => 'nullable|string',
        'qty' => 'required|integer|min:1',
        'producer' => 'required|string|max:255',
        'supplier_id' => 'required|exists:suppliers,id', // Validasi supplier_id
    ]);

    $product = Product::findOrFail($id);

    $product->update([
        'product_name' => $request->product_name,
        'unit' => $request->unit,
        'type' => $request->type,
        'information' => $request->information,
        'qty' => $request->qty,
        'producer' => $request->producer,
        'supplier_id' => $request->supplier_id, // Update supplier_id
    ]);

    return redirect()->route('product-index')->with('success', 'Product updated successfully!');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return redirect()->route('product-index')->with('success', 'Product Berhasil Dihapus.');
        }
        return redirect()->route('product-index')->with('error', 'product tidak ditemukan.');
    }

    public function exportExcel(){
        return Excel::download(new ProductsExport, 'product.xlsx');
    }

    public function exportPDF()
    {
        $products = Product::all();
        $pdf = Pdf::loadView('exports.products-pdf', compact('products'));
        return $pdf->download('products.pdf');
    }
}