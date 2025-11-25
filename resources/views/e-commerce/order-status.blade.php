<x-fe-layout>

    <x-page-header-static :title="__('Sipariş Durumu')" />

    <section class="content-section">
        <div class="container">
        
            <div class="alert alert-success mb-5">Sipariş durumu: <b>{{$order->status->name}}</b></div>

            @if ($order->payment_type == 'transfer')
            
                @php
                    $bank_accounts = \App\Models\BankAccount::all();
                @endphp

                <div class="alert alert-info mb-5 vstack gap-3">
                    <p class="mb-0"><b>Siparişinizin onaylanması için aşağıdaki hesaplarımızdan birine EFT/Havale işlemi yapabilirsiniz</b></p>
                    @foreach ($bank_accounts as $account)
                    <div class="vstack bg-light rounded p-3">
                        <span>{{ $account->bank }}</span>
                        <span>{{ $account->holder }}</span>
                        <span>{{ $account->iban }}</span>
                        <span>{{ $account->currency }}</span>
                    </div>              
                    @endforeach
                </div>

            @endif

            <div class="row g-4 g-xl-5">
                <div class="col-lg-4">
                    <h2 class="h4">Sipariş Özeti</h2>
                    <div class="rounded bg-light p-3 vstack gap-3 mb-4">
                        <div class="hstack gap-2 justify-content-between align-items-center">
                            <span>Sipariş No:</span>
                            <span class="fw-bold">{{$order->code}}</span>
                        </div>
                        <div class="hstack gap-2 justify-content-between align-items-center">
                            <span>Sipariş Tarihi:</span>
                            <span class="fw-bold">{{$order->created_at->format('d.m.Y H:i:s')}}</span>
                        </div>
                        <div class="hstack gap-2 justify-content-between align-items-center">
                            <span>Sipariş Durumu:</span>
                            <span class="fw-bold">{{$order->status->name}}</span>
                        </div>
                    </div>
                    <div class="rounded bg-light p-3 vstack gap-3 mb-5">
                        

                        @php
    
                            $taxable_area = false;
                            $tax_values = [];
                            
                            foreach ($order->cart_details['items'] as $key => $item) {
                                if( $item['extra_info']['tax'] ){
                        
                                    $tax = $item['extra_info']['tax'];
                                    $tax_values[] = $item['subtotal'] * $tax['percentage'] / 100;
                        
                                }
                            }
                        
                            if( count($tax_values) > 0 ){
                                $taxable_area = true;
                            }
                        
                        @endphp
                        
                        @if ($taxable_area)
                        <div class="d-flex flex-row align-items-center justify-content-between">
                            <small>Vergi Öncesi:</small>
                            <span class="text-end">&#x20BA; {{$order->cart_details['items_subtotal'] - array_sum($tax_values)}}</span>
                        </div>
                        <div class="d-flex flex-row align-items-center justify-content-between mb-2">
                            <small>Vergiler:</small>
                            <span class="text-end">&#x20BA; {{array_sum($tax_values)}}</span>
                        </div>
                        @endif
                        
                        <div class="d-flex flex-row align-items-center justify-content-between">
                            <span>Ara toplam:</span>
                            <span class="text-end">&#x20BA; {{$order->cart_details['items_subtotal']}}</span>
                        </div>
                        @if ($order->cart_details['actions_amount'])
                        @foreach ($order->cart_details['applied_actions'] as $action)
                        <div class="d-flex flex-row align-items-center justify-content-between">
                            <small>{{$action['title']}}</small>
                            <span class="text-end">&#x20BA; {{$action['amount']}}</span>
                        </div>
                        @endforeach
                        @endif
                        <div class="d-flex flex-row align-items-center justify-content-between mt-2 fw-bolder">
                            <span>TOPLAM:</span>
                            <span class="text-end">&#x20BA; {{$order->cart_details['total']}}</span>
                        </div>


                    </div>
                </div>
                <div class="col-lg-8">
                    <h2 class="h4">Sepet İçeriği</h2>
                    <div class="table-responsive mb-5">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Ürün</th>
                                    <th>Adet</th>
                                    <th>Birim Fiyat</th>
                                    <th>Toplam</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->cart_details['items'] as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-row align-items-center text-nowrap">
                                            @if ($item['extra_info']['main_image'])
                                            <img src="{{ $item['extra_info']['main_image']['preview_url'] }}" class="box-50 rounded me-2" />
                                            @endif
                                            <div class="d-flex flex-column flex-grow-1">
                                                <small class="fw-bold">{{ $item['title'] }}</small>
                                                @if ($item['extra_info']['event_date'])
                                                <small class="">{{ \Carbon\Carbon::parse($item['extra_info']['event_date'])->format('d.m.Y') }}</small>
                                                @endif
                                                @if ($item['extra_info']['selections'])
                                                <div class="d-flex flex-column">
                                                    @foreach ($item['extra_info']['selections'] as $val)
                                                    <small class="fw-normal me-1">{{$val}}</small>
                                                    @endforeach
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{$item['quantity']}}</td>
                                    <td class="text-nowrap">&#x20BA; {{$item['price']}}</td>
                                    <td class="text-nowrap">
                                        <div class="vstack">
                                            <span>&#x20BA; {{$item['subtotal']}}</span>
                                            @if (count($item['applied_actions']) > 0)
                                                @foreach ($item['applied_actions'] as $act)
                                                <small>{{$act['title'].': '.$act['amount']}}</small>
                                                @endforeach
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row g-4 g-xl-5">
                        <div class="col-lg-6">
                            <div class="rounded bg-light p-3 vstack gap-3 mb-4">
                                <h3 class="h5 mb-0">Teslimat Bilgileri</h3>
                                <p class="mb-0">
                                    <b>{{ $order->cart_details['extra_info']['shipping']['name'] }}</b>
                                    <br>
                                    {{ $order->cart_details['extra_info']['shipping']['email'] }}
                                    <br>
                                    {{ $order->cart_details['extra_info']['shipping']['phone'] }}
                                </p>
                                <p class="mb-0">{{ $order->cart_details['extra_info']['shipping']['address'] }}</p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="rounded bg-light p-3 vstack gap-3 mb-4">
                                <h3 class="h5 mb-0">Fatura Bilgileri</h3>
                                <p class="mb-0">
                                    <b>{{ $order->cart_details['extra_info']['billing']['name'] }}</b>
                                    <br>
                                    {{ $order->cart_details['extra_info']['billing']['email'] }}
                                    <br>
                                    {{ $order->cart_details['extra_info']['billing']['phone'] }}
                                </p>
                                <p class="mb-0">{{ $order->cart_details['extra_info']['billing']['address'] }}</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

</x-fe-layout>