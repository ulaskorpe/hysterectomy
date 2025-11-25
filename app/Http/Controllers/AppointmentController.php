<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Mail\NewAppointmentMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Notification;
use Spatie\ResponseCache\Facades\ResponseCache;
use App\Notifications\NewAppointmentNotification;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Appointment::class);

        $search = $request->search;

        $appointments = QueryBuilder::for(Appointment::class)
        ->when($search && !empty($search), function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('phone', 'like', '%' . $search . '%')
                ->orWhere('appointment_date', 'like', '%' . $search . '%')
                ->orWhere('json_data', 'like', '%' . $search . '%');
            });
        })
        ->defaultSort('-id')
        ->allowedSorts('id','appointment_date','name','email')
        ->allowedFilters([
            'name',
            'appointment_date',
            'email',
            'phone',
            'status'
        ])
        ->paginate(30)
        ->withQueryString();

        return Inertia('Appointment/Index',[
            'appointments' => $appointments,
            'filters' => [
                'status' => [
                    'label' => 'Durum',
                    'type' => 'select',
                    'options' => [
                        [
                            'label' => 'YENİ',
                            'value' => 'new'
                        ],
                        [
                            'label' => 'ONAYLANDI',
                            'value' => 'success'
                        ],
                        [
                            'label' => 'İPTAL EDİLDİ',
                            'value' => 'canceled'
                        ]
                    ],
                    'value' => $request->filter['status'] ?? [],
                    'full_width' => true
                ]
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string',
            'phone' => 'required|string',
            'appointment_date' => 'required|string',
            'appointment_time' => 'required|string',
            'kvkk' => 'required'
        ]);

        //once DB ye yazalim.
        $appointment = Appointment::create($request->all());

        try {

            $mail = Notification::route('mail', config('app-ea.app_appointment_email'))->notify(new NewAppointmentNotification($appointment));
            
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }

        ResponseCache::forget(url()->previous());

        return redirect(url()->previous().'#appointment-success')->with('appointmentSuccess', $appointment->toArray());
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        $this->authorize('update',Appointment::class);

        $appointment->update($request->all());

        return back()->with('flash',[
            "type" => "success",
            "message" => "Randevu başarıyla güncellendi!",
            "data" => $appointment
        ]);


    }


    public function delete(Appointment $appointment)
    {
        $this->authorize('delete', Appointment::class);

        $appointment->delete();
        return back();
    }

    public function restore(Appointment $appointment)
    {
        $this->authorize('restore', Appointment::class);

        $appointment->restore();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        //
    }
}
