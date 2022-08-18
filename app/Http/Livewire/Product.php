<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Product as ProductModel;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Product extends Component
{
    use WithFileUploads;

    public $name, $image, $description, $qty, $price;

    public $deleteId = '';

    public function render()
    {
        $products = ProductModel::orderBy('created_at', 'DESC')->get();
        return view('livewire.product', [
            'products' => $products
        ]);
    }

    public function previewImage()
    {
        $this->validate([
            'image' => 'image|max:2048'
        ]);
    }

    public function store()
    {
        $this->validate([
            'name' => 'required',
            'image' => 'required|max:2048|image',
            'description' => 'required',
            'qty' => 'required',
            'price' => 'required'
        ]);

        $imageName = md5($this->image . microtime() . '.' . $this->image->extension());

        Storage::putFileAs(
            'public/images',
            $this->image,
            $imageName
        );

        ProductModel::create([
            'name' => $this->name,
            'image' => $imageName,
            'description' => $this->description,
            'qty' => $this->qty,
            'price' => $this->price,
        ]);

        session()->flash('info', 'Product Berhasil Ditambahkan !!!');

        $this->name = '';
        $this->image = '';
        $this->description = '';
        $this->qty = '';
        $this->price = '';
    }

    public function deleteId($id)
    {
        $this->deleteId = $id;
    }
    public function delete()
    {
        ProductModel::find($this->deleteId)->delete();
    }
}
