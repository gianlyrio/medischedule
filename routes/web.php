<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Specialty;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Services\AppointmentService;
use App\Models\User;

// Rota inicial pública (Dashboard / Listagem de Médicos por Especialidade)
Route::get('/', function (Request $request) {
    $specialtyId = $request->query('specialty_id');
    $specialties = Specialty::all();
    
    $doctorsQuery = Doctor::with('specialty')->where('is_active', true);
    if ($specialtyId) {
        $doctorsQuery->where('specialty_id', $specialtyId);
    }
    $doctors = $doctorsQuery->get();

    return view('welcome', compact('specialties', 'doctors', 'specialtyId'));
});

// Rotas de Autenticação Manual Básicas (Login / Cadastro / Logout)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('patient.dashboard');
    }

    return back()->withErrors(['email' => 'As credenciais fornecidas não correspondem aos nossos registros.']);
});

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', function (Request $request) {
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6',
        'cpf' => 'required|string|unique:users',
        'phone' => 'required|string',
    ]);

    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
        'cpf' => $data['cpf'],
        'phone' => $data['phone'],
        'role' => 'patient',
        'status' => 'active',
    ]);

    Auth::login($user);
    return redirect()->route('patient.dashboard');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');


// ==========================================
// ROTAS DE PACIENTE (Requer Autenticação)
// ==========================================
Route::middleware(['auth'])->prefix('patient')->name('patient.')->group(function () {
    
    Route::get('/dashboard', function () {
        $specialties = Specialty::all();
        $doctors = Doctor::with('specialty')->where('is_active', true)->get();
        return view('patient.dashboard', compact('specialties', 'doctors'));
    })->name('dashboard');

    Route::get('/appointments', function () {
        $appointments = Auth::user()->appointments()->with('doctor.specialty')->orderBy('appointment_date', 'desc')->get();
        return view('patient.appointments', compact('appointments'));
    })->name('appointments');

    Route::post('/appointments', function (Request $request, AppointmentService $service) {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
        ]);

        try {
            $service->schedule([
                'patient_id' => Auth::id(),
                'doctor_id' => $request->doctor_id,
                'appointment_date' => $request->appointment_date,
                'notes' => $request->notes,
            ]);
            return redirect()->route('patient.appointments')->with('success', 'Consulta agendada com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    })->name('appointments.store');

    Route::post('/appointments/{appointment}/cancel', function (Appointment $appointment, AppointmentService $service) {
        try {
            $service->cancel($appointment, Auth::user());
            return back()->with('success', 'Consulta cancelada com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    })->name('appointments.cancel');
});


// ==========================================
// ROTAS ADMINISTRATIVAS (Requer Auth + Admin)
// ==========================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/dashboard', function () {
        $totalDoctors = Doctor::count();
        $totalPatients = User::where('role', 'patient')->count();
        $totalAppointments = Appointment::count();
        $todayAppointments = Appointment::whereDate('appointment_date', today())->get();

        return view('admin.dashboard', compact('totalDoctors', 'totalPatients', 'totalAppointments', 'todayAppointments'));
    })->name('dashboard');

    Route::get('/specialties', function () {
        $specialties = Specialty::all();
        return view('admin.specialties', compact('specialties'));
    })->name('specialties');

    Route::get('/doctors', function () {
        $doctors = Doctor::with('specialty')->get();
        $specialties = Specialty::all();
        return view('admin.doctors', compact('doctors', 'specialties'));
    })->name('doctors');

    Route::post('/doctors', function (Request $request) {
        $request->validate([
            'specialty_id' => 'required|exists:specialties,id',
            'name' => 'required|string',
            'crm' => 'required|string|unique:doctors',
            'phone' => 'required|string',
            'work_start_time' => 'required',
            'work_end_time' => 'required',
        ]);

        Doctor::create($request->all());
        return back()->with('success', 'Médico cadastrado com sucesso!');
    })->name('doctors.store');
});