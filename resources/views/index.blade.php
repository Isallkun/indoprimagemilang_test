<!DOCTYPE html>
<html>
<head>
    <title>Sistem Gudang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Stok Gudang PT IndoPrima Gemilang</h5>
            <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Produk</button>
        </div>
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $err)
                <div>{{ $err }}</div>
                @endforeach
            </div>
            @endif

            <form action="{{ route('home') }}" method="GET" class="row g-2 mb-3">
                <div class="col">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari barang..." value="{{ request('search') }}">
                </div>
                <div class="col-auto">
                    <button class="btn btn-sm btn-primary" type="submit">Cari</button>
                    @if(request('search'))
                    <a href="{{ route('home') }}" class="btn btn-sm btn-secondary">Reset</a>
                    @endif
                </div>
            </form>

            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($products as $i => $p)
                    <tr>
                        <td>{{ $products->firstItem() + $i }}</td>
                        <td>{{ $p->kode_barang }}</td>
                        <td>{{ $p->nama_barang }}</td>
                        <td>
                            @if($p->stok == 0)
                            <span class="badge bg-danger">{{ $p->stok }}</span>
                            @elseif($p->stok <= 10)
                            <span class="badge bg-warning text-dark">{{ $p->stok }}</span>
                            @else
                            <span class="badge bg-success">{{ $p->stok }}</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-update" data-id="{{ $p->id }}" data-nama="{{ $p->nama_barang }}" data-stok="{{ $p->stok }}">Update Stok</button>
                            <a href="{{ route('produk.history', $p->id) }}" class="btn btn-info btn-sm">History</a>
                            <button class="btn btn-danger btn-sm btn-hapus" data-id="{{ $p->id }}" data-nama="{{ $p->nama_barang }}">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada data</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-between align-items-center">
                <small>Menampilkan {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} dari {{ $products->total() }}</small>
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah">
    <div class="modal-dialog">
        <form action="{{ route('produk.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Kode Barang</label>
                        <input type="text" name="kode_barang" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Stok Awal</label>
                        <input type="number" name="stok" class="form-control" value="0" min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalStok">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Stok: <span id="labelNama"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="updateId">
                <p>Stok saat ini: <strong id="labelStok"></strong></p>
                <div class="mb-3">
                    <label>Jenis</label>
                    <select id="updateType" class="form-select">
                        <option value="masuk">Barang Masuk (+)</option>
                        <option value="keluar">Barang Keluar (-)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Jumlah</label>
                    <input type="number" id="updateQty" class="form-control" min="1" value="1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnUpdate">Update</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHapus">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h6 class="modal-title">Hapus Produk</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Hapus <strong id="hapusNama"></strong>?</p>
                <input type="hidden" id="hapusId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="btnHapus">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$.ajaxSetup({
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
});

$('.btn-update').click(function(){
    $('#updateId').val($(this).data('id'));
    $('#labelNama').text($(this).data('nama'));
    $('#labelStok').text($(this).data('stok'));
    $('#updateQty').val(1);
    $('#modalStok').modal('show');
});

$('#btnUpdate').click(function(){
    var btn = $(this);
    btn.prop('disabled', true);
    $.post("{{ route('stok.update') }}", {
        id: $('#updateId').val(),
        type: $('#updateType').val(),
        qty: $('#updateQty').val()
    }).done(function(){
        location.reload();
    }).fail(function(xhr){
        alert(xhr.responseJSON?.message || 'Error');
        btn.prop('disabled', false);
    });
});

$('.btn-hapus').click(function(){
    $('#hapusId').val($(this).data('id'));
    $('#hapusNama').text($(this).data('nama'));
    $('#modalHapus').modal('show');
});

$('#btnHapus').click(function(){
    var btn = $(this);
    btn.prop('disabled', true);
    $.ajax({
        url: '/produk/' + $('#hapusId').val(),
        type: 'DELETE'
    }).done(function(){
        location.reload();
    }).fail(function(){
        alert('Gagal menghapus');
        btn.prop('disabled', false);
    });
});
</script>
</body>
</html>