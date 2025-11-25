@if ($appointment = Session::get('appointmentSuccess'))

    <div id="appointment-success" class="text-center px-4">
        <p class="mb-4">{{ __('Click the button below to complete the reservation fee of 400 USD.') }}</p>
        <a href="{{config('app-ea.app_appointment_pay_link')}}" target="_blank" class="btn btn-primary">{{ __('Pay with IYZICO') }}</a>
    </div>

@else

    @php
        $formId = rand(111111,999999);

        $swiperId = 'swiper-'.$formId;

        $formSwiperOptions = [
            'grabCursor' => false,
            'autoHeight' => true,
            'spaceBetween' => 0,
            'slidesPerView' => 1,
            'touchRatio' => 0,
            'simulateTouch' => false,
            'allowTouchMove' => false,
            'keyboard' => false,
            'mousewheel' => false,
            'navigation' => false,
        ];

        $jsonSwiperOptions = json_encode($formSwiperOptions);

        $disabledDates = \App\Models\WorkingHour::where('is_active',false)->pluck('day_of_week')->toArray();

    @endphp

    <div>

        <form action="{{route('appointment-form.submit')}}" method="POST" novalidate class="form needs-validation text-center">
            
            @csrf
            @honeypot

            <div id="{{$swiperId}}" @class(['swiper','ea-swiper','position-relative','opacity-100','overlow-hidden']) data-swiper-options="{{$jsonSwiperOptions}}">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="mw-600px mx-auto">
                            <div class="h3">{{ __('Reservation Page') }}</div>
                            <p>{{ __('Select a Date & Time') }}</p>
                            <div>
                                <input type="hidden" id="date-input-{{ $formId }}" name="appointment_date" required>
                                <div id="date-container-{{ $formId }}"></div>
                            </div>
                            <div id="date-slots-{{ $formId }}" class="pt-3 min-h-125px">
                                <div class="slots d-flex gap-2 flex-wrap justify-content-center"></div>
                                <div class="alert alert-danger error d-none w-100 mt-4 mb-0">
                                    {{ __('Uygun saat bulunmuyor!') }}
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="location-online-{{$formId}}" value="Online" name="json_data[location]" required>
                                    <label class="form-check-label" for="location-online-{{$formId}}">{{ __('ONLINE') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="location-clinic-{{$formId}}" value="Clinic" name="json_data[location]" required>
                                    <label class="form-check-label" for="location-clinic-{{$formId}}">{{ __('CLINIC') }}</label>
                                </div>
                            </div>
                            <div>
                                <button class="btn btn-primary btn-sm px-lg-6" type="button" onclick="stepperFormNextPrev('{{$swiperId}}','next')">{{ __('Continue') }}</button> 
                            </div>
                        </div>
                        <div class="slide-error mt-4 text-danger fs-sm d-none">
                            {{ __('Please select date, time and location.') }}
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="mw-600px mx-auto">
                            <div class="h3">{{ __('Form Page') }}</div>
                            <p>{{ __('Personal Information') }}</p>
                            <div class="d-flex flex-column gap-3">
                                <div>
                                    <input required type="text" name="name" placeholder="{{__('Name')}}" class="form-control border-0 border-bottom bg-transparent px-0" autocomplete="off">
                                    <div class="invalid-feedback fs-sm text-start">{{ __('Name field is required') }}</div>
                                </div>
                                <div>
                                    <input required type="email" name="email" placeholder="{{__('Email')}}" class="form-control border-0 border-bottom bg-transparent px-0" autocomplete="off">
                                    <div class="invalid-feedback fs-sm text-start">{{ __('Email field is required') }}</div>
                                </div>
                                <div>
                                    <input required type="text" name="phone" placeholder="{{__('Phone')}}" class="form-control border-0 border-bottom bg-transparent px-0" autocomplete="off">
                                    <div class="invalid-feedback fs-sm text-start">{{ __('Phone field is required') }}</div>
                                </div>
                            </div>
                            <p class="mt-4 mb-3">{{ __('Medical History') }}</p>
                            <textarea name="message" cols="30" rows="4" class="form-control border-0 border-bottom bg-transparent px-0" placeholder="{{ __('Medical History') }}"></textarea>
                        </div>
                        <div class="px-4 px-lg-7 px-xl-10 mt-5">
                            <div class="d-flex flex-column gap-2">
                                <div class="fs-sm">
                                    {!! __('This consultation is a 30-minute online face-to-face session with Dr. Mirza Fırat. During the meeting, you will discuss your treatment options in detail and create a personalized plan. The consultation fee is 400 USD. You can review cancellation and refund policies here (<a href="#" target="_blank">link</a>).') !!}
                                </div>
                                <div class="form-check d-flex align-items-center justify-content-center flex-wrap">
                                    <input class="form-check-input" type="checkbox" id="kvkk-check-{{$formId}}" name="kvkk" required>
                                    <label class="form-check-label fs-sm pt-1 ps-2 text-gray-600" for="kvkk-check-{{$formId}}">
                                        {{__('I have read and agree to the terms & conditions')}}
                                    </label>
                                    <div class="invalid-feedback w-100 fs-sm">{{ __('This field is required') }}</div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button class="btn btn-primary btn-sm px-lg-6" type="submit">{{ __('Schedule an Appointment') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </form>

    </div>

    @push('footer')
    <script>
        window.addEventListener("DOMContentLoaded",() => {

            var dbDisabledDays = @json($disabledDates);
            var disabledDays = dbDisabledDays.map(d => d % 7);

            var picker = new Pikaday({
                field: document.getElementById('date-input-{{ $formId }}'),
                firstDay: 1,
                minDate: new Date(),
                bound: false,
                showDaysInNextAndPreviousMonths:true,
                defaultDate:new Date(),
                setDefaultDate:true,
                container: document.getElementById('date-container-{{ $formId }}'),
                disableDayFn: function(date) {
                return disabledDays.includes(date.getDay());
                },
                onSelect:(date) => {
                    getAvailableWorkingHourSlots(date,'date-slots-{{ $formId }}');
                },
                onOpen: () => {
                    document.getElementById('{{$swiperId}}').swiper.updateAutoHeight(100);
                }
            });

            getAvailableWorkingHourSlots(new Date(),'date-slots-{{ $formId }}');

        });
    </script>
    @endpush

@endif