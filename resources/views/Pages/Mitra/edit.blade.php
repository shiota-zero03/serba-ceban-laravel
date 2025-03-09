@extends('Components.Layout')
@section('content')

<div class="row gap-4">
    <div class="col-12">
        <h4 class="m-md-0 text-md-start text-center">Edit Mitra</h4>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="row g-2" action="{{route('mitra.update', $user->id)}}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="col-12">
                        <div class="form-group">
                            <label for="name">Nama Mitra</label>
                            <input type="text" class="form-control" name="name" placeholder="Masukkan nama mitra disini" value="{{ $user['name'] }}">
                            @error('name')
                                <small class="text-danger"><em>{{ $message }}</em></small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" name="email" placeholder="Masukkan email mitra disini" value="{{ $user['email'] }}">
                            @error('email')
                                <small class="text-danger"><em>{{ $message }}</em></small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Masukkan password disini" >
                            @error('password')
                                <small class="text-danger"><em>{{ $message }}</em></small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label for="whatsapp">Nomor Whatsapp Aktif</label>
                            <input type="number" class="form-control" name="whatsapp" placeholder="Masukkan whatsapp mitra disini" value="{{ $user['phone_number'] }}">
                            @error('whatsapp')
                                <small class="text-danger"><em>{{ $message }}</em></small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option @selected($user['email_verified_at']) value="Aktif">Aktif</option>
                                <option @selected(!$user['email_verified_at']) value="Tidak Aktif">Tidak Aktif</option>
                            </select>
                            @error('status')
                                <small class="text-danger"><em>{{ $message }}</em></small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4 col-12">
                        <div class="form-group">
                            <label for="bank_name">Nama Bank</label>
                            <input type="text" class="form-control" name="bank_name" placeholder="Masukkan nama bank mitra disini" value="{{ $user['nama_bank'] }}">
                            @error('bank_name')
                                <small class="text-danger"><em>{{ $message }}</em></small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4 col-12">
                        <div class="form-group">
                            <label for="rekening">Nomor Rekening</label>
                            <input type="text" class="form-control" name="rekening" placeholder="Masukkan nama bank mitra disini" value="{{ $user['nomor_rekening'] }}">
                            @error('rekening')
                                <small class="text-danger"><em>{{ $message }}</em></small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4 col-12">
                        <div class="form-group">
                            <label for="atas_nama">Atas Nama</label>
                            <input type="text" class="form-control" name="atas_nama" placeholder="Masukkan nama bank mitra disini" value="{{ $user['atas_nama'] }}">
                            @error('atas_nama')
                                <small class="text-danger"><em>{{ $message }}</em></small>
                            @enderror
                        </div>
                    </div>

                    <button class="btn btn-primary col-12 mt-3 d-flex align-items-center justify-content-center gap-2" type="submit">
                        <i class="bx bx-save"></i>
                        Simpan
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        $('#mitra').addClass('active')
    </script>
@endpush

@endsection
