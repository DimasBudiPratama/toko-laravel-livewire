<div>
   <div class="row">
      <div class="col-md-8">
         <div class="card">
            <div class="card-body">
               <h2 class="font-weight-bold mb-3">Product List</h2>
               <table class="table table-bordered table-hovered ">
                  <thead class="bg-primary text-light">
                     <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Image</th>
                        <th>Description</th>
                        <th>Qty</th>
                        <th>Price</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach($products as $index=>$product)
                     <tr>
                        <td style="text-align: center ;">{{$index + 1}} <br>
                           <button type="button" wire:click="deleteId({{ $product->id }})" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#hapus"><i class="fa fa-trash"></i></button>
                        </td>
                        <td>{{$product->name}}</td>
                        <td><img src="{{ asset('storage/images/'.$product->image)}}" width="100" alt="Product Image" class="img-fluid"></td>
                        <td>{{$product->description}}</td>
                        <td>{{$product->qty}} Kg</td>
                        <td>Rp{{number_format($product->price ,0,',','.') }}</td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
            </div>
         </div>
      </div>
      <div wire:ignore.self class="modal fade" id="hapus" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
         <div class="modal-dialog" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Delete Confirm</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true close-btn">×</span>
                  </button>
               </div>
               <div class="modal-body">
                  <p>Are you sure want to delete?</p>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary close-btn" data-dismiss="modal">Close</button>
                  <button type="button" wire:click.prevent="delete()" class="btn btn-danger close-modal" data-dismiss="modal">Yes, Delete</button>
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-4">
         <div class="card">
            <div class="card-body">
               <h2 class="font-weight-bold mb-3">Create Product</h2>
               <form wire:submit.prevent="store">
                  <div class="form-group">
                     <label for="name">Product Name</label>
                     <input type="text" wire:model="name" id="name" class="form-control" placeholder="Product Name">
                     @error('name') <small class="text-danger">{{$message}}</small>@enderror
                  </div>
                  <div class="form-group">
                     <label for="image">Image</label>
                     <div class="custom-file">
                        <input type="file" wire:model="image" id="customFile" class="custom-file-input">
                        <label for="customFile" class="custom-file-label">Choose Image</label>
                        @error('image') <small class="text-danger">{{$message}}</small>@enderror
                     </div>
                     @if($image)
                     <label for="" class="mt-2">Image Preview: </label>
                     <img src="{{$image->temporaryUrl()}}" class="img-fluid" alt="Preview Image">
                     @endif
                  </div>
                  <div class="form-group">
                     <label for="desc">Description</label>
                     <textarea wire:model="description" id="desc" class="form-control"></textarea>
                     @error('description') <small class="text-danger">{{$message}}</small>@enderror
                  </div>
                  <div class="form-group">
                     <label for="qty">Qty</label>
                     <input type="number" wire:model="qty" id="qty" class="form-control" placeholder="Qty">
                     @error('qty') <small class="text-danger">{{$message}}</small>@enderror
                  </div>
                  <div class="form-group">
                     <label for="price">Price</label>
                     <input type="number" wire:model="price" id="price" class="form-control" placeholder="Price">
                     @error('price') <small class="text-danger">{{$message}}</small>@enderror
                  </div>
                  <div class="form-group">
                     <button type="submit" class="btn btn-primary btn-block">Simpan Product</button>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>