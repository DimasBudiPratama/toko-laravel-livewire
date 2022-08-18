<div class="container">
    <div class="row mt-5">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h1 class="text-center">Login</h1>
                    <form wire:submit.prevent="submit">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input wire:model="form.email" type="email" class="form-control" id="username">
                            @error('form.email') <span class="text-danger">{{$message}}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input wire:model="form.password" type="password" class="form-control" id="password">
                            @error('form.password') <span class="text-danger">{{$message}}</span> @enderror
                        </div>
                        <div class="form-group">
                            <button class="btn btn-block btn-primary">Login</button>
                        </div>
                        <a href="/register">Register</a>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4"></div>
    </div>
</div>