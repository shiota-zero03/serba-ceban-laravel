@extends('Components.Layout')
@section('content')

<div class="row">
    <div class="col-12 mb-4 order-0">
        <div class="card">
            <div class="d-flex align-items-end row">
                <div class="col-sm-8">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Selamat datang, {{ auth()->user()->name }}! 🎉</h5>
                        <p class="mb-4">
                            Sistem <span class="fw-bold">Serba Ceban</span> dirancang untuk membantu Anda menambahkan, mengedit, dan mengatur produk serta mencatat setiap transaksi dengan cepat. Semua dalam satu platform yang simpel dan efisien!
                        </p>
                    </div>
                </div>
                <div class="col-sm-4 text-center text-sm-left">
                    <div class="card-body pb-0 px-0 px-md-4">
                        <img
                            src="{{ asset('/') }}assets/img/illustrations/man-with-laptop-light.png"
                            height="140"
                            alt="View Badge User"
                            data-app-dark-img="illustrations/man-with-laptop-dark.png"
                            data-app-light-img="illustrations/man-with-laptop-light.png"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->role == 'MITRA')
        <!-- Total Revenue -->
        <div class="col-12 col-md-8 col-lg-9 order-2 order-md-3 order-lg-2 mb-4">
            <div class="card">
                <div class="row row-bordered g-0">
                    <div class="col-12">
                        <h5 class="card-header m-0 me-2 pb-3">Total Penjualan Tahun {{ date('Y') }}</h5>
                        <div class="me-2">
                            <div id="chart" style="min-height: 300"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Total Revenue -->
        <div class="col-12 col-md-4 col-lg-3 order-3 order-md-2">
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('/') }}assets/img/icons/unicons/chart-success.png" alt="Credit Card" class="rounded" />
                                </div>
                            </div>
                            <span class="d-block mb-1">Total Produk</span>
                            <h3 class="card-title text-nowrap mb-2">{{ number_format($data['totalProduk'], 0, '.', '.') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('/') }}assets/img/icons/unicons/cc-primary.png" alt="Credit Card" class="rounded" />
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Profit</span>
                            <h3 class="card-title mb-2">Rp {{ number_format($data['totalTransferHariIni'], 0, '.', '.') }}</h3>
                            <small class="@if($data['persentasePerubahan'] > 0) text-success @else text-danger @endif fw-semibold"><i class="bx @if($data['persentasePerubahan'] > 0) bx-up-arrow-alt @else bx-down-arrow-alt @endif"></i> {{ $data['persentasePerubahan'] > 0 ? "+".$data['persentasePerubahan'] : $data['persentasePerubahan'] }}%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 order-2 order-md-3 order-lg-2 mb-4">
            <div class="card">
                <div class="row row-bordered g-0">
                    <div class="col-12">
                        <h5 class="card-header m-0 me-2 pb-3">Total Pendapatan Tahun {{ date('Y') }}</h5>
                        <div class="me-2">
                            <div id="chart-revenue" style="min-height: 300"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="col-12 order-3 order-md-2">
            <div class="row">
                <div class=" col-lg-3 col-md-6 col-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="bx bxs-user-account text-primary alert alert-primary"></i>
                                </div>
                            </div>
                            <span class="d-block mb-1">Total Mitra</span>
                            <h3 class="card-title text-nowrap mb-2">{{ number_format($data['totalMitra'], 0, '.', '.') }}</h3>
                        </div>
                    </div>
                </div>
                <div class=" col-lg-3 col-md-6 col-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="bx bx-archive-in text-warning alert alert-warning"></i>
                                </div>
                            </div>
                            <span class="d-block mb-1">Total Pesanan Hari Ini</span>
                            <h3 class="card-title text-nowrap mb-2">{{ number_format($data['pesananHariIni'], 0, '.', '.') }}</h3>
                        </div>
                    </div>
                </div>
                <div class=" col-lg-3 col-md-6 col-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="bx bxs-cart-add text-info alert alert-info"></i>
                                </div>
                            </div>
                            <span class="d-block mb-1">Total Penjualan Hari Ini</span>
                            <h3 class="card-title text-nowrap mb-2">{{ number_format($data['penjualanHariIni'], 0, '.', '.') }}</h3>
                        </div>
                    </div>
                </div>
                <div class=" col-lg-3 col-md-6 col-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="bx bx-wallet text-success alert alert-success"></i>
                                </div>
                            </div>
                            <span class="d-block mb-1">Pendapatan Hari Ini</span>
                            <h3 class="card-title text-nowrap mb-2">Rp {{ number_format($data['pendapatanHariIni'], 0, '.', '.') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 order-2 order-md-3 order-lg-2 mb-4">
            <div class="card">
                <div class="row row-bordered g-0">
                    <div class="col-12">
                        <h5 class="card-header m-0 me-2 pb-3">Transaksi Tahun {{ date('Y') }}</h5>
                        <div class="me-2">
                            <div id="chart-revenue-count" style="min-height: 300"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 order-2 order-md-3 order-lg-2 mb-4">
            <div class="card">
                <div class="row row-bordered g-0">
                    <div class="col-12">
                        <h5 class="card-header m-0 me-2 pb-3">Pendapatan Tahun {{ date('Y') }}</h5>
                        <div class="me-2">
                            <div id="chart-revenue-admin" style="min-height: 300"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif




  @push('script')
    <script>
        $('#dashboard').addClass('active')

        $.ajax({
            url: "{!! route('dashboard') !!}",
            method: "GET",
            success: function(res) {
                if(res.data.role === 'mitra'){
                    var options = {
                        chart: {
                            type: 'line',
                            height: 300
                        },
                        stroke: {
                            curve: 'smooth', // Biar garisnya melengkung
                            width: 3
                        },
                        colors: ['#696cff'],
                        series: [{
                            name: 'Produk Terjual',
                            data: res.data.count
                        }],
                        xaxis: {
                            categories: res.data.bulan
                        }
                    };
                    var chart = new ApexCharts(document.querySelector("#chart"), options);
                    chart.render();

                    var options2 = {
                        chart: {
                            type: 'bar',
                            height: 300
                        },
                        colors: [
                            "#FF5733", "#33FF57", "#3357FF", "#FF33A1", "#FFD700", "#00CED1", "#FF4500", "#8A2BE2", "#32CD32", "#FF1493", "#4682B4", "#D2691E"
                        ], // Warna custom untuk bar
                        plotOptions: {
                            bar: {
                                borderRadius: 10, // Biar sudutnya lebih melengkung
                                distributed: true, // Pastikan warna bisa diubah
                                horizontal: false, // Pastikan vertikal
                                columnWidth: '50%' // Atur ukuran bar agar tidak terlalu tebal
                            }
                        },
                        dataLabels: {
                            enabled: false // Matikan label angka di atas bar
                        },
                        series: [{
                            name: 'Pendapatan',
                            data: res.data.transfer
                        }],
                        xaxis: {
                            categories: res.data.bulan
                        },
                        legend: {
                            show: false
                        }
                    };
                    var chartV = new ApexCharts(document.querySelector("#chart-revenue"), options2);
                    chartV.render();
                } else {
                    // var options = {
                    //     chart: {
                    //         type: 'line',
                    //         height: 300
                    //     },
                    //     stroke: {
                    //         curve: 'smooth', // Biar garisnya melengkung
                    //         width: 3
                    //     },
                    //     colors: ['#696cff'],
                    //     series: [{
                    //         name: 'Produk Terjual',
                    //         data: res.data.count
                    //     }],
                    //     xaxis: {
                    //         categories: res.data.bulan
                    //     }
                    // };
                    // var chart = new ApexCharts(document.querySelector("#chart"), options);
                    // chart.render();

                    var options = {
                        chart: {
                            type: 'bar',
                            height: 300
                        },
                        colors: ["#696cff", "#03c3ec", "#007bff"],
                        dataLabels: {
                            enabled: false // Matikan label angka di atas bar
                        },
                        series: [{
                            name: 'Produk Request',
                            data: res.data.pesan
                        },{
                            name: 'Produk Diterima',
                            data: res.data.terima
                        },{
                            name: 'Produk Terjual',
                            data: res.data.jual
                        }],
                        xaxis: {
                            categories: res.data.bulan
                        },
                        legend: {
                            show: false
                        }
                    };
                    var chart = new ApexCharts(document.querySelector("#chart-revenue-count"), options);
                    chart.render();

                    var options2 = {
                        chart: {
                            type: 'line',
                            height: 300
                        },
                        colors: ["#696cff", "#03c3ec"],
                        stroke: {
                            curve: 'smooth', // Biar garisnya melengkung
                            width: 3
                        },
                        dataLabels: {
                            enabled: false // Matikan label angka di atas bar
                        },
                        series: [{
                            name: 'Penjualan',
                            data: res.data.penjualan
                        },{
                            name: 'Potongan',
                            data: res.data.potongan
                        }],
                        xaxis: {
                            categories: res.data.bulan
                        },
                        legend: {
                            show: false
                        }
                    };
                    var chartV = new ApexCharts(document.querySelector("#chart-revenue-admin"), options2);
                    chartV.render();
                }
            }
        })
    </script>
@endpush
@endsection
