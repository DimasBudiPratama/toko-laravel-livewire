<?php

namespace App\Http\Livewire;

use Livewire\Component;

use Carbon\Carbon;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Haruncpi\LaravelIdGenerator\IdGenerator;

use App\Models\Product as ProductModel;
use App\Models\Transaction;
use App\Models\ProductTransaction;

class Cart extends Component
{
    //Custom Pagination
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $tax = "0%";

    //Search
    public $search;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    //Payment
    public $payment = 0;

    public function render()
    {
        $products = ProductModel::where('name', 'like', '%' . $this->search . '%')->orderBy('created_at', 'DESC')->paginate(6);

        //Memanggil condition package darryldecode
        $condition = new \Darryldecode\Cart\CartCondition([
            'name'  => 'pajak',
            'type'  => 'tax',
            'target' => 'total', //total dari belanja kita
            'value' => $this->tax,
            'order' => 1
        ]);

        //Membatasi session cart yang berbeda
        \Cart::session(Auth()->id())->condition($condition);
        // List dari cart
        $items = \Cart::session(Auth()->id())->getContent()->sortBy(function ($cart) {
            return $cart->attributes->get('added_at');
        });

        //Membuat conditional
        if (\Cart::isEmpty()) {
            $cartData = [];
        } else {
            foreach ($items as $item) {
                $cart[] = [
                    'rowId'         => $item->id,
                    'name'          => $item->name,
                    'qty'           => $item->quantity,
                    'pricesingle'   => $item->price, //Price Secara Single
                    'price'         => $item->getPriceSum(), //Price Keseluruhan
                ];
            }

            $cartData = collect($cart);
        }

        //Sub Total
        $sub_total = \Cart::session(Auth()->id())->getSubTotal();
        $total = \Cart::session(Auth()->id())->getTotal();
        //Condition Pajak
        $newCondition = \Cart::session(Auth()->id())->getCondition('pajak'); //Pajak di ambil dari condition di atas name => pajak
        $pajak = $newCondition->getCalculatedValue($sub_total);

        $summary = [
            'sub_total' => $sub_total,
            'pajak'     => $pajak,
            'total'     => $total
        ];
        return view('livewire.cart', [
            'products'  => $products,
            'carts'      => $cartData,
            'summary'   => $summary
        ]);
    }

    public function addItem($id)
    {
        $rowId = "Cart" . $id;

        $cart = \Cart::session(Auth()->id())->getContent();
        $cekItemId = $cart->whereIn('id', $rowId);

        $idProduct = substr($rowId, 4, 5);
        $product = ProductModel::find($idProduct);

        if ($cekItemId->isNotEmpty()) {
            if ($product->qty == $cekItemId[$rowId]->quantity) {
                session()->flash('error', 'Jumlah Barang Kurang');
            } else {
                \Cart::session(Auth()->id())->update($rowId, [
                    'quantity' => [
                        'relative'  => true,
                        'value'     => 1
                    ]
                ]);
            }
        } else {
            if ($product->qty == 0) {
                session()->flash('error', 'Jumlah Barang Kurang');
            } else {
                \Cart::session(Auth()->id())->add([
                    'id'            => "Cart" . $product->id,
                    'name'          => $product->name,
                    'price'         => $product->price,
                    'quantity'      => 1,
                    'attributes'    => [
                        'added_at'  => Carbon::now()
                    ],
                ]);
            }
        }
    }



    public function increaseItem($rowId)
    {
        $idProduct = substr($rowId, 4, 5);
        $product = ProductModel::find($idProduct);
        $cart = \Cart::session(Auth()->id())->getContent();

        $checkItem = $cart->whereIn('id', $rowId);

        if ($product->qty == $checkItem[$rowId]->quantity) {
            session()->flash('error', 'Jumlah Barang Kurang');
        } else {
            \Cart::session(Auth()->id())->update($rowId, [
                'quantity' => [
                    'relative'  => true,
                    'value'     => 1,
                ]
            ]);
        }
    }

    public function decreaseItem($rowId)
    {
        $idProduct = substr($rowId, 4, 5);
        $product = ProductModel::find($idProduct);
        $cart = \Cart::session(Auth()->id())->getContent();

        $checkItem = $cart->whereIn('id', $rowId);

        if ($checkItem[$rowId]->quantity == 1) {
            $this->removeItem($rowId);
        } else {
            if ($product->qty == 0) {
                session()->flash('error', 'Jumlah Barang Kurang');
            } else {
                \Cart::session(Auth()->id())->update($rowId, [
                    'quantity' => [
                        'relative'  => true,
                        'value'     => -1,
                    ]
                ]);
            }
        }
    }

    public function removeItem($rowId)
    {
        \Cart::session(Auth()->id())->remove($rowId);
    }

    //Tax
    public function enableTax()
    {
        $this->tax = "+10%";
    }

    public function disableTax()
    {
        $this->tax = "0%";
    }

    public function handleSubmit()
    {
        $cartTotal = \Cart::session(Auth()->id())->getTotal();
        $bayar = $this->payment;
        $kembalian = (int) $bayar - (int) $cartTotal;
        // $allCart = \Cart::session(Auth()->id())->getContent();

        // $filterCart = $allCart->map(function ($item) {
        //     return [
        //         'id' => substr($item->id, 4, 5),
        //         'quantity' => $item->quantity
        //     ];
        // });

        // foreach ($filterCart as $cart) {
        //     $product = ProductModel::find($cart['id']);

        //     if ($product->qty === 0) {
        //         return session()->flash('error', 'Jumlah item kurang');
        //     }

        //     $product->decrement('qty', $cart['quantity']);
        // }

        // dd($cart);

        if ($kembalian >= 0) {
            DB::beginTransaction();

            try {
                $allCart = \Cart::session(Auth()->id())->getContent();

                $filterCart = $allCart->map(function ($item) {
                    return [
                        'id' => substr($item->id, 4, 5),
                        'quantity' => $item->quantity
                    ];
                });

                foreach ($filterCart as $cart) {
                    $product = ProductModel::find($cart['id']);

                    if ($product->qty === 0) {
                        return session()->flash('error', 'Jumlah item kurang');
                    }

                    $product->decrement('qty', $cart['quantity']);
                }

                $id = IdGenerator::generate([
                    'table' => 'transaction',
                    'length' => 10,
                    'prefix' => 'INV-',
                    'field' => 'invoice_number'
                ]);

                Transaction::create([
                    'invoice_number' => $id,
                    'user_id' => Auth()->id(),
                    'pay' => $bayar,
                    'total' => $cartTotal
                ]);

                foreach ($filterCart as $cart) {
                    ProductTransaction::create([
                        'product_id' => $cart['id'],
                        'invoice_number' => $id,
                        'qty' => $cart['quantity']
                    ]);
                }

                \Cart::session(Auth()->id())->clear();
                $this->payment = 0;

                DB::commit();
            } catch (\Throwable $th) {
                DB::rollback();
                return session()->flash('error', $th);
            }
        }
    }
}
