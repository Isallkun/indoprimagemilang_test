<!DOCTYPE html>
<html>
<head>
    <title>History - {{ $product->nama_barang }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">History: {{ $product->nama_barang }}</h5>
            <a href="{{ route('home') }}" class="btn btn-light btn-sm">Kembali</a>
        </div>
        <div class="card-body">
            <div class="row mb-4 g-3">
                <div class="col-md-4">
                    <div class="bg-primary text-white rounded p-3 text-center">
                        <small>Kode</small>
                        <h5 class="mb-0">{{ $product->kode_barang }}</h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-primary text-white rounded p-3 text-center">
                        <small>Nama</small>
                        <h5 class="mb-0">{{ $product->nama_barang }}</h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-primary text-white rounded p-3 text-center">
                        <small>Stok Sekarang</small>
                        <h5 class="mb-0">{{ $product->stok }}</h5>
                    </div>
                </div>
            </div>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Waktu</th>
                        <th>Tipe</th>
                        <th>Perubahan</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($product->histories as $i => $h)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $h->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($h->type == 'awal')
                            <span class="badge bg-secondary">Stok Awal</span>
                            @elseif($h->type == 'masuk')
                            <span class="badge bg-success">Masuk</span>
                            @else
                            <span class="badge bg-danger">Keluar</span>
                            @endif
                        </td>
                        <td>
                            @if($h->perubahan > 0)
                            <span class="text-success">+{{ $h->perubahan }}</span>
                            @else
                            <span class="text-danger">{{ $h->perubahan }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada history</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>