<div>
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="font-weight-bold">Product List <i class="fa-solid fa-store"></i></h3>
                        </div>
                        <div class="col-md-6">
                            <input wire:model="search" type="text" class="form-control" placeholder="Cari Buah...">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($products as $product)
                        <div class="col-md-3 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <img src="{{ asset('storage/images/'.$product->image)}}" alt="Denim Jeans" style="object-fit:fill; width:100%;height:125px;">
                                    <button wire:click="addItem({{$product->id}})" class="btn btn-primary btn-sm" style="position:absolute;top:0;right:0;margin: 8px 8px"><i class="fa-solid fa-cart-shopping"></i></button>
                                    <h6 class="badge badge-info" style="position:absolute;right:0;margin: 120px 8px">{{$product->description}}</h6>
                                </div>
                                <div class="card-footer">
                                    <h6>{{$product->name}}</h6>
                                    <h6>Rp{{number_format($product->price ,0,',','.') }}</h6>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-md-12 mt-3">
                            <h4 class="text-center">Barang Tidak Ditemukkan</h4>
                        </div>
                        @endforelse
                    </div>
                    <div style="display: flex;justify-content:center;">{{$products->links()}}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="font-weight-bold">Belanja <i class="fa-solid fa-cart-shopping"></i></h3>
                </div>
                <div class="card-body">
                    @if(session()->has('error'))
                    <p class="text-danger">
                        {{session('error')}}
                    </p>
                    @endif
                    <table class="table table-sm table-bordered table-hover">
                        <thead class="bg-primary text-light">
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Kg</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($carts as $index=>$cart)
                            <tr>
                                <td>{{$index+1}}
                                    <span wire:click="removeItem('{{$cart['rowId']}}')" class="text-grey"><i class="fa-solid fa-trash"></i></span>
                                </td>
                                <td>{{$cart['name']}}
                                    <br>
                                    Rp{{number_format($cart['pricesingle'] ,0,',','.') }}
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-success" style="padding: 2px 5px;" wire:click="increaseItem('{{$cart['rowId']}}')"><i class="fa-solid fa-plus"></i></button>
                                    {{$cart['qty']}} Kg
                                    <button class="btn btn-sm btn-danger" style="padding: 2px 5px;" wire:click="decreaseItem('{{$cart['rowId']}}')"><i class="fa-solid fa-minus"></i></button>
                                </td>
                                <td>
                                    Rp{{number_format($cart['price'] ,0,',','.') }}
                                </td>
                            </tr>
                            @empty
                            <td colspan="4">
                                <h6 class="text-Center">Keranjang Kosong</h6>
                            </td>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <h4 class="font-weight-bold">Jumlah Pembayaran</h4>
                    <h5 class="font-weight-bold">Total Pembayaran : Rp.{{number_format($summary['total'] ,0,',','.') }}</h5>
                    <!-- <h5 class="font-weight-bold">Tax: {{$summary['pajak']}}</h5>
                    <h5 class="font-weight-bold">Total: {{$summary['total']}}</h5> -->

                    <!-- <div>
                        <button wire:click="enableTax" class="btn btn-block btn-primary">Tambah Pajak</button>
                        <button wire:click="disableTax" class="btn btn-block btn-primary">Hapus Pajak</button>
                    </div> -->
                    <div class="form-group mt-3">
                        <input type="number" wire:model="payment" class="form-control" id="payment" placeholder="Masukkan Jumlah Pembayaran">
                        <input type="hidden" id="total" value="{{$summary['total']}}">
                    </div>

                    <form wire:submit.prevent="handleSubmit">
                        <div>
                            <label class="font-weight-bold">Total Kembalian</label>
                            <h5 class="font-weight-bold" wire:ignore id="totalkembalian">Rp. 0</h5>
                        </div>
                        <div class="mt-4">
                            <button wire:ignore id="saveButton" type="submit" class="btn btn-block btn-success" disabled><i class="fa-solid fa-save fa-lg"></i> Bayar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script-custom')
<script>
    payment.oninput = () => {
        const paymentAmount = document.getElementById("payment").value
        const totalAmount = document.getElementById("total").value

        const kembalian = paymentAmount - totalAmount

        document.getElementById("totalkembalian").innerHTML = `Rp. ${rupiah(kembalian)}`

        const saveButton = document.getElementById("saveButton")
        if (kembalian < 0) {
            saveButton.disabled = true
        } else {
            saveButton.disabled = false
        }
    }

    const rupiah = (angka) => {
        const numberString = angka.toString()
        const split = numberString.split(',')
        const sisa = split[0].length % 3
        let rupiah = split[0].substr(0, sisa)
        const ribuan = split[0].substr(sisa).match(/\d{1,3}/gi)

        if (ribuan) {
            const separator = sisa ? '.' : ''
            rupiah += separator + ribuan.join('.')
        }
        return split[1] != undefined ? rupiah + ',' + split[1] : rupiah
    }
</script>
@endpush