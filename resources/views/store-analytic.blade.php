@extends('layouts.admin')
@section('page-title')
    {{ __('Store Analytics') }}
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{__('Store Analytics')}}</li>
@endsection
@push('css-page')
@endpush
@section('content')
<div class="row">
    <!-- [ sample-page ] start -->
    <div class="col-sm-12">
        <div class="row gy-4">
            <div class="col-lg-12">
                <div class="card mb-0">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Visitor') }}</h5>
                    </div>
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <h3 class="flex-grow-1 mb-0">4,354</h3>

                        </div>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                <div id="Analytics"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12 flex">
                <div class="card top-url-card table-card h-100 mb-0">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Top URL') }}</h5>
                    </div>
                    <div class="card-body overflow-hidden table-border-style">
                        <div class="top-product-table">
                            <div class="table-responsive custom-scrollbar">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th class="bg-transparent">{{ __('Url') }}</th>
                                            <th class="bg-transparent">{{ __('Views') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($visitor_url as $url)
                                            <tr>
                                                <td><a href="{{ $url->url }}">{{ $slug }}</a></td>
                                                <td>{{ $url->total }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-md-6 col-12 flex">
                {{-- <div class="platform-card-inner cart-card-inner h-100"> --}}
                    <div class="card h-100 mb-0">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('Platform') }}</h5>
                        </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <h3 class="flex-grow-1 mb-0">{{ __('Analytics') }}</h3>

                        </div>

                        <div class="tab-content" id="analyticsTabContent">
                            <div class="tab-pane fade show active" id="home1" role="tabpanel" aria-labelledby="home-tab1">
                                <div id="user-chart"></div>
                            </div>
                        </div>
                    </div>
                {{-- </div> --}}
            </div>
            </div>
            <div class="col-lg-6 col-md-6 col-12">
                <div class="card mb-0">
                    <div class="card-header">
                        <h5 class="mb-0"> {{ __('Device') }}</h5>
                    </div>
                    <div class="card-body chart-card">
                        <div class="d-flex gap-2 flex-wrap justify-content-between align-items-center">
                            {{-- <h3 class="mb-0 d-flex align-items-start">{{ __('WebKit') }} </h3> --}}

                        </div>
                        <div class="tab-content chart-body-wrp custom-scrollbar" id="analyticsTabContent">
                            <div class="tab-pane fade show active" id="home2" role="tabpanel" aria-labelledby="home-tab2">
                               <div id="WebKit"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-12">
                <div class="card mb-0">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Browser') }}</h5>
                    </div>
                    <div class="card-body chart-card">
                        <div class="d-flex gap-2 justify-content-between flex-wrap align-items-center">
                            {{-- <h3 class="mb-0 d-flex align-items-start">{{ __('Safari') }} </h3> --}}

                        </div>
                        <div class="tab-content chart-body-wrp custom-scrollbar" id="analyticsTabContent">
                            <div class="tab-pane fade show active" id="home3" role="tabpanel" aria-labelledby="home-tab3">
                               <div id="Safari"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ sample-page ] end -->
</div>
@endsection
@push('script-page')
    <script>
        (function () {
                var options = {
                    chart: {
                        height: 300,
                        type: 'area',
                        toolbar: {
                            show: false,
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: 2,
                        curve: 'smooth'
                    },
                    series: [{
                        name: "{{ __('Refferal') }}",
                        data: {!! json_encode($chartData['data']) !!}
                    }, {
                        name: "{{ __('Organic search') }}",
                        data: {!! json_encode($chartData['unique_data']) !!}
                    }],
                    xaxis: {
                        categories: {!! json_encode($chartData['label']) !!},
                        title: {
                            text: 'Days'
                        }
                    },
                    colors: ['#ffa21d', '#FF3A6E'],

                    grid: {
                        strokeDashArray: 4,
                        show: false,
                    },
                    legend: {
                        show: false,
                    },
                    {{--  markers: {
                        size: 4,
                        colors: ['#ffa21d', '#FF3A6E'],
                        opacity: 0.9,
                        strokeWidth: 2,
                        hover: {
                            size: 7,
                        }
                    },  --}}
                    yaxis: {
                        tickAmount: 3,
                    },

                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'dark',
                            type: "horizontal",
                            shadeIntensity: 0,
                            gradientToColors: undefined,
                            inverseColors: true,
                            opacityFrom: 0,
                            opacityTo: 0,
                            stops: [0, 50, 100],
                            colorStops: []
                        }
                    }
                };
                var chart = new ApexCharts(document.querySelector("#Analytics"), options);
                chart.render();
            })();
            (function () {
                var options = {
                    chart: {
                        type: 'bar',
                        height: 300,
                        zoom: {
                            enabled: false
                        },
                        toolbar: {
                            show: false,
                        },
                    },
                    dataLabels: {
                        enabled: false,
                    },

                    plotOptions: {
                        bar: {
                            color: '#fff',
                            columnWidth: '20%',
                        }
                    },
                    fill: {
                        type: 'solid',
                        opacity: 1,
                    },
                    series: [{
                        name: "{{ __('Platform') }}",
                        data: {!! json_encode($platformarray['data']) !!},
                    }],
                    colors: ['#6FD943','#162C4E','#DAE0E0','#316849','#1A3C4E','#203E4C'],
                    xaxis: {
                        labels: {
                            // format: 'MMM',
                            style: {
                                colors: PurposeStyle.colors.gray[600],
                                fontSize: '14px',
                                fontFamily: PurposeStyle.fonts.base,
                                cssClass: 'apexcharts-xaxis-label',
                            },
                        },
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: true,
                            borderType: 'solid',
                            color: PurposeStyle.colors.gray[300],
                            height: 6,
                            offsetX: 0,
                            offsetY: 0
                        },
                        title: {
                            text: '{{ __('Platform') }}'
                        },
                        categories: {!! json_encode($platformarray['label']) !!},
                    },
                    yaxis: {
                        tickAmount: 4,
                        labels: {
                            style: {
                                colors: "#000",
                            }
                        },
                    },
                    grid: {
                        borderColor: '#ffffff00',
                        padding: {
                            bottom: 0,
                            left: 10,
                        }
                    },
                    tooltip: {
                        fixed: {
                            enabled: false
                        },
                        x: {
                            show: false
                        },
                        y: {
                            title: {
                                formatter: function (seriesName) {
                                    return 'Total Earnings'
                                }
                            }
                        },
                        marker: {
                            show: false
                        }
                    }
                };
                var chart = new ApexCharts(document.querySelector("#user-chart"), options);
                chart.render();
            })();

            var options = {
                    series: {!! json_encode($devicearray['data']) !!},
                    chart: {
                        width: 450,
                        type: 'donut',
                    },
                    colors: ["#6FD943", "#316849", "#1A3C4E", "#EBF7E7", " #EBEDEF"],
                    labels: {!! json_encode($devicearray['label']) !!},
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 300
                            },
                            legend: {
                                position: 'bottom',
                            }
                        }
                    }]
                };
                var chart = new ApexCharts(document.querySelector("#WebKit"), options);
                chart.render();
                var options = {
                    series: {!! json_encode($browserarray['data']) !!},
                    chart: {
                        width: 450,
                        type: 'donut',
                    },
                    colors: ["#6FD943", "#316849", "#1A3C4E", "#EBF7E7", " #EBEDEF"],
                    labels: {!! json_encode($browserarray['label']) !!},
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 300
                            },
                            legend: {
                                position: 'bottom',
                            }
                        }
                    }]
                };
                var chart = new ApexCharts(document.querySelector("#Safari"), options);
                chart.render();
        </script>
@endpush
